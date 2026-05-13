@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
    @php
        $teamStats = $teamStats ?? [];
        $todayStats = $teamStats['today_stats'] ?? [];
        $pendingApprovals = $teamStats['pending_approvals'] ?? [];
    @endphp

    <x-breadcrumb :items="[['name' => 'Dashboard']]" />

    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard Manager</h2>
                <div class="text-muted mt-1">Team overview &mdash; {{ $teamStats['team_count'] ?? 0 }} team members</div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Team Members" value="{{ $teamStats['team_count'] ?? 0 }}" icon="ti ti-users" color="blue" :trend="null">Active employees</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Hadir" value="{{ $todayStats['hadir'] ?? 0 }}" icon="ti ti-user-check" color="green" :trend="null">Checked in today</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Alpha" value="{{ $todayStats['alpha'] ?? 0 }}" icon="ti ti-user-x" color="red" :trend="null">Not checked in</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Cuti" value="{{ $todayStats['cuti'] ?? 0 }}" icon="ti ti-calendar-off" color="purple" :trend="null">On leave today</x-stat-card>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Approvals</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <span class="avatar avatar-sm bg-blue-lt me-3"><i class="ti ti-calendar-time"></i></span>
                            <div class="flex-fill">
                                <strong>Leave Requests</strong>
                                <span class="badge bg-warning ms-2">{{ $pendingApprovals['leave'] ?? 0 }} pending</span>
                            </div>
                            <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <span class="avatar avatar-sm bg-orange-lt me-3"><i class="ti ti-clock-hour-4"></i></span>
                            <div class="flex-fill">
                                <strong>Overtime Requests</strong>
                                <span class="badge bg-warning ms-2">{{ $pendingApprovals['overtime'] ?? 0 }} pending</span>
                            </div>
                            <a href="{{ route('overtimes.index') }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Team Attendance</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Status</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($teamStats['today_attendance'] ?? []) as $att)
                                <tr>
                                    <td>{{ $att->employee?->full_name }}</td>
                                    <td>
                                        @php
                                            $badgeMap = ['hadir' => 'bg-success', 'telat' => 'bg-warning', 'alpha' => 'bg-danger', 'cuti' => 'bg-info', 'izin' => 'bg-secondary', 'sakit' => 'bg-danger'];
                                        @endphp
                                        <span class="badge {{ $badgeMap[$att->status] ?? 'bg-secondary' }}">{{ $att->status }}</span>
                                    </td>
                                    <td>{{ $att->clock_in ? $att->clock_in->format('H:i') : '-' }}</td>
                                    <td>{{ $att->clock_out ? $att->clock_out->format('H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No attendance records today</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
