/**
 * Admin Subscriptions JavaScript
 * Handles subscriptions management functionality
 */
/* eslint-env browser */
/* global document, window, confirm */
(function () {
    'use strict';

    class AdminSubscriptions {
        constructor() {
            this.init();
        }

        init() {
            this.initReceiptImageClicks();
            this.initConfirmationHandlers();
        }

        /**
         * Initialize receipt image click handlers
         */
        initReceiptImageClicks() {
            const receiptImages = document.querySelectorAll('.transfer-receipt-image');
            receiptImages.forEach(img => {
                img.addEventListener('click', () => {
                    window.open(img.src, '_blank');
                });
            });
        }

        /**
         * Initialize confirmation handlers for approve/reject actions
         */
        initConfirmationHandlers() {
            // Handle approve subscription buttons
            const approveButtons = document.querySelectorAll('form[action*="/approve"] button[type="submit"]');
            approveButtons.forEach(button => {
                if (button.dataset.confirmHandler === 'true') {
                    return;
                }

                button.addEventListener('click', (e) => {
                    const form = button.closest('form');
                    if (form) {
                        const action = form.action;
                        let confirmMessage = 'Are you sure?';

                        if (action.includes('/approve')) {
                            if (action.includes('/subscriptions')) {
                                confirmMessage = button.dataset.confirmApproveSubscription ||
                                    'Are you sure you want to approve this subscription?';
                            } else if (action.includes('/approve-wallet')) {
                                confirmMessage = button.dataset.confirmApprovePayment ||
                                    'Are you sure you want to approve this payment?';
                            }
                        } else if (action.includes('/reject')) {
                            if (action.includes('/subscriptions')) {
                                confirmMessage = button.dataset.confirmRejectSubscription ||
                                    'Are you sure you want to reject this subscription?';
                            } else if (action.includes('/reject-wallet')) {
                                confirmMessage = button.dataset.confirmRejectPayment ||
                                    'Are you sure you want to reject this payment?';
                            }
                        }

                        if (!confirm(confirmMessage)) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });

                button.dataset.confirmHandler = 'true';
            });

            // Handle reject buttons
            const rejectButtons = document.querySelectorAll('form[action*="/reject"] button[type="submit"]');
            rejectButtons.forEach(button => {
                if (button.dataset.confirmHandler === 'true') {
                    return;
                }

                button.addEventListener('click', (e) => {
                    const form = button.closest('form');
                    if (form) {
                        const action = form.action;
                        let confirmMessage = 'Are you sure?';

                        if (action.includes('/subscriptions')) {
                            confirmMessage = button.dataset.confirmRejectSubscription ||
                                'Are you sure you want to reject this subscription?';
                        } else if (action.includes('/reject-wallet')) {
                            confirmMessage = button.dataset.confirmRejectPayment ||
                                'Are you sure you want to reject this payment?';
                        }

                        if (!confirm(confirmMessage)) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });

                button.dataset.confirmHandler = 'true';
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new AdminSubscriptions();
        });
    } else {
        new AdminSubscriptions();
    }
})();

