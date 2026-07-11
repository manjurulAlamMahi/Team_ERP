<?php

namespace App\Http\Requests\Client;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientsBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clients' => ['required', 'array', 'min:1'],
            'clients.*.username' => ['required', 'string', 'max:255'],
            'clients.*.profile_id' => ['required', 'integer', 'exists:fiverr_profiles,id'],
            'clients.*.client_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rows = $this->input('clients', []);
            $seen = [];

            foreach ($rows as $index => $row) {
                $username = trim((string) ($row['username'] ?? ''));
                $profileId = $row['profile_id'] ?? null;

                if ($username === '' || !$profileId) {
                    continue;
                }

                $key = $profileId . '|' . mb_strtolower($username);

                if (isset($seen[$key])) {
                    $validator->errors()->add("clients.$index.username", 'This username is duplicated elsewhere in this list for the same profile.');
                    continue;
                }
                $seen[$key] = true;

                if (Client::where('profile_id', $profileId)->where('username', $username)->exists()) {
                    $validator->errors()->add("clients.$index.username", 'This username already exists for this profile.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'clients.required' => 'Please add at least one client.',
            'clients.*.username.required' => 'Username is required for every client.',
            'clients.*.profile_id.required' => 'Profile is required for every client.',
        ];
    }
}
