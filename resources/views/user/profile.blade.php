@extends('layouts.app')

@section('content')
<div class="profile-page" dir="{{ $dir }}">
  <!-- Tabs -->
  <div class="profile-tabs">
    <a href="#" class="tab-item active" data-tab="profile">
      <i class="fa-solid fa-user"></i>
      <span>{{ $t('messages.profile') }}</span>
    </a>
    <a href="#" class="tab-item" data-tab="password">
      <i class="fa-solid fa-lock"></i>
      <span>{{ $t('messages.change_password') }}</span>
    </a>
  </div>

  <!-- Profile Tab Content -->
  <div class="profile-card" data-tab-content="profile">
    <div class="profile-header">
      <h2 class="profile-title">
        <i class="fa-solid fa-user"></i>
        {{ $t('messages.profile') }}
      </h2>
      <a href="#" class="edit-link">
        <i class="fa-solid fa-pencil"></i>
        {{ $t('messages.edit') }}
      </a>
    </div>

    <form method="POST" action="{{ route('user.profile.update') }}" id="profileForm">
      @csrf
      @method('PUT')

      <div class="profile-fields">
        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-user"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.full_name') }}</label>
            <input type="text" name="name" class="field-input" value="{{ $user->name }}" readonly id="nameInput" />
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-phone"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.phone_number') }}</label>
            <input type="text" name="phone" class="field-input" value="{{ $user->phone ?? '+201030995992' }}" readonly id="phoneInput" />
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-dollar-sign"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.free_balance') }}</label>
            <div class="field-value">{{ number_format($user->wallet_balance ?? 0, 2) }}</div>
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-calendar"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.creation_date') }}</label>
            <div class="field-value">{{ $user->created_at->format('d M Y') }}</div>
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.email') }}</label>
            <div class="field-value">{{ $user->email }}</div>
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-gift"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.subscription_plan') }}</label>
            <div class="field-value">{{ strtolower($user->subscription && $user->subscription->package ? $user->subscription->package->name : 'free') }}</div>
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-dollar-sign"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.wallet_balance') }}</label>
            <div class="field-value">{{ number_format($user->wallet_balance ?? 0, 2) }} {{ $t('messages.currency') }}</div>
          </div>
        </div>
      </div>

      <div class="profile-status">
        <div class="status-item">
          <i class="fa-solid fa-check"></i>
          <span>{{ $t('messages.email_confirmed') }}</span>
        </div>
        <div class="status-item">
          <i class="fa-solid fa-check"></i>
          <span>{{ $t('messages.account_active') }}</span>
        </div>
      </div>

      <div class="form-actions" id="formActions">
        <button type="submit" class="btn">{{ $t('messages.save') }}</button>
        <button type="button" class="btn" id="cancelEditBtn">{{ $t('messages.cancel') }}</button>
      </div>
    </form>
  </div>

  <!-- Change Password Tab Content -->
  <div class="profile-card" data-tab-content="password">
    <div class="profile-header">
      <h2 class="profile-title">
        <i class="fa-solid fa-lock"></i>
        {{ $t('messages.change_password') }}
      </h2>
    </div>

    <form method="POST" action="{{ route('user.profile.update-password') }}" id="passwordForm">
      @csrf
      @method('PUT')

      <div class="profile-fields">
        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-lock"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.password') }} {{ $t('messages.current') ?? 'Current' }} *</label>
            <input type="password" name="current_password" class="field-input" required />
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-lock"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.password') }} {{ $t('messages.new') ?? 'New' }} *</label>
            <input type="password" name="password" class="field-input" required />
          </div>
        </div>

        <div class="profile-field">
          <div class="field-icon">
            <i class="fa-solid fa-lock"></i>
          </div>
          <div class="field-content">
            <label class="field-label">{{ $t('messages.password_confirm') }} *</label>
            <input type="password" name="password_confirmation" class="field-input" required />
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn">{{ $t('messages.save') }}</button>
        <button type="button" class="btn" id="reset-password-form-btn">{{ $t('messages.cancel') }}</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script src="{{ asset('js/user-tabs.js') }}"></script>
<script src="{{ asset('js/user-profile.js') }}"></script>
@endpush
@endsection