<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuGroup;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Menu Groups
        $dashboardGroup = MenuGroup::create(['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'order' => 1]);
        $masterDataGroup = MenuGroup::create(['name' => 'Master Data', 'icon' => 'fas fa-database', 'order' => 2]);
        $attendanceGroup = MenuGroup::create(['name' => 'Attendance', 'icon' => 'fas fa-fingerprint', 'order' => 3]);
        $hrGroup = MenuGroup::create(['name' => 'HR Management', 'icon' => 'fas fa-users-cog', 'order' => 4]);
        $reportsGroup = MenuGroup::create(['name' => 'Reports', 'icon' => 'fas fa-chart-bar', 'order' => 5]);
        $settingsGroup = MenuGroup::create(['name' => 'Settings', 'icon' => 'fas fa-cog', 'order' => 6]);

        // Dashboard
        Menu::create(['menu_group_id' => $dashboardGroup->id, 'name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route' => 'dashboard', 'order' => 1]);

        // Master Data
        Menu::create(['menu_group_id' => $masterDataGroup->id, 'name' => 'Employees', 'icon' => 'fas fa-users', 'route' => 'employees.index', 'order' => 1]);
        Menu::create(['menu_group_id' => $masterDataGroup->id, 'name' => 'Departments', 'icon' => 'fas fa-building', 'route' => 'departments.index', 'order' => 2]);
        Menu::create(['menu_group_id' => $masterDataGroup->id, 'name' => 'Positions', 'icon' => 'fas fa-briefcase', 'route' => 'positions.index', 'order' => 3]);

        // Attendance
        Menu::create(['menu_group_id' => $attendanceGroup->id, 'name' => 'Check In/Out', 'icon' => 'fas fa-fingerprint', 'route' => 'attendance.scan', 'order' => 1]);
        Menu::create(['menu_group_id' => $attendanceGroup->id, 'name' => 'Attendance List', 'icon' => 'fas fa-list', 'route' => 'attendance.index', 'order' => 2]);
        Menu::create(['menu_group_id' => $attendanceGroup->id, 'name' => 'Shifts', 'icon' => 'fas fa-clock', 'route' => 'shifts.index', 'order' => 3]);
        Menu::create(['menu_group_id' => $attendanceGroup->id, 'name' => 'Schedule', 'icon' => 'fas fa-calendar-alt', 'route' => 'schedule.index', 'order' => 4]);

        // HR Management
        Menu::create(['menu_group_id' => $hrGroup->id, 'name' => 'Leave Requests', 'icon' => 'fas fa-file-alt', 'route' => 'leaves.index', 'order' => 1]);
        Menu::create(['menu_group_id' => $hrGroup->id, 'name' => 'Overtime', 'icon' => 'fas fa-clock', 'route' => 'overtimes.index', 'order' => 2]);
        Menu::create(['menu_group_id' => $hrGroup->id, 'name' => 'Holidays', 'icon' => 'fas fa-calendar-day', 'route' => 'holidays.index', 'order' => 3]);
        Menu::create(['menu_group_id' => $hrGroup->id, 'name' => 'Announcements', 'icon' => 'fas fa-bullhorn', 'route' => 'announcements.index', 'order' => 4]);

        // Reports
        Menu::create(['menu_group_id' => $reportsGroup->id, 'name' => 'Daily Report', 'icon' => 'fas fa-file-day', 'route' => 'report.daily', 'order' => 1]);
        Menu::create(['menu_group_id' => $reportsGroup->id, 'name' => 'Monthly Report', 'icon' => 'fas fa-file-month', 'route' => 'report.monthly', 'order' => 2]);
        Menu::create(['menu_group_id' => $reportsGroup->id, 'name' => 'Employee Report', 'icon' => 'fas fa-file-user', 'route' => 'report.employee', 'order' => 3]);

        // Settings
        Menu::create(['menu_group_id' => $settingsGroup->id, 'name' => 'Users', 'icon' => 'fas fa-user-shield', 'route' => 'users.index', 'order' => 1]);
        Menu::create(['menu_group_id' => $settingsGroup->id, 'name' => 'Roles', 'icon' => 'fas fa-user-tag', 'route' => 'roles.index', 'order' => 2]);
        Menu::create(['menu_group_id' => $settingsGroup->id, 'name' => 'Menu Management', 'icon' => 'fas fa-bars', 'route' => 'menus.index', 'order' => 3]);
        Menu::create(['menu_group_id' => $settingsGroup->id, 'name' => 'Logout', 'icon' => 'fas fa-sign-out-alt', 'route' => 'logout', 'order' => 4]);
    }
}
