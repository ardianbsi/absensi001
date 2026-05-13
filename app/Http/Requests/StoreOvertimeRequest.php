<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s', 'after:start_time'],
            'reason' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'End time must be after start time.',
        ];
    }
}
