<?php

namespace App\Models;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'quota',
        'is_paid',
        'is_active',
    ];

    protected $casts = [
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
