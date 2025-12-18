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
        <h1 class="page-title">{{ $t('messages.send_new_message') ?? 'Send New Message' }}</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-error">
        <i class="fa-solid fa-exclamation-circle"></i>
        <div>
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Message Form -->
    <div class="message-detail-container">
        <div class="message-info-card">
            <form method="POST" action="{{ route('user.messages.store-new') }}" class="message-form">
                @csrf

                <div class="form-group">
                    <label for="order_id" class="form-label">
                        {{ $t('messages.select_order') ?? 'Select Order' }} <span class="required">*</span>
                    </label>
                    <select id="order_id" name="order_id" class="form-control" required>
                        <option value="">{{ $t('messages.select_order') ?? 'Select Order' }}</option>
                        @foreach($orders as $order)
                        <option value="{{ $order->id }}"
                            {{ old('order_id', $selectedOrder?->id) == $order->id ? 'selected' : '' }}
                            data-customer-name="{{ $order->customer_data['name'] ?? '' }}"
                            data-customer-email="{{ $order->customer_data['email'] ?? '' }}"
                            data-customer-phone="{{ $order->customer_data['phone'] ?? '' }}"
                            data-order-number="{{ $order->order_number }}">
                            #{{ $order->order_number }} - {{ $order->product ? $order->product->name : '-' }} ({{ $order->customer_data['name'] ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                    @error('order_id')
                    <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div id="customerInfo" style="display: none; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                    <h4 style="margin: 0 0 10px 0; color: #333; font-size: 16px;">{{ $t('messages.customer_info') ?? 'Customer Information' }}</h4>
                    <p style="margin: 5px 0; color: #666;"><strong>{{ $t('messages.customer_name') ?? 'Name' }}:</strong> <span id="customerName"></span></p>
                    <p style="margin: 5px 0; color: #666;"><strong>{{ $t('messages.email') ?? 'Email' }}:</strong> <span id="customerEmail"></span></p>
                    <p style="margin: 5px 0; color: #666;"><strong>{{ $t('messages.phone') ?? 'Phone' }}:</strong> <span id="customerPhone"></span></p>
                </div>

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
                        placeholder="{{ $t('messages.enter_subject') ?? 'Enter message subject' }}"
                        required
                        minlength="5"
                        maxlength="255">
                    @error('subject')
                    <span class="form-error">{{ $message }}</span>
                    @enderror
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
                    <a href="{{ route('user.messages.index') }}" class="btn btn-secondary">
                        {{ $t('messages.cancel') ?? 'Cancel' }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderSelect = document.getElementById('order_id');
        const customerInfo = document.getElementById('customerInfo');
        const customerName = document.getElementById('customerName');
        const customerEmail = document.getElementById('customerEmail');
        const customerPhone = document.getElementById('customerPhone');

        orderSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const name = selectedOption.getAttribute('data-customer-name') || 'N/A';
                const email = selectedOption.getAttribute('data-customer-email') || 'N/A';
                const phone = selectedOption.getAttribute('data-customer-phone') || 'N/A';

                customerName.textContent = name;
                customerEmail.textContent = email;
                customerPhone.textContent = phone;

                customerInfo.style.display = 'block';
            } else {
                customerInfo.style.display = 'none';
            }
        });

        // Trigger change if order is pre-selected
        if (orderSelect.value) {
            orderSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush