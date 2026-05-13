<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function stats(): JsonResponse
    {
        try {
            $user = auth()->user();
            $role = $user->getRoleNames()->first() ?? 'employee';

            $data = [
                'general_stats' => $this->dashboardService->getStats(),
            ];

            if ($role === 'manager') {
                $data['team_stats'] = $this->dashboardService->getTeamStats($user);
            }

            $data['personal_stats'] = $this->dashboardService->getEmployeeStats($user);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard stats: ' . $e->getMessage(),
            ], 500);
        }
    }
}
