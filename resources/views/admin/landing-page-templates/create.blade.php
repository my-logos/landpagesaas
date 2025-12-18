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
            <h1 class="page-title">{{ $t('messages.create_new_landing_page') ?? 'إنشاء صفحة هبوط جديدة' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.landing-page-templates.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> {{ $t('messages.back') ?? 'رجوع' }}
            </a>
        </div>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.landing-page-templates.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">{{ $t('messages.name') ?? 'Name' }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required />
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">{{ $t('messages.slug') ?? 'Slug' }}</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="form-control" />
                <small class="form-text text-muted">{{ $t('messages.slug_auto_generated') ?? 'Leave empty to auto-generate from name' }}</small>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">{{ $t('messages.description') ?? 'Description' }}</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="category" class="form-label">{{ $t('messages.category') ?? 'Category' }}</label>
                <input type="text" id="category" name="category" value="{{ old('category') }}" class="form-control" placeholder="e.g., modern, classic, minimal" />
            </div>

            <div class="form-group">
                <label for="preview_image" class="form-label">{{ $t('messages.preview_image') ?? 'Preview Image' }}</label>
                <input type="file" id="preview_image" name="preview_image" class="form-control" accept="image/*" />
                <small class="form-text text-muted">{{ $t('messages.max_size_2mb') ?? 'Max size: 2MB' }}</small>
            </div>

            <div class="form-group">
                <label for="template_file" class="form-label">{{ $t('messages.template_file') ?? 'Template File' }} (Blade)</label>
                <input type="file" id="template_file" name="template_file" class="form-control" accept=".blade.php,.php" />
                <small class="form-text text-muted">{{ $t('messages.upload_blade_template') ?? 'Upload a Blade template file (.blade.php)' }}</small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled') ? 'checked' : '' }} />
                    {{ $t('messages.enabled') ?? 'Enabled' }}
                </label>
            </div>

            <div class="form-group">
                <label for="sort_order" class="form-label">{{ $t('messages.sort_order') ?? 'Sort Order' }}</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" />
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') ?? 'Save' }}</button>
                <a href="{{ route('admin.landing-page-templates.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
            </div>
        </form>
    </div>
</div>
@endsection