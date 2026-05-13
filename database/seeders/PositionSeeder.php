<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positionsByDept = [
            'IT' => [
                'IT Staff', 'Programmer', 'System Analyst', 'Database Administrator',
                'Network Engineer', 'IT Supervisor', 'IT Manager', 'IT Director',
                'IT Support Specialist', 'Security Engineer',
            ],
            'HR' => [
                'HR Staff', 'Recruitment Specialist', 'Training Coordinator',
                'HR Supervisor', 'HR Manager', 'Compensation & Benefit Specialist',
                'HR Director', 'Industrial Relations Specialist',
            ],
            'FIN' => [
                'Finance Staff', 'Accountant', 'Tax Specialist', 'Finance Supervisor',
                'Finance Manager', 'Finance Director', 'Treasurer', 'Internal Auditor',
            ],
            'MKT' => [
                'Marketing Staff', 'Brand Specialist', 'Digital Marketing',
                'Marketing Supervisor', 'Marketing Manager', 'Marketing Director',
                'Market Research Analyst', 'Content Creator',
            ],
            'OPS' => [
                'Operations Staff', 'Logistics Coordinator', 'Quality Control',
                'Operations Supervisor', 'Operations Manager', 'Operations Director',
                'Project Coordinator', 'Process Improvement Specialist',
            ],
            'SLS' => [
                'Sales Staff', 'Sales Representative', 'Account Executive',
                'Sales Supervisor', 'Sales Manager', 'Sales Director',
                'Business Development', 'Key Account Manager',
            ],
            'LGL' => [
                'Legal Staff', 'Legal Officer', 'Corporate Lawyer',
                'Legal Supervisor', 'Legal Manager', 'Legal Director',
                'Contract Specialist', 'Compliance Officer',
            ],
            'RND' => [
                'R&D Staff', 'Research Analyst', 'Product Developer',
                'R&D Supervisor', 'R&D Manager', 'R&D Director',
                'Innovation Specialist', 'Lab Technician',
            ],
            'PRC' => [
                'Procurement Staff', 'Purchasing Officer', 'Vendor Coordinator',
                'Procurement Supervisor', 'Procurement Manager',
                'Supply Chain Analyst', 'Procurement Director',
            ],
            'GA' => [
                'GA Staff', 'Facility Coordinator', 'Office Administrator',
                'GA Supervisor', 'GA Manager', 'Building Maintenance',
                'Receptionist', 'Driver',
            ],
        ];

        $departments = Department::all();

        foreach ($departments as $department) {
            $positions = $positionsByDept[$department->code] ?? ['Staff', 'Supervisor', 'Manager'];

            foreach ($positions as $i => $posName) {
                Position::create([
                    'department_id' => $department->id,
                    'code' => $department->code . '-POS-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'name' => $posName,
                    'description' => $posName . ' di ' . $department->name,
                    'is_active' => true,
                ]);
            }
        }
    }
}
