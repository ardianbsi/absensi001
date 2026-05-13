<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'employee-create', 'employee-read', 'employee-update', 'employee-delete', 'employee-export',
            'attendance-create', 'attendance-read', 'attendance-update', 'attendance-delete',
            'shift-create', 'shift-read', 'shift-update', 'shift-delete',
            'leave-create', 'leave-read', 'leave-update', 'leave-delete', 'leave-approve',
            'overtime-create', 'overtime-read', 'overtime-update', 'overtime-delete', 'overtime-approve',
            'report-read', 'report-export',
            'user-create', 'user-read', 'user-update', 'user-delete',
            'role-create', 'role-read', 'role-update', 'role-delete',
            'department-create', 'department-read', 'department-update', 'department-delete',
            'position-create', 'position-read', 'position-update', 'position-delete',
            'holiday-create', 'holiday-read', 'holiday-update', 'holiday-delete',
            'announcement-create', 'announcement-read', 'announcement-update', 'announcement-delete',
            'setting-read', 'setting-update',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $superAdmin->givePermissionTo(Permission::all());

        $hr = Role::findOrCreate('HR', 'web');
        $hr->givePermissionTo([
            'employee-create', 'employee-read', 'employee-update', 'employee-delete', 'employee-export',
            'attendance-create', 'attendance-read', 'attendance-update', 'attendance-delete',
            'leave-create', 'leave-read', 'leave-update', 'leave-delete', 'leave-approve',
            'overtime-create', 'overtime-read', 'overtime-update', 'overtime-delete', 'overtime-approve',
            'shift-create', 'shift-read', 'shift-update', 'shift-delete',
            'report-read', 'report-export',
            'department-create', 'department-read', 'department-update', 'department-delete',
            'position-create', 'position-read', 'position-update', 'position-delete',
            'holiday-create', 'holiday-read', 'holiday-update', 'holiday-delete',
            'announcement-create', 'announcement-read', 'announcement-update', 'announcement-delete',
        ]);

        $manager = Role::findOrCreate('Manager', 'web');
        $manager->givePermissionTo([
            'employee-read',
            'attendance-read',
            'leave-read', 'leave-approve',
            'overtime-read', 'overtime-approve',
            'report-read',
        ]);

        $employee = Role::findOrCreate('Employee', 'web');
        $employee->givePermissionTo([
            'attendance-create', 'attendance-read',
            'leave-create', 'leave-read',
            'overtime-create', 'overtime-read',
        ]);
    }
}
