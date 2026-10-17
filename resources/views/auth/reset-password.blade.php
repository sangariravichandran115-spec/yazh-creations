@extends('layouts.auth')

@section('title', 'Reset Password — ' . $brandName)
@section('subtitle', 'Set a new password')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" class="form-control yc-input" required autofocus autocomplete="email">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" id="password" name="password" class="form-control yc-input" required autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control yc-input" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-yc btn-lg w-100">Reset Password</button>
    </form>
@endsection