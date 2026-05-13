@extends('layouts.app')

@section('title', 'Shifts')

@section('content')
    <x-breadcrumb :items="[['name' => 'Shifts']]" />

    <x-page-header title="Shifts" subtitle="Manage work shifts">
        <a href="{{ route('shifts.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Add Shift
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Code', 'Name', 'Clock In', 'Clock Out', 'Type', 'Tolerance', 'Active']">
                @forelse($shifts as $shift)
                    <tr>
                        <td><span class="badge bg-blue">{{ $shift->code }}</span></td>
                        <td>
                            <span class="badge" style="background-color: {{ $shift->color ?? '#6c757d' }}">{{ $shift->name }}</span>
                        </td>
                        <td>{{ $shift->clock_in_time ? substr($shift->clock_in_time, 0, 5) : '-' }}</td>
                        <td>{{ $shift->clock_out_time ? substr($shift->clock_out_time, 0, 5) : '-' }}</td>
                        <td>{{ ucfirst($shift->type) }}</td>
                        <td>{{ $shift->late_tolerance_minutes ?? 0 }} min</td>
                        <td>
                            <span class="badge {{ $shift->is_active ? 'bg-success' : 'bg-danger' }}">{{ $shift->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $shift->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $shift->id }}" action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No shifts found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $shifts->links() }}</div>
        </div>
    </div>
@endsection
