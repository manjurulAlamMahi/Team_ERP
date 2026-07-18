<?php

namespace App\Http\Requests\TeamSheet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:team_sheets,id'],
            'title' => ['required', 'string', 'max:255'],
            'link' => ['required', 'url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Sheet title is required.',
            'link.required' => 'Sheet link is required.',
            'link.url' => 'Sheet link must be a valid URL.',
        ];
    }
}
