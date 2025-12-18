@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <a href="{{ route('user.support.index') }}" class="btn-back">
                <i class="fa-solid {{ $dir === 'rtl' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                {{ $t('messages.back_to_support') ?? 'Back to Support' }}
            </a>
            <h1 class="page-title">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.create_new_ticket') ?? 'Create New Ticket' }}
            </h1>
        </div>
    </div>

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

    <!-- Ticket Info -->
    @if($ticketLimit !== null)
    <div class="ticket-limit-info">
        <i class="fa-solid fa-info-circle"></i>
        <span>{{ $t('messages.tickets_used_this_month') ?? 'Tickets used this month' }}: {{ $currentMonthCount }}/{{ $ticketLimit }}</span>
    </div>
    @endif

    <!-- Create Ticket Form -->
    <div class="support-create-form-container">
        <form method="POST" action="{{ route('user.support.store') }}" class="support-ticket-form">
            @csrf

            <div class="form-group">
                <label for="subject" class="form-label">
                    {{ $t('messages.subject') ?? 'Subject' }} <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    class="form-control"
                    value="{{ old('subject') }}"
                    placeholder="{{ $t('messages.enter_ticket_subject') ?? 'Enter ticket subject' }}"
                    required
                    minlength="5"
                    maxlength="255">
                @error('subject')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">
                    {{ $t('messages.priority') ?? 'Priority' }}
                </label>
                <select id="priority" name="priority" class="form-control">
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>
                        {{ $t('messages.priority_medium') ?? 'Medium' }}
                    </option>
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                        {{ $t('messages.priority_low') ?? 'Low' }}
                    </option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                        {{ $t('messages.priority_high') ?? 'High' }}
                    </option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                        {{ $t('messages.priority_urgent') ?? 'Urgent' }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="message" class="form-label">
                    {{ $t('messages.message') ?? 'Message' }} <span class="required">*</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    class="form-control"
                    rows="10"
                    placeholder="{{ $t('messages.describe_your_issue') ?? 'Describe your issue in detail...' }}"
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
                    {{ $t('messages.create_ticket') ?? 'Create Ticket' }}
                </button>
                <a href="{{ route('user.support.index') }}" class="btn btn-secondary">
                    {{ $t('messages.cancel') ?? 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
<style>
    .ticket-limit-info {
        padding: 12px 16px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1e40af;
        font-size: 14px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        text-decoration: none;
        margin-bottom: 16px;
        font-size: 14px;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: #1f2b6b;
    }

    .support-create-form-container {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .support-ticket-form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        font-size: 14px;
    }

    .required {
        color: #ef4444;
    }

    .form-control {
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
    }

    .form-control:focus {
        outline: none;
        border-color: #1f2b6b;
        box-shadow: 0 0 0 3px rgba(31, 43, 107, 0.1);
    }

    .form-error {
        color: #ef4444;
        font-size: 13px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background: #1f2b6b;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1a2360;
    }

    .btn-secondary {
        background: #6b7280;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }
</style>
@endpush