<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CommunityStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('community_create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:communities,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'This community name is already taken.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
