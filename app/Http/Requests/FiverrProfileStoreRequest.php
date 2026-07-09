<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class FiverrProfileStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('fiverr_profile_create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:fiverr_profiles,name',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'This profile name already exists.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
