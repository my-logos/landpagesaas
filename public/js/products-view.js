// Products View JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Edit product function
    function editProduct(productId) {
        // Redirect to edit page or show edit modal
        // For now, redirect to create page with product ID
        window.location.href = '/user/products/create?edit=' + productId;
    }

    // Make function globally available
    window.editProduct = editProduct;

    // Attach event listeners to edit buttons
    document.querySelectorAll('.product-edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                editProduct(productId);
            }
        });
    });

    // View toggle functionality
    const viewButtons = document.querySelectorAll('.view-btn');
    const productsContainer = document.getElementById('products-container');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const view = this.getAttribute('data-view');
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (productsContainer) {
                if (view === 'list') {
                    productsContainer.style.gridTemplateColumns = '1fr';
                } else {
                    productsContainer.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
                }
            }
        });
    });

    // Filter toggle
    const filterToggle = document.getElementById('filterToggle');
    const filterContent = document.getElementById('filterContent');
    const filterChevron = document.getElementById('filterChevron');

    if (filterToggle) {
        filterToggle.addEventListener('click', function () {
            filterContent.classList.toggle('collapsed');
            filterChevron.classList.toggle('rotated');
        });
    }

    // Clear Filters
    const clearFiltersBtn = document.querySelector('.btn-clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function () {
            const form = document.getElementById('filterForm');
            if (form) {
                form.reset();
                const productsIndexRoute = form.action || window.location.pathname;
                window.location.href = productsIndexRoute;
            }
        });
    }
});
