<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $employees = Employee::all();
        $shifts = Shift::all();

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $statuses = ['hadir', 'telat', 'izin', 'sakit'];

        foreach ($employees as $employee) {
            $current = $startDate->copy();

            while ($current->lte($endDate)) {
                if ($current->isSaturday() || $current->isSunday()) {
                    $current->addDay();
                    continue;
                }

                $shift = $shifts->random();
                $status = $faker->randomElement($statuses);
                $dateStr = $current->format('Y-m-d');
                $shiftStart = $shift->clock_in_time;
                $shiftEnd = $shift->clock_out_time;
                $isOvernight = strtotime($shiftEnd) < strtotime($shiftStart);

                if ($status === 'izin' || $status === 'sakit') {
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $dateStr,
                        'status' => $status,
                        'is_late' => false,
                        'late_minutes' => 0,
                        'total_work_hours' => 0,
                        'clock_in_note' => $status === 'sakit' ? 'Sakit, tidak masuk' : 'Izin, tidak masuk',
                    ]);
                } else {
                    $clockInStart = $dateStr . ' ' . $shiftStart;
                    $clockInEnd = $dateStr . ' ' . date('H:i:s', strtotime($shiftStart . '+2 hours'));

                    if ($isOvernight) {
                        $clockInEnd = date('Y-m-d', strtotime($dateStr . ' +1 day')) . ' 00:00:00';
                    }

                    $clockInDateTime = $faker->dateTimeBetween($clockInStart, $clockInEnd);

                    if ($isOvernight) {
                        $nextDate = date('Y-m-d', strtotime($dateStr . ' +1 day'));
                        $endMinus = $nextDate . ' ' . date('H:i:s', strtotime($shiftEnd . '-2 hours'));
                        $endPlus = $nextDate . ' ' . date('H:i:s', strtotime($shiftEnd . '+1 hour'));
                    } else {
                        $endMinus = $dateStr . ' ' . date('H:i:s', strtotime($shiftEnd . '-2 hours'));
                        $endPlus = $dateStr . ' ' . date('H:i:s', strtotime($shiftEnd . '+1 hour'));
                    }
                    $clockOutDateTime = $faker->dateTimeBetween($endMinus, $endPlus);

                    $isLate = false;
                    $lateMinutes = 0;
                    $toleranceTime = date('H:i:s', strtotime($shiftStart . '+15 minutes'));

                    if (date('H:i:s', $clockInDateTime->getTimestamp()) > $toleranceTime) {
                        $isLate = true;
                        $lateMinutes = (int) ((strtotime(date('H:i:s', $clockInDateTime->getTimestamp())) - strtotime($shiftStart)) / 60);
                    }

                    $workSeconds = $clockOutDateTime->getTimestamp() - $clockInDateTime->getTimestamp();
                    $totalWorkHours = round(max($workSeconds, 0) / 3600, 2);

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'shift_id' => $shift->id,
                        'date' => $dateStr,
                        'clock_in' => $clockInDateTime,
                        'clock_out' => $clockOutDateTime,
                        'status' => $isLate ? 'telat' : 'hadir',
                        'is_late' => $isLate,
                        'late_minutes' => $lateMinutes,
                        'total_work_hours' => $totalWorkHours,
                    ]);
                }

                $current->addDay();
            }
        }
    }
}
