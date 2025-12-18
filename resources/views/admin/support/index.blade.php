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
            <h1 class="page-title">{{ $t('messages.support_tickets') ?? 'تذاكر الدعم' }}</h1>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="messages-tabs">
        <a href="{{ route('admin.support.index', ['status' => '']) }}"
            class="message-tab {{ !request('status') ? 'active' : '' }}">
            {{ $t('messages.all_tickets') ?? 'All Tickets' }}
            <span class="badge">{{ $tickets->total() }}</span>
        </a>
        <a href="{{ route('admin.support.index', ['status' => 'open']) }}"
            class="message-tab {{ request('status') === 'open' ? 'active' : '' }}">
            {{ $t('messages.open') ?? 'Open' }}
            @if($openCount > 0)
            <span class="badge badge-new">{{ $openCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.support.index', ['status' => 'in_progress']) }}"
            class="message-tab {{ request('status') === 'in_progress' ? 'active' : '' }}">
            {{ $t('messages.in_progress') ?? 'In Progress' }}
            @if($inProgressCount > 0)
            <span class="badge">{{ $inProgressCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.support.index', ['status' => 'resolved']) }}"
            class="message-tab {{ request('status') === 'resolved' ? 'active' : '' }}">
            {{ $t('messages.resolved') ?? 'Resolved' }}
        </a>
        <a href="{{ route('admin.support.index', ['status' => 'closed']) }}"
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
                        <a href="{{ route('admin.support.show', $ticket) }}" class="ticket-subject">
                            {{ $ticket->subject }}
                        </a>
                    </div>
                    <div class="ticket-meta">
                        <span class="ticket-user">
                            <i class="fa-solid fa-user"></i>
                            {{ $ticket->user->name }} ({{ $ticket->user->email }})
                        </span>
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
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                    <span class="ticket-priority priority-{{ $ticket->priority }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                    @if($ticket->assignedAdmin)
                    <span class="ticket-assigned">
                        <i class="fa-solid fa-user-check"></i>
                        {{ $ticket->assignedAdmin->name }}
                    </span>
                    @endif
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

@push('scripts')
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush