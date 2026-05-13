@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <x-breadcrumb :items="[['name' => 'Profile']]" />

    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Profile</h2>
                <div class="text-muted mt-1">Your account information</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <i class="ti ti-edit"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @php
                            $photoUrl = $user->employee?->photo
                                ? asset('storage/' . $user->employee->photo)
                                : ($user->profile_photo_path
                                    ? asset('storage/' . $user->profile_photo_path)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
                        @endphp
                        <span class="avatar avatar-xl" style="background-image: url({{ $photoUrl }})"></span>
                    </div>
                    <h3>{{ $user->name }}</h3>
                    <div class="text-muted">{{ $user->email }}</div>
                    <div class="mt-2">
                        @foreach($user->roles as $role)
                            <span class="badge bg-blue">{{ $role->name }}</span>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-camera"></i> Change Photo
                                <input type="file" name="photo" class="d-none" accept="image/*" onchange="this.form.submit()">
                            </label>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Account Information</h3></div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Name</div>
                            <div class="datagrid-content">{{ $user->name }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Email</div>
                            <div class="datagrid-content">{{ $user->email }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Member Since</div>
                            <div class="datagrid-content">{{ $user->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Last Login</div>
                            <div class="datagrid-content">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->employee)
            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Employee Information</h3></div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">NIK</div>
                            <div class="datagrid-content">{{ $user->employee->nik }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Full Name</div>
                            <div class="datagrid-content">{{ $user->employee->full_name }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Department</div>
                            <div class="datagrid-content">{{ $user->employee->department?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Position</div>
                            <div class="datagrid-content">{{ $user->employee->position?->name ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Phone</div>
                            <div class="datagrid-content">{{ $user->employee->phone ?? '-' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Employment Status</div>
                            <div class="datagrid-content">{{ ucfirst($user->employee->employment_status ?? '-') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Change Password</h3></div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        @if($errors->any())
                            <div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>
                        @endif
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
