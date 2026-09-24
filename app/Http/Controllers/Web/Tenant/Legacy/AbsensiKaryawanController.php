<?php

namespace App\Http\Controllers\Web\Tenant\Legacy;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class AbsensiKaryawanController extends Controller
{
    /**
     * Daftar/rekap absensi karyawan (admin), bisa difilter per tanggal & karyawan.
     */
    public function index(Request $request)
    {
        $query = AbsensiKaryawan::with('karyawan')->latest('tanggal');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $absensi = $query->paginate(20)->withQueryString();
        $karyawan = Karyawan::orderBy('nama')->get();

        return view('_legacy.pages.tenant.absensi-karyawan.index', compact('absensi', 'karyawan'));
    }

    /**
     * Form pencatatan manual oleh admin (misal untuk izin/sakit/alpa).
     */
    public function create()
    {
        $karyawan = Karyawan::orderBy('nama')->get();

        return view('_legacy.pages.tenant.absensi-karyawan.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,telat,izin,sakit,alpa,lembur',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // tenant_id otomatis terisi lewat trait BelongsToTenant
        $absensi = AbsensiKaryawan::where('karyawan_id', $request->karyawan_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first() ?? new AbsensiKaryawan([
                'karyawan_id' => $request->karyawan_id,
                'tanggal' => $request->tanggal,
            ]);

        $absensi->fill($request->only('jam_masuk', 'jam_pulang', 'status', 'keterangan'));
        $absensi->save();

        return redirect()->route('_legacy.pages.tenant.absensi-karyawan.index')->with('success', 'Absensi karyawan berhasil dicatat.');
    }

    public function edit(AbsensiKaryawan $absensiKaryawan)
    {
        $karyawan = Karyawan::orderBy('nama')->get();

        return view('_legacy.pages.tenant.absensi-karyawan.edit', [
            'absensi' => $absensiKaryawan,
            'karyawan' => $karyawan,
        ]);
    }

    public function update(Request $request, AbsensiKaryawan $absensiKaryawan)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,telat,izin,sakit,alpa,lembur',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $absensiKaryawan->update($request->only(
            'karyawan_id', 'tanggal', 'jam_masuk', 'jam_pulang', 'status', 'keterangan'
        ));

        return redirect()->route('_legacy.pages.tenant.absensi-karyawan.index')->with('success', 'Absensi karyawan berhasil diperbarui.');
    }

    public function destroy(AbsensiKaryawan $absensiKaryawan)
    {
        $absensiKaryawan->delete();

        return redirect()->route('_legacy.pages.tenant.absensi-karyawan.index')->with('success', 'Absensi karyawan berhasil dihapus.');
    }

    /**
     * Halaman absen mandiri: pilih nama karyawan lalu absen masuk/pulang
     * dengan waktu & lokasi otomatis (dipakai di kios/perangkat bersama).
     */
    public function absenMandiri()
    {
        $karyawan = Karyawan::where('status_aktif', true)->orderBy('nama')->get();
        $today = Carbon::today()->toDateString();

        $absensiHariIni = AbsensiKaryawan::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', $karyawan->pluck('id'))
            ->get()
            ->keyBy('karyawan_id');

        return view('_legacy.pages.tenant.absensi-karyawan.absen', compact('karyawan', 'absensiHariIni', 'today'));
    }

    /**
     * Absen masuk (check-in) untuk satu karyawan, dipanggil dari halaman absen mandiri.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'lokasi' => 'nullable|string',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        $today = Carbon::today()->toDateString();

        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first() ?? new AbsensiKaryawan([
                'karyawan_id' => $karyawan->id,
                'tanggal' => $today,
            ]);

        if ($absensi->exists && $absensi->sudahCheckIn()) {
            return back()->with('error', "{$karyawan->nama} sudah absen masuk hari ini.");
        }

        $jamMasuk = Carbon::now();
        $status = 'hadir';

        // telat kalau ada jam_masuk standar di data karyawan dan sudah lewat
        if ($karyawan->jam_masuk && $jamMasuk->format('H:i:s') > $karyawan->jam_masuk) {
            $status = 'telat';
        }

        $absensi->fill([
            'jam_masuk' => $jamMasuk->format('H:i:s'),
            'lokasi_masuk' => $request->lokasi,
            'status' => $status,
        ]);
        $absensi->save();

        return back()->with('success', "Absen masuk {$karyawan->nama} berhasil dicatat pukul {$jamMasuk->format('H:i')}.");
    }

    /**
     * Absen pulang (check-out) untuk satu karyawan.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'lokasi' => 'nullable|string',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        $today = Carbon::today()->toDateString();

        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (! $absensi || ! $absensi->sudahCheckIn()) {
            return back()->with('error', "{$karyawan->nama} belum absen masuk hari ini.");
        }

        if ($absensi->sudahCheckOut()) {
            return back()->with('error', "{$karyawan->nama} sudah absen pulang hari ini.");
        }

        $jamPulang = Carbon::now();

        $absensi->update([
            'jam_pulang' => $jamPulang->format('H:i:s'),
            'lokasi_pulang' => $request->lokasi,
        ]);

        return back()->with('success', "Absen pulang {$karyawan->nama} berhasil dicatat pukul {$jamPulang->format('H:i')}.");
    }
}