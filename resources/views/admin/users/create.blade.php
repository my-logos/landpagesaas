@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.add_new') ?? 'Add New' }} - {{ $t('messages.users') ?? 'User' }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">{{ $t('messages.name') }} *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">{{ $t('messages.email') }} *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">{{ $t('messages.password') }} *</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ $t('messages.password_confirm') }} *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phone">{{ $t('messages.phone') }}</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    {{ $t('messages.is_active') ?? 'Is Active' }}
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