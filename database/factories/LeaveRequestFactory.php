<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d');
        $endDate = date('Y-m-d', strtotime($startDate . ' +' . rand(1, 5) . ' days'));

        $totalDays = (int) (strtotime($endDate) - strtotime($startDate)) / 86400 + 1;

        return [
            'employee_id' => $this->faker->randomElement(\App\Models\Employee::pluck('id')->toArray()),
            'leave_type_id' => $this->faker->randomElement(\App\Models\LeaveType::pluck('id')->toArray()),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'reason' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
        ];
    }
}
