<?php

namespace App\Models;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_out',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_in_selfie',
        'clock_out_selfie',
        'clock_in_note',
        'clock_out_note',
        'clock_in_ip',
        'clock_out_ip',
        'clock_in_device',
        'clock_out_device',
        'status',
        'is_late',
        'late_minutes',
        'early_leave_minutes',
        'is_early_leave',
        'total_work_hours',
    ];

    protected $casts = [
            'date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'is_late' => 'boolean',
            'is_early_leave' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
