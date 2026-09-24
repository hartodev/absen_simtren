<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResendActivationRequest;
use App\Models\Company;
use App\Models\User;
use App\Notifications\CompanyActivationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Versi API dari alur "daftar -> aktivasi -> login" yang sudah jalan di web
 * (App\Http\Controllers\Web\Auth\RegisterController, ActivationController,
 * dan LoginController + App\Http\Middleware\ResolveTenant).
 *
 * BEDA UTAMA DENGAN WEB:
 * - Di web, tenant diketahui dari HOST request (Route::domain('{tenant}.xxx')).
 *   Mobile app selalu memukul satu base URL yang sama, jadi tenant dikirim
 *   eksplisit sebagai field "subdomain" (body login) atau path param
 *   (GET /tenant/{subdomain}).
 * - Aktivasi TETAP lewat email + link signed URL yang dibuka di browser
 *   (CompanyActivationNotification, ->action('Aktivasi Akun Saya', $url)),
 *   karena link itu berlaku 24 jam dan aman ditandatangani (Route::signed).
 *   App HANYA bertanggung jawab men-submit form daftar dan menampilkan
 *   layar "cek email" + tombol kirim ulang -- bukan memproses link itu sendiri.
 * - Setelah login berhasil, token Sanctum sudah terikat ke satu user yang
 *   pasti terhubung ke satu company_id, jadi endpoint lain tidak perlu tahu
 *   subdomain lagi.
 *
 * FORMAT RESPONSE (disamakan dengan konvensi module lain di app, mis.
 * HrEmployee*ResponseModel di Flutter): {"status": bool, "message": string,
 * "data": mixed|null}. Error validasi (422) TETAP pakai format standar
 * Laravel ({"message", "errors"}) karena itu datang otomatis dari FormRequest.
 */
class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Daftar lembaga baru + user admin pertamanya. Status company otomatis
     * "pending" sampai admin klik link aktivasi di email (24 jam).
     * Mirror dari RegisterController::store() di web, response-nya JSON.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data  = $request->validated();
        $types = config('organization_types', []);

        [$company, $admin] = DB::transaction(function () use ($data, $types) {
            $company = Company::create([
                'name'      => $data['name'],
                'subdomain' => strtolower($data['subdomain']),
                'type'      => $data['type'],
                'status'    => 'pending',
            ]);

            $adminRole = $types[$data['type']]['admin_role'] ?? 'hr';

            $admin = User::create([
                'name'       => $data['admin_name'],
                'email'      => $data['admin_email'],
                'password'   => Hash::make($data['password']),
                'role'       => $adminRole,
                'company_id' => $company->id,
            ]);

            return [$company, $admin];
        });

        $admin->notify(new CompanyActivationNotification($company, $admin));

        return $this->ok(
            'Pendaftaran berhasil! Silakan cek email Anda untuk mengaktifkan akun sebelum login.',
            [
                'company' => $this->formatCompany($company),
                'admin_email' => $admin->email,
            ],
            201,
        );
    }

    /**
     * POST /api/auth/resend-activation
     * Kirim ulang email aktivasi. Pesan SENGAJA digeneralisir (tidak bilang
     * "email tidak ditemukan") supaya tidak bisa dipakai buat enumerasi
     * email terdaftar -- sama seperti ActivationController::resend() di web.
     */
    public function resendActivation(ResendActivationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if ($user && $user->company && $user->company->status === 'pending') {
            $user->notify(new CompanyActivationNotification($user->company, $user));
        }

        return $this->ok(
            'Jika akun ditemukan dan masih menunggu aktivasi, email aktivasi baru telah dikirim.',
        );
    }

    /**
     * GET /api/auth/tenant/{subdomain}
     * Lookup info lembaga dari subdomain, dipakai layar "cari lembaga saya"
     * di mobile SEBELUM user mengisi email/password. Juga berguna sebagai
     * "cek status aktivasi": kalau company masih pending, endpoint ini
     * balas 403 dengan pesan yang jelas.
     */
    public function tenant(string $subdomain): JsonResponse
    {
        $company = Company::bySubdomain(strtolower(trim($subdomain)))->first();

        if (! $company) {
            return $this->fail('Subdomain lembaga tidak ditemukan.', 404);
        }

        if ($company->status === 'pending') {
            return $this->fail(
                'Lembaga ini belum diaktivasi. Silakan cek email aktivasi yang dikirim saat pendaftaran.',
                403,
            );
        }

        if ($company->status === 'nonaktif') {
            return $this->fail(
                'Lembaga ini sedang tidak aktif. Hubungi admin untuk informasi lebih lanjut.',
                403,
            );
        }

        return $this->ok('Lembaga ditemukan.', $this->formatCompany($company));
    }

    /**
     * POST /api/auth/login
     * Kalau "subdomain" diisi -> login user lembaga (hr/ustadz/teacher/
     * employee/santri/student), company_id user harus cocok dengan company
     * hasil resolve subdomain. Kalau "subdomain" kosong -> khusus superadmin.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data      = $request->validated();
        $subdomain = $data['subdomain'] ?? null;
        $company   = null;

        if ($subdomain) {
            $company = Company::bySubdomain($subdomain)->first();

            if (! $company) {
                throw ValidationException::withMessages([
                    'subdomain' => 'Subdomain lembaga tidak ditemukan.',
                ]);
            }

            if ($company->status === 'pending') {
                throw ValidationException::withMessages([
                    'subdomain' => 'Lembaga ini belum diaktivasi. Silakan cek email aktivasi yang dikirim saat pendaftaran.',
                ]);
            }

            if ($company->status === 'nonaktif') {
                throw ValidationException::withMessages([
                    'subdomain' => 'Lembaga ini sedang tidak aktif. Hubungi admin untuk informasi lebih lanjut.',
                ]);
            }
        }

        $query = User::where('email', $data['email']);

        $user = $subdomain
            ? $query->where('company_id', $company->id)->first()
            : $query->whereNull('company_id')->where('role', 'superadmin')->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => $subdomain
                    ? 'Email atau kata sandi salah, atau akun ini bukan milik lembaga ini.'
                    : 'Email atau kata sandi salah.',
            ]);
        }

        if (! $subdomain && $user->role !== 'superadmin') {
            throw ValidationException::withMessages([
                'subdomain' => 'Akun ini terdaftar di sebuah lembaga. Isi subdomain lembaga Anda untuk login.',
            ]);
        }

        if ($subdomain && $user->role === 'superadmin') {
            throw ValidationException::withMessages([
                'subdomain' => 'Superadmin login tanpa subdomain lembaga.',
            ]);
        }

        if ($user->company && $user->company->status !== 'aktif') {
            throw ValidationException::withMessages([
                'email' => 'Akun organisasi Anda sedang tidak aktif. Hubungi admin.',
            ]);
        }

        // Satu device = satu token aktif.
        $deviceName = $data['device_name'] ?? 'mobile';
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->ok('Login berhasil.', [
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->formatUser($user),
        ]);
    }

    /**
     * GET /api/auth/me (butuh token)
     * Dipanggil mobile app tiap kali dibuka ulang untuk memastikan token
     * masih valid dan tahu harus buka dashboard yang mana.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->ok('OK', $this->formatUser($request->user()));
    }

    /**
     * POST /api/auth/logout (butuh token)
     * Cabut token yang sedang dipakai (device ini saja).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('Berhasil logout.');
    }

    private function formatUser(User $user): array
    {
        return [
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role,
            'company' => $user->company ? $this->formatCompany($user->company) : null,
        ];
    }

    private function formatCompany(Company $company): array
    {
        return [
            'id'        => $company->id,
            'name'      => $company->name,
            'type'      => $company->type,
            'subdomain' => $company->subdomain,
            'status'    => $company->status,
        ];
    }

    /**
     * Bentuk response sukses standar: {"status": true, "message": ..., "data": ...}
     */
    private function ok(string $message, $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Bentuk response gagal (di luar 422 validasi, yang otomatis dari
     * FormRequest): {"status": false, "message": ...}
     */
    private function fail(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], $status);
    }
}
