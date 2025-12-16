<div class="webhooks-statistics">
    <div class="statistics-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($successRate, 2) }}%</div>
                <div class="stat-label">{{ $t('messages.success_rate') ?? 'Success Rate' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-deliveries">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($totalDeliveries) }}</div>
                <div class="stat-label">{{ $t('messages.total_deliveries') ?? 'Total Deliveries' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-active">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($activeWebhooks) }}</div>
                <div class="stat-label">{{ $t('messages.active_webhooks') ?? 'Active Webhooks' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-total">
                <i class="fa-solid fa-list"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($totalWebhooks) }}</div>
                <div class="stat-label">{{ $t('messages.total_webhooks') ?? 'Total Webhooks' }}</div>
            </div>
        </div>
    </div>
</div>