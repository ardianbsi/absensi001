<?php

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        $shifts = [
            [
                'name' => 'Pagi',
                'code' => 'MORNING',
                'type' => 'fixed',
                'clock_in_time' => '07:00:00',
                'clock_out_time' => '16:00:00',
                'color' => '#3498db',
            ],
            [
                'name' => 'Siang',
                'code' => 'AFTERNOON',
                'type' => 'fixed',
                'clock_in_time' => '13:00:00',
                'clock_out_time' => '22:00:00',
                'color' => '#e67e22',
            ],
            [
                'name' => 'Malam',
                'code' => 'NIGHT',
                'type' => 'rotating',
                'clock_in_time' => '22:00:00',
                'clock_out_time' => '07:00:00',
                'color' => '#2c3e50',
            ],
            [
                'name' => 'Flexible',
                'code' => 'FLEX',
                'type' => 'flexible',
                'clock_in_time' => '08:00:00',
                'clock_out_time' => '17:00:00',
                'color' => '#27ae60',
            ],
            [
                'name' => 'Custom',
                'code' => 'CUSTOM',
                'type' => 'flexible',
                'clock_in_time' => '09:00:00',
                'clock_out_time' => '18:00:00',
                'color' => '#9b59b6',
            ],
        ];

        $shift = $this->faker->unique()->randomElement($shifts);

        return $shift + [
            'late_tolerance_minutes' => 15,
            'max_early_clock_in' => 30,
            'is_active' => true,
            'description' => $this->faker->sentence(),
        ];
    }
}
