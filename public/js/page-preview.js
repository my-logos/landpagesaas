/**
 * Page Preview Controller
 * Handles preview modal, iframe controls, and responsive preview
 */
/* eslint-env browser */
(function () {
    'use strict';

    class PagePreview {
        constructor(config) {
            // Get config from data attributes or window or provided config
            const container = document.querySelector('.preview-frame-container');
            if (container && !config) {
                config = {
                    modalId: container.dataset.modalId || 'previewNotificationModal',
                    iframeId: container.dataset.iframeId || 'previewIframe',
                    backRoute: container.dataset.backRoute || '/user/pages',
                    dontShowKey: container.dataset.dontShowKey || 'dontShowPreviewNotification'
                };
            }
            this.config = config || window.pagePreviewConfig || {};
            this.modal = document.getElementById(this.config.modalId || 'previewNotificationModal');
            this.iframe = document.getElementById(this.config.iframeId || 'previewIframe');
            this.dontShowAgain = document.getElementById('dontShowAgain');
            this.backBtn = document.getElementById('backFromPreview');
            this.continueBtn = document.getElementById('continuePreview');
            this.refreshBtn = document.querySelector('[data-action="refresh"]');
            this.fullscreenBtn = document.querySelector('[data-action="fullscreen"]');
            this.sizeSelect = document.getElementById('previewSize');

            this.init();
        }

        init() {
            this.checkModalVisibility();
            this.attachEventListeners();
        }

        checkModalVisibility() {
            const dontShowKey = this.config.dontShowKey || 'dontShowPreviewNotification';
            if (!localStorage.getItem(dontShowKey) && this.modal) {
                this.modal.classList.add('active');
            }
        }

        attachEventListeners() {
            // Don't show again checkbox
            if (this.dontShowAgain) {
                this.dontShowAgain.addEventListener('change', () => this.handleDontShowAgain());
            }

            // Back button
            if (this.backBtn) {
                this.backBtn.addEventListener('click', () => this.handleBack());
            }

            // Continue button
            if (this.continueBtn) {
                this.continueBtn.addEventListener('click', () => this.handleContinue());
            }

            // Refresh iframe
            if (this.refreshBtn) {
                this.refreshBtn.addEventListener('click', () => this.handleRefresh());
            }

            // Fullscreen
            if (this.fullscreenBtn) {
                this.fullscreenBtn.addEventListener('click', () => this.handleFullscreen());
            }

            // Size selector
            if (this.sizeSelect) {
                this.sizeSelect.addEventListener('change', (e) => this.handleSizeChange(e.target.value));
            }

            // Close modal on overlay click
            if (this.modal) {
                this.modal.addEventListener('click', (e) => {
                    if (e.target === this.modal) {
                        this.closeModal();
                    }
                });
            }
        }

        handleDontShowAgain() {
            const dontShowKey = this.config.dontShowKey || 'dontShowPreviewNotification';
            if (this.dontShowAgain.checked) {
                localStorage.setItem(dontShowKey, 'true');
            } else {
                localStorage.removeItem(dontShowKey);
            }
        }

        handleBack() {
            const backRoute = this.config.backRoute || '/user/pages';
            window.location.href = backRoute;
        }

        handleContinue() {
            this.closeModal();
        }

        handleRefresh() {
            if (this.iframe) {
                const currentSrc = this.iframe.src;
                this.iframe.src = '';
                setTimeout(() => {
                    this.iframe.src = currentSrc;
                }, 10);
            }
        }

        handleFullscreen() {
            if (!this.iframe) return;

            if (this.iframe.requestFullscreen) {
                this.iframe.requestFullscreen();
            } else if (this.iframe.webkitRequestFullscreen) {
                this.iframe.webkitRequestFullscreen();
            } else if (this.iframe.msRequestFullscreen) {
                this.iframe.msRequestFullscreen();
            }
        }

        handleSizeChange(size) {
            if (!this.iframe) return;

            this.iframe.classList.remove('mobile', 'tablet', 'desktop');
            if (size !== 'desktop') {
                this.iframe.classList.add(size);
            }
        }

        closeModal() {
            if (this.modal) {
                this.modal.classList.remove('active');
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new PagePreview();
        });
    } else {
        new PagePreview();
    }

    // Set config from window if available
    if (window.pagePreviewConfig) {
        new PagePreview(window.pagePreviewConfig);
    }
})();
