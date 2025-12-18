@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-header">
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
                            <i class="fa-solid fa-user"></i>
                            <span><strong>{{ $t('messages.user') ?? 'User' }}:</strong> {{ $ticket->user->name }} ({{ $ticket->user->email }})</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            <span>{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
                <div class="message-info-right">
                    <div class="message-actions">
                        <form method="POST" action="{{ route('admin.support.update', $ticket) }}" class="inline-form">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>{{ $t('messages.status') ?? 'Status' }}</label>
                                <select name="status" class="form-control status-select" onchange="this.form.submit()">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('messages.priority') ?? 'Priority' }}</label>
                                <select name="priority" class="form-control" onchange="this.form.submit()">
                                    <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('messages.assign_to') ?? 'Assign To' }}</label>
                                <select name="assigned_to" class="form-control" onchange="this.form.submit()">
                                    <option value="">{{ $t('messages.unassigned') ?? 'Unassigned' }}</option>
                                    @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
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
                            <strong>{{ $reply->user ? $reply->user->name : ($t('messages.admin') ?? 'Admin') }}</strong>
                            @else
                            <i class="fa-solid fa-user"></i>
                            <strong>{{ $ticket->user->name }}</strong>
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
            <div class="reply-form-container">
                <h4>{{ $t('messages.send_reply') ?? 'Send Reply' }}</h4>
                <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" class="reply-form" id="replyForm">
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
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush