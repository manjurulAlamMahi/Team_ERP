<?php

namespace App\Http\Requests\TodayPlan;

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
            'client_name' => ['required', 'string', 'max:255'],
            'profile_name' => ['required', 'string', 'max:255'],
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
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a team member.',
            'client_name.required' => 'Client name is required.',
            'profile_name.required' => 'Profile is required.',
            'details.required' => 'Task details are required.',
        ];
    }
}
