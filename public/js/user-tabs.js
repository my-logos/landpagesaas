document.addEventListener('DOMContentLoaded', function () {
    // Handle tab switching
    const tabItems = document.querySelectorAll('.tab-item[data-tab]');

    tabItems.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();

            // Remove active class from all tabs in the same container
            const tabContainer = tab.closest('.profile-tabs, .transactions-tabs');
            if (tabContainer) {
                tabContainer.querySelectorAll('.tab-item').forEach(function (t) {
                    t.classList.remove('active');
                });
            }

            // Add active class to clicked tab
            tab.classList.add('active');

            // Handle tab content switching
            const tabName = tab.getAttribute('data-tab');
            const tabContents = document.querySelectorAll('[data-tab-content]');

            tabContents.forEach(function (content) {
                if (content.getAttribute('data-tab-content') === tabName) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        });
    });

    // Initialize: show first tab content if exists
    const firstTab = document.querySelector('.tab-item.active[data-tab]');
    if (firstTab) {
        const firstTabName = firstTab.getAttribute('data-tab');
        const tabContents = document.querySelectorAll('[data-tab-content]');
        tabContents.forEach(function (content) {
            if (content.getAttribute('data-tab-content') === firstTabName) {
                content.classList.add('active');
            } else {
                content.classList.remove('active');
            }
        });
    }
});
