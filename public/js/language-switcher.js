// Language Dropdown Toggle
document.addEventListener('DOMContentLoaded', function () {
    const languageDropdowns = document.querySelectorAll('.language-dropdown');

    languageDropdowns.forEach(function (dropdown) {
        const toggle = dropdown.querySelector('.language-dropdown-toggle');

        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Close other dropdowns
                languageDropdowns.forEach(function (other) {
                    if (other !== dropdown) {
                        other.classList.remove('active');
                    }
                });

                // Toggle current dropdown
                dropdown.classList.toggle('active');
            });
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.language-dropdown')) {
            languageDropdowns.forEach(function (dropdown) {
                dropdown.classList.remove('active');
            });
        }
    });
});
