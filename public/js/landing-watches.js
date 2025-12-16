/**
 * Decoration & Furniture Landing Page - JavaScript
 * FAQ Accordion, Smooth Scroll, Form Validation, Gallery Lightbox, Scroll Animations
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

    // ===== Gallery Lightbox =====
    function initGallery() {
        const galleryItems = document.querySelectorAll('.gallery-item');

        galleryItems.forEach(item => {
            item.addEventListener('click', function () {
                const img = this.querySelector('img');
                if (!img) return;

                // Create lightbox
                const lightbox = document.createElement('div');
                lightbox.className = 'gallery-lightbox';
                lightbox.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    cursor: pointer;
                    animation: fadeIn 0.3s ease-out;
                `;

                const lightboxImg = document.createElement('img');
                lightboxImg.src = img.src;
                lightboxImg.style.cssText = `
                    max-width: 90%;
                    max-height: 90%;
                    object-fit: contain;
                    border-radius: 8px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
                `;

                const closeBtn = document.createElement('button');
                closeBtn.innerHTML = '&times;';
                closeBtn.style.cssText = `
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: white;
                    font-size: 2rem;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    cursor: pointer;
                    transition: background 0.3s;
                `;

                closeBtn.addEventListener('mouseenter', function () {
                    this.style.background = 'rgba(255, 255, 255, 0.3)';
                });

                closeBtn.addEventListener('mouseleave', function () {
                    this.style.background = 'rgba(255, 255, 255, 0.2)';
                });

                lightbox.appendChild(lightboxImg);
                lightbox.appendChild(closeBtn);
                document.body.appendChild(lightbox);

                // Close on click
                const closeLightbox = function () {
                    lightbox.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        document.body.removeChild(lightbox);
                    }, 300);
                };

                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox || e.target === closeBtn) {
                        closeLightbox();
                    }
                });

                // Close on ESC key
                const handleEsc = function (e) {
                    if (e.key === 'Escape') {
                        closeLightbox();
                        document.removeEventListener('keydown', handleEsc);
                    }
                };
                document.addEventListener('keydown', handleEsc);
            });
        });

        // Add fade animations
        if (!document.querySelector('#gallery-animations')) {
            const style = document.createElement('style');
            style.id = 'gallery-animations';
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
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
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });

        // Observe testimonial cards
        document.querySelectorAll('.testimonial-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });

        // Observe why choose cards
        document.querySelectorAll('.why-choose-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });

        // Observe gallery items
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.9)';
            item.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(item);
        });

        // Observe details items
        document.querySelectorAll('.details-item').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(item);
        });
    }

    // ===== Parallax Effect for Hero Section =====
    function initParallax() {
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return;

        window.addEventListener('scroll', function () {
            const scrolled = window.pageYOffset;
            const heroImage = heroSection.querySelector('.hero-image');

            if (heroImage && scrolled < heroSection.offsetHeight) {
                heroImage.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    }

    // ===== Initialize on DOM Ready =====
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                initFAQ();
                initSmoothScroll();
                initForm();
                initGallery();
                initScrollAnimations();
                initParallax();
            });
        } else {
            initCountdown();
            initFAQ();
            initSmoothScroll();
            initForm();
            initGallery();
            initScrollAnimations();
            initParallax();
        }
    }

    init();
})();
