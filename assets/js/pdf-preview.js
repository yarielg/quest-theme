(function () {
  'use strict';

  var canvases = document.querySelectorAll('.qt-resource-card__pdf-canvas[data-pdf-url]');
  if (!canvases.length) return;

  var script = document.createElement('script');
  script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
  script.onload = function () {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    canvases.forEach(renderPdf);
  };
  document.head.appendChild(script);

  function renderPdf(canvas) {
    var url = canvas.getAttribute('data-pdf-url');
    if (!url) return;

    pdfjsLib.getDocument(url).promise.then(function (pdf) {
      return pdf.getPage(1);
    }).then(function (page) {
      var container = canvas.parentElement;
      var containerWidth = container.offsetWidth || 300;
      var viewport = page.getViewport({ scale: 1 });
      var scale = (containerWidth * 2) / viewport.width;
      var scaledViewport = page.getViewport({ scale: scale });

      canvas.width = scaledViewport.width;
      canvas.height = scaledViewport.height;

      page.render({
        canvasContext: canvas.getContext('2d'),
        viewport: scaledViewport
      });
    }).catch(function () {
      canvas.style.display = 'none';
      var fallback = document.createElement('div');
      fallback.className = 'qt-resource-card__icon';
      fallback.innerHTML = '<span class="qt-resource-card__ext">PDF</span>';
      canvas.parentElement.appendChild(fallback);
    });
  }
})();
