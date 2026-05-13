@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <x-breadcrumb :items="[['name' => 'Users']]" />
    <x-page-header title="Users" subtitle="Manage system users">
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add User</a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-3">
                <div class="col-md-8">
                    <div class="input-icon">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
                </div>
            </form>

            <x-table :headers="['Name', 'Email', 'Roles', 'Last Login', 'Active']">
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($user->name) }})"></span>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-blue me-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <a href="{{ route('users.toggle-status', $user) }}" class="btn btn-outline-{{ $user->is_active ? 'danger' : 'success' }}" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="ti ti-{{ $user->is_active ? 'player-pause' : 'player-play' }}"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $user->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $users->withQueryString()->links() }}</div>
        </div>
    </div>
@endsection
