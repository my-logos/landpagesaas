@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fa-solid fa-bolt"></i>
                {{ $t('messages.webhooks_management') ?? 'Webhooks Management' }}
            </h1>
        </div>
        <div class="page-header-right">
            <button class="btn-send-message" data-open-modal="add-webhook">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.add_new_webhook') ?? 'Add New Webhook' }}
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="webhooks-tabs">
        <a href="{{ route('user.webhooks.index', ['tab' => 'endpoints']) }}"
            class="webhook-tab {{ $tab === 'endpoints' ? 'active' : '' }}"
            data-tab="endpoints">
            <i class="fa-solid fa-link"></i>
            {{ $t('messages.endpoints') ?? 'Endpoints' }}
        </a>
        <a href="{{ route('user.webhooks.index', ['tab' => 'statistics']) }}"
            class="webhook-tab {{ $tab === 'statistics' ? 'active' : '' }}"
            data-tab="statistics">
            <i class="fa-solid fa-chart-bar"></i>
            {{ $t('messages.statistics') ?? 'Statistics' }}
        </a>
        <a href="{{ route('user.webhooks.index', ['tab' => 'settings']) }}"
            class="webhook-tab {{ $tab === 'settings' ? 'active' : '' }}"
            data-tab="settings">
            <i class="fa-solid fa-gear"></i>
            {{ $t('messages.settings') ?? 'Settings' }}
        </a>
    </div>

    <!-- Tab Content -->
    <div class="webhooks-content">
        @if($tab === 'endpoints')
        @include('user.webhooks.partials.endpoints')
        @elseif($tab === 'statistics')
        @include('user.webhooks.partials.statistics')
        @elseif($tab === 'settings')
        @include('user.webhooks.partials.settings')
        @endif
    </div>

    <!-- Information Section (Collapsible) -->
    <div class="webhooks-info-section">
        <div class="webhooks-info-header collapsed" data-toggle-info="true">
            <i class="fa-solid fa-chevron-down info-toggle"></i>
            <span>{{ $t('messages.what_are_webhooks_benefit') ?? 'What are Webhooks and how can you benefit from them?' }}</span>
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div class="webhooks-info-content hidden">
            @include('user.webhooks.partials.info')
        </div>
    </div>
</div>

<!-- Add/Edit Webhook Modal -->
@include('user.webhooks.partials.modal')
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/user-webhooks.js') }}"></script>
@endpush