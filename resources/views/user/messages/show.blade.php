@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <a href="{{ route('user.messages.index') }}" class="btn-back">
            <i class="fa-solid {{ $dir === 'rtl' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
            {{ $t('messages.back_to_messages') ?? 'Back to Messages' }}
        </a>
        <h1 class="page-title">{{ $t('messages.message_details') ?? 'Message Details' }}</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Message Details -->
    <div class="message-detail-container">
        <!-- Message Info -->
        <div class="message-info-card">
            <div class="message-info-header">
                <div class="message-info-left">
                    <h2 class="message-subject">{{ $message->subject }}</h2>
                    <div class="message-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-user"></i>
                            <span><strong>{{ $t('messages.sender') ?? 'Sender' }}:</strong> {{ $message->sender_name }}</span>
                        </div>
                        @if($message->sender_email)
                        <div class="meta-item">
                            <i class="fa-solid fa-envelope"></i>
                            <span>{{ $message->sender_email }}</span>
                        </div>
                        @endif
                        @if($message->sender_phone)
                        <div class="meta-item">
                            <i class="fa-solid fa-phone"></i>
                            <span>{{ $message->sender_phone }}</span>
                        </div>
                        @endif
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            <span>{{ $message->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($message->order_number)
                        <div class="meta-item">
                            <i class="fa-solid fa-box"></i>
                            <span><strong>{{ $t('messages.order_number') ?? 'Order Number' }}:</strong> #{{ $message->order_number }}</span>
                            @if($message->order)
                            <button type="button" class="link-order btn-view-order-modal" data-order-id="{{ $message->order->id }}">
                                <i class="fa-solid fa-eye"></i>
                                {{ $t('messages.view_order') ?? 'View Order' }}
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                <div class="message-info-right">
                    <div class="message-status-badge">
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
                    <div class="message-actions">
                        <form method="POST" action="{{ route('user.messages.update-status', $message) }}" class="inline-form">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-control status-select" onchange="this.form.submit()">
                                <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>
                                    {{ $t('messages.mark_as_read') ?? 'Mark as Read' }}
                                </option>
                                <option value="replied" {{ $message->status === 'replied' ? 'selected' : '' }}>
                                    {{ $t('messages.mark_as_replied') ?? 'Mark as Replied' }}
                                </option>
                                <option value="closed" {{ $message->status === 'closed' ? 'selected' : '' }}>
                                    {{ $t('messages.close') ?? 'Close' }}
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="message-content">
                <h3>{{ $t('messages.message') ?? 'Message' }}</h3>
                <div class="message-text">
                    {!! nl2br(e($message->message)) !!}
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <div class="replies-section">
            <h3 class="replies-title">
                <i class="fa-solid fa-reply"></i>
                {{ $t('messages.replies') ?? 'Replies' }}
                <span class="replies-count">({{ $message->replies->count() }})</span>
            </h3>

            <div class="replies-list">
                @forelse($message->replies as $reply)
                <div class="reply-item {{ $reply->is_customer_reply ? 'customer-reply' : 'user-reply' }}">
                    <div class="reply-header">
                        <div class="reply-author">
                            @if($reply->is_customer_reply)
                            <i class="fa-solid fa-user"></i>
                            <strong>{{ $message->sender_name }}</strong>
                            @else
                            <i class="fa-solid fa-user-shield"></i>
                            <strong>{{ $reply->user ? $reply->user->name : ($t('messages.you') ?? 'You') }}</strong>
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
                    <p>{{ $t('messages.no_replies') ?? 'No replies yet. Be the first to reply!' }}</p>
                </div>
                @endforelse
            </div>

            <!-- Reply Form -->
            <div class="reply-form-container">
                <div class="reply-form-header">
                    <h4>{{ $t('messages.send_reply') ?? 'Send Reply' }}</h4>
                    @if($message->order_number)
                    <button type="button" class="btn-toggle-message-type" id="toggleMessageType">
                        <i class="fa-solid fa-exchange-alt"></i>
                        {{ $t('messages.send_new_message_to_customer') ?? 'Send New Message to Customer' }}
                    </button>
                    @endif
                </div>

                <!-- Reply to Current Message Form -->
                <form method="POST" action="{{ route('user.messages.reply', $message) }}" class="reply-form" id="replyForm">
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

                <!-- Send New Message to Customer Form -->
                @if($message->order_number)
                <form method="POST" action="{{ route('user.messages.send-to-customer', $message) }}" class="reply-form new-message-form" id="newMessageForm" style="display: none;">
                    @csrf
                    <div class="form-group">
                        <label for="new_message_subject">{{ $t('messages.subject') ?? 'Subject' }} <span class="required">*</span></label>
                        <input
                            type="text"
                            id="new_message_subject"
                            name="subject"
                            class="form-control"
                            placeholder="{{ $t('messages.enter_subject') ?? 'Enter message subject' }}"
                            value="{{ old('subject', $t('messages.re_re') ?? 'Re: ') . $message->subject }}"
                            required
                            minlength="5"
                            maxlength="255">
                        @error('subject')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new_message_text">{{ $t('messages.message') ?? 'Message' }} <span class="required">*</span></label>
                        <textarea
                            id="new_message_text"
                            name="message"
                            class="form-control"
                            rows="6"
                            placeholder="{{ $t('messages.enter_your_message') ?? 'Enter your message...' }}"
                            required
                            minlength="10"
                            maxlength="5000">{{ old('message') }}</textarea>
                        @error('message')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-actions visible">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ $t('messages.send_message') ?? 'Send Message' }}
                        </button>
                        <button type="button" class="btn btn-secondary" id="cancelNewMessage">
                            {{ $t('messages.cancel') ?? 'Cancel' }}
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal-container order-details-modal">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fa-solid fa-box"></i>
                {{ $t('messages.order_details') ?? 'Order Details' }}
            </h2>
            <button type="button" class="modal-close" id="closeOrderModal">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="orderModalBody">
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>{{ $t('messages.loading') ?? 'Loading...' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/user-messages.js') }}"></script>
@endpush