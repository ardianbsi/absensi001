@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <x-breadcrumb :items="[['name' => 'Announcements']]" />
    <x-page-header title="Announcements" subtitle="Manage company announcements">
        <a href="{{ route('announcements.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add Announcement</a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Title', 'Type', 'Status', 'Published', 'Created By']">
                @forelse($announcements as $ann)
                    <tr>
                        <td>{{ $ann->title }}</td>
                        <td><span class="badge bg-{{ $ann->type === 'info' ? 'info' : ($ann->type === 'warning' ? 'warning' : ($ann->type === 'urgent' ? 'danger' : 'primary')) }}">{{ ucfirst($ann->type) }}</span></td>
                        <td><span class="badge {{ $ann->is_active ? 'bg-success' : 'bg-danger' }}">{{ $ann->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>{{ $ann->published_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>{{ $ann->creator?->name ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('announcements.edit', $ann) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $ann->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $ann->id }}" action="{{ route('announcements.destroy', $ann) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No announcements found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $announcements->links() }}</div>
        </div>
    </div>
@endsection
