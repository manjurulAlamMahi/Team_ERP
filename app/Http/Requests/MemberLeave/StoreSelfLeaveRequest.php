<?php

namespace App\Http\Requests\MemberLeave;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelfLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:absent,leave,home_office'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be Absent, Leave or Home Office.',
            'start_date.required' => 'Start date is required.',
            'end_date.after_or_equal' => 'End date cannot be before the start date.',
            'reason.required' => 'Please provide a reason.',
        ];
    }
}
