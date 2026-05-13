@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
    <x-breadcrumb :items="[['name' => 'Departments', 'route' => 'departments.index'], ['name' => $department->name, 'route' => 'departments.edit'], ['name' => 'Edit']]" />
    <x-page-header title="Edit Department" subtitle="Update department details" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('departments.update', $department) }}" method="POST">
                @csrf @method('PUT')
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Code</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $department->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $department->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Department</button>
                </div>
            </form>
        </div>
    </div>
@endsection
