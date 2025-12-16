// User Orders Page JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Collapsible Sections
    const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
    collapsibleHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const targetBody = document.getElementById(targetId);
            const toggleIcon = this.querySelector('.toggle-icon');
            
            if (targetBody) {
                const isOpen = targetBody.classList.contains('open');
                
                if (isOpen) {
                    // Close
                    targetBody.classList.remove('open');
                    this.classList.remove('active');
                    setTimeout(() => {
                        targetBody.style.display = 'none';
                    }, 300);
                } else {
                    // Open
                    targetBody.style.display = 'block';
                    setTimeout(() => {
                        targetBody.classList.add('open');
                        this.classList.add('active');
                    }, 10);
                }
            }
        });
    });

    // Performance Preset Selection
    const performancePresets = document.querySelectorAll('.performance-preset');
    const performanceModeInput = document.getElementById('performance_mode');

    performancePresets.forEach(preset => {
        preset.addEventListener('click', function () {
            const mode = this.getAttribute('data-mode');

            // Update active state
            performancePresets.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            // Update hidden input
            if (performanceModeInput) {
                performanceModeInput.value = mode;
            }

            // Automatically update checkboxes based on selected mode
            if (includeSession && includeLocation && includeDevice) {
                switch (mode) {
                    case 'fast':
                        // Fast mode: uncheck all
                        includeSession.checked = false;
                        includeLocation.checked = false;
                        includeDevice.checked = false;
                        break;
                    case 'selective':
                        // Selective mode: only session data
                        includeSession.checked = true;
                        includeLocation.checked = false;
                        includeDevice.checked = false;
                        break;
                    case 'full':
                        // Full mode: check all
                        includeSession.checked = true;
                        includeLocation.checked = true;
                        includeDevice.checked = true;
                        break;
                }
            }
        });
    });

    // Status Update Dropdown
    function toggleStatusDropdown(orderId) {
        const dropdown = document.getElementById('status-dropdown-' + orderId);
        const allDropdowns = document.querySelectorAll('.status-dropdown');

        // Close all other dropdowns
        allDropdowns.forEach(d => {
            if (d.id !== 'status-dropdown-' + orderId) {
                d.style.display = 'none';
            }
        });

        // Toggle current dropdown
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Attach event listeners to all status update buttons
    document.querySelectorAll('.btn-status-update').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const orderId = this.getAttribute('data-order-id');
            if (orderId) {
                toggleStatusDropdown(orderId);
            }
        });
    });

    // Auto-submit form when status changes
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function () {
            this.form.submit();
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.status-update-dropdown')) {
            document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    // Performance Settings Form - Update mode when checkboxes change
    const performanceForm = document.getElementById('performanceForm');
    const includeSession = document.getElementById('include_session');
    const includeLocation = document.getElementById('include_location');
    const includeDevice = document.getElementById('include_device');

    if (performanceForm) {
        // When custom settings are changed, update to 'full' mode if all are checked
        [includeSession, includeLocation, includeDevice].forEach(checkbox => {
            if (checkbox) {
                checkbox.addEventListener('change', function () {
                    const allChecked = includeSession?.checked && includeLocation?.checked && includeDevice?.checked;
                    const someChecked = includeSession?.checked || includeLocation?.checked || includeDevice?.checked;

                    if (allChecked) {
                        // Set to full mode
                        if (performanceModeInput) {
                            performanceModeInput.value = 'full';
                        }
                        performancePresets.forEach(p => {
                            p.classList.remove('active');
                            if (p.getAttribute('data-mode') === 'full') {
                                p.classList.add('active');
                            }
                        });
                    } else if (someChecked) {
                        // Set to selective mode
                        if (performanceModeInput) {
                            performanceModeInput.value = 'selective';
                        }
                        performancePresets.forEach(p => {
                            p.classList.remove('active');
                            if (p.getAttribute('data-mode') === 'selective') {
                                p.classList.add('active');
                            }
                        });
                    } else {
                        // Set to fast mode
                        if (performanceModeInput) {
                            performanceModeInput.value = 'fast';
                        }
                        performancePresets.forEach(p => {
                            p.classList.remove('active');
                            if (p.getAttribute('data-mode') === 'fast') {
                                p.classList.add('active');
                            }
                        });
                    }
                });
            }
        });
    }

    // Filter Form - Auto-submit on select change (optional)
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        // You can enable auto-submit on filter change if needed
        // const filterSelects = filterForm.querySelectorAll('.filter-select');
        // filterSelects.forEach(select => {
        //     select.addEventListener('change', function () {
        //         filterForm.submit();
        //     });
        // });
    }

    // Reset performance settings
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('reset_performance') === '1') {
        // Reset checkboxes
        if (includeSession) includeSession.checked = false;
        if (includeLocation) includeLocation.checked = false;
        if (includeDevice) includeDevice.checked = false;

        // Reset to fast mode
        if (performanceModeInput) {
            performanceModeInput.value = 'fast';
        }

        // Update active preset
        performancePresets.forEach(p => {
            p.classList.remove('active');
            if (p.getAttribute('data-mode') === 'fast') {
                p.classList.add('active');
            }
        });
    }
});
