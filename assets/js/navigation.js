(function () {
  'use strict';

  var mobileMenu = document.getElementById('qt-mobile-menu');
  var backdrop = document.querySelector('.qt-mobile-menu__backdrop');
  var toggleBtns = document.querySelectorAll('.js-mobile-toggle');
  var closeBtn = document.querySelector('.js-mobile-close');

  if (!mobileMenu) return;

  function openMenu() {
    mobileMenu.setAttribute('aria-hidden', 'false');
    if (backdrop) backdrop.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    toggleBtns.forEach(function (btn) {
      btn.setAttribute('aria-expanded', 'true');
    });
  }

  function closeMenu() {
    mobileMenu.setAttribute('aria-hidden', 'true');
    if (backdrop) backdrop.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    toggleBtns.forEach(function (btn) {
      btn.setAttribute('aria-expanded', 'false');
    });
  }

  toggleBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var isOpen = mobileMenu.getAttribute('aria-hidden') === 'false';
      if (isOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', closeMenu);
  }

  if (backdrop) {
    backdrop.addEventListener('click', closeMenu);
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileMenu.getAttribute('aria-hidden') === 'false') {
      closeMenu();
    }
  });
})();
