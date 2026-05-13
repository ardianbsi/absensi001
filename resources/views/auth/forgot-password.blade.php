@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Forgot password</h2>
            <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Send reset link</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center text-muted mt-3">
        <a href="{{ route('login') }}">Back to login</a>
    </div>
@endsection
