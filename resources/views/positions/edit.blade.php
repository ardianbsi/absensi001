@extends('layouts.app')

@section('title', 'Edit Position')

@section('content')
    <x-breadcrumb :items="[['name' => 'Positions', 'route' => 'positions.index'], ['name' => $position->name, 'route' => 'positions.edit'], ['name' => 'Edit']]" />
    <x-page-header title="Edit Position" subtitle="Update position details" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('positions.update', $position) }}" method="POST">
                @csrf @method('PUT')
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Code</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $position->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $position->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Department</label>
                    <select name="department_id" class="form-select tom-select @error('department_id') is-invalid @enderror" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ old('department_id', $position->department_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $position->description) }}</textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', $position->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Position</button>
                </div>
            </form>
        </div>
    </div>
@endsection
