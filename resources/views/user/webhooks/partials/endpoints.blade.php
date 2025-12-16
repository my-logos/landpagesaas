@if($endpoints->count() > 0)
<div class="webhooks-list">
    @foreach($endpoints as $webhook)
    <div class="webhook-card" data-webhook-id="{{ $webhook->id }}">
        <div class="webhook-card-header">
            <div class="webhook-card-title">
                <h3>{{ $webhook->name }}</h3>
                <span class="webhook-status-badge {{ $webhook->is_active ? 'active' : 'inactive' }}">
                    {{ $webhook->is_active ? ($t('messages.active') ?? 'Active') : ($t('messages.inactive') ?? 'Inactive') }}
                </span>
            </div>
            <div class="webhook-card-actions">
                <button class="btn-icon" data-edit-webhook="{{ $webhook->id }}" title="{{ $t('messages.edit_webhook') ?? 'Edit Webhook' }}">
                    <i class="fa-solid fa-edit"></i>
                </button>
                <button class="btn-icon" data-test-webhook="{{ $webhook->id }}" title="{{ $t('messages.test_webhook') ?? 'Test Webhook' }}">
                    <i class="fa-solid fa-flask"></i>
                </button>
                <button class="btn-icon btn-danger" data-delete-webhook="{{ $webhook->id }}" title="{{ $t('messages.delete_webhook') ?? 'Delete Webhook' }}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="webhook-card-body">
            <div class="webhook-info-item">
                <span class="webhook-info-label">{{ $t('messages.webhook_url') ?? 'Webhook URL' }}:</span>
                <span class="webhook-info-value">{{ $webhook->url }}</span>
            </div>
            <div class="webhook-info-item">
                <span class="webhook-info-label">{{ $t('messages.events_to_track') ?? 'Events' }}:</span>
                <div class="webhook-events">
                    @foreach($webhook->events as $event)
                    <span class="webhook-event-badge">{{ $t('messages.' . $event) ?? $event }}</span>
                    @endforeach
                </div>
            </div>
            <div class="webhook-info-item">
                <span class="webhook-info-label">{{ $t('messages.last_sent') ?? 'Last Sent' }}:</span>
                <span class="webhook-info-value">
                    {{ $webhook->last_log_sent_at ? $webhook->last_log_sent_at->diffForHumans() : ($t('messages.never') ?? 'Never') }}
                </span>
            </div>
        </div>
        <div class="webhook-card-footer">
            <button class="btn-toggle-webhook" data-webhook-id="{{ $webhook->id }}" data-is-active="{{ $webhook->is_active ? '1' : '0' }}">
                <i class="fa-solid fa-{{ $webhook->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                {{ $webhook->is_active ? ($t('messages.deactivate') ?? 'Deactivate') : ($t('messages.activate') ?? 'Activate') }}
            </button>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="webhooks-empty">
    <div class="webhooks-empty-icon">
        <i class="fa-solid fa-bolt"></i>
    </div>
    <h3 class="webhooks-empty-title">{{ $t('messages.no_webhooks') ?? 'No Webhooks' }}</h3>
    <p class="webhooks-empty-message">{{ $t('messages.start_adding_first_webhook') ?? 'Start by adding your first Webhook to receive order notifications' }}</p>
    <button class="btn-add-webhook-empty" data-open-modal="add-webhook">
        <i class="fa-solid fa-plus"></i>
        {{ $t('messages.add_new_webhook') ?? 'Add New Webhook' }}
    </button>
</div>
@endif