<?php

namespace App\Http\Requests\DailyReminder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:daily_reminders,id'],
            'details' => ['required', 'string', 'max:2000'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'due_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->due_date && $this->due_time
                && \Carbon\Carbon::parse($this->due_date . ' ' . $this->due_time)->isPast()) {
                $validator->errors()->add('due_time', 'Due date and time cannot be in the past.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Reminder details are required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
            'due_time.required' => 'Due time is required.',
            'due_time.date_format' => 'Due time must be a valid time.',
        ];
    }
}
