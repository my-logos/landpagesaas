<!-- Page Settings Modal -->
<div class="modal-overlay" id="pageSettingsModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fa-solid fa-cog"></i>
                {{ $t('messages.page_settings') ?? 'إعدادات الصفحة' }}
            </h3>
            <button type="button" class="modal-close" id="close-page-settings-modal">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="modal-content">
            <!-- Smart Sales Assistant -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="settings-section-content">
                        <h4 class="settings-section-title">
                            <i class="fa-solid fa-robot"></i>
                            {{ $t('messages.smart_sales_assistant') ?? 'المساعد الذكي للمبيعات' }}
                        </h4>
                        <p class="settings-section-description">{{ $t('messages.smart_assistant_description') ?? 'يساعد عملاءك في الإجابة على أسئلتهم حول المنتج' }}</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="smartAssistantToggle">
                        <span class="toggle-slider">
                            <span></span>
                        </span>
                    </label>
                </div>
                <p class="settings-section-note">
                    {{ $t('messages.smart_assistant_note') ?? 'لن يظهر زر المحادثة في صفحة الهبوط' }}
                </p>
            </div>

            <!-- Fixed Order Button -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="settings-section-content">
                        <h4 class="settings-section-title">
                            <i class="fa-solid fa-cart-shopping"></i>
                            {{ $t('messages.fixed_order_button') ?? 'زر الطلب الثابت' }}
                        </h4>
                        <p class="settings-section-description">{{ $t('messages.fixed_order_button_description') ?? 'زر ثابت يظهر في صفحة الهبوط لتسهيل عملية الطلب' }}</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="fixedOrderButtonToggle">
                        <span class="toggle-slider">
                            <span></span>
                        </span>
                    </label>
                </div>
                <div id="fixedOrderButtonSettings">
                    <div class="button-settings-grid">
                        <div class="button-setting-group">
                            <label class="button-setting-label">{{ $t('messages.button_text') ?? 'نص الزر' }}</label>
                            <input type="text" id="buttonText" value="اطلب الآن" class="button-setting-input">
                        </div>
                        <div class="button-setting-group">
                            <label class="button-setting-label">{{ $t('messages.button_position') ?? 'موقع الزر' }}</label>
                            <select id="buttonPosition" class="button-setting-select">
                                <option value="bottom-right">{{ $t('messages.bottom_right') ?? 'أسفل اليمين' }}</option>
                                <option value="bottom-left">{{ $t('messages.bottom_left') ?? 'أسفل اليسار' }}</option>
                                <option value="top-right">{{ $t('messages.top_right') ?? 'أعلى اليمين' }}</option>
                                <option value="top-left">{{ $t('messages.top_left') ?? 'أعلى اليسار' }}</option>
                            </select>
                        </div>
                        <div class="button-setting-group">
                            <label class="button-setting-label">{{ $t('messages.button_color') ?? 'لون الزر' }}</label>
                            <input type="color" id="buttonColor" value="#667eea" class="button-setting-input">
                        </div>
                        <div class="button-setting-group">
                            <label class="button-setting-label">{{ $t('messages.button_shape') ?? 'شكل الزر' }}</label>
                            <select id="buttonShape" class="button-setting-select">
                                <option value="rounded">{{ $t('messages.rounded') ?? 'مدور' }}</option>
                                <option value="square">{{ $t('messages.square') ?? 'مربع' }}</option>
                            </select>
                        </div>
                        <div class="button-setting-group">
                            <label class="button-setting-label">{{ $t('messages.button_size') ?? 'حجم الزر' }}</label>
                            <select id="buttonSize" class="button-setting-select">
                                <option value="small">{{ $t('messages.small') ?? 'صغير' }}</option>
                                <option value="medium" selected>{{ $t('messages.medium') ?? 'متوسط' }}</option>
                                <option value="large">{{ $t('messages.large') ?? 'كبير' }}</option>
                            </select>
                        </div>
                        <div class="button-setting-group">
                            <label class="button-setting-label">{{ $t('messages.button_icon') ?? 'أيقونة الزر' }}</label>
                            <select id="buttonIcon" class="button-setting-select">
                                <option value="cart-shopping">{{ $t('messages.shopping_cart') ?? 'عربة التسوق' }}</option>
                                <option value="bag">{{ $t('messages.bag') ?? 'حقيبة' }}</option>
                                <option value="credit-card">{{ $t('messages.credit_card') ?? 'بطاقة ائتمان' }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Smart Coupons -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="settings-section-content">
                        <h4 class="settings-section-title">
                            <i class="fa-solid fa-ticket"></i>
                            {{ $t('messages.smart_coupons_system') ?? 'نظام الكوبونات الذكي' }}
                        </h4>
                        <p class="settings-section-description">{{ $t('messages.smart_coupons_description') ?? 'كوبونات خصم تلقائية بناء على سلوك الزائر' }}</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="smartCouponsToggle">
                        <span class="toggle-slider">
                            <span></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Remaining Items Counter -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="settings-section-content">
                        <h4 class="settings-section-title">
                            <i class="fa-solid fa-gear"></i>
                            {{ $t('messages.remaining_items_counter') ?? 'عداد القطع المتبقية' }}
                        </h4>
                        <p class="settings-section-description">{{ $t('messages.remaining_items_counter_description') ?? 'عداد تنازلي يخلق شعور بالإلحاح لتشجيع الشراء' }}</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="remainingItemsCounterToggle">
                        <span class="toggle-slider">
                            <span></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" id="cancel-page-settings-modal">
                {{ $t('messages.close') ?? 'إغلاق' }}
            </button>
            <button type="button" class="btn-modal-save" id="save-page-settings">
                <i class="fa-solid fa-save"></i>
                {{ $t('messages.save_order_button_settings') ?? 'حفظ إعدادات زر الطلب' }}
            </button>
        </div>
    </div>
</div>