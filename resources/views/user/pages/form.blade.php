@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-form-container">
        <div class="page-form-header">
            <button type="button" class="close-btn" data-action="close-form" data-route="{{ route('user.pages.index') }}">
                <i class="fa-solid fa-times"></i>
            </button>
            <div class="page-form-title-group">
                <h1 class="page-title">{{ $t('messages.control_panel') ?? 'لوحة التحكم' }}</h1>
                <h2 class="page-form-title">
                    @if(isset($page))
                    {{ $t('messages.edit_landing_page') ?? 'تعديل صفحة الهبوط' }}
                    @else
                    {{ $t('messages.create_new_landing_page') ?? 'إنشاء صفحة هبوط جديدة' }}
                    @endif
                </h2>
            </div>
        </div>

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif


        <!-- Tabs Navigation -->
        <div class="page-creation-tabs">
            @if($aiEnabled)
            <button type="button" class="tab-btn {{ ($pageType === 'ai' && $aiEnabled) ? 'active' : '' }}" data-tab="ai-tab">
                <i class="fa-solid fa-brain"></i>
                {{ $t('messages.ai') ?? 'الذكاء الاصطناعي' }}
            </button>
            @else
            <button type="button" class="tab-btn disabled" disabled title="{{ $t('messages.ai_requires_paid_plan') ?? 'الذكاء الاصطناعي متاح فقط للباقات المدفوعة' }}">
                <i class="fa-solid fa-brain"></i>
                {{ $t('messages.ai') ?? 'الذكاء الاصطناعي' }}
                <i class="fa-solid fa-lock lock-icon"></i>
            </button>
            @endif
            <button type="button" class="tab-btn {{ ($pageType === 'classic' || !$aiEnabled) ? 'active' : '' }}" data-tab="classic-tab">
                <i class="fa-solid fa-pen-to-square"></i>
                {{ $t('messages.classic') ?? 'كلاسيك' }}
            </button>
        </div>

        <form method="POST" action="{{ isset($page) ? route('user.pages.update', $page) : route('user.pages.store') }}" class="page-form" id="pageForm">
            @csrf
            @if(isset($page))
            @method('PUT')
            @endif
            <input type="hidden" name="creation_type" id="creation_type" value="{{ $pageType === 'ai' && $aiEnabled ? 'ai' : 'classic' }}" data-ai-enabled="{{ $aiEnabled ? 'true' : 'false' }}" data-ai-available="{{ $aiAvailable ? 'true' : 'false' }}">

            <!-- AI Tab Content -->
            <div class="tab-content {{ ($pageType === 'ai' && $aiEnabled) ? 'active' : '' }}" id="ai-tab">
                @if(!$aiEnabled)
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <h3>{{ $t('messages.ai_requires_paid_plan') ?? 'الذكاء الاصطناعي متاح فقط للباقات المدفوعة' }}</h3>
                    <p>{{ $t('messages.upgrade_to_use_ai') ?? 'للاستفادة من ميزة إنشاء الصفحات بالذكاء الاصطناعي، يرجى الترقية إلى باقة مدفوعة.' }}</p>
                    <a href="{{ route('user.packages.index') }}" class="btn btn-upgrade">
                        <i class="fa-solid fa-arrow-up"></i>
                        {{ $t('messages.view_plans') ?? 'عرض الخطط' }}
                    </a>
                </div>
                @elseif(!$aiAvailable)
                <div class="alert alert-warning">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    {{ $t('messages.ai_not_configured_warning') ?? 'خدمة الذكاء الاصطناعي غير متاحة حالياً. يرجى التواصل مع المسؤول.' }}
                </div>
                @endif

                <!-- Basic Information Section -->
                <div class="page-form-card">
                    <h3 class="form-section-title">
                        <i class="fa-solid fa-info-circle"></i>
                        {{ $t('messages.basic_information') ?? 'المعلومات الأساسية' }}
                    </h3>

                    <!-- Choose Product -->
                    <div class="form-group">
                        <label for="ai_product_id" class="form-label">{{ $t('messages.choose_product') ?? 'اختر المنتج' }} *</label>
                        <select id="ai_product_id" name="product_id" class="form-control ai-field" data-required="true" required>
                            <option value="">{{ $t('messages.choose_product') ?? 'اختر منتجاً' }}</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', (isset($page) && $page->product_id == $product->id) ? $page->product_id : ($selectedProductId ?? '')) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                                @if($product->description)
                                - {{ Str::limit($product->description, 50) }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('product_id')<span class="form-error">{{ $message }}</span>@enderror
                        <small class="form-help">{{ $t('messages.select_product_for_landing_page') ?? 'اختر المنتج الذي تريد إنشاء صفحة هبوط له. سيتم استخدام معلومات المنتج (الاسم، الوصف، الصور، السعر) لإنشاء الصفحة تلقائياً.' }}</small>
                    </div>

                    <!-- Choose Language -->
                    <div class="form-group">
                        <label for="ai_language" class="form-label">{{ $t('messages.page_language') ?? 'لغة الصفحة' }} *</label>
                        <select id="ai_language" name="language" class="form-control ai-field" data-required="true" required>
                            <option value="ar" {{ ($currentLanguage ?? 'ar') == 'ar' ? 'selected' : '' }}>
                                {{ $t('messages.arabic') ?? 'العربية' }}
                            </option>
                            <option value="en" {{ ($currentLanguage ?? 'ar') == 'en' ? 'selected' : '' }}>
                                {{ $t('messages.english') ?? 'English' }}
                            </option>
                        </select>
                        @error('language')<span class="form-error">{{ $message }}</span>@enderror
                        <small class="form-help">{{ $t('messages.select_page_language') ?? 'اختر اللغة التي تريد أن تكون بها صفحة الهبوط. سيتم إنشاء المحتوى بالكامل بهذه اللغة.' }}</small>
                    </div>
                </div>

                <!-- AI Information Section -->
                <div class="page-form-card ai-info-box">
                    <div class="form-info-box">
                        <div class="info-item">
                            <i class="fa-solid fa-brain"></i>
                            <div>
                                <h4>
                                    {{ $t('messages.ai_will_create_page') ?? 'الذكاء الاصطناعي سيقوم بإنشاء الصفحة تلقائياً' }}
                                </h4>
                                <p>
                                    {{ $t('messages.ai_page_creation_info') ?? 'سيقوم الذكاء الاصطناعي بإنشاء صفحة هبوط احترافية وجذابة تلقائياً بناءً على معلومات المنتج المحدد. الصفحة ستشمل:' }}
                                </p>
                                <ul>
                                    <li>{{ $t('messages.ai_feature_hero') ?? 'قسم Hero جذاب مع عنوان رئيسي ووصف' }}</li>
                                    <li>{{ $t('messages.ai_feature_product') ?? 'قسم للمنتج يعرض المعلومات والصور' }}</li>
                                    <li>{{ $t('messages.ai_feature_features') ?? 'قسم للمميزات والفوائد' }}</li>
                                    <li>{{ $t('messages.ai_feature_form') ?? 'نموذج طلب احترافي' }}</li>
                                    <li>{{ $t('messages.ai_feature_design') ?? 'تصميم عصري وجذاب responsive' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classic Tab Content -->
            <div class="tab-content {{ ($pageType === 'classic' || !$aiEnabled) ? 'active' : '' }}" id="classic-tab">
                <!-- Basic Information Section -->
                <div class="page-form-card">
                    <h3 class="form-section-title">
                        <i class="fa-solid fa-info-circle"></i>
                        {{ $t('messages.basic_information') ?? 'المعلومات الأساسية' }}
                    </h3>

                    <!-- Page Title -->
                    <div class="form-group">
                        <label for="classic_title" class="form-label">{{ $t('messages.page_title') ?? 'عنوان الصفحة' }} *</label>
                        <input type="text" id="classic_title" name="title" class="form-control classic-field" data-required="true" value="{{ old('title', isset($page) ? $page->title : '') }}" maxlength="150" required pattern="[a-zA-Z0-9\s\-_]+" title="{{ $t('messages.english_only') ?? 'English letters, numbers, spaces, hyphens and underscores only' }}">
                        <small class="form-counter"><span id="classic_title-counter">0/150</span></small>
                        <small class="form-help text-muted">
                            <i class="fa-solid fa-info-circle"></i>
                            {{ $t('messages.english_only_title') ?? 'English letters, numbers, spaces, hyphens (-) and underscores (_) only' }}
                        </small>
                        @error('title')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <!-- Page Language -->
                    <div class="form-group">
                        <label for="classic_language" class="form-label">{{ $t('messages.page_language') ?? 'لغة الصفحة' }} *</label>
                        <select id="classic_language" name="language" class="form-control classic-field" data-required="true" required>
                            @php
                            $classicLanguage = old('language', isset($page) && $page->content ? (is_string($page->content) ? json_decode($page->content, true) : $page->content)['language'] ?? app()->getLocale() : app()->getLocale());
                            @endphp
                            <option value="ar" {{ $classicLanguage == 'ar' ? 'selected' : '' }}>
                                {{ $t('messages.arabic') ?? 'العربية' }} (RTL)
                            </option>
                            <option value="en" {{ $classicLanguage == 'en' ? 'selected' : '' }}>
                                {{ $t('messages.english') ?? 'English' }} (LTR)
                            </option>
                        </select>
                        <small class="form-help">{{ $t('messages.select_page_language') ?? 'اختر اللغة التي تريد أن تكون بها صفحة الهبوط.' }}</small>
                        @error('language')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <!-- Choose Product -->
                    <div class="form-group">
                        <label for="classic_product_id" class="form-label">{{ $t('messages.choose_product') ?? 'اختر المنتج' }}</label>
                        <select id="classic_product_id" name="product_id" class="form-control classic-field">
                            <option value="">{{ $t('messages.choose_product') ?? 'اختر منتجاً (اختياري)' }}</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', (isset($page) && $page->product_id == $product->id) ? $page->product_id : ($selectedProductId ?? '')) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('product_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <!-- Choose Template -->
                    <div class="form-group">
                        <label class="form-label">{{ $t('messages.choose_template') ?? 'اختر القالب' }}</label>
                        <div class="templates-selection">
                            <label class="template-option" data-template-id="">
                                <input type="radio" name="template_id" id="template_none" value="" {{ old('template_id', (isset($page) && !$page->template_id) ? '' : '') == '' ? 'checked' : '' }}>
                                <div class="template-preview">
                                    <i class="fa-solid fa-file-code"></i>
                                </div>
                                <label for="template_none">{{ $t('messages.no_template') ?? 'بدون قالب' }}</label>
                            </label>
                            @foreach($templates ?? [] as $template)
                            <label class="template-option" data-template-id="{{ $template->id }}">
                                <input type="radio" name="template_id" id="template_{{ $template->id }}" value="{{ $template->id }}" {{ old('template_id', (isset($page) && $page->template_id == $template->id) ? $page->template_id : '') == $template->id ? 'checked' : '' }}>
                                <div class="template-preview">
                                    @if($template->preview_image)
                                    <img src="{{ asset('' . $template->preview_image) }}" alt="{{ $template->name }}">
                                    @else
                                    <i class="fa-solid fa-palette"></i>
                                    @endif
                                </div>
                                <label for="template_{{ $template->id }}">{{ $template->name }}</label>
                            </label>
                            @endforeach
                        </div>
                        @error('template_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <!-- Additional Description -->
                    <div class="form-group">
                        <label for="additional_description" class="form-label">{{ $t('messages.additional_description') ?? 'وصف إضافي' }}</label>
                        <textarea id="additional_description" name="additional_description" class="form-control" rows="4" maxlength="2000">{{ old('additional_description', isset($page) ? $page->additional_description : '') }}</textarea>
                        <small class="form-counter"><span id="additional_description-counter">0/2000</span></small>
                        @error('additional_description')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="page-form-card collapsible-section">
                    <div class="toggle-section-btn">
                        <h3 class="form-section-title">
                            <i class="fa-solid fa-search"></i>
                            {{ $t('messages.seo_settings') ?? 'إعدادات SEO' }}
                        </h3>
                        <i class="fa-solid fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label for="seo_title" class="form-label">{{ $t('messages.seo_title') ?? 'عنوان SEO' }}</label>
                            <input type="text" id="seo_title" name="seo_title" class="form-control" value="{{ old('seo_title', isset($page) ? $page->seo_title : '') }}" maxlength="255">
                            <small class="form-counter"><span id="seo_title-counter">0/255</span></small>
                            @error('seo_title')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="seo_description" class="form-label">{{ $t('messages.seo_description') ?? 'وصف SEO' }}</label>
                            <textarea id="seo_description" name="seo_description" class="form-control" rows="3" maxlength="500">{{ old('seo_description', isset($page) ? $page->seo_description : '') }}</textarea>
                            <small class="form-counter"><span id="seo_description-counter">0/500</span></small>
                            @error('seo_description')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="seo_keywords" class="form-label">{{ $t('messages.seo_keywords') ?? 'كلمات مفتاحية SEO' }}</label>
                            <input type="text" id="seo_keywords" name="seo_keywords" class="form-control" value="{{ old('seo_keywords', isset($page) ? $page->seo_keywords : '') }}" maxlength="500">
                            <small class="form-counter"><span id="seo_keywords-counter">0/500</span></small>
                            @error('seo_keywords')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Form Builder Section -->
                <div class="page-form-card">
                    <h3 class="form-section-title">
                        <i class="fa-solid fa-list-check"></i>
                        {{ $t('messages.order_form_settings') ?? 'إعدادات نموذج الطلب' }}
                    </h3>

                    <!-- Form Type -->
                    <div class="form-group">
                        <label class="form-label">{{ $t('messages.form_type') ?? 'نوع النموذج' }} *</label>
                        <div class="custom-form-fields-row">
                            <button type="button" class="form-type-btn {{ ($formType ?? 'default') === 'default' ? 'active' : '' }}" data-type="default">
                                {{ $t('messages.default') ?? 'افتراضي' }}
                            </button>
                            <button type="button" class="form-type-btn {{ ($formType ?? 'default') === 'custom' ? 'active' : '' }}" data-type="custom">
                                {{ $t('messages.custom') ?? 'مخصص' }}
                            </button>
                        </div>
                        <input type="hidden" id="form_type" name="form_type" value="{{ $formType ?? 'default' }}">
                        @error('form_type')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <!-- Custom Form Fields -->
                    <div id="custom-form-fields" class="{{ ($formType ?? 'default') === 'custom' ? '' : 'hidden' }}">
                        <div class="form-group">
                            <label class="form-label">{{ $t('messages.available_fields') ?? 'الحقول المتاحة' }}</label>
                            <div class="custom-form-fields-container">
                                <button type="button" class="field-btn" data-field="full_name">
                                    {{ $t('messages.full_name') ?? 'الاسم الكامل' }}
                                </button>
                                <button type="button" class="field-btn" data-field="email">
                                    {{ $t('messages.email') ?? 'البريد الإلكتروني' }}
                                </button>
                                <button type="button" class="field-btn" data-field="phone">
                                    {{ $t('messages.phone') ?? 'الهاتف' }}
                                </button>
                                <button type="button" class="field-btn" data-field="address">
                                    {{ $t('messages.address') ?? 'العنوان' }}
                                </button>
                                <button type="button" class="field-btn" data-field="notes">
                                    {{ $t('messages.notes') ?? 'ملاحظات' }}
                                </button>
                                <button type="button" class="field-btn" data-field="quantity">
                                    {{ $t('messages.quantity') ?? 'الكمية' }}
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ $t('messages.selected_fields') ?? 'الحقول المختارة' }}</label>
                            <div id="selected-fields" data-old-fields="{{ $formFields ?? '[]' }}"></div>
                            <input type="hidden" id="form_fields" name="form_fields" value="{{ $formFields ?? '[]' }}">
                        </div>
                    </div>
                </div>

                <!-- Product Variations (Sizes & Colors) -->
                <div class="page-form-card">
                    <h3 class="form-section-title">
                        <i class="fa-solid fa-palette"></i>
                        {{ $t('messages.product_variations') ?? 'مقاسات وألوان المنتج' }}
                    </h3>
                    <p class="form-help product-variations-help">{{ $t('messages.product_variations_help') ?? 'أضف المقاسات والألوان المتاحة للمنتج مع ربط كل منها بصورة' }}</p>

                    <!-- Sizes Section -->
                    <div class="form-group">
                        <label class="form-label">{{ $t('messages.product_sizes') ?? 'المقاسات' }}</label>
                        <div id="sizes-container">
                            @if(!empty($sizes))
                            @foreach($sizes as $index => $size)
                            <div class="variation-item" data-index="{{ $index }}">
                                <div class="variation-item-content">
                                    <input type="text" name="sizes[{{ $index }}][name]" placeholder="{{ $t('messages.size_name') ?? 'اسم المقاس' }}" value="{{ $size['name'] ?? '' }}" class="form-control">
                                    <input type="url" name="sizes[{{ $index }}][image]" placeholder="{{ $t('messages.size_image_url') ?? 'رابط صورة المقاس' }}" value="{{ $size['image'] ?? '' }}" class="form-control variation-image-input" data-action="preview-variation">
                                    <div class="variation-preview {{ isset($size['image']) && $size['image'] ? '' : 'hidden' }}">
                                        @if(isset($size['image']) && $size['image'])
                                        <img src="{{ $size['image'] }}" alt="Preview">
                                        @endif
                                    </div>
                                    <button type="button" class="btn-remove-variation" data-action="remove-variation" data-type="sizes">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn" data-action="add-variation" data-type="sizes">
                            <i class="fa-solid fa-plus"></i>
                            {{ $t('messages.add_size') ?? 'إضافة مقاس' }}
                        </button>
                    </div>

                    <!-- Colors Section -->
                    <div class="form-group">
                        <label class="form-label">{{ $t('messages.product_colors') ?? 'الألوان' }}</label>
                        <div id="colors-container" data-initial-index="{{ count($colors) }}" data-color-name-placeholder="{{ $t('messages.color_name') ?? 'Color name' }}" data-color-image-placeholder="{{ $t('messages.color_image_url') ?? 'Color image URL' }}">
                            @if(!empty($colors))
                            @foreach($colors as $index => $color)
                            <div class="variation-item" data-index="{{ $index }}">
                                <div class="variation-item-content">
                                    <input type="text" name="colors[{{ $index }}][name]" placeholder="{{ $t('messages.color_name') ?? 'اسم اللون' }}" value="{{ $color['name'] ?? '' }}" class="form-control">
                                    <input type="url" name="colors[{{ $index }}][image]" placeholder="{{ $t('messages.color_image_url') ?? 'رابط صورة اللون' }}" value="{{ $color['image'] ?? '' }}" class="form-control variation-image-input" data-action="preview-variation">
                                    <div class="variation-preview {{ isset($color['image']) && $color['image'] ? '' : 'hidden' }}">
                                        @if(isset($color['image']) && $color['image'])
                                        <img src="{{ $color['image'] }}" alt="Preview">
                                        @endif
                                    </div>
                                    <button type="button" class="btn-remove-variation" data-action="remove-variation" data-type="colors">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn" data-action="add-variation" data-type="colors">
                            <i class="fa-solid fa-plus"></i>
                            {{ $t('messages.add_color') ?? 'إضافة لون' }}
                        </button>
                    </div>
                </div>

                <!-- AI Version (required for classic) -->
                <input type="hidden" name="ai_version" value="{{ old('ai_version', isset($page) && $page->ai_version ? $page->ai_version : 'v2') }}">
            </div>

            <!-- Submit Button -->
            <div class="form-actions visible">
                <button type="button" class="btn" data-action="close-form" data-route="{{ route('user.pages.index') }}">
                    {{ $t('messages.cancel') ?? 'إلغاء' }}
                </button>
                <button type="submit" class="btn-create-page-submit" id="submitBtn">
                    <i class="fa-solid fa-magic"></i>
                    <span id="submitBtnText">
                        @if(isset($page))
                        {{ $t('messages.update_page') ?? 'تحديث الصفحة' }}
                        @else
                        {{ $t('messages.generate_page_with_ai') ?? 'إنشاء الصفحة بالذكاء الاصطناعي' }}
                        @endif
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/user-pages-form.js') }}"></script>
@endpush
@push('styles')
<!-- Styles included in main.css -->
@endpush
@endsection