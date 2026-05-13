<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Pagi',
                'code' => 'MORNING',
                'type' => 'fixed',
                'clock_in_time' => '07:00:00',
                'clock_out_time' => '16:00:00',
                'late_tolerance_minutes' => 15,
                'max_early_clock_in' => 30,
                'color' => '#3498db',
                'description' => 'Shift pagi: 07:00 - 16:00',
            ],
            [
                'name' => 'Siang',
                'code' => 'AFTERNOON',
                'type' => 'fixed',
                'clock_in_time' => '13:00:00',
                'clock_out_time' => '22:00:00',
                'late_tolerance_minutes' => 15,
                'max_early_clock_in' => 30,
                'color' => '#e67e22',
                'description' => 'Shift siang: 13:00 - 22:00',
            ],
            [
                'name' => 'Malam',
                'code' => 'NIGHT',
                'type' => 'rotating',
                'clock_in_time' => '22:00:00',
                'clock_out_time' => '07:00:00',
                'late_tolerance_minutes' => 15,
                'max_early_clock_in' => 30,
                'color' => '#2c3e50',
                'description' => 'Shift malam: 22:00 - 07:00',
            ],
            [
                'name' => 'Flexible',
                'code' => 'FLEX',
                'type' => 'flexible',
                'clock_in_time' => '08:00:00',
                'clock_out_time' => '17:00:00',
                'late_tolerance_minutes' => 30,
                'max_early_clock_in' => 60,
                'color' => '#27ae60',
                'description' => 'Shift flexible: 08:00 - 17:00',
            ],
            [
                'name' => 'Custom',
                'code' => 'CUSTOM',
                'type' => 'flexible',
                'clock_in_time' => '09:00:00',
                'clock_out_time' => '18:00:00',
                'late_tolerance_minutes' => 30,
                'max_early_clock_in' => 60,
                'color' => '#9b59b6',
                'description' => 'Shift custom: 09:00 - 18:00',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift + ['is_active' => true]);
        }
    }
}
