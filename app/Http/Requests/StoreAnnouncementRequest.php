<?php

namespace App\Http\Requests;

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
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,important,urgent'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Type must be one of: info, warning, important, or urgent.',
        ];
    }
}
