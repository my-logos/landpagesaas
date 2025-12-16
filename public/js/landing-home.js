document.addEventListener('DOMContentLoaded', function () {
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var href = this.getAttribute('href');
      if (href !== '#' && href !== '') {
        e.preventDefault();
        var target = document.querySelector(href);
        if (target) {
          var offsetTop = target.offsetTop - 80;
          window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
          });
        }
      }
    });
  });

  // FAQ Accordion
  var faqItems = document.querySelectorAll('.lp-faq-item');
  faqItems.forEach(function (item) {
    var question = item.querySelector('.lp-faq-question');
    if (question) {
      question.addEventListener('click', function () {
        var isActive = item.classList.contains('active');
        // Close all items
        faqItems.forEach(function (otherItem) {
          otherItem.classList.remove('active');
        });
        // Toggle current item
        if (!isActive) {
          item.classList.add('active');
        }
      });
    }
  });

  // Pricing Toggle
  var pricingToggle = document.getElementById('pricingToggle');
  var priceAmounts = document.querySelectorAll('.lp-price-amount');
  if (pricingToggle) {
    pricingToggle.addEventListener('change', function () {
      var isAnnual = this.checked;
      priceAmounts.forEach(function (amount) {
        var monthlyPrice = amount.getAttribute('data-monthly');
        var annualPrice = amount.getAttribute('data-annually');
        if (monthlyPrice && annualPrice) {
          if (isAnnual) {
            amount.innerHTML = annualPrice + '<span class="lp-price-period">/year</span>';
          } else {
            amount.innerHTML = monthlyPrice + '<span class="lp-price-period">/month</span>';
          }
        }
      });
    });
  }

  // Scroll Animations
  var observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, observerOptions);

  // Observe elements for scroll animations
  var animatedElements = document.querySelectorAll('.lp-platform-card, .lp-stat-card, .lp-story-card');
  animatedElements.forEach(function (el) {
    el.classList.add('lp-fade-in');
    observer.observe(el);
  });

  // Old topbar removed - using lp-topbar-new

  // New topbar scroll effect
  var topbarNew = document.querySelector('.lp-topbar-new');
  if (topbarNew) {
    window.addEventListener('scroll', function () {
      var currentScroll = window.pageYOffset;
      if (currentScroll > 50) {
        topbarNew.style.background = 'rgba(255, 255, 255, 0.95)';
        topbarNew.style.backdropFilter = 'blur(10px)';
        topbarNew.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';
      } else {
        topbarNew.style.background = 'transparent';
        topbarNew.style.backdropFilter = 'none';
        topbarNew.style.boxShadow = 'none';
      }
    });
  }

  // Hero input focus effect
  var productInput = document.getElementById('productNameInput');
  if (productInput) {
    productInput.addEventListener('focus', function () {
      this.parentElement.style.transform = 'scale(1.02)';
    });
    productInput.addEventListener('blur', function () {
      this.parentElement.style.transform = 'scale(1)';
    });
  }

  // Old hero media removed - using lp-hero-media-new

  // Parallax effect for new hero illustration
  var heroIllustration = document.querySelector('.lp-hero-illustration-wrapper');
  if (heroIllustration) {
    window.addEventListener('scroll', function () {
      var scrolled = window.pageYOffset;
      var rate = scrolled * 0.3;
      if (scrolled < window.innerHeight) {
        heroIllustration.style.transform = 'translateY(' + rate + 'px)';
      }
    });
  }

  // Hover effects handled by CSS transitions

  // Animated Product Names in Hero Section
  var animatedProductName = document.getElementById('animatedProductName');
  var productInput = document.getElementById('productNameInput');

  if (animatedProductName && productInput) {
    var locale = animatedProductName.getAttribute('data-locale');

    // Get words from search box data-words attribute (separate from title)
    var wordsData = animatedProductName.getAttribute('data-words');

    var products = [];
    if (wordsData && wordsData.trim() !== '') {
      products = wordsData.split(',').map(function (word) {
        return word.trim();
      }).filter(function (word) {
        return word !== '';
      });
    }

    // Fallback to default words if no data-words attribute
    if (products.length === 0) {
      if (locale === 'ar') {
        products = ['ساعة ذكية', 'عطور', 'سماعات لاسلكية'];
      } else {
        products = ['smart watch', 'perfumes', 'wireless headphones'];
      }
    }
    var currentIndex = 0;
    var isTyping = false;
    var animationInterval;

    function animateProductName() {
      // Don't animate if input has value or is focused
      if (isTyping || productInput.value.trim() !== '' || document.activeElement === productInput) {
        return;
      }
      isTyping = true;

      var currentProduct = products[currentIndex];
      var element = animatedProductName;

      // Fade out
      element.style.opacity = '0';
      element.style.transform = 'translateY(10px)';

      setTimeout(function () {
        // Change text
        element.textContent = currentProduct;

        // Fade in (only if input is still empty and not focused)
        if (productInput.value.trim() === '' && document.activeElement !== productInput) {
          element.style.opacity = '1';
          element.style.transform = 'translateY(0)';
        }

        // Move to next product
        currentIndex = (currentIndex + 1) % products.length;
        isTyping = false;
      }, 300);
    }

    // Initial display
    if (products.length > 0) {
      animatedProductName.textContent = products[0];
      currentIndex = 1;
    }

    // Hide animated text when user types
    productInput.addEventListener('input', function () {
      if (this.value.trim() !== '') {
        animatedProductName.style.opacity = '0';
      } else {
        animatedProductName.style.opacity = '1';
      }
    });

    // Hide animated text on focus, show on blur if empty
    productInput.addEventListener('focus', function () {
      animatedProductName.style.opacity = '0';
    });

    productInput.addEventListener('blur', function () {
      if (this.value.trim() === '') {
        animatedProductName.style.opacity = '1';
      }
    });

    // Start animation every 3 seconds
    animationInterval = setInterval(animateProductName, 3000);
  }

  // Animated Title Text in Hero Section
  var animatedTitleText = document.getElementById('animatedTitleText');
  if (animatedTitleText) {
    var titleLocale = animatedTitleText.getAttribute('data-locale');
    var wordsData = animatedTitleText.getAttribute('data-words');

    // Get words from data-words attribute or use defaults
    var titles = [];
    if (wordsData && wordsData.trim() !== '') {
      titles = wordsData.split(',').map(function (word) {
        return word.trim();
      }).filter(function (word) {
        return word !== '';
      });
    }

    // Fallback to default words if no data-words attribute
    if (titles.length === 0) {
      if (titleLocale === 'ar') {
        titles = ['ساعة ذكية', 'منتج رقمي', 'لعبة'];
      } else {
        titles = ['smart watch', 'digital product', 'game'];
      }
    }
    var currentTitleIndex = 0;
    var isTitleAnimating = false;

    function animateTitleText() {
      if (isTitleAnimating) return;
      isTitleAnimating = true;

      var currentTitle = titles[currentTitleIndex];
      var element = animatedTitleText;

      // Fade out
      element.style.opacity = '0';
      element.style.transform = 'translateY(20px)';

      setTimeout(function () {
        // Change text
        element.textContent = currentTitle;

        // Fade in
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';

        // Move to next title
        currentTitleIndex = (currentTitleIndex + 1) % titles.length;
        isTitleAnimating = false;
      }, 400);
    }

    // Initial display
    if (titles.length > 0) {
      animatedTitleText.textContent = titles[0];
      animatedTitleText.style.opacity = '1';
      animatedTitleText.style.transform = 'translateY(0)';
      currentTitleIndex = 1;
    }

    // Start animation every 4 seconds (slightly longer than product names)
    setInterval(animateTitleText, 4000);
  }

});
