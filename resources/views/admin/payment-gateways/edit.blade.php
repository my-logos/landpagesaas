@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.edit') ?? 'Edit' }} - {{ $t('messages.payment_gateway') ?? 'Payment Gateway' }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.payment-gateways.update', $gateway) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">{{ $t('messages.gateway_code') ?? 'Gateway Code' }}</label>
                <input type="text" value="{{ $gateway->code }}" class="form-control" disabled />
                <small class="form-text text-muted">{{ $t('messages.code_cannot_be_changed') ?? 'Code cannot be changed after creation' }}</small>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">{{ $t('messages.name') ?? 'Name' }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $gateway->name) }}" class="form-control" required />
            </div>

            <div class="form-group">
                <label for="description" class="form-label">{{ $t('messages.description') ?? 'Description' }}</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $gateway->description) }}</textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $gateway->is_enabled) ? 'checked' : '' }} />
                    {{ $t('messages.enabled') ?? 'Enabled' }}
                </label>
            </div>

            <div class="form-group">
                <label for="sort_order" class="form-label">{{ $t('messages.sort_order') ?? 'Sort Order' }}</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $gateway->sort_order) }}" class="form-control" />
            </div>

            <h3>{{ $t('messages.credentials') ?? 'Credentials' }}</h3>
            @if(count($fields) > 0)
            @foreach($fields as $field)
            <div class="form-group">
                <label for="credentials_{{ $field }}" class="form-label">{{ $field }}</label>
                <input type="text" id="credentials_{{ $field }}" name="credentials[{{ $field }}]" class="form-control" value="{{ old('credentials.' . $field, $gateway->credentials[$field] ?? '') }}" />
            </div>
            @endforeach
            @else
            <p>{{ $t('messages.no_credentials_required') ?? 'No credentials required for this gateway' }}</p>
            @endif

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') ?? 'Save' }}</button>
                <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
            </div>
        </form>
    </div>
</div>
@endsection