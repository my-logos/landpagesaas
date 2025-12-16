/**
 * Default Landing Page - JavaScript
 * Form Validation, Smooth Scroll, Scroll Animations
 */

(function() {
    'use strict';

    // ===== Form Validation & Submission =====
    function initForm() {
        const form = document.getElementById('orderForm');
        if (!form) return;

        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoading = document.getElementById('submitLoading');

        form.addEventListener('submit', function(e) {
            // Prevent double submission
            if (submitBtn && submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Validate form
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return false;
            }

            // Show loading state
            if (submitBtn) submitBtn.disabled = true;
            if (submitText) submitText.style.display = 'none';
            if (submitLoading) submitLoading.style.display = 'inline-flex';

            // Form will submit normally
            return true;
        });
    }

    // ===== Smooth Scroll =====
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#' || !href) return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ===== Scroll Animations =====
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe sections
        document.querySelectorAll('.product-info, .order-form').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';
            section.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
            observer.observe(section);
        });
    }

    // ===== Initialize on DOM Ready =====
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initForm();
                initSmoothScroll();
                initScrollAnimations();
            });
        } else {
            initForm();
            initSmoothScroll();
            initScrollAnimations();
        }
    }

    init();
})();
