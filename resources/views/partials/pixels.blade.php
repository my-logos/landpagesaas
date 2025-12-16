<!-- Tracking Pixels - Landing Page -->
@php
// Get Facebook Conversion API settings from FacebookConversionAPISetting (system default for landing page)
$fbConversionAPISettings = \App\Models\FacebookConversionAPISetting::getSystemDefault();
$facebookConversionAPIEnabled = $fbConversionAPISettings && $fbConversionAPISettings->is_enabled ? true : false;
$facebookConversionAPIPixelId = $fbConversionAPISettings && $fbConversionAPISettings->pixel_id ? $fbConversionAPISettings->pixel_id : '';
$facebookConversionAPIAccessToken = $fbConversionAPISettings && $fbConversionAPISettings->access_token ? $fbConversionAPISettings->access_token : '';

// Get pixel settings from AdditionalSettings (admin/global settings)
// Only show Facebook Pixel if Facebook Conversion API is NOT enabled
$facebookPixelEnabled = \App\Models\AdditionalSetting::getValue('facebook_pixel_enabled', false);
$facebookPixelId = \App\Models\AdditionalSetting::getValue('facebook_pixel_id', '');
// If Conversion API is enabled, disable regular pixel
if ($facebookConversionAPIEnabled && $facebookConversionAPIPixelId) {
$facebookPixelEnabled = false;
}
$googleAnalyticsEnabled = \App\Models\AdditionalSetting::getValue('google_analytics_enabled', false);
$googleAnalyticsId = \App\Models\AdditionalSetting::getValue('google_analytics_id', '');
$tiktokPixelEnabled = \App\Models\AdditionalSetting::getValue('tiktok_pixel_enabled', false);
$tiktokPixelId = \App\Models\AdditionalSetting::getValue('tiktok_pixel_id', '');
$snapchatPixelEnabled = \App\Models\AdditionalSetting::getValue('snapchat_pixel_enabled', false);
$snapchatPixelId = \App\Models\AdditionalSetting::getValue('snapchat_pixel_id', '');
$googleTagEnabled = \App\Models\AdditionalSetting::getValue('google_tag_enabled', false);
$googleTagId = \App\Models\AdditionalSetting::getValue('google_tag_id', '');
@endphp

@if($facebookPixelEnabled && $facebookPixelId)
<!-- Facebook Pixel Code -->
<script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $facebookPixelId }}');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->
@endif

@if($googleAnalyticsEnabled && $googleAnalyticsId)
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '{{ $googleAnalyticsId }}');
</script>
<!-- End Google Analytics -->
@endif

@if($googleTagEnabled && $googleTagId)
<!-- Google Tag Manager -->
<script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '{{ $googleTagId }}');
</script>
<!-- End Google Tag Manager -->
@endif

@if($tiktokPixelEnabled && $tiktokPixelId)
<!-- TikTok Pixel Code -->
<script>
    ! function(w, d, t) {
        w.TiktokAnalyticsObject = t;
        var ttq = w[t] = w[t] || [];
        ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "enableCookie", "disableCookie"], ttq.setAndDefer = function(t, e) {
            t[e] = function() {
                t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
            }
        };
        for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
        ttq.instance = function(t) {
            for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
            return e
        }, ttq.load = function(e, n) {
            var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
            ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = i, ttq._t = ttq._t || {}, ttq._t[e] = +new Date, ttq._o = ttq._o || {}, ttq._o[e] = n || {};
            var o = document.createElement("script");
            o.type = "text/javascript", o.async = !0, o.src = i + "?sdkid=" + e + "&lib=" + t;
            var a = document.getElementsByTagName("script")[0];
            a.parentNode.insertBefore(o, a)
        };

        ttq.load('{{ $tiktokPixelId }}');
        ttq.page();
    }(window, document, 'ttq');
</script>
<!-- End TikTok Pixel Code -->
@endif

@if($snapchatPixelEnabled && $snapchatPixelId)
<!-- Snap Pixel Code -->
<script type='text/javascript'>
    (function(e, t, n) {
        if (e.snaptr) return;
        var a = e.snaptr = function() {
            a.handleRequest ? a.handleRequest.apply(a, arguments) : a.queue.push(arguments)
        };
        a.queue = [];
        var s = 'script';
        r = t.createElement(s);
        r.async = !0;
        r.src = n;
        var u = t.getElementsByTagName(s)[0];
        u.parentNode.insertBefore(r, u);
    })(window, document,
        'https://sc-static.net/scevent.min.js');

    snaptr('init', '{{ $snapchatPixelId }}', {
        'user_email': ''
    });
    snaptr('track', 'PAGE_VIEW');
</script>
<!-- End Snap Pixel Code -->
@endif

@if($facebookConversionAPIEnabled && $facebookConversionAPIPixelId && $facebookConversionAPIAccessToken)
<!-- Facebook Conversion API -->
<!-- Facebook Pixel Code for Conversion API (Hybrid Tracking) -->
<script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $facebookConversionAPIPixelId }}');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $facebookConversionAPIPixelId }}&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Conversion API Pixel Code -->
<!-- Note: Purchase events are sent server-side via Facebook Conversion API -->
@endif