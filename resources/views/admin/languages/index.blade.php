@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.manage_languages') ?? 'Manage Languages' }}</h1>
        <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">{{ $t('messages.add_new') ?? 'Add New' }}</a>
    </div>

    <div class="chart-card">
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.language') ?? 'Language' }}</th>
                        <th>{{ $t('messages.code') ?? 'Code' }}</th>
                        <th>{{ $t('messages.direction') ?? 'Direction' }}</th>
                        <th>{{ $t('messages.is_active') ?? 'Active' }}</th>
                        <th>{{ $t('messages.is_default') ?? 'Default' }}</th>
                        <th>{{ $t('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($languages as $language)
                    <tr>
                        <td>{{ $language->native_name }} ({{ $language->name }})</td>
                        <td>{{ $language->code }}</td>
                        <td>{{ strtoupper($language->direction) }}</td>
                        <td>
                            @if($language->is_active)
                            <span class="badge badge-success">{{ $t('messages.yes') }}</span>
                            @else
                            <span class="badge badge-danger">{{ $t('messages.no') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($language->is_default)
                            <span class="badge badge-success">{{ $t('messages.yes') }}</span>
                            @else
                            <span class="badge badge-danger">{{ $t('messages.no') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.languages.edit', $language) }}" class="btn btn-sm btn-secondary">{{ $t('messages.edit') }}</a>
                            <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" data-confirm-message="{{ $t('messages.confirm_delete') }}">{{ $t('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ $t('messages.no_languages') ?? 'No languages found' }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush
@endsection