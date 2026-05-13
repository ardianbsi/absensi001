<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'total_hours',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approval_reason',
    ];

    protected $casts = [
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
