@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <x-breadcrumb :items="[['name' => 'Attendance']]" />

    <x-page-header title="Attendance Records" subtitle="View and manage attendance">
        <a href="{{ route('attendance.scan') }}" class="btn btn-primary">
            <i class="ti ti-camera"></i> Scan Attendance
        </a>
        <a href="{{ route('attendance.export') }}" class="btn btn-outline-primary">
            <i class="ti ti-download"></i> Export
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.index') }}" class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="text" name="date" class="form-control datepicker" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select tom-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $id => $name)
                            <option value="{{ $id }}" {{ request('employee_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select tom-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
                </div>
            </form>

            <x-table :headers="['Employee', 'Date', 'Clock In', 'Clock Out', 'Status', 'Late', 'Work Hours']">
                @forelse($attendances as $att)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url({{ $att->employee?->photo ? asset('storage/' . $att->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($att->employee?->full_name ?? 'N/A') }})"></span>
                                <div>
                                    <a href="{{ route('attendance.show', $att) }}" class="text-reset">{{ $att->employee?->full_name ?? 'N/A' }}</a>
                                    <div class="text-muted small">{{ $att->employee?->department?->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $att->date->format('d M Y') }}</td>
                        <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                        <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                        <td>
                            @php
                                $badgeMap = ['hadir' => 'bg-success', 'telat' => 'bg-warning', 'izin' => 'bg-info', 'sakit' => 'bg-danger', 'cuti' => 'bg-purple', 'alpha' => 'bg-secondary', 'lembur' => 'bg-teal'];
                            @endphp
                            <span class="badge {{ $badgeMap[$att->status] ?? 'bg-secondary' }}">{{ ucfirst($att->status ?? 'N/A') }}</span>
                        </td>
                        <td>{{ $att->is_late ? $att->late_minutes . ' min' : '-' }}</td>
                        <td>{{ $att->total_work_hours ? $att->total_work_hours . 'h' : '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('attendance.show', $att) }}" class="btn btn-outline-primary" title="View"><i class="ti ti-eye"></i></a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-att-{{ $att->id }}').submit())">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <form id="del-att-{{ $att->id }}" action="{{ route('attendance.destroy', $att) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No attendance records found</td></tr>
                @endforelse
            </x-table>

            <div class="mt-3">
                {{ $attendances->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
