@extends('layouts.app')

@section('title', 'Departments')

@section('content')
    <x-breadcrumb :items="[['name' => 'Departments']]" />
    <x-page-header title="Departments" subtitle="Manage company departments">
        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Add Department
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Code', 'Name', 'Description', 'Employees', 'Active']">
                @forelse($departments as $dept)
                    <tr>
                        <td><span class="badge bg-blue">{{ $dept->code }}</span></td>
                        <td>{{ $dept->name }}</td>
                        <td>{{ $dept->description ?? '-' }}</td>
                        <td><span class="badge bg-green">{{ $dept->employees_count }}</span></td>
                        <td>
                            <span class="badge {{ $dept->is_active ? 'bg-success' : 'bg-danger' }}">{{ $dept->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('departments.edit', $dept) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $dept->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $dept->id }}" action="{{ route('departments.destroy', $dept) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No departments found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $departments->links() }}</div>
        </div>
    </div>
@endsection
