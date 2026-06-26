<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClientMessageTypeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('client_message_type_edit');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:client_message_types,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('client_message_types', 'name')->ignore($this->id),
            ],
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'short_description' => 'nullable|string|max:255',
            'format' => 'required|string',
            'restriction' => 'required|string',
            'mandatory' => 'required|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'This message type name is already taken.',
            'format.required' => 'The format field is required.',
            'restriction.required' => 'The restriction field is required.',
            'mandatory.required' => 'The mandatory field is required.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
