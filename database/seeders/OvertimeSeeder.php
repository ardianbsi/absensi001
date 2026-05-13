<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\OvertimeRequest;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class OvertimeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $employees = Employee::all();

        foreach ($employees as $employee) {
            if (rand(1, 100) > 60) {
                continue;
            }

            $numOvertimes = rand(1, 5);

            for ($i = 0; $i < $numOvertimes; $i++) {
                $date = $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d');
                $startTime = $faker->dateTimeBetween(
                    $date . ' 17:00:00',
                    $date . ' 20:00:00'
                );
                $endTime = (clone $startTime)->modify('+' . rand(2, 4) . ' hours');
                $totalHours = round(($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600, 2);

                OvertimeRequest::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_hours' => $totalHours,
                    'reason' => $faker->paragraph(),
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
                ]);
            }
        }
    }
}
