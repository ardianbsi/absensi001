@extends('layouts.app')

@section('title', 'Request Overtime')

@section('content')
    <x-breadcrumb :items="[['name' => 'Overtimes', 'route' => 'overtimes.index'], ['name' => 'Request']]" />
    <x-page-header title="Request Overtime" subtitle="Submit a new overtime request" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('overtimes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Date</label>
                        <input type="text" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Start Time</label>
                        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                        @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">End Time</label>
                        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                        @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Reason</label>
                    <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" placeholder="Why is overtime needed?" required>{{ old('reason') }}</textarea>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Attachment (optional)</label>
                    <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-hint">Approval form or supporting document. Max 5MB.</small>
                    @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Submit Overtime Request</button>
                </div>
            </form>
        </div>
    </div>
@endsection
