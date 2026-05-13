@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
    <x-breadcrumb :items="[['name' => 'Roles', 'route' => 'roles.index'], ['name' => 'Create']]" />
    <x-page-header title="Create Role" subtitle="Add a new role with permissions" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif

                <div class="mb-3">
                    <label class="form-label required">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. HR Manager" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="accordion" id="permsAccordion">
                        @forelse($permissions as $group => $groupPerms)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#perm-group-{{ Str::slug($group) }}">
                                        <strong>{{ ucfirst($group) }}</strong>
                                    </button>
                                </h2>
                                <div id="perm-group-{{ Str::slug($group) }}" class="accordion-collapse collapse" data-bs-parent="#permsAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            @foreach($groupPerms as $perm)
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-check">
                                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="form-check-input" {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <span class="form-check-label">{{ $perm->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No permissions available.</p>
                        @endforelse
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection
