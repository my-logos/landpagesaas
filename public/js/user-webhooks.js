// Webhooks Management - User Page Scripts

(function () {
    'use strict';

    // Modal Management
    function openModal(webhookId = null) {
        const modal = document.getElementById('webhook-modal');
        const form = document.getElementById('webhook-form');
        const title = document.getElementById('modal-title');
        const submitText = document.getElementById('submit-text');

        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            if (webhookId) {
                // Edit mode
                title.textContent = document.querySelector('[data-edit-webhook="' + webhookId + '"]')?.getAttribute('title') || 'Edit Webhook';
                submitText.textContent = 'Update Webhook';
                loadWebhookData(webhookId);
            } else {
                // Add mode
                title.textContent = 'Add New Webhook';
                submitText.textContent = 'Create Webhook';
                form.reset();
                document.getElementById('webhook-id').value = '';
            }
        }
    }

    function closeModal() {
        const modal = document.getElementById('webhook-modal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function loadWebhookData(webhookId) {
        // Fetch webhook data from API
        fetch(`/user/webhooks/${webhookId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.webhook) {
                    document.getElementById('webhook-name').value = data.webhook.name || '';
                    document.getElementById('webhook-url').value = data.webhook.url || '';
                    document.getElementById('webhook-id').value = webhookId;
                    if (data.webhook.secret) {
                        document.getElementById('webhook-secret').value = data.webhook.secret;
                    }

                    // Set checked events
                    const events = data.webhook.events || [];
                    document.querySelectorAll('input[name="events[]"]').forEach(cb => {
                        cb.checked = events.includes(cb.value);
                    });
                }
            })
            .catch(error => {
                // Silently handle error - webhook data loading failed
            });
    }

    // Toggle Info Section
    function toggleInfoSection() {
        const header = document.querySelector('.webhooks-info-header');
        const content = document.querySelector('.webhooks-info-content');

        if (header && content) {
            header.classList.toggle('collapsed');
            content.classList.toggle('hidden');
        }
    }

    // Form Submission
    function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const webhookId = formData.get('webhook_id');
        const url = webhookId
            ? `/user/webhooks/${webhookId}`
            : '/user/webhooks';
        const method = webhookId ? 'PUT' : 'POST';

        // Get checked events
        const events = Array.from(form.querySelectorAll('input[name="events[]"]:checked'))
            .map(cb => cb.value);

        if (events.length === 0) {
            if (window.showToast) {
                window.showToast('Please select at least one event', 'warning');
            } else {
                alert('Please select at least one event');
            }
            return;
        }

        const data = {
            name: formData.get('name'),
            url: formData.get('url'),
            events: events,
            secret: formData.get('secret') || null,
        };

        if (webhookId) {
            data._method = 'PUT';
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    if (window.showToast) {
                        window.showToast(data.message || 'An error occurred', 'error');
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                if (window.showToast) {
                    window.showToast('An error occurred while saving the webhook', 'error');
                } else {
                    alert('An error occurred while saving the webhook');
                }
            });
    }

    // Delete Webhook
    function deleteWebhook(webhookId) {
        if (!confirm('Are you sure you want to delete this webhook?')) {
            return;
        }

        fetch(`/user/webhooks/${webhookId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    if (window.showToast) {
                        window.showToast(data.message || 'An error occurred', 'error');
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                if (window.showToast) {
                    window.showToast('An error occurred while deleting the webhook', 'error');
                } else {
                    alert('An error occurred while deleting the webhook');
                }
            });
    }

    // Toggle Webhook Status
    function toggleWebhook(webhookId, isActive) {
        fetch(`/user/webhooks/${webhookId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    if (window.showToast) {
                        window.showToast(data.message || 'An error occurred', 'error');
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                if (window.showToast) {
                    window.showToast('An error occurred while updating the webhook', 'error');
                } else {
                    alert('An error occurred while updating the webhook');
                }
            });
    }

    // Test Webhook
    function testWebhook(webhookId) {
        fetch(`/user/webhooks/${webhookId}/test`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.showToast) {
                        window.showToast('Test event sent successfully!', 'success');
                    } else {
                        alert('Test event sent successfully!');
                    }
                } else {
                    if (window.showToast) {
                        window.showToast(data.message || 'An error occurred', 'error');
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                if (window.showToast) {
                    window.showToast('An error occurred while testing the webhook', 'error');
                } else {
                    alert('An error occurred while testing the webhook');
                }
            });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Modal open/close
        document.querySelectorAll('[data-open-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                const webhookId = btn.getAttribute('data-webhook-id');
                openModal(webhookId);
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        // Info section toggle
        const infoHeader = document.querySelector('.webhooks-info-header[data-toggle-info]');
        if (infoHeader) {
            infoHeader.addEventListener('click', toggleInfoSection);
        }

        // Form submission
        const form = document.getElementById('webhook-form');
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
        }

        // Edit webhook
        document.querySelectorAll('[data-edit-webhook]').forEach(btn => {
            btn.addEventListener('click', () => {
                const webhookId = btn.getAttribute('data-edit-webhook');
                openModal(webhookId);
            });
        });

        // Delete webhook
        document.querySelectorAll('[data-delete-webhook]').forEach(btn => {
            btn.addEventListener('click', () => {
                const webhookId = btn.getAttribute('data-delete-webhook');
                deleteWebhook(webhookId);
            });
        });

        // Toggle webhook
        document.querySelectorAll('[data-webhook-id]').forEach(btn => {
            if (btn.classList.contains('btn-toggle-webhook')) {
                btn.addEventListener('click', () => {
                    const webhookId = btn.getAttribute('data-webhook-id');
                    const isActive = btn.getAttribute('data-is-active') === '1';
                    toggleWebhook(webhookId, isActive);
                });
            }
        });

        // Test webhook
        document.querySelectorAll('[data-test-webhook]').forEach(btn => {
            btn.addEventListener('click', () => {
                const webhookId = btn.getAttribute('data-test-webhook');
                testWebhook(webhookId);
            });
        });
    });
})();

