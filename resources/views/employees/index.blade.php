@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <x-breadcrumb :items="[['name' => 'Employees']]" />

    <x-page-header title="Employees" subtitle="Manage all employees">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Add Employee
        </a>
        <a href="{{ route('employees.export') }}" class="btn btn-outline-primary">
            <i class="ti ti-download"></i> Export
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="ti ti-upload"></i> Import
        </button>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('employees.index') }}" class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="input-icon">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, NIK, email..." value="{{ request('search') }}">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="department_id" class="form-select tom-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select tom-select">
                        <option value="">All Status</option>
                        <option value="permanent" {{ request('status') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contract" {{ request('status') == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="intern" {{ request('status') == 'intern' ? 'selected' : '' }}>Intern</option>
                        <option value="probation" {{ request('status') == 'probation' ? 'selected' : '' }}>Probation</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="position_id" class="form-select tom-select">
                        <option value="">All Positions</option>
                        @foreach($positions as $id => $name)
                            <option value="{{ $id }}" {{ request('position_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i></button>
                </div>
            </form>

            <x-table :headers="['NIK', 'Name', 'Department', 'Position', 'Status', 'Active']">
                @forelse($employees as $emp)
                    <tr>
                        <td><a href="{{ route('employees.show', $emp) }}">{{ $emp->nik }}</a></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url({{ $emp->photo ? asset('storage/' . $emp->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($emp->full_name) }})"></span>
                                <div>
                                    <a href="{{ route('employees.show', $emp) }}" class="text-reset">{{ $emp->full_name }}</a>
                                    <div class="text-muted small">{{ $emp->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $emp->department?->name ?? '-' }}</td>
                        <td>{{ $emp->position?->name ?? '-' }}</td>
                        <td>
                            @php
                                $statusBadge = ['permanent' => 'bg-green', 'contract' => 'bg-blue', 'intern' => 'bg-purple', 'probation' => 'bg-yellow'];
                            @endphp
                            <span class="badge {{ $statusBadge[$emp->employment_status] ?? 'bg-secondary' }}">{{ ucfirst($emp->employment_status) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $emp->is_active ? 'bg-success' : 'bg-danger' }}">{{ $emp->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('employees.show', $emp) }}" class="btn btn-outline-primary" title="View"><i class="ti ti-eye"></i></a>
                                <a href="{{ route('employees.edit', $emp) }}" class="btn btn-outline-warning" title="Edit"><i class="ti ti-edit"></i></a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete('Delete {{ $emp->full_name }}?').then((r) => r.isConfirmed && document.getElementById('delete-form-{{ $emp->id }}').submit())">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $emp->id }}" action="{{ route('employees.destroy', $emp) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No employees found</td></tr>
                @endforelse
            </x-table>

            <div class="mt-3">
                {{ $employees->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <x-modal id="importModal" title="Import Employees">
        <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Excel / CSV File</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small class="form-hint">Upload file with columns: NIK, Name, Email, Department, Position, etc.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </x-modal>
@endsection
