<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'calculation_type',
        'value',
        'description',
        'is_active',
    ];

    protected $casts = [
            'is_active' => 'boolean',
    ];
}
