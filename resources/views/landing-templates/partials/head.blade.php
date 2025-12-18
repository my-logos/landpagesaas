@php
// Get language from page content, fallback to current locale
$pageLanguage = app()->getLocale();
if ($page->content) {
$contentData = is_string($page->content) ? json_decode($page->content, true) : $page->content;
if (is_array($contentData) && isset($contentData['language'])) {
$pageLanguage = $contentData['language'];
}
}
$pageDir = $pageLanguage === 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLanguage }}" dir="{{ $pageDir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO Meta Tags -->
    <title>{{ $page->seo_title ?? $page->title ?? 'Landing Page' }}</title>
    <meta name="description" content="{{ $page->seo_description ?? ($page->content ?? ($page->product->description ?? '')) }}">
    @if($page->seo_keywords)
    <meta name="keywords" content="{{ $page->seo_keywords }}">
    @endif
    <meta name="author" content="{{ $page->user->name ?? 'DropSaas' }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $page->seo_title ?? $page->title ?? 'Landing Page' }}">
    <meta property="og:description" content="{{ $page->seo_description ?? ($page->content ?? ($page->product->description ?? '')) }}">
    @if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:locale" content="{{ $pageLanguage === 'ar' ? 'ar_EG' : 'en_US' }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $page->seo_title ?? $page->title ?? 'Landing Page' }}">
    <meta name="twitter:description" content="{{ $page->seo_description ?? ($page->content ?? ($page->product->description ?? '')) }}">
    @if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
        {
            !!json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!
        }
    </script>

    <!-- Additional Fonts (optional, can be set by template) -->
    @if(isset($additionalFonts) && $additionalFonts)
    {!! $additionalFonts !!}
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">

    <!-- Template CSS (default to landing-default.css if not specified) -->
    <link rel="stylesheet" href="{{ asset($templateCss ?? 'landpage/css/landing-default.css') }}">

    @if($page->google_analytics_id || $page->facebook_pixel || $page->tiktok_pixel || $page->snapchat_pixel)
    <script src="{{ asset('js/landing-page-tracking.js') }}"></script>
    @endif

    <!-- Landing Pages Unified JavaScript -->
    <script src="{{ asset('landpage/js/landing-pages.js') }}"></script>
</head>