@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.users') ?? 'Users' }}</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ $t('messages.add_new') ?? 'Add New' }}</a>
    </div>

    <div class="chart-card">
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.name') }}</th>
                        <th>{{ $t('messages.email') }}</th>
                        <th>{{ $t('messages.phone') }}</th>
                        <th>{{ $t('messages.status') }}</th>
                        <th>{{ $t('messages.email_verified') ?? 'Email Verified' }}</th>
                        <th>{{ $t('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            @if($user->is_active)
                            <span class="badge badge-success">{{ $t('messages.active') ?? 'Active' }}</span>
                            @else
                            <span class="badge badge-danger">{{ $t('messages.inactive') ?? 'Inactive' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($user->email_verified_at)
                            <span class="badge badge-success">{{ $t('messages.yes') }}</span>
                            @else
                            <span class="badge badge-danger">{{ $t('messages.no') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-secondary">{{ $t('messages.edit') }}</a>
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}">
                                    {{ $user->is_active ? ($t('messages.deactivate') ?? 'Deactivate') : ($t('messages.activate') ?? 'Activate') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.toggle-email-verified', $user) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info">
                                    {{ $user->email_verified_at ? ($t('messages.unverify_email') ?? 'Unverify') : ($t('messages.verify_email') ?? 'Verify') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" data-confirm-message="{{ $t('messages.confirm_delete') }}">{{ $t('messages.delete') }}</button>
                            </form>
                            <a href="{{ route('admin.subscriptions.show', $user) }}" class="btn btn-sm btn-primary">{{ $t('messages.view_subscriptions') ?? 'View Subscriptions' }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ $t('messages.no_users') ?? 'No users found' }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush
@endsection