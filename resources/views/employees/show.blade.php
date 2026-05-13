@extends('layouts.app')

@section('title', $employee->full_name)

@section('content')
    <x-breadcrumb :items="[['name' => 'Employees', 'route' => 'employees.index'], ['name' => $employee->full_name]]" />

    <x-page-header :title="$employee->full_name" subtitle="Employee details">
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
            <i class="ti ti-edit"></i> Edit
        </a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete('Delete {{ $employee->full_name }}?').then((r) => r.isConfirmed && document.getElementById('delete-form').submit())">
            <i class="ti ti-trash"></i> Delete
        </button>
        <form id="delete-form" action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
    </x-page-header>

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="avatar avatar-xl" style="background-image: url({{ $employee->photo ? asset('storage/' . $employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($employee->full_name) }})"></span>
                    </div>
                    <h3>{{ $employee->full_name }}</h3>
                    <div class="text-muted">{{ $employee->position?->name ?? '-' }}</div>
                    <div class="mt-2">
                        <span class="badge {{ $employee->is_active ? 'bg-success' : 'bg-danger' }}">{{ $employee->is_active ? 'Active' : 'Inactive' }}</span>
                        <span class="badge bg-blue">{{ ucfirst($employee->employment_status ?? 'N/A') }}</span>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-muted">NIK</div>
                            <div class="fw-bold">{{ $employee->nik }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted">Joined</div>
                            <div class="fw-bold">{{ $employee->join_date?->format('d M Y') ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Personal Information</h3></div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Email</div>
                            <div class="datagrid-content">{{ $employee->email ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Phone</div>
                            <div class="datagrid-content">{{ $employee->phone ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Gender</div>
                            <div class="datagrid-content">{{ ucfirst($employee->gender ?? '-') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Birth Date</div>
                            <div class="datagrid-content">{{ $employee->birth_date?->format('d M Y') ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Department</div>
                            <div class="datagrid-content">{{ $employee->department?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Position</div>
                            <div class="datagrid-content">{{ $employee->position?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Manager</div>
                            <div class="datagrid-content">{{ $employee->manager?->full_name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Shift</div>
                            <div class="datagrid-content">{{ $employee->shift?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Address</div>
                            <div class="datagrid-content">{{ $employee->address ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Attendance Summary (Last 30 Days)</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Status</th>
                                <th>Work Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($attendanceSummary ?? []) as $att)
                                <tr>
                                    <td>{{ $att->date->format('d M Y') }}</td>
                                    <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                                    <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeMap = ['hadir' => 'bg-success', 'telat' => 'bg-warning', 'alpha' => 'bg-danger', 'cuti' => 'bg-info', 'izin' => 'bg-secondary', 'sakit' => 'bg-danger'];
                                        @endphp
                                        <span class="badge {{ $badgeMap[$att->status] ?? 'bg-secondary' }}">{{ ucfirst($att->status) }}</span>
                                    </td>
                                    <td>{{ $att->total_work_hours ? $att->total_work_hours . 'h' : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No attendance records</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Leave History</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($leaveHistory ?? []) as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType?->name ?? '-' }}</td>
                                    <td>{{ $leave->start_date->format('d M Y') }}</td>
                                    <td>{{ $leave->end_date->format('d M Y') }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td>
                                        @php
                                            $lBadge = ['approved' => 'bg-success', 'pending' => 'bg-warning', 'rejected' => 'bg-danger', 'cancelled' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $lBadge[$leave->status] ?? 'bg-secondary' }}">{{ ucfirst($leave->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No leave requests</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
