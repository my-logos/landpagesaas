<!-- Create Link Modal -->
<div class="modal-overlay" id="createLinkModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fa-solid fa-rocket"></i>
                <i class="fa-solid fa-link"></i>
                {{ $t('messages.last_step_create_link') ?? 'الخطوة الأخيرة: أنشئ رابط لصفحتك' }}
            </h3>
            <button type="button" class="modal-close" id="close-create-link-modal">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="modal-content">
            <!-- Current Links Section -->
            <div id="currentLinksSection">
                <div class="current-links-header">
                    <h4 class="current-links-title">
                        <i class="fa-solid fa-link"></i>
                        {{ $t('messages.current_links') ?? 'الروابط الحالية' }}
                    </h4>
                    <button type="button" id="hideLinksBtn" class="btn-hide-links">
                        {{ $t('messages.hide_form') ?? 'إخفاء النموذج' }}
                    </button>
                </div>
                <div id="linksList">
                    <!-- Links will be populated here -->
                </div>
            </div>

            <!-- Link Creation Form -->
            <form id="createLinkForm" class="link-form">
                <!-- Store Name -->
                <div class="link-form-group">
                    <label class="link-form-label">
                        <span class="step-number">1</span>
                        {{ $t('messages.store_name') ?? 'اسم المتجر' }}
                    </label>
                    <input type="text" name="store_name" id="store_name" placeholder="{{ $t('messages.enter_store_name') ?? 'أدخل اسم المتجر' }}" required class="link-form-input">
                </div>

                <!-- Landing Page Name -->
                <div class="link-form-group">
                    <label class="link-form-label">
                        <span class="step-number">2</span>
                        {{ $t('messages.landing_page_name') ?? 'اسم صفحة الهبوط' }}
                    </label>
                    <input type="text" name="page_name" id="page_name" placeholder="{{ $t('messages.enter_landing_page_name') ?? 'أدخل اسم صفحة الهبوط' }}" required class="link-form-input">
                </div>

                <!-- Naming Requirements -->
                <div class="naming-requirements">
                    <div class="naming-requirements-content">
                        <i class="fa-solid fa-file-circle-exclamation naming-requirements-icon"></i>
                        <div>
                            <strong class="naming-requirements-title">{{ $t('messages.naming_requirements') ?? 'متطلبات التسمية:' }}</strong>
                            <p class="naming-requirements-text">
                                {{ $t('messages.naming_requirements_text') ?? 'يجب أن يزيد الاسم عن 3 أحرف، ويُسمح فقط بالأحرف والأرقام الإنجليزية وعلامة (-)، دون مسافات أو رموز خاصة.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Custom Domains Section -->
            <div class="custom-domains-section">
                <div class="custom-domains-header">
                    <h4 class="custom-domains-title">
                        <i class="fa-solid fa-globe"></i>
                        {{ $t('messages.custom_domains') ?? 'النطاقات المخصصة' }}
                    </h4>
                    <i class="fa-solid fa-chevron-up custom-domains-chevron"></i>
                </div>
                <p class="custom-domains-description">
                    {{ $t('messages.custom_domains_description') ?? 'ربط نطاق مخصص بصفحة الهبوط الخاصة بك' }}
                </p>
                <button type="button" class="btn-link-domain">
                    <i class="fa-solid fa-plus"></i>
                    {{ $t('messages.link_domain') ?? 'ربط نطاق' }}
                </button>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" id="cancel-create-link-modal">
                {{ $t('messages.cancel') ?? 'إلغاء' }}
            </button>
            <button type="button" class="btn-modal-save" id="save-create-link">
                <i class="fa-solid fa-link"></i>
                {{ $t('messages.create_new_link') ?? 'إنشاء رابط جديد' }}
            </button>
        </div>
    </div>
</div>