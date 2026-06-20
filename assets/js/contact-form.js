(function () {
  'use strict';

  var form = document.getElementById('qt-contact-form');
  var submitBtn = document.getElementById('qt-contact-submit');
  var resultEl = document.getElementById('qt-contact-result');

  if (!form || !submitBtn) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';
    resultEl.hidden = true;

    var formData = new FormData(form);
    formData.append('action', 'quest_contact_form');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', questContact.ajaxUrl);
    xhr.onload = function () {
      var data;
      try { data = JSON.parse(xhr.responseText); } catch (e) { return; }

      resultEl.hidden = false;

      if (data.success) {
        resultEl.className = 'qt-contact-form__result qt-contact-form__result--success';
        resultEl.textContent = data.data.message;
        form.reset();
        form.style.display = 'none';
      } else {
        resultEl.className = 'qt-contact-form__result qt-contact-form__result--error';
        resultEl.textContent = data.data.message || 'An error occurred.';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
      }
    };
    xhr.onerror = function () {
      resultEl.hidden = false;
      resultEl.className = 'qt-contact-form__result qt-contact-form__result--error';
      resultEl.textContent = 'Network error. Please try again.';
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Message';
    };
    xhr.send(formData);
  });
})();
