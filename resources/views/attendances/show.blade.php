@extends('layouts.app')

@section('title', 'Attendance Detail')

@section('content')
    <x-breadcrumb :items="[['name' => 'Attendance', 'route' => 'attendance.index'], ['name' => 'Detail']]" />

    <x-page-header title="Attendance Detail" :subtitle="$attendance->employee?->full_name . ' - ' . $attendance->date->format('d M Y')" />

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Employee</h3></div>
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3" style="background-image: url({{ $attendance->employee?->photo ? asset('storage/' . $attendance->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($attendance->employee?->full_name ?? 'N/A') }})"></span>
                    <h3>{{ $attendance->employee?->full_name ?? 'N/A' }}</h3>
                    <div class="text-muted">{{ $attendance->employee?->department?->name ?? '-' }}</div>
                    <div class="text-muted">{{ $attendance->employee?->position?->name ?? '-' }}</div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Selfie Photos</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <label class="form-label">Check In</label>
                            @if($attendance->clock_in_selfie)
                                <img src="{{ asset('storage/' . $attendance->clock_in_selfie) }}" class="img-fluid rounded" alt="Check In Selfie">
                            @else
                                <div class="text-muted">No photo</div>
                            @endif
                        </div>
                        <div class="col-6 text-center">
                            <label class="form-label">Check Out</label>
                            @if($attendance->clock_out_selfie)
                                <img src="{{ asset('storage/' . $attendance->clock_out_selfie) }}" class="img-fluid rounded" alt="Check Out Selfie">
                            @else
                                <div class="text-muted">No photo</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Attendance Timeline</h3></div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-badge bg-success"><i class="ti ti-login"></i></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Check In</strong>
                                    <span class="text-muted">{{ $attendance->clock_in?->format('H:i:s') ?? '-' }}</span>
                                </div>
                                <div class="text-muted small">
                                    @if($attendance->clock_in_latitude && $attendance->clock_in_longitude)
                                        <a href="https://maps.google.com/maps?q={{ $attendance->clock_in_latitude }},{{ $attendance->clock_in_longitude }}" target="_blank" class="text-reset">
                                            <i class="ti ti-map-pin"></i> View Location
                                        </a>
                                    @endif
                                    @if($attendance->clock_in_note)
                                        <div class="mt-1"><i class="ti ti-notes"></i> {{ $attendance->clock_in_note }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-badge bg-danger"><i class="ti ti-logout"></i></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Check Out</strong>
                                    <span class="text-muted">{{ $attendance->clock_out?->format('H:i:s') ?? '-' }}</span>
                                </div>
                                <div class="text-muted small">
                                    @if($attendance->clock_out_latitude && $attendance->clock_out_longitude)
                                        <a href="https://maps.google.com/maps?q={{ $attendance->clock_out_latitude }},{{ $attendance->clock_out_longitude }}" target="_blank" class="text-reset">
                                            <i class="ti ti-map-pin"></i> View Location
                                        </a>
                                    @endif
                                    @if($attendance->clock_out_note)
                                        <div class="mt-1"><i class="ti ti-notes"></i> {{ $attendance->clock_out_note }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Details</h3></div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Date</div>
                            <div class="datagrid-content">{{ $attendance->date->format('d M Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Status</div>
                            <div class="datagrid-content">
                                @php
                                    $badgeMap = ['hadir' => 'bg-success', 'telat' => 'bg-warning', 'izin' => 'bg-info', 'sakit' => 'bg-danger', 'cuti' => 'bg-purple', 'alpha' => 'bg-secondary'];
                                @endphp
                                <span class="badge {{ $badgeMap[$attendance->status] ?? 'bg-secondary' }}">{{ ucfirst($attendance->status) }}</span>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Shift</div>
                            <div class="datagrid-content">{{ $attendance->shift?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Total Work Hours</div>
                            <div class="datagrid-content">{{ $attendance->total_work_hours ? $attendance->total_work_hours . ' hours' : '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Late</div>
                            <div class="datagrid-content">{{ $attendance->is_late ? 'Yes (' . $attendance->late_minutes . ' min)' : 'No' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Early Leave</div>
                            <div class="datagrid-content">{{ $attendance->is_early_leave ? 'Yes (' . $attendance->early_leave_minutes . ' min)' : 'No' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Device</div>
                            <div class="datagrid-content">{{ $attendance->clock_in_device ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">IP Address</div>
                            <div class="datagrid-content">{{ $attendance->clock_in_ip ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($attendance->attendanceLogs && $attendance->attendanceLogs->count() > 0)
            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Attendance Logs</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Timestamp</th>
                                <th>Location</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendance->attendanceLogs as $log)
                                <tr>
                                    <td><span class="badge bg-{{ $log->action === 'check_in' ? 'success' : 'danger' }}">{{ str_replace('_', ' ', ucfirst($log->action)) }}</span></td>
                                    <td>{{ $log->timestamp?->format('d M Y H:i:s') }}</td>
                                    <td>
                                        @if($log->latitude && $log->longitude)
                                            <a href="https://maps.google.com/maps?q={{ $log->latitude }},{{ $log->longitude }}" target="_blank"><i class="ti ti-map-pin"></i></a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $log->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
