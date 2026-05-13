<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Cuti Tahunan', 'code' => 'ANNUAL', 'description' => 'Cuti tahunan karyawan dengan quota 12 hari', 'quota' => 12, 'is_paid' => true],
            ['name' => 'Sakit', 'code' => 'SICK', 'description' => 'Cuti sakit dengan keterangan dokter', 'quota' => 0, 'is_paid' => true],
            ['name' => 'Izin Pribadi', 'code' => 'PERSONAL', 'description' => 'Izin untuk keperluan pribadi', 'quota' => 0, 'is_paid' => false],
            ['name' => 'WFH', 'code' => 'WFH', 'description' => 'Work From Home', 'quota' => 0, 'is_paid' => true],
            ['name' => 'Dinas Luar', 'code' => 'DUTY', 'description' => 'Perjalanan dinas luar kantor', 'quota' => 0, 'is_paid' => true],
            ['name' => 'Cuti Melahirkan', 'code' => 'MATERNITY', 'description' => 'Cuti melahirkan 90 hari', 'quota' => 90, 'is_paid' => true],
        ];

        foreach ($types as $type) {
            LeaveType::create($type + ['is_active' => true]);
        }
    }
}
