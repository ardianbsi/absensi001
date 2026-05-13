<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'selfie' => ['required', 'image', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'selfie.required' => 'Selfie photo is required for check-in.',
            'selfie.image' => 'Selfie must be an image file.',
            'selfie.max' => 'Selfie must not exceed 2MB.',
        ];
    }
}
