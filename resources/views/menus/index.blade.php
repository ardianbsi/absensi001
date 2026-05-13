@extends('layouts.app')

@section('title', 'Menus')

@section('content')
    <x-breadcrumb :items="[['name' => 'Menus']]" />
    <x-page-header title="Menus" subtitle="Manage application menus">
        <a href="{{ route('menus.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add Menu</a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <x-table :headers="['Name', 'Icon', 'Route / URL', 'Parent', 'Order', 'Active']">
                @forelse($menus as $menu)
                    <tr>
                        <td>{{ $menu->name }}</td>
                        <td><i class="{{ $menu->icon ?? 'ti ti-circle' }}"></i></td>
                        <td><code>{{ $menu->route ?? $menu->url ?? '-' }}</code></td>
                        <td>{{ $menu->parent?->name ?? '-' }}</td>
                        <td>{{ $menu->order ?? 0 }}</td>
                        <td><span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-danger' }}">{{ $menu->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('menus.edit', $menu) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $menu->id }}').submit())"><i class="ti ti-trash"></i></button>
                            </div>
                            <form id="del-{{ $menu->id }}" action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                    @if($menu->children->count() > 0)
                        @foreach($menu->children as $child)
                            <tr class="table-active">
                                <td> &mdash; {{ $child->name }}</td>
                                <td><i class="{{ $child->icon ?? 'ti ti-circle' }}"></i></td>
                                <td><code>{{ $child->route ?? $child->url ?? '-' }}</code></td>
                                <td>{{ $menu->name }}</td>
                                <td>{{ $child->order ?? 0 }}</td>
                                <td><span class="badge {{ $child->is_active ? 'bg-success' : 'bg-danger' }}">{{ $child->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('menus.edit', $child) }}" class="btn btn-outline-warning"><i class="ti ti-edit"></i></a>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete().then((r) => r.isConfirmed && document.getElementById('del-{{ $child->id }}').submit())"><i class="ti ti-trash"></i></button>
                                    </div>
                                    <form id="del-{{ $child->id }}" action="{{ route('menus.destroy', $child) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No menus found</td></tr>
                @endforelse
            </x-table>
            <div class="mt-3">{{ $menus->links() }}</div>
        </div>
    </div>
@endsection
