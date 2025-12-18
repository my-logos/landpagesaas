// User Pages Index JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Filter Toggle Functionality
    const filterToggle = document.getElementById('filterToggle');
    const filterContent = document.getElementById('filterContent');
    const filterChevron = filterToggle?.querySelector('.fa-chevron-down');
    const hideFilterBtn = document.getElementById('hideFilterBtn');

    if (filterToggle && filterContent) {
        filterToggle.addEventListener('click', function (e) {
            // Don't toggle if clicking inside select or chevron
            if (e.target.closest('select') || e.target.closest('.fa-chevron-down')) {
                return;
            }
            toggleFilter();
        });
    }

    function toggleFilter() {
        if (filterContent) {
            const isHidden = filterContent.style.display === 'none' || !filterContent.style.display;
            if (isHidden) {
                filterContent.style.display = 'block';
                if (filterChevron) {
                    filterChevron.style.transform = 'rotate(180deg)';
                }
            } else {
                filterContent.style.display = 'none';
                if (filterChevron) {
                    filterChevron.style.transform = 'rotate(0deg)';
                }
            }
        }
    }

    // Clear Filters Functionality
    const clearFiltersBtn = document.querySelector('.btn-clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function () {
            const form = document.getElementById('filterForm');
            if (form) {
                form.reset();
                // Reset status to 'all'
                const statusSelect = form.querySelector('select[name="status"]');
                if (statusSelect) {
                    statusSelect.value = 'all';
                }
                form.submit();
            }
        });
    }

    // View Toggle Functionality
    const viewButtons = document.querySelectorAll('.view-toggle-btn, .view-btn');
    const pagesContainer = document.getElementById('pagesContainer');

    // Initialize grid view by default
    if (pagesContainer) {
        pagesContainer.classList.add('grid-view');
    }

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const view = this.dataset.view;

            // Update active state
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Toggle grid/list view
            if (pagesContainer) {
                if (view === 'list') {
                    pagesContainer.classList.add('list-view');
                    pagesContainer.classList.remove('grid-view');
                } else {
                    pagesContainer.classList.add('grid-view');
                    pagesContainer.classList.remove('list-view');
                }
            }
        });
    });

    // Additional Sales Modal Functions
    window.openAdditionalSalesModal = function () {
        const modal = document.getElementById('additionalSalesModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeAdditionalSalesModal = function () {
        const modal = document.getElementById('additionalSalesModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    };

    // Close modal when clicking outside
    const modal = document.getElementById('additionalSalesModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeAdditionalSalesModal();
            }
        });
    }

    // Tab switching
    document.querySelectorAll('.modal-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Open additional sales modal button
    const openBtn = document.getElementById('open-additional-sales-btn');
    if (openBtn) {
        openBtn.addEventListener('click', openAdditionalSalesModal);
    }

    // Close modal buttons
    const closeBtn = document.getElementById('close-additional-sales-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeAdditionalSalesModal);
    }

    const cancelBtn = document.getElementById('cancel-additional-sales-modal');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeAdditionalSalesModal);
    }

    // Save button
    const saveBtn = document.getElementById('save-additional-sales-settings');
    if (saveBtn) {
        saveBtn.addEventListener('click', saveAdditionalSalesSettings);
    }

    // Menu item clicks - Open modals
    document.querySelectorAll('.menu-item[data-action]').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const action = this.dataset.action;
            const pageId = this.dataset.pageId;

            // Close dropdown menu
            const dropdown = this.closest('.page-menu-dropdown');
            if (dropdown) {
                dropdown.style.display = 'none';
            }

            // Open appropriate modal
            if (action === 'settings') {
                const modal = document.getElementById('pageSettingsModal');
                if (modal) {
                    const saveBtn = document.getElementById('save-page-settings');
                    if (saveBtn) {
                        saveBtn.dataset.pageId = pageId;
                    }
                    // Load current settings
                    loadPageSettings(pageId);
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
            } else if (action === 'variations') {
                const modal = document.getElementById('variationsModal');
                if (modal) {
                    // Load variations
                    if (typeof window.loadVariations === 'function') {
                        window.loadVariations(pageId);
                    }
                    modal.style.display = 'flex';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
            } else if (action === 'tracking') {
                const modal = document.getElementById('trackingIdsModal');
                if (modal) {
                    const saveBtn = document.getElementById('save-tracking-ids');
                    if (saveBtn) {
                        saveBtn.dataset.pageId = pageId;
                    }
                    // Set pageId in modal's data attribute as backup
                    modal.dataset.pageId = pageId;
                    // Load current tracking IDs
                    loadTrackingIds(pageId);
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
            } else if (action === 'link') {
                const modal = document.getElementById('createLinkModal');
                if (modal) {
                    const saveBtn = document.getElementById('save-create-link');
                    if (saveBtn) {
                        saveBtn.dataset.pageId = pageId;
                    }
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
            }
        });
    });

    // Page menu button clicks (three dots)
    document.querySelectorAll('.page-menu-dots-btn, .page-menu-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const pageId = this.dataset.pageId;
            const dropdown = document.getElementById(`menu-${pageId}`);
            if (dropdown) {
                // Close all other dropdowns
                document.querySelectorAll('.page-menu-dropdown').forEach(d => {
                    if (d.id !== dropdown.id) {
                        d.style.display = 'none';
                    }
                });
                // Toggle current dropdown
                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.page-card-menu-dots') && !e.target.closest('.page-actions-menu')) {
            document.querySelectorAll('.page-menu-dropdown').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    // Publish Button Handler (for forms)
    document.querySelectorAll('.publish-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const pageId = this.dataset.pageId || this.querySelector('button[type="submit"]')?.dataset.pageId;
            if (!pageId) {
                const formAction = this.action;
                const match = formAction.match(/\/pages\/(\d+)\/publish/);
                if (match) {
                    pageId = match[1];
                } else {
                    return;
                }
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (submitBtn.dataset.publishingText || (document.documentElement.lang === 'ar' ? 'جاري النشر...' : 'Publishing...'));
            }

            const formData = new FormData(this);
            const csrfToken = formData.get('_token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        if (window.showToast) {
                            window.showToast(data.message || (document.documentElement.lang === 'ar' ? 'تم نشر الصفحة بنجاح' : 'Page published successfully'), 'success');
                        } else if (window.showFlashMessage) {
                            window.showFlashMessage(data.message || (document.documentElement.lang === 'ar' ? 'تم نشر الصفحة بنجاح' : 'Page published successfully'), 'success');
                        }
                        // Reload page to update UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Failed to publish');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                    const errorMsg = error.message || (document.documentElement.lang === 'ar' ? 'حدث خطأ أثناء نشر الصفحة' : 'Error publishing page');
                    if (window.showToast) {
                        window.showToast(errorMsg, 'error');
                    } else if (window.showFlashMessage) {
                        window.showFlashMessage(errorMsg, 'error');
                    } else {
                        if (window.showFlashMessage) {
                            window.showFlashMessage(errorMsg, 'error');
                        }
                    }
                });
        });
    });

    // Publish Button Handler (for buttons with class btn-publish)
    document.querySelectorAll('.btn-publish').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const pageId = this.dataset.pageId;
            if (!pageId) return;

            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (this.dataset.publishingText || 'Publishing...');

            fetch(`/user/pages/${pageId}/publish`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update status badge
                        const badge = document.querySelector(`[data-page-id="${pageId}"]`)?.closest('.page-card, .page-card-new')?.querySelector('.status-badge, .page-status-badge');
                        if (badge) {
                            badge.textContent = data.message?.includes('Published') || data.message?.includes('منشورة')
                                ? (badge.dataset.publishedText || 'Published')
                                : badge.textContent;
                            badge.classList.add('published');
                        }
                        // Reload page to update UI
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Failed to publish');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.disabled = false;
                    this.innerHTML = originalText;
                    if (window.showToast) {
                        window.showToast('Error publishing page', 'error');
                    } else if (window.showFlashMessage) {
                        window.showFlashMessage('Error publishing page', 'error');
                    } else {
                        if (window.showFlashMessage) {
                            window.showFlashMessage('Error publishing page', 'error');
                        }
                    }
                });
        });
    });

    // Delete Form Confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const confirmMessage = this.dataset.confirmMessage || 'Are you sure you want to delete this page?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Check page generation status for pending/processing pages
    const pendingPages = document.querySelectorAll('[data-generation-status="pending"], [data-generation-status="processing"]');
    if (pendingPages.length > 0) {
        const checkInterval = 3000; // Check every 3 seconds
        const maxChecks = 100; // Maximum 5 minutes (100 * 3 seconds)
        let checkCount = 0;

        const checkPageStatus = () => {
            if (checkCount >= maxChecks) {
                return;
            }

            pendingPages.forEach(pageElement => {
                const pageId = pageElement.dataset.pageId || pageElement.closest('[data-page-id]')?.dataset.pageId;
                if (!pageId) return;

                fetch(`/user/pages/${pageId}/status`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.completed) {
                            // Reload page to show updated status
                            window.location.reload();
                        } else if (data.failed) {
                            // Show error message
                            const errorElement = pageElement.querySelector('.generation-error');
                            if (errorElement) {
                                errorElement.textContent = data.error || 'Generation failed';
                                errorElement.style.display = 'block';
                            }
                            // Update status badge
                            const statusBadge = pageElement.querySelector('.generation-status-badge');
                            if (statusBadge) {
                                statusBadge.textContent = document.documentElement.lang === 'ar' ? 'فشل' : 'Failed';
                                statusBadge.className = 'generation-status-badge failed';
                            }
                        } else {
                            // Update progress indicator
                            const statusBadge = pageElement.querySelector('.generation-status-badge');
                            if (statusBadge) {
                                if (data.status === 'processing') {
                                    statusBadge.textContent = document.documentElement.lang === 'ar' ? 'جاري الإنشاء...' : 'Generating...';
                                    statusBadge.className = 'generation-status-badge processing';
                                } else if (data.status === 'pending') {
                                    statusBadge.textContent = document.documentElement.lang === 'ar' ? 'في الانتظار...' : 'Pending...';
                                    statusBadge.className = 'generation-status-badge pending';
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking page status:', error);
                    });
            });

            checkCount++;
        };

        // Start checking immediately
        checkPageStatus();

        // Continue checking at intervals
        const statusCheckInterval = setInterval(() => {
            checkPageStatus();

            // Stop if no pending pages remain
            const remainingPending = document.querySelectorAll('[data-generation-status="pending"], [data-generation-status="processing"]');
            if (remainingPending.length === 0) {
                clearInterval(statusCheckInterval);
            }
        }, checkInterval);
    }
});

// Save additional sales settings
function saveAdditionalSalesSettings() {
    const toggle = document.getElementById('additionalSalesToggle');
    const enabled = toggle ? toggle.checked : false;
    const saveBtn = document.getElementById('save-additional-sales-settings');
    const route = saveBtn?.dataset.additionalSalesRoute || document.body.dataset.additionalSalesRoute || '/user/pages/additional-sales';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (saveBtn.dataset.savingText || 'Saving...');
    }

    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ additional_sales_enabled: enabled })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    window.showToast(data.message || 'Settings saved', 'success');
                } else if (window.showFlashMessage) {
                    window.showFlashMessage(data.message || 'Settings saved', 'success');
                } else {
                    if (window.showFlashMessage) {
                        window.showFlashMessage(data.message || 'Settings saved', 'success');
                    }
                }
                window.closeAdditionalSalesModal();
            } else {
                throw new Error(data.message || 'Failed to save');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.showToast) {
                window.showToast('Error saving settings', 'error');
            } else if (window.showFlashMessage) {
                window.showFlashMessage('Error saving settings', 'error');
            } else {
                if (window.showFlashMessage) {
                    window.showFlashMessage('Error saving settings', 'error');
                }
            }
            if (toggle) {
                toggle.checked = !enabled;
            }
        })
        .finally(() => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-save"></i> ' + (saveBtn.dataset.saveText || 'Save');
            }
        });
};

// Load tracking IDs for a page
function loadTrackingIds(pageId) {
    fetch(`/user/pages/${pageId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
        .then(res => {
            if (!res.ok) {
                throw new Error('Failed to load tracking IDs');
            }
            return res.json();
        })
        .then(data => {
            // Set tracking IDs from page data
            if (document.getElementById('facebook_pixel')) {
                document.getElementById('facebook_pixel').value = data.facebook_pixel || '';
            }
            if (document.getElementById('tiktok_pixel')) {
                document.getElementById('tiktok_pixel').value = data.tiktok_pixel || '';
            }
            if (document.getElementById('snapchat_pixel')) {
                document.getElementById('snapchat_pixel').value = data.snapchat_pixel || '';
            }
            if (document.getElementById('google_analytics_id')) {
                document.getElementById('google_analytics_id').value = data.google_analytics_id || '';
            }
        })
        .catch(err => {
            console.error('Error loading tracking IDs:', err);
        });
}

// Load page settings for a page
function loadPageSettings(pageId) {
    fetch(`/user/pages/${pageId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
        .then(res => {
            if (!res.ok) {
                throw new Error('Failed to load page settings');
            }
            return res.json();
        })
        .then(data => {
            const settings = data.settings || {};

            // Set toggle states
            const smartAssistantToggle = document.getElementById('smartAssistantToggle');
            if (smartAssistantToggle) {
                smartAssistantToggle.checked = settings.smart_assistant_enabled === true || settings.smart_assistant_enabled === 1 || settings.smart_assistant_enabled === '1';
            }

            const fixedOrderButtonToggle = document.getElementById('fixedOrderButtonToggle');
            const fixedOrderButtonSettings = document.getElementById('fixedOrderButtonSettings');
            if (fixedOrderButtonToggle) {
                const isEnabled = settings.fixed_order_button_enabled === true || settings.fixed_order_button_enabled === 1 || settings.fixed_order_button_enabled === '1';
                fixedOrderButtonToggle.checked = isEnabled;
                // Show/hide settings based on toggle
                if (fixedOrderButtonSettings) {
                    fixedOrderButtonSettings.style.display = isEnabled ? 'block' : 'none';
                    if (isEnabled) {
                        fixedOrderButtonSettings.classList.add('show');
                    } else {
                        fixedOrderButtonSettings.classList.remove('show');
                    }
                }
            }

            const smartCouponsToggle = document.getElementById('smartCouponsToggle');
            if (smartCouponsToggle) {
                smartCouponsToggle.checked = settings.smart_coupons_enabled === true || settings.smart_coupons_enabled === 1 || settings.smart_coupons_enabled === '1';
            }

            const remainingItemsCounterToggle = document.getElementById('remainingItemsCounterToggle');
            if (remainingItemsCounterToggle) {
                remainingItemsCounterToggle.checked = settings.remaining_items_counter_enabled === true || settings.remaining_items_counter_enabled === 1 || settings.remaining_items_counter_enabled === '1';
            }

            // Set fixed order button settings (always set, even if empty)
            const buttonText = document.getElementById('buttonText');
            if (buttonText) {
                buttonText.value = settings.fixed_order_button_text || 'اطلب الآن';
            }

            const buttonPosition = document.getElementById('buttonPosition');
            if (buttonPosition) {
                buttonPosition.value = settings.fixed_order_button_position || 'bottom-right';
            }

            const buttonColor = document.getElementById('buttonColor');
            if (buttonColor) {
                buttonColor.value = settings.fixed_order_button_color || '#667eea';
            }

            const buttonShape = document.getElementById('buttonShape');
            if (buttonShape) {
                buttonShape.value = settings.fixed_order_button_shape || 'rounded';
            }

            const buttonSize = document.getElementById('buttonSize');
            if (buttonSize) {
                buttonSize.value = settings.fixed_order_button_size || 'medium';
            }

            const buttonIcon = document.getElementById('buttonIcon');
            if (buttonIcon) {
                buttonIcon.value = settings.fixed_order_button_icon || 'cart-shopping';
            }
        })
        .catch(err => {
            console.error('Error loading page settings:', err);
        });
}
