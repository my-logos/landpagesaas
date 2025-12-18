// Admin Additional Settings Tabs
(function () {
    function switchSettingsTab(tab) {
        // Update active tab
        document.querySelectorAll('.tab-item').forEach(function (item) {
            item.classList.remove('active');
        });

        // Show/hide content
        document.querySelectorAll('.settings-tab-content').forEach(function (content) {
            content.classList.remove('active');
        });
        const targetContent = document.querySelector('[data-tab-content="' + tab + '"]');
        if (targetContent) {
            targetContent.classList.add('active');
        }
    }

    // Initialize tabs on page load
    document.addEventListener('DOMContentLoaded', function () {
        const tabItems = document.querySelectorAll('.tab-item[data-tab]');
        tabItems.forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                const tabName = tab.getAttribute('data-tab');
                tab.classList.add('active');
                switchSettingsTab(tabName);
            });
        });
    });
})();
