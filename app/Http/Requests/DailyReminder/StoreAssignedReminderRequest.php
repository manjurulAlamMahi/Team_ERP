<?php

namespace App\Http\Requests\DailyReminder;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAssignedReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'details' => ['required', 'string', 'max:2000'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $actor = Auth::user();
            $targetId = $this->input('user_id');

            if (!$targetId) {
                return;
            }

            if ((int) $targetId === $actor->id) {
                $validator->errors()->add('user_id', 'You cannot assign a reminder to yourself here.');
                return;
            }

            $target = User::where('id', $targetId)->where('team_id', $actor->team_id)->first();

            if (!$target) {
                $validator->errors()->add('user_id', 'The selected member must belong to your team.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a team member.',
            'details.required' => 'Reminder details are required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }
}
