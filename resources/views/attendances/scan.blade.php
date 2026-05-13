@extends('layouts.app')

@section('title', 'Attendance Scan')

@section('content')
    <x-breadcrumb :items="[['name' => 'Attendance', 'route' => 'attendance.index'], ['name' => 'Scan']]" />

    <x-page-header title="Attendance Scan" subtitle="Check in or check out" />

    @if(!auth()->user()->employee)
        <div class="alert alert-danger mt-3">You do not have an employee record. Please contact HR.</div>
    @else
        @php
            $hasCheckedIn = $activeAttendance && $activeAttendance->clock_in;
            $hasCheckedOut = $activeAttendance && $activeAttendance->clock_out;
        @endphp

        <div class="row row-deck row-cards mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <span class="avatar avatar-xl" style="background-image: url({{ auth()->user()->employee->photo ? asset('storage/' . auth()->user()->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }})"></span>
                        </div>
                        <h2>{{ auth()->user()->name }}</h2>
                        <div class="text-muted mb-4">{{ auth()->user()->employee->department?->name ?? '-' }}</div>

                        @if(!$hasCheckedIn)
                            <div class="mb-4">
                                <span class="badge bg-danger fs-5 p-2">Not Checked In</span>
                            </div>
                            <form action="{{ route('attendance.check-in') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Selfie Photo</label>
                                        <input type="file" name="selfie" class="form-control @error('selfie') is-invalid @enderror" accept="image/*" capture="environment" required>
                                        @error('selfie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <small class="form-hint">Take a selfie for verification</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Notes</label>
                                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="Optional notes...">{{ old('note') }}</textarea>
                                        @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="ti ti-login"></i> Check In
                                </button>
                            </form>

                        @elseif($hasCheckedIn && !$hasCheckedOut)
                            <div class="mb-4">
                                <span class="badge bg-success fs-5 p-2">Checked In at {{ $activeAttendance->clock_in->format('H:i') }}</span>
                            </div>
                            <form action="{{ route('attendance.check-out', $activeAttendance) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Selfie Photo</label>
                                        <input type="file" name="selfie" class="form-control @error('selfie') is-invalid @enderror" accept="image/*" capture="environment" required>
                                        @error('selfie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Notes</label>
                                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="Optional notes...">{{ old('note') }}</textarea>
                                        @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="ti ti-logout"></i> Check Out
                                </button>
                            </form>

                        @else
                            <div class="mb-4">
                                <span class="badge bg-secondary fs-5 p-2">Completed</span>
                                <div class="mt-2 text-muted">{{ $activeAttendance->clock_in->format('H:i') }} &mdash; {{ $activeAttendance->clock_out->format('H:i') }}</div>
                            </div>
                            <div class="alert alert-success">You have completed today's attendance.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Today's Info</h3></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted">Date</div>
                            <div class="fw-bold">{{ now()->format('l, d F Y') }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted">Current Time</div>
                            <div class="fw-bold" id="current-time">{{ now()->format('H:i:s') }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted">Your Shift</div>
                            <div class="fw-bold">{{ auth()->user()->employee->shift?->name ?? 'No Shift Assigned' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted">Location</div>
                            <div class="fw-bold" id="location-status">Detecting...</div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Recent Activity</h3></div>
                    <div class="list-group list-group-flush">
                        @forelse(auth()->user()->employee->attendances()->latest()->take(5)->get() as $att)
                            <div class="list-group-item d-flex justify-content-between">
                                <span>{{ $att->date->format('d M') }}</span>
                                <span class="badge bg-{{ $att->status === 'hadir' ? 'success' : ($att->status === 'telat' ? 'warning' : 'secondary') }}">{{ ucfirst($att->status) }}</span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">No records</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var timeEl = document.getElementById('current-time');
            if (timeEl) {
                setInterval(function() {
                    var now = new Date();
                    timeEl.textContent = now.toTimeString().split(' ')[0];
                }, 1000);
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    document.getElementById('latitude').value = pos.coords.latitude;
                    document.getElementById('longitude').value = pos.coords.longitude;
                    document.getElementById('location-status').textContent = 'Location detected';
                }, function() {
                    document.getElementById('location-status').textContent = 'Location unavailable';
                });
            } else {
                document.getElementById('location-status').textContent = 'GPS not supported';
            }
        });
    </script>
    @endpush
@endsection
