@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
    <x-breadcrumb :items="[['name' => 'Announcements', 'route' => 'announcements.index'], ['name' => 'Create']]" />
    <x-page-header title="Create Announcement" subtitle="Publish a new announcement" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif

                <div class="mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label required">Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Published At</label>
                        <input type="text" name="published_at" class="form-control datetimepicker @error('published_at') is-invalid @enderror" value="{{ old('published_at', now()->format('Y-m-d H:i:s')) }}">
                        @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Content</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="6" required>{{ old('content') }}</textarea>
                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Publish Announcement</button>
                </div>
            </form>
        </div>
    </div>
@endsection
