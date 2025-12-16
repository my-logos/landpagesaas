// Facebook Conversion API - User Page Scripts

(function() {
    'use strict';

    function toggleInfoSection() {
        const header = document.querySelector('.info-header');
        const container = document.querySelector('.info-cards-container');
        
        if (header && container) {
            header.classList.toggle('collapsed');
            container.classList.toggle('hidden');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener to info header if it exists
        const infoHeader = document.querySelector('.info-header[data-toggle-info]');
        if (infoHeader) {
            infoHeader.addEventListener('click', toggleInfoSection);
        }
    });
})();
