<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\LeaveApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('dashboard/stats', [DashboardApiController::class, 'stats']);

        Route::get('attendance', [AttendanceApiController::class, 'index']);
        Route::post('attendance/check-in', [AttendanceApiController::class, 'checkIn']);
        Route::post('attendance/check-out', [AttendanceApiController::class, 'checkOut']);
        Route::get('attendance/today', [AttendanceApiController::class, 'today']);
        Route::get('attendance/history', [AttendanceApiController::class, 'history']);

        Route::apiResource('leaves', LeaveApiController::class);
        Route::post('leaves/{id}/approve', [LeaveApiController::class, 'approve']);
        Route::post('leaves/{id}/reject', [LeaveApiController::class, 'reject']);

        Route::apiResource('employees', EmployeeApiController::class);
    });
});
