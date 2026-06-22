(function () {
  'use strict';

  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  if ('loading' in HTMLImageElement.prototype) {
    document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
      if (img.dataset.src) {
        img.src = img.dataset.src;
      }
    });
  }

  // Hero slider
  var hero = document.querySelector('.qt-hero[data-slides]');
  if (hero) {
    var slideCount = parseInt(hero.getAttribute('data-slides'), 10);
    if (slideCount > 1) {
      var slides = hero.querySelectorAll('.qt-hero__slide');
      var dots = hero.querySelectorAll('.qt-hero__progress-dot');
      var counter = hero.querySelector('.qt-hero__counter-current');
      var prevBtn = hero.querySelector('.qt-hero__arrow--prev');
      var nextBtn = hero.querySelector('.qt-hero__arrow--next');
      var current = 0;
      var duration = 6000;
      var timer;

      function goToSlide(index) {
        slides[current].classList.remove('qt-hero__slide--active');
        slides[current].setAttribute('aria-hidden', 'true');
        var oldLinks = slides[current].querySelectorAll('a, button');
        oldLinks.forEach(function (l) { l.setAttribute('tabindex', '-1'); });
        if (dots[current]) {
          dots[current].classList.remove('qt-hero__progress-dot--active');
          var bar = dots[current].querySelector('.qt-hero__progress-bar');
          if (bar) bar.style.width = '0';
        }

        current = ((index % slideCount) + slideCount) % slideCount;

        slides[current].classList.add('qt-hero__slide--active');
        slides[current].setAttribute('aria-hidden', 'false');
        var newLinks = slides[current].querySelectorAll('a, button');
        newLinks.forEach(function (l) { l.removeAttribute('tabindex'); });
        if (dots[current]) {
          dots[current].classList.add('qt-hero__progress-dot--active');
          var activeBar = dots[current].querySelector('.qt-hero__progress-bar');
          if (activeBar) {
            activeBar.style.width = '0';
            activeBar.offsetWidth; // force reflow
            activeBar.style.animation = 'none';
            activeBar.offsetWidth;
            activeBar.style.animation = '';
          }
        }
        if (counter) {
          counter.textContent = String(current + 1).padStart(2, '0');
        }
      }

      function startAutoplay() {
        clearInterval(timer);
        timer = setInterval(function () {
          goToSlide(current + 1);
        }, duration);
      }

      dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
          goToSlide(parseInt(this.getAttribute('data-slide'), 10));
          startAutoplay();
        });
      });

      if (prevBtn) {
        prevBtn.addEventListener('click', function () {
          goToSlide(current - 1);
          startAutoplay();
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', function () {
          goToSlide(current + 1);
          startAutoplay();
        });
      }

      startAutoplay();
    }
  }

  // Resource filters
  document.querySelectorAll('[data-component="resource-filters"]').forEach(function (container) {
    var buttons = container.querySelectorAll('.qt-resources-filter');
    var cards = document.querySelectorAll('.qt-resource-card[data-category]');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var filter = this.getAttribute('data-filter');

        buttons.forEach(function (b) { b.classList.remove('qt-resources-filter--active'); });
        this.classList.add('qt-resources-filter--active');

        cards.forEach(function (card) {
          if (filter === 'all' || card.getAttribute('data-category') === filter) {
            card.classList.remove('qt-resource-card--hidden');
          } else {
            card.classList.add('qt-resource-card--hidden');
          }
        });
      });
    });
  });

  // Product tabs
  document.querySelectorAll('[data-component="tabs"]').forEach(function (tabsEl) {
    var buttons = tabsEl.querySelectorAll('.qt-tabs__btn');
    var panels = tabsEl.querySelectorAll('.qt-tabs__panel');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var tabId = this.getAttribute('data-tab');

        buttons.forEach(function (b) {
          b.classList.remove('qt-tabs__btn--active');
          b.setAttribute('aria-selected', 'false');
        });
        this.classList.add('qt-tabs__btn--active');
        this.setAttribute('aria-selected', 'true');

        panels.forEach(function (panel) {
          if (panel.getAttribute('data-tab-panel') === tabId) {
            panel.classList.add('qt-tabs__panel--active');
            panel.removeAttribute('hidden');
          } else {
            panel.classList.remove('qt-tabs__panel--active');
            panel.setAttribute('hidden', '');
          }
        });
      });
    });
  });

  // AJAX live search
  document.querySelectorAll('[data-component="ajax-search"]').forEach(function (form) {
    var input = form.querySelector('input[type="search"]');
    var resultsEl = form.querySelector('.qt-search-results');
    if (!input || !resultsEl) return;

    var debounceTimer;
    var currentXhr;

    function doSearch(query) {
      if (currentXhr) currentXhr.abort();

      if (query.length < 2) {
        resultsEl.innerHTML = '';
        resultsEl.hidden = true;
        return;
      }

      resultsEl.hidden = false;
      resultsEl.innerHTML = '<div class="qt-search-results__loading">Searching...</div>';

      currentXhr = new XMLHttpRequest();
      currentXhr.open('GET', questSearch.ajaxUrl + '?action=quest_search&q=' + encodeURIComponent(query));
      currentXhr.onload = function () {
        if (currentXhr.status !== 200) return;
        var data;
        try { data = JSON.parse(currentXhr.responseText); } catch (e) { return; }
        renderResults(data, query);
      };
      currentXhr.send();
    }

    function renderResults(data, query) {
      if (!data.results || data.results.length === 0) {
        resultsEl.innerHTML = '<div class="qt-search-results__empty">No products found for "' + escHtml(query) + '"</div>';
        return;
      }

      var html = '<ul class="qt-search-results__list">';
      data.results.forEach(function (item) {
        html += '<li class="qt-search-results__item">';
        html += '<a href="' + item.url + '" class="qt-search-results__link">';
        html += '<img src="' + item.image + '" alt="" class="qt-search-results__img">';
        html += '<div class="qt-search-results__info">';
        html += '<span class="qt-search-results__name">' + escHtml(item.name) + '</span>';
        if (item.sku) {
          html += '<span class="qt-search-results__sku">SKU: ' + escHtml(item.sku) + '</span>';
        }
        if (item.category) {
          html += '<span class="qt-search-results__cat">' + escHtml(item.category) + '</span>';
        }
        html += '</div>';
        html += '</a></li>';
      });
      html += '</ul>';

      if (data.total > data.results.length) {
        html += '<a href="' + questSearch.shopUrl + '?s=' + encodeURIComponent(query) + '&post_type=product" class="qt-search-results__view-all">';
        html += 'View all ' + data.total + ' results';
        html += '</a>';
      }

      resultsEl.innerHTML = html;
    }

    function escHtml(str) {
      var el = document.createElement('span');
      el.textContent = str;
      return el.innerHTML;
    }

    input.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      var val = this.value.trim();
      debounceTimer = setTimeout(function () {
        doSearch(val);
      }, 300);
    });

    input.addEventListener('focus', function () {
      if (this.value.trim().length >= 2 && resultsEl.innerHTML) {
        resultsEl.hidden = false;
      }
    });

    document.addEventListener('click', function (e) {
      if (!form.contains(e.target)) {
        resultsEl.hidden = true;
      }
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        resultsEl.hidden = true;
        input.blur();
      }
    });
  });

  // Account dropdown toggle
  var accountToggle = document.querySelector('.js-account-toggle');
  if (accountToggle) {
    var dropdown = document.getElementById(accountToggle.getAttribute('aria-controls'));

    accountToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', !open);
      dropdown.hidden = open;
    });

    document.addEventListener('click', function (e) {
      if (!accountToggle.parentElement.contains(e.target)) {
        accountToggle.setAttribute('aria-expanded', 'false');
        dropdown.hidden = true;
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !dropdown.hidden) {
        accountToggle.setAttribute('aria-expanded', 'false');
        dropdown.hidden = true;
      }
    });
  }

  // Auth tabs (login / register toggle)
  document.querySelectorAll('[data-component="auth-tabs"]').forEach(function (container) {
    var tabs = container.querySelectorAll('.qt-auth__tab');
    var panels = container.querySelectorAll('.qt-auth__panel');
    var switchBtns = container.querySelectorAll('.qt-auth__switch-btn');

    function switchTo(tabId) {
      tabs.forEach(function (t) {
        t.classList.toggle('qt-auth__tab--active', t.getAttribute('data-tab') === tabId);
      });
      panels.forEach(function (p) {
        p.classList.toggle('qt-auth__panel--active', p.getAttribute('data-panel') === tabId);
      });
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        switchTo(this.getAttribute('data-tab'));
      });
    });

    switchBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        switchTo(this.getAttribute('data-tab'));
      });
    });
  });

  // Product carousel (prev/next within tabs)
  document.querySelectorAll('[data-component="carousel"]').forEach(function (carousel) {
    var track = carousel.querySelector('.qt-tabs__carousel-track');
    var prevBtn = carousel.querySelector('[data-dir="prev"]');
    var nextBtn = carousel.querySelector('[data-dir="next"]');

    if (!prevBtn || !nextBtn) return;

    nextBtn.addEventListener('click', function () {
      track.classList.toggle('qt-tabs__carousel--shifted');
    });

    prevBtn.addEventListener('click', function () {
      track.classList.toggle('qt-tabs__carousel--shifted');
    });
  });

  // Animated number counters
  if ('IntersectionObserver' in window) {
    var counters = document.querySelectorAll('.qt-why__stat-number');
    if (counters.length) {
      var counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var text = el.textContent.trim();
          var match = text.match(/^([\d,]+)(\+?)$/);
          if (!match) return;

          var target = parseInt(match[1].replace(/,/g, ''), 10);
          var suffix = match[2] || '';
          var duration = 1500;
          var start = 0;
          var startTime = null;

          function animate(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString() + suffix;
            if (progress < 1) requestAnimationFrame(animate);
          }

          requestAnimationFrame(animate);
          counterObserver.unobserve(el);
        });
      }, { threshold: 0.5 });

      counters.forEach(function (el) { counterObserver.observe(el); });
    }
  }

  // Locator map/list toggle (mobile)
  var locToggle = document.getElementById('qt-loc-toggle');
  if (locToggle) {
    var locBody = document.querySelector('.qt-locator__body');
    var locBtns = locToggle.querySelectorAll('.qt-locator__toggle-btn');
    locBody.setAttribute('data-mobile-view', 'map');

    locBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var view = this.getAttribute('data-view');
        locBody.setAttribute('data-mobile-view', view);
        locBtns.forEach(function (b) { b.classList.remove('qt-locator__toggle-btn--active'); });
        this.classList.add('qt-locator__toggle-btn--active');
      });
    });
  }

  // Back to top button
  var backToTop = document.createElement('button');
  backToTop.className = 'qt-back-to-top';
  backToTop.setAttribute('aria-label', 'Back to top');
  backToTop.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>';
  document.body.appendChild(backToTop);

  backToTop.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  var backToTopVisible = false;
  window.addEventListener('scroll', function () {
    var show = window.scrollY > 400;
    if (show !== backToTopVisible) {
      backToTopVisible = show;
      backToTop.classList.toggle('qt-back-to-top--visible', show);
    }
  }, { passive: true });

  // Product image hover zoom
  var productImg = document.querySelector('.woocommerce div.product div.images .woocommerce-product-gallery__image img');
  if (productImg) {
    var zoomWrap = productImg.closest('.woocommerce-product-gallery__image') || productImg.parentElement;
    zoomWrap.classList.add('qt-zoom-wrap');

    var lens = document.createElement('div');
    lens.className = 'qt-zoom-lens';
    lens.hidden = true;
    zoomWrap.appendChild(lens);

    zoomWrap.addEventListener('mouseenter', function () {
      lens.hidden = false;
      lens.style.backgroundImage = 'url(' + (productImg.dataset.largeImage || productImg.src) + ')';
    });

    zoomWrap.addEventListener('mouseleave', function () {
      lens.hidden = true;
    });

    zoomWrap.addEventListener('mousemove', function (e) {
      if (lens.hidden) return;
      var rect = zoomWrap.getBoundingClientRect();
      var x = ((e.clientX - rect.left) / rect.width) * 100;
      var y = ((e.clientY - rect.top) / rect.height) * 100;
      lens.style.backgroundPosition = x + '% ' + y + '%';
    });
  }

  // Sticky Add to Quote bar on single product
  var quoteActions = document.querySelector('.quest-quote-actions');
  var stickyBar = null;
  if (quoteActions) {
    var productName = document.querySelector('.product_title');
    stickyBar = document.createElement('div');
    stickyBar.className = 'qt-sticky-quote';
    stickyBar.innerHTML = '<div class="qt-container qt-sticky-quote__inner">'
      + '<span class="qt-sticky-quote__name">' + (productName ? productName.textContent : '') + '</span>'
      + '<button type="button" class="qt-btn qt-btn--primary qt-btn--sm qt-sticky-quote__btn">Add to Quote</button>'
      + '</div>';
    document.body.appendChild(stickyBar);

    stickyBar.querySelector('.qt-sticky-quote__btn').addEventListener('click', function () {
      var btn = quoteActions.querySelector('.quest-add-to-quote');
      if (btn) btn.click();
    });

    var stickyVisible = false;
    window.addEventListener('scroll', function () {
      var rect = quoteActions.getBoundingClientRect();
      var show = rect.bottom < 0;
      if (show !== stickyVisible) {
        stickyVisible = show;
        stickyBar.classList.toggle('qt-sticky-quote--visible', show);
      }
    }, { passive: true });
  }

  // Newsletter form AJAX
  var nlForm = document.getElementById('qt-newsletter-form');
  if (nlForm) {
    nlForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = nlForm.querySelector('button[type="submit"]');
      var result = document.getElementById('qt-newsletter-result');
      btn.disabled = true;
      btn.textContent = 'Sending...';

      var formData = new FormData(nlForm);
      formData.append('action', 'quest_newsletter');

      var xhr = new XMLHttpRequest();
      xhr.open('POST', (typeof questContact !== 'undefined' ? questContact.ajaxUrl : '/wp-admin/admin-ajax.php'));
      xhr.onload = function () {
        var data;
        try { data = JSON.parse(xhr.responseText); } catch (ex) { return; }
        if (result) {
          result.hidden = false;
          result.textContent = data.data.message || '';
          result.className = 'qt-newsletter__result qt-newsletter__result--' + (data.success ? 'success' : 'error');
        }
        if (data.success) {
          nlForm.style.display = 'none';
        } else {
          btn.disabled = false;
          btn.textContent = 'Subscribe';
        }
      };
      xhr.send(formData);
    });
  }

  // Scroll reveal — IntersectionObserver (zero performance cost)
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var revealTargets = [
      '.qt-section__header',
      '.qt-cat-card',
      '.qt-why__stat',
      '.qt-why__card',
      '.qt-new-product-card',
      '.qt-product-card',
      '.qt-tabs__card',
      '.qt-dept-card',
      '.qt-warranty-card',
      '.qt-affil-card',
      '.qt-info-card',
      '.qt-cta__content',
      '.qt-cta__media',
      '.qt-partners__logo',
      '.qt-qwa-hero__video',
      '.qt-qwa-hero__content',
      '.qt-locator-card'
    ];

    var allEls = document.querySelectorAll(revealTargets.join(','));
    allEls.forEach(function (el) {
      el.classList.add('qt-reveal');
    });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('qt-reveal--visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    allEls.forEach(function (el) { observer.observe(el); });
  }
})();
