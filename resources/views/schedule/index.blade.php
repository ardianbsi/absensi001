@extends('layouts.app')

@section('title', 'Schedule')

@section('content')
    <x-breadcrumb :items="[['name' => 'Schedule']]" />
    <x-page-header title="Schedule" subtitle="Manage employee shift schedules" />

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('schedule.index') }}" class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select tom-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
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
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="text" name="date" class="form-control datepicker" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Shift Schedules</h3>
            <div class="card-actions">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                    <i class="ti ti-plus"></i> Assign Shift
                </button>
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#massAssignModal">
                    <i class="ti ti-users"></i> Mass Assign
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Override</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $sched)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2" style="background-image: url({{ $sched->employee?->photo ? asset('storage/' . $sched->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($sched->employee?->full_name ?? 'N/A') }})"></span>
                                    {{ $sched->employee?->full_name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>{{ $sched->employee?->department?->name ?? '-' }}</td>
                            <td>{{ $sched->date->format('d M Y') }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $sched->shift?->color ?? '#6c757d' }}">{{ $sched->shift?->name ?? '-' }}</span>
                            </td>
                            <td>{{ $sched->shift?->clock_in_time ? substr($sched->shift->clock_in_time, 0, 5) : '-' }}</td>
                            <td>{{ $sched->shift?->clock_out_time ? substr($sched->shift->clock_out_time, 0, 5) : '-' }}</td>
                            <td>
                                @if($sched->is_override)
                                    <span class="badge bg-warning">Override</span>
                                @else
                                    <span class="badge bg-secondary">Default</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="overrideSchedule('{{ $sched->id }}', '{{ $sched->shift_id }}')">
                                    <i class="ti ti-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No schedules found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $schedules->withQueryString()->links() }}</div>

    <x-modal id="assignModal" title="Assign Shift">
        <form action="{{ route('schedule.assign') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Employee</label>
                <select name="employee_id" class="form-select tom-select" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label required">Shift</label>
                <select name="shift_id" class="form-select tom-select" required>
                    <option value="">Select Shift</option>
                    @foreach($shifts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label required">Date</label>
                <input type="text" name="schedule_date" class="form-control datepicker" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </x-modal>

    <x-modal id="massAssignModal" title="Mass Assign Shift">
        <form action="{{ route('schedule.mass-assign') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Department</label>
                <select name="department_id" class="form-select tom-select" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label required">Shift</label>
                <select name="shift_id" class="form-select tom-select" required>
                    <option value="">Select Shift</option>
                    @foreach($shifts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Start Date</label>
                    <input type="text" name="start_date" class="form-control datepicker" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">End Date</label>
                    <input type="text" name="end_date" class="form-control datepicker" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </x-modal>

    <x-modal id="overrideModal" title="Override Schedule">
        <form id="overrideForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label required">New Shift</label>
                <select name="shift_id" class="form-select tom-select" required>
                    <option value="">Select Shift</option>
                    @foreach($shifts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label required">Reason</label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Override</button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        function overrideSchedule(scheduleId, currentShiftId) {
            document.getElementById('overrideForm').action = '/schedule/override/' + scheduleId;
            new bootstrap.Modal(document.getElementById('overrideModal')).show();
        }
    </script>
    @endpush
@endsection
