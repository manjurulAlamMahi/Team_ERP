<?php

namespace App\Http\Requests\DailyIssue;

use App\Models\Client;
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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'issue' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'in:Critical,Urgent,High,Normal'],
            'category' => ['required', 'string', 'max:255'],
            'responsible_ids' => ['required', 'array', 'min:1'],
            'responsible_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $ids = $this->input('responsible_ids', []);

            if (!empty($ids) && User::whereIn('id', $ids)->where('team_id', $user?->team_id)->count() !== count($ids)) {
                $validator->errors()->add('responsible_ids', 'All responsible persons must belong to your team.');
            }

            $clientId = $this->input('client_id');
            if ($clientId && $user && !Client::where('id', $clientId)->assignedTo($user->id)->exists()) {
                $validator->errors()->add('client_id', 'You may only select a client assigned to you.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Please select a client.',
            'issue.required' => 'Remarks are required.',
            'type.required' => 'Please select an issue type.',
            'category.required' => 'Please select an issue.',
            'responsible_ids.required' => 'Please select at least one responsible person.',
        ];
    }
}
