@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fa-solid fa-envelope"></i>
                {{ $t('messages.messages') ?? 'Messages' }}
            </h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('user.messages.create') }}" class="btn-send-message">
                <i class="fa-solid fa-paper-plane"></i>
                {{ $t('messages.send_new_message') ?? 'Send New Message' }}
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="messages-tabs">
        <a href="{{ route('user.messages.index', ['status' => '']) }}"
            class="message-tab {{ !request('status') ? 'active' : '' }}">
            {{ $t('messages.all_messages') ?? 'All Messages' }}
            <span class="badge">{{ $messages->total() }}</span>
        </a>
        <a href="{{ route('user.messages.index', ['status' => 'new']) }}"
            class="message-tab {{ request('status') === 'new' ? 'active' : '' }}">
            {{ $t('messages.new_messages') ?? 'New' }}
            @if($newCount > 0)
            <span class="badge badge-new">{{ $newCount }}</span>
            @endif
        </a>
        <a href="{{ route('user.messages.index', ['status' => 'read']) }}"
            class="message-tab {{ request('status') === 'read' ? 'active' : '' }}">
            {{ $t('messages.read') ?? 'Read' }}
        </a>
        <a href="{{ route('user.messages.index', ['status' => 'replied']) }}"
            class="message-tab {{ request('status') === 'replied' ? 'active' : '' }}">
            {{ $t('messages.replied') ?? 'Replied' }}
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="messages-filter-section">
        <form method="GET" action="{{ route('user.messages.index') }}" class="messages-filter-form">
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="filter-input-group">
                <i class="fa-solid fa-search"></i>
                <input type="text"
                    name="search"
                    class="filter-input"
                    placeholder="{{ $t('messages.search_messages') ?? 'Search messages...' }}"
                    value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-filter-abandoned">
                {{ $t('messages.search') ?? 'Search' }}
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('user.messages.index') }}" class="btn-clear">
                {{ $t('messages.clear') ?? 'Clear' }}
            </a>
            @endif
        </form>
    </div>

    <!-- Messages List -->
    <div class="messages-list">
        @forelse($messages as $message)
        <div class="message-item {{ !$message->isRead() ? 'unread' : '' }}">
            <div class="message-item-header">
                <div class="message-item-left">
                    <div class="message-sender">
                        <strong>{{ $message->sender_name }}</strong>
                        @if($message->sender_email)
                        <span class="message-email">{{ $message->sender_email }}</span>
                        @endif
                    </div>
                    <div class="message-subject">
                        <a href="{{ route('user.messages.show', $message) }}">
                            {{ $message->subject }}
                        </a>
                    </div>
                    @if($message->order_number)
                    <div class="message-order">
                        <i class="fa-solid fa-box"></i>
                        {{ $t('messages.order_number') ?? 'Order' }}: #{{ $message->order_number }}
                    </div>
                    @endif
                </div>
                <div class="message-item-right">
                    <div class="message-date">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </div>
                    <div class="message-status">
                        <span class="status-badge status-{{ $message->status }}">
                            @if($locale === 'ar')
                            @if($message->status === 'new') جديد
                            @elseif($message->status === 'read') مقروء
                            @elseif($message->status === 'replied') تم الرد
                            @elseif($message->status === 'closed') مغلق
                            @else {{ ucfirst($message->status) }}
                            @endif
                            @else
                            {{ ucfirst($message->status) }}
                            @endif
                        </span>
                    </div>
                    @if($message->hasReplies())
                    <div class="message-replies-count">
                        <i class="fa-solid fa-reply"></i>
                        {{ $message->replies->count() }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="message-preview">
                {{ \Illuminate\Support\Str::limit($message->message, 150) }}
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fa-solid fa-envelope-open"></i>
            <h3>{{ $t('messages.no_messages') ?? 'No messages found' }}</h3>
            <p>{{ $t('messages.no_messages_description') ?? 'You don\'t have any messages yet.' }}</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($messages->hasPages())
    <div class="messages-pagination">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush