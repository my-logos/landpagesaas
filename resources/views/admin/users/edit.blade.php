@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.edit_user') ?? 'Edit User' }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">{{ $t('messages.name') }} *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="form-group">
                <label for="email">{{ $t('messages.email') }} *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>

            <div class="form-group">
                <label for="phone">{{ $t('messages.phone') }}</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                    {{ $t('messages.is_active') ?? 'Is Active' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="email_verified_at" value="now" {{ $user->email_verified_at ? 'checked' : '' }}>
                    {{ $t('messages.email_verified') ?? 'Email Verified' }}
                </label>
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection