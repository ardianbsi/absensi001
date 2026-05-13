@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <x-breadcrumb :items="[['name' => 'Users', 'route' => 'users.index'], ['name' => $user->name, 'route' => 'users.edit'], ['name' => 'Edit']]" />
    <x-page-header title="Edit User" subtitle="Update user details" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">New Password <small class="text-muted">(leave empty to keep)</small></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Role</label>
                    <select name="role_id" class="form-select tom-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $id => $name)
                            <option value="{{ $id }}" {{ old('role_id', $userRole?->id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Update User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
