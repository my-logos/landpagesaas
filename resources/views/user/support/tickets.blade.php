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
            <a href="{{ route('user.support.index') }}" class="btn-back" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6b7280; text-decoration: none;">
                <i class="fa-solid {{ $dir === 'rtl' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                {{ $t('messages.back_to_support') ?? 'Back to Support' }}
            </a>
            <h1 class="page-title">
                <i class="fa-solid fa-list"></i>
                {{ $t('messages.my_tickets') ?? 'My Tickets' }}
            </h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('user.support.create') }}" class="btn-create-ticket">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.create_new_ticket') ?? 'Create New Ticket' }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Status Tabs -->
    <div class="messages-tabs">
        <a href="{{ route('user.support.tickets.index', ['status' => '']) }}"
            class="message-tab {{ !request('status') ? 'active' : '' }}">
            {{ $t('messages.all_tickets') ?? 'All Tickets' }}
            <span class="badge">{{ $tickets->total() }}</span>
        </a>
        <a href="{{ route('user.support.tickets.index', ['status' => 'open']) }}"
            class="message-tab {{ request('status') === 'open' ? 'active' : '' }}">
            {{ $t('messages.open') ?? 'Open' }}
        </a>
        <a href="{{ route('user.support.tickets.index', ['status' => 'in_progress']) }}"
            class="message-tab {{ request('status') === 'in_progress' ? 'active' : '' }}">
            {{ $t('messages.in_progress') ?? 'In Progress' }}
        </a>
        <a href="{{ route('user.support.tickets.index', ['status' => 'resolved']) }}"
            class="message-tab {{ request('status') === 'resolved' ? 'active' : '' }}">
            {{ $t('messages.resolved') ?? 'Resolved' }}
        </a>
        <a href="{{ route('user.support.tickets.index', ['status' => 'closed']) }}"
            class="message-tab {{ request('status') === 'closed' ? 'active' : '' }}">
            {{ $t('messages.closed') ?? 'Closed' }}
        </a>
    </div>

    <!-- Tickets List -->
    <div class="tickets-list">
        @forelse($tickets as $ticket)
        <div class="ticket-item">
            <div class="ticket-item-header">
                <div class="ticket-item-left">
                    <div class="ticket-number-title">
                        <strong>#{{ $ticket->ticket_number }}</strong>
                        <a href="{{ route('user.support.tickets.show', $ticket) }}" class="ticket-subject">
                            {{ $ticket->subject }}
                        </a>
                    </div>
                    <div class="ticket-meta">
                        <span class="ticket-date">
                            <i class="fa-solid fa-calendar"></i>
                            {{ $ticket->created_at->format('Y-m-d H:i') }}
                        </span>
                        @if($ticket->replies->count() > 0)
                        <span class="ticket-replies-count">
                            <i class="fa-solid fa-comments"></i>
                            {{ $ticket->replies->count() }} {{ $t('messages.replies') ?? 'replies' }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="ticket-item-right">
                    <span class="ticket-status status-{{ $ticket->status }}">
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
                    <span class="ticket-priority priority-{{ $ticket->priority }}">
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
            <div class="ticket-preview">
                {{ \Illuminate\Support\Str::limit($ticket->message, 150) }}
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fa-solid fa-ticket-alt"></i>
            <h3>{{ $t('messages.no_tickets') ?? 'No tickets found' }}</h3>
            <p>{{ $t('messages.no_tickets_description') ?? 'You don\'t have any support tickets yet.' }}</p>
            <a href="{{ route('user.support.create') }}" class="btn-create-ticket">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.create_new_ticket') ?? 'Create New Ticket' }}
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tickets->hasPages())
    <div class="messages-pagination">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush