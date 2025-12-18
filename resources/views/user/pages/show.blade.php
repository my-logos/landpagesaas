@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Preview Header -->
    <div class="ls-card page-preview-header">
        <div class="preview-header-content">
            <div class="preview-title-section">
                <h1 class="page-title">
                    <i class="fa-solid fa-eye"></i>
                    {{ $t('messages.preview_page') ?? 'معاينة الصفحة' }}
                </h1>
                <p class="preview-subtitle">{{ $page->title ?? '' }}</p>
            </div>
            <div class="preview-actions">
                <a href="{{ route('user.pages.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i>
                    {{ $t('messages.back') ?? 'العودة' }}
                </a>
                <a href="{{ $pageUrl }}" target="_blank" class="btn btn-primary">
                    <i class="fa-solid fa-external-link"></i>
                    {{ $t('messages.open_page') ?? 'فتح الصفحة' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Preview Notification Modal -->
    <div class="modal-overlay" id="previewNotificationModal">
        <div class="modal-container preview-modal">
            <div class="modal-header">
                <div class="modal-icon-wrapper">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h2 class="modal-title">{{ $t('messages.preview_notification') ?? 'تنبيه المعاينة' }}</h2>
            </div>

            <div class="modal-body">
                <p class="modal-note">{{ $t('messages.preview_note') ?? 'يرجى ملاحظة أن صفحة المعاينة:' }}</p>

                <ul class="preview-features-list">
                    <li class="feature-item feature-visible">
                        <i class="fa-solid fa-eye"></i>
                        <span>{{ $t('messages.visible_only_to_you') ?? 'مرئية فقط لك' }}</span>
                    </li>
                    <li class="feature-item feature-disabled">
                        <i class="fa-solid fa-xmark"></i>
                        <span>{{ $t('messages.cannot_use_in_campaigns') ?? 'لا يمكن استخدامها في الحملات' }}</span>
                    </li>
                    <li class="feature-item feature-disabled">
                        <i class="fa-solid fa-xmark"></i>
                        <span>{{ $t('messages.cannot_receive_orders_preview') ?? 'لا يمكن استلام الطلبات في نسخة المعاينة' }}</span>
                    </li>
                </ul>

                <div class="info-box info-primary">
                    <p>{{ $t('messages.must_publish_first') ?? 'يجب نشر الصفحة أولاً لتصبح مرئية للجميع على الإنترنت واستقبال الطلبات' }}</p>
                </div>

                <div class="info-box info-tip">
                    <i class="fa-solid fa-pencil"></i>
                    <p>{{ $t('messages.ai_editing_tip') ?? 'إذا كان يوجد أي مشاكل في تصميم أو شكل صفحة الهبوط يمكنك تعديلها بسهولة. قم بالضغط على زر تعديل واكتب التعديل الذي ترغب به وسيقوم الذكاء الاصطناعي بتنفيذه.' }}</p>
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" id="dontShowAgain">
                    <span>{{ $t('messages.dont_show_again') ?? 'عدم عرض هذه النافذة مرة أخرى' }}</span>
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" id="backFromPreview" class="btn btn-secondary">
                    {{ $t('messages.back') ?? 'العودة' }}
                </button>
                <button type="button" id="continuePreview" class="btn btn-primary">
                    {{ $t('messages.continue_preview') ?? 'متابعة المعاينة' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Page Preview Frame -->
    <div class="ls-card preview-frame-container"
        data-modal-id="previewNotificationModal"
        data-iframe-id="previewIframe"
        data-back-route="{{ route('user.pages.index') }}"
        data-dont-show-key="dontShowPreviewNotification">
        <div class="preview-frame-wrapper">
            <iframe src="{{ $pageUrl }}" class="preview-iframe" id="previewIframe"></iframe>
        </div>
        <div class="preview-controls">
            <button type="button" class="preview-control-btn" data-action="refresh">
                <i class="fa-solid fa-rotate"></i>
                <span>{{ $t('messages.refresh') ?? 'تحديث' }}</span>
            </button>
            <button type="button" class="preview-control-btn" data-action="fullscreen">
                <i class="fa-solid fa-expand"></i>
                <span>{{ $t('messages.fullscreen') ?? 'ملء الشاشة' }}</span>
            </button>
            <div class="preview-size-selector">
                <select id="previewSize" class="preview-size-select">
                    <option value="desktop">{{ $t('messages.desktop') ?? 'سطح المكتب' }}</option>
                    <option value="tablet">{{ $t('messages.tablet') ?? 'جهاز لوحي' }}</option>
                    <option value="mobile">{{ $t('messages.mobile') ?? 'جوال' }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/page-preview.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/page-preview.js') }}"></script>
@endpush
@endsection