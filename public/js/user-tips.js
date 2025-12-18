document.addEventListener('DOMContentLoaded', function () {
    const tipsModal = document.getElementById('tipsModal');
    const closeTipsModal = document.getElementById('closeTipsModal');
    const closeTipsModalBtn = document.getElementById('closeTipsModalBtn');
    const dontShowAgainCheckbox = document.getElementById('dontShowTipsAgain');
    const tipsPrevBtn = document.getElementById('tipsPrevBtn');
    const tipsNextBtn = document.getElementById('tipsNextBtn');
    const stepIndicators = document.querySelectorAll('.tips-step-indicator-item');
    const stepContentWrappers = document.querySelectorAll('.tips-step-content-wrapper');

    if (!tipsModal) {
        return;
    }

    // Check if tips should be shown (not disabled in localStorage)
    const tipsDisabled = localStorage.getItem('tipsDisabled') === 'true';
    if (tipsDisabled) {
        tipsModal.classList.add('hidden');
        return;
    }

    // Current step (1-based)
    let currentStep = 1;
    const totalSteps = 3;

    // Show modal on page load
    tipsModal.classList.remove('hidden');

    // Update step display
    function updateStep(step) {
        currentStep = step;

        // Update step content wrappers
        stepContentWrappers.forEach((wrapper, index) => {
            if (index + 1 === step) {
                wrapper.classList.add('active');
            } else {
                wrapper.classList.remove('active');
            }
        });

        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            if (index + 1 === step) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });

        // Update navigation buttons
        if (tipsPrevBtn) {
            if (step === 1) {
                tipsPrevBtn.style.display = 'none';
            } else {
                tipsPrevBtn.style.display = 'flex';
            }
        }

        if (tipsNextBtn) {
            const nextText = tipsNextBtn.getAttribute('data-next-text') || 'Next';
            const closeText = tipsNextBtn.getAttribute('data-close-text') || 'Close';
            const nextSpan = tipsNextBtn.querySelector('span');

            if (step === totalSteps) {
                if (nextSpan) {
                    nextSpan.textContent = closeText;
                }
            } else {
                if (nextSpan) {
                    nextSpan.textContent = nextText;
                }
            }
        }

        // Handle images carousel for step 3
        if (step === 3) {
            initImagesCarousel();
        }
    }

    // Initialize images carousel for step 3
    function initImagesCarousel() {
        const imagesWrapper = document.querySelector('.tips-step-images-wrapper');
        if (!imagesWrapper) return;

        const images = imagesWrapper.querySelectorAll('.tips-step-image');
        const dots = document.querySelectorAll('.tips-image-dot');
        const prevBtn = document.getElementById('tipsImagePrev');
        const nextBtn = document.getElementById('tipsImageNext');

        let currentImageIndex = 0;

        function showImage(index) {
            images.forEach((img, i) => {
                img.classList.toggle('active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            currentImageIndex = index;

            if (prevBtn) {
                prevBtn.disabled = index === 0;
            }
            if (nextBtn) {
                nextBtn.disabled = index === images.length - 1;
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentImageIndex > 0) {
                    showImage(currentImageIndex - 1);
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (currentImageIndex < images.length - 1) {
                    showImage(currentImageIndex + 1);
                }
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showImage(index);
            });
        });

        // Initialize
        showImage(0);
    }

    // Step indicator click
    stepIndicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            updateStep(index + 1);
        });
    });

    // Navigation buttons
    if (tipsPrevBtn) {
        tipsPrevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                updateStep(currentStep - 1);
            }
        });
    }

    if (tipsNextBtn) {
        tipsNextBtn.addEventListener('click', function () {
            if (currentStep < totalSteps) {
                updateStep(currentStep + 1);
            } else {
                closeModal();
            }
        });
    }

    // Close modal function
    function closeModal() {
        tipsModal.classList.add('hidden');

        // If checkbox is checked, save preference
        if (dontShowAgainCheckbox && dontShowAgainCheckbox.checked) {
            localStorage.setItem('tipsDisabled', 'true');
            // Also save to server
            saveTipsPreference(true);
        }
    }

    // Close modal events
    if (closeTipsModal) {
        closeTipsModal.addEventListener('click', closeModal);
    }

    if (closeTipsModalBtn) {
        closeTipsModalBtn.addEventListener('click', closeModal);
    }

    // Close on overlay click
    tipsModal.addEventListener('click', function (e) {
        if (e.target === tipsModal) {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !tipsModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Arrow keys navigation
    document.addEventListener('keydown', function (e) {
        if (tipsModal.classList.contains('hidden')) return;

        if (e.key === 'ArrowLeft' && currentStep > 1) {
            updateStep(currentStep - 1);
        } else if (e.key === 'ArrowRight' && currentStep < totalSteps) {
            updateStep(currentStep + 1);
        }
    });

    // Save tips preference to server
    function saveTipsPreference(disabled) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch('/user/tips/disable', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                disabled: disabled
            })
        }).catch(error => {
            console.error('Failed to save tips preference:', error);
        });
    }

    // Initialize first step
    updateStep(1);
});
