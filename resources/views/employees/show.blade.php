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
                <div class="nav-tabs-custom" data-bs-toggle="tabs">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#profile-tab" class="nav-link active" data-bs-toggle="tab" role="tab" aria-selected="true">
                                <i class="ti ti-user"></i> Profile Details
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#attendance-tab" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
                                <i class="ti ti-calendar-check"></i> Attendance
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#leave-tab" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
                                <i class="ti ti-calendar-off"></i> Leave History
                            </a>
                        </li>
                    </ul>
                    <div class="card-body tab-content">
                        <!-- Profile Details Tab -->
                        <div id="profile-tab" class="tab-pane active" role="tabpanel">
                            <div class="row gx-4 gy-3">
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Email</div>
                                    <div class="fw-semibold">{{ $employee->email ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Phone</div>
                                    <div class="fw-semibold">{{ $employee->phone ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Gender</div>
                                    <div class="fw-semibold">{{ ucfirst($employee->gender ?? '-') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Birth Date</div>
                                    <div class="fw-semibold">{{ $employee->birth_date?->format('d M Y') ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Department</div>
                                    <div class="fw-semibold">{{ $employee->department?->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Position</div>
                                    <div class="fw-semibold">{{ $employee->position?->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Manager</div>
                                    <div class="fw-semibold">{{ $employee->manager?->full_name ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Shift</div>
                                    <div class="fw-semibold">{{ $employee->shift?->name ?? '-' }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small mb-1">Address</div>
                                    <div class="fw-semibold">{{ $employee->address ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Tab -->
                        <div id="attendance-tab" class="tab-pane" role="tabpanel">
                            @php
                                $attendanceSummary = collect($attendanceSummary ?? []);
                                $summaryCounts = [
                                    'total' => $attendanceSummary->count(),
                                    'hadir' => $attendanceSummary->where('status', 'hadir')->count(),
                                    'telat' => $attendanceSummary->where('status', 'telat')->count(),
                                    'alpha' => $attendanceSummary->where('status', 'alpha')->count(),
                                    'izin' => $attendanceSummary->where('status', 'izin')->count(),
                                    'sakit' => $attendanceSummary->where('status', 'sakit')->count(),
                                ];
                            @endphp
                            
                            <div class="row gx-3 gy-3 mb-4">
                                <div class="col-6 col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-item-value text-primary">{{ $summaryCounts['total'] }}</div>
                                        <div class="stat-item-label">Total Records</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-item-value text-success">{{ $summaryCounts['hadir'] }}</div>
                                        <div class="stat-item-label">Present</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-item-value text-warning">{{ $summaryCounts['telat'] }}</div>
                                        <div class="stat-item-label">Late</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-item-value text-danger">{{ $summaryCounts['alpha'] }}</div>
                                        <div class="stat-item-label">Absent</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-item-value text-secondary">{{ $summaryCounts['izin'] }}</div>
                                        <div class="stat-item-label">Permission</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-item-value text-info">{{ $summaryCounts['sakit'] }}</div>
                                        <div class="stat-item-label">Sick</div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="mb-3">Attendance Details (Last 30 Days)</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th class="text-muted">Date</th>
                                            <th class="text-muted">Clock In</th>
                                            <th class="text-muted">Clock Out</th>
                                            <th class="text-muted">Status</th>
                                            <th class="text-muted">Work Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendanceSummary as $att)
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
                                            <tr><td colspan="5" class="text-center text-muted py-3">No attendance records available</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Leave History Tab -->
                        <div id="leave-tab" class="tab-pane" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th class="text-muted">Type</th>
                                            <th class="text-muted">Start Date</th>
                                            <th class="text-muted">End Date</th>
                                            <th class="text-muted">Days</th>
                                            <th class="text-muted">Status</th>
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
                                            <tr><td colspan="5" class="text-center text-muted py-3">No leave requests</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-item {
            text-align: center;
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }
        .stat-item-value {
            font-size: 1.75rem;
            font-weight: bold;
            line-height: 1;
        }
        .stat-item-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
    </style>
