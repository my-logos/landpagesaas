@extends('layouts.app')

@section('content')
<div class="subscription-plans-page user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $t('messages.plans') ?? 'الخطط' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">{{ $t('messages.add_new') ?? 'إضافة جديد' }}</a>
        </div>
    </div>

    <!-- Available Plans -->
    <h2 class="section-title">{{ $t('messages.packages_for_everyone') ?? 'Available Packages' }}</h2>
    <div class="plans-grid">
        @foreach($packages as $package)
        <div class="plan-card">
            <h3 class="plan-name">{{ $package->name }}</h3>
            <div class="plan-price">
                @if($package->is_free)
                {{ $t('messages.free') ?? 'Free' }}
                @else
                {{ number_format($package->price_cents, 0) }} {{ $t('messages.currency') }} {{ $t('messages.per_month') }}
                @endif
            </div>
            <ul class="plan-features">
                <li><i class="fa-solid fa-check"></i> {{ ($package->pages_limit !== null) ? $package->pages_limit : $t('messages.unlimited') }} {{ $t('messages.pages') }}</li>
                <li><i class="fa-solid fa-check"></i> {{ ($package->products_limit !== null) ? $package->products_limit : $t('messages.unlimited') }} {{ $t('messages.products') }}</li>
                <li><i class="fa-solid fa-check"></i> {{ ($package->daily_orders_limit !== null) ? $package->daily_orders_limit : $t('messages.unlimited') }} {{ $t('messages.edits_per_day') }}</li>
                <li><i class="fa-solid fa-check"></i> {{ ($package->monthly_orders_limit !== null) ? $package->monthly_orders_limit : $t('messages.unlimited') }} {{ $t('messages.requests_per_month') }}</li>
            </ul>

            @if(!empty($package->features_array) && (($package->features_array['google_analytics'] ?? false) || ($package->features_array['facebook_pixel'] ?? false) || ($package->features_array['facebook_conversion_api'] ?? false) || ($package->features_array['tiktok_pixel'] ?? false) || ($package->features_array['snapchat_pixel'] ?? false)))
            <div class="plan-tracking-features">
                <h4 class="tracking-features-title">{{ $t('messages.tracking_features') ?? 'Tracking Features' }}</h4>
                <ul class="tracking-features-list">
                    @if($package->features_array['google_analytics'] ?? false)
                    <li><i class="fa-solid fa-check-circle text-success"></i> {{ $t('messages.google_analytics') ?? 'Google Analytics' }}</li>
                    @endif
                    @if($package->features_array['facebook_pixel'] ?? false)
                    <li><i class="fa-solid fa-check-circle text-success"></i> {{ $t('messages.facebook_pixel') ?? 'Facebook Pixel' }}</li>
                    @endif
                    @if($package->features_array['facebook_conversion_api'] ?? false)
                    <li><i class="fa-solid fa-check-circle text-success"></i> <strong>{{ $t('messages.facebook_conversion_api') ?? 'Facebook Conversion API (PRO)' }}</strong></li>
                    @endif
                    @if($package->features_array['tiktok_pixel'] ?? false)
                    <li><i class="fa-solid fa-check-circle text-success"></i> {{ $t('messages.tiktok_pixel') ?? 'TikTok Pixel' }}</li>
                    @endif
                    @if($package->features_array['snapchat_pixel'] ?? false)
                    <li><i class="fa-solid fa-check-circle text-success"></i> {{ $t('messages.snapchat_pixel') ?? 'Snapchat Pixel' }}</li>
                    @endif
                </ul>
            </div>
            @endif
            <div class="plan-actions">
                <a href="{{ route('admin.packages.edit', $package) }}" class="plan-action-btn plan-action-edit">
                    <i class="fa-solid fa-pencil"></i>
                    <span>{{ $t('messages.edit') ?? 'Edit' }}</span>
                </a>
                <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="plan-action-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="plan-action-btn plan-action-delete" data-confirm-message="{{ $t('messages.confirm_delete') ?? 'Delete?' }}">
                        <i class="fa-solid fa-trash"></i>
                        <span>{{ $t('messages.delete') ?? 'Delete' }}</span>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrapper">
        {{ $packages->links() }}
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush
@endsection