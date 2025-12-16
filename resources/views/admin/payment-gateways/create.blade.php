@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.add_new') ?? 'Add New' }} - {{ $t('messages.payment_gateway') ?? 'Payment Gateway' }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.payment-gateways.store') }}" id="gatewayForm" data-get-fields-route="{{ route('admin.payment-gateways.get-fields') }}">
            @csrf

            <div class="form-group">
                <label for="code" class="form-label">{{ $t('messages.gateway_code') ?? 'Gateway Code' }} *</label>
                <select id="code" name="code" class="form-control" required>
                    <option value="">{{ $t('messages.select_gateway') ?? 'Select Gateway' }}</option>
                    @foreach($availableGateways as $code => $name)
                    <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">{{ $t('messages.name') ?? 'Name' }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required />
            </div>

            <div class="form-group">
                <label for="description" class="form-label">{{ $t('messages.description') ?? 'Description' }}</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
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

            <h3>{{ $t('messages.credentials') ?? 'Credentials' }}</h3>
            <div id="credentialsFields"></div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') ?? 'Save' }}</button>
                <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-payment-gateways.js') }}"></script>
@endpush
@endsection