@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.landing_page_templates') ?? 'Landing Page Templates' }}</h1>
        <a href="{{ route('admin.landing-page-templates.create') }}" class="btn-save">{{ $t('messages.add_new') ?? 'Add New' }}</a>
    </div>

    <div class="ls-card">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <div class="templates-grid">
            @forelse($templates as $template)
            <div class="template-card">
                @if($template->preview_image)
                <div class="template-preview">
                    <img src="{{ $template->getPreviewImageUrl() }}" alt="{{ $template->name }}">
                </div>
                @else
                <div class="template-preview-placeholder">
                    <i class="fa-solid fa-image"></i>
                </div>
                @endif

                <div class="template-info">
                    <h3>{{ $template->name }}</h3>
                    @if($template->description)
                    <p>{{ Str::limit($template->description, 100) }}</p>
                    @endif

                    <div class="template-badges">
                        @if($template->is_enabled)
                        <span class="badge badge-success">{{ $t('messages.enabled') ?? 'Enabled' }}</span>
                        @else
                        <span class="badge badge-secondary">{{ $t('messages.disabled') ?? 'Disabled' }}</span>
                        @endif
                        @if($template->category)
                        <span class="badge badge-info">{{ $template->category }}</span>
                        @endif
                    </div>

                    <div class="template-actions">
                        <a href="{{ route('admin.landing-page-templates.edit', $template) }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-edit"></i> {{ $t('messages.edit') ?? 'Edit' }}
                        </a>
                        <form action="{{ route('admin.landing-page-templates.destroy', $template) }}" method="POST" data-confirm-message="{{ $t('messages.are_you_sure') ?? 'Are you sure?' }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash"></i> {{ $t('messages.delete') ?? 'Delete' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="templates-empty-state">
                <p>{{ $t('messages.no_templates_found') ?? 'No templates found' }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-templates.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush
@endsection