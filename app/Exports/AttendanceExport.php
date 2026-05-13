<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Employee Name',
            'Department',
            'Position',
            'Date',
            'Clock In',
            'Clock Out',
            'Status',
            'Late (mins)',
            'Early Leave (mins)',
            'Work Hours',
        ];
    }

    public function map($row): array
    {
        return [
            $row->employee?->nik,
            $row->employee?->full_name,
            $row->employee?->department?->name,
            $row->employee?->position?->name,
            $row->date,
            $row->clock_in ? $row->clock_in->format('H:i:s') : '-',
            $row->clock_out ? $row->clock_out->format('H:i:s') : '-',
            $row->status,
            $row->late_minutes,
            $row->early_leave_minutes,
            $row->total_work_hours,
        ];
    }
}
