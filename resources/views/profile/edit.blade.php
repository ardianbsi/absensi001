@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <x-breadcrumb :items="[['name' => 'Profile', 'route' => 'profile.show'], ['name' => 'Edit']]" />

    <x-page-header title="Edit Profile" subtitle="Update your account information" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('PUT')

                @if(session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Profile updated successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
