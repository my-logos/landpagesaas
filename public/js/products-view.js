// Products View Toggle
document.addEventListener('DOMContentLoaded', function () {
    // View Toggle (Grid/List)
    const viewButtons = document.querySelectorAll('.view-btn');
    const productsGrid = document.querySelector('.products-grid');

    if (viewButtons.length > 0 && productsGrid) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function () {
                const view = this.getAttribute('data-view');

                // Remove active class from all buttons
                viewButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Update grid class
                productsGrid.className = 'products-grid';
                productsGrid.classList.add(`view-${view}`);
            });
        });
    }

    // Filter Toggle
    const filterToggle = document.getElementById('filterToggle');
    const filterContent = document.getElementById('filterContent');
    const filterChevron = document.getElementById('filterChevron');

    if (filterToggle && filterContent && filterChevron) {
        filterToggle.addEventListener('click', function () {
            const isCollapsed = filterContent.classList.contains('collapsed');

            if (isCollapsed) {
                filterContent.classList.remove('collapsed');
                filterContent.classList.add('show');
                filterChevron.classList.add('rotated');
            } else {
                filterContent.classList.add('collapsed');
                filterContent.classList.remove('show');
                filterChevron.classList.remove('rotated');
            }
        });
    }
});
