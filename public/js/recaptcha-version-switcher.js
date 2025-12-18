// reCAPTCHA Version Switcher
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const versionSelect = document.getElementById('recaptcha_version');
        const versionFields = document.querySelectorAll('.recaptcha-version-fields');

        if (!versionSelect) {
            return;
        }

        function toggleVersionFields(selectedVersion) {
            versionFields.forEach(function (field) {
                if (field.getAttribute('data-version') === selectedVersion) {
                    field.style.display = 'block';
                } else {
                    field.style.display = 'none';
                }
            });
        }

        // Initialize on page load - check data-initial-display first
        versionFields.forEach(function (field) {
            const initialDisplay = field.getAttribute('data-initial-display');
            if (initialDisplay) {
                field.style.display = initialDisplay;
            }
        });
        toggleVersionFields(versionSelect.value);

        // Handle version change
        versionSelect.addEventListener('change', function () {
            toggleVersionFields(this.value);
        });
    });
})();
