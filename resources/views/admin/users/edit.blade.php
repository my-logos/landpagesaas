@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $t('messages.edit_user') ?? 'تعديل مستخدم' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> {{ $t('messages.back') ?? 'رجوع' }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin: 20px 0; padding: 15px; background-color: #d4edda; color: #155724; border-radius: 5px;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" style="margin: 20px 0; padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">
        {{ session('error') }}
    </div>
    @endif

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">{{ $t('messages.name') ?? 'Full Name' }} *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">{{ $t('messages.email') ?? 'Email' }} *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">{{ $t('messages.phone') ?? 'Phone' }}</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">{{ $t('messages.role') ?? 'Role' }}</label>
                <select name="role" id="role" class="form-control">
                    <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    {{ $t('messages.is_active') ?? 'Is Active' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="email_verified_at" value="now" {{ old('email_verified_at', $user->email_verified_at) ? 'checked' : '' }}>
                    {{ $t('messages.email_verified') ?? 'Email Verified' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="additional_sales_enabled" value="1" {{ old('additional_sales_enabled', $user->additional_sales_enabled ?? false) ? 'checked' : '' }}>
                    {{ $t('messages.enable_additional_sales') ?? 'Enable Additional Sales' }}
                </label>
            </div>

            <div class="form-group">
                <label for="performance_mode">{{ $t('messages.performance_mode') ?? 'Performance Mode' }}</label>
                <select name="performance_mode" id="performance_mode" class="form-control">
                    <option value="fast" {{ old('performance_mode', $user->performance_mode ?? 'fast') === 'fast' ? 'selected' : '' }}>{{ $t('messages.performance_mode_fast') ?? 'Fast' }}</option>
                    <option value="selective" {{ old('performance_mode', $user->performance_mode ?? 'fast') === 'selective' ? 'selected' : '' }}>{{ $t('messages.performance_mode_selective') ?? 'Selective' }}</option>
                    <option value="full" {{ old('performance_mode', $user->performance_mode ?? 'fast') === 'full' ? 'selected' : '' }}>{{ $t('messages.performance_mode_full') ?? 'Full' }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="include_session_data" value="1" {{ old('include_session_data', $user->include_session_data ?? false) ? 'checked' : '' }}>
                    {{ $t('messages.include_session_data') ?? 'Include Session Data' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="include_location_data" value="1" {{ old('include_location_data', $user->include_location_data ?? false) ? 'checked' : '' }}>
                    {{ $t('messages.include_location_data') ?? 'Include Location Data' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="include_device_type" value="1" {{ old('include_device_type', $user->include_device_type ?? false) ? 'checked' : '' }}>
                    {{ $t('messages.include_device_type') ?? 'Include Device Type' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="tips_disabled" value="1" {{ old('tips_disabled', $user->tips_disabled ?? false) ? 'checked' : '' }}>
                    {{ $t('messages.tips_disabled') ?? 'Disable Tips' }}
                </label>
            </div>

            <div class="form-group" style="padding: 15px; background-color: #f8f9fa; border-radius: 5px; margin: 20px 0;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">{{ $t('messages.wallet_balance') ?? 'Wallet Balance' }}</label>
                <input type="text" class="form-control" value="{{ number_format($user->wallet_balance ?? 0, 2) }} {{ $t('messages.currency') ?? 'EGP' }}" readonly style="background-color: #e9ecef;">
                <small style="color: #666; display: block; margin-top: 5px;">{{ $t('messages.wallet_balance_readonly') ?? 'Wallet balance cannot be edited from here. Use wallet management features.' }}</small>
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') ?? 'Save' }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
            </div>
        </form>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e0e0e0;">
            <h3 style="margin-bottom: 20px;">{{ $t('messages.email_actions') ?? 'Email Actions' }}</h3>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <form method="POST" action="{{ route('admin.users.resend-verification', $user) }}" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fa-solid fa-envelope"></i>
                        {{ $t('messages.resend_verification_email') ?? 'Resend Verification Email' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.users.send-password-reset', $user) }}" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-warning" onclick="return confirm('{{ $t('messages.confirm_send_password_reset') ?? 'Are you sure you want to send a password reset link to this user?' }}');">
                        <i class="fa-solid fa-key"></i>
                        {{ $t('messages.send_password_reset_link') ?? 'Send Password Reset Link' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection