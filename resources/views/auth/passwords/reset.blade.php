@extends('layouts.guest')

@section('content')
<div class="auth-container" dir="{{ $dir }}">
    <div class="auth-card-container">
        <div class="auth-logo">{{ $t('messages.app_name') }}</div>

        <h2 class="auth-title">{{ $t('messages.reset_password_title') ?? 'Reset Password' }}</h2>
        <p class="auth-subtitle">{{ $t('messages.reset_password_subtitle') ?? 'Enter your new password below' }}</p>

        @if (session('status'))
        <div data-flash-message="{{ session('status') }}" data-flash-type="success"></div>
        @endif

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="auth-field">
                <label>{{ $t('messages.email') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="email"
                        value="{{ $email }}"
                        type="email"
                        class="form-control auth-input"
                        readonly
                        required />
                    <i class="fa-solid fa-envelope auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('email')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            <div class="auth-field">
                <label>{{ $t('messages.password') }} {{ $t('messages.new') ?? 'New' }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="password"
                        type="password"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.enter_new_password') ?? 'Enter new password' }}"
                        required
                        minlength="8" />
                    <i class="fa-solid fa-lock auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('password')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            <div class="auth-field">
                <label>{{ $t('messages.password_confirm') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="password_confirmation"
                        type="password"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.confirm_password') ?? 'Confirm password' }}"
                        required
                        minlength="8" />
                    <i class="fa-solid fa-lock auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('password_confirmation')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            @include('partials.recaptcha')

            <button type="submit" class="auth-button">{{ $t('messages.reset_password_button') ?? 'Reset Password' }}</button>
        </form>

        <p class="auth-link">
            <a href="{{ route('login') }}">{{ $t('messages.back_to_login') ?? 'Back to Login' }}</a>
        </p>
    </div>
</div>
@endsection