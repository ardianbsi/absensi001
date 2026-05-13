<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:2048', 'mimes:pdf,jpg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Start date must be today or later.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'attachment.mimes' => 'Attachment must be a PDF, JPG, or PNG file.',
        ];
    }
}
