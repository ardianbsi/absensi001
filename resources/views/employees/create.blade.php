@extends('layouts.app')

@section('title', 'Create Employee')

@section('content')
    <x-breadcrumb :items="[['name' => 'Employees', 'route' => 'employees.index'], ['name' => 'Create']]" />

    <x-page-header title="Create Employee" subtitle="Add a new employee to the system" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">NIK</label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" required>
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Full Name</label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Birth Date</label>
                        <input type="text" name="birth_date" class="form-control datepicker @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}">
                        @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Department</label>
                        <select name="department_id" class="form-select tom-select @error('department_id') is-invalid @enderror" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Position</label>
                        <select name="position_id" class="form-select tom-select @error('position_id') is-invalid @enderror" required>
                            <option value="">Select Position</option>
                            @foreach($positions as $id => $name)
                                <option value="{{ $id }}" {{ old('position_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('position_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Employment Status</label>
                        <select name="employment_status" class="form-select @error('employment_status') is-invalid @enderror" required>
                            <option value="">Select Status</option>
                            <option value="permanent" {{ old('employment_status') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="contract" {{ old('employment_status') == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="intern" {{ old('employment_status') == 'intern' ? 'selected' : '' }}>Intern</option>
                            <option value="probation" {{ old('employment_status') == 'probation' ? 'selected' : '' }}>Probation</option>
                        </select>
                        @error('employment_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Join Date</label>
                        <input type="text" name="join_date" class="form-control datepicker @error('join_date') is-invalid @enderror" value="{{ old('join_date') }}">
                        @error('join_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Manager</label>
                        <select name="manager_id" class="form-select tom-select @error('manager_id') is-invalid @enderror">
                            <option value="">No Manager</option>
                            @foreach($managers as $id => $name)
                                <option value="{{ $id }}" {{ old('manager_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('manager_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Shift</label>
                        <select name="shift_id" class="form-select tom-select @error('shift_id') is-invalid @enderror">
                            <option value="">No Shift</option>
                            @foreach($shifts as $id => $name)
                                <option value="{{ $id }}" {{ old('shift_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('shift_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" onchange="previewPhoto(event)">
                        <small class="form-hint">Max 2MB. JPEG, PNG, or WebP.</small>
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <img id="photo-preview" src="#" alt="Preview" class="d-none" style="max-height: 120px; border-radius: 8px;">
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Employee</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewPhoto(e) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var img = document.getElementById('photo-preview');
                img.src = ev.target.result;
                img.classList.remove('d-none');
            }
            if (e.target.files[0]) reader.readAsDataURL(e.target.files[0]);
        }
    </script>
    @endpush
@endsection
