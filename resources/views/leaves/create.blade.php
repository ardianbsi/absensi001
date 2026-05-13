@extends('layouts.app')

@section('title', 'Request Leave')

@section('content')
    <x-breadcrumb :items="[['name' => 'Leaves', 'route' => 'leaves.index'], ['name' => 'Request']]" />
    <x-page-header title="Request Leave" subtitle="Submit a new leave request" />

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label required">Leave Type</label>
                            <select name="leave_type_id" class="form-select tom-select @error('leave_type_id') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($leaveTypes as $lt)
                                    <option value="{{ $lt->id }}" {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>{{ $lt->name }}</option>
                                @endforeach
                            </select>
                            @error('leave_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Start Date</label>
                                <input type="text" name="start_date" class="form-control datepicker @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">End Date</label>
                                <input type="text" name="end_date" class="form-control datepicker @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" placeholder="Explain the reason for your leave...">{{ old('reason') }}</textarea>
                            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="form-hint">Medical certificate or supporting document. Max 5MB.</small>
                            @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Leave Balance</h3></div>
                <div class="card-body">
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
                        <p class="text-muted">No leave balance data available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
