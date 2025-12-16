@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.page_details') ?? 'Page Details' }}</h1>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {{ $t('messages.back') ?? 'Back' }}
        </a>
    </div>

    <div class="details-container">
        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.page_information') ?? 'Page Information' }}</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>{{ $t('messages.page_title') ?? 'Page Title' }}</label>
                    <span>{{ $page->title }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.page_url') ?? 'Page URL' }}</label>
                    <span>
                        <a href="{{ $pageUrl }}" target="_blank" class="page-link">
                            {{ $pageUrl }} <i class="fa-solid fa-external-link"></i>
                        </a>
                    </span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.ai_version') ?? 'AI Version' }}</label>
                    <span class="badge badge-info">{{ strtoupper($page->ai_version ?? 'v2') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.form_type') ?? 'Form Type' }}</label>
                    <span class="badge badge-secondary">{{ ucfirst($page->form_type ?? 'default') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.created_at') ?? 'Created At' }}</label>
                    <span>{{ $page->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>

        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.user_information') ?? 'User Information' }}</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>{{ $t('messages.user_name') ?? 'User Name' }}</label>
                    <span>{{ $page->user->name ?? '-' }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.email') ?? 'Email' }}</label>
                    <span>{{ $page->user->email ?? '-' }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.phone') ?? 'Phone' }}</label>
                    <span>{{ $page->user->phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        @if($page->product)
        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.product_information') ?? 'Product Information' }}</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>{{ $t('messages.product_name') ?? 'Product Name' }}</label>
                    <span>{{ $page->product->name }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.price') ?? 'Price' }}</label>
                    <span>{{ number_format($page->product->price_cents / 100, 2) }} {{ $t('messages.currency') ?? 'EGP' }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($page->additional_description)
        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.additional_description') ?? 'Additional Description' }}</h2>
            <div class="description-content">
                {{ $page->additional_description }}
            </div>
        </div>
        @endif

        @if($page->facebook_pixel || $page->tiktok_pixel || $page->snapchat_pixel || $page->google_analytics_id)
        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.tracking_codes') ?? 'Tracking Codes' }}</h2>
            <div class="details-grid">
                @if($page->facebook_pixel)
                <div class="detail-item">
                    <label>{{ $t('messages.facebook_pixel') ?? 'Facebook Pixel' }}</label>
                    <span class="code-value">{{ $page->facebook_pixel }}</span>
                </div>
                @endif
                @if($page->tiktok_pixel)
                <div class="detail-item">
                    <label>{{ $t('messages.tiktok_pixel') ?? 'TikTok Pixel' }}</label>
                    <span class="code-value">{{ $page->tiktok_pixel }}</span>
                </div>
                @endif
                @if($page->snapchat_pixel)
                <div class="detail-item">
                    <label>{{ $t('messages.snapchat_pixel') ?? 'Snapchat Pixel' }}</label>
                    <span class="code-value">{{ $page->snapchat_pixel }}</span>
                </div>
                @endif
                @if($page->google_analytics_id)
                <div class="detail-item">
                    <label>{{ $t('messages.google_analytics') ?? 'Google Analytics' }}</label>
                    <span class="code-value">{{ $page->google_analytics_id }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection