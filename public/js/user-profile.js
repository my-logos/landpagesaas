document.addEventListener('DOMContentLoaded', function () {
    // Toggle edit mode
    const editLink = document.querySelector('.edit-link');
    const cancelBtn = document.getElementById('cancelEditBtn');

    if (editLink) {
        editLink.addEventListener('click', function (e) {
            e.preventDefault();
            toggleEdit();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function (e) {
            e.preventDefault();
            cancelEdit();
        });
    }
});

function toggleEdit() {
    const inputs = document.querySelectorAll('.field-input');
    const formActions = document.getElementById('formActions');

    inputs.forEach(function (input) {
        input.removeAttribute('readonly');
        input.style.background = '#fff';
    });

    if (formActions) {
        formActions.classList.add('visible');
    }
}

function cancelEdit() {
    const inputs = document.querySelectorAll('.field-input');
    const formActions = document.getElementById('formActions');

    inputs.forEach(function (input) {
        input.setAttribute('readonly', 'readonly');
        input.style.background = '#f9fafb';
    });

    if (formActions) {
        formActions.classList.remove('visible');
    }
}

// Make functions globally available
window.toggleEdit = toggleEdit;
window.cancelEdit = cancelEdit;

// Reset password form
const resetPasswordFormBtn = document.getElementById('reset-password-form-btn');
if (resetPasswordFormBtn) {
    resetPasswordFormBtn.addEventListener('click', function () {
        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.reset();
        }
    });
}
