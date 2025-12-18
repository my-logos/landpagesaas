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
            <h1 class="page-title">{{ $t('messages.pages') ?? 'الصفحات' }}</h1>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="filter-header" id="filterToggle">
            <div class="filter-header-title">
                <i class="fa-solid fa-filter"></i>
                <span>{{ $t('messages.filter') ?? 'Filter' }}</span>
            </div>
            <i class="fa-solid fa-chevron-down" id="filterChevron"></i>
        </div>

        <div class="filter-content collapsed" id="filterContent">
            <form method="GET" action="{{ route('admin.pages.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="search">{{ $t('messages.search') ?? 'Search' }}</label>
                        <input type="text" id="search" name="search" class="form-control"
                            value="{{ request('search') }}"
                            placeholder="{{ $t('messages.search_by_page_title') ?? 'Search by page title' }}">
                    </div>
                    <div class="filter-group">
                        <label for="user_id">{{ $t('messages.user') ?? 'User' }}</label>
                        <select id="user_id" name="user_id" class="form-control">
                            <option value="">{{ $t('messages.all_users') ?? 'All Users' }}</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="product_id">{{ $t('messages.product') ?? 'Product' }}</label>
                        <select id="product_id" name="product_id" class="form-control">
                            <option value="">{{ $t('messages.all_products') ?? 'All Products' }}</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->user->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">{{ $t('messages.filter') ?? 'Filter' }}</button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">{{ $t('messages.reset') ?? 'Reset' }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ $t('messages.page_title') ?? 'Page Title' }}</th>
                    <th>{{ $t('messages.user') ?? 'User' }}</th>
                    <th>{{ $t('messages.product') ?? 'Product' }}</th>
                    <th>{{ $t('messages.ai_version') ?? 'AI Version' }}</th>
                    <th>{{ $t('messages.created_at') ?? 'Created At' }}</th>
                    <th>{{ $t('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>
                        <strong>{{ $page->title }}</strong>
                    </td>
                    <td>
                        <div class="user-info">
                            <span class="user-name">{{ $page->user->name ?? '-' }}</span>
                            <small class="user-email">{{ $page->user->email ?? '-' }}</small>
                        </div>
                    </td>
                    <td>
                        @if($page->product)
                        <span class="product-name">{{ $page->product->name }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">{{ strtoupper($page->ai_version ?? 'v2') }}</span>
                    </td>
                    <td>
                        {{ $page->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-eye"></i> {{ $t('messages.view_details') ?? 'View Details' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        {{ $t('messages.no_pages_found') ?? 'No pages found' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $pages->links() }}
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush
@endsection