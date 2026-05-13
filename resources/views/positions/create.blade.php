@extends('layouts.app')

@section('title', 'Create Position')

@section('content')
    <x-breadcrumb :items="[['name' => 'Positions', 'route' => 'positions.index'], ['name' => 'Create']]" />
    <x-page-header title="Create Position" subtitle="Add a new job position" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('positions.store') }}" method="POST">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>
                @endif
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Code</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Department</label>
                    <select name="department_id" class="form-select tom-select @error('department_id') is-invalid @enderror" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Position</button>
                </div>
            </form>
        </div>
    </div>
@endsection
