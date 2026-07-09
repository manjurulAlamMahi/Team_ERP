<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class FiverrProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('fiverr_profile_edit');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:fiverr_profiles,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fiverr_profiles', 'name')->ignore($this->id),
            ],
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
