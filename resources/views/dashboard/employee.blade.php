@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $employeeStats = $employeeStats ?? [];
        $todayAttendance = $employeeStats['today'] ?? null;
        $monthlySummary = $employeeStats['monthly_summary'] ?? [];
        $recentAttendances = $employeeStats['recent_attendance'] ?? [];
        $employee = auth()->user()->employee;
    @endphp

    <x-breadcrumb :items="[['name' => 'Dashboard']]" />

    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Welcome, {{ auth()->user()->name }}</h2>
                <div class="text-muted mt-1">{{ now()->format('l, d F Y') }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('attendance.scan') }}" class="btn btn-primary">
                        <i class="ti ti-camera"></i> Check In / Out
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="avatar avatar-xl" style="background-image: url({{ $employee?->photo ? asset('storage/' . $employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }})"></span>
                    </div>
                    <h3>{{ auth()->user()->name }}</h3>
                    <div class="text-muted">{{ $employee?->position?->name ?? '-' }} &mdash; {{ $employee?->department?->name ?? '-' }}</div>
                    <div class="mt-3">
                        @if($todayAttendance)
                            @php
                                $clockIn = $todayAttendance->clock_in instanceof \Carbon\Carbon ? $todayAttendance->clock_in : \Carbon\Carbon::parse($todayAttendance->clock_in);
                                $clockOut = $todayAttendance->clock_out ? ($todayAttendance->clock_out instanceof \Carbon\Carbon ? $todayAttendance->clock_out : \Carbon\Carbon::parse($todayAttendance->clock_out)) : null;
                            @endphp
                            @if($clockIn && !$clockOut)
                                <span class="badge bg-success fs-6 p-2"><i class="ti ti-check"></i> Checked In</span>
                                <div class="mt-2 text-muted">at {{ $clockIn->format('H:i') }}</div>
                            @elseif($clockIn && $clockOut)
                                <span class="badge bg-secondary fs-6 p-2"><i class="ti ti-check"></i> Completed</span>
                                <div class="mt-2 text-muted">{{ $clockIn->format('H:i') }} &mdash; {{ $clockOut->format('H:i') }}</div>
                            @endif
                        @else
                            <span class="badge bg-danger fs-6 p-2"><i class="ti ti-x"></i> Not Checked In</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row row-deck row-cards">
                <div class="col-sm-4">
                    <x-stat-card title="Hadir" value="{{ $monthlySummary['hadir'] ?? 0 }}" icon="ti ti-user-check" color="green" :trend="null">This month</x-stat-card>
                </div>
                <div class="col-sm-4">
                    <x-stat-card title="Alpha" value="{{ $monthlySummary['alpha'] ?? 0 }}" icon="ti ti-user-x" color="red" :trend="null">This month</x-stat-card>
                </div>
                <div class="col-sm-4">
                    <x-stat-card title="Telat" value="{{ $monthlySummary['total_late'] ?? 0 }} min" icon="ti ti-clock" color="orange" :trend="null">Total late minutes</x-stat-card>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Monthly Attendance Summary</h3>
                </div>
                <div class="card-body">
                    <div id="employee-monthly-chart" style="height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentAttendances as $att)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @php
                                        $iconMap = ['hadir' => 'ti ti-circle-check text-success', 'telat' => 'ti ti-alert-triangle text-warning', 'alpha' => 'ti ti-circle-x text-danger', 'cuti' => 'ti ti-calendar-off text-info', 'izin' => 'ti ti-file-text text-secondary', 'sakit' => 'ti ti-heart-off text-danger'];
                                    @endphp
                                    <i class="{{ $iconMap[$att->status] ?? 'ti ti-circle' }}"></i>
                                </div>
                                <div class="col text-truncate">
                                    <span>{{ \Carbon\Carbon::parse($att->date)->format('D, d M Y') }}</span>
                                    <small class="text-muted d-block">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }} &mdash; {{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</small>
                                </div>
                                <div class="col-auto">
                                    <span class="badge {{ $att->status === 'hadir' ? 'bg-success' : ($att->status === 'telat' ? 'bg-warning' : 'bg-secondary') }}">{{ ucfirst($att->status) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted">No recent attendance records</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leave Balance</h3>
                </div>
                <div class="card-body">
                    @php
                        $leaveBalances = app('App\Services\LeaveService')->getLeaveBalances($employee) ?? [];
                    @endphp
                    @forelse($leaveBalances as $balance)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <span>{{ $balance['name'] ?? $balance['leave_type'] }}</span>
                                <span class="ms-auto">{{ $balance['used'] ?? 0 }} / {{ $balance['quota'] ?? 0 }}</span>
                            </div>
                            @php $pct = ($balance['quota'] ?? 0) > 0 ? (($balance['used'] ?? 0) / $balance['quota']) * 100 : 0; @endphp
                            <div class="progress progress-sm">
                                <div class="progress-bar {{ $pct >= 80 ? 'bg-danger' : ($pct >= 50 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No leave types configured</p>
                    @endforelse
                    <div class="mt-3">
                        <a href="{{ route('leaves.create') }}" class="btn btn-primary w-100">
                            <i class="ti ti-plus"></i> Request Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var hadir = {{ $monthlySummary['hadir'] ?? 0 }};
            var sakit = {{ $monthlySummary['sakit'] ?? 0 }};
            var cuti = {{ $monthlySummary['cuti'] ?? 0 }};
            var alpha = {{ $monthlySummary['alpha'] ?? 0 }};

            new ApexCharts(document.querySelector('#employee-monthly-chart'), {
                chart: { type: 'bar', height: 200, toolbar: { show: false } },
                series: [{ name: 'Days', data: [hadir, sakit, cuti, alpha] }],
                xaxis: { categories: ['Hadir', 'Sakit', 'Cuti', 'Alpha'] },
                colors: ['#2fb344', '#e53e3e', '#4299e1', '#d63939'],
                plotOptions: { bar: { distributed: true } }
            }).render();
        });
    </script>
    @endpush
@endsection
