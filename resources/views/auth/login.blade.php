@extends('layouts.guest')

@section('content')
<div class="auth-container" dir="{{ $dir }}">
    <div class="auth-card-container">
        <div class="auth-logo">{{ $t('messages.app_name') }}</div>

        <h2 class="auth-title">{{ $t('messages.login') }}</h2>
        <p class="auth-subtitle">{{ $t('messages.welcome_back') }}</p>

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="auth-field">
                <label>{{ $t('messages.email') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="email"
                        value="{{ old('email') }}"
                        type="email"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.enter_email') }}"
                        required />
                    <i class="fa-solid fa-envelope auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('email')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            <div class="auth-field">
                <label>{{ $t('messages.password') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="password"
                        type="password"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.enter_password') }}"
                        required />
                    <i class="fa-solid fa-lock auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('password')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            <div class="auth-forgot-row">
                <a href="{{ route('password.request') }}" class="auth-forgot-link">{{ $t('messages.forgot_password') }}</a>
            </div>

            @include('partials.recaptcha')

            <button type="submit" class="auth-button">{{ $t('messages.login_button') }}</button>
        </form>

        <p class="auth-link">
            {{ $t('messages.new_user') }}
            <a href="{{ route('register.step1') }}">{{ $t('messages.register_new_account') }}</a>
        </p>

        <p class="auth-link">
            <a href="/">{{ $t('messages.back_home') }}</a>
        </p>
    </div>
</div>
@endsection