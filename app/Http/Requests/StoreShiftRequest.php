<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'unique:shifts,code'],
            'type' => ['required', 'in:fixed,flexible,rotating'],
            'clock_in_time' => ['required', 'date_format:H:i'],
            'clock_out_time' => ['required', 'date_format:H:i'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0'],
            'max_early_clock_in' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
        ];
    }
}
