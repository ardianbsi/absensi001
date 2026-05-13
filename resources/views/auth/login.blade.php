@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Login to your account</h2>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label required">
                        Password
                        @if(Route::has('password.request'))
                            <span class="form-label-description">
                                <a href="{{ route('password.request') }}">Forgot password?</a>
                            </span>
                        @endif
                    </label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Your password" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                        <span class="form-check-label">Remember me</span>
                    </label>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Sign in</button>
                </div>
            </form>
        </div>
    </div>

    @if(Route::has('register'))
        <div class="text-center text-muted mt-3">
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </div>
    @endif
@endsection
