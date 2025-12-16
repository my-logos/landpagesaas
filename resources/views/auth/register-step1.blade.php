@extends('layouts.guest')

@section('content')
<div class="auth-container" dir="{{ $dir }}">
    <div class="auth-card-container">
        <div class="auth-logo">{{ $t('messages.app_name') }}</div>

        <h2 class="auth-title">{{ $t('messages.register_new_account') }}</h2>
        <p class="auth-subtitle">{{ $t('messages.register_subtitle') }}</p>

        <div class="auth-step-indicator">
            <div class="auth-step active">1</div>
            <div class="auth-step inactive">2</div>
        </div>

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif

        <form method="POST" action="{{ route('register.step1.post') }}">
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

            <button type="submit" class="auth-button">{{ $t('messages.continue') }}</button>
        </form>

        <p class="auth-link">
            {{ $t('messages.already_user') }}
            <a href="{{ route('login') }}">{{ $t('messages.login') }}</a>
        </p>

        <p class="auth-link">
            <a href="/">{{ $t('messages.back_home') }}</a>
        </p>
    </div>
</div>
@endsection