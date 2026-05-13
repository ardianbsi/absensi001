<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'nik' => ['required', 'string', Rule::unique('employees', 'nik')->ignore($employeeId)],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('employees', 'email')->ignore($employeeId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'employment_status' => ['required', 'in:permanent,contract,intern,probation'],
            'join_date' => ['required', 'date'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.unique' => 'NIK already exists in the system.',
            'email.unique' => 'Email already exists in the system.',
            'photo.max' => 'Photo must not exceed 2MB.',
        ];
    }
}
