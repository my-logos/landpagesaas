/**
 * User Packages JavaScript
 * Handles package details modal and progress bars
 */
/* eslint-env browser */
/* global document */
(function () {
    'use strict';

    class UserPackages {
        constructor() {
            this.modal = document.getElementById('packageDetailsModal');
            this.openBtn = document.querySelector('.btn-view-details');
            this.closeBtn = document.querySelector('.modal-close');
            this.closeBtnFooter = document.querySelector('.modal-footer .btn');

            this.init();
        }

        init() {
            if (this.openBtn) {
                this.openBtn.addEventListener('click', () => this.openModal());
            }

            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', () => this.closeModal());
            }

            if (this.closeBtnFooter) {
                this.closeBtnFooter.addEventListener('click', () => this.closeModal());
            }

            // Close modal when clicking outside
            if (this.modal) {
                this.modal.addEventListener('click', (e) => {
                    if (e.target === this.modal) {
                        this.closeModal();
                    }
                });
            }

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal && this.modal.classList.contains('show')) {
                    this.closeModal();
                }
            });

            // Initialize progress bars
            this.initProgressBars();
        }

        openModal() {
            if (this.modal) {
                this.modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        closeModal() {
            if (this.modal) {
                this.modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        initProgressBars() {
            // Initialize progress bars from data-width attributes
            const progressBars = document.querySelectorAll('.progress-bar[data-width]');
            progressBars.forEach(bar => {
                const width = parseFloat(bar.dataset.width);
                if (!isNaN(width) && width >= 0 && width <= 100) {
                    bar.style.width = `${width}%`;
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new UserPackages();
        });
    } else {
        new UserPackages();
    }
})();










