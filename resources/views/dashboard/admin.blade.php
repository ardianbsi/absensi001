@extends('layouts.app')

@section('title', 'Dashboard HR')

@section('content')
    @php
        $todayStats = $stats['today_stats'] ?? [];
        $pendingApprovals = $stats['pending_approvals'] ?? [];
        $attendanceChart = $stats['attendance_chart'] ?? [];
        $departmentStats = $stats['department_stats'] ?? [];
    @endphp

    <x-breadcrumb :items="[['name' => 'Dashboard']]" />

    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard HR</h2>
                <div class="text-muted mt-1">Overview of today's attendance {{ now()->format('d F Y') }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('attendance.scan') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <i class="ti ti-camera"></i> Scan Attendance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Hadir" value="{{ $todayStats['hadir'] ?? 0 }}" icon="ti ti-user-check" color="green" :trend="null">Checked in today</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Telat" value="{{ $todayStats['telat'] ?? 0 }}" icon="ti ti-alert-triangle" color="orange" :trend="null">Arrived late</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Tidak Hadir" value="{{ $todayStats['alpha'] ?? 0 }}" icon="ti ti-user-x" color="red" :trend="null">No attendance</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Cuti / Izin" value="{{ ($todayStats['cuti'] ?? 0) + ($todayStats['izin'] ?? 0) }}" icon="ti ti-calendar-off" color="blue" :trend="null">On leave</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="WFH" value="{{ $todayStats['wfh'] ?? 0 }}" icon="ti ti-home" color="purple" :trend="null">Work from home</x-stat-card>
        </div>
        <div class="col-sm-6 col-lg-2">
            <x-stat-card title="Lembur" value="{{ $todayStats['lembur'] ?? 0 }}" icon="ti ti-clock-hour-4" color="teal" :trend="null">Overtime today</x-stat-card>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Attendance Trend (30 Days)</h3>
                </div>
                <div class="card-body">
                    <div id="attendance-trend-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Distribution</h3>
                </div>
                <div class="card-body">
                    <div id="attendance-donut-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
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
    </div>

    @if(count($departmentStats) > 0)
    <div class="row row-deck row-cards mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Department Attendance Summary</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Total Employees</th>
                                <th>Present</th>
                                <th>Attendance Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departmentStats as $dept)
                                <tr>
                                    <td>{{ $dept['department'] }}</td>
                                    <td>{{ $dept['total_employees'] }}</td>
                                    <td>{{ $dept['present'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm flex-grow-1 me-2">
                                                <div class="progress-bar bg-success" style="width: {{ $dept['present_percentage'] }}%"></div>
                                            </div>
                                            <span>{{ $dept['present_percentage'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No department data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chartData = @json($attendanceChart);
            if (chartData && chartData.length > 0) {
                var categories = chartData.map(function(d) { return d.date; });
                var hadir = chartData.map(function(d) { return d.hadir; });
                var telat = chartData.map(function(d) { return d.telat; });
                var izin = chartData.map(function(d) { return d.izin || 0; });
                var sakit = chartData.map(function(d) { return d.sakit || 0; });

                var trendOptions = {
                    chart: { type: 'line', height: 300, toolbar: { show: false } },
                    series: [
                        { name: 'Hadir', data: hadir },
                        { name: 'Telat', data: telat },
                        { name: 'Izin', data: izin },
                        { name: 'Sakit', data: sakit }
                    ],
                    xaxis: { categories: categories, labels: { rotate: -45 } },
                    colors: ['#2fb344', '#f59f00', '#4299e1', '#e53e3e'],
                    stroke: { curve: 'smooth', width: 2 },
                    legend: { position: 'top' }
                };
                new ApexCharts(document.querySelector('#attendance-trend-chart'), trendOptions).render();

                var hadirVal = {{ $todayStats['hadir'] ?? 0 }};
                var telatVal = {{ $todayStats['telat'] ?? 0 }};
                var alphaVal = {{ $todayStats['alpha'] ?? 0 }};
                var cutiVal = {{ ($todayStats['cuti'] ?? 0) + ($todayStats['izin'] ?? 0) }};

                var donutOptions = {
                    chart: { type: 'donut', height: 300 },
                    series: [hadirVal, telatVal, alphaVal, cutiVal],
                    labels: ['Hadir', 'Telat', 'Alpha', 'Cuti/Izin'],
                    colors: ['#2fb344', '#f59f00', '#e53e3e', '#4299e1'],
                    legend: { position: 'bottom' },
                    responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
                };
                new ApexCharts(document.querySelector('#attendance-donut-chart'), donutOptions).render();
            }
        });
    </script>
    @endpush
@endsection
