<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\PayrollComponent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'payroll_component_id',
        'effective_date',
        'value',
        'is_active',
    ];

    protected $casts = [
            'effective_date' => 'date',
            'is_active' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollComponent(): BelongsTo
    {
        return $this->belongsTo(PayrollComponent::class);
    }
}
