@extends('layouts.app')

@section('title', 'Overtime Requests')

@section('content')
    <x-breadcrumb :items="[['name' => 'Overtimes']]" />

    <x-page-header title="Overtime Requests" subtitle="Manage overtime requests">
        <a href="{{ route('overtimes.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Request Overtime
        </a>
        <a href="{{ route('overtimes.export') }}" class="btn btn-outline-primary">
            <i class="ti ti-download"></i> Export
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('overtimes.index') }}" class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select tom-select">
                        <option value="">All</option>
                        @foreach($employees as $id => $name)
                            <option value="{{ $id }}" {{ request('employee_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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

            <x-table :headers="['Employee', 'Date', 'Start', 'End', 'Hours', 'Status', 'Actions']">
                @forelse($overtimes as $ov)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url({{ $ov->employee?->photo ? asset('storage/' . $ov->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($ov->employee?->full_name ?? 'N/A') }})"></span>
                                <a href="{{ route('overtimes.show', $ov) }}">{{ $ov->employee?->full_name ?? 'N/A' }}</a>
                            </div>
                        </td>
                        <td>{{ $ov->date->format('d M Y') }}</td>
                        <td>{{ $ov->start_time?->format('H:i') ?? '-' }}</td>
                        <td>{{ $ov->end_time?->format('H:i') ?? '-' }}</td>
                        <td><span class="badge bg-blue">{{ $ov->total_hours }}h</span></td>
                        <td>
                            @php
                                $sBadge = ['approved' => 'bg-success', 'pending' => 'bg-warning', 'rejected' => 'bg-danger'];
                            @endphp
                            <span class="badge {{ $sBadge[$ov->status] ?? 'bg-secondary' }}">{{ ucfirst($ov->status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('overtimes.show', $ov) }}" class="btn btn-outline-primary"><i class="ti ti-eye"></i></a>
                                @can('overtime-approve')
                                    @if($ov->status === 'pending')
                                        <form action="{{ route('overtimes.approve', $ov->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Approve"><i class="ti ti-check"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-outline-danger" title="Reject" onclick="rejectOvertime('{{ $ov->id }}')"><i class="ti ti-x"></i></button>
                                    @endif
                                @endcan
                                @if($ov->status === 'pending' && $ov->employee_id === auth()->user()->employee?->id)
                                    <form action="{{ route('overtimes.cancel', $ov->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary" title="Cancel"><i class="ti ti-circle-off"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No overtime requests found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $overtimes->withQueryString()->links() }}</div>
        </div>
    </div>

    <x-modal id="rejectModal" title="Reject Overtime Request">
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
        function rejectOvertime(id) {
            document.getElementById('rejectForm').action = '/overtimes/' + id + '/reject';
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
    </script>
    @endpush
@endsection
