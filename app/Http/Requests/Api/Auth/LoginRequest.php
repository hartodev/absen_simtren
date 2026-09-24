<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * "subdomain" WAJIB diisi untuk user lembaga (hr/ustadz/teacher/
     * employee/santri/student), dan dikosongkan hanya untuk login
     * superadmin. Ini menggantikan peran Route::domain('{tenant}.xxx')
     * di web, karena mobile app memukul satu base URL yang sama untuk
     * semua tenant.
     */
    public function rules(): array
    {
        return [
            'subdomain'   => ['nullable', 'string', 'alpha_dash', 'max:255'],
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            // Nama perangkat, dipakai sebagai nama token Sanctum supaya
            // gampang dikenali/dicabut dari daftar device di masa depan
            // (mis. "iPhone 15 - Budi", "Xiaomi Redmi Note 12").
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'subdomain' => $this->subdomain ? strtolower(trim($this->subdomain)) : null,
            'email'     => $this->email ? strtolower(trim($this->email)) : $this->email,
        ]);
    }
}