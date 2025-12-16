/**
 * User Dashboard Progress Bars JavaScript
 * Initializes progress bars with data-width attributes
 */
/* eslint-env browser */
/* global document */
(function () {
    'use strict';

    class DashboardProgress {
        constructor() {
            this.init();
        }

        init() {
            // Initialize progress bars from data-width attributes
            const progressBars = document.querySelectorAll('.progress-bar[data-width]');
            progressBars.forEach(bar => {
                const width = parseFloat(bar.dataset.width);
                if (!isNaN(width) && width >= 0 && width <= 100) {
                    bar.style.width = `${width}%`;
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new DashboardProgress();
        });
    } else {
        new DashboardProgress();
    }
})();





