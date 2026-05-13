<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ShiftSchedule;

class ScheduleService
{

    public function assignShift(int $employeeId, int $shiftId, string $date): ShiftSchedule
    {
        return ShiftSchedule::create([
            'employee_id' => $employeeId,
            'shift_id' => $shiftId,
            'date' => $date,
        ]);
    }

    public function massAssignShift(int $departmentId, int $shiftId, string $startDate, string $endDate): int
    {
        $employees = Employee::where('department_id', $departmentId)->pluck('id');
        $dates = [];

        $current = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        $count = 0;
        foreach ($employees as $employeeId) {
            foreach ($dates as $date) {
                ShiftSchedule::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $date,
                    ],
                    [
                        'shift_id' => $shiftId,
                    ]
                );
                $count++;
            }
        }

        return $count;
    }

    public function overrideSchedule(int $scheduleId, int $shiftId, string $reason): ShiftSchedule
    {
        $schedule = ShiftSchedule::findOrFail($scheduleId);
        $schedule->update([
            'shift_id' => $shiftId,
            'is_override' => true,
        ]);

        return $schedule->fresh();
    }
}
