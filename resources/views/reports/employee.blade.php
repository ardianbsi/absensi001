@extends('layouts.app')

@section('title', 'Employee Report')

@section('content')
    <x-breadcrumb :items="[['name' => 'Reports', 'route' => 'report.employee'], ['name' => 'Employee']]" />

    <x-page-header title="Employee Attendance Report" subtitle="View attendance records per employee">
        <a href="{{ route('report.export-excel', 'employee') }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-success">
            <i class="ti ti-file-spreadsheet"></i> Excel
        </a>
        <a href="{{ route('report.export-pdf', 'employee') }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-danger">
            <i class="ti ti-file-pdf"></i> PDF
        </a>
    </x-page-header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('report.employee') }}" class="row g-3 mb-3">
                <div class="col-md-5">
                    <div class="input-icon">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or NIK..." value="{{ request('search') }}">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    </div>
                </div>
                <div class="col-md-5">
                    <select name="department_id" class="form-select tom-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td><code>{{ $emp->nik }}</code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2" style="background-image: url({{ $emp->photo ? asset('storage/' . $emp->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($emp->full_name) }})"></span>
                                    <div>
                                        {{ $emp->full_name }}
                                        <div class="text-muted small">{{ $emp->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $emp->department?->name ?? '-' }}</td>
                            <td>{{ $emp->position?->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $emp->employment_status === 'permanent' ? 'success' : ($emp->employment_status === 'contract' ? 'info' : 'warning') }}">
                                    {{ ucfirst($emp->employment_status ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('employees.show', $emp) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-eye"></i> View Attendance
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No employees found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $employees->withQueryString()->links() }}</div>
@endsection
