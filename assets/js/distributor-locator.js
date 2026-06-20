(function () {
  'use strict';

  var configEl = document.getElementById('qt-locator-config');
  if (!configEl) return;

  var config = JSON.parse(configEl.textContent);
  var allStores = config.stores || [];
  var map, markers = [], markerGroup;
  var activeCard = null;

  var input = document.getElementById('qt-loc-input');
  var radiusSelect = document.getElementById('qt-loc-radius');
  var searchBtn = document.getElementById('qt-loc-search');
  var listInner = document.getElementById('qt-loc-list-inner');
  var statusEl = document.getElementById('qt-loc-status');

  // Init Leaflet map
  map = L.map('qt-loc-map', { scrollWheelZoom: true, zoomControl: true })
    .setView([config.center.lat, config.center.lng], config.zoom);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map);

  markerGroup = L.markerClusterGroup({
    maxClusterRadius: 50,
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    iconCreateFunction: function (cluster) {
      var count = cluster.getChildCount();
      var size = count < 10 ? 32 : count < 50 ? 38 : 44;
      return L.divIcon({
        html: '<div class="qt-cluster"><span>' + count + '</span></div>',
        className: 'qt-cluster-icon',
        iconSize: [size, size]
      });
    }
  });
  map.addLayer(markerGroup);

  var questIcon = L.divIcon({
    className: 'qt-map-pin',
    html: '<div class="qt-map-pin__dot"></div>',
    iconSize: [24, 24],
    iconAnchor: [12, 12],
    popupAnchor: [0, -14]
  });

  // Initial render
  renderStores(allStores);
  updateStatus(config.total + ' distributors worldwide');

  // Search
  searchBtn.addEventListener('click', performSearch);
  input.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); performSearch(); }
  });

  function performSearch() {
    var query = input.value.trim().toLowerCase();

    // Empty query — show all
    if (!query) {
      allStores.forEach(function (s) { delete s._distance; });
      renderStores(allStores);
      updateStatus(config.total + ' distributors worldwide');
      fitBounds(allStores);
      return;
    }

    // Try local match first (state, country, city)
    var localMatch = allStores.filter(function (s) {
      return (s.state && s.state.toLowerCase() === query)
        || (s.country && s.country.toLowerCase() === query)
        || (s.city && s.city.toLowerCase() === query)
        || (s.state && s.state.toLowerCase().indexOf(query) === 0)
        || (s.country && s.country.toLowerCase().indexOf(query) === 0)
        || (s.city && s.city.toLowerCase().indexOf(query) === 0);
    });

    if (localMatch.length >= 3) {
      localMatch.forEach(function (s) { delete s._distance; });
      renderStores(localMatch);
      updateStatus(localMatch.length + ' distributors found');
      fitBounds(localMatch);
      return;
    }

    // Geocode with Nominatim
    updateStatus('Searching...');

    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(input.value.trim()) + '&limit=1')
      .then(function (r) { return r.json(); })
      .then(function (results) {
        if (!results.length) {
          updateStatus('Location not found. Try a different search.');
          return;
        }

        var center = {
          lat: parseFloat(results[0].lat),
          lng: parseFloat(results[0].lon)
        };

        var radius = parseInt(radiusSelect.value, 10);
        var filtered;

        if (radius === 0) {
          filtered = allStores.map(function (s) {
            s._distance = haversine(center.lat, center.lng, s.lat, s.lng);
            return s;
          }).sort(function (a, b) { return a._distance - b._distance; });
        } else {
          filtered = allStores.map(function (s) {
            s._distance = haversine(center.lat, center.lng, s.lat, s.lng);
            return s;
          }).filter(function (s) {
            return s._distance <= radius;
          }).sort(function (a, b) { return a._distance - b._distance; });
        }

        renderStores(filtered);
        updateStatus(filtered.length + ' distributor' + (filtered.length !== 1 ? 's' : '') + ' found');

        if (filtered.length > 0) {
          fitBounds(filtered);
        } else {
          map.setView([center.lat, center.lng], 8);
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
        + '<p>Try a different location or expand your search radius.</p>'
        + '</div>';
      return;
    }

    stores.forEach(function (store, i) {
      var marker = L.marker([store.lat, store.lng], { icon: questIcon });
      marker.bindPopup(buildPopup(store));
      marker.on('click', function () { highlightCard(i); });
      markerGroup.addLayer(marker);
      markers.push(marker);

      var card = document.createElement('div');
      card.className = 'qt-locator-card';
      card.setAttribute('data-index', i);

      var dist = store._distance
        ? '<span class="qt-locator-card__distance">' + store._distance.toFixed(1) + ' mi</span>'
        : '';

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
        + (store.phone ? '<a href="tel:' + esc(store.phone) + '" class="qt-locator-card__link">' + esc(store.phone) + '</a>' : '')
        + (store.email ? '<a href="mailto:' + esc(store.email) + '" class="qt-locator-card__link">' + esc(store.email) + '</a>' : '')
        + '<a href="https://www.google.com/maps/dir/?api=1&destination=' + store.lat + ',' + store.lng
        + '" target="_blank" rel="noopener" class="qt-locator-card__directions">Directions &rarr;</a>'
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
    html += '<br><a href="https://www.google.com/maps/dir/?api=1&destination=' + store.lat + ',' + store.lng
      + '" target="_blank" rel="noopener"><strong>Get Directions &rarr;</strong></a>';
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

  function updateStatus(text) { statusEl.textContent = text; }

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
