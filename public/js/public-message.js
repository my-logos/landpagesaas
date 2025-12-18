// Public Message Form Handler

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('messageForm');
        if (!form) {
            return;
        }

        // Form validation
        form.addEventListener('submit', function (e) {
            const subject = form.querySelector('#subject');
            const message = form.querySelector('#message');
            const senderName = form.querySelector('#sender_name');

            let isValid = true;

            // Reset previous errors
            form.querySelectorAll('.form-error').forEach(function (error) {
                error.remove();
            });

            // Validate sender name
            if (!senderName.value.trim() || senderName.value.trim().length < 2) {
                showFieldError(senderName, 'Name must be at least 2 characters');
                isValid = false;
            }

            // Validate subject
            if (!subject.value.trim() || subject.value.trim().length < 5) {
                showFieldError(subject, 'Subject must be at least 5 characters');
                isValid = false;
            }

            // Validate message
            if (!message.value.trim() || message.value.trim().length < 10) {
                showFieldError(message, 'Message must be at least 10 characters');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });

        function showFieldError(field, message) {
            const error = document.createElement('span');
            error.className = 'form-error';
            error.textContent = message;
            field.parentElement.appendChild(error);
            field.style.borderColor = '#ef4444';
        }
    });
})();
