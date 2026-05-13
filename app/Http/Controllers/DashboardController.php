<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'employee';

        if ($role === 'admin' || $role === 'super_admin') {
            $stats = $this->dashboardService->getHrDashboard();
            return view('dashboard.admin', compact('stats'));
        }

        if ($role === 'manager') {
            $stats = $this->dashboardService->getTodayStats();
            $teamStats = $this->dashboardService->getManagerDashboard($user->employee->id);
            return view('dashboard.manager', compact('stats', 'teamStats'));
        }

        $stats = $this->dashboardService->getTodayStats();
        $employeeStats = $this->dashboardService->getEmployeeDashboard($user->employee->id);
        return view('dashboard.employee', compact('stats', 'employeeStats'));
    }
}
