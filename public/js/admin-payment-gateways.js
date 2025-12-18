/**
 * Admin Payment Gateways JavaScript
 * Handles gateway field loading and form interactions
 */
/* eslint-env browser */
/* global document, fetch, console */
(function () {
    'use strict';

    class PaymentGatewayForm {
        constructor() {
            this.codeSelect = document.getElementById('code');
            this.nameField = document.getElementById('name');
            this.credentialsDiv = document.getElementById('credentialsFields');
            this.getFieldsRoute = null;

            this.init();
        }

        init() {
            if (!this.codeSelect || !this.credentialsDiv) {
                return;
            }

            // Get route from data attribute or use default
            const form = document.getElementById('gatewayForm');
            if (form) {
                this.getFieldsRoute = form.dataset.getFieldsRoute || '/admin/payment-gateways/get-fields';
            }

            // Attach event listener
            this.codeSelect.addEventListener('change', () => this.loadGatewayFields());

            // Initialize if code is already selected
            if (this.codeSelect.value) {
                this.loadGatewayFields();
            }
        }

        loadGatewayFields() {
            const code = this.codeSelect.value;

            if (!code) {
                this.credentialsDiv.innerHTML = '';
                return;
            }

            // Set name from selected option
            const selectedOption = this.codeSelect.querySelector(`option[value="${code}"]`);
            if (selectedOption && this.nameField && !this.nameField.value) {
                this.nameField.value = selectedOption.textContent.trim();
            }

            // Show loading
            this.credentialsDiv.innerHTML = '<p class="loading-text">Loading...</p>';

            // Load fields via AJAX
            const url = `${this.getFieldsRoute}?code=${encodeURIComponent(code)}`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    this.renderCredentialsFields(data);
                })
                .catch(error => {
                    console.error('Error loading fields:', error);
                    this.showError(error.message);
                });
        }

        renderCredentialsFields(data) {
            let html = '';

            if (data.success !== false && data.fields && Array.isArray(data.fields) && data.fields.length > 0) {
                data.fields.forEach(field => {
                    html += `
                        <div class="form-group">
                            <label for="credentials_${field}" class="form-label">${this.escapeHtml(field)}</label>
                            <input type="text" id="credentials_${field}" name="credentials[${field}]" class="form-control" value="" />
                        </div>
                    `;
                });
            } else {
                html = '<p class="text-muted">' + (data.message || 'No credentials required for this gateway') + '</p>';
            }

            this.credentialsDiv.innerHTML = html;
        }

        showError(message) {
            this.credentialsDiv.innerHTML = `<p class="text-danger">Error loading fields: ${this.escapeHtml(message)}</p>`;
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new PaymentGatewayForm();
        });
    } else {
        new PaymentGatewayForm();
    }
})();
