@extends('layouts.app')

@section('title', 'Monthly Report')

@section('content')
    <x-breadcrumb :items="[['name' => 'Reports', 'route' => 'report.monthly'], ['name' => 'Monthly']]" />

    <x-page-header title="Monthly Attendance Report" :subtitle="Carbon\Carbon::create($year, $month)->format('F Y')">
        <a href="{{ route('report.export-excel', 'monthly') }}?year={{ $year }}&month={{ $month }}&department_id={{ request('department_id') }}" class="btn btn-outline-success">
            <i class="ti ti-file-spreadsheet"></i> Excel
        </a>
        <a href="{{ route('report.export-pdf', 'monthly') }}?year={{ $year }}&month={{ $month }}&department_id={{ request('department_id') }}" class="btn btn-outline-danger">
            <i class="ti ti-file-pdf"></i> PDF
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('report.monthly') }}" class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($month ?? now()->month) == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select tom-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Generate</button>
                </div>
            </form>
        </div>
    </div>

    @if($summary)
    <div class="row row-deck row-cards mt-3">
        <div class="col-sm-4 col-lg-2">
            <x-stat-card title="Total Employees" value="{{ $summary['total_employees'] ?? 0 }}" icon="ti ti-users" color="blue" />
        </div>
        <div class="col-sm-4 col-lg-2">
            <x-stat-card title="Working Days" value="{{ $summary['working_days'] ?? 0 }}" icon="ti ti-calendar" color="green" />
        </div>
        <div class="col-sm-4 col-lg-2">
            <x-stat-card title="Avg Attendance" value="{{ $summary['avg_attendance'] ?? 0 }}%" icon="ti ti-percentage" color="purple" />
        </div>
        <div class="col-sm-4 col-lg-2">
            <x-stat-card title="Total Late" value="{{ $summary['total_late'] ?? 0 }}" icon="ti ti-clock" color="orange" />
        </div>
        <div class="col-sm-4 col-lg-2">
            <x-stat-card title="Total Absent" value="{{ $summary['total_alpha'] ?? 0 }}" icon="ti ti-user-x" color="red" />
        </div>
        <div class="col-sm-4 col-lg-2">
            <x-stat-card title="On Leave" value="{{ $summary['total_leave'] ?? 0 }}" icon="ti ti-calendar-off" color="info" />
        </div>
    </div>
    @endif

    <div class="card mt-3">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Hadir</th>
                        <th>Telat</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Cuti</th>
                        <th>Alpha</th>
                        <th>Late (min)</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr>
                            <td>{{ $rec['employee_name'] ?? $rec->employee?->full_name ?? 'N/A' }}</td>
                            <td>{{ $rec['department'] ?? $rec->employee?->department?->name ?? '-' }}</td>
                            <td><span class="badge bg-success">{{ $rec['hadir'] ?? $rec->hadir ?? 0 }}</span></td>
                            <td><span class="badge bg-warning">{{ $rec['telat'] ?? $rec->telat ?? 0 }}</span></td>
                            <td><span class="badge bg-info">{{ $rec['izin'] ?? $rec->izin ?? 0 }}</span></td>
                            <td><span class="badge bg-danger">{{ $rec['sakit'] ?? $rec->sakit ?? 0 }}</span></td>
                            <td><span class="badge bg-purple">{{ $rec['cuti'] ?? $rec->cuti ?? 0 }}</span></td>
                            <td><span class="badge bg-secondary">{{ $rec['alpha'] ?? $rec->alpha ?? 0 }}</span></td>
                            <td>{{ $rec['total_late_minutes'] ?? $rec->total_late_minutes ?? 0 }}</td>
                            <td>
                                @php
                                    $pct = $rec['attendance_percentage'] ?? $rec->attendance_percentage ?? 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm flex-grow-1 me-2" style="min-width: 60px;">
                                        <div class="progress-bar bg-{{ $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span>{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No records for this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $records->withQueryString()->links() ?? '' }}</div>
@endsection
