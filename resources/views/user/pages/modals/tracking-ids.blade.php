<!-- Tracking IDs Modal -->
<div class="modal-overlay" id="trackingIdsModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fa-solid fa-pencil"></i>
                {{ $t('messages.edit_tracking_ids') ?? 'تحرير معرفات التتبع' }}
            </h3>
            <button type="button" class="modal-close" id="close-tracking-ids-modal">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="modal-content">
            <p class="tracking-ids-description">
                {{ $t('messages.tracking_ids_description') ?? 'يمكنك إضافة أو تحديث معرفات التتبع لصفحة الهبوط. اتركها فارغة إذا كنت لا تريد استخدامها.' }}
            </p>

            <form id="trackingIdsForm" class="tracking-form">
                <!-- Facebook Pixel -->
                <div class="tracking-form-group {{ !($packageFeatures['facebook_pixel'] ?? false) ? 'feature-locked' : '' }}">
                    <label class="tracking-form-label">
                        Facebook Pixel ID
                        @if(!($packageFeatures['facebook_pixel'] ?? false))
                        <span class="feature-badge">{{ $t('messages.paid_plans_only') ?? 'للباقات المدفوعة فقط' }}</span>
                        @endif
                    </label>
                    <input type="text" name="facebook_pixel" id="facebook_pixel" placeholder="{{ $t('messages.example') ?? 'مثال' }}: 123456789012345" class="tracking-form-input" {{ !($packageFeatures['facebook_pixel'] ?? false) ? 'disabled' : '' }}>
                    @if(!($packageFeatures['facebook_pixel'] ?? false))
                    <div class="form-warning">
                        <i class="fa-solid fa-lock"></i>
                        {{ $t('messages.feature_available_for_paid_plans') ?? 'هذه الخدمة متاحة فقط للباقات المدفوعة' }}
                    </div>
                    @endif
                </div>

                <!-- TikTok Pixel -->
                <div class="tracking-form-group {{ !($packageFeatures['tiktok_pixel'] ?? false) ? 'feature-locked' : '' }}">
                    <label class="tracking-form-label">
                        TikTok Pixel ID
                        @if(!($packageFeatures['tiktok_pixel'] ?? false))
                        <span class="feature-badge">{{ $t('messages.paid_plans_only') ?? 'للباقات المدفوعة فقط' }}</span>
                        @endif
                    </label>
                    <input type="text" name="tiktok_pixel" id="tiktok_pixel" placeholder="{{ $t('messages.example') ?? 'مثال' }}: C4A1B2C3D4E5F6G7H819J0" class="tracking-form-input" {{ !($packageFeatures['tiktok_pixel'] ?? false) ? 'disabled' : '' }}>
                    @if(!($packageFeatures['tiktok_pixel'] ?? false))
                    <div class="form-warning">
                        <i class="fa-solid fa-lock"></i>
                        {{ $t('messages.feature_available_for_paid_plans') ?? 'هذه الخدمة متاحة فقط للباقات المدفوعة' }}
                    </div>
                    @endif
                </div>

                <!-- Snapchat Pixel -->
                <div class="tracking-form-group {{ !($packageFeatures['snapchat_pixel'] ?? false) ? 'feature-locked' : '' }}">
                    <label class="tracking-form-label">
                        Snapchat Pixel ID
                        @if(!($packageFeatures['snapchat_pixel'] ?? false))
                        <span class="feature-badge">{{ $t('messages.paid_plans_only') ?? 'للباقات المدفوعة فقط' }}</span>
                        @endif
                    </label>
                    <input type="text" name="snapchat_pixel" id="snapchat_pixel" placeholder="{{ $t('messages.example') ?? 'مثال' }}: a1b2c3d4-e5f6-7g8h-9i0j-k112m3n4o5p6" class="tracking-form-input" {{ !($packageFeatures['snapchat_pixel'] ?? false) ? 'disabled' : '' }}>
                    @if(!($packageFeatures['snapchat_pixel'] ?? false))
                    <div class="form-warning">
                        <i class="fa-solid fa-lock"></i>
                        {{ $t('messages.feature_available_for_paid_plans') ?? 'هذه الخدمة متاحة فقط للباقات المدفوعة' }}
                    </div>
                    @endif
                </div>

                <!-- Google Analytics -->
                <div class="tracking-form-group {{ !($packageFeatures['google_analytics'] ?? false) ? 'feature-locked' : '' }}">
                    <label class="tracking-form-label">
                        Google Analytics ID
                        @if(!($packageFeatures['google_analytics'] ?? false))
                        <span class="feature-badge">{{ $t('messages.paid_plans_only') ?? 'للباقات المدفوعة فقط' }}</span>
                        @endif
                    </label>
                    <input type="text" name="google_analytics_id" id="google_analytics_id" placeholder="{{ $t('messages.example') ?? 'مثال' }}: UA-XXXXXXXX-X أو G-XXXXXXXXXX" class="tracking-form-input" {{ !($packageFeatures['google_analytics'] ?? false) ? 'disabled' : '' }}>
                    @if(!($packageFeatures['google_analytics'] ?? false))
                    <div class="form-warning">
                        <i class="fa-solid fa-lock"></i>
                        {{ $t('messages.feature_available_for_paid_plans') ?? 'هذه الخدمة متاحة فقط للباقات المدفوعة' }}
                    </div>
                    @endif
                </div>

                <!-- Info Note -->
                <div class="info-note">
                    <i class="fa-solid fa-info-circle info-note-icon"></i>
                    <div class="info-note-content">
                        <p class="info-note-text">
                            {{ $t('messages.tracking_ids_note') ?? 'ملحوظة: إذا كانت الصفحة منشورة فالتعديلات قد تستغرق وقتاً للظهور' }}
                        </p>
                        <button type="button" class="btn-info-details">
                            <i class="fa-solid fa-eye"></i>
                            {{ $t('messages.details') ?? 'التفاصيل' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" id="cancel-tracking-ids-modal">
                {{ $t('messages.cancel') ?? 'إلغاء' }}
            </button>
            <button type="button" class="btn-modal-save" id="save-tracking-ids">
                {{ $t('messages.save_changes') ?? 'حفظ التغييرات' }}
            </button>
        </div>
    </div>
</div>