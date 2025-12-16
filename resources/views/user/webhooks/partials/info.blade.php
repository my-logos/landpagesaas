<div class="webhooks-info-grid">
    <!-- What are Webhooks? -->
    <div class="info-card">
        <div class="info-card-icon">
            <i class="fa-solid fa-file-lines"></i>
        </div>
        <h3 class="info-card-title">{{ $t('messages.what_are_webhooks') ?? 'What are Webhooks?' }}</h3>
        <p class="info-card-content">
            {{ $t('messages.what_are_webhooks_desc') ?? 'Webhooks are a way to send instant notifications from our application to your external applications when certain events occur, such as receiving a new order or updating an order status. Instead of constantly checking for updates, you will receive notifications instantly.' }}
        </p>
    </div>

    <!-- Main Benefits -->
    <div class="info-card">
        <div class="info-card-icon">
            <i class="fa-solid fa-gem"></i>
        </div>
        <h3 class="info-card-title">{{ $t('messages.main_benefits_webhooks') ?? 'Main Benefits' }}</h3>
        <ul class="info-card-list">
            <li><i class="fa-solid fa-check-circle"></i> {{ $t('messages.instant_notifications') ?? 'Instant: Receive notifications the moment events occur' }}</li>
            <li><i class="fa-solid fa-check-circle"></i> {{ $t('messages.efficiency_no_checking') ?? 'Efficiency: No need for continuous checking for updates' }}</li>
            <li><i class="fa-solid fa-check-circle"></i> {{ $t('messages.integration_easy') ?? 'Integration: Easy connection with external systems' }}</li>
            <li><i class="fa-solid fa-check-circle"></i> {{ $t('messages.automation_trigger') ?? 'Automation: Trigger automatic operations' }}</li>
        </ul>
    </div>

    <!-- WhatsApp Bot Integration -->
    <div class="info-card">
        <div class="info-card-icon">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
        <h3 class="info-card-title">{{ $t('messages.whatsapp_bot_integration') ?? 'WhatsApp Bot Integration' }}</h3>
        <p class="info-card-content">
            {{ $t('messages.whatsapp_bot_desc') ?? 'You can connect a WhatsApp bot to send automatic messages to customers when their orders are received or updated. The bot will receive customer data (name, phone number, order details) and send customized messages.' }}
        </p>
    </div>

    <!-- Email Notifications -->
    <div class="info-card">
        <div class="info-card-icon">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <h3 class="info-card-title">{{ $t('messages.email_notifications') ?? 'Email Notifications' }}</h3>
        <p class="info-card-content">
            {{ $t('messages.email_notifications_desc') ?? 'Send automatic email messages to customers or the work team when important events occur. Content can be customized based on event type and order data.' }}
        </p>
    </div>

    <!-- Mobile Applications -->
    <div class="info-card">
        <div class="info-card-icon">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <h3 class="info-card-title">{{ $t('messages.mobile_applications') ?? 'Mobile Applications' }}</h3>
        <p class="info-card-content">
            {{ $t('messages.mobile_applications_desc') ?? 'Connect mobile applications to send instant notifications to managers or customer service teams when new orders are received or statuses are updated.' }}
        </p>
    </div>

    <!-- CRM Systems -->
    <div class="info-card">
        <div class="info-card-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <h3 class="info-card-title">{{ $t('messages.crm_systems') ?? 'Customer Relationship Management Systems (CRM)' }}</h3>
        <p class="info-card-content">
            {{ $t('messages.crm_systems_desc') ?? 'Automatically update CRM systems with new customer and order data, which helps in tracking the customer journey and improving customer service.' }}
        </p>
    </div>
</div>

<!-- How to Setup -->
<div class="webhooks-setup-section">
    <h3 class="setup-title">{{ $t('messages.how_to_setup_webhooks') ?? 'How to set up Webhooks' }}</h3>
    <ol class="setup-steps">
        <li>{{ $t('messages.click_add_webhook') ?? 'Click on "Add New Webhook" at the top of the page' }}</li>
        <li>{{ $t('messages.enter_descriptive_name') ?? 'Enter a descriptive name for the Webhook (e.g., "WhatsApp Bot for Orders")' }}</li>
        <li>{{ $t('messages.enter_webhook_url') ?? 'Enter the Webhook URL for your external application or service' }}</li>
        <li>{{ $t('messages.choose_events') ?? 'Choose the events you want to track (order received, status update, etc.)' }}</li>
        <li>{{ $t('messages.save_and_test') ?? 'Save settings and test the Webhook to ensure it works' }}</li>
        <li>{{ $t('messages.activate_webhook') ?? 'Activate the Webhook to start receiving notifications' }}</li>
    </ol>
</div>

<!-- Webhook Data Example -->
<div class="webhooks-example-section">
    <div class="example-header">
        <div class="example-icon">
            <i class="fa-solid fa-code"></i>
        </div>
        <h3 class="example-title">{{ $t('messages.webhook_data_example') ?? 'Example of Webhook Data' }}</h3>
    </div>
    <p class="example-description">{{ $t('messages.webhook_data_example_desc') ?? 'When an event occurs, the following data will be sent to your Webhook URL:' }}</p>
    <pre class="example-code"><code>{
  "event_type": "order_received",
  "timestamp": "2024-01-15T10:30:00Z",
  "data": {
    "order_id": "ORD-12345",
    "customer_name": "أحمد محمد",
    "customer_phone": "+201234567890",
    "customer_email": "ahmed@example.com",
    "product_name": "منتج رائع",
    "quantity": 2,
    "total_amount": 500.00,
    "currency": "EGP",
    "landing_page": "متجر الإلكتروني",
    "order_status": "pending"
  }
}</code></pre>
    <p class="example-usage">{{ $t('messages.webhook_data_usage') ?? 'Your external application (e.g., WhatsApp bot) can read this data and use it to send customized messages to the customer or update your internal systems.' }}</p>
</div>