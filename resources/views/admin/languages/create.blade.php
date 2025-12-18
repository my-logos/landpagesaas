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
            <h1 class="page-title">{{ $t('messages.add_new') ?? 'إضافة جديد' }} - {{ $t('messages.language') ?? 'لغة' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> {{ $t('messages.back') ?? 'رجوع' }}
            </a>
        </div>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.languages.store') }}">
            @csrf

            <div class="form-group">
                <label for="code">{{ $t('messages.code') ?? 'Code' }} * (e.g., en, ar, fr)</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required maxlength="10">
            </div>

            <div class="form-group">
                <label for="name">{{ $t('messages.name') }} * (English name)</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="native_name">{{ $t('messages.native_name') ?? 'Native Name' }} *</label>
                <input type="text" id="native_name" name="native_name" class="form-control" value="{{ old('native_name') }}" required>
            </div>

            <div class="form-group">
                <label for="direction">{{ $t('messages.direction') ?? 'Direction' }} *</label>
                <select id="direction" name="direction" class="form-control" required>
                    <option value="ltr" {{ old('direction', 'ltr') === 'ltr' ? 'selected' : '' }}>LTR (Left to Right)</option>
                    <option value="rtl" {{ old('direction') === 'rtl' ? 'selected' : '' }}>RTL (Right to Left)</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    {{ $t('messages.is_active') ?? 'Is Active' }}
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
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