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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'details' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Please select a client.',
            'details.required' => 'Plan details are required.',
        ];
    }
}
