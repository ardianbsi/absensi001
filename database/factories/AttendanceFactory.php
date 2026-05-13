<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $employee = Employee::inRandomOrder()->first();
        $shift = Shift::inRandomOrder()->first();

        $date = $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d');
        return $this->generateAttendance($employee->id, $shift->id, $date);
    }

    public function untukTanggal(string $date, int $shiftId): static
    {
        $employee = Employee::inRandomOrder()->first();
        $data = $this->generateAttendance($employee->id, $shiftId, $date);
        return $this->state(fn (array $attributes) => $data);
    }

    private function generateAttendance(int $employeeId, int $shiftId, string $date): array
    {
        $shift = Shift::find($shiftId);
        $shiftStart = $shift->clock_in_time;
        $shiftEnd = $shift->clock_out_time;

        $clockInDateTime = $this->faker->dateTimeBetween(
            $date . ' ' . $shiftStart,
            $date . ' ' . date('H:i:s', strtotime($shiftStart . '+2 hours'))
        );

        $isOvernight = strtotime($shiftEnd) < strtotime($shiftStart);

        if ($isOvernight) {
            $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
            $endMinus = $nextDate . ' ' . date('H:i:s', strtotime($shiftEnd . '-2 hours'));
            $endPlus = $nextDate . ' ' . date('H:i:s', strtotime($shiftEnd . '+1 hour'));
            $clockOutDateTime = $this->faker->dateTimeBetween($endMinus, $endPlus);
        } else {
            $endMinus = $date . ' ' . date('H:i:s', strtotime($shiftEnd . '-2 hours'));
            $endPlus = $date . ' ' . date('H:i:s', strtotime($shiftEnd . '+1 hour'));
            $clockOutDateTime = $this->faker->dateTimeBetween($endMinus, $endPlus);
        }

        $lateMinutes = 0;
        $isLate = false;
        $toleranceTime = date('H:i:s', strtotime($shiftStart . '+15 minutes'));

        if (date('H:i:s', $clockInDateTime->getTimestamp()) > $toleranceTime) {
            $isLate = true;
            $lateMinutes = (int) ((strtotime(date('H:i:s', $clockInDateTime->getTimestamp())) - strtotime($shiftStart)) / 60);
        }

        $status = $isLate ? 'telat' : 'hadir';

        $workSeconds = $clockOutDateTime->getTimestamp() - $clockInDateTime->getTimestamp();
        $totalWorkHours = round(max($workSeconds, 0) / 3600, 2);

        return [
            'employee_id' => $employeeId,
            'shift_id' => $shiftId,
            'date' => $date,
            'clock_in' => $clockInDateTime,
            'clock_out' => $clockOutDateTime,
            'status' => $status,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => 0,
            'is_early_leave' => false,
            'total_work_hours' => $totalWorkHours,
            'clock_in_note' => $this->faker->optional(0.3)->sentence(),
            'clock_out_note' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
