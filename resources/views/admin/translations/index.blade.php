@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-header">
        <h1 class="page-title">{{ $t('messages.manage_translations') ?? 'Manage Translations' }}</h1>
    </div>

    <div class="ls-card">
        <!-- Language Tabs -->
        <div class="profile-tabs">
            @foreach($languages as $lang)
            <a href="#" class="tab-item {{ $loop->first ? 'active' : '' }}" data-tab="{{ $lang->code }}">
                <i class="fa-solid fa-globe"></i>
                <span>{{ $lang->native_name }}</span>
            </a>
            @endforeach
        </div>

        <!-- Add New Translation Form -->
        <div class="form-section form-section-spacing">
            <h3>{{ $t('messages.add_new_translation') ?? 'Add New Translation' }}</h3>
            <form method="POST" action="{{ route('admin.translations.store') }}">
                @csrf
                <div class="form-group">
                    <label for="new_key">{{ $t('messages.key') ?? 'Key' }} *</label>
                    <input type="text" id="new_key" name="key" class="form-control" required placeholder="messages.new_key">
                </div>
                <div class="translations-grid">
                    @foreach($languages as $lang)
                    <div class="form-group">
                        <label for="translation_{{ $lang->code }}">{{ $lang->native_name }} *</label>
                        <input type="text" id="translation_{{ $lang->code }}" name="translations[{{ $lang->code }}]" class="form-control" required>
                    </div>
                    @endforeach
                </div>
                <div class="form-actions visible">
                    <button type="submit" class="btn btn-primary">{{ $t('messages.add') ?? 'Add' }}</button>
                </div>
            </form>
        </div>

        <hr class="section-divider">

        <!-- Edit Translations by Language (Tabs) -->
        <h3>{{ $t('messages.edit_translations') ?? 'Edit Translations' }}</h3>
        <form method="POST" action="{{ route('admin.translations.update') }}">
            @csrf
            @method('PUT')

            @foreach($languages as $lang)
            <div class="translations-tab-content {{ $loop->first ? 'active' : '' }}" data-tab-content="{{ $lang->code }}">
                <div class="form-section">
                    <h4>{{ $lang->native_name }} - {{ $t('messages.translations') ?? 'Translations' }}</h4>
                    <div class="translations-scrollable">
                        @foreach($allKeys as $key)
                        <div class="form-group">
                            <label for="trans_{{ $lang->code }}_{{ $key }}">{{ $key }}</label>
                            <input type="text" id="trans_{{ $lang->code }}_{{ $key }}"
                                name="translations[{{ $lang->code }}][{{ $key }}]"
                                class="form-control"
                                value="{{ $translations[$lang->code][$key] ?? '' }}">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="form-actions visible form-actions-spacing">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-translations.js') }}"></script>
@endpush
@endsection