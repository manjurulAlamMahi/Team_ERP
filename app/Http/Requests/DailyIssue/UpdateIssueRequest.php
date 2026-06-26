<?php

namespace App\Http\Requests\DailyIssue;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:daily_issues,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'profile_name' => ['required', 'string', 'max:255'],
            'issue' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'in:Critical,Urgent,High,Normal'],
            'responsible_ids' => ['required', 'array', 'min:1'],
            'responsible_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $teamId = Auth::user()?->team_id;
            $ids = $this->input('responsible_ids', []);

            if (!empty($ids) && User::whereIn('id', $ids)->where('team_id', $teamId)->count() !== count($ids)) {
                $validator->errors()->add('responsible_ids', 'All responsible persons must belong to your team.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Client name is required.',
            'profile_name.required' => 'Profile is required.',
            'issue.required' => 'Issue details are required.',
            'type.required' => 'Please select a type.',
            'responsible_ids.required' => 'Please select at least one responsible person.',
        ];
    }
}
