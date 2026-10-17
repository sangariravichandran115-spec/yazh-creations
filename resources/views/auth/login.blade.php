@extends('layouts.auth')

@section('title', 'Login — ' . $brandName)

@section('content')
    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control yc-input" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control yc-input" required autocomplete="current-password">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}" class="small yc-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-yc btn-lg w-100">Sign In</button>
    </form>
@endsection