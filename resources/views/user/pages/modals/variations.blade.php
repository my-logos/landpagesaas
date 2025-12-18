<!-- Product Variations Modal -->
<div class="modal-overlay" id="variationsModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fa-solid fa-palette"></i>
                {{ $t('messages.manage_variations') ?? 'إدارة المقاسات والألوان' }}
            </h3>
            <button type="button" class="modal-close" id="close-variations-modal">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form id="variationsForm">
            <div class="modal-content">
                <p class="form-help product-variations-help">{{ $t('messages.product_variations_help') ?? 'أضف المقاسات (اسم فقط) والألوان (اسم + كود لون أو صورة)' }}</p>

                <!-- Sizes Section -->
                <div class="form-group">
                    <label class="form-label">{{ $t('messages.product_sizes') ?? 'المقاسات' }}</label>
                    <div id="modal-sizes-container" data-size-name-placeholder="{{ $t('messages.size_name') ?? 'Size name' }}">
                        <!-- Sizes will be loaded here -->
                    </div>
                    <button type="button" class="btn" data-variation-type="sizes">
                        <i class="fa-solid fa-plus"></i>
                        {{ $t('messages.add_size') ?? 'إضافة مقاس' }}
                    </button>
                </div>

                <!-- Colors Section -->
                <div class="form-group">
                    <label class="form-label">{{ $t('messages.product_colors') ?? 'الألوان' }}</label>
                    <div id="modal-colors-container" data-color-name-placeholder="{{ $t('messages.color_name') ?? 'Color name' }}" data-color-code-placeholder="{{ $t('messages.color_code_or_image') ?? 'Color code (#FF0000) or image URL' }}">
                        <!-- Colors will be loaded here -->
                    </div>
                    <button type="button" class="btn" data-variation-type="colors">
                        <i class="fa-solid fa-plus"></i>
                        {{ $t('messages.add_color') ?? 'إضافة لون' }}
                    </button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" id="cancel-variations-modal">
                    {{ $t('messages.cancel') ?? 'إلغاء' }}
                </button>
                <button type="button" class="btn-modal-save" id="save-variations-settings" data-saving-text="{{ $t('messages.saving') ?? 'Saving...' }}" data-success-text="{{ $t('messages.variations_updated') ?? 'Variations updated successfully' }}" data-error-text="{{ $t('messages.error_occurred') ?? 'An error occurred' }}">
                    <i class="fa-solid fa-save"></i>
                    {{ $t('messages.save') ?? 'حفظ' }}
                </button>
            </div>
        </form>
    </div>
</div>