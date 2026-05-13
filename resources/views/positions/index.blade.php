@extends('layouts.app')

@section('title', 'Positions')

@section('content')
    <x-breadcrumb :items="[['name' => 'Positions']]" />
    <x-page-header title="Positions" subtitle="Manage job positions">
        <a href="{{ route('positions.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add Position</a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Code', 'Name', 'Department', 'Employees', 'Active']">
                @forelse($positions as $pos)
                    <tr>
                        <td><span class="badge bg-purple">{{ $pos->code }}</span></td>
                        <td>{{ $pos->name }}</td>
                        <td>{{ $pos->department?->name ?? '-' }}</td>
                        <td><span class="badge bg-green">{{ $pos->employees_count }}</span></td>
                        <td><span class="badge {{ $pos->is_active ? 'bg-success' : 'bg-danger' }}">{{ $pos->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('positions.edit', $pos) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $pos->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $pos->id }}" action="{{ route('positions.destroy', $pos) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No positions found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $positions->links() }}</div>
        </div>
    </div>
@endsection
