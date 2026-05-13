@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <x-breadcrumb :items="[['name' => 'Users', 'route' => 'users.index'], ['name' => 'Create']]" />
    <x-page-header title="Create User" subtitle="Add a new system user" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                        @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Role</label>
                    <select name="role_id" class="form-select tom-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $id => $name)
                            <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
