/**
 * Disable console methods in production
 * This file should be included before other JavaScript files in production
 */
(function () {
    'use strict';

    // Check if we're in production (APP_ENV=production)
    var isProduction = false;
    if (document.body && document.body.dataset.env) {
        isProduction = document.body.dataset.env === 'production';
    }

    // If production, disable console methods
    if (isProduction) {
        // Override console methods with no-op functions
        if (typeof console !== 'undefined') {
            console.log = function () { };
            console.debug = function () { };
            console.info = function () { };
            // Keep console.error and console.warn for critical errors, but make them silent
            // Or uncomment to disable them completely:
            // console.error = function() {};
            // console.warn = function() {};
        }
    }
})();

