@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
    <x-breadcrumb :items="[['name' => 'Shifts', 'route' => 'shifts.index'], ['name' => $shift->name, 'route' => 'shifts.edit'], ['name' => 'Edit']]" />
    <x-page-header title="Edit Shift" subtitle="Update shift details" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('shifts.update', $shift) }}" method="POST">
                @csrf @method('PUT')
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $shift->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Code</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $shift->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="regular" {{ old('type', $shift->type) == 'regular' ? 'selected' : '' }}>Regular</option>
                            <option value="flexible" {{ old('type', $shift->type) == 'flexible' ? 'selected' : '' }}>Flexible</option>
                            <option value="split" {{ old('type', $shift->type) == 'split' ? 'selected' : '' }}>Split</option>
                            <option value="night" {{ old('type', $shift->type) == 'night' ? 'selected' : '' }}>Night</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label required">Clock In Time</label>
                        <input type="time" name="clock_in_time" class="form-control @error('clock_in_time') is-invalid @enderror" value="{{ old('clock_in_time', $shift->clock_in_time) }}" required>
                        @error('clock_in_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label required">Clock Out Time</label>
                        <input type="time" name="clock_out_time" class="form-control @error('clock_out_time') is-invalid @enderror" value="{{ old('clock_out_time', $shift->clock_out_time) }}" required>
                        @error('clock_out_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Late Tolerance (minutes)</label>
                        <input type="number" name="late_tolerance_minutes" class="form-control @error('late_tolerance_minutes') is-invalid @enderror" value="{{ old('late_tolerance_minutes', $shift->late_tolerance_minutes ?? 15) }}">
                        @error('late_tolerance_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Max Early Clock In (minutes)</label>
                        <input type="number" name="max_early_clock_in" class="form-control @error('max_early_clock_in') is-invalid @enderror" value="{{ old('max_early_clock_in', $shift->max_early_clock_in ?? 60) }}">
                        @error('max_early_clock_in') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror" value="{{ old('color', $shift->color ?? '#6c757d') }}">
                        @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $shift->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', $shift->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Shift</button>
                </div>
            </form>
        </div>
    </div>
@endsection
