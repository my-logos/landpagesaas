@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.products') ?? 'Products' }}</h1>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.products.index') }}" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">{{ $t('messages.search') ?? 'Search' }}</label>
                    <input type="text" id="search" name="search" class="form-control"
                        value="{{ request('search') }}"
                        placeholder="{{ $t('messages.search_by_product_name') ?? 'Search by product name' }}">
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
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">{{ $t('messages.filter') ?? 'Filter' }}</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">{{ $t('messages.reset') ?? 'Reset' }}</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ $t('messages.product_name') ?? 'Product Name' }}</th>
                    <th>{{ $t('messages.user') ?? 'User' }}</th>
                    <th>{{ $t('messages.price') ?? 'Price' }}</th>
                    <th>{{ $t('messages.orders_count') ?? 'Orders Count' }}</th>
                    <th>{{ $t('messages.created_at') ?? 'Created At' }}</th>
                    <th>{{ $t('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <strong>{{ $product->name }}</strong>
                    </td>
                    <td>
                        <div class="user-info">
                            <span class="user-name">{{ $product->user->name ?? '-' }}</span>
                            <small class="user-email">{{ $product->user->email ?? '-' }}</small>
                        </div>
                    </td>
                    <td>
                        {{ number_format($product->price_cents / 100, 2) }} {{ $t('messages.currency') ?? 'EGP' }}
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $product->orders_count ?? 0 }}</span>
                    </td>
                    <td>
                        {{ $product->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-eye"></i> {{ $t('messages.view_details') ?? 'View Details' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        {{ $t('messages.no_products_found') ?? 'No products found' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $products->links() }}
    </div>
</div>
@endsection