@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <a href="{{ route('user.support.tickets.index') }}" class="btn-back">
            <i class="fa-solid {{ $dir === 'rtl' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
            {{ $t('messages.back_to_tickets') ?? 'Back to Tickets' }}
        </a>
        <h1 class="page-title">{{ $t('messages.ticket_details') ?? 'Ticket Details' }}</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Ticket Details -->
    <div class="message-detail-container">
        <!-- Ticket Info -->
        <div class="message-info-card">
            <div class="message-info-header">
                <div class="message-info-left">
                    <h2 class="message-subject">{{ $ticket->subject }}</h2>
                    <div class="message-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-ticket-alt"></i>
                            <span><strong>{{ $t('messages.ticket_number') ?? 'Ticket Number' }}:</strong> #{{ $ticket->ticket_number }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            <span>{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
                <div class="message-info-right">
                    <div class="ticket-status-badge">
                        <span class="status-badge status-{{ $ticket->status }}">
                            @if($locale === 'ar')
                            @if($ticket->status === 'open') مفتوح
                            @elseif($ticket->status === 'in_progress') قيد المعالجة
                            @elseif($ticket->status === 'resolved') تم الحل
                            @elseif($ticket->status === 'closed') مغلق
                            @else {{ ucfirst($ticket->status) }}
                            @endif
                            @else
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            @endif
                        </span>
                    </div>
                    <div class="ticket-priority-badge">
                        <span class="priority-badge priority-{{ $ticket->priority }}">
                            @if($locale === 'ar')
                            @if($ticket->priority === 'low') منخفض
                            @elseif($ticket->priority === 'medium') متوسط
                            @elseif($ticket->priority === 'high') عالي
                            @elseif($ticket->priority === 'urgent') عاجل
                            @else {{ ucfirst($ticket->priority) }}
                            @endif
                            @else
                            {{ ucfirst($ticket->priority) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="message-content">
                <h3>{{ $t('messages.message') ?? 'Message' }}</h3>
                <div class="message-text">
                    {!! nl2br(e($ticket->message)) !!}
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <div class="replies-section">
            <h3 class="replies-title">
                <i class="fa-solid fa-reply"></i>
                {{ $t('messages.replies') ?? 'Replies' }}
                <span class="replies-count">({{ $ticket->replies->count() }})</span>
            </h3>

            <div class="replies-list">
                @forelse($ticket->replies as $reply)
                <div class="reply-item {{ $reply->is_admin_reply ? 'admin-reply' : 'user-reply' }}">
                    <div class="reply-header">
                        <div class="reply-author">
                            @if($reply->is_admin_reply)
                            <i class="fa-solid fa-user-shield"></i>
                            <strong>{{ $reply->user ? $reply->user->name : ($t('messages.support_team') ?? 'Support Team') }}</strong>
                            @else
                            <i class="fa-solid fa-user"></i>
                            <strong>{{ $t('messages.you') ?? 'You' }}</strong>
                            @endif
                        </div>
                        <div class="reply-date">
                            {{ $reply->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    <div class="reply-content">
                        {!! nl2br(e($reply->reply)) !!}
                    </div>
                </div>
                @empty
                <div class="no-replies">
                    <i class="fa-solid fa-comment-slash"></i>
                    <p>{{ $t('messages.no_replies') ?? 'No replies yet.' }}</p>
                </div>
                @endforelse
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
            <div class="reply-form-container">
                <h4>{{ $t('messages.send_reply') ?? 'Send Reply' }}</h4>
                <form method="POST" action="{{ route('user.support.tickets.reply', $ticket) }}" class="reply-form" id="replyForm">
                    @csrf
                    <div class="form-group">
                        <label for="reply">{{ $t('messages.your_reply') ?? 'Your Reply' }}</label>
                        <textarea
                            id="reply"
                            name="reply"
                            class="form-control"
                            rows="6"
                            placeholder="{{ $t('messages.enter_your_reply') ?? 'Enter your reply...' }}"
                            required
                            minlength="10"
                            maxlength="5000">{{ old('reply') }}</textarea>
                        @error('reply')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-actions visible">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ $t('messages.send_reply') ?? 'Send Reply' }}
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
<style>
    .priority-badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .ticket-priority-badge {
        margin-top: 8px;
    }
</style>
@endpush