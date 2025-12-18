@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $locale === 'ar' ? 'لوحة التحكم' : 'Control Panel' }}</h2>

        <!-- Progress Steps Card -->
        <div class="progress-steps-card">
            <div class="progress-steps-header">
                <i class="fa-solid fa-rocket progress-steps-icon"></i>
                <div class="progress-steps-info">
                    <h3 class="progress-steps-title">{{ $locale === 'ar' ? '3 خطوات لبدء مبيعاتك الأولى' : '3 Steps to Start Your First Sales' }}</h3>
                    <p class="progress-steps-subtitle">{{ $completedSteps ?? 0 }} {{ $locale === 'ar' ? 'من 3 خطوات مكتملة' : 'of 3 steps completed' }}</p>
                </div>
                <div class="progress-percentage">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>{{ round((($completedSteps ?? 0) / 3) * 100) }}%</span>
                </div>
            </div>

            <div class="steps-list">
                <!-- Step 1: Create Landing Page -->
                <div class="step-item {{ $progressSteps['step1']['completed'] ?? false ? 'completed' : '' }}">
                    <div class="step-circle">
                        @if($progressSteps['step1']['completed'] ?? false)
                        <i class="fa-solid fa-check"></i>
                        @endif
                    </div>
                    <div class="step-content">
                        <h4 class="step-title">{{ $progressSteps['step1']['title'] ?? ($locale === 'ar' ? 'إنشاء صفحة هبوط' : 'Create Landing Page') }}</h4>
                        <p class="step-description">{{ $progressSteps['step1']['description'] ?? ($locale === 'ar' ? 'قم بإنشاء أول منتج وصفحة هبوط احترافية باستخدام الذكاء الاصطناعي في ثوان.' : 'Create your first professional product and landing page using AI in seconds.') }}</p>
                        @if(!($progressSteps['step1']['completed'] ?? false))
                        <a href="{{ $progressSteps['step1']['action_url'] ?? route('user.pages.create') }}" class="step-action-btn">
                            <i class="fa-solid fa-square"></i>
                            {{ $progressSteps['step1']['action_text'] ?? ($locale === 'ar' ? 'إنشاء صفحة الآن' : 'Create Page Now') }}
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Step 2: Publish Page -->
                <div class="step-item {{ $progressSteps['step2']['completed'] ?? false ? 'completed' : '' }}">
                    <div class="step-circle">
                        @if($progressSteps['step2']['completed'] ?? false)
                        <i class="fa-solid fa-check"></i>
                        @endif
                    </div>
                    <div class="step-content">
                        <h4 class="step-title">{{ $progressSteps['step2']['title'] ?? ($locale === 'ar' ? 'نشر الصفحة' : 'Publish Page') }}</h4>
                        <p class="step-description">{{ $progressSteps['step2']['description'] ?? ($locale === 'ar' ? 'قم بنشر صفحتك لتصبح متاحة للزوار. انقر على زر "نشر" في قائمة الإجراءات لصفحتك.' : 'Publish your page to make it available to visitors. Click the "Publish" button in your page\'s actions menu.') }}</p>
                    </div>
                </div>

                <!-- Step 3: Upgrade -->
                <div class="step-item {{ $progressSteps['step3']['completed'] ?? false ? 'completed' : '' }}">
                    <div class="step-circle step-circle-upgrade">
                        @if(!($progressSteps['step3']['completed'] ?? false))
                        <i class="fa-solid fa-circle"></i>
                        @else
                        <i class="fa-solid fa-check"></i>
                        @endif
                    </div>
                    <div class="step-content">
                        <h4 class="step-title">{{ $progressSteps['step3']['title'] ?? ($locale === 'ar' ? 'الترقية للاستمرار بلا توقف' : 'Upgrade for Continuous Operation') }}</h4>
                        <p class="step-description">{{ $progressSteps['step3']['description'] ?? ($locale === 'ar' ? 'اشترك في باقة مدفوعة لضمان بقاء صفحاتك منشورة، تفعيل الميزات المتقدمة (Pixel, Webhooks)، وتجنب توقف الخدمة.' : 'Subscribe to a paid plan to ensure your pages remain published, activate advanced features (Pixel, Webhooks), and avoid service interruption.') }}</p>
                        @if(!($progressSteps['step3']['completed'] ?? false))
                        <div class="step-upgrade-actions">
                            <a href="{{ $progressSteps['step3']['action_url'] ?? route('user.packages.index') }}" class="step-upgrade-btn">
                                <i class="fa-solid fa-star"></i>
                                {{ $progressSteps['step3']['action_text'] ?? ($locale === 'ar' ? 'عرض الخطط' : 'View Plans') }}
                            </a>
                            <p class="step-warning">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                {{ $locale === 'ar' ? 'هام لاستمرار عمل الروابط' : 'Important for links to continue working' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Landing Pages Section -->
    <div class="landing-pages-section">
        <div class="landing-pages-header">
            <h2 class="landing-pages-title">
                <i class="fa-solid fa-check landing-pages-icon"></i>
                {{ $locale === 'ar' ? 'صفحات الهبوط' : 'Landing Pages' }}
            </h2>
        </div>

        <!-- Upgrade Banner (for free plan) -->
        @if($isFreePlan ?? true)
        <div class="upgrade-speed-banner">
            <i class="fa-solid fa-bolt upgrade-speed-icon"></i>
            <div class="upgrade-speed-content">
                <p class="upgrade-speed-title">{{ $locale === 'ar' ? 'سلمت الانتظار؟ الخطط المدفوعة تولّد صفحات أسرع بـ 4 مرات' : 'Tired of waiting? Paid plans generate pages 4 times faster' }}</p>
                <span class="upgrade-speed-tag">{{ $locale === 'ar' ? 'النسخة المجانية' : 'Free Version' }}</span>
                <p class="upgrade-speed-desc">{{ $locale === 'ar' ? 'احصل على صفحات عالية الدقة جاهزة في ثوان بدلاً من دقائق، بدون قيود' : 'Get high-quality pages ready in seconds instead of minutes, no restrictions' }}</p>
                <div class="speed-progress">
                    <div class="speed-progress-label">{{ $locale === 'ar' ? 'سرعتك الحالية' : 'Your current speed' }}</div>
                    <div class="speed-progress-bar">
                        <div class="speed-progress-fill"></div>
                    </div>
                    <div class="speed-progress-text">
                        <span>{{ $locale === 'ar' ? 'سرعة غير محدودة -' : 'Unlimited speed -' }}</span>
                        <span class="speed-progress-filled"></span>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="upgrade-conversion-banner">
            <p class="upgrade-conversion-text">{{ $locale === 'ar' ? 'مستخدمو الخطط المدفوعة يحققون معدلات تحويل أعلى بنسبة 45%' : 'Paid plan users achieve higher conversion rates by 45%' }}</p>
            <span class="upgrade-conversion-tag">{{ $locale === 'ar' ? 'النسخة المجانية' : 'Free Version' }}</span>
            <p class="upgrade-conversion-desc">{{ $locale === 'ar' ? 'انضم إلى أكثر من 5,000 تاجر يستمتعون بتصميمات احترافية بدقة عالية ودون حدود - معدل التوصيل الحالي' : 'Join over 5,000 merchants who enjoy high-quality professional designs without limits - Current delivery rate' }}</p>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="landing-pages-actions">
            <a href="{{ route('user.pages.create') }}" class="action-btn action-btn-primary">
                <i class="fa-solid fa-plus"></i>
                {{ $locale === 'ar' ? 'إنشاء صفحة هبوط جديدة' : 'Create new landing page' }}
            </a>
            <button type="button" class="action-btn action-btn-secondary" data-open-store-links-modal>
                <i class="fa-solid fa-link"></i>
                {{ $locale === 'ar' ? 'روابط متاجرك' : 'Your store links' }}
            </button>
            <button type="button" class="action-btn action-btn-secondary" id="open-additional-sales-btn">
                <i class="fa-solid fa-cog"></i>
                <i class="fa-solid fa-th"></i>
                {{ $locale === 'ar' ? 'إعدادات البيع الاضافي' : 'Additional sales settings' }}
            </button>
        </div>

        <!-- Upgrade for Better Results Banner -->
        @if($isFreePlan ?? true)
        <div class="upgrade-results-banner">
            <i class="fa-solid fa-lock upgrade-results-icon"></i>
            <div class="upgrade-results-content">
                <span class="upgrade-results-title">{{ $locale === 'ar' ? 'ترقية لنتائج أفضل' : 'Upgrade for better results' }}</span>
                <span class="upgrade-results-desc">{{ $locale === 'ar' ? 'إمكانيات غير محدودة -' : 'Unlimited possibilities -' }}</span>
            </div>
        </div>
        @endif

        <!-- View Toggle and Filter -->
        <div class="pages-view-controls">
            <div class="view-toggle">
                <button type="button" class="view-toggle-btn" data-view="list" title="{{ $t('messages.list_view') ?? 'عرض القائمة' }}">
                    <i class="fa-solid fa-list"></i>
                </button>
                <button type="button" class="view-toggle-btn active" data-view="grid" title="{{ $t('messages.grid_view') ?? 'عرض الشبكة' }}">
                    <i class="fa-solid fa-th"></i>
                </button>
            </div>
            <div class="filter-section-toggle" id="filterToggle">
                <i class="fa-solid fa-filter"></i>
                <span>{{ $locale === 'ar' ? 'البحث والتصفية المتقدمة' : 'Advanced Search and Filter' }}</span>
                <select name="show_filter" class="filter-show-select">
                    <option>{{ $locale === 'ar' ? 'إظهار' : 'Show' }}</option>
                </select>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>

        <!-- Filter Content (Collapsible) -->
        <div id="filterContent" class="filter-content-collapsible">
            <form method="GET" action="{{ route('user.pages.index') }}" id="filterForm">
                <div class="filter-form-grid">
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-search"></i>
                            {{ $t('messages.search_in_title_description') ?? 'البحث في العنوان والوصف' }}
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $t('messages.search_page_title_description') ?? 'ابحث في عنوان أو وصف الصفحة....' }}" class="form-control">
                    </div>
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-calendar"></i>
                            {{ $t('messages.creation_date_from') ?? 'من تاريخ' }}
                        </label>
                        <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control">
                    </div>
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-calendar"></i>
                            {{ $t('messages.creation_date_to') ?? 'إلى تاريخ' }}
                        </label>
                        <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control">
                    </div>
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-filter"></i>
                            {{ $t('messages.page_status') ?? 'حالة الصفحة' }}
                        </label>
                        <select name="status" class="form-control">
                            <option value="all">{{ $t('messages.all_statuses') ?? 'جميع الحالات' }}</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ $t('messages.draft') ?? 'مسودة' }}</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>{{ $t('messages.published') ?? 'منشورة' }}</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-apply-filters">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $t('messages.apply_filters') ?? 'تطبيق الفلاتر' }}</span>
                    </button>
                    <button type="button" class="btn-clear-filters">
                        <i class="fa-solid fa-rotate"></i>
                        <span>{{ $t('messages.reset') ?? 'إعادة تعيين' }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Pages Content -->
        @if($pages->count() > 0)
        <div class="pages-grid" id="pagesContainer">
            @foreach($pages as $page)
            <div class="page-card-new">
                <!-- Three Dots Menu -->
                <div class="page-card-menu-dots">
                    <button class="page-menu-dots-btn" data-page-id="{{ $page->id }}">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="page-menu-dropdown" id="menu-{{ $page->id }}">
                        @if($page->status !== 'published')
                        <form method="POST" action="{{ route('user.pages.publish', $page) }}" class="menu-item-form publish-form" data-page-id="{{ $page->id }}">
                            @csrf
                            <button type="submit" class="menu-item menu-item-publish">
                                <i class="fa-solid fa-upload"></i>
                                <span>{{ $t('messages.publish') ?? 'نشر' }}</span>
                            </button>
                        </form>
                        @endif
                        <a href="#" class="menu-item" data-action="settings" data-page-id="{{ $page->id }}">
                            <i class="fa-solid fa-cog"></i>
                            <span>{{ $t('messages.page_settings') ?? 'إعدادات الصفحة' }}</span>
                        </a>
                        <a href="#" class="menu-item" data-action="variations" data-page-id="{{ $page->id }}">
                            <i class="fa-solid fa-palette"></i>
                            <span>{{ $t('messages.manage_variations') ?? 'إدارة المقاسات والألوان' }}</span>
                        </a>
                        <a href="#" class="menu-item" data-action="tracking" data-page-id="{{ $page->id }}">
                            <i class="fa-solid fa-pencil"></i>
                            <span>{{ $t('messages.edit_tracking_ids') ?? 'تحرير معرفات التتبع' }}</span>
                        </a>
                        <a href="#" class="menu-item" data-action="link" data-page-id="{{ $page->id }}">
                            <i class="fa-solid fa-link"></i>
                            <span>{{ $t('messages.create_link') ?? 'إنشاء رابط' }}</span>
                        </a>
                        <a href="#" class="menu-item" data-action="recreate" data-page-id="{{ $page->id }}">
                            <i class="fa-solid fa-rotate"></i>
                            <span>{{ $t('messages.recreate_page') ?? 'إعادة إنشاء الصفحة' }}</span>
                        </a>
                        <form method="POST" action="{{ route('user.pages.destroy', $page) }}" class="menu-item-form" data-confirm-message="{{ $t('messages.confirm_delete_page') ?? 'هل أنت متأكد من حذف هذه الصفحة؟' }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="menu-item menu-item-danger">
                                <i class="fa-solid fa-trash"></i>
                                <span>{{ $t('messages.delete_page') ?? 'حذف الصفحة' }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Page Link and Status -->
                <div class="page-card-top-actions" data-page-id="{{ $page->id }}" data-generation-status="{{ $page->generation_status ?? 'completed' }}">
                    @if($page->status === 'published')
                    <a href="{{ route('page.show', $page->id) }}" target="_blank" class="page-link-btn">
                        <i class="fa-solid fa-link"></i>
                        {{ $locale === 'ar' ? 'رابط الصفحة' : 'Page Link' }}
                    </a>
                    <span class="page-status-badge published">{{ $locale === 'ar' ? 'منشورة' : 'Published' }}</span>
                    @else
                    <a href="{{ route('page.show', $page->id) }}" target="_blank" class="page-link-btn preview">
                        <i class="fa-solid fa-eye"></i>
                        {{ $locale === 'ar' ? 'معاينة الصفحة' : 'Preview Page' }}
                    </a>
                    <span class="page-status-badge draft">{{ $locale === 'ar' ? 'مسودة' : 'Draft' }}</span>
                    @endif

                    @if(isset($page->generation_status) && in_array($page->generation_status, ['pending', 'processing']))
                    <div class="generation-status-container">
                        <span class="generation-status-badge {{ $page->generation_status }}">
                            @if($page->generation_status === 'pending')
                            <i class="fa-solid fa-clock"></i>
                            {{ $locale === 'ar' ? 'في الانتظار...' : 'Pending...' }}
                            @elseif($page->generation_status === 'processing')
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            {{ $locale === 'ar' ? 'جاري الإنشاء...' : 'Generating...' }}
                            @endif
                        </span>
                        <div class="generation-progress">
                            <div class="generation-progress-bar"></div>
                        </div>
                    </div>
                    @elseif(isset($page->generation_status) && $page->generation_status === 'failed')
                    <div class="generation-status-container">
                        <span class="generation-status-badge failed">
                            <i class="fa-solid fa-exclamation-triangle"></i>
                            {{ $locale === 'ar' ? 'فشل الإنشاء' : 'Generation Failed' }}
                        </span>
                        @if($page->generation_error)
                        <div class="generation-error">
                            {{ Str::limit($page->generation_error, 50) }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Page Title -->
                <h3 class="page-card-title-new">{{ $page->title ?? '-' }}</h3>

                <!-- Creation Date -->
                <div class="page-card-date-new">
                    {{ $locale === 'ar' ? 'تاريخ الإنشاء' : 'Creation Date' }}: {{ $page->created_at->format('Y/m/d') }}
                </div>

                <!-- Action Buttons -->
                <div class="page-card-actions-new">
                    @if($page->status !== 'published')
                    <form method="POST" action="{{ route('user.pages.publish', $page) }}" class="publish-form" data-page-id="{{ $page->id }}">
                        @csrf
                        <button type="submit" class="page-action-btn page-action-publish">
                            <i class="fa-solid fa-upload"></i>
                            {{ $locale === 'ar' ? 'نشر' : 'Publish' }}
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('user.pages.destroy', $page) }}" class="delete-form" data-confirm-message="{{ $t('messages.confirm_delete_page') ?? 'هل أنت متأكد من حذف هذه الصفحة؟' }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="page-action-btn page-action-delete">
                            <i class="fa-solid fa-trash"></i>
                            {{ $locale === 'ar' ? 'حذف' : 'Delete' }}
                        </button>
                    </form>
                    @if($page->status === 'published')
                    <a href="{{ route('page.show', $page->id) }}" target="_blank" class="page-action-btn page-action-stats">
                        <i class="fa-solid fa-chart-bar"></i>
                        {{ $locale === 'ar' ? 'احصائيات' : 'Statistics' }}
                    </a>
                    @endif
                    @if($page->status !== 'published')
                    <a href="{{ route('user.pages.edit', $page) }}" class="page-action-btn page-action-edit">
                        <i class="fa-solid fa-pencil"></i>
                        {{ $locale === 'ar' ? 'تعديل' : 'Edit' }}
                    </a>
                    @else
                    <button type="button" class="page-action-btn page-action-edit" disabled title="{{ $locale === 'ar' ? 'غير متاح للصفحات المنشورة' : 'Not available for published pages' }}">
                        <i class="fa-solid fa-pencil"></i>
                        {{ $locale === 'ar' ? 'تعديل' : 'Edit' }}
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="pages-empty-state-new">
            <h2 class="empty-state-title-new">
                {{ $locale === 'ar' ? 'لم تقم بإنشاء أي صفحة هبوط بعد، ابدأ بإنشاء أول صفحة الآن! ✨' : 'You haven\'t created any landing page yet, start by creating your first page now! ✨' }}
            </h2>
            <div class="empty-state-actions-new">
                <a href="{{ route('user.pages.create') }}" class="empty-state-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    {{ $locale === 'ar' ? 'إنشاء أول صفحة هبوط' : 'Create first landing page' }}
                </a>
                <a href="#" class="empty-state-btn-secondary">
                    <i class="fa-brands fa-youtube"></i>
                    {{ $locale === 'ar' ? 'شاهد فيديو شرح كيفية استخدام الموقع' : 'Watch video tutorial on how to use the site' }}
                </a>
            </div>
            <p class="empty-state-info">{{ $locale === 'ar' ? 'انضم إلى أكثر من 5,000 تاجر يستخدمون المنصة' : 'Join over 5,000 merchants using the platform' }}</p>
            <div class="empty-state-avatars">
                <div class="empty-state-avatar"></div>
                <div class="empty-state-avatar"></div>
                <div class="empty-state-avatar"></div>
            </div>
        </div>
        @endif
    </div>

    <!-- Page Settings Modal -->
    @include('user.pages.modals.settings')

    <!-- Tracking IDs Modal -->
    @include('user.pages.modals.tracking-ids')

    <!-- Create Link Modal -->
    @include('user.pages.modals.create-link')

    <!-- Store Links Modal -->
    @include('user.pages.modals.store-links')

    <!-- Variations Modal -->
    @include('user.pages.modals.variations')

    <!-- Additional Sales Settings Modal -->
    <div class="modal-overlay" id="additionalSalesModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fa-solid fa-cog"></i>
                    {{ $t('messages.additional_sales_settings') ?? 'إعدادات البيع الاضافي' }}
                </h3>
                <button type="button" class="modal-close" id="close-additional-sales-modal">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div class="modal-content">
                <div class="modal-section">
                    <h4 class="modal-section-title">
                        {{ $t('messages.what_is_upselling') ?? 'ما هو البيع الاضافي (Upselling)؟' }}
                    </h4>
                    <div class="modal-section-content">
                        <p>{{ $t('messages.upselling_description') ?? 'بعد أن يشتري العميل منتجاً من صفحة الهبوط، يقترح النظام عليه منتجات أخرى:' }}</p>
                        <ul>
                            <li>{{ $t('messages.upselling_first') ?? 'أولاً: منتجات مشابهة من متجرك' }}</li>
                            <li>{{ $t('messages.upselling_second') ?? 'ثانياً: إذا لم توجد منتجات مشابهة، منتجات من بائعين آخرين' }}</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-section">
                    <div class="modal-section-toggle">
                        <h4 class="modal-section-title">
                            {{ $t('messages.enable_additional_sales') ?? 'تفعيل البيع الاضافي' }}
                        </h4>
                        <label class="toggle-switch">
                            <input type="checkbox" id="additionalSalesToggle" {{ (isset($additionalSalesEnabled) && $additionalSalesEnabled) ? 'checked' : '' }}>
                            <span class="toggle-slider">
                                <span></span>
                            </span>
                        </label>
                    </div>
                    <p class="modal-section-note">
                        {{ $t('messages.additional_sales_note') ?? 'سيظهر زر "تسوق المزيد" في صفحة الشكر' }}
                    </p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" id="cancel-additional-sales-modal">
                    {{ $t('messages.close') ?? 'إغلاق' }}
                </button>
                <button type="button" class="btn-modal-save" id="save-additional-sales-settings"
                    data-additional-sales-route="{{ route('user.pages.additional-sales') }}">
                    <i class="fa-solid fa-save"></i>
                    {{ $t('messages.save') ?? 'حفظ' }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/user-pages-index.js') }}"></script>
<script src="{{ asset('js/user-pages-modals.js') }}"></script>
<script src="{{ asset('js/user-create-link-modal.js') }}"></script>
<script src="{{ asset('js/admin-common.js') }}"></script>
@endpush

@endsection