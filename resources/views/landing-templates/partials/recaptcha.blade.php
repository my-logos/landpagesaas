@php
// Get reCAPTCHA settings from AdditionalSettings (admin/global settings)
$recaptchaVersion = \App\Models\AdditionalSetting::getValue('recaptcha_version', 'none');
$recaptchaV2SiteKey = \App\Models\AdditionalSetting::getValue('recaptcha_v2_site_key', '');
$recaptchaV3SiteKey = \App\Models\AdditionalSetting::getValue('recaptcha_v3_site_key', '');

// Enable V2 if version is v2 or if V2 site key exists (allows both to work together)
$recaptchaV2Enabled = ($recaptchaVersion === 'v2' || !empty($recaptchaV2SiteKey)) && !empty($recaptchaV2SiteKey);

// Enable V3 if version is v3 or if V3 site key exists (allows both to work together)
$recaptchaV3Enabled = ($recaptchaVersion === 'v3' || !empty($recaptchaV3SiteKey)) && !empty($recaptchaV3SiteKey);
@endphp

@if($recaptchaV2Enabled)
<!-- Google reCAPTCHA V2 -->
<div class="recaptcha-wrapper">
    <div class="g-recaptcha" data-sitekey="{{ $recaptchaV2SiteKey }}"></div>
    @error('g-recaptcha-response')
    <span class="error-message">{{ $message }}</span>
    @enderror
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@if($recaptchaV3Enabled)
<!-- Google reCAPTCHA V3 -->
<script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaV3SiteKey }}"></script>
<script src="{{ asset('js/recaptcha-v3-handler.js') }}"></script>
@endif