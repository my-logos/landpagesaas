// Page Loading Bar
(function() {
    const loadingBar = document.createElement('div');
    loadingBar.className = 'page-loading-bar';
    document.body.appendChild(loadingBar);

    // Show loading bar on page navigation
    let loadingTimeout;
    document.addEventListener('DOMContentLoaded', function() {
        loadingBar.classList.add('loading');
        
        window.addEventListener('beforeunload', function() {
            loadingBar.classList.add('loading');
        });
    });

    // Complete loading when page is fully loaded
    window.addEventListener('load', function() {
        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(function() {
            loadingBar.classList.remove('loading');
            loadingBar.classList.add('complete');
            setTimeout(function() {
                loadingBar.style.width = '0%';
                loadingBar.classList.remove('complete');
            }, 500);
        }, 300);
    });

    // Handle AJAX requests
    let activeRequests = 0;
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        activeRequests++;
        loadingBar.classList.add('loading');
        
        return originalFetch.apply(this, args)
            .finally(function() {
                activeRequests--;
                if (activeRequests === 0) {
                    setTimeout(function() {
                        loadingBar.classList.remove('loading');
                        loadingBar.classList.add('complete');
                        setTimeout(function() {
                            loadingBar.style.width = '0%';
                            loadingBar.classList.remove('complete');
                        }, 500);
                    }, 300);
                }
            });
    };
})();

// Pre-loader (before page opens)
(function() {
    const preLoader = document.createElement('div');
    preLoader.className = 'pre-loader';
    preLoader.innerHTML = `
        <div class="pre-loader-content">
            <div class="pre-loader-spinner"></div>
            <div class="pre-loader-text">Loading...</div>
        </div>
    `;
    document.body.appendChild(preLoader);

    window.addEventListener('load', function() {
        setTimeout(function() {
            preLoader.classList.add('hidden');
            setTimeout(function() {
                preLoader.remove();
            }, 500);
        }, 500);
    });
})();
