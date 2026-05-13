<?php

namespace Database\Factories;

use App\Models\OvertimeRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class OvertimeRequestFactory extends Factory
{
    protected $model = OvertimeRequest::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d');
        $startTime = $this->faker->dateTimeBetween(
            $date . ' 17:00:00',
            $date . ' 20:00:00'
        );
        $endTime = (clone $startTime)->modify('+' . rand(2, 4) . ' hours');
        $totalHours = round(($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600, 2);

        return [
            'employee_id' => $this->faker->randomElement(\App\Models\Employee::pluck('id')->toArray()),
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_hours' => $totalHours,
            'reason' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
        ];
    }
}
