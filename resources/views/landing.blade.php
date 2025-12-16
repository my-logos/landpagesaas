<!doctype html>
<html lang="{{ $locale }}" dir="{{ $dir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $settings['seo_title'] ?? ($settings['site_name'] ?? $t('messages.app_name')) . ' - ' . $t('messages.welcome') }}</title>
    <meta name="description" content="{{ $settings['seo_description'] ?? $t('messages.welcome') }}">
    <meta name="keywords" content="{{ $settings['seo_keywords'] ?? '' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 7 - Latest Version with Colors -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/landing-saas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing-home.css') }}">
</head>

<body class="lp-body">
    @php
    $googleTagEnabled = \App\Models\AdditionalSetting::getValue('google_tag_enabled', false);
    $googleTagId = \App\Models\AdditionalSetting::getValue('google_tag_id', '');
    @endphp
    @if($googleTagEnabled && $googleTagId)
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $googleTagId }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif
    <!-- Header -->
    <nav class="lp-topbar-new">
        <div class="container lp-topbar-inner-new">
            @if((isset($siteLogo) && $siteLogo) || (isset($settings['site_logo']) && $settings['site_logo']))
            <img src="{{ asset('' . ($siteLogo ?? $settings['site_logo'])) }}" alt="{{ $siteName ?? $settings['site_name'] ?? $t('messages.app_name') }}" class="lp-logo-img" style="max-height: 40px;" />
            @else
            <div class="lp-logo-new">{{ $siteName ?? $settings['site_name'] ?? $t('messages.app_name') }}</div>
            @endif
            <div class="lp-actions-new">
                @auth
                <a href="{{ ($isAdmin) ? route('admin.dashboard') : route('user.dashboard') }}" class="lp-link-new">{{ $t('messages.dashboard') }}</a>
                <form method="POST" action="{{ route('logout') }}" class="lp-logout-form">
                    @csrf
                    <button type="submit" class="lp-btn-primary-new lp-logout-btn">{{ $t('messages.logout') }}</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="lp-link-new">{{ $t('messages.login') }}</a>
                <a href="{{ route('register.step1') }}" class="lp-btn-primary-new">{{ $t('messages.start_free') }}</a>
                @endauth
                @include('partials.language-switcher')
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="lp-hero-new">
        <div class="lp-hero-background">
            <div class="lp-hero-glow lp-hero-glow-purple"></div>
            <div class="lp-hero-glow lp-hero-glow-green"></div>
            <div class="lp-hero-grid"></div>
        </div>
        <div class="container">
            <div class="lp-hero-inner-new">
                <!-- Right Side: Text and CTA (RTL) -->
                <div class="lp-hero-copy-new">
                    <small class="lp-badge-new">{{ $t('messages.hero_badge') }}</small>
                    <h1 class="lp-title-new">
                        {{ $heroTitlePrefix ?? ($locale === 'ar' ? 'أنشئ ' : 'Create ') }}
                        <span class="lp-animated-title-text" id="animatedTitleText" data-locale="{{ $locale }}" data-words="{{ $heroAnimatedWords ?? ($locale === 'ar' ? 'ساعة ذكية,منتج رقمي,لعبة' : 'smart watch,digital product,game') }}"></span>
                    </h1>
                    <p class="lp-sub-new">
                        {{ $heroSubtitle ?? ($locale === 'ar' ? 'في 30 ثانية ⚡ بالذكاء الاصطناعي. ضاعف أرباحك مع ' . ($siteName ?? 'Sawa') . '. استضافة سريعة، تصميم احترافي بضغطة زر, وحماية متقدمة توفر 100% من ميزانية إعلاناتك المهدرة.' : 'In 30 seconds ⚡ with AI. Double your profits with ' . ($siteName ?? 'Sawa') . '. Fast hosting, professional design with a click of a button, and advanced protection that saves 100% of your wasted advertising budget.') }}
                    </p>

                    <form action="{{ route('register.step1') }}" method="GET" class="lp-hero-input-group">
                        <div class="lp-hero-input-wrapper">
                            <input type="text" id="productNameInput" name="product_name" class="lp-hero-input" placeholder="{{ $heroSearchPlaceholder ?? ($locale === 'ar' ? 'أكتب اسم منتجك:' : 'Type your product name:') }}" />
                            @php
                            $searchWords = $heroSearchAnimatedWords ?? $heroAnimatedWords ?? '';
                            $firstWord = !empty($searchWords) ? explode(',', $searchWords)[0] : ($locale === 'ar' ? 'ساعة ذكية' : 'smart watch');
                            @endphp
                            <span class="lp-animated-product-name" id="animatedProductName" data-locale="{{ $locale }}" data-words="{{ $heroSearchAnimatedWords ?? '' }}">{{ $firstWord }}</span>
                        </div>
                        <button type="submit" class="lp-btn-create-now">
                            <span>{{ $locale === 'ar' ? 'أنشئ الآن' : 'Create Now' }}</span>
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                    </form>

                    <div class="lp-hero-features">
                        <span class="lp-hero-feature-item">
                            <i class="fa-solid fa-bolt"></i> {{ $locale === 'ar' ? 'بدون دفع مقدم' : 'No upfront payment' }}
                        </span>
                        <span class="lp-hero-feature-item">
                            <i class="fa-solid fa-check"></i> {{ $locale === 'ar' ? 'ابدأ مجاناً' : 'Start for free' }}
                        </span>
                        @if(($heroVideoEnabled ?? false) && !empty($heroVideoUrl ?? ''))
                        <a href="{{ $heroVideoUrl }}" target="_blank" class="lp-hero-watch-link">
                            <i class="fa-solid fa-play-circle"></i> {{ $locale === 'ar' ? 'شاهد كيف يعمل' : 'Watch how it works' }}
                        </a>
                        @endif
                    </div>

                    <div class="lp-hero-social-proof">
                        <span class="lp-hero-customers-text">{{ $locale === 'ar' ? 'انضم إلى +3500 تاجر ناجح' : 'Join +3500 successful merchants' }}</span>
                        <div class="lp-hero-avatars">
                            <img src="https://i.pravatar.cc/100?img=3" alt="Customer" class="lp-hero-avatar" />
                            <img src="https://i.pravatar.cc/100?img=4" alt="Customer" class="lp-hero-avatar" />
                            <img src="https://i.pravatar.cc/100?img=5" alt="Customer" class="lp-hero-avatar" />
                        </div>
                    </div>
                </div>

                <!-- Left Side: Animated SVG Illustration or Custom Image -->
                <div class="lp-hero-media-new">
                    <div class="lp-hero-illustration-wrapper">
                        @if(($heroImageEnabled ?? false) && !empty($heroImage ?? ''))
                        <img src="{{ asset($heroImage) }}" alt="Hero Image" class="lp-hero-illustration" />
                        @else
                        <img src="https://sawa.store/static/media/hero-illustration-ai.46e2030b8091affb5eca66e67a54537a.svg" alt="Hero Illustration" class="lp-hero-illustration" />
                        @endif
                    </div>
                </div>
            </div>

            <!-- Supported Platforms Section (at end of Hero) -->
            @if(isset($platforms) && count($platforms) > 0)
            <div class="lp-hero-supported-platforms">
                <h3 class="lp-hero-platforms-title">{{ $platformsTitle ?? ($locale === 'ar' ? 'يدعم التكامل مع 20+ منصة' : 'Supports integration with 20+ platforms') }}</h3>
                <div class="lp-hero-platforms-wrapper">
                    <div class="lp-hero-platforms-list lp-hero-platforms-list-1">
                        @foreach($platforms as $platform)
                        <div class="lp-hero-platform-item">
                            <div class="lp-hero-platform-icon">
                                <i class="{{ $platform['icon'] ?? 'fa-solid fa-link' }}"></i>
                            </div>
                            <span class="lp-hero-platform-name">{{ $platform['name'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                    <!-- Duplicate for seamless loop -->
                    <div class="lp-hero-platforms-list lp-hero-platforms-list-2" aria-hidden="true">
                        @foreach($platforms as $platform)
                        <div class="lp-hero-platform-item">
                            <div class="lp-hero-platform-icon">
                                <i class="{{ $platform['icon'] ?? 'fa-solid fa-link' }}"></i>
                            </div>
                            <span class="lp-hero-platform-name">{{ $platform['name'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </header>

    <!-- Advanced Features Section (We Use Platforms) -->
    <section class="lp-advanced-features lp-section">
        <div class="container">
            <div class="lp-advanced-features-header">
                <div class="lp-advanced-features-title-wrapper">
                    <i class="fa-solid fa-rocket lp-advanced-features-title-icon"></i>
                    <h2 class="lp-advanced-features-title">{{ $advancedFeaturesTitle ?? ($locale === 'ar' ? 'ميزات متقدمة لضمان نجاح أعمالك' : 'Advanced Features to Ensure Your Business Success') }}</h2>
                </div>
                <p class="lp-advanced-features-subtitle">{{ $advancedFeaturesSubtitle ?? ($locale === 'ar' ? 'نحن لا نقدم مجرد صفحات هبوط بل نقدم نظاماً متكاملاً لحماية استثماراتك وزيادة أرباحك بشكل مضمون' : 'We do not just offer landing pages, but rather an integrated system to protect your investments and increase your profits in a guaranteed way') }}</p>
            </div>

            <div class="lp-advanced-features-grid">
                <!-- Card 1: Protection from Duplicate Orders -->
                <div class="lp-advanced-feature-card">
                    <div class="lp-advanced-feature-icon lp-advanced-icon-green">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="lp-advanced-feature-title">{{ $locale === 'ar' ? 'حماية من الطلبات المكررة' : 'Protection from Duplicate Orders' }}</h3>
                    <p class="lp-advanced-feature-desc">{{ $locale === 'ar' ? 'خوارزميات متطورة تمنع الطلبات المكررة بنسبة 100%. لا مزيد من الخسائر المالية أو الطلبات الوهمية. تضمن عدم دفع رسوم شحن إضافية للطلبات المكررة.' : 'Advanced algorithms prevent duplicate orders by 100%. No more financial losses or fake orders. Ensures no additional shipping fees are paid for duplicate orders.' }}</p>
                </div>

                <!-- Card 2: Protection from Bots and Automation -->
                <div class="lp-advanced-feature-card">
                    <div class="lp-advanced-feature-icon lp-advanced-icon-red">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <h3 class="lp-advanced-feature-title">{{ $locale === 'ar' ? 'حماية من البوتات والأتمتة' : 'Protection from Bots and Automation' }}</h3>
                    <p class="lp-advanced-feature-desc">{{ $locale === 'ar' ? 'طبقات حماية متعددة تشمل reCAPTCHA وتقنيات الأمان المتقدمة وتحليل السلوك تمنع البوتات والطلبات المزيفة حماية شاملة لاستثماراتك الإعلانية.' : 'Multiple layers of protection including reCAPTCHA, advanced security techniques, and behavior analysis prevent bots and fake requests, providing comprehensive protection for your advertising investments.' }}</p>
                </div>

                <!-- Card 3: Facebook Conversion API -->
                <div class="lp-advanced-feature-card">
                    <div class="lp-advanced-feature-icon lp-advanced-icon-blue">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="lp-advanced-feature-title">Facebook Conversion API</h3>
                    <p class="lp-advanced-feature-desc">{{ $locale === 'ar' ? 'تكامل احترافي مع Facebook Conversion API يقلل تكلفة الإعلانات بنسبة تصل إلى 100%. تتبع دقيق للتحويلات وتحسين أداء الحملات الإعلانية تلقائياً.' : 'Professional integration with Facebook Conversion API. Reduces advertising costs by up to 100%. Accurate conversion tracking and automatic optimization of advertising campaign performance.' }}</p>
                </div>

                <!-- Card 4: Fast and Responsive Pages -->
                <div class="lp-advanced-feature-card">
                    <div class="lp-advanced-feature-icon lp-advanced-icon-purple">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="lp-advanced-feature-title">{{ $locale === 'ar' ? 'صفحات سريعة ومتجاوبة' : 'Fast and Responsive Pages' }}</h3>
                    <p class="lp-advanced-feature-desc">{{ $locale === 'ar' ? 'صفحات هبوط فائقة السرعة مصممة لجميع الأجهزة. تحميل أقل من ثانيتين على جميع الشبكات. تصميم متجاوب يضمن تجربة مثالية على الهواتف والأجهزة اللوحية.' : 'Ultra-fast landing pages designed for all devices. Loads in less than two seconds on all networks. Responsive design ensures an optimal experience on phones and tablets.' }}</p>
                </div>

                <!-- Card 5: Comprehensive Control Panel -->
                <div class="lp-advanced-feature-card">
                    <div class="lp-advanced-feature-icon lp-advanced-icon-orange">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <h3 class="lp-advanced-feature-title">{{ $locale === 'ar' ? 'لوحة تحكم شاملة' : 'Comprehensive Control Panel' }}</h3>
                    <p class="lp-advanced-feature-desc">{{ $locale === 'ar' ? 'لوحة تحكم متقدمة لإدارة جميع طلباتك وتحليل الأداء. تقارير مفصلة، إحصائيات في الوقت الفعلي، وأدوات تحليل متقدمة لاتخاذ قرارات مدروسة.' : 'Advanced control panel for managing all your orders and performance analysis. Detailed reports, real-time statistics, and advanced analytical tools for informed decision-making.' }}</p>
                </div>

                <!-- Card 6: Webhook Integration -->
                <div class="lp-advanced-feature-card">
                    <div class="lp-advanced-feature-icon lp-advanced-icon-teal">
                        <i class="fa-solid fa-link"></i>
                    </div>
                    <h3 class="lp-advanced-feature-title">{{ $locale === 'ar' ? 'تكامل Webhook' : 'Webhook Integration' }}</h3>
                    <p class="lp-advanced-feature-desc">{{ $locale === 'ar' ? 'ربط مع آلاف التطبيقات الخارجية عبر Webhooks. أتمتة المهام، إرسال الإشعارات. ربط مع أنظمة CRM، وتكامل مع منصات التسويق الإلكتروني بسهولة.' : 'Connect with thousands of external applications via Webhooks. Automate tasks and send notifications. Connect with CRM systems, and easily integrate with e-marketing platforms.' }}</p>
                </div>
            </div>

            <!-- Our Promise Section -->
            <div class="lp-our-promise">
                <div class="lp-our-promise-content">
                    <i class="fa-solid fa-award lp-our-promise-icon-left"></i>
                    <h3 class="lp-our-promise-title">{{ $locale === 'ar' ? 'وعدنا لك' : 'Our Promise to You' }}</h3>
                    <i class="fa-solid fa-award lp-our-promise-icon-right"></i>
                </div>
                <p class="lp-our-promise-desc">{{ $locale === 'ar' ? 'نضمن لك زيادة مبيعاتك وتقليل تكاليف الإعلانات، أو نعيد لك أموالك كاملة خلال 30 يوماً' : 'We guarantee to increase your sales and reduce your advertising costs, or we will refund your money in full within 30 days' }}</p>
            </div>
        </div>
    </section>

    <!-- CTA Banner 1 -->
    <section class="lp-cta-banner lp-cta-green">
        <div class="container">
            <div class="lp-cta-banner-content">
                <p>{{ $t('messages.start_with_us_today') }}</p>
                <a href="{{ route('register.step1') }}" class="lp-btn-green">{{ $t('messages.start_free') }}</a>
            </div>
        </div>
    </section>



    <!-- Basic Services Section -->
    <section class="lp-basic-services lp-section">
        <div class="container">
            <div class="lp-basic-services-header">
                <h2 class="lp-basic-services-title">{{ $basicServicesTitle ?? ($locale === 'ar' ? 'خدماتنا الأساسية' : 'Our Basic Services') }}</h2>
                <p class="lp-basic-services-subtitle">{{ $basicServicesSubtitle ?? ($locale === 'ar' ? 'تقدم لك أدوات متقدمة لإنشاء صفحات هبوط جذابة وفعالة تساعدك على زيادة مبيعاتك' : 'We provide you with advanced tools to create attractive and effective landing pages that help you increase your sales') }}</p>
            </div>
            <div class="lp-basic-services-grid">
                <!-- Card 1: Artificial Intelligence -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-regular fa-star lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'الذكاء الاصطناعي' : 'Artificial Intelligence' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'إنشاء محتوى جذاب باستخدام الذكاء الاصطناعي يناسب منتجك بالضبط. يمكنك تعديل التصميم بالكامل باستخدام أوامر بسيطة بالعربية أو الإنجليزية.' : 'Create engaging content using AI that perfectly suits your product. You can fully customize the design using simple commands in Arabic or English.' }}</p>
                </div>

                <!-- Card 2: Advanced Analytics -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-chart-line lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'تحليلات متقدمة' : 'Advanced Analytics' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'متابعة أداء صفحاتك وتحليل سلوك الزوار لتحسين معدلات التحويل، عرض إحصائيات مفصلة عن الطلبات والمبيعات ونسبة التوصيل.' : 'Monitor your page performance and analyze visitor behavior to improve conversion rates. Display detailed statistics on orders, sales, and delivery rates.' }}</p>
                </div>

                <!-- Card 3: Security and Privacy -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-shield-halved lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'أمان وخصوصية' : 'Security and Privacy' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'حماية كاملة لبياناتك ومعلومات عملاءك مع التوافق مع معايير الخصوصية العالمية تشفير البيانات وتأمين المعاملات.' : 'Complete protection for your data and customer information in compliance with global privacy standards. Data encryption and transaction security.' }}</p>
                </div>

                <!-- Card 4: Comprehensive Order Management -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-box lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'إدارة طلبات شاملة' : 'Comprehensive Order Management' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'نظام متكامل لإدارة الطلبات يتيح لك متابعة حالة الطلبات وإدارة المخزون بكفاءة عالية. تتبع الطلبات المهجورة والمكررة.' : 'An integrated system for order management that allows you to monitor order status and manage inventory with high efficiency. Track abandoned and duplicate orders.' }}</p>
                </div>

                <!-- Card 5: Customer Tracking System -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-book lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'نظام تتبع للعملاء' : 'Customer Tracking System' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'تتبع سلوك العملاء وتفضيلاتهم لتقديم تجربة مخصصة تزيد من فرص التحويل والمبيعات. تحليل مسارات العملاء.' : 'Track customer behavior and preferences to provide a personalized experience that increases conversion opportunities and sales. Analyze customer journeys.' }}</p>
                </div>

                <!-- Card 6: Integrated Technical Support -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-headphones lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'دعم فني متكامل' : 'Integrated Technical Support' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'فريق دعم متخصص على مدار الساعة لمساعدتك في حل المشكلات التقنية وغير التقنية بسرعة وكفاءة. دعم عبر البريد الإلكتروني والدردشة.' : 'A specialized support team available around the clock to help you solve technical and non-technical problems quickly and efficiently. Support via email and chat.' }}</p>
                </div>

                <!-- Card 7: Multi-Product Capability -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-layer-group lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'تعدد المنتجات' : 'Multi-Product Capability' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'إمكانية إضافة وإدارة عدة منتجات في نفس الوقت. ربط المنتجات بصفحات هبوط مختلفة وتتبع أداء كل منتج.' : 'Ability to add and manage multiple products simultaneously. Link products to different landing pages and track each product\'s performance.' }}</p>
                </div>

                <!-- Card 8: Customer Management -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-users lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'إدارة العملاء' : 'Customer Management' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'نظام متكامل لإدارة علاقات العملاء. تتبع تاريخ الطلبات والتفاعلات وتقديم تجربة مخصصة لكل عميل.' : 'An integrated system for customer relationship management. Track order history and interactions, and provide a personalized experience for each customer.' }}</p>
                </div>

                <!-- Card 9: Detailed Reports -->
                <div class="lp-basic-service-card">
                    <div class="lp-basic-service-icon-wrapper">
                        <div class="lp-basic-service-icon-circle">
                            <i class="fa-solid fa-file-chart-column lp-basic-service-icon"></i>
                        </div>
                    </div>
                    <h3 class="lp-basic-service-title">{{ $locale === 'ar' ? 'تقارير مفصلة' : 'Detailed Reports' }}</h3>
                    <p class="lp-basic-service-desc">{{ $locale === 'ar' ? 'تقارير شاملة عن أداء متجرك، تحليل المبيعات والطلبات والتحويلات مع إمكانية تصدير البيانات.' : 'Comprehensive reports on your store\'s performance, sales analysis, orders, and conversions with data export capability.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner 2 -->
    <section class="lp-cta-banner lp-cta-green">
        <div class="container">
            <div class="lp-cta-banner-content">
                <p>{{ $t('messages.start_with_us_today') }}</p>
                <a href="{{ route('register.step1') }}" class="lp-btn-green">{{ $t('messages.start_free') }}</a>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="packages" class="lp-packages lp-section">
        <div class="container">
            <div class="lp-section-head">
                <h2 class="lp-section-title">{{ $t('messages.packages_for_everyone') }}</h2>
            </div>
            <div class="lp-pricing-grid-new">
                @if(isset($packages) && $packages->count() > 0)
                @php
                $packageIndex = 0;
                @endphp
                @foreach($packages as $package)
                @php
                $isPopular = ($packageIndex == 1 && $packages->count() >= 2);
                $packageIndex++;
                @endphp
                <div class="lp-price-card-new {{ $package->is_free ? 'lp-price-free-new' : ($isPopular ? 'lp-price-popular' : 'lp-price-pro-new') }}">
                    @if($isPopular)
                    <div class="lp-popular-badge">
                        <i class="fa-solid fa-star"></i>
                        <span>{{ $locale === 'ar' ? 'الأكثر شعبية' : 'Most Popular' }}</span>
                    </div>
                    @endif

                    <div class="lp-price-header-new">
                        <h3 class="lp-price-title-new">{{ $package->name }}</h3>
                    </div>

                    <div class="lp-price-amount-new">
                        @if($package->is_free)
                        <span class="lp-price-number">0</span>
                        <span class="lp-price-currency">{{ $settings['currency_code'] ?? 'EGP' }}</span>
                        @else
                        <span class="lp-price-number">{{ number_format($package->price_cents, 0) }}</span>
                        <span class="lp-price-currency">{{ $settings['currency_code'] ?? 'EGP' }}</span>
                        <span class="lp-price-period-new">{{ $locale === 'ar' ? 'شهرياً' : '/month' }}</span>
                        @endif
                    </div>

                    <ul class="lp-price-features-new">
                        @if($package->products_limit !== null)
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $package->products_limit }} {{ $locale === 'ar' ? 'منتج' : 'products' }}</span>
                        </li>
                        @else
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $locale === 'ar' ? 'غير محدود' : 'Unlimited' }} {{ $locale === 'ar' ? 'منتجات' : 'products' }}</span>
                        </li>
                        @endif

                        @if($package->pages_limit !== null)
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $package->pages_limit }} {{ $locale === 'ar' ? 'صفحة هبوط' : 'landing pages' }}</span>
                        </li>
                        @else
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $locale === 'ar' ? 'غير محدود' : 'Unlimited' }} {{ $locale === 'ar' ? 'صفحات هبوط' : 'landing pages' }}</span>
                        </li>
                        @endif

                        @if($package->monthly_orders_limit !== null)
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $package->monthly_orders_limit }} {{ $locale === 'ar' ? 'طلب شهرياً' : 'orders/month' }}</span>
                        </li>
                        @else
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $locale === 'ar' ? 'غير محدود' : 'Unlimited' }} {{ $locale === 'ar' ? 'طلبات' : 'orders' }}</span>
                        </li>
                        @endif

                        @if($package->daily_orders_limit !== null)
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $package->daily_orders_limit }} {{ $locale === 'ar' ? 'تعديل يومياً' : 'edits/day' }}</span>
                        </li>
                        @endif

                        @if($package->is_free)
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $locale === 'ar' ? 'دعم عبر البريد الإلكتروني' : 'Email support' }}</span>
                        </li>
                        @else
                        <li>
                            <i class="fa-solid fa-check lp-feature-check"></i>
                            <span>{{ $locale === 'ar' ? 'دعم فني متميز' : 'Premium technical support' }}</span>
                        </li>
                        @endif
                    </ul>

                    <a href="{{ route('register.step1') }}" class="lp-btn-price {{ $isPopular ? 'lp-btn-popular' : ($package->is_free ? 'lp-btn-outline-new' : 'lp-btn-primary-new') }}">
                        {{ $package->is_free ? ($locale === 'ar' ? 'ابدأ مجاناً' : 'Start for free') : ($locale === 'ar' ? 'ابدأ الآن' : 'Start now') }}
                    </a>
                </div>
                @endforeach
                @else
                {{-- Fallback if no packages --}}
                <div class="lp-price-card-new lp-price-free-new">
                    <div class="lp-price-header-new">
                        <h3 class="lp-price-title-new">{{ $locale === 'ar' ? 'الخطة المجانية' : 'Free Plan' }}</h3>
                    </div>
                    <div class="lp-price-amount-new">
                        <span class="lp-price-number">0</span>
                        <span class="lp-price-currency">EGP</span>
                    </div>
                    <ul class="lp-price-features-new">
                        <li><i class="fa-solid fa-check lp-feature-check"></i> <span>{{ $t('messages.plan_free_f1') }}</span></li>
                        <li><i class="fa-solid fa-check lp-feature-check"></i> <span>{{ $t('messages.plan_free_f2') }}</span></li>
                        <li><i class="fa-solid fa-check lp-feature-check"></i> <span>{{ $t('messages.plan_free_f3') }}</span></li>
                    </ul>
                    <a href="{{ route('register.step1') }}" class="lp-btn-price lp-btn-outline-new">{{ $locale === 'ar' ? 'ابدأ مجاناً' : 'Start for free' }}</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Platform Comparison Section -->
        <div class="container">
            <div class="lp-comparison-table-wrapper">
                <table class="lp-comparison-table">
                    <thead>
                        <tr>
                            <th class="lp-comparison-feature-col">{{ $locale === 'ar' ? 'الميزة' : 'Feature' }}</th>
                            <th class="lp-comparison-platform-col lp-comparison-sawa">{{ $siteName ?? 'Sawa' }}</th>
                            <th class="lp-comparison-platform-col">SHOPIFY</th>
                            <th class="lp-comparison-platform-col">WORDPRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="lp-comparison-feature-name">{{ $locale === 'ar' ? 'التكلفة الشهرية' : 'Monthly Cost' }}</td>
                            <td class="lp-comparison-cell lp-comparison-sawa">
                                <span class="lp-comparison-success">{{ $locale === 'ar' ? '0 ج.م (البداية)' : '0 EGP (Starting)' }}</span>
                            </td>
                            <td class="lp-comparison-cell">+$29</td>
                            <td class="lp-comparison-cell">{{ $locale === 'ar' ? '$10 + (استضافة)' : '$10 + (Hosting)' }}</td>
                        </tr>
                        <tr>
                            <td class="lp-comparison-feature-name">{{ $locale === 'ar' ? 'حماية الطلبات الوهمية' : 'Fake Order Protection' }}</td>
                            <td class="lp-comparison-cell lp-comparison-sawa">
                                <i class="fa-solid fa-check lp-comparison-check"></i>
                            </td>
                            <td class="lp-comparison-cell">
                                <i class="fa-solid fa-times lp-comparison-cross"></i>
                                <span class="lp-comparison-note">{{ $locale === 'ar' ? '(تطبيقات مدفوعة)' : '(Paid Apps)' }}</span>
                            </td>
                            <td class="lp-comparison-cell">
                                <i class="fa-solid fa-times lp-comparison-cross"></i>
                            </td>
                        </tr>
                        <tr>
                            <td class="lp-comparison-feature-name">{{ $locale === 'ar' ? 'الإنشاء بالذكاء الاصطناعي' : 'AI-powered Creation' }}</td>
                            <td class="lp-comparison-cell lp-comparison-sawa">
                                <i class="fa-solid fa-check lp-comparison-check"></i>
                            </td>
                            <td class="lp-comparison-cell">
                                <i class="fa-solid fa-times lp-comparison-cross"></i>
                            </td>
                            <td class="lp-comparison-cell">
                                <i class="fa-solid fa-times lp-comparison-cross"></i>
                            </td>
                        </tr>
                        <tr>
                            <td class="lp-comparison-feature-name">{{ $locale === 'ar' ? 'سرعة التحميل (مصر/الخليج)' : 'Download Speed (Egypt/Gulf)' }}</td>
                            <td class="lp-comparison-cell lp-comparison-sawa">
                                <span class="lp-comparison-success">{{ $locale === 'ar' ? 'فائق السرعة' : 'Ultra-fast' }} ⚡</span>
                            </td>
                            <td class="lp-comparison-cell">{{ $locale === 'ar' ? 'متوسط' : 'Average' }}</td>
                            <td class="lp-comparison-cell">{{ $locale === 'ar' ? 'يعتمد على الاستضافة' : 'Depends on Hosting' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Testimonials / Client Logos -->
    <section class="lp-testimonials-logos lp-section">
        <div class="container">
            <div class="lp-section-head">
                <h2 class="lp-section-title">{{ $t('messages.our_customers_speak') }}</h2>
            </div>
            <div class="lp-logos-grid">
                <div class="lp-logo-item">⭐</div>
                <div class="lp-logo-item">⭐</div>
                <div class="lp-logo-item">⭐</div>
                <div class="lp-logo-item">⭐</div>
                <div class="lp-logo-item">⭐</div>
            </div>
        </div>
    </section>


    <!-- Global Success Stories -->
    <section class="lp-success-stories lp-section">
        <div class="container">
            <div class="lp-section-head">
                <h2 class="lp-section-title">{{ $t('messages.global_success_stories') }}</h2>
            </div>
            <div class="lp-stories-grid">
                <div class="lp-story-card">
                    <div class="lp-story-header"></div>
                    <div class="lp-story-avatar">👤</div>
                    <div class="lp-story-name">{{ $locale === 'ar' ? 'سارة أحمد' : 'Sarah Ahmed' }}</div>
                    <div class="lp-story-rating">⭐⭐⭐⭐⭐</div>
                    <p class="lp-story-text">{{ $locale === 'ar' ? '"منصة سهلة وسريعة — زادت مبيعاتي 3x"' : '"Easy and fast platform — increased my sales 3x"' }}</p>
                </div>
                <div class="lp-story-card">
                    <div class="lp-story-header"></div>
                    <div class="lp-story-avatar">👤</div>
                    <div class="lp-story-name">{{ $locale === 'ar' ? 'أحمد محمد' : 'Ahmed Mohamed' }}</div>
                    <div class="lp-story-rating">⭐⭐⭐⭐⭐</div>
                    <p class="lp-story-text">{{ $locale === 'ar' ? '"دعم رائع وتصاميم احترافية."' : '"Great support and professional designs."' }}</p>
                </div>
                <div class="lp-story-card">
                    <div class="lp-story-header"></div>
                    <div class="lp-story-avatar">👤</div>
                    <div class="lp-story-name">{{ $locale === 'ar' ? 'ليلى علي' : 'Layla Ali' }}</div>
                    <div class="lp-story-rating">⭐⭐⭐⭐⭐</div>
                    <p class="lp-story-text">{{ $locale === 'ar' ? '"التحويلات تحسنت بفضل صفحات الهبوط."' : '"Conversions improved thanks to landing pages."' }}</p>
                </div>
                <div class="lp-story-card">
                    <div class="lp-story-header"></div>
                    <div class="lp-story-avatar">👤</div>
                    <div class="lp-story-name">{{ $locale === 'ar' ? 'محمد خالد' : 'Mohamed Khaled' }}</div>
                    <div class="lp-story-rating">⭐⭐⭐⭐⭐</div>
                    <p class="lp-story-text">{{ $locale === 'ar' ? '"أفضل منصة للتجارة الإلكترونية"' : '"Best e-commerce platform"' }}</p>
                </div>
                <div class="lp-story-card">
                    <div class="lp-story-header"></div>
                    <div class="lp-story-avatar">👤</div>
                    <div class="lp-story-name">{{ $locale === 'ar' ? 'فاطمة حسن' : 'Fatima Hassan' }}</div>
                    <div class="lp-story-rating">⭐⭐⭐⭐⭐</div>
                    <p class="lp-story-text">{{ $locale === 'ar' ? '"خدمة ممتازة ونتائج مذهلة"' : '"Excellent service and amazing results"' }}</p>
                </div>
            </div>
        </div>
    </section>


    <!-- FAQ Section -->
    <section id="faq" class="lp-faq lp-section">
        <div class="container">
            <div class="lp-faq-header">
                <h2 class="lp-faq-title">{{ $faqTitle ?? ($locale === 'ar' ? 'الأسئلة الشائعة' : 'Frequently Asked Questions') }}</h2>
                <p class="lp-faq-subtitle">{{ $faqSubtitle ?? ($locale === 'ar' ? 'إجابات على الأسئلة الأكثر شيوعًا حول منصتنا وخدماتنا' : 'Answers to the most common questions about our platform and services') }}</p>
            </div>
            <div class="lp-faq-list">
                <div class="lp-faq-item">
                    <div class="lp-faq-question">
                        <strong>{{ $locale === 'ar' ? 'ما هي منصة ' . ($siteName ?? 'Sawa') . '؟' : 'What is the ' . ($siteName ?? 'Sawa') . ' platform?' }}</strong>
                        <span class="lp-faq-icon">+</span>
                    </div>
                    <div class="lp-faq-answer">
                        <p>{{ $locale === 'ar' ? 'منصة ' . ($siteName ?? 'Sawa') . ' هي منصة متكاملة لإنشاء صفحات الهبوط الاحترافية باستخدام الذكاء الاصطناعي. تمكنك من إنشاء صفحات هبوط جذابة وفعالة في دقائق معدودة دون الحاجة لمعرفة تقنية متقدمة.' : ($siteName ?? 'Sawa') . ' is an integrated platform for creating professional landing pages using artificial intelligence. It enables you to create attractive and effective landing pages in minutes without the need for advanced technical knowledge.' }}</p>
                    </div>
                </div>
                <div class="lp-faq-item">
                    <div class="lp-faq-question">
                        <strong>{{ $locale === 'ar' ? 'كيف يمكنني البدء باستخدام المنصة؟' : 'How can I start using the platform?' }}</strong>
                        <span class="lp-faq-icon">+</span>
                    </div>
                    <div class="lp-faq-answer">
                        <p>{{ $locale === 'ar' ? 'يمكنك البدء مجاناً عن طريق إنشاء حساب جديد. بعد التسجيل، ستحصل على خطط مجانية تسمح لك بإنشاء صفحات هبوط محدودة. يمكنك الترقية لاحقاً للحصول على ميزات أكثر.' : 'You can start for free by creating a new account. After registration, you will get free plans that allow you to create limited landing pages. You can upgrade later to get more features.' }}</p>
                    </div>
                </div>
                <div class="lp-faq-item">
                    <div class="lp-faq-question">
                        <strong>{{ $locale === 'ar' ? 'ما هي الخطط المتاحة وأسعارها؟' : 'What are the available plans and their prices?' }}</strong>
                        <span class="lp-faq-icon">+</span>
                    </div>
                    <div class="lp-faq-answer">
                        <p>{{ $locale === 'ar' ? 'نوفر عدة خطط تناسب مختلف الاحتياجات. تبدأ من خطة مجانية تسمح لك بتجربة المنصة، وخطط مدفوعة توفر ميزات متقدمة مثل عدد صفحات غير محدود، تحليلات متقدمة، ودعم فني متميز.' : 'We offer several plans that suit different needs. Starting from a free plan that allows you to try the platform, and paid plans that provide advanced features such as unlimited pages, advanced analytics, and premium technical support.' }}</p>
                    </div>
                </div>
                <div class="lp-faq-item">
                    <div class="lp-faq-question">
                        <strong>{{ $locale === 'ar' ? 'هل يمكنني تخصيص تصميم صفحات الهبوط؟' : 'Can I customize the design of landing pages?' }}</strong>
                        <span class="lp-faq-icon">+</span>
                    </div>
                    <div class="lp-faq-answer">
                        <p>{{ $locale === 'ar' ? 'نعم بالطبع! توفر المنصة أدوات قوية لتخصيص التصميم بالكامل. يمكنك تخصيص الألوان، الخطوط، الصور، التخطيط، وأكثر من ذلك. كما يمكنك استخدام الذكاء الاصطناعي لإنشاء محتوى مناسب لمنتجك تلقائياً.' : 'Yes, of course! The platform provides powerful tools to fully customize the design. You can customize colors, fonts, images, layout, and more. You can also use artificial intelligence to automatically create content suitable for your product.' }}</p>
                    </div>
                </div>
                <div class="lp-faq-item">
                    <div class="lp-faq-question">
                        <strong>{{ $locale === 'ar' ? 'كيف يمكنني متابعة أداء متجري وصفحات الهبوط؟' : 'How can I track the performance of my store and landing pages?' }}</strong>
                        <span class="lp-faq-icon">+</span>
                    </div>
                    <div class="lp-faq-answer">
                        <p>{{ $locale === 'ar' ? 'توفر المنصة لوحة تحكم شاملة تمكنك من متابعة جميع الإحصائيات المهمة. يمكنك رؤية عدد الزوار، معدلات التحويل، الطلبات، المبيعات، وأكثر من ذلك. كما يمكنك ربط صفحتك مع Google Analytics و Facebook Pixel لمتابعة أكثر تفصيلاً.' : 'The platform provides a comprehensive dashboard that enables you to track all important statistics. You can see the number of visitors, conversion rates, orders, sales, and more. You can also link your page with Google Analytics and Facebook Pixel for more detailed tracking.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer id="contact" class="lp-footer">
        <div class="container">
            <div class="lp-footer-inner">
                <div class="lp-footer-left">
                    @if((isset($siteLogo) && $siteLogo) || (isset($settings['site_logo']) && $settings['site_logo']))
                    <img src="{{ asset('' . ($siteLogo ?? $settings['site_logo'])) }}" alt="{{ $siteName ?? $settings['site_name'] ?? $t('messages.app_name') }}" class="lp-footer-logo-img" style="max-height: 40px;" />
                    @else
                    <div class="lp-footer-logo">{{ $siteName ?? $settings['site_name'] ?? $t('messages.app_name') }}</div>
                    @endif
                    <p>&copy; {{ date('Y') }} {{ $t('messages.app_name') }}. {{ $t('messages.footer_rights') }}</p>
                </div>


                <div class="lp-footer-social">
                    @if(isset($socialMediaLinks) && count($socialMediaLinks) > 0)
                    @foreach($socialMediaLinks as $social)
                    @php
                    $iconMap = [
                    'facebook' => 'fa-brands fa-facebook',
                    'twitter' => 'fa-brands fa-twitter',
                    'instagram' => 'fa-brands fa-instagram',
                    'linkedin' => 'fa-brands fa-linkedin',
                    'youtube' => 'fa-brands fa-youtube',
                    'tiktok' => 'fa-brands fa-tiktok',
                    'snapchat' => 'fa-brands fa-snapchat',
                    ];
                    $icon = $iconMap[$social['platform'] ?? ''] ?? 'fa-solid fa-link';
                    @endphp
                    <a href="{{ $social['url'] ?? '#' }}" target="_blank" class="lp-social-icon" rel="noopener noreferrer">
                        <i class="{{ $icon }}"></i>
                    </a>
                    @endforeach
                    @else
                    <a href="#" class="lp-social-icon"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="lp-social-icon"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="lp-social-icon"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="lp-social-icon"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#" class="lp-social-icon"><i class="fa-brands fa-youtube"></i></a>
                    @endif
                </div>
            </div>
    </footer>

    @include('partials.pixels')

    <script src="{{ asset('js/landing-saas.js') }}"></script>
    <script src="{{ asset('js/landing-home.js') }}"></script>
</body>

</html>