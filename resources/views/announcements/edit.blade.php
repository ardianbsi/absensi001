@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
    <x-breadcrumb :items="[['name' => 'Announcements', 'route' => 'announcements.index'], ['name' => $announcement->title, 'route' => 'announcements.edit'], ['name' => 'Edit']]" />
    <x-page-header title="Edit Announcement" subtitle="Update announcement details" />

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('announcements.update', $announcement) }}" method="POST">
                @csrf @method('PUT')
                @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</div>@endif

                <div class="mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title) }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label required">Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="info" {{ old('type', $announcement->type) == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ old('type', $announcement->type) == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="urgent" {{ old('type', $announcement->type) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="general" {{ old('type', $announcement->type) == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Published At</label>
                        <input type="text" name="published_at" class="form-control datetimepicker" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d H:i:s')) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Content</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="6" required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Announcement</button>
                </div>
            </form>
        </div>
    </div>
@endsection
