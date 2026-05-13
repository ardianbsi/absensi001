@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
    <x-breadcrumb :items="[['name' => 'Reports', 'route' => 'report.daily'], ['name' => 'Daily']]" />

    <x-page-header title="Daily Attendance Report" :subtitle="$date->format('d F Y')">
        <a href="{{ route('report.export-excel', 'daily') }}?date={{ $date->format('Y-m-d') }}&department_id={{ request('department_id') }}" class="btn btn-outline-success">
            <i class="ti ti-file-spreadsheet"></i> Excel
        </a>
        <a href="{{ route('report.export-pdf', 'daily') }}?date={{ $date->format('Y-m-d') }}&department_id={{ request('department_id') }}" class="btn btn-outline-danger">
            <i class="ti ti-file-pdf"></i> PDF
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('report.daily') }}" class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="text" name="date" class="form-control datepicker" value="{{ $date->format('Y-m-d') }}">
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
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Generate</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        @if($summary)
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Total" value="{{ $summary['total_employees'] ?? 0 }}" icon="ti ti-users" color="blue" />
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Hadir" value="{{ $summary['hadir'] ?? 0 }}" icon="ti ti-user-check" color="green" />
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Telat" value="{{ $summary['telat'] ?? 0 }}" icon="ti ti-alert-triangle" color="orange" />
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Izin/Sakit" value="{{ ($summary['izin'] ?? 0) + ($summary['sakit'] ?? 0) }}" icon="ti ti-file-text" color="purple" />
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Cuti" value="{{ $summary['cuti'] ?? 0 }}" icon="ti ti-calendar-off" color="info" />
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Alpha" value="{{ $summary['alpha'] ?? 0 }}" icon="ti ti-user-x" color="red" />
        </div>
        @endif
    </div>

    <div class="card mt-3">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th>Late</th>
                        <th>Work Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2" style="background-image: url({{ $att->employee?->photo ? asset('storage/' . $att->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($att->employee?->full_name ?? 'N/A') }})"></span>
                                    {{ $att->employee?->full_name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>{{ $att->employee?->department?->name ?? '-' }}</td>
                            <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                            <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                            <td>
                                @php
                                    $bMap = ['hadir' => 'bg-success', 'telat' => 'bg-warning', 'izin' => 'bg-info', 'sakit' => 'bg-danger', 'cuti' => 'bg-purple', 'alpha' => 'bg-secondary'];
                                @endphp
                                <span class="badge {{ $bMap[$att->status] ?? 'bg-secondary' }}">{{ ucfirst($att->status) }}</span>
                            </td>
                            <td>{{ $att->is_late ? $att->late_minutes . ' min' : '-' }}</td>
                            <td>{{ $att->total_work_hours ? $att->total_work_hours . 'h' : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No records for this date</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $attendances->withQueryString()->links() }}</div>
@endsection
