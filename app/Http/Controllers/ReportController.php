<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function daily(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : today();
        $departmentId = $request->department_id;

        $attendances = $this->reportService->getDailyReport($date, $departmentId);
        $departments = Department::pluck('name', 'id');
        $summary = $this->reportService->getDailySummary($date, $departmentId);

        return view('reports.daily', compact('attendances', 'departments', 'summary', 'date'));
    }

    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $departmentId = $request->department_id;

        $records = $this->reportService->getMonthlyReport($year, $month, $departmentId);
        $departments = Department::pluck('name', 'id');
        $summary = $this->reportService->getMonthlySummary($year, $month, $departmentId);

        return view('reports.monthly', compact('records', 'departments', 'summary', 'year', 'month'));
    }

    public function employee(Request $request)
    {
        $employees = Employee::with(['user', 'department', 'position']);

        if ($request->filled('department_id')) {
            $employees->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $employees->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $employees = $employees->paginate(15);
        $departments = Department::pluck('name', 'id');

        return view('reports.employee', compact('employees', 'departments'));
    }

    public function exportPdf($type, Request $request)
    {
        try {
            return $this->reportService->exportPdf($type, $request->all());
        } catch (\Exception $e) {
            return back()->with('toast_error', 'PDF export failed: ' . $e->getMessage());
        }
    }

    public function exportExcel($type, Request $request)
    {
        try {
            return $this->reportService->exportExcel($type, $request->all());
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Excel export failed: ' . $e->getMessage());
        }
    }
}
