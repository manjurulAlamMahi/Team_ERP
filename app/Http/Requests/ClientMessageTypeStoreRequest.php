<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ClientMessageTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('client_message_type_create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:client_message_types,name',
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
