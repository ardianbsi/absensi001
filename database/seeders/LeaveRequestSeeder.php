<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $employees = Employee::all();
        $leaveTypes = LeaveType::all();

        foreach ($employees as $employee) {
            $numLeaves = rand(1, 3);

            for ($i = 0; $i < $numLeaves; $i++) {
                $startDate = Carbon::instance($faker->dateTimeBetween('now', '+3 months'));
                $endDate = $startDate->copy()->addDays(rand(1, 5));

                $totalDays = $startDate->diffInDays($endDate) + 1;

                LeaveRequest::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveTypes->random()->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'total_days' => $totalDays,
                    'reason' => $faker->paragraph(),
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
                ]);
            }
        }
    }
}
