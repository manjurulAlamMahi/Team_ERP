<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clients', 'username')->where(fn ($q) => $q->where('profile_id', $this->profile_id)),
            ],
            'profile_id' => ['required', 'integer', 'exists:fiverr_profiles,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'sales_man_name' => ['nullable', 'string', 'max:255'],
            'sales_man_whatsapp' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username Client is required.',
            'username.unique' => 'This username already exists for this profile.',
            'profile_id.required' => 'Fiverr Profile is required.',
        ];
    }
}
