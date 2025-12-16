<div class="webhook-modal" id="webhook-modal">
    <div class="webhook-modal-overlay" data-close-modal></div>
    <div class="webhook-modal-content">
        <div class="webhook-modal-header">
            <h2 class="webhook-modal-title" id="modal-title">{{ $t('messages.add_new_webhook') ?? 'Add New Webhook' }}</h2>
            <button class="webhook-modal-close" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="webhook-form" class="webhook-form">
            @csrf
            <input type="hidden" id="webhook-id" name="webhook_id">

            <!-- Webhook Name -->
            <div class="form-group">
                <label for="webhook-name" class="form-label">
                    {{ $t('messages.webhook_name') ?? 'Webhook Name' }} *
                </label>
                <input type="text" id="webhook-name" name="name" class="form-control"
                    placeholder="{{ $t('messages.webhook_name_placeholder') ?? 'Example: Order Notifications' }}" required>
            </div>

            <!-- Webhook URL -->
            <div class="form-group">
                <label for="webhook-url" class="form-label">
                    {{ $t('messages.webhook_url') ?? 'Webhook URL' }} *
                </label>
                <input type="url" id="webhook-url" name="url" class="form-control"
                    placeholder="{{ $t('messages.webhook_url_placeholder') ?? 'https://example.com/webhook' }}" required>
            </div>

            <!-- Events to Track -->
            <div class="form-group">
                <label class="form-label">
                    {{ $t('messages.events_to_track') ?? 'Events to track' }} *
                </label>
                <div class="events-checkboxes">
                    <label class="checkbox-label">
                        <input type="checkbox" name="events[]" value="order_received" id="event-order-received">
                        <span>{{ $t('messages.order_received') ?? 'Receive new order' }}</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="events[]" value="order_status_updated" id="event-order-status">
                        <span>{{ $t('messages.order_status_updated') ?? 'Update order status' }}</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="events[]" value="merchant_status_updated" id="event-merchant-status">
                        <span>{{ $t('messages.merchant_status_updated') ?? 'Update merchant status' }}</span>
                    </label>
                </div>
            </div>

            <!-- Secret (Optional) -->
            <div class="form-group">
                <label for="webhook-secret" class="form-label">
                    {{ $t('messages.secret') ?? 'Secret' }} ({{ $t('messages.optional') ?? 'Optional' }})
                </label>
                <input type="text" id="webhook-secret" name="secret" class="form-control"
                    placeholder="{{ $t('messages.secret_placeholder') ?? 'Optional secret for webhook signature verification' }}">
                <small class="form-help">{{ $t('messages.secret_help') ?? 'Optional: Add a secret to verify webhook signatures' }}</small>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" data-close-modal>
                    {{ $t('messages.cancel') ?? 'Cancel' }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i>
                    <span id="submit-text">{{ $t('messages.create_webhook') ?? 'Create Webhook' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>