@extends('layouts.guest')

@section('content')
<div class="auth-container" dir="{{ $dir }}">
    <div class="auth-card-container">
        <div class="auth-logo">{{ $t('messages.app_name') }}</div>

        <h2 class="auth-title">{{ $t('messages.forgot_password_title') }}</h2>
        <p class="auth-subtitle">{{ $t('messages.forgot_password_subtitle') }}</p>

        @if (session('status'))
        <div data-flash-message="{{ session('status') }}" data-flash-type="success"></div>
        @endif

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="auth-field">
                <label>{{ $t('messages.email') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="email"
                        value="{{ old('email') }}"
                        type="email"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.enter_email_address') }}"
                        required />
                    <i class="fa-solid fa-envelope auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('email')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            @include('partials.recaptcha')

            <button type="submit" class="auth-button">{{ $t('messages.send_reset_link_button') }}</button>
        </form>

        <p class="auth-link">
            {{ $t('messages.remember_password') }}
            <a href="{{ route('login') }}">{{ $t('messages.login') }}</a>
        </p>

        <p class="auth-link">
            <a href="/">{{ $t('messages.back_home') }}</a>
        </p>
    </div>
</div>
@endsection