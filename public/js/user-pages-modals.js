/**
 * User Pages Modals JavaScript
 * Handles page settings modal and tracking IDs modal
 */
/* eslint-env browser */
(function () {
    'use strict';

    // Page Settings Modal
    function initPageSettingsModal() {
        const fixedOrderButtonToggle = document.getElementById('fixedOrderButtonToggle');
        const fixedOrderButtonSettings = document.getElementById('fixedOrderButtonSettings');
        const saveBtn = document.getElementById('save-page-settings');
        const closeBtn = document.getElementById('close-page-settings-modal');
        const cancelBtn = document.getElementById('cancel-page-settings-modal');

        // Fixed Order Button Toggle Settings
        if (fixedOrderButtonToggle && fixedOrderButtonSettings) {
            fixedOrderButtonToggle.addEventListener('change', function () {
                if (this.checked) {
                    fixedOrderButtonSettings.classList.add('show');
                } else {
                    fixedOrderButtonSettings.classList.remove('show');
                }
            });
        }

        // Save Page Settings
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                const pageId = this.dataset.pageId;
                if (!pageId) {
                    const pageNotFoundMsg = this.dataset.pageNotFoundMsg || 'Page not found';
                    alert(pageNotFoundMsg);
                    return;
                }

                // Disable button during save
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (this.dataset.savingText || 'Saving...');

                const data = {
                    smart_assistant_enabled: document.getElementById('smartAssistantToggle')?.checked || false,
                    fixed_order_button_enabled: document.getElementById('fixedOrderButtonToggle')?.checked || false,
                    fixed_order_button_text: document.getElementById('buttonText')?.value?.trim() || '',
                    fixed_order_button_position: document.getElementById('buttonPosition')?.value || 'bottom-right',
                    fixed_order_button_color: document.getElementById('buttonColor')?.value || '#667eea',
                    fixed_order_button_shape: document.getElementById('buttonShape')?.value || 'rounded',
                    fixed_order_button_size: document.getElementById('buttonSize')?.value || 'medium',
                    fixed_order_button_icon: document.getElementById('buttonIcon')?.value || 'cart-shopping',
                    smart_coupons_enabled: document.getElementById('smartCouponsToggle')?.checked || false,
                    remaining_items_counter_enabled: document.getElementById('remainingItemsCounterToggle')?.checked || false,
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                fetch(`/user/pages/${pageId}/settings`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(err => Promise.reject(err));
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('pageSettingsModal');
                            if (modal) {
                                modal.classList.remove('show');
                                document.body.style.overflow = '';
                            }
                            const successMsg = data.message || this.dataset.successMsg || 'Settings saved successfully';
                            if (window.showToast) {
                                window.showToast(successMsg, 'success');
                            } else if (window.showFlashMessage) {
                                window.showFlashMessage(successMsg, 'success');
                            } else {
                                alert(successMsg);
                            }
                        } else {
                            throw new Error(data.message || 'Error saving settings');
                        }
                    })
                    .catch(err => {
                        console.error('Error saving settings:', err);
                        const errorMsg = err.message || err.error?.message || this.dataset.errorMsg || 'Error saving settings';
                        if (window.showToast) {
                            window.showToast(errorMsg, 'error');
                        } else if (window.showFlashMessage) {
                            window.showFlashMessage(errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
            });
        }

        // Close Modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                const modal = document.getElementById('pageSettingsModal');
                if (modal) {
                    modal.classList.remove('show');
                }
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                const modal = document.getElementById('pageSettingsModal');
                if (modal) {
                    modal.classList.remove('show');
                }
            });
        }
    }

    // Tracking IDs Modal
    function initTrackingIdsModal() {
        const saveBtn = document.getElementById('save-tracking-ids');
        const closeBtn = document.getElementById('close-tracking-ids-modal');
        const cancelBtn = document.getElementById('cancel-tracking-ids-modal');

        // Save Tracking IDs
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                // Get pageId from the button's dataset or from the modal's data attribute
                let pageId = this.dataset.pageId;
                if (!pageId) {
                    // Try to get from modal's data attribute
                    const modal = document.getElementById('trackingIdsModal');
                    if (modal) {
                        pageId = modal.dataset.pageId;
                    }
                }
                if (!pageId) {
                    const pageNotFoundMsg = this.dataset.pageNotFoundMsg || 'Page not found';
                    alert(pageNotFoundMsg);
                    return;
                }

                // Disable button during save
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (this.dataset.savingText || 'Saving...');

                const data = {
                    facebook_pixel: document.getElementById('facebook_pixel')?.value?.trim() || null,
                    tiktok_pixel: document.getElementById('tiktok_pixel')?.value?.trim() || null,
                    snapchat_pixel: document.getElementById('snapchat_pixel')?.value?.trim() || null,
                    google_analytics_id: document.getElementById('google_analytics_id')?.value?.trim() || null,
                };

                // Convert empty strings to null
                Object.keys(data).forEach(key => {
                    if (data[key] === '') {
                        data[key] = null;
                    }
                });

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                fetch(`/user/pages/${pageId}/tracking-ids`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(err => Promise.reject(err));
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('trackingIdsModal');
                            if (modal) {
                                modal.classList.remove('show');
                                document.body.style.overflow = '';
                            }
                            const successMsg = data.message || this.dataset.successMsg || 'Tracking IDs saved successfully';
                            if (window.showToast) {
                                window.showToast(successMsg, 'success');
                            } else if (window.showFlashMessage) {
                                window.showFlashMessage(successMsg, 'success');
                            } else {
                                alert(successMsg);
                            }
                        } else {
                            throw new Error(data.message || 'Error saving tracking IDs');
                        }
                    })
                    .catch(err => {
                        console.error('Error saving tracking IDs:', err);
                        const errorMsg = err.message || err.error?.message || this.dataset.errorMsg || 'Error saving tracking IDs';
                        if (window.showToast) {
                            window.showToast(errorMsg, 'error');
                        } else if (window.showFlashMessage) {
                            window.showFlashMessage(errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
            });
        }

        // Close Modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                const modal = document.getElementById('trackingIdsModal');
                if (modal) {
                    modal.classList.remove('show');
                }
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                const modal = document.getElementById('trackingIdsModal');
                if (modal) {
                    modal.classList.remove('show');
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initPageSettingsModal();
            initTrackingIdsModal();
        });
    } else {
        initPageSettingsModal();
        initTrackingIdsModal();
    }
    // Store Links Modal
    function initStoreLinksModal() {
        const openBtn = document.querySelector('[data-open-store-links-modal]');
        const modal = document.getElementById('storeLinksModal');
        
        if (!modal) {
            console.warn('Store Links Modal not found');
            return;
        }
        
        const closeBtn = modal.querySelector('[data-close-modal]');

        if (openBtn && modal) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeModal() {
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        // Close modal when clicking on overlay (the modal itself)
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Copy link functionality - use event delegation for dynamically loaded content
        modal.addEventListener('click', function(e) {
            const copyBtn = e.target.closest('[data-copy-url]');
            if (copyBtn) {
                e.preventDefault();
                const urlInputSelector = copyBtn.getAttribute('data-copy-url');
                const urlInput = document.querySelector(urlInputSelector);
                const successMsg = copyBtn.getAttribute('data-copy-success') || 'Link copied!';
                
                if (urlInput) {
                    urlInput.select();
                    urlInput.setSelectionRange(0, 99999); // For mobile devices
                    
                    try {
                        document.execCommand('copy');
                        
                        // Show success feedback
                        const originalText = copyBtn.querySelector('span')?.textContent || '';
                        const icon = copyBtn.querySelector('i');
                        
                        if (icon) {
                            icon.className = 'fa-solid fa-check';
                        }
                        if (copyBtn.querySelector('span')) {
                            copyBtn.querySelector('span').textContent = successMsg;
                        }
                        
                        setTimeout(() => {
                            if (icon) {
                                icon.className = 'fa-solid fa-copy';
                            }
                            if (copyBtn.querySelector('span')) {
                                copyBtn.querySelector('span').textContent = originalText;
                            }
                        }, 2000);
                    } catch (err) {
                        console.error('Failed to copy:', err);
                    }
                }
            }
        });
    }

    // Product Variations Modal
    let modalSizesIndex = 0;
    let modalColorsIndex = 0;
    let currentPageId = null;

    window.addModalVariation = function(type) {
        const container = document.getElementById('modal-' + type + '-container');
        if (!container) return;
        
        const index = type === 'sizes' ? modalSizesIndex++ : modalColorsIndex++;
        const placeholder = type === 'sizes' ?
            (container.dataset.sizeNamePlaceholder || 'Size name') :
            (container.dataset.colorNamePlaceholder || 'Color name');
        const valuePlaceholder = container.dataset.colorCodePlaceholder || 'Color code (#FF0000) or image URL';

        let html = '';
        if (type === 'sizes') {
            // Sizes: only name field (no image)
            html = `
                <div class="variation-item-content">
                    <input type="text" name="${type}[${index}][name]" placeholder="${placeholder}" class="form-control">
                    <button type="button" class="btn-remove-variation" onclick="removeModalVariation(this, '${type}')">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            // Colors: name + color code or image URL
            html = `
                <div class="variation-item-content">
                    <input type="text" name="${type}[${index}][name]" placeholder="${placeholder}" class="form-control">
                    <input type="text" name="${type}[${index}][value]" placeholder="${valuePlaceholder}" class="form-control variation-value-input" onchange="previewColorVariation(this)">
                    <div class="variation-preview" style="display: none;"></div>
                    <button type="button" class="btn-remove-variation" onclick="removeModalVariation(this, '${type}')">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        }

        const item = document.createElement('div');
        item.className = 'variation-item';
        item.setAttribute('data-index', index);
        item.innerHTML = html;
        container.appendChild(item);
    };

    window.removeModalVariation = function(btn, type) {
        const item = btn.closest('.variation-item');
        if (item) {
            item.remove();
        }
    };

    window.previewColorVariation = function(input) {
        const url = input.value;
        const preview = input.parentElement.querySelector('.variation-preview');
        if (url && preview) {
            preview.innerHTML = `<img src="${url}" alt="Preview" style="max-width: 100px; max-height: 100px; border-radius: 4px;" onerror="this.parentElement.style.display='none'">`;
            preview.style.display = 'block';
        } else if (preview) {
            preview.style.display = 'none';
        }
    };

    window.loadVariations = function(pageId) {
        currentPageId = pageId;
        fetch(`/user/pages/${pageId}/variations`)
            .then(response => response.json())
            .then(data => {
                const sizes = data.sizes || [];
                const colors = data.colors || [];

                // Clear containers
                const sizesContainer = document.getElementById('modal-sizes-container');
                const colorsContainer = document.getElementById('modal-colors-container');
                if (sizesContainer) sizesContainer.innerHTML = '';
                if (colorsContainer) colorsContainer.innerHTML = '';

                // Reset indices
                modalSizesIndex = 0;
                modalColorsIndex = 0;

                // Load sizes
                sizes.forEach((size) => {
                    addModalVariation('sizes');
                    const sizeItem = sizesContainer?.querySelector('.variation-item:last-child');
                    if (sizeItem) {
                        const nameInput = sizeItem.querySelector('input[name^="sizes"][name$="[name]"]');
                        if (nameInput) {
                            nameInput.value = size.name || '';
                        }
                    }
                    modalSizesIndex++;
                });

                // Load colors
                colors.forEach((color) => {
                    addModalVariation('colors');
                    const colorItem = colorsContainer?.querySelector('.variation-item:last-child');
                    if (colorItem) {
                        const nameInput = colorItem.querySelector('input[name^="colors"][name$="[name]"]');
                        const valueInput = colorItem.querySelector('input[name^="colors"][name$="[value]"]');
                        if (nameInput) {
                            nameInput.value = color.name || '';
                        }
                        if (valueInput) {
                            const colorValue = color.value || color.image || '';
                            valueInput.value = colorValue;
                            if (colorValue) {
                                previewColorVariation(valueInput);
                            }
                        }
                    }
                    modalColorsIndex++;
                });
            })
            .catch(error => {
                console.error('Error loading variations:', error);
            });
    };

    // Variations Modal Event Handlers
    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('save-variations-settings');
        const closeBtn = document.getElementById('close-variations-modal');
        const cancelBtn = document.getElementById('cancel-variations-modal');
        const modal = document.getElementById('variationsModal');

        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (!currentPageId) return;

                const form = document.getElementById('variationsForm');
                if (!form) return;
                
                const formData = new FormData(form);

                // Collect sizes and colors
                const sizes = [];
                const colors = [];

                formData.forEach((value, key) => {
                    // Match sizes[name] or colors[name] or colors[value]
                    const match = key.match(/(sizes|colors)\[(\d+)\]\[(name|value|image)\]/);
                    if (match) {
                        const type = match[1];
                        const index = parseInt(match[2], 10);
                        const field = match[3];

                        if (type === 'sizes') {
                            if (!sizes[index]) sizes[index] = {};
                            if (field === 'name') {
                                sizes[index][field] = value;
                            }
                        } else if (type === 'colors') {
                            if (!colors[index]) colors[index] = {};
                            if (field === 'name') {
                                colors[index][field] = value;
                            } else if (field === 'value' || field === 'image') {
                                // Store as 'value' for both color code and image URL
                                colors[index]['value'] = value;
                            }
                        }
                    }
                });

                // Filter out empty entries
                const filteredSizes = sizes.filter(s => s.name && s.name.trim() !== '');
                const filteredColors = colors.filter(c => c.name && c.name.trim() !== '');

                // Disable button
                const originalText = this.innerHTML;
                this.disabled = true;
                const savingText = saveBtn.dataset.savingText || 'Saving...';
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + savingText;

                // Send to server
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/user/pages/${currentPageId}/variations`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            sizes: filteredSizes,
                            colors: filteredColors
                        })
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await response.text();
                            console.error('Non-JSON response:', text);
                            throw new Error('Server returned non-JSON response. Please check the console.');
                        }

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({
                                message: 'Error saving variations'
                            }));
                            return Promise.reject(new Error(errorData.message || 'Error saving variations'));
                        }

                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            if (modal) {
                                modal.style.display = 'none';
                                modal.classList.remove('show');
                                document.body.style.overflow = '';
                            }
                            // Show success message
                            const successMsg = data.message || (saveBtn.dataset.successText || 'Variations updated successfully');
                            if (window.showToast) {
                                window.showToast(successMsg, 'success');
                            } else if (window.showFlashMessage) {
                                window.showFlashMessage(successMsg, 'success');
                            } else {
                                alert(successMsg);
                            }
                            // Reload page to reflect changes
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || (saveBtn.dataset.errorText || 'An error occurred'));
                        }
                    })
                    .catch(error => {
                        console.error('Error saving variations:', error);
                        const errorMsg = error.message || (saveBtn.dataset.errorText || 'An error occurred');
                        if (window.showToast) {
                            window.showToast(errorMsg, 'error');
                        } else if (window.showFlashMessage) {
                            window.showFlashMessage(errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
            });
        }

        // Close modal buttons
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        }

        // Close modal when clicking outside
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    this.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initPageSettingsModal();
        initTrackingIdsModal();
        initStoreLinksModal();
    });
})();
