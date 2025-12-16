@extends('layouts.app')

@section('content')
<div class="subscription-plans-page" dir="{{ $dir }}">
  <h1 class="page-title">{{ $t('messages.subscription_plans') }}</h1>
  <h2 class="section-title">{{ $t('messages.your_current_plan') }}</h2>

  <!-- Current Usage and Plan Cards -->
  <div class="current-plan-grid">
    <!-- Current Usage Card -->
    <div class="usage-card">
      <h3 class="card-title">{{ $t('messages.current_usage') }}</h3>
      <div class="usage-metrics">
        <div class="usage-item">
          <span class="usage-label">{{ $t('messages.monthly_requests') }}:</span>
          <span class="usage-value">{{ $user->orders()->whereMonth('created_at', now()->month)->count() }}/{{ ($currentPackage && $currentPackage->monthly_orders_limit !== null) ? $currentPackage->monthly_orders_limit : ($currentPackage ? 10 : 0) }}</span>
        </div>
        <div class="usage-item">
          <span class="usage-label">{{ $t('messages.daily_edits') }}:</span>
          <span class="usage-value">{{ $user->pages()->whereDate('updated_at', today())->count() }}/{{ ($currentPackage && $currentPackage->daily_orders_limit !== null) ? $currentPackage->daily_orders_limit : ($currentPackage ? 2 : 0) }}</span>
        </div>
        <div class="usage-item">
          <span class="usage-label">{{ $t('messages.landing_pages') }}:</span>
          <span class="usage-value">{{ $user->pages()->count() }}/{{ ($currentPackage && $currentPackage->pages_limit !== null) ? $currentPackage->pages_limit : ($currentPackage ? 1 : 0) }}</span>
        </div>
        <div class="usage-item">
          <span class="usage-label">{{ $t('messages.added_products') }}:</span>
          <span class="usage-value">{{ $user->products()->count() }}/{{ ($currentPackage && $currentPackage->products_limit !== null) ? $currentPackage->products_limit : ($currentPackage ? 1 : 0) }}</span>
        </div>
      </div>
      <button class="btn-view-details">{{ $t('messages.view_full_details') }}</button>
    </div>

    <!-- Current Plan Card -->
    <div class="plan-info-card">
      <div class="plan-info-item">
        <i class="fa-solid fa-gift"></i>
        <div>
          <div class="info-label">{{ $t('messages.plan_name') }}</div>
          <div class="info-value">{{ ($currentPackage ? $currentPackage->name : $t('messages.free_plan')) }}</div>
        </div>
      </div>
      <div class="plan-info-item">
        <i class="fa-solid fa-info-circle"></i>
        <div>
          <div class="info-label">{{ $t('messages.plan_description') }}</div>
          <div class="info-value">
            @if($currentPackage && is_array($currentPackage->features) && isset($currentPackage->features['description']))
            {{ $currentPackage->features['description'] }}
            @elseif($currentPackage)
            {{ $t('messages.basic_plan_description') }}
            @else
            {{ $t('messages.no_active_subscription') }}
            @endif
          </div>
        </div>
      </div>
      <div class="plan-info-item">
        <i class="fa-solid fa-calendar"></i>
        <div>
          <div class="info-label">{{ $t('messages.subscription_period') }}:</div>
          <div class="info-value">
            {{ $currentSubscription && $currentSubscription->starts_at ? $currentSubscription->starts_at->format('d M Y') : '—' }} - {{ $currentSubscription && $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d M Y') : '—' }}
          </div>
        </div>
      </div>
      <div class="expiry-info">
        <div class="expiry-label">{{ $t('messages.expires_on') }}</div>
        <div class="expiry-date">{{ $currentSubscription && $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d M Y') : '—' }}</div>
      </div>
    </div>
  </div>

  @if($currentPackage && $currentPackage->is_free)
  <div class="warning-message">
    <i class="fa-solid fa-exclamation-triangle"></i>
    <span>{{ $t('messages.free_plan_warning') }}</span>
  </div>
  @endif

  <!-- Available Plans -->
  <h2 class="section-title">{{ $t('messages.packages_for_everyone') }}</h2>
  <div class="plans-grid">
    @foreach($packages as $package)
    <div class="plan-card {{ ($currentPackage && $currentPackage->id === $package->id) ? 'current-plan' : '' }}">
      @if($currentPackage && $currentPackage->id === $package->id)
      <div class="current-badge">{{ $t('messages.your_current_package') }}</div>
      @endif
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
        <li><i class="fa-solid fa-check"></i> {{ ($package->monthly_orders_limit !== null) ? $package->monthly_orders_limit : $t('messages.unlimited') }} {{ $t('messages.requests_per_day') }}</li>
        <li><i class="fa-solid fa-check"></i> {{ ($package->monthly_orders_limit !== null) ? $package->monthly_orders_limit : $t('messages.unlimited') }} {{ $t('messages.requests_per_month') }}</li>
        @if($package->hasFeature('google_analytics'))
        <li><i class="fa-solid fa-check"></i> {{ $locale === 'ar' ? 'Google Analytics' : 'Google Analytics' }}</li>
        @endif
        @if($package->hasFeature('facebook_pixel'))
        <li><i class="fa-solid fa-check"></i> {{ $locale === 'ar' ? 'Facebook Pixel' : 'Facebook Pixel' }}</li>
        @endif
        @if($package->hasFeature('facebook_conversion_api'))
        <li><i class="fa-solid fa-check"></i> <strong>{{ $locale === 'ar' ? 'Facebook Conversion API (PRO)' : 'Facebook Conversion API (PRO)' }}</strong></li>
        @endif
        @if($package->hasFeature('tiktok_pixel'))
        <li><i class="fa-solid fa-check"></i> {{ $locale === 'ar' ? 'TikTok Pixel' : 'TikTok Pixel' }}</li>
        @endif
        @if($package->hasFeature('snapchat_pixel'))
        <li><i class="fa-solid fa-check"></i> {{ $locale === 'ar' ? 'Snapchat Pixel' : 'Snapchat Pixel' }}</li>
        @endif
      </ul>
      @if($currentPackage && $currentPackage->id === $package->id)
      <button class="btn-subscribe current" disabled>{{ $t('messages.your_current_package') }}</button>
      @elseif($package->is_free)
      <form method="POST" action="{{ route('user.packages.subscribe', $package) }}">
        @csrf
        <button type="submit" class="btn-subscribe">{{ $t('messages.subscribe_now') }}</button>
      </form>
      @else
      <a href="{{ route('payments.package.show', $package) }}" class="btn-subscribe">{{ $t('messages.subscribe_now') }}</a>
      @endif
    </div>
    @endforeach

    <!-- Pay As You Go Card -->
    <div class="plan-card pay-as-you-go">
      <h3 class="plan-name">{{ $t('messages.pay_as_you_go') }}</h3>
      <div class="plan-price">
        0 {{ $t('messages.currency') }} {{ $t('messages.per_month') }}
      </div>
      <div class="unlimited-badge">{{ $t('messages.unlimited') }}</div>
      <ul class="plan-features">
        <li><i class="fa-solid fa-check"></i> {{ $t('messages.charged_by_usage_only') }}</li>
        <li><i class="fa-solid fa-check"></i> {{ $t('messages.price_per_request') }}</li>
        <li><i class="fa-solid fa-check"></i> {{ $t('messages.no_charge_duplicate') }}</li>
      </ul>
      <button class="btn-subscribe">{{ $t('messages.subscribe_now') }}</button>
    </div>
  </div>
</div>

<!-- Package Details Modal -->
<div class="modal-overlay" id="packageDetailsModal">
  <div class="modal-container package-details-modal">
    <div class="modal-header">
      <h2 class="modal-title">{{ $t('messages.package_details') ?? 'Package Details' }}</h2>
      <button type="button" class="modal-close">
        <i class="fa-solid fa-times"></i>
      </button>
    </div>
    <div class="modal-body">
      <!-- Package Information -->
      <div class="details-section">
        <h3 class="section-title">
          <i class="fa-solid fa-gift"></i>
          {{ $t('messages.package_features') ?? 'Package Features' }}
        </h3>
        <div class="details-grid">
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.plan_name') }}:</span>
            <span class="detail-value">{{ ($currentPackage ? $currentPackage->name : $t('messages.free_plan')) }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.plan_description') }}:</span>
            <span class="detail-value">
              @if($currentPackage && is_array($currentPackage->features) && isset($currentPackage->features['description']))
              {{ $currentPackage->features['description'] }}
              @elseif($currentPackage)
              {{ $t('messages.basic_plan_description') }}
              @else
              {{ $t('messages.no_active_subscription') }}
              @endif
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.subscription_period') }}:</span>
            <span class="detail-value">
              {{ $currentSubscription && $currentSubscription->starts_at ? $currentSubscription->starts_at->format('d M Y') : '—' }} - {{ $currentSubscription && $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d M Y') : '—' }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.expires_on') }}:</span>
            <span class="detail-value">{{ $currentSubscription && $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d M Y H:i') : '—' }}</span>
          </div>
        </div>
      </div>

      <!-- Usage Limits -->
      <div class="details-section">
        <h3 class="section-title">
          <i class="fa-solid fa-chart-bar"></i>
          {{ $t('messages.limits') ?? 'Usage Limits' }}
        </h3>
        <div class="usage-limits-grid">
          <div class="limit-item">
            <div class="limit-header">
              <span class="limit-label">{{ $t('messages.landing_pages') }}</span>
              <span class="limit-value">{{ $user->pages()->count() }}/{{ ($currentPackage && $currentPackage->pages_limit !== null) ? $currentPackage->pages_limit : ($currentPackage ? 1 : $t('messages.unlimited')) }}</span>
            </div>
            <div class="limit-progress">
              <div class="progress-bar" data-width="{{ ($currentPackage && $currentPackage->pages_limit !== null) ? min(100, ($user->pages()->count() / $currentPackage->pages_limit) * 100) : 0 }}"></div>
            </div>
          </div>
          <div class="limit-item">
            <div class="limit-header">
              <span class="limit-label">{{ $t('messages.added_products') }}</span>
              <span class="limit-value">{{ $user->products()->count() }}/{{ ($currentPackage && $currentPackage->products_limit !== null) ? $currentPackage->products_limit : ($currentPackage ? 1 : $t('messages.unlimited')) }}</span>
            </div>
            <div class="limit-progress">
              <div class="progress-bar" data-width="{{ ($currentPackage && $currentPackage->products_limit !== null) ? min(100, ($user->products()->count() / $currentPackage->products_limit) * 100) : 0 }}"></div>
            </div>
          </div>
          <div class="limit-item">
            <div class="limit-header">
              <span class="limit-label">{{ $t('messages.daily_edits') }}</span>
              <span class="limit-value">{{ $user->pages()->whereDate('updated_at', today())->count() }}/{{ ($currentPackage && $currentPackage->daily_orders_limit !== null) ? $currentPackage->daily_orders_limit : ($currentPackage ? 2 : $t('messages.unlimited')) }}</span>
            </div>
            <div class="limit-progress">
              <div class="progress-bar" data-width="{{ ($currentPackage && $currentPackage->daily_orders_limit !== null) ? min(100, ($user->pages()->whereDate('updated_at', today())->count() / $currentPackage->daily_orders_limit) * 100) : 0 }}"></div>
            </div>
          </div>
          <div class="limit-item">
            <div class="limit-header">
              <span class="limit-label">{{ $t('messages.monthly_requests') }}</span>
              <span class="limit-value">{{ $user->orders()->whereMonth('created_at', now()->month)->count() }}/{{ ($currentPackage && $currentPackage->monthly_orders_limit !== null) ? $currentPackage->monthly_orders_limit : ($currentPackage ? 10 : $t('messages.unlimited')) }}</span>
            </div>
            <div class="limit-progress">
              <div class="progress-bar" data-width="{{ ($currentPackage && $currentPackage->monthly_orders_limit !== null) ? min(100, ($user->orders()->whereMonth('created_at', now()->month)->count() / $currentPackage->monthly_orders_limit) * 100) : 0 }}"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Information -->
      <div class="details-section">
        <h3 class="section-title">
          <i class="fa-solid fa-user"></i>
          {{ $t('messages.user_information') ?? 'User Information' }}
        </h3>
        <div class="details-grid">
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.full_name') }}:</span>
            <span class="detail-value">{{ $user->name }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.email') }}:</span>
            <span class="detail-value">{{ $user->email }}</span>
          </div>
          @if($user->phone)
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.phone_number') }}:</span>
            <span class="detail-value">{{ $user->phone }}</span>
          </div>
          @endif
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.account_active') }}:</span>
            <span class="detail-value">
              @if($user->is_active)
              <span class="badge-success">{{ $t('messages.active') }}</span>
              @else
              <span class="badge-danger">{{ $t('messages.inactive') }}</span>
              @endif
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">{{ $t('messages.creation_date') }}:</span>
            <span class="detail-value">{{ $user->created_at->format('d M Y') }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-close">{{ $t('messages.close') ?? 'Close' }}</button>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-packages.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/user-packages.js') }}"></script>
@endpush

@endsection