@include('landing-templates.partials.head')

<body data-google-analytics-id="{{ $page->google_analytics_id ?? '' }}"
    data-facebook-pixel="{{ $page->facebook_pixel ?? '' }}"
    data-tiktok-pixel="{{ $page->tiktok_pixel ?? '' }}"
    data-snapchat-pixel="{{ $page->snapchat_pixel ?? '' }}">

    @include('landing-templates.partials.page-features')

    <!-- Hero Section -->
    @php
    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
    $heroDescription = $aiContent['description'] ?? ($page->product->description ?? '');
    @endphp
    <section class="hero">
        <div class="container">
            <h1>{{ $page->title }}</h1>
            <p>{{ $heroDescription }}</p>
        </div>
    </section>

    <!-- Product Information -->
    @if($page->product)
    <section class="product-info">
        <div class="container">
            <h2>{{ $page->product->name }}</h2>
            @if($mainImage)
            <img src="{{ $mainImage }}" alt="{{ $page->product->name }}" class="product-image">
            @endif
            @php
            $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
            $productDescription = $aiContent['description'] ?? $page->product->description ?? '';
            @endphp
            <p>{{ $productDescription }}</p>
            <p>
                @if($smartCouponsEnabled)
                <strong>
                    <span class="old-price-inline">{{ number_format($increasedPrice, 2) }}</span>
                    <span class="new-price-inline">{{ number_format($finalPrice, 2) }}</span>
                    {{ $page->product->currency ?? 'EGP' }}
                </strong>
                @else
                <strong>Price: {{ number_format($originalPrice, 2) }} {{ $page->product->currency ?? 'EGP' }}</strong>
                @endif
            </p>
        </div>
    </section>
    @endif

    <!-- Features Section -->
    @php
    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
    $features = $aiContent['features'] ?? [];
    @endphp
    @if(!empty($features))
    @php
    // Limit features to maximum 6 items (3 per row, max 2 rows)
    $limitedFeatures = array_slice($features, 0, 6);
    @endphp
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">{{ $pageLanguage === 'ar' ? 'مميزات المنتج' : 'Product Features' }}</h2>
            <div class="features-grid">
                @foreach($limitedFeatures as $feature)
                <div class="feature-card">
                    @if(isset($feature['icon']))
                    <div class="feature-icon">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    @endif
                    <h3 class="feature-title">{{ $feature['title'] ?? '' }}</h3>
                    <p class="feature-description">{{ $feature['description'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ Section -->
    @php
    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
    $faqs = $aiContent['faqs'] ?? [];
    @endphp
    @if(!empty($faqs))
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title">{{ $pageLanguage === 'ar' ? 'أسئلة متكررة' : 'Frequently Asked Questions' }}</h2>
            <div class="faq-list">
                @foreach($faqs as $faq)
                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ $faq['question'] ?? '' }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>{{ $faq['answer'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Order Form -->
    @include('landing-templates.partials.order-form')
</body>

</html>