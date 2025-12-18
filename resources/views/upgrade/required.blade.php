@extends('layouts.app')

@section('content')
<div class="subscription-plans-page" dir="{{ $dir }}">
  <div class="upgrade-required-header">
    <div class="upgrade-icon">
      <i class="fa-solid fa-lock"></i>
    </div>
    <h1 class="page-title">{{ $locale === 'ar' ? 'ترقية مطلوبة' : 'Upgrade Required' }}</h1>
    <p class="upgrade-message">
      {{ $locale === 'ar' 
        ? 'هذه الميزة متاحة فقط في الباقات الأعلى. يرجى الترقية للوصول إلى ' . $featureName 
        : 'This feature is only available in higher plans. Please upgrade to access ' . $featureName }}
    </p>
  </div>

  @if($packagesWithFeature->count() > 0)
  <h2 class="section-title">{{ $locale === 'ar' ? 'الباقات المتاحة' : 'Available Plans' }}</h2>
  <div class="plans-grid">
    @foreach($packagesWithFeature as $package)
    <div class="plan-card {{ ($currentPackage && $currentPackage->id === $package->id) ? 'current-plan' : '' }}">
      @if($currentPackage && $currentPackage->id === $package->id)
      <div class="current-badge">{{ $locale === 'ar' ? 'باقتك الحالية' : 'Your Current Plan' }}</div>
      @endif
      <h3 class="plan-name">{{ $package->name }}</h3>
      <div class="plan-price">
        @if($package->is_free)
        {{ $locale === 'ar' ? 'مجاني' : 'Free' }}
        @else
        {{ number_format($package->price_cents, 0) }} {{ $t('messages.currency') ?? 'EGP' }} {{ $t('messages.per_month') ?? '/month' }}
        @endif
      </div>
      <ul class="plan-features">
        <li><i class="fa-solid fa-check"></i> {{ ($package->pages_limit !== null) ? $package->pages_limit : ($locale === 'ar' ? 'غير محدود' : 'Unlimited') }} {{ $t('messages.pages') ?? 'Pages' }}</li>
        <li><i class="fa-solid fa-check"></i> {{ ($package->products_limit !== null) ? $package->products_limit : ($locale === 'ar' ? 'غير محدود' : 'Unlimited') }} {{ $t('messages.products') ?? 'Products' }}</li>
        <li><i class="fa-solid fa-check"></i> {{ ($package->daily_orders_limit !== null) ? $package->daily_orders_limit : ($locale === 'ar' ? 'غير محدود' : 'Unlimited') }} {{ $t('messages.edits_per_day') ?? 'Edits/Day' }}</li>
        <li><i class="fa-solid fa-check"></i> {{ ($package->monthly_orders_limit !== null) ? $package->monthly_orders_limit : ($locale === 'ar' ? 'غير محدود' : 'Unlimited') }} {{ $t('messages.requests_per_month') ?? 'Requests/Month' }}</li>
        @if($package->hasFeature('facebook_conversion_api'))
        <li><i class="fa-solid fa-check"></i> {{ $locale === 'ar' ? 'Facebook Conversion API' : 'Facebook Conversion API' }}</li>
        @endif
      </ul>
      @if($currentPackage && $currentPackage->id === $package->id)
      <button class="btn-subscribe current" disabled>{{ $locale === 'ar' ? 'باقتك الحالية' : 'Your Current Plan' }}</button>
      @elseif($package->is_free)
      <form method="POST" action="{{ route('user.packages.subscribe', $package) }}">
        @csrf
        <button type="submit" class="btn-subscribe">{{ $locale === 'ar' ? 'اشترك الآن' : 'Subscribe Now' }}</button>
      </form>
      @else
      <a href="{{ route('payments.package.show', $package) }}" class="btn-subscribe">{{ $locale === 'ar' ? 'اشترك الآن' : 'Subscribe Now' }}</a>
      @endif
    </div>
    @endforeach
  </div>
  @else
  <div class="alert alert-info">
    <i class="fa-solid fa-info-circle"></i>
    <span>{{ $locale === 'ar' ? 'لا توجد باقات متاحة حالياً' : 'No plans available at the moment' }}</span>
  </div>
  @endif

  <div class="upgrade-footer">
    <a href="{{ route('user.packages.index') }}" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left"></i>
      {{ $locale === 'ar' ? 'عودة إلى الباقات' : 'Back to Plans' }}
    </a>
  </div>
</div>

@push('styles')
<!-- Styles included in main.css -->
@endpush
@endsection