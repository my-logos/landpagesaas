/* eslint-env browser */
// User Pages Form JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Template selection function
    window.selectTemplate = function (templateId) {
        // Uncheck all radio buttons
        document.querySelectorAll('input[name="template_id"]').forEach(radio => {
            radio.checked = false;
        });

        // Check selected template
        if (templateId === null) {
            const noneRadio = document.getElementById('template_none');
            if (noneRadio) {
                noneRadio.checked = true;
            }
        } else {
            const templateRadio = document.getElementById('template_' + templateId);
            if (templateRadio) {
                templateRadio.checked = true;
            }
        }

        // Update visual selection
        document.querySelectorAll('.template-option').forEach(option => {
            option.style.borderColor = '#e5e7eb';
            option.style.backgroundColor = 'transparent';
        });

        // Highlight selected
        const selectedRadio = document.querySelector('input[name="template_id"]:checked');
        if (selectedRadio && selectedRadio.closest('.template-option')) {
            const option = selectedRadio.closest('.template-option');
            option.style.borderColor = '#3b82f6';
            option.style.backgroundColor = '#eff6ff';
        }
    };

    // Initialize template selection on page load
    const selectedRadio = document.querySelector('input[name="template_id"]:checked');
    if (selectedRadio) {
        const value = selectedRadio.value;
        selectTemplate(value ? parseInt(value) : null);
    }

    // Add click handlers to template options
    document.querySelectorAll('.template-option').forEach(option => {
        option.addEventListener('click', function (e) {
            // Don't trigger if clicking on the radio or label
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL') {
                return;
            }
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                const value = radio.value;
                selectTemplate(value ? parseInt(value) : null);
            }
        });
    });

    // Initialize character counters for SEO fields
    const seoFields = ['seo_title', 'seo_description', 'seo_keywords'];
    seoFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const counter = document.getElementById(fieldId + '-counter');
        if (field && counter) {
            // Update counter on input
            field.addEventListener('input', function () {
                const maxLength = field.getAttribute('maxlength') || 255;
                counter.textContent = field.value.length + '/' + maxLength;
            });
            // Initialize counter
            const maxLength = field.getAttribute('maxlength') || 255;
            counter.textContent = field.value.length + '/' + maxLength;
        }
    });
    // Character counters
    const counters = {
        'title': 150,
        'additional_description': 2000,
        'facebook_pixel': 100,
        'tiktok_pixel': 100,
        'snapchat_pixel': 100,
        'google_analytics_id': 100
    };

    function updateCounter(input, counter, max) {
        const length = input.value.length;
        counter.textContent = length + '/' + max;
    }

    Object.keys(counters).forEach(field => {
        const input = document.getElementById(field);
        const counter = document.getElementById(field + '-counter');
        if (input && counter) {
            updateCounter(input, counter, counters[field]);
            input.addEventListener('input', () => updateCounter(input, counter, counters[field]));
        }
    });

    // Form type toggle
    document.querySelectorAll('.form-type-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.form-type-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const type = this.dataset.type;
            document.getElementById('form_type').value = type;

            const customFields = document.getElementById('custom-form-fields');
            if (customFields) {
                if (type === 'custom') {
                    customFields.classList.remove('hidden');
                    customFields.classList.add('active');
                } else {
                    customFields.classList.add('hidden');
                    customFields.classList.remove('active');
                    const selectedFieldsEl = document.getElementById('selected-fields');
                    if (selectedFieldsEl) {
                        selectedFieldsEl.innerHTML = '';
                    }
                    const formFieldsEl = document.getElementById('form_fields');
                    if (formFieldsEl) {
                        formFieldsEl.value = '[]';
                    }
                    window.selectedFields = [];
                }
            }
        });
    });

    // Form fields selection
    window.selectedFields = [];
    document.querySelectorAll('.field-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const field = this.dataset.field;
            if (!window.selectedFields.includes(field)) {
                window.selectedFields.push(field);
                updateSelectedFields();
            }
        });
    });

    window.updateSelectedFields = function () {
        const container = document.getElementById('selected-fields');
        if (!container) return;

        const labels = {
            'full_name': container.dataset.labelFullName || 'Full Name',
            'email': container.dataset.labelEmail || 'Email',
            'phone': container.dataset.labelPhone || 'Phone',
            'address': container.dataset.labelAddress || 'Address',
            'notes': container.dataset.labelNotes || 'Notes',
            'quantity': container.dataset.labelQuantity || 'Quantity'
        };

        // Initialize form fields from old values
        if (container.dataset.oldFields && !window.selectedFieldsInitialized) {
            try {
                const oldFields = JSON.parse(container.dataset.oldFields);
                if (oldFields && Array.isArray(oldFields) && oldFields.length > 0) {
                    window.selectedFields = oldFields;
                    updateSelectedFields();
                    const formTypeEl = document.getElementById('form_type');
                    if (formTypeEl) {
                        formTypeEl.value = 'custom';
                    }
                    document.querySelector('.form-type-btn[data-type="custom"]')?.classList.add('active');
                    document.querySelector('.form-type-btn[data-type="default"]')?.classList.remove('active');
                    const customFields = document.getElementById('custom-form-fields');
                    if (customFields) {
                        customFields.classList.remove('hidden');
                        customFields.classList.add('active');
                    }
                    window.selectedFieldsInitialized = true;
                }
            } catch (e) {
                console.error('Error parsing old fields:', e);
            }
        }

        container.innerHTML = window.selectedFields.map(field => {
            return `<span class="selected-field-tag">
                ${labels[field] || field}
                <button type="button" class="remove-field-btn" data-field="${field}">
                    <i class="fa-solid fa-times"></i>
                </button>
            </span>`;
        }).join('');

        // Attach event listeners to remove buttons
        container.querySelectorAll('.remove-field-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const field = this.dataset.field;
                removeField(field);
            });
        });

        const formFieldsEl = document.getElementById('form_fields');
        if (formFieldsEl) {
            formFieldsEl.value = JSON.stringify(window.selectedFields);
        }
    };

    window.removeField = function (field) {
        const index = window.selectedFields.indexOf(field);
        if (index > -1) {
            window.selectedFields.splice(index, 1);
            updateSelectedFields();
        }
    };

    // Collapsible sections
    document.querySelectorAll('.toggle-section-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const section = this.closest('.collapsible-section');
            if (!section) return;

            const content = section.querySelector('.section-content');
            const icon = this.querySelector('.toggle-icon');

            if (content) {
                const isHidden = !content.classList.contains('active') || content.classList.contains('hidden');

                if (isHidden) {
                    content.classList.add('active');
                    content.classList.remove('hidden');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                } else {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                    if (icon) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                }
            }
        });
    });

    // Close and cancel buttons
    document.querySelectorAll('.close-btn, .btn').forEach(btn => {
        if (!btn.id || btn.id !== 'reset-password-form-btn') {
            btn.addEventListener('click', function () {
                window.history.back();
            });
        }
    });

    // Restrict page title to English only
    const titleInput = document.getElementById('classic_title');
    if (titleInput) {
        // Remove any non-English characters on input
        titleInput.addEventListener('input', function (e) {
            // Remove Arabic and other non-ASCII characters, keep only English letters, numbers, spaces, hyphens, and underscores
            let value = this.value;
            let newValue = value.replace(/[^\x00-\x7F\s\-_]/g, '');

            if (value !== newValue) {
                this.value = newValue;
                // Show a brief warning
                const warning = document.createElement('div');
                warning.className = 'form-error english-only-warning';
                const warningText = titleInput.dataset.englishOnlyWarning || 'Only English letters, numbers, spaces, hyphens and underscores are allowed';
                warning.textContent = warningText;

                // Remove existing warning if any
                const existingWarning = this.parentElement.querySelector('.english-only-warning');
                if (existingWarning) {
                    existingWarning.remove();
                }

                this.parentElement.appendChild(warning);

                // Remove warning after 3 seconds
                setTimeout(() => {
                    warning.remove();
                }, 3000);
            }
        });

        // Prevent paste of non-English characters
        titleInput.addEventListener('paste', function (e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            // Remove non-English characters from pasted text
            const cleanPaste = paste.replace(/[^\x00-\x7F\s\-_]/g, '');
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const currentValue = this.value;
            this.value = currentValue.substring(0, start) + cleanPaste + currentValue.substring(end);
            this.setSelectionRange(start + cleanPaste.length, start + cleanPaste.length);
        });
    }

    // Product Variations Management
    let sizesIndex = 0;
    let colorsIndex = 0;

    // Initialize indices from data attributes if available
    const sizesContainer = document.getElementById('sizes-container');
    const colorsContainer = document.getElementById('colors-container');
    if (sizesContainer && sizesContainer.dataset.initialIndex) {
        sizesIndex = parseInt(sizesContainer.dataset.initialIndex) || 0;
    }
    if (colorsContainer && colorsContainer.dataset.initialIndex) {
        colorsIndex = parseInt(colorsContainer.dataset.initialIndex) || 0;
    }

    window.addVariation = function (type) {
        const container = document.getElementById(type + '-container');
        const index = type === 'sizes' ? sizesIndex++ : colorsIndex++;
        const placeholder = type === 'sizes' ?
            (container.dataset.sizeNamePlaceholder || 'Size name') :
            (container.dataset.colorNamePlaceholder || 'Color name');
        const imagePlaceholder = type === 'sizes' ?
            (container.dataset.sizeImagePlaceholder || 'Size image URL') :
            (container.dataset.colorImagePlaceholder || 'Color image URL');

        const item = document.createElement('div');
        item.className = 'variation-item';
        item.setAttribute('data-index', index);
        item.innerHTML = `
            <div class="variation-item-content">
                <input type="text" name="${type}[${index}][name]" placeholder="${placeholder}" class="form-control" required>
                <input type="url" name="${type}[${index}][image]" placeholder="${imagePlaceholder}" class="form-control variation-image-input" data-action="preview-variation">
                <div class="variation-preview hidden"></div>
                <button type="button" class="btn-remove-variation" data-action="remove-variation" data-type="${type}">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        `;
        // Add event listener for the new input
        const newInput = item.querySelector('.variation-image-input');
        if (newInput) {
            newInput.addEventListener('change', function () {
                previewVariationImage(this);
            });
        }
        container.appendChild(item);
    };

    window.removeVariation = function (btn, type) {
        const item = btn.closest('.variation-item');
        if (item) {
            item.remove();
        }
    };

    window.previewVariationImage = function (input) {
        const url = input.value;
        const preview = input.parentElement.querySelector('.variation-preview');
        if (url && preview) {
            preview.innerHTML = `<img src="${url}" alt="Preview">`;
            preview.classList.remove('hidden');
            preview.classList.add('show');
            // Handle image error
            const img = preview.querySelector('img');
            if (img) {
                img.addEventListener('error', function () {
                    preview.classList.add('hidden');
                    preview.classList.remove('show');
                });
            }
        } else if (preview) {
            preview.classList.add('hidden');
            preview.classList.remove('show');
        }
    };

    // Initialize previews for existing images
    document.querySelectorAll('.variation-image-input').forEach(input => {
        if (input.value) {
            previewVariationImage(input);
        }
        // Add event listener for data-action
        if (input.dataset.action === 'preview-variation') {
            input.addEventListener('change', function () {
                previewVariationImage(this);
            });
        }
    });

    // Handle data-action attributes
    document.addEventListener('click', function (e) {
        const action = e.target.closest('[data-action]')?.dataset.action;
        if (!action) return;

        if (action === 'close-form') {
            const route = e.target.closest('[data-action]').dataset.route;
            if (route) {
                window.location.href = route;
            }
        } else if (action === 'add-variation') {
            const type = e.target.closest('[data-action]').dataset.type;
            if (type) {
                addVariation(type);
            }
        } else if (action === 'remove-variation') {
            const btn = e.target.closest('[data-action]');
            const type = btn.dataset.type;
            if (type) {
                removeVariation(btn, type);
            }
        }
    });

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            // Prevent clicking on disabled AI tab
            if (this.classList.contains('disabled') || this.disabled) {
                return;
            }

            const targetTab = this.dataset.tab;
            if (!targetTab) return;

            // Get AI enabled status from hidden input
            const creationTypeInput = document.getElementById('creation_type');
            const aiEnabled = creationTypeInput ? creationTypeInput.dataset.aiEnabled === 'true' : false;

            // Prevent switching to AI tab if not enabled
            if (targetTab === 'ai-tab' && !aiEnabled) {
                return;
            }

            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
                // Disable all fields in inactive tabs (disabled fields are not submitted)
                content.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = true;
                    // Remove required attribute from hidden fields to prevent validation errors
                    if (field.hasAttribute('required')) {
                        field.removeAttribute('required');
                        field.dataset.wasRequired = 'true';
                    }
                });
            });

            const activeTab = document.getElementById(targetTab);
            if (activeTab) {
                activeTab.classList.add('active');
                // Enable all fields in active tab and restore required attributes
                activeTab.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = false;
                    // Restore required attribute if it was previously required
                    if (field.dataset.wasRequired === 'true' || field.dataset.required === 'true') {
                        field.setAttribute('required', 'required');
                    }
                });
            }

            // Update creation type
            const creationType = targetTab === 'ai-tab' ? 'ai' : 'classic';
            if (creationTypeInput) {
                creationTypeInput.value = creationType;
            }

            // Update submit button
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            if (targetTab === 'ai-tab') {
                const aiAvailable = creationTypeInput ? creationTypeInput.dataset.aiAvailable === 'true' : false;
                if (submitBtn) {
                    submitBtn.disabled = !aiAvailable;
                }
                if (submitBtnText) {
                    submitBtnText.textContent = submitBtnText.dataset.generateWithAi || 'Generate Page with AI';
                }
                if (submitBtn && submitBtn.querySelector('i')) {
                    submitBtn.querySelector('i').className = 'fa-solid fa-magic';
                }
            } else {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
                if (submitBtnText) {
                    const isEdit = submitBtnText.dataset.isEdit === 'true';
                    submitBtnText.textContent = isEdit ?
                        (submitBtnText.dataset.updatePage || 'Update Page') :
                        (submitBtnText.dataset.createPage || 'Create Page');
                }
                if (submitBtn && submitBtn.querySelector('i')) {
                    submitBtn.querySelector('i').className = 'fa-solid fa-plus';
                }
            }
        });
    });

    // Initialize: disable fields in inactive tabs on page load and open correct tab
    const creationTypeInput = document.getElementById('creation_type');
    if (creationTypeInput) {
        let creationType = creationTypeInput.value;
        const aiEnabled = creationTypeInput.dataset.aiEnabled === 'true';

        // If AI is not enabled, force classic tab
        if (creationType === 'ai' && !aiEnabled) {
            creationType = 'classic';
            creationTypeInput.value = 'classic';
        }
        const targetTab = creationType === 'ai' ? 'ai-tab' : 'classic-tab';

        // Find the active tab button and content
        const activeTabBtn = document.querySelector(`.tab-btn[data-tab="${targetTab}"]`);
        const activeTabContent = document.getElementById(targetTab);

        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
            // Disable all fields in inactive tabs
            content.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = true;
                // Remove required attribute from hidden fields to prevent validation errors
                if (field.hasAttribute('required')) {
                    field.removeAttribute('required');
                    field.dataset.wasRequired = 'true';
                }
            });
        });

        // Activate the correct tab
        if (activeTabBtn && activeTabContent) {
            activeTabBtn.classList.add('active');
            activeTabContent.classList.add('active');

            // Enable all fields in active tab and restore required attributes
            activeTabContent.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = false;
                // Restore required attribute if it was previously required
                if (field.dataset.wasRequired === 'true' || field.dataset.required === 'true') {
                    field.setAttribute('required', 'required');
                }
            });
        }

        // Set initial submit button state based on active tab
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        if (targetTab === 'ai-tab') {
            const aiAvailable = activeTabBtn?.dataset.aiAvailable === 'true';
            if (submitBtn) {
                submitBtn.disabled = !aiAvailable;
            }
            if (submitBtnText) {
                submitBtnText.textContent = submitBtnText.dataset.generateWithAi || 'Generate Page with AI';
            }
            if (submitBtn && submitBtn.querySelector('i')) {
                submitBtn.querySelector('i').className = 'fa-solid fa-magic';
            }
        } else {
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            if (submitBtnText) {
                const isEdit = submitBtnText.dataset.isEdit === 'true';
                submitBtnText.textContent = isEdit ?
                    (submitBtnText.dataset.updatePage || 'Update Page') :
                    (submitBtnText.dataset.createPage || 'Create Page');
            }
            if (submitBtn && submitBtn.querySelector('i')) {
                submitBtn.querySelector('i').className = 'fa-solid fa-plus';
            }
        }
    }

    // Form validation
    const pageForm = document.getElementById('pageForm');
    if (pageForm) {
        pageForm.addEventListener('submit', function (e) {
            const creationTypeInput = document.getElementById('creation_type');
            const creationType = creationTypeInput ? creationTypeInput.value : 'classic';

            // Remove required attributes from all disabled fields to prevent browser validation errors
            document.querySelectorAll('input[disabled], select[disabled], textarea[disabled]').forEach(field => {
                if (field.hasAttribute('required')) {
                    field.removeAttribute('required');
                }
            });

            if (creationType === 'ai') {
                const productId = document.getElementById('ai_product_id') ? document.getElementById('ai_product_id').value : '';
                const language = document.getElementById('ai_language') ? document.getElementById('ai_language').value : '';

                if (!productId || !language) {
                    e.preventDefault();
                    const errorMsg = document.getElementById('pageForm').dataset.fillAllFields || 'Please fill all required fields';
                    if (window.showToast) {
                        window.showToast(errorMsg, 'warning');
                    } else {
                        alert(errorMsg);
                    }
                    return false;
                }
            } else {
                const title = document.getElementById('classic_title') ? document.getElementById('classic_title').value.trim() : '';

                if (!title) {
                    e.preventDefault();
                    const errorMsg = document.getElementById('pageForm').dataset.fillAllFields || 'Please fill all required fields';
                    if (window.showToast) {
                        window.showToast(errorMsg, 'warning');
                    } else {
                        alert(errorMsg);
                    }
                    return false;
                }
            }
        });
    }
});
