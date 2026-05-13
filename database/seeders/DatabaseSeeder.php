<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            MenuSeeder::class,
            MenuRoleAssignmentSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            ShiftSeeder::class,
            LeaveTypeSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            AttendanceSeeder::class,
            LeaveRequestSeeder::class,
            OvertimeSeeder::class,
        ]);
    }
}
