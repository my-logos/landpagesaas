document.addEventListener('DOMContentLoaded', function () {
  // Sidebar toggle for small screens
  var toggle = document.querySelector('.ls-toggle-sidebar');
  var sidebar = document.querySelector('.ls-sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('ls-collapsed');
      document.querySelector('.ls-main').classList.toggle('ls-expanded');
    });
  }

  // Language switcher is handled by language-switcher.js

  // Small hover effects for cards
  document.querySelectorAll('.ls-card').forEach(function (el) {
    el.addEventListener('mouseenter', function () { el.classList.add('glow'); });
    el.addEventListener('mouseleave', function () { el.classList.remove('glow'); });
  });

  // Responsive: collapse sidebar on mobile
  function handleResize() {
    if (window.innerWidth <= 576) {
      sidebar && sidebar.classList.add('ls-collapsed-mobile');
    } else {
      sidebar && sidebar.classList.remove('ls-collapsed-mobile');
    }
  }
  handleResize();
  window.addEventListener('resize', handleResize);

  // Toast/Flash Messages System
  function initToastSystem() {
    var container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    function showToast(message, type) {
      var toast = document.createElement('div');
      toast.className = 'toast toast-' + type;

      var icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
      };

      var icon = document.createElement('span');
      icon.className = 'toast-icon';
      icon.textContent = icons[type] || icons.info;

      var content = document.createElement('div');
      content.className = 'toast-content';
      content.textContent = message;

      var close = document.createElement('button');
      close.className = 'toast-close';
      close.textContent = '×';
      close.setAttribute('aria-label', 'Close');
      close.addEventListener('click', function () {
        removeToast(toast);
      });

      toast.appendChild(icon);
      toast.appendChild(content);
      toast.appendChild(close);

      container.appendChild(toast);

      setTimeout(function () {
        removeToast(toast);
      }, 5000);
    }

    function removeToast(toast) {
      toast.classList.add('fade-out');
      setTimeout(function () {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }

    window.showToast = showToast;
  }

  initToastSystem();

  // Auto-show flash messages from session
  var flashMessages = document.querySelectorAll('[data-flash-message]');
  flashMessages.forEach(function (el) {
    var message = el.getAttribute('data-flash-message');
    var type = el.getAttribute('data-flash-type') || 'info';
    if (window.showToast && message && message.trim() !== '') {
      setTimeout(function () {
        window.showToast(message, type);
      }, 100);
    }
    el.remove();
  });
});
