@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <x-breadcrumb :items="[['name' => 'Roles']]" />
    <x-page-header title="Roles" subtitle="Manage user roles and permissions">
        <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add Role</a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Name', 'Permissions', 'Users Count']">
                @forelse($roles as $role)
                    <tr>
                        <td><span class="badge bg-blue">{{ $role->name }}</span></td>
                        <td>
                            @foreach($role->permissions->take(5) as $perm)
                                <span class="badge bg-secondary me-1">{{ $perm->name }}</span>
                            @endforeach
                            @if($role->permissions->count() > 5)
                                <span class="badge bg-dark">+{{ $role->permissions->count() - 5 }}</span>
                            @endif
                        </td>
                        <td><span class="badge bg-green">{{ $role->users_count ?? $role->users?->count() ?? 0 }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $role->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $role->id }}" action="{{ route('roles.destroy', $role) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No roles found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $roles->links() }}</div>
        </div>
    </div>
@endsection
