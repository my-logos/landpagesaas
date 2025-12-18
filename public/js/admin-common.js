/**
 * Admin Common JavaScript
 * Handles common admin functionality like delete confirmations
 */
/* eslint-env browser */
(function () {
    'use strict';

    class AdminCommon {
        constructor() {
            this.init();
        }

        init() {
            this.initDeleteConfirmations();
            this.initFilterToggle();
        }

        /**
         * Initialize delete confirmation handlers
         */
        initDeleteConfirmations() {
            // Find all forms with delete action
            const deleteForms = document.querySelectorAll('form[action*="/destroy"], form[action*="/delete"]');

            deleteForms.forEach(form => {
                // Skip if already has handler
                if (form.dataset.deleteHandler === 'true') {
                    return;
                }

                form.addEventListener('submit', (e) => {
                    const confirmMessage = form.dataset.confirmMessage ||
                        form.querySelector('[data-confirm-message]')?.dataset.confirmMessage ||
                        form.querySelector('[data-confirm]')?.dataset.confirm ||
                        'Are you sure you want to delete this item?';

                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                        return false;
                    }
                });

                form.dataset.deleteHandler = 'true';
            });

            // Also handle buttons with data-confirm-message
            const deleteButtons = document.querySelectorAll('button[data-confirm-message], button[type="submit"][data-confirm-message]');
            deleteButtons.forEach(button => {
                if (button.dataset.confirmHandler === 'true') {
                    return;
                }

                button.addEventListener('click', (e) => {
                    const form = button.closest('form');
                    if (form) {
                        const confirmMessage = button.dataset.confirmMessage ||
                            form.dataset.confirmMessage ||
                            'Are you sure you want to delete this item?';

                        if (!confirm(confirmMessage)) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });

                button.dataset.confirmHandler = 'true';
            });
        }

        /**
         * Initialize filter toggle functionality
         */
        initFilterToggle() {
            const filterToggle = document.getElementById('filterToggle');
            const filterContent = document.getElementById('filterContent');
            const filterChevron = document.getElementById('filterChevron');

            if (filterToggle && filterContent && filterChevron) {
                filterToggle.addEventListener('click', () => {
                    const isCollapsed = filterContent.classList.contains('collapsed');

                    if (isCollapsed) {
                        filterContent.classList.remove('collapsed');
                        filterContent.classList.add('show');
                        filterChevron.classList.add('rotated');
                    } else {
                        filterContent.classList.add('collapsed');
                        filterContent.classList.remove('show');
                        filterChevron.classList.remove('rotated');
                    }
                });
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new AdminCommon();
        });
    } else {
        new AdminCommon();
    }
})();
