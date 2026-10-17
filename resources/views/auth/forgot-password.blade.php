@extends('layouts.auth')

@section('title', 'Forgot Password — ' . $brandName)
@section('subtitle', 'Reset your password')

@section('content')
    <p class="text-muted small mb-4">Enter your email address and we'll send you a link to reset your password.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control yc-input" required autofocus autocomplete="email">
        </div>

        <button type="submit" class="btn btn-yc btn-lg w-100">Send Password Reset Link</button>
    </form>
@endsection