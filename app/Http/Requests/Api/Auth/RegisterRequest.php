<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Versi API dari App\Http\Controllers\Web\Auth\RegisterController::store().
 * Aturan validasinya SENGAJA disamakan persis dengan versi web supaya
 * perilaku "daftar lembaga baru" konsisten di kedua platform.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reserved = config('reserved_subdomains', []);
        $types    = config('organization_types', []);

        return [
            'name' => ['required', 'string', 'max:255'],

            // Tipe organisasi yang boleh daftar mandiri (company/pesantren/school),
            // lihat config/organization_types.php.
            'type' => ['required', 'string', Rule::in(array_keys($types))],

            'subdomain' => [
                'required',
                'alpha_dash',
                'max:63',
                Rule::unique('companies', 'subdomain'),
                function ($attribute, $value, $fail) use ($reserved) {
                    if (in_array(strtolower($value), $reserved, true)) {
                        $fail('Subdomain ini tidak boleh digunakan, silakan pilih yang lain.');
                    }
                },
            ],

            'admin_name'  => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],

            // Frontend wajib kirim "password" + "password_confirmation".
            'password' => ['required', 'confirmed', Password::min(6)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'subdomain'   => $this->subdomain ? strtolower(trim($this->subdomain)) : null,
            'admin_email' => $this->admin_email ? strtolower(trim($this->admin_email)) : $this->admin_email,
        ]);
    }
}
