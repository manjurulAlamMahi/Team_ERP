<?php

namespace App\Http\Requests\TodayPlan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.client_name' => ['required', 'string', 'max:255'],
            'items.*.profile_name' => ['required', 'string', 'max:255'],
            'items.*.details' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one plan.',
            'items.*.client_name.required' => 'Client name is required for every plan.',
            'items.*.profile_name.required' => 'Profile is required for every plan.',
            'items.*.details.required' => 'Plan details are required for every plan.',
        ];
    }
}
