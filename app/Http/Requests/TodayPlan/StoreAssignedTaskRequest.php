<?php

namespace App\Http\Requests\TodayPlan;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAssignedTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'details' => ['required', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $target = User::find($this->input('user_id'));
            $leader = Auth::user();

            if (!$target || !$leader || $target->team_id !== $leader->team_id) {
                $validator->errors()->add('user_id', 'Selected member must belong to your team.');
                return;
            }

            $clientId = $this->input('client_id');
            if ($clientId && !Client::where('id', $clientId)->assignedTo($target->id)->exists()) {
                $validator->errors()->add('client_id', 'Selected client is not assigned to this member.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a team member.',
            'client_id.required' => 'Please select a client.',
            'details.required' => 'Task details are required.',
        ];
    }
}
