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
        if (dots[current]) {
          dots[current].classList.remove('qt-hero__progress-dot--active');
          var bar = dots[current].querySelector('.qt-hero__progress-bar');
          if (bar) bar.style.width = '0';
        }

        current = ((index % slideCount) + slideCount) % slideCount;

        slides[current].classList.add('qt-hero__slide--active');
        slides[current].setAttribute('aria-hidden', 'false');
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
})();
