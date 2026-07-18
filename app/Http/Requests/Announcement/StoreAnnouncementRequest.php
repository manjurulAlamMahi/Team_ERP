<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:info,warning,urgent'],
            'ends_at' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Announcement title is required.',
            'description.required' => 'Announcement description is required.',
            'priority.in' => 'Priority must be Info, Warning or Urgent.',
            'ends_at.required' => 'End date is required.',
            'ends_at.after_or_equal' => 'End date cannot be in the past.',
        ];
    }
}
