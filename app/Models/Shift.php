<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'clock_in_time',
        'clock_out_time',
        'late_tolerance_minutes',
        'max_early_clock_in',
        'is_active',
        'color',
        'description',
    ];

    protected $casts = [
            'clock_in_time' => 'string',
            'clock_out_time' => 'string',
            'is_active' => 'boolean',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function assignedEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_shifts');
    }
}
