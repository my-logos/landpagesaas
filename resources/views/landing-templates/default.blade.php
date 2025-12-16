@include('landing-templates.partials.head')

<body data-google-analytics-id="{{ $page->google_analytics_id ?? '' }}"
    data-facebook-pixel="{{ $page->facebook_pixel ?? '' }}"
    data-tiktok-pixel="{{ $page->tiktok_pixel ?? '' }}"
    data-snapchat-pixel="{{ $page->snapchat_pixel ?? '' }}">

    @include('landing-templates.partials.page-features')

    <!-- Hero Section -->
    @php
    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
    $heroDescription = $aiContent['description'] ?? ($page->product->description ?? $page->content ?? '');
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
                    <span style="text-decoration: line-through; color: #999;">{{ number_format($increasedPrice, 2) }}</span>
                    <span style="color: #667eea; font-size: 1.2em;">{{ number_format($finalPrice, 2) }}</span>
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
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">{{ $pageLanguage === 'ar' ? 'مميزات المنتج' : 'Product Features' }}</h2>
            <div class="features-grid">
                @foreach($features as $feature)
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

    <script src="{{ asset('js/landing-default.js') }}"></script>
    <script>
        // FAQ Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(function(item) {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                const icon = question.querySelector('i');

                if (question && answer) {
                    question.addEventListener('click', function() {
                        const isOpen = item.classList.contains('active');

                        // Close all other items
                        faqItems.forEach(function(otherItem) {
                            if (otherItem !== item) {
                                otherItem.classList.remove('active');
                                const otherIcon = otherItem.querySelector('.faq-question i');
                                if (otherIcon) {
                                    otherIcon.style.transform = 'rotate(0deg)';
                                }
                            }
                        });

                        // Toggle current item
                        if (isOpen) {
                            item.classList.remove('active');
                            if (icon) {
                                icon.style.transform = 'rotate(0deg)';
                            }
                        } else {
                            item.classList.add('active');
                            if (icon) {
                                icon.style.transform = 'rotate(180deg)';
                            }
                        }
                    });
                }
            });
        });
    </script>
    <style>
        /* FAQ Styles */
        .faq-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }

        .faq-list {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .faq-question:hover {
            background-color: #f0f0f0;
        }

        .faq-question i {
            transition: transform 0.3s;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
        }

        .faq-answer p {
            padding: 0 20px 20px 20px;
            margin: 0;
            color: #666;
            line-height: 1.6;
        }

        /* Features Styles */
        .features-section {
            padding: 60px 0;
        }

        .section-title {
            text-align: center;
            font-size: 2em;
            margin-bottom: 40px;
            color: #333;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.3em;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 1.5em;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>

</html>