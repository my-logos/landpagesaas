@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.edit') }} - {{ $t('messages.language') ?? 'Language' }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.languages.update', $language) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">{{ $t('messages.code') ?? 'Code' }} *</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $language->code) }}" required maxlength="10">
            </div>

            <div class="form-group">
                <label for="name">{{ $t('messages.name') }} *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $language->name) }}" required>
            </div>

            <div class="form-group">
                <label for="native_name">{{ $t('messages.native_name') ?? 'Native Name' }} *</label>
                <input type="text" id="native_name" name="native_name" class="form-control" value="{{ old('native_name', $language->native_name) }}" required>
            </div>

            <div class="form-group">
                <label for="direction">{{ $t('messages.direction') ?? 'Direction' }} *</label>
                <select id="direction" name="direction" class="form-control" required>
                    <option value="ltr" {{ old('direction', $language->direction) === 'ltr' ? 'selected' : '' }}>LTR</option>
                    <option value="rtl" {{ old('direction', $language->direction) === 'rtl' ? 'selected' : '' }}>RTL</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $language->is_active) ? 'checked' : '' }}>
                    {{ $t('messages.is_active') ?? 'Is Active' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $language->is_default) ? 'checked' : '' }}>
                    {{ $t('messages.is_default') ?? 'Is Default' }}
                </label>
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
                <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection