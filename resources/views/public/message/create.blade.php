@extends('layouts.guest')

@section('content')
<div class="public-message-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="message-container">
        <div class="message-header">
            <div class="header-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <h1>{{ app()->getLocale() === 'ar' ? (isset($existingMessages) && $existingMessages && $existingMessages->count() > 0 ? 'الرسائل السابقة' : 'إرسال رسالة') : (isset($existingMessages) && $existingMessages && $existingMessages->count() > 0 ? 'Previous Messages' : 'Send Message') }}</h1>
            @if($orderNumber)
            <p class="header-subtitle">{{ app()->getLocale() === 'ar' ? 'بخصوص الطلب رقم' : 'Regarding Order Number' }} #{{ $orderNumber }}</p>
            @endif
        </div>

        @if(isset($existingMessages) && $existingMessages && $existingMessages->count() > 0)
        <!-- Existing Messages Thread -->
        <div class="messages-thread">
            @foreach($existingMessages as $msg)
            <div class="message-thread-item">
                <div class="message-thread-header">
                    <div class="message-thread-info">
                        <strong>{{ $msg->sender_name }}</strong>
                        <span class="message-date">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="message-status">
                        <span class="status-badge status-{{ $msg->status }}">
                            @if(app()->getLocale() === 'ar')
                            @if($msg->status === 'new') جديد
                            @elseif($msg->status === 'read') مقروء
                            @elseif($msg->status === 'replied') تم الرد
                            @else {{ ucfirst($msg->status) }}
                            @endif
                            @else
                            {{ ucfirst($msg->status) }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="message-thread-subject">
                    <strong>{{ $msg->subject }}</strong>
                </div>
                <div class="message-thread-content">
                    {!! nl2br(e($msg->message)) !!}
                </div>

                @if($msg->replies && $msg->replies->count() > 0)
                <div class="message-replies">
                    @foreach($msg->replies as $reply)
                    <div class="reply-item {{ $reply->is_customer_reply ? 'customer-reply' : 'admin-reply' }}">
                        <div class="reply-header">
                            <strong>
                                @if($reply->is_customer_reply)
                                {{ $msg->sender_name }}
                                @else
                                {{ app()->getLocale() === 'ar' ? 'الرد من المتجر' : 'Store Reply' }}
                                @endif
                            </strong>
                            <span class="reply-date">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="reply-content">
                            {!! nl2br(e($reply->reply)) !!}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if(!isset($replyingToMessage) || !$replyingToMessage)
        <div class="divider">
            <span>{{ app()->getLocale() === 'ar' ? 'إرسال رسالة جديدة' : 'Send New Message' }}</span>
        </div>
        @else
        <div class="divider">
            <span>{{ app()->getLocale() === 'ar' ? 'إرسال رد' : 'Send Reply' }}</span>
        </div>
        @endif
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

        @if(isset($replyingToMessage) && $replyingToMessage)
        <!-- Reply Form -->
        <form method="POST" action="{{ route('public.message.store') }}" class="message-form" id="replyForm">
            @csrf
            <input type="hidden" name="message_id" value="{{ $replyingToMessage->id }}">
            @if($orderNumber)
            <input type="hidden" name="order_number" value="{{ $orderNumber }}">
            @endif

            <div class="form-group">
                <label class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'الرد على' : 'Replying to' }}: <strong>{{ $replyingToMessage->subject }}</strong>
                </label>
                <div class="reply-to-info">
                    <p><strong>{{ app()->getLocale() === 'ar' ? 'المرسل' : 'From' }}:</strong> {{ $replyingToMessage->sender_name }}
                        @if($replyingToMessage->sender_email)
                        ({{ $replyingToMessage->sender_email }})
                        @endif
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label for="reply" class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'الرد' : 'Your Reply' }} <span class="required">*</span>
                </label>
                <textarea
                    id="reply"
                    name="reply"
                    class="form-control"
                    rows="8"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل ردك هنا...' : 'Enter your reply here...' }}"
                    required
                    minlength="10"
                    maxlength="5000">{{ old('reply') }}</textarea>
                @error('reply')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            @include('partials.recaptcha')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i>
                    {{ app()->getLocale() === 'ar' ? 'إرسال الرد' : 'Send Reply' }}
                </button>
                @if($orderNumber)
                <a href="{{ route('order.track', $orderNumber) }}" class="btn btn-secondary">
                    <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                    {{ app()->getLocale() === 'ar' ? 'العودة لتتبع الطلب' : 'Back to Order Tracking' }}
                </a>
                @endif
            </div>
        </form>
        @else
        <!-- New Message Form -->
        <form method="POST" action="{{ route('public.message.store') }}" class="message-form" id="messageForm">
            @csrf

            @if($orderNumber)
            <input type="hidden" name="order_number" value="{{ $orderNumber }}">
            @endif

            <div class="form-group">
                <label for="sender_name" class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }} <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="sender_name"
                    name="sender_name"
                    class="form-control"
                    value="{{ old('sender_name') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name' }}"
                    required
                    minlength="2"
                    maxlength="255">
                @error('sender_name')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sender_email" class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="optional">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'Optional' }})</span>
                </label>
                <input
                    type="email"
                    id="sender_email"
                    name="sender_email"
                    class="form-control"
                    value="{{ old('sender_email') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email' }}"
                    maxlength="255">
                @error('sender_email')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sender_phone" class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }} <span class="optional">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'Optional' }})</span>
                </label>
                <input
                    type="text"
                    id="sender_phone"
                    name="sender_phone"
                    class="form-control"
                    value="{{ old('sender_phone') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل رقم هاتفك' : 'Enter your phone number' }}"
                    maxlength="50">
                @error('sender_phone')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="subject" class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'الموضوع' : 'Subject' }} <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    class="form-control"
                    value="{{ old('subject') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل موضوع الرسالة' : 'Enter message subject' }}"
                    required
                    minlength="5"
                    maxlength="255">
                @error('subject')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="message" class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'الرسالة' : 'Message' }} <span class="required">*</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    class="form-control"
                    rows="8"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل رسالتك هنا...' : 'Enter your message here...' }}"
                    required
                    minlength="10"
                    maxlength="5000">{{ old('message') }}</textarea>
                @error('message')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            @include('partials.recaptcha')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i>
                    {{ app()->getLocale() === 'ar' ? 'إرسال الرسالة' : 'Send Message' }}
                </button>
                @if($orderNumber)
                <a href="{{ route('order.track', $orderNumber) }}" class="btn btn-secondary">
                    <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                    {{ app()->getLocale() === 'ar' ? 'العودة لتتبع الطلب' : 'Back to Order Tracking' }}
                </a>
                @endif
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/public-message.js') }}"></script>
@endpush