<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TeamUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('team_edit');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:teams,id',
            'community_id' => 'required|exists:communities,id',
            'name' => 'required|string|max:255',
            'started_at' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'community_id.required' => 'The community field is required.',
            'community_id.exists' => 'The selected community is invalid.',
            'name.required' => 'The team name field is required.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
