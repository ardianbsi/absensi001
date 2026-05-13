@extends('layouts.app')

@section('title', 'Create Menu')

@section('content')
    <x-breadcrumb :items="[['name' => 'Menus', 'route' => 'menus.index'], ['name' => 'Create']]" />
    <x-page-header title="Create Menu" subtitle="Add a new menu item" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST">
                @csrf
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="ti ti-home">
                        @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}">
                        @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Route Name</label>
                        <input type="text" name="route" class="form-control @error('route') is-invalid @enderror" value="{{ old('route') }}" placeholder="employees.index">
                        @error('route') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">URL (if no route)</label>
                        <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}">
                        @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Menu</label>
                        <select name="parent_id" class="form-select tom-select">
                            <option value="">No Parent (Top Level)</option>
                            @foreach($parentMenus as $id => $name)
                                <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="permission_name" class="form-control" value="{{ old('permission_name') }}" placeholder="employee-read">
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Menu</button>
                </div>
            </form>
        </div>
    </div>
@endsection
