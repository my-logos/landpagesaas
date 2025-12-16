/**
 * User Create Link Modal JavaScript
 * Handles link creation and copy functionality
 */
/* eslint-env browser */
/* global document, fetch, console, navigator, alert */
(function () {
    'use strict';

    class CreateLinkModal {
        constructor() {
            this.saveBtn = document.getElementById('save-create-link');
            this.closeBtn = document.getElementById('close-create-link-modal');
            this.cancelBtn = document.getElementById('cancel-create-link-modal');
            this.createLinkForm = document.getElementById('createLinkForm');
            this.linksList = document.getElementById('linksList');
            this.currentLinksSection = document.getElementById('currentLinksSection');
            this.modal = document.getElementById('createLinkModal');

            this.init();
        }

        init() {
            if (!this.saveBtn) {
                return;
            }

            this.saveBtn.addEventListener('click', () => this.handleSave());
            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', () => this.closeModal());
            }
            if (this.cancelBtn) {
                this.cancelBtn.addEventListener('click', () => this.closeModal());
            }

            // Handle copy link buttons (delegated event listener)
            if (this.linksList) {
                this.linksList.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-copy-link')) {
                        this.handleCopyLink(e.target.closest('.btn-copy-link'));
                    }
                });
            }
        }

        handleSave() {
            const pageId = this.saveBtn.dataset.pageId;
            if (!pageId) {
                alert(this.getTranslation('messages.page_not_found', 'الصفحة غير موجودة'));
                return;
            }

            const storeName = document.getElementById('store_name')?.value;
            const pageName = document.getElementById('page_name')?.value;

            if (!storeName || !pageName) {
                alert(this.getTranslation('messages.please_fill_all_fields', 'يرجى ملء جميع الحقول'));
                return;
            }

            // Validate naming requirements
            const nameRegex = /^[a-zA-Z0-9-]+$/;
            if (storeName.length < 4 || !nameRegex.test(storeName)) {
                alert(this.getTranslation('messages.invalid_store_name', 'اسم المتجر غير صحيح'));
                return;
            }
            if (pageName.length < 4 || !nameRegex.test(pageName)) {
                alert(this.getTranslation('messages.invalid_page_name', 'اسم صفحة الهبوط غير صحيح'));
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            fetch(`/user/pages/${pageId}/link`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    store_name: storeName,
                    page_name: pageName,
                }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.displayLinks(data.links);
                        alert(data.message);
                    } else {
                        alert(data.message || 'حدث خطأ في إنشاء الرابط');
                    }
                })
                .catch(err => {
                    console.error('Error creating link:', err);
                    alert('حدث خطأ في إنشاء الرابط');
                });
        }

        displayLinks(links) {
            if (!this.currentLinksSection || !this.linksList) {
                return;
            }

            this.currentLinksSection.classList.add('show');
            this.linksList.innerHTML = '';

            if (links.default) {
                this.createLinkItem(links.default);
            }
            if (links.custom) {
                this.createLinkItem(links.custom);
            }
        }

        createLinkItem(url) {
            const linkDiv = document.createElement('div');
            linkDiv.className = 'link-item';
            linkDiv.innerHTML = `
                <span class="link-url">${this.escapeHtml(url)}</span>
                <button type="button" class="btn-copy-link" data-url="${this.escapeHtml(url)}">
                    <i class="fa-solid fa-copy"></i> ${this.getTranslation('messages.copy', 'نسخ')}
                </button>
            `;
            this.linksList.appendChild(linkDiv);
        }

        handleCopyLink(button) {
            const url = button.dataset.url;
            if (!url) {
                return;
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url)
                    .then(() => {
                        alert(this.getTranslation('messages.copied', 'تم النسخ'));
                    })
                    .catch(err => {
                        console.error('Failed to copy:', err);
                        this.fallbackCopyTextToClipboard(url);
                    });
            } else {
                this.fallbackCopyTextToClipboard(url);
            }
        }

        fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                alert(this.getTranslation('messages.copied', 'تم النسخ'));
            } catch (err) {
                console.error('Fallback copy failed:', err);
            }

            document.body.removeChild(textArea);
        }

        closeModal() {
            if (this.modal) {
                this.modal.classList.remove('show');
            }
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        getTranslation(key, fallback) {
            // Try to get from data attribute or use fallback
            const element = document.querySelector(`[data-translation-${key}]`);
            return element ? element.dataset[`translation${key.replace(/\./g, '')}`] : fallback;
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new CreateLinkModal();
        });
    } else {
        new CreateLinkModal();
    }
})();





