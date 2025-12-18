@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-header">
        <h1 class="page-title">{{ $package->exists ? ($t('messages.edit') ?? 'Edit') : ($t('messages.add_new') ?? 'Add New') }} - {{ $t('messages.plans') ?? 'Package' }}</h1>
    </div>

    <div class="ls-card">

        <form method="POST" action="{{ $package->exists ? route('admin.packages.update', $package) : route('admin.packages.store') }}">
            @csrf
            @if($package->exists)
            @method('PUT')
            @endif

            <div class="form-group">
                <label for="name" class="form-label">{{ $t('messages.name') ?? 'Name' }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $package->name) }}" class="form-control" required />
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">Slug *</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $package->slug) }}" class="form-control" required />
            </div>

            <div class="form-group">
                <label for="price" class="form-label">{{ $t('messages.price') ?? 'Price' }} ({{ $t('messages.currency') ?? 'EGP' }})</label>
                <input type="number" id="price" name="price" value="{{ old('price', $package->price_cents ?? '') }}" class="form-control" step="1" min="0" />
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_free" value="1" {{ old('is_free', $package->is_free) ? 'checked' : '' }} />
                    {{ $t('messages.free') ?? 'Is Free' }}
                </label>
            </div>

            <h3>{{ $t('messages.limits') ?? 'Limits' }} ({{ $t('messages.leave_blank_unlimited') ?? 'Leave blank for unlimited' }})</h3>
            <div class="form-group">
                <label for="pages_limit" class="form-label">{{ $t('messages.pages') ?? 'Pages' }} {{ $t('messages.limit') ?? 'Limit' }}</label>
                <input type="number" id="pages_limit" name="pages_limit" value="{{ old('pages_limit', $package->pages_limit) }}" class="form-control" />
            </div>
            <div class="form-group">
                <label for="products_limit" class="form-label">{{ $t('messages.products') ?? 'Products' }} {{ $t('messages.limit') ?? 'Limit' }}</label>
                <input type="number" id="products_limit" name="products_limit" value="{{ old('products_limit', $package->products_limit) }}" class="form-control" />
            </div>
            <div class="form-group">
                <label for="daily_orders_limit" class="form-label">{{ $t('messages.daily_orders') ?? 'Daily Orders' }} {{ $t('messages.limit') ?? 'Limit' }}</label>
                <input type="number" id="daily_orders_limit" name="daily_orders_limit" value="{{ old('daily_orders_limit', $package->daily_orders_limit) }}" class="form-control" />
            </div>
            <div class="form-group">
                <label for="monthly_orders_limit" class="form-label">{{ $t('messages.monthly_orders') ?? 'Monthly Orders' }} {{ $t('messages.limit') ?? 'Limit' }}</label>
                <input type="number" id="monthly_orders_limit" name="monthly_orders_limit" value="{{ old('monthly_orders_limit', $package->monthly_orders_limit) }}" class="form-control" />
            </div>
            <div class="form-group">
                <label for="monthly_support_tickets_limit" class="form-label">{{ $t('messages.monthly_support_tickets') ?? 'Monthly Support Tickets' }} {{ $t('messages.limit') ?? 'Limit' }}</label>
                <input type="number" id="monthly_support_tickets_limit" name="monthly_support_tickets_limit" value="{{ old('monthly_support_tickets_limit', $package->monthly_support_tickets_limit) }}" class="form-control" />
                <small>{{ $t('messages.monthly_support_tickets_hint') ?? 'Leave blank or 0 for unlimited' }}</small>
            </div>

            <h3>{{ $t('messages.package_features') ?? 'Package Features' }}</h3>
            <div class="features-section">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="features[google_analytics]" value="1"
                            {{ old('features.google_analytics', $package->features['google_analytics'] ?? false) ? 'checked' : '' }} />
                        <i class="fa-solid fa-chart-line"></i>
                        {{ $t('messages.google_analytics') ?? 'Google Analytics' }}
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="features[facebook_pixel]" value="1"
                            {{ old('features.facebook_pixel', $package->features['facebook_pixel'] ?? false) ? 'checked' : '' }} />
                        <i class="fa-solid fa-facebook"></i>
                        {{ $t('messages.facebook_pixel') ?? 'Facebook Pixel' }}
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="features[facebook_conversion_api]" value="1"
                            {{ old('features.facebook_conversion_api', $package->features['facebook_conversion_api'] ?? false) ? 'checked' : '' }} />
                        <i class="fa-brands fa-facebook"></i>
                        <strong>{{ $t('messages.facebook_conversion_api') ?? 'Facebook Conversion API (PRO)' }}</strong>
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="features[tiktok_pixel]" value="1"
                            {{ old('features.tiktok_pixel', $package->features['tiktok_pixel'] ?? false) ? 'checked' : '' }} />
                        <i class="fa-solid fa-music"></i>
                        {{ $t('messages.tiktok_pixel') ?? 'TikTok Pixel' }}
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="features[snapchat_pixel]" value="1"
                            {{ old('features.snapchat_pixel', $package->features['snapchat_pixel'] ?? false) ? 'checked' : '' }} />
                        <i class="fa-solid fa-snapchat"></i>
                        {{ $t('messages.snapchat_pixel') ?? 'Snapchat Pixel' }}
                    </label>
                </div>
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') ?? 'Save' }}</button>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
            </div>
        </form>
    </div>
</div>
@endsection