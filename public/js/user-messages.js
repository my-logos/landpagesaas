// User Messages Scripts

(function () {
    'use strict';

    // Order Modal Management
    function openOrderModal(orderId) {
        const modal = document.getElementById('orderModal');
        const modalBody = document.getElementById('orderModalBody');

        if (!modal || !modalBody) {
            return;
        }

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Show loading
        modalBody.innerHTML = `
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Loading...</p>
            </div>
        `;

        // Fetch order details
        fetch(`/user/messages/order/${orderId}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.order) {
                    renderOrderDetails(data.order);
                } else {
                    modalBody.innerHTML = `
                        <div class="error-message">
                            <i class="fa-solid fa-exclamation-circle"></i>
                            <p>Failed to load order details</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading order details:', error);
                modalBody.innerHTML = `
                    <div class="error-message">
                        <i class="fa-solid fa-exclamation-circle"></i>
                        <p>An error occurred while loading order details</p>
                    </div>
                `;
            });
    }

    function renderOrderDetails(order) {
        const modalBody = document.getElementById('orderModalBody');
        if (!modalBody) {
            return;
        }

        const locale = document.documentElement.lang || 'en';
        const isRTL = document.documentElement.dir === 'rtl' || locale === 'ar';

        // Prepare customer data HTML
        let customerDataHTML = '';
        if (order.customer_data) {
            const excludedKeys = ['size', 'color', 'ip', 'session_id', 'city', 'device_type'];
            const customerFields = Object.entries(order.customer_data).filter(([key]) => !excludedKeys.includes(key));

            if (customerFields.length > 0) {
                customerDataHTML = `
                    <div class="order-detail-section">
                        <h3><i class="fa-solid fa-user"></i> ${locale === 'ar' ? 'بيانات العميل' : 'Customer Information'}</h3>
                        <div class="order-detail-grid">
                            ${customerFields.map(([key, value]) => {
                    const label = locale === 'ar'
                        ? key.replace(/_/g, ' ').replace('name', 'الاسم').replace('phone', 'الهاتف').replace('address', 'العنوان').replace('email', 'البريد')
                        : key.replace(/_/g, ' ');
                    return `
                                    <div class="detail-item">
                                        <label>${label}:</label>
                                        <span>${value || '-'}</span>
                                    </div>
                                `;
                }).join('')}
                        </div>
                    </div>
                `;
            }
        }

        // Status translations
        const statusTranslations = {
            ar: {
                pending: 'قيد الانتظار',
                processing: 'قيد المعالجة',
                shipped: 'تم الشحن',
                delivered: 'تم التوصيل',
                rejected: 'مرفوض',
                failed_delivery: 'فشل التوصيل',
                postponed: 'مؤجل'
            },
            en: {
                pending: 'Pending',
                processing: 'Processing',
                shipped: 'Shipped',
                delivered: 'Delivered',
                rejected: 'Rejected',
                failed_delivery: 'Failed Delivery',
                postponed: 'Postponed'
            }
        };

        const t = statusTranslations[locale] || statusTranslations.en;
        const statusText = t[order.status] || order.status;
        const shippingStatusText = t[order.shipping_status] || order.shipping_status;

        modalBody.innerHTML = `
            <div class="order-details-content">
                <div class="order-detail-section">
                    <h3><i class="fa-solid fa-info-circle"></i> ${locale === 'ar' ? 'معلومات الطلب' : 'Order Information'}</h3>
                    <div class="order-detail-grid">
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'رقم الطلب' : 'Order Number'}:</label>
                            <span><strong>#${order.order_number || order.id}</strong></span>
                        </div>
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'التاريخ' : 'Date'}:</label>
                            <span>${order.created_at}</span>
                        </div>
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'المنتج' : 'Product'}:</label>
                            <span>${order.product_name}</span>
                        </div>
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'الكمية' : 'Quantity'}:</label>
                            <span>${order.quantity}</span>
                        </div>
                        ${order.size ? `
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'المقاس' : 'Size'}:</label>
                            <span>${order.size}</span>
                        </div>
                        ` : ''}
                        ${order.color ? `
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'اللون' : 'Color'}:</label>
                            <span>${order.color}</span>
                        </div>
                        ` : ''}
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'المجموع' : 'Total'}:</label>
                            <span><strong>${order.total} ${order.currency}</strong></span>
                        </div>
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'حالة الطلب' : 'Order Status'}:</label>
                            <span class="status-badge status-${order.status}">${statusText}</span>
                        </div>
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'حالة الشحن' : 'Shipping Status'}:</label>
                            <span class="status-badge status-${order.shipping_status}">${shippingStatusText}</span>
                        </div>
                        ${order.landing_page ? `
                        <div class="detail-item">
                            <label>${locale === 'ar' ? 'صفحة الهبوط' : 'Landing Page'}:</label>
                            <span>${order.landing_page}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ${customerDataHTML}
            </div>
        `;
    }

    function closeOrderModal() {
        const modal = document.getElementById('orderModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Toggle between reply and new message forms
    function toggleMessageForm() {
        const toggleBtn = document.getElementById('toggleMessageType');
        const replyForm = document.getElementById('replyForm');
        const newMessageForm = document.getElementById('newMessageForm');

        if (!toggleBtn || !replyForm || !newMessageForm) {
            return;
        }

        toggleBtn.addEventListener('click', function () {
            if (replyForm.style.display === 'none') {
                // Show reply form
                replyForm.style.display = 'block';
                newMessageForm.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fa-solid fa-exchange-alt"></i> ' + (document.documentElement.lang === 'ar' ? 'إرسال رسالة جديدة للعميل' : 'Send New Message to Customer');
            } else {
                // Show new message form
                replyForm.style.display = 'none';
                newMessageForm.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fa-solid fa-exchange-alt"></i> ' + (document.documentElement.lang === 'ar' ? 'الرد على الرسالة الحالية' : 'Reply to Current Message');
            }
        });
    }

    function cancelNewMessage() {
        const cancelBtn = document.getElementById('cancelNewMessage');
        const toggleBtn = document.getElementById('toggleMessageType');
        const replyForm = document.getElementById('replyForm');
        const newMessageForm = document.getElementById('newMessageForm');

        if (!cancelBtn || !toggleBtn || !replyForm || !newMessageForm) {
            return;
        }

        cancelBtn.addEventListener('click', function () {
            replyForm.style.display = 'block';
            newMessageForm.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fa-solid fa-exchange-alt"></i> ' + (document.documentElement.lang === 'ar' ? 'إرسال رسالة جديدة للعميل' : 'Send New Message to Customer');
            // Reset form
            newMessageForm.reset();
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle message form
        toggleMessageForm();
        cancelNewMessage();

        // Open order modal buttons
        document.querySelectorAll('.btn-view-order-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                const orderId = this.getAttribute('data-order-id');
                if (orderId) {
                    openOrderModal(orderId);
                }
            });
        });

        // Close modal buttons
        const closeBtn = document.getElementById('closeOrderModal');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeOrderModal);
        }

        // Close modal on overlay click
        const modal = document.getElementById('orderModal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeOrderModal();
                }
            });
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeOrderModal();
            }
        });
    });
})();
