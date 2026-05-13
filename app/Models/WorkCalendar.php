<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'type',
        'description',
        'is_national_holiday',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_national_holiday' => 'boolean',
        ];
    }
}
