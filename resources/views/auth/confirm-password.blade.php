@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Confirm your password</h2>
            <p class="text-muted mb-4">This is a secure area of the application. Please confirm your password before continuing.</p>

            <form action="{{ route('password.confirm') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label required">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autofocus>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Confirm</button>
                </div>
            </form>
        </div>
    </div>
@endsection
