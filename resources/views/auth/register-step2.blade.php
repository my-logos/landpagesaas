@extends('layouts.guest')

@section('content')
<div class="auth-container" dir="{{ $dir }}">
    <div class="auth-card-container">
        <div class="auth-logo">{{ $t('messages.app_name') }}</div>

        <h2 class="auth-title">{{ $t('messages.register_new_account') }}</h2>
        <p class="auth-subtitle">{{ $t('messages.register_subtitle') }}</p>

        <div class="auth-step-indicator">
            <div class="auth-step active">✓</div>
            <div class="auth-step active">2</div>
        </div>

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif

        <form method="POST" action="{{ route('register.step2.post') }}">
            @csrf

            <div class="auth-field">
                <label>{{ $t('messages.email') }}</label>
                <input
                    type="text"
                    class="form-control"
                    value="{{ $email }}"
                    disabled
                    class="form-control auth-input auth-input-disabled" />
            </div>

            <div class="auth-field">
                <label>{{ $t('messages.name') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="name"
                        value="{{ old('name') }}"
                        type="text"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.enter_full_name') }}"
                        required />
                    <i class="fa-solid fa-user auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
                @error('name')<span class="auth-error">{{ $message }}</span>@enderror
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

            <div class="auth-field">
                <label>{{ $t('messages.password_confirm') }} *</label>
                <div class="auth-input-wrapper">
                    <input
                        name="password_confirmation"
                        type="password"
                        class="form-control auth-input"
                        placeholder="{{ $t('messages.confirm_password') }}"
                        required />
                    <i class="fa-solid fa-lock auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                </div>
            </div>

            <div class="auth-field">
                <label>{{ $t('messages.phone') }} ({{ $t('messages.optional') }})</label>
                <div class="auth-phone-wrapper">
                    <div class="auth-country-code-wrapper">
                        <span class="auth-country-code-prefix">+</span>
                        <input
                            name="country_code"
                            type="text"
                            class="auth-country-code-input"
                            value="{{ old('country_code', '20') }}"
                            placeholder="20"
                            maxlength="4"
                            pattern="[1-9][0-9]{0,3}"
                            data-validate="country-code"
                            required />
                    </div>
                    <div class="auth-input-wrapper auth-input-wrapper-phone">
                        <input
                            name="phone"
                            value="{{ old('phone') }}"
                            type="tel"
                            class="form-control auth-input auth-phone-input"
                            placeholder="{{ $t('messages.phone_number') }}"
                            data-validate="phone" />
                        <i class="fa-solid fa-phone auth-input-icon {{ $dir === 'rtl' ? 'right' : 'left' }}"></i>
                        <span class="auth-phone-validation"></span>
                    </div>
                </div>
                @error('phone')<span class="auth-error">{{ $message }}</span>@enderror
                @error('country_code')<span class="auth-error">{{ $message }}</span>@enderror
            </div>

            @include('partials.recaptcha')

            <button type="submit" class="auth-button">{{ $t('messages.register_button') }}</button>
        </form>

        <p class="auth-link">
            <a href="/">{{ $t('messages.back_home') }}</a>
        </p>
    </div>
</div>
@endsection