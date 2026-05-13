@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
    <x-breadcrumb :items="[['name' => 'Leaves']]" />

    <x-page-header title="Leave Requests" subtitle="Manage employee leave">
        <a href="{{ route('leaves.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Request Leave
        </a>
        <a href="{{ route('leaves.export') }}" class="btn btn-outline-primary">
            <i class="ti ti-download"></i> Export
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('leaves.index') }}" class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Leave Type</label>
                    <select name="leave_type_id" class="form-select tom-select">
                        <option value="">All Types</option>
                        @foreach($leaveTypes as $id => $name)
                            <option value="{{ $id }}" {{ request('leave_type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="text" name="date_from" class="form-control datepicker" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="text" name="date_to" class="form-control datepicker" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
                </div>
            </form>

            <x-table :headers="['Employee', 'Type', 'Start', 'End', 'Days', 'Status', 'Actions']">
                @forelse($leaveRequests as $leave)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url({{ $leave->employee?->photo ? asset('storage/' . $leave->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($leave->employee?->full_name ?? 'N/A') }})"></span>
                                <a href="{{ route('leaves.show', $leave) }}">{{ $leave->employee?->full_name ?? 'N/A' }}</a>
                            </div>
                        </td>
                        <td>{{ $leave->leaveType?->name ?? '-' }}</td>
                        <td>{{ $leave->start_date->format('d M Y') }}</td>
                        <td>{{ $leave->end_date->format('d M Y') }}</td>
                        <td><span class="badge bg-blue">{{ $leave->total_days }} day(s)</span></td>
                        <td>
                            @php
                                $sBadge = ['approved' => 'bg-success', 'pending' => 'bg-warning', 'rejected' => 'bg-danger', 'cancelled' => 'bg-secondary'];
                            @endphp
                            <span class="badge {{ $sBadge[$leave->status] ?? 'bg-secondary' }}">{{ ucfirst($leave->status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('leaves.show', $leave) }}" class="btn btn-outline-primary"><i class="ti ti-eye"></i></a>
                                @can('leave-approve')
                                    @if($leave->status === 'pending')
                                        <form action="{{ route('leaves.approve', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Approve"><i class="ti ti-check"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-outline-danger" title="Reject" onclick="rejectLeave('{{ $leave->id }}')"><i class="ti ti-x"></i></button>
                                    @endif
                                @endcan
                                @if($leave->status === 'pending' && $leave->employee_id === auth()->user()->employee?->id)
                                    <form action="{{ route('leaves.cancel', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary" title="Cancel"><i class="ti ti-circle-off"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No leave requests found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $leaveRequests->withQueryString()->links() }}</div>
        </div>
    </div>

    <x-modal id="rejectModal" title="Reject Leave Request">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Rejection Reason</label>
                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject</button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        function rejectLeave(id) {
            document.getElementById('rejectForm').action = '/leaves/' + id + '/reject';
            var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }
    </script>
    @endpush
@endsection
