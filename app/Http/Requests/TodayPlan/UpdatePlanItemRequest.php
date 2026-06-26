<?php

namespace App\Http\Requests\TodayPlan;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:today_plan_tasks,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'profile_name' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Client name is required.',
            'profile_name.required' => 'Profile is required.',
            'details.required' => 'Plan details are required.',
        ];
    }
}
