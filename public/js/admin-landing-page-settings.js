// Admin Landing Page Settings JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Tab switching functionality
    const tabItems = document.querySelectorAll('.tab-item');
    const tabContents = document.querySelectorAll('.settings-tab-content');

    tabItems.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all tabs and contents
            tabItems.forEach(item => item.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const targetContent = document.querySelector(`[data-tab-content="${targetTab}"]`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // Platform management
    let platformIndex = document.querySelectorAll('.platform-item').length;

    // Add new platform
    const addPlatformBtn = document.getElementById('add-platform');
    if (addPlatformBtn) {
        addPlatformBtn.addEventListener('click', function () {
            const container = document.getElementById('platforms-container');
            if (!container) return;

            const newPlatform = document.createElement('div');
            newPlatform.className = 'platform-item';
            newPlatform.setAttribute('data-platform-index', platformIndex);
            newPlatform.style.cssText = 'background: #f8f9fa; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #dee2e6;';

            const platformNameLabel = getTranslation('messages.platform_name') || 'Platform Name';
            const platformIconLabel = getTranslation('messages.platform_icon') || 'Icon Class';
            const enabledLabel = getTranslation('messages.enabled') || 'Enabled';
            const iconHint = getTranslation('messages.platform_icon_hint') || 'Font Awesome icon class';
            const statusLabel = getTranslation('messages.status') || 'Status';

            newPlatform.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <label>${platformNameLabel}</label>
                        <input type="text" name="platforms[${platformIndex}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>${platformIconLabel}</label>
                        <input type="text" name="platforms[${platformIndex}][icon]" class="form-control" 
                            placeholder="fa-brands fa-google" required>
                        <small class="form-text text-muted">${iconHint}</small>
                    </div>
                    <div class="col-md-3">
                        <label style="display: block; margin-bottom: 5px;">${statusLabel}</label>
                        <label style="margin-top: 25px;">
                            <input type="checkbox" name="platforms[${platformIndex}][enabled]" value="1" checked>
                            ${enabledLabel}
                        </label>
                    </div>
                    <div class="col-md-1">
                        <label style="display: block; margin-bottom: 5px;">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-platform" style="margin-top: 25px; width: 100%;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newPlatform);
            platformIndex++;

            // Attach remove event to new platform
            attachRemovePlatformEvent(newPlatform);
        });
    }

    // Remove platform
    function attachRemovePlatformEvent(element) {
        const removeBtn = element.querySelector('.remove-platform');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                const platformItem = this.closest('.platform-item');
                if (platformItem && confirm(getTranslation('messages.confirm_delete') || 'Are you sure you want to remove this platform?')) {
                    platformItem.remove();
                }
            });
        }
    }

    // Attach remove events to existing platforms
    document.querySelectorAll('.remove-platform').forEach(btn => {
        btn.addEventListener('click', function () {
            const platformItem = this.closest('.platform-item');
            if (platformItem && confirm(getTranslation('messages.confirm_delete') || 'Are you sure you want to remove this platform?')) {
                platformItem.remove();
            }
        });
    });

    // Social Media management
    let socialMediaIndex = document.querySelectorAll('.social-media-item').length;

    // Add new social media
    const addSocialMediaBtn = document.getElementById('add-social-media');
    if (addSocialMediaBtn) {
        addSocialMediaBtn.addEventListener('click', function () {
            const container = document.getElementById('social-media-container');
            if (!container) return;

            const newSocialMedia = document.createElement('div');
            newSocialMedia.className = 'social-media-item';
            newSocialMedia.setAttribute('data-social-index', socialMediaIndex);
            newSocialMedia.style.cssText = 'background: #f8f9fa; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #dee2e6;';

            const platformLabel = getTranslation('messages.platform') || 'Platform';
            const urlLabel = getTranslation('messages.url') || 'URL';
            const enabledLabel = getTranslation('messages.enabled') || 'Enabled';

            newSocialMedia.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <label>${platformLabel}</label>
                        <select name="social_media[${socialMediaIndex}][platform]" class="form-control" required>
                            <option value="facebook">Facebook</option>
                            <option value="twitter">Twitter</option>
                            <option value="instagram">Instagram</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="youtube">YouTube</option>
                            <option value="tiktok">TikTok</option>
                            <option value="snapchat">Snapchat</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>${urlLabel}</label>
                        <input type="url" name="social_media[${socialMediaIndex}][url]" class="form-control" placeholder="https://..." required>
                    </div>
                    <div class="col-md-1">
                        <label style="display: block; margin-bottom: 5px;">&nbsp;</label>
                        <label style="margin-top: 25px;">
                            <input type="checkbox" name="social_media[${socialMediaIndex}][enabled]" value="1" checked>
                            ${enabledLabel}
                        </label>
                    </div>
                    <div class="col-md-1">
                        <label style="display: block; margin-bottom: 5px;">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-social-media" style="margin-top: 25px; width: 100%;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newSocialMedia);
            socialMediaIndex++;

            // Attach remove event to new social media
            attachRemoveSocialMediaEvent(newSocialMedia);
        });
    }

    // Remove social media
    function attachRemoveSocialMediaEvent(element) {
        const removeBtn = element.querySelector('.remove-social-media');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                const socialMediaItem = this.closest('.social-media-item');
                if (socialMediaItem && confirm(getTranslation('messages.confirm_delete') || 'Are you sure you want to remove this social media link?')) {
                    socialMediaItem.remove();
                }
            });
        }
    }

    // Attach remove events to existing social media
    document.querySelectorAll('.remove-social-media').forEach(btn => {
        btn.addEventListener('click', function () {
            const socialMediaItem = this.closest('.social-media-item');
            if (socialMediaItem && confirm(getTranslation('messages.confirm_delete') || 'Are you sure you want to remove this social media link?')) {
                socialMediaItem.remove();
            }
        });
    });

    // Hero Image Validation
    const heroImageInput = document.getElementById('hero_image');
    const heroImageEnabledCheckboxes = document.querySelectorAll('input[name*="[hero_image_enabled]"]');
    const firstHeroImage = document.querySelector('input[name="hero_image_url"]');

    // Check if hero image exists (file input or existing image)
    function hasHeroImage() {
        return (heroImageInput && heroImageInput.files && heroImageInput.files.length > 0) || 
               (firstHeroImage && firstHeroImage.value);
    }

    // Validate hero image when checkbox is checked
    heroImageEnabledCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && !hasHeroImage()) {
                alert(getTranslation('messages.hero_image_required_alert') || 
                      'Please upload a hero image first before enabling it. Hero image is required when enabled.');
                this.checked = false;
                
                // Scroll to hero image input
                if (heroImageInput) {
                    heroImageInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    heroImageInput.focus();
                }
            }
        });
    });

    // Update validation when hero image is uploaded
    if (heroImageInput) {
        heroImageInput.addEventListener('change', function() {
            // If image is uploaded, allow enabling hero image
            if (this.files && this.files.length > 0) {
                heroImageEnabledCheckboxes.forEach(checkbox => {
                    // Enable checkbox if it was previously disabled
                });
            }
        });
    }

    // Form submission - clean up empty platforms and social media
    const form = document.getElementById('landingPageSettingsForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            // Validate hero image if enabled
            let hasHeroImageEnabled = false;
            heroImageEnabledCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    hasHeroImageEnabled = true;
                }
            });

            if (hasHeroImageEnabled && !hasHeroImage()) {
                e.preventDefault();
                alert(getTranslation('messages.hero_image_required_alert') || 
                      'Hero image is required when "Enable Hero Image" is checked. Please upload a hero image.');
                if (heroImageInput) {
                    heroImageInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    heroImageInput.focus();
                }
                return false;
            }

            // Remove platforms with empty names before submission
            const platformItems = document.querySelectorAll('.platform-item');
            platformItems.forEach(item => {
                const nameInput = item.querySelector('input[name*="[name]"]');
                if (nameInput && !nameInput.value.trim()) {
                    item.remove();
                }
            });

            // Remove social media with empty URLs before submission
            const socialMediaItems = document.querySelectorAll('.social-media-item');
            socialMediaItems.forEach(item => {
                const urlInput = item.querySelector('input[name*="[url]"]');
                if (urlInput && !urlInput.value.trim()) {
                    item.remove();
                }
            });
        });
    }
});

// Helper function to get translations (if available)
function getTranslation(key) {
    // This would typically use a translation system
    // For now, return null to use fallback text
    return null;
}
