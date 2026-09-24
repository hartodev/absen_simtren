<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendActivationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : $this->email,
        ]);
    }
}
