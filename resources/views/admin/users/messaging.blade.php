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
            <h1 class="page-title">{{ $t('messages.message_users') ?? 'مراسلة المستخدمين' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ $t('messages.back') ?? 'رجوع' }}</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin: 20px 0; padding: 15px; background-color: #d4edda; color: #155724; border-radius: 5px;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" style="margin: 20px 0; padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">
        {{ session('error') }}
    </div>
    @endif

    @if(session('errors'))
    <div class="alert alert-warning" style="margin: 20px 0; padding: 15px; background-color: #fff3cd; color: #856404; border-radius: 5px;">
        <strong>Errors Details:</strong>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            @foreach(session('errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        @if(session('errors_more'))
        <p style="margin: 10px 0 0 0; font-style: italic;">... and {{ session('errors_more') }} more errors. Check logs for details.</p>
        @endif
    </div>
    @endif

    <div class="chart-card">
        <form method="POST" action="{{ route('admin.users.messaging.send') }}">
            @csrf

            <!-- Filter Section -->
            <div class="filter-section" style="margin-bottom: 24px;">
                <div class="filter-header" id="filterToggle">
                    <div class="filter-header-title">
                        <i class="fa-solid fa-filter"></i>
                        <span>{{ $t('messages.filter_users') ?? 'تصفية المستخدمين' }}</span>
                    </div>
                    <i class="fa-solid fa-chevron-down" id="filterChevron"></i>
                </div>

                <div class="filter-content collapsed" id="filterContent">
                    <div style="margin-bottom: 20px;">
                        <label for="filter_type" style="display: block; margin-bottom: 8px; font-weight: bold;">
                            {{ $t('messages.filter_type') ?? 'نوع التصفية' }}
                        </label>
                        <select name="filter_type" id="filter_type" required class="form-control">
                            <option value="all">{{ $t('messages.all_users') ?? 'All Users' }}</option>
                            <option value="active">{{ $t('messages.active_users') ?? 'Active Users Only' }}</option>
                            <option value="inactive">{{ $t('messages.inactive_users') ?? 'Inactive Users Only' }}</option>
                            <option value="verified">{{ $t('messages.verified_users') ?? 'Verified Users Only' }}</option>
                            <option value="unverified">{{ $t('messages.unverified_users') ?? 'Unverified Users Only' }}</option>
                            <option value="package">{{ $t('messages.by_package') ?? 'By Subscription Package' }}</option>
                        </select>
                    </div>

                    <div id="package-selector" style="margin-bottom: 20px; display: none;">
                        <label for="package_id" style="display: block; margin-bottom: 8px; font-weight: bold;">
                            {{ $t('messages.select_package') ?? 'Select Package' }}
                        </label>
                        <select name="package_id" id="package_id" class="form-control">
                            <option value="">{{ $t('messages.select_package') ?? 'Select Package' }}</option>
                            @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="subject" style="display: block; margin-bottom: 8px; font-weight: bold;">
                    {{ $t('messages.subject') ?? 'Subject' }}
                </label>
                <input type="text" name="subject" id="subject" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                    placeholder="{{ $t('messages.enter_subject') ?? 'Enter email subject' }}">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="message" style="display: block; margin-bottom: 8px; font-weight: bold;">
                    {{ $t('messages.message') ?? 'Message' }}
                </label>
                <textarea name="message" id="message" required rows="10"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;"
                    placeholder="{{ $t('messages.enter_message') ?? 'Enter your message here' }}"></textarea>
                <small style="color: #666; display: block; margin-top: 5px;">
                    {{ $t('messages.message_hint') ?? 'You can use {name} to include the user\'s name in the message' }}
                </small>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                    {{ $t('messages.send_message') ?? 'Send Message' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="padding: 12px 30px; margin-left: 10px;">
                    {{ $t('messages.cancel') ?? 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>

@endpush
@endsection