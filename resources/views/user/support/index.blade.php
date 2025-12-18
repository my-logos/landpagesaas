@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fa-solid fa-headset"></i>
            {{ $t('messages.support_center') ?? 'Support Center' }}
        </h1>
        <div class="page-header-right">
            <a href="{{ route('user.support.create') }}" class="btn-create-ticket {{ !$canCreateTicket ? 'disabled' : '' }}">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.create_new_ticket') ?? 'Create New Ticket' }}
            </a>
            <a href="{{ route('user.support.tickets.index') }}" class="btn-view-tickets">
                <i class="fa-solid fa-list"></i>
                {{ $t('messages.view_my_tickets') ?? 'View My Tickets' }}
            </a>
        </div>
    </div>

    @if(!$canCreateTicket)
    <div class="alert alert-warning">
        <i class="fa-solid fa-exclamation-triangle"></i>
        {{ $t('messages.ticket_limit_reached_message') ?? 'You have reached your monthly ticket limit' }} ({{ $currentMonthCount }}/{{ $ticketLimit ?? 'Unlimited' }})
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">
        <i class="fa-solid fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- How Can We Help You Section -->
    <div class="support-ai-section">
        <h2 class="support-section-title">{{ $t('messages.how_can_we_help_you') ?? 'How can we help you?' }}</h2>
        <p class="support-section-subtitle">{{ $t('messages.ask_question_ai_helper') ?? 'Ask your question and the intelligent assistant will answer you immediately.' }}</p>

        <div class="support-search-form">
            <div class="search-input-wrapper">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" id="faqSearch" class="support-search-input" placeholder="{{ $t('messages.ask_your_question') ?? 'Ask your question...' }}">
            </div>
            <button type="button" id="searchFaqBtn" class="btn-search-faq">
                {{ $t('messages.send_question') ?? 'Send Question' }}
            </button>
        </div>

        <div id="faqResults" class="faq-suggestions" style="display: none;">
            <h3 class="faq-suggestions-title">{{ $t('messages.suggested_questions') ?? 'Suggested Questions:' }}</h3>
            <div id="faqSuggestionsList" class="faq-suggestions-list"></div>
        </div>

        <div id="faqAnswer" class="faq-answer-box" style="display: none;">
            <h3 class="faq-answer-title">{{ $t('messages.answer') ?? 'Answer:' }}</h3>
            <div id="faqAnswerContent" class="faq-answer-content"></div>
            <div class="faq-answer-footer">
                <strong>{{ $t('messages.technical_support_team') ?? 'Technical Support Team' }}</strong>
                <span>{{ config('app.name') }}</span>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="support-faq-section">
        <h2 class="support-section-title">{{ $t('messages.frequently_asked_questions') ?? 'Frequently Asked Questions' }}</h2>
        <div class="faq-list">
            @foreach($faqs as $faq)
            <div class="faq-item">
                <div class="faq-question" data-faq-id="{{ $faq->id }}">
                    <span class="faq-question-text">{{ $faq->question }}</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Contact Us Section -->
    @if(!empty($socialMedia) && count($socialMedia) > 0)
    <div class="support-contact-section">
        <h2 class="support-section-title">{{ $t('messages.contact_us_via_social_media') ?? 'Contact us via social media' }}</h2>
        <div class="social-contact-list">
            @foreach($socialMedia as $social)
            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="social-contact-item">
                <i class="{{ $social['icon'] }}"></i>
                <span>{{ $social['display'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Tickets Section -->
    @if($tickets->count() > 0)
    <div class="support-recent-tickets">
        <h2 class="support-section-title">{{ $t('messages.recent_tickets') ?? 'Recent Tickets' }}</h2>
        <div class="tickets-preview-list">
            @foreach($tickets as $ticket)
            <div class="ticket-preview-item">
                <div class="ticket-preview-header">
                    <span class="ticket-number">#{{ $ticket->ticket_number }}</span>
                    <span class="ticket-status status-{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
                </div>
                <div class="ticket-preview-subject">{{ $ticket->subject }}</div>
                <a href="{{ route('user.support.tickets.show', $ticket) }}" class="ticket-preview-link">
                    {{ $t('messages.view_ticket') ?? 'View Ticket' }} <i class="fa-solid fa-arrow-{{ $dir === 'rtl' ? 'left' : 'right' }}"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/user-support.js') }}"></script>
@endpush