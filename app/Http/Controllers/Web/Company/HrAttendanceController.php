<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrAttendanceController extends Controller
{
    // ----------------------------------------------------------
    // HELPERS (pattern sama dengan HrDashboardController)
    // ----------------------------------------------------------

    private function ensureHr(): void
    {
        // Controller ini dipakai bareng oleh 3 tipe tenant (company/pesantren/school)
        // — lihat routes/web.php. Role admin organisasi berbeda per tipe (hr/ustadz/teacher),
        // jadi cek di sini cukup "termasuk salah satu admin organisasi", karena tipe-nya
        // sendiri sudah dijamin cocok oleh middleware 'tenant.type:{type}' + 'role:{adminRole}'
        // di routing.
        if (! Auth::check() || ! in_array(Auth::user()->role, ['hr', 'ustadz', 'teacher'], true)) {
            abort(403, 'Akses ditolak (khusus admin organisasi).');
        }
    }

    private function companyOrFail()
    {
        $company = Auth::user()->company;

        if (! $company) {
            abort(422, 'Company tidak ditemukan untuk user ini.');
        }

        return $company;
    }

    /**
     * Role anggota (bukan admin) sesuai tipe tenant.
     * Samakan dengan HrEmployeeController::memberRole() kalau sudah ada di sana.
     */
    private function memberRole(string $companyType): string
    {
        return match ($companyType) {
            'pesantren' => 'santri',
            'school'    => 'student',
            default     => 'employee',
        };
    }

    // ----------------------------------------------------------
    // GET /company/attendances
    // "Daftar Karyawan Hari Ini" — semua employee + status
    // check-in/check-out pada tanggal terpilih (default: hari ini)
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $this->ensureHr();
        $company = $this->companyOrFail();

        $date = $request->filled('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : Carbon::today()->toDateString();

        $search = trim((string) $request->query('search', ''));

        $employeesQuery = User::where('company_id', $company->id)
            ->where('role', $this->memberRole($company->type))
            ->orderBy('name');

        if ($search !== '') {
            $employeesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // TODO: tabel `users` saat ini cuma punya id/name/email/role/company_id
        // — belum ada kolom position/department/image_url (foto profil, jabatan, divisi).
        // Kalau field itu memang mau ditampilkan di sini, kasih tau disimpan di
        // tabel/kolom mana (langsung di users, atau tabel terpisah semacam
        // employee_profiles dengan relasi ke users), nanti aku sambungkan lagi.
        $employees = $employeesQuery->get(['id', 'name', 'email']);

        // Ambil semua attendance company pada tanggal itu, di-key by user_id
        // supaya bisa digabung ke tiap employee tanpa N+1 query.
        $attendances = Attendance::where('company_id', $company->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('user_id');

        $rows = $employees->map(function (User $emp) use ($attendances) {
            /** @var Attendance|null $a */
            $a = $attendances->get($emp->id);

            return [
                'id'          => $emp->id,
                'attendance_id' => $a?->id,
                'name'        => $emp->name,
                'email'       => $emp->email,
                'time_in'     => $a?->time_in,
                'time_out'    => $a?->time_out,
                'status'      => $a?->status,      // on_time / late / overtime / absent / guest / null
                'late_minutes' => $a?->late_minutes ?? 0,
                'checked_in'  => (bool) $a?->time_in,
                'checked_out' => (bool) $a?->time_out,
            ];
        })->values();

        return view('pages.company.attendances.index', [
            'company'   => $company,
            'date'      => $date,
            'search'    => $search,
            'rows'      => $rows,
            'summary'   => [
                'total'      => $rows->count(),
                'hadir'      => $rows->where('checked_in', true)->count(),
                'belum_hadir' => $rows->where('checked_in', false)->count(),
                'terlambat'  => $rows->where('status', 'late')->count(),
            ],
        ]);
    }

    // ----------------------------------------------------------
    // GET /company/attendances/{id}
    // Detail satu record absensi (bukan id user — id Attendance)
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $this->ensureHr();
        $company = $this->companyOrFail();

        $attendance = Attendance::with(['user:id,name,email', 'shift'])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        return view('pages.company.attendances.show', [
            'company'    => $company,
            'attendance' => $attendance,
        ]);
    }

    // ----------------------------------------------------------
    // GET /company/attendances/settings
    // Form pengaturan lokasi, radius, dan jam kerja default company
    // ----------------------------------------------------------
    public function settings(): View
    {
        $this->ensureHr();
        $company = $this->companyOrFail();

        return view('pages.company.attendances.settings', [
            'company' => $company,
        ]);
    }

    // ----------------------------------------------------------
    // PUT /company/attendances/settings
    // ----------------------------------------------------------
    public function updateSettings(Request $request): RedirectResponse
    {
        $this->ensureHr();
        $company = $this->companyOrFail();

        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_km' => ['required', 'numeric', 'min:0.01', 'max:50'],
            'time_in'   => ['required', 'date_format:H:i'],
            'time_out'  => ['required', 'date_format:H:i', 'after:time_in'],
        ], [
            'time_out.after' => 'Jam pulang harus setelah jam masuk.',
        ]);

        $company->update($validated);

        return redirect()
            ->route('company.attendances.settings')
            ->with('success', 'Pengaturan absensi berhasil disimpan.');
    }
}