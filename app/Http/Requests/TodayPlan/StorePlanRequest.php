<?php

namespace App\Http\Requests\TodayPlan;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'items.*.client_id' => ['required', 'integer', 'exists:clients,id'],
            'items.*.details' => ['required', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $clientId = $item['client_id'] ?? null;
                if ($clientId && $user && !Client::where('id', $clientId)->where('team_id', $user->team_id)->exists()) {
                    $validator->errors()->add("items.$index.client_id", 'You may only select a client from your team.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one plan.',
            'items.*.client_id.required' => 'Client is required for every plan.',
            'items.*.details.required' => 'Plan details are required for every plan.',
        ];
    }
}
