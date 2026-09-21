<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     *
     * Dipakai di DUA tempat:
     *  - domain utama (/superadmin/login) → login superadmin (tidak ada tenant)
     *  - subdomain tenant → login employee/hr/ustadz/santri/dst milik tenant itu
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->redirectPath(Auth::user()));
        }

        $tenant = $request->attributes->get('tenant');

        return view('pages.auth.auth-login', compact('tenant'));
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $tenant   = $request->attributes->get('tenant');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Guard: pastikan user punya context yang valid
        // (superadmin tanpa company, atau user dengan company)
        if (! $user->company_id && $user->role !== 'superadmin') {
            $this->rejectLogin($request, 'Akun Anda belum terhubung ke perusahaan/pesantren/sekolah manapun.');
        }

        // [DOMAIN UTAMA] Form ini khusus superadmin. User lembaga harus lewat
        // subdomain lembaganya (gerbang: /login -> isi subdomain).
        if (! $tenant && $user->role !== 'superadmin') {
            $this->rejectLogin($request, 'Halaman ini khusus superadmin. Silakan masuk lewat halaman lembaga Anda.');
        }

        // [SUBDOMAIN] Login lewat subdomain tenant → wajib superadmin TIDAK
        // boleh, dan user WAJIB milik tenant (company) yang sedang diakses.
        if ($tenant) {
            if ($user->role === 'superadmin') {
                $this->rejectLogin($request, 'Superadmin login lewat halaman pusat, bukan subdomain tenant.');
            }

            if ((int) $user->company_id !== (int) $tenant->id) {
                $this->rejectLogin($request, 'Akun Anda tidak terdaftar di organisasi ini.');
            }
        }

        // Kalau punya company, pastikan company masih aktif
        if ($user->company && $user->company->status !== 'aktif') {
            $this->rejectLogin($request, 'Akun organisasi Anda sedang tidak aktif. Hubungi admin.');
        }

        return redirect()->intended($this->redirectPath($user));
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Di subdomain tenant -> kembali ke /login subdomain itu.
        // Di domain utama (superadmin) -> ke login superadmin.
        if ($request->attributes->get('tenant')) {
            return redirect('/login');
        }

        return redirect()->route('superadmin.login.form');
    }

    /**
     * Batalkan login yang baru saja terjadi & lempar validation error.
     */
    private function rejectLogin(Request $request, string $message): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages(['email' => $message]);
    }

    /**
     * Tentukan tujuan redirect berdasarkan context user.
     *
     * PENTING: untuk user dengan company (bukan superadmin), ini SEKARANG
     * mengembalikan URL ABSOLUT ke subdomain tenant miliknya
     * (contoh: http://acme.ptutamacta.test:8000/company/dashboard),
     * bukan path relatif. Sebelumnya path relatif ('/company/dashboard')
     * dipakai apa adanya, sehingga kalau user login dari domain utama,
     * redirect tetap menempel di domain utama dan berujung 404 karena
     * route dashboard hanya terdaftar di bawah subdomain tenant.
     */
    private function redirectPath($user): string
    {
        // Superadmin — tidak punya company, hanya login di domain utama
        if ($user->role === 'superadmin') {
            return route('superadmin.dashboard');
        }

        $type = $user->company?->type;
        $role = $user->role;

        $path = match (true) {
            $type === 'company'   && $role === 'hr'       => '/company/dashboard',
            $type === 'company'   && $role === 'employee' => '/employee/dashboard',
            $type === 'pesantren' && $role === 'ustadz'   => '/pesantren/dashboard',
            $type === 'pesantren' && $role === 'santri'   => '/santri/dashboard',
            $type === 'school'    && $role === 'teacher'  => '/school/dashboard',
            $type === 'school'    && $role === 'student'  => '/student/dashboard',
            default => null,
        };

        if (! $path || ! $user->company) {
            return route('login.form');
        }

        return $this->tenantUrl($user->company->subdomain, $path);
    }

    /**
     * Bangun URL absolut ke subdomain tenant tertentu, ikut scheme & port
     * yang sedang dipakai saat request berjalan (http/https, :8000 waktu
     * lokal, dst), lalu ditempel path tujuan (mis. '/company/dashboard').
     *
     * PERLU DICEK: nama kolom "subdomain" di bawah ini adalah TEBAKAN
     * berdasarkan konteks (istilah "{tenant}" di comment route dan
     * middleware ResolveTenant yang men-set attribute 'tenant'). Kalau
     * kolom identifier subdomain di tabel companies kamu namanya beda
     * (misal "slug" atau "code"), ganti $user->company->subdomain di
     * atas dan di bawah ini sesuai nama kolom asli.
     */
    private function tenantUrl(string $subdomain, string $path): string
    {
        $tenantDomain = config('app.tenant_domain');
        $scheme       = request()->getScheme();
        $port         = request()->getPort();

        $host = $subdomain . '.' . $tenantDomain;
        if (! in_array($port, [80, 443], true)) {
            $host .= ':' . $port;
        }

        return $scheme . '://' . $host . $path;
    }
}