@extends('layouts.app')

@section('title', 'Overtime Detail')

@section('content')
    <x-breadcrumb :items="[['name' => 'Overtimes', 'route' => 'overtimes.index'], ['name' => 'Detail']]" />

    <x-page-header title="Overtime Request Detail" :subtitle="$overtime->employee?->full_name . ' - ' . $overtime->date->format('d M Y')" />

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Employee</h3></div>
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3" style="background-image: url({{ $overtime->employee?->photo ? asset('storage/' . $overtime->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($overtime->employee?->full_name ?? 'N/A') }})"></span>
                    <h3>{{ $overtime->employee?->full_name ?? 'N/A' }}</h3>
                    <div class="text-muted">{{ $overtime->employee?->department?->name ?? '-' }}</div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $overtime->status === 'approved' ? 'success' : ($overtime->status === 'pending' ? 'warning' : 'danger') }} fs-6">{{ ucfirst($overtime->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Overtime Details</h3></div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Date</div>
                            <div class="datagrid-content">{{ $overtime->date->format('d M Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Start Time</div>
                            <div class="datagrid-content">{{ $overtime->start_time?->format('H:i') ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">End Time</div>
                            <div class="datagrid-content">{{ $overtime->end_time?->format('H:i') ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Total Hours</div>
                            <div class="datagrid-content">{{ $overtime->total_hours }}h</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Status</div>
                            <div class="datagrid-content">
                                <span class="badge bg-{{ $overtime->status === 'approved' ? 'success' : ($overtime->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($overtime->status) }}</span>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Approved By</div>
                            <div class="datagrid-content">{{ $overtime->approver?->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4>Reason</h4>
                        <p>{{ $overtime->reason ?? 'No reason provided' }}</p>
                    </div>

                    @if($overtime->approval_reason)
                        <div class="mt-3">
                            <h4>Approval Notes</h4>
                            <p>{{ $overtime->approval_reason }}</p>
                        </div>
                    @endif

                    @if($overtime->attachment)
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $overtime->attachment) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="ti ti-file-download"></i> View Attachment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
