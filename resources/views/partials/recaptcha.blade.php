@php
// Get reCAPTCHA settings from AdditionalSettings (admin/global settings)
$recaptchaVersion = \App\Models\AdditionalSetting::getValue('recaptcha_version', 'none');
$recaptchaV2SiteKey = \App\Models\AdditionalSetting::getValue('recaptcha_v2_site_key', '');
$recaptchaV3SiteKey = \App\Models\AdditionalSetting::getValue('recaptcha_v3_site_key', '');
@endphp

@if($recaptchaVersion === 'v2' && $recaptchaV2SiteKey)
<!-- Google reCAPTCHA V2 -->
<div class="recaptcha-wrapper">
    <div class="g-recaptcha" data-sitekey="{{ $recaptchaV2SiteKey }}"></div>
    @error('g-recaptcha-response')
    <span class="auth-error">{{ $message }}</span>
    @enderror
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@if($recaptchaVersion === 'v3' && $recaptchaV3SiteKey)
<!-- Google reCAPTCHA V3 -->
<script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaV3SiteKey }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[method="POST"]');
        if (form) {
            // Create hidden input for token
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'g-recaptcha-response';
            form.appendChild(tokenInput);

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $recaptchaV3SiteKey }}', {
                        action: 'submit'
                    }).then(function(token) {
                        // Set token value
                        tokenInput.value = token;

                        // Submit form
                        form.submit();
                    }).catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        // Submit anyway if reCAPTCHA fails (optional - you can show error instead)
                        form.submit();
                    });
                });
            });
        }
    });
</script>
@endif