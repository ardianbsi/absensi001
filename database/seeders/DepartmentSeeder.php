<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'IT', 'name' => 'Teknologi Informasi', 'description' => 'Information Technology Department'],
            ['code' => 'HR', 'name' => 'Sumber Daya Manusia', 'description' => 'Human Resources Department'],
            ['code' => 'FIN', 'name' => 'Keuangan', 'description' => 'Finance Department'],
            ['code' => 'MKT', 'name' => 'Pemasaran', 'description' => 'Marketing Department'],
            ['code' => 'OPS', 'name' => 'Operasional', 'description' => 'Operations Department'],
            ['code' => 'SLS', 'name' => 'Penjualan', 'description' => 'Sales Department'],
            ['code' => 'LGL', 'name' => 'Legal', 'description' => 'Legal Department'],
            ['code' => 'RND', 'name' => 'Penelitian & Pengembangan', 'description' => 'Research & Development Department'],
            ['code' => 'PRC', 'name' => 'Pengadaan', 'description' => 'Procurement Department'],
            ['code' => 'GA', 'name' => 'Umum & Administrasi', 'description' => 'General Affairs Department'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept + ['is_active' => true]);
        }
    }
}
