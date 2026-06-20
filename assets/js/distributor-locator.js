(function () {
  'use strict';

  var configEl = document.getElementById('qt-locator-config');
  if (!configEl) return;

  var config = JSON.parse(configEl.textContent);
  var allStores = config.stores || [];
  var map, markers = [], markerGroup;
  var activeCard = null;
  var searchMode = 'location';
  var searchCenter = null;

  var input = document.getElementById('qt-loc-input');
  var stateSelect = document.getElementById('qt-loc-state');
  var countrySelect = document.getElementById('qt-loc-country');
  var radiusSelect = document.getElementById('qt-loc-radius');
  var searchBtn = document.getElementById('qt-loc-search');
  var listInner = document.getElementById('qt-loc-list-inner');
  var statusEl = document.getElementById('qt-loc-status');

  // Init map
  map = L.map('qt-loc-map', {
    scrollWheelZoom: true,
    zoomControl: true
  }).setView([config.center.lat, config.center.lng], config.zoom);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map);

  markerGroup = L.featureGroup().addTo(map);

  // Custom icon
  var questIcon = L.divIcon({
    className: 'qt-map-pin',
    html: '<div class="qt-map-pin__dot"></div>',
    iconSize: [24, 24],
    iconAnchor: [12, 12],
    popupAnchor: [0, -14]
  });

  // Render all stores initially
  renderStores(allStores);
  updateStatus(config.total + ' distributors worldwide');

  // Tab switching
  document.querySelectorAll('.qt-locator__tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      searchMode = this.getAttribute('data-mode');
      document.querySelectorAll('.qt-locator__tab').forEach(function (t) {
        t.classList.remove('qt-locator__tab--active');
      });
      this.classList.add('qt-locator__tab--active');

      document.querySelectorAll('.qt-locator__panel').forEach(function (p) {
        var show = p.getAttribute('data-panel') === searchMode;
        p.classList.toggle('qt-locator__panel--active', show);
        p.hidden = !show;
      });

      // Show/hide radius for location mode only
      radiusSelect.style.display = searchMode === 'location' ? '' : 'none';
    });
  });

  // Search
  searchBtn.addEventListener('click', performSearch);
  if (input) {
    input.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); performSearch(); }
    });
  }
  if (stateSelect) {
    stateSelect.addEventListener('change', performSearch);
  }
  if (countrySelect) {
    countrySelect.addEventListener('change', performSearch);
  }

  function performSearch() {
    if (searchMode === 'state') {
      var state = stateSelect.value;
      if (!state) return;
      var filtered = allStores.filter(function (s) {
        return s.state && s.state.toLowerCase() === state.toLowerCase();
      });
      renderStores(filtered);
      updateStatus(filtered.length + ' distributors in ' + state);
      if (filtered.length > 0) fitBounds(filtered);
      return;
    }

    if (searchMode === 'country') {
      var country = countrySelect.value;
      if (!country) return;
      var filtered = allStores.filter(function (s) {
        return s.country && s.country.toLowerCase() === country.toLowerCase();
      });
      renderStores(filtered);
      updateStatus(filtered.length + ' distributors in ' + country);
      if (filtered.length > 0) fitBounds(filtered);
      return;
    }

    // Location mode — geocode with Nominatim
    var query = input.value.trim();
    if (!query) {
      searchCenter = null;
      renderStores(allStores);
      updateStatus(config.total + ' distributors worldwide');
      fitBounds(allStores);
      return;
    }

    updateStatus('Searching...');

    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=1')
      .then(function (r) { return r.json(); })
      .then(function (results) {
        if (!results.length) {
          updateStatus('Location not found. Try a different search.');
          return;
        }

        searchCenter = {
          lat: parseFloat(results[0].lat),
          lng: parseFloat(results[0].lon)
        };

        var radius = parseInt(radiusSelect.value, 10);
        var filtered;

        if (radius === 0) {
          filtered = allStores;
        } else {
          filtered = allStores.map(function (s) {
            s._distance = haversine(searchCenter.lat, searchCenter.lng, s.lat, s.lng);
            return s;
          }).filter(function (s) {
            return s._distance <= radius;
          }).sort(function (a, b) {
            return a._distance - b._distance;
          });
        }

        renderStores(filtered);
        updateStatus(filtered.length + ' distributor' + (filtered.length !== 1 ? 's' : '') + ' found');

        if (filtered.length > 0) {
          fitBounds(filtered);
        } else {
          map.setView([searchCenter.lat, searchCenter.lng], 8);
        }
      })
      .catch(function () {
        updateStatus('Search error. Please try again.');
      });
  }

  function renderStores(stores) {
    markerGroup.clearLayers();
    markers = [];
    listInner.innerHTML = '';
    activeCard = null;

    if (stores.length === 0) {
      listInner.innerHTML = '<div class="qt-locator__empty">'
        + '<p>No distributors found in this area.</p>'
        + '<p>Try expanding your search radius or searching a different location.</p>'
        + '</div>';
      return;
    }

    stores.forEach(function (store, i) {
      // Marker
      var marker = L.marker([store.lat, store.lng], { icon: questIcon });
      marker.bindPopup(buildPopup(store));
      marker.on('click', function () { highlightCard(i); });
      markerGroup.addLayer(marker);
      markers.push(marker);

      // Card
      var card = document.createElement('div');
      card.className = 'qt-locator-card';
      card.setAttribute('data-index', i);

      var dist = store._distance ? '<span class="qt-locator-card__distance">' + store._distance.toFixed(1) + ' mi</span>' : '';

      card.innerHTML = '<div class="qt-locator-card__header">'
        + '<h3 class="qt-locator-card__name">' + esc(store.name) + '</h3>'
        + dist
        + '</div>'
        + '<p class="qt-locator-card__address">'
        + esc(store.address || '') + '<br>'
        + esc(store.city || '') + (store.state ? ', ' + esc(store.state) : '') + ' ' + esc(store.zip || '')
        + (store.country ? '<br>' + esc(store.country) : '')
        + '</p>'
        + '<div class="qt-locator-card__actions">'
        + (store.phone ? '<a href="tel:' + esc(store.phone) + '" class="qt-locator-card__link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.11 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg> ' + esc(store.phone) + '</a>' : '')
        + (store.email ? '<a href="mailto:' + esc(store.email) + '" class="qt-locator-card__link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> ' + esc(store.email) + '</a>' : '')
        + (store.url ? '<a href="' + esc(store.url) + '" target="_blank" rel="noopener" class="qt-locator-card__link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> Website</a>' : '')
        + '<a href="https://www.google.com/maps/dir/?api=1&destination=' + store.lat + ',' + store.lng + '" target="_blank" rel="noopener" class="qt-locator-card__directions">Directions &rarr;</a>'
        + '</div>';

      card.addEventListener('click', function (e) {
        if (e.target.closest('a')) return;
        map.setView([store.lat, store.lng], 14);
        markers[i].openPopup();
        highlightCard(i);
      });

      listInner.appendChild(card);
    });
  }

  function buildPopup(store) {
    var html = '<div class="qt-map-popup">'
      + '<strong>' + esc(store.name) + '</strong><br>'
      + '<span class="qt-map-popup__addr">'
      + esc(store.address || '') + '<br>'
      + esc(store.city || '') + (store.state ? ', ' + esc(store.state) : '') + ' ' + esc(store.zip || '')
      + '</span>';

    if (store.phone) html += '<br><a href="tel:' + esc(store.phone) + '">' + esc(store.phone) + '</a>';
    if (store._distance) html += '<br><small>' + store._distance.toFixed(1) + ' miles</small>';
    html += '<br><a href="https://www.google.com/maps/dir/?api=1&destination=' + store.lat + ',' + store.lng + '" target="_blank" rel="noopener"><strong>Get Directions &rarr;</strong></a>';
    html += '</div>';
    return html;
  }

  function highlightCard(index) {
    if (activeCard !== null) {
      var prev = listInner.querySelector('[data-index="' + activeCard + '"]');
      if (prev) prev.classList.remove('qt-locator-card--active');
    }
    var card = listInner.querySelector('[data-index="' + index + '"]');
    if (card) {
      card.classList.add('qt-locator-card--active');
      card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    activeCard = index;
  }

  function fitBounds(stores) {
    var bounds = L.latLngBounds(stores.map(function (s) { return [s.lat, s.lng]; }));
    map.fitBounds(bounds, { padding: [40, 40] });
  }

  function updateStatus(text) {
    statusEl.textContent = text;
  }

  function haversine(lat1, lng1, lat2, lng2) {
    var R = 3959;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLng = (lng2 - lng1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
      + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
      * Math.sin(dLng / 2) * Math.sin(dLng / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  function esc(str) {
    var el = document.createElement('span');
    el.textContent = str || '';
    return el.innerHTML;
  }
})();
