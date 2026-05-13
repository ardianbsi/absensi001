@extends('layouts.app')

@section('title', 'Create Holiday')

@section('content')
    <x-breadcrumb :items="[['name' => 'Holidays', 'route' => 'holidays.index'], ['name' => 'Create']]" />
    <x-page-header title="Create Holiday" subtitle="Add a new holiday" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('holidays.store') }}" method="POST">
                @csrf
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Date</label>
                        <input type="text" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Year</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" required>
                        @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check">
                            <input type="checkbox" name="is_recurring" class="form-check-input" value="1" id="is_recurring" {{ old('is_recurring') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_recurring">Recurring (every year)</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Holiday</button>
                </div>
            </form>
        </div>
    </div>
@endsection
