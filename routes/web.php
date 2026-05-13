<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    Route::middleware(['permission:employee-read'])->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    });
    Route::middleware(['permission:employee-create'])->group(function () {
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    });
    Route::middleware(['permission:employee-read'])->group(function () {
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    });
    Route::middleware(['permission:employee-update'])->group(function () {
        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    });
    Route::middleware(['permission:employee-delete'])->group(function () {
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('employees/{id}/force', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
    });
    Route::middleware(['permission:employee-read'])->group(function () {
        Route::get('employees/export/excel', [EmployeeController::class, 'export'])->name('employees.export');
    });
    Route::middleware(['permission:employee-create'])->group(function () {
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    });

    Route::middleware(['permission:department-read'])->group(function () {
        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
    });
    Route::middleware(['permission:department-create'])->group(function () {
        Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
    });
    Route::middleware(['permission:department-read'])->group(function () {
        Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    });
    Route::middleware(['permission:department-update'])->group(function () {
        Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    });
    Route::middleware(['permission:department-delete'])->group(function () {
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    Route::middleware(['permission:position-read'])->group(function () {
        Route::get('positions', [PositionController::class, 'index'])->name('positions.index');
    });
    Route::middleware(['permission:position-create'])->group(function () {
        Route::get('positions/create', [PositionController::class, 'create'])->name('positions.create');
        Route::post('positions', [PositionController::class, 'store'])->name('positions.store');
    });
    Route::middleware(['permission:position-read'])->group(function () {
        Route::get('positions/{position}', [PositionController::class, 'show'])->name('positions.show');
    });
    Route::middleware(['permission:position-update'])->group(function () {
        Route::get('positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
        Route::put('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
    });
    Route::middleware(['permission:position-delete'])->group(function () {
        Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');
    });

    Route::get('attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('attendance/check-out/{attendance}', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    Route::middleware(['permission:attendance-read'])->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    });
    Route::middleware(['permission:attendance-update'])->group(function () {
        Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    });
    Route::middleware(['permission:attendance-delete'])->group(function () {
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    });
    Route::middleware(['permission:attendance-read'])->group(function () {
        Route::get('attendance/export/excel', [AttendanceController::class, 'export'])->name('attendance.export');
    });

    Route::middleware(['permission:shift-read'])->group(function () {
        Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
    });
    Route::middleware(['permission:shift-create'])->group(function () {
        Route::get('shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
        Route::post('shifts', [ShiftController::class, 'store'])->name('shifts.store');
    });
    Route::middleware(['permission:shift-read'])->group(function () {
        Route::get('shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
    });
    Route::middleware(['permission:shift-update'])->group(function () {
        Route::get('shifts/{shift}/edit', [ShiftController::class, 'edit'])->name('shifts.edit');
        Route::put('shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
    });
    Route::middleware(['permission:shift-delete'])->group(function () {
        Route::delete('shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
    });

    Route::middleware(['permission:schedule-read'])->group(function () {
        Route::get('schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    });
    Route::middleware(['permission:schedule-create'])->group(function () {
        Route::post('schedule/assign', [ScheduleController::class, 'assign'])->name('schedule.assign');
        Route::post('schedule/mass-assign', [ScheduleController::class, 'massAssign'])->name('schedule.mass-assign');
    });
    Route::middleware(['permission:schedule-update'])->group(function () {
        Route::post('schedule/override/{schedule}', [ScheduleController::class, 'override'])->name('schedule.override');
    });

    Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::get('leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
    Route::put('leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
    Route::middleware(['permission:leave-approve'])->group(function () {
        Route::post('leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    });
    Route::post('leaves/{id}/cancel', [LeaveController::class, 'cancel'])->name('leaves.cancel');
    Route::middleware(['permission:leave-read'])->group(function () {
        Route::get('leaves/export/excel', [LeaveController::class, 'export'])->name('leaves.export');
    });

    Route::get('overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('overtimes/create', [OvertimeController::class, 'create'])->name('overtimes.create');
    Route::post('overtimes', [OvertimeController::class, 'store'])->name('overtimes.store');
    Route::get('overtimes/{overtime}', [OvertimeController::class, 'show'])->name('overtimes.show');
    Route::get('overtimes/{overtime}/edit', [OvertimeController::class, 'edit'])->name('overtimes.edit');
    Route::put('overtimes/{overtime}', [OvertimeController::class, 'update'])->name('overtimes.update');
    Route::delete('overtimes/{overtime}', [OvertimeController::class, 'destroy'])->name('overtimes.destroy');
    Route::middleware(['permission:overtime-approve'])->group(function () {
        Route::post('overtimes/{id}/approve', [OvertimeController::class, 'approve'])->name('overtimes.approve');
        Route::post('overtimes/{id}/reject', [OvertimeController::class, 'reject'])->name('overtimes.reject');
    });
    Route::post('overtimes/{id}/cancel', [OvertimeController::class, 'cancel'])->name('overtimes.cancel');
    Route::middleware(['permission:overtime-read'])->group(function () {
        Route::get('overtimes/export/excel', [OvertimeController::class, 'export'])->name('overtimes.export');
    });

    Route::middleware(['permission:holiday-read'])->group(function () {
        Route::get('holidays', [HolidayController::class, 'index'])->name('holidays.index');
    });
    Route::middleware(['permission:holiday-create'])->group(function () {
        Route::get('holidays/create', [HolidayController::class, 'create'])->name('holidays.create');
        Route::post('holidays', [HolidayController::class, 'store'])->name('holidays.store');
    });
    Route::middleware(['permission:holiday-read'])->group(function () {
        Route::get('holidays/{holiday}', [HolidayController::class, 'show'])->name('holidays.show');
    });
    Route::middleware(['permission:holiday-update'])->group(function () {
        Route::get('holidays/{holiday}/edit', [HolidayController::class, 'edit'])->name('holidays.edit');
        Route::put('holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
    });
    Route::middleware(['permission:holiday-delete'])->group(function () {
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
    });

    Route::middleware(['permission:announcement-read'])->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    });
    Route::middleware(['permission:announcement-create'])->group(function () {
        Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    });
    Route::middleware(['permission:announcement-read'])->group(function () {
        Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    });
    Route::middleware(['permission:announcement-update'])->group(function () {
        Route::get('announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    });
    Route::middleware(['permission:announcement-delete'])->group(function () {
        Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    Route::middleware(['permission:report-read'])->group(function () {
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('report.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('report.monthly');
        Route::get('reports/employee', [ReportController::class, 'employee'])->name('report.employee');
        Route::get('reports/export-pdf/{type}', [ReportController::class, 'exportPdf'])->name('report.export-pdf');
        Route::get('reports/export-excel/{type}', [ReportController::class, 'exportExcel'])->name('report.export-excel');
    });

    Route::middleware(['role:Super Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('roles', RoleController::class);
        Route::resource('menus', MenuController::class);
    });

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');

    Route::post('theme/toggle', function (\Illuminate\Http\Request $request) {
        $theme = $request->input('theme', 'light');
        session(['theme' => $theme]);
        return response()->json(['success' => true]);
    })->name('theme.toggle');
});

require __DIR__ . '/auth.php';
