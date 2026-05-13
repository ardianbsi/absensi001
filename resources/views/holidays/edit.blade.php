@extends('layouts.app')

@section('title', 'Edit Holiday')

@section('content')
    <x-breadcrumb :items="[['name' => 'Holidays', 'route' => 'holidays.index'], ['name' => $holiday->name, 'route' => 'holidays.edit'], ['name' => 'Edit']]" />
    <x-page-header title="Edit Holiday" subtitle="Update holiday details" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('holidays.update', $holiday) }}" method="POST">
                @csrf @method('PUT')
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $holiday->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Date</label>
                        <input type="text" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Year</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $holiday->year) }}" required>
                        @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" name="is_recurring" class="form-check-input" value="1" id="is_recurring" {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_recurring">Recurring (every year)</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $holiday->description) }}</textarea>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Holiday</button>
                </div>
            </form>
        </div>
    </div>
@endsection
