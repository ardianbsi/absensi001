@extends('layouts.app')

@section('title', 'Leave Detail')

@section('content')
    <x-breadcrumb :items="[['name' => 'Leaves', 'route' => 'leaves.index'], ['name' => 'Detail']]" />

    <x-page-header title="Leave Request Detail" :subtitle="$leaveRequest->leaveType?->name . ' - ' . $leaveRequest->employee?->full_name" />

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Employee</h3></div>
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3" style="background-image: url({{ $leaveRequest->employee?->photo ? asset('storage/' . $leaveRequest->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($leaveRequest->employee?->full_name ?? 'N/A') }})"></span>
                    <h3>{{ $leaveRequest->employee?->full_name ?? 'N/A' }}</h3>
                    <div class="text-muted">{{ $leaveRequest->employee?->department?->name ?? '-' }}</div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $leaveRequest->status === 'approved' ? 'success' : ($leaveRequest->status === 'pending' ? 'warning' : 'danger') }} fs-6">{{ ucfirst($leaveRequest->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Leave Details</h3></div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Leave Type</div>
                            <div class="datagrid-content">{{ $leaveRequest->leaveType?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Start Date</div>
                            <div class="datagrid-content">{{ $leaveRequest->start_date->format('d M Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">End Date</div>
                            <div class="datagrid-content">{{ $leaveRequest->end_date->format('d M Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Total Days</div>
                            <div class="datagrid-content">{{ $leaveRequest->total_days }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Status</div>
                            <div class="datagrid-content">
                                <span class="badge bg-{{ $leaveRequest->status === 'approved' ? 'success' : ($leaveRequest->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($leaveRequest->status) }}</span>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Approved By</div>
                            <div class="datagrid-content">{{ $leaveRequest->approver?->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4>Reason</h4>
                        <p>{{ $leaveRequest->reason ?? 'No reason provided' }}</p>
                    </div>

                    @if($leaveRequest->approval_reason)
                        <div class="mt-3">
                            <h4>Approval Notes</h4>
                            <p>{{ $leaveRequest->approval_reason }}</p>
                        </div>
                    @endif

                    @if($leaveRequest->attachment)
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="ti ti-file-download"></i> View Attachment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
