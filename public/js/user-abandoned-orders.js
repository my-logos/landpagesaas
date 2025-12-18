// User Abandoned Orders Page JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Auto-submit search form on input
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length > 2 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }

    // Show filter dropdown (if needed in future)
    const btnShowFilter = document.querySelector('.btn-show-filter');
    if (btnShowFilter) {
        btnShowFilter.addEventListener('click', function () {
            // Toggle filter dropdown if needed
        });
    }

    // Filter button - submit form
    const btnFilterAbandoned = document.querySelector('.btn-filter-abandoned[data-submit-form]');
    if (btnFilterAbandoned) {
        btnFilterAbandoned.addEventListener('click', function () {
            const formSelector = this.getAttribute('data-submit-form');
            const form = document.querySelector(formSelector);
            if (form) {
                form.submit();
            }
        });
    }
});
