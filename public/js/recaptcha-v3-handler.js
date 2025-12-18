/**
 * Google reCAPTCHA V3 Handler
 * Handles reCAPTCHA V3 token generation and form submission
 */
(function () {
    'use strict';

    function initRecaptchaV3() {
        // Check if reCAPTCHA V3 is needed
        const recaptchaV3Script = document.querySelector('script[src*="recaptcha/api.js?render="]');
        if (!recaptchaV3Script) {
            return; // reCAPTCHA V3 not loaded
        }

        // Extract site key from script src
        const scriptSrc = recaptchaV3Script.getAttribute('src');
        const siteKeyMatch = scriptSrc.match(/render=([^&]+)/);
        if (!siteKeyMatch || !siteKeyMatch[1]) {
            return;
        }

        const siteKey = siteKeyMatch[1];

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setupRecaptchaV3(siteKey);
            });
        } else {
            setupRecaptchaV3(siteKey);
        }
    }

    function setupRecaptchaV3(siteKey) {
        // Find form that contains reCAPTCHA wrapper or is in the same container
        const recaptchaWrapper = document.querySelector('.recaptcha-wrapper');
        let form = null;

        if (recaptchaWrapper) {
            // Find the closest form
            form = recaptchaWrapper.closest('form');
        }

        // Fallback: find any POST form
        if (!form) {
            form = document.querySelector('form[method="POST"]');
        }

        if (!form) {
            return;
        }

        // Create hidden input for token if it doesn't exist
        let tokenInput = form.querySelector('input[name="g-recaptcha-response"]');
        if (!tokenInput) {
            tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'g-recaptcha-response';
            form.appendChild(tokenInput);
        }

        // Handle form submission
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (typeof grecaptcha === 'undefined') {
                console.error('reCAPTCHA not loaded');
                form.submit();
                return;
            }

            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, {
                    action: 'submit'
                }).then(function (token) {
                    // Set token value
                    tokenInput.value = token;

                    // Submit form
                    form.submit();
                }).catch(function (error) {
                    console.error('reCAPTCHA error:', error);
                    // Submit anyway if reCAPTCHA fails (optional - you can show error instead)
                    form.submit();
                });
            });
        });
    }

    // Initialize
    initRecaptchaV3();
})();
