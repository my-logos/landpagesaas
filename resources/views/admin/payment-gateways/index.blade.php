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
            <h1 class="page-title">
                <i class="fa-solid fa-credit-card"></i>
                {{ $t('messages.payment_gateways') ?? 'بوابات الدفع' }}
            </h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.payment-gateways.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.add_new') ?? 'إضافة جديد' }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Payment Gateways Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ $t('messages.name') ?? 'Name' }}</th>
                    <th>{{ $t('messages.code') ?? 'Code' }}</th>
                    <th>{{ $t('messages.description') ?? 'Description' }}</th>
                    <th>{{ $t('messages.status') ?? 'Status' }}</th>
                    <th>{{ $t('messages.sort_order') ?? 'Sort Order' }}</th>
                    <th>{{ $t('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gateways as $gateway)
                <tr>
                    <td>
                        <strong>{{ $gateway->name }}</strong>
                    </td>
                    <td>
                        <code class="code-inline">{{ $gateway->code }}</code>
                    </td>
                    <td>
                        @if($gateway->description)
                        <span class="text-muted">{{ Str::limit($gateway->description, 50) }}</span>
                        @else
                        <span class="text-muted-dash">-</span>
                        @endif
                    </td>
                    <td>
                        @if($gateway->is_enabled)
                        <span class="badge badge-success">
                            <i class="fa-solid fa-check-circle"></i>
                            {{ $t('messages.enabled') ?? 'Enabled' }}
                        </span>
                        @else
                        <span class="badge badge-secondary">
                            <i class="fa-solid fa-times-circle"></i>
                            {{ $t('messages.disabled') ?? 'Disabled' }}
                        </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $gateway->sort_order ?? 0 }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-edit"></i>
                                <span>{{ $t('messages.edit') ?? 'Edit' }}</span>
                            </a>
                            <form action="{{ route('admin.payment-gateways.destroy', $gateway) }}" method="POST" data-confirm-message="{{ $t('messages.are_you_sure') ?? 'Are you sure?' }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>{{ $t('messages.delete') ?? 'Delete' }}</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center table-empty-state">
                        <i class="fa-solid fa-credit-card table-empty-state-icon"></i>
                        <p class="table-empty-state-text">{{ $t('messages.no_payment_gateways') ?? 'No payment gateways found' }}</p>
                        <a href="{{ route('admin.payment-gateways.create') }}" class="btn btn-primary table-empty-state-action">
                            <i class="fa-solid fa-plus"></i>
                            {{ $t('messages.add_new') ?? 'Add New Gateway' }}
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush
@endsection