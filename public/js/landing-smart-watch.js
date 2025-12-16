/**
 * Smart Watch Landing Page - JavaScript
 * Countdown Timer, FAQ Accordion, Smooth Scroll, Form Validation
 */

(function () {
    'use strict';

    // ===== Countdown Timer =====
    function initCountdown() {
        const countdownBoxes = document.querySelectorAll('.countdown-box');
        if (countdownBoxes.length === 0) return;

        // Get countdown duration from data attribute or default to 48 hours
        const countdownContainer = document.querySelector('.countdown');
        const totalHours = countdownContainer ? parseInt(countdownContainer.dataset.hours || '48') : 48;

        // Start countdown from total hours (fake countdown)
        let hours = totalHours;
        let minutes = 0;
        let seconds = 0;

        function updateCountdown() {
            // Update boxes (hours, minutes, seconds)
            if (countdownBoxes[0]) countdownBoxes[0].textContent = String(hours).padStart(2, '0');
            if (countdownBoxes[1]) countdownBoxes[1].textContent = String(minutes).padStart(2, '0');
            if (countdownBoxes[2]) countdownBoxes[2].textContent = String(seconds).padStart(2, '0');
            // Hide 4th box if exists (for backward compatibility)
            if (countdownBoxes[3]) countdownBoxes[3].style.display = 'none';

            // Decrease countdown
            if (seconds > 0) {
                seconds--;
            } else if (minutes > 0) {
                minutes--;
                seconds = 59;
            } else if (hours > 0) {
                hours--;
                minutes = 59;
                seconds = 59;
            } else {
                // Countdown finished - reset to 00:00:00
                hours = 0;
                minutes = 0;
                seconds = 0;
            }
        }

        // Update immediately
        updateCountdown();
        // Update every second
        setInterval(updateCountdown, 1000);
    }

    // ===== FAQ Accordion =====
    function initFAQ() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            if (!question) return;

            // Create answer element if it doesn't exist
            let answer = item.querySelector('.faq-answer');
            if (!answer) {
                answer = document.createElement('div');
                answer.className = 'faq-answer';
                answer.textContent = 'إجابة السؤال ستظهر هنا...'; // Placeholder
                item.appendChild(answer);
            }

            question.addEventListener('click', function () {
                const isActive = item.classList.contains('active');

                // Close all other items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });

                // Toggle current item
                item.classList.toggle('active', !isActive);
            });
        });
    }

    // ===== Smooth Scroll =====
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');

        links.forEach(link => {
            link.addEventListener('click', function (e) {
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

    // ===== Form Validation & Submission =====
    function initForm() {
        const form = document.getElementById('orderForm');
        if (!form) return;

        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoading = document.getElementById('submitLoading');

        form.addEventListener('submit', function (e) {
            // Prevent double submission
            if (submitBtn.disabled) {
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
            submitBtn.disabled = true;
            if (submitText) submitText.style.display = 'none';
            if (submitLoading) submitLoading.style.display = 'inline-flex';

            // Form will submit normally
            return true;
        });
    }

    // ===== Scroll Animations =====
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });

        // Observe testimonial cards
        document.querySelectorAll('.testimonial-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });
    }

    // ===== Initialize on DOM Ready =====
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                initCountdown();
                initFAQ();
                initSmoothScroll();
                initForm();
                initScrollAnimations();
            });
        } else {
            initCountdown();
            initFAQ();
            initSmoothScroll();
            initForm();
            initScrollAnimations();
        }
    }

    init();
})();
