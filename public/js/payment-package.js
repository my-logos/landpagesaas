/**
 * Payment Package JavaScript
 * Handles wallet balance usage and payment amount calculation
 */
/* eslint-env browser */
/* global document */
(function () {
    'use strict';

    class PaymentPackage {
        constructor() {
            this.paymentMethodSelect = document.getElementById('payment_method');
            this.bankTransferInfoGroup = document.getElementById('bank_transfer_info_group');
            this.transferReceiptGroup = document.getElementById('transfer_receipt_group');
            this.transferReceiptInput = document.getElementById('transfer_receipt');
            this.transferReceiptPreview = document.getElementById('transfer_receipt_preview');
            this.transferReceiptPreviewImg = document.getElementById('transfer_receipt_preview_img');
            this.paymentForm = document.querySelector('.payment-form');

            this.init();
        }

        init() {
            // Handle payment method change (for bank transfer)
            if (this.paymentMethodSelect) {
                this.paymentMethodSelect.addEventListener('change', () => this.handlePaymentMethodChange());
            }

            // Handle transfer receipt preview
            if (this.transferReceiptInput) {
                this.transferReceiptInput.addEventListener('change', (e) => this.handleReceiptPreview(e));
            }

            // Add form validation for bank transfer
            if (this.paymentForm) {
                this.paymentForm.addEventListener('submit', (e) => {
                    const selectedMethod = this.paymentMethodSelect ? this.paymentMethodSelect.value : '';
                    const isBankTransfer = selectedMethod === 'bank_transfer';

                    if (isBankTransfer && (!this.transferReceiptInput || !this.transferReceiptInput.files || this.transferReceiptInput.files.length === 0)) {
                        e.preventDefault();
                        alert('يرجى رفع صورة إيصال التحويل');
                        if (this.transferReceiptInput) {
                            this.transferReceiptInput.focus();
                        }
                        return false;
                    }
                });
            }
        }

        handlePaymentMethodChange() {
            if (!this.paymentMethodSelect) {
                return;
            }

            const selectedMethod = this.paymentMethodSelect.value;
            const isBankTransfer = selectedMethod === 'bank_transfer';

            // Show/hide bank transfer info and receipt field
            if (this.bankTransferInfoGroup) {
                if (isBankTransfer) {
                    this.bankTransferInfoGroup.classList.add('active');
                } else {
                    this.bankTransferInfoGroup.classList.remove('active');
                }
            }

            // Make transfer receipt required only for bank transfer
            if (this.transferReceiptInput) {
                this.transferReceiptInput.required = isBankTransfer;
                if (!isBankTransfer) {
                    this.transferReceiptInput.value = '';
                    if (this.transferReceiptPreview) {
                        this.transferReceiptPreview.classList.remove('active');
                    }
                }
            }
        }

        handleReceiptPreview(event) {
            if (!event.target.files || !event.target.files[0]) {
                return;
            }

            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = (e) => {
                if (this.transferReceiptPreviewImg) {
                    this.transferReceiptPreviewImg.src = e.target.result;
                }
                if (this.transferReceiptPreview) {
                    this.transferReceiptPreview.classList.add('active');
                }
            };

            reader.readAsDataURL(file);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new PaymentPackage();
        });
    } else {
        new PaymentPackage();
    }
})();
