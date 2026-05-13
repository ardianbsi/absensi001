@extends('layouts.app')

@section('title', 'Holidays')

@section('content')
    <x-breadcrumb :items="[['name' => 'Holidays']]" />
    <x-page-header title="Holidays" subtitle="Manage company holidays">
        <a href="{{ route('holidays.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add Holiday</a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Date', 'Name', 'Year', 'Recurring', 'Description']">
                @forelse($holidays as $holiday)
                    <tr>
                        <td>{{ $holiday->date->format('d M Y') }}</td>
                        <td>{{ $holiday->name }}</td>
                        <td><span class="badge bg-blue">{{ $holiday->year }}</span></td>
                        <td>
                            <span class="badge {{ $holiday->is_recurring ? 'bg-success' : 'bg-secondary' }}">{{ $holiday->is_recurring ? 'Yes' : 'No' }}</span>
                        </td>
                        <td>{{ $holiday->description ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('holidays.edit', $holiday) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $holiday->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $holiday->id }}" action="{{ route('holidays.destroy', $holiday) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No holidays found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $holidays->links() }}</div>
        </div>
    </div>
@endsection
