/* COT Dashboard — WordPress Plugin JS */
(function () {
  'use strict';

  /* ── Config ──────────────────────────────────────────────────────────── */

  var API_URL =
    typeof COT_CONFIG !== 'undefined' && COT_CONFIG.api_url
      ? COT_CONFIG.api_url.replace(/\/$/, '')
      : 'http://localhost:8000';

  var CHART_JS_CDN = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
  var HAMMER_CDN   = 'https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js';
  var ZOOM_CDN     = 'https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js';

  /* ── State ───────────────────────────────────────────────────────────── */

  var STATE = {
    markets:        [],
    selectedMarket: null,
    searchQuery:    '',
    activeRange:    104,
    charts:         {},
    loading:        false,
    cotIndexMode:   '26w',
    crosshairIndex: null,
  };

  // softs + livestock are merged into grains under one "COMMODITIES" section
  var CATEGORIES = ['forex','crypto','indices','bonds','energy','metals','grains'];

  var CATEGORY_LABELS = {
    forex:     'Forex',
    crypto:    'Crypto',
    indices:   'Indices',
    bonds:     'Bonds',
    energy:    'Energy',
    metals:    'Metals',
    grains:    'Commodities',   // grains + softs + livestock merged
    softs:     'Commodities',
    livestock: 'Commodities',
  };

  var RANGE_LABELS = [
    [52,   '1Y'],
    [104,  '2Y'],
    [260,  '5Y'],
    [520,  '10Y'],
    [1300, '25Y'],
  ];

  /* ── Number formatting ───────────────────────────────────────────────── */

  function _spaced(n) {
    return Math.abs(Math.round(n)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\u00a0');
  }

  function formatNet(v) {
    if (v === null || v === undefined) return '\u2014';
    return (v >= 0 ? '+' : '\u2212') + _spaced(v);
  }

  function formatAxisY(v) {
    var a = Math.abs(v);
    if (a >= 1000000) return (v / 1000000).toFixed(1) + 'M';
    if (a >= 1000)    return (v / 1000).toFixed(0)    + 'K';
    return String(v);
  }

  /* ── COT helpers ─────────────────────────────────────────────────────── */

  function cotColor(idx) {
    if (idx === null || idx === undefined) return '#60a5fa';
    if (idx >= 80) return '#22c55e';
    if (idx <= 20) return '#ef4444';
    return '#60a5fa';
  }

  function signalBadge(idx) {
    if (idx === null || idx === undefined)
      return '<span class="cot-signal-badge neutral">NEUTRAL</span>';
    if (idx >= 80) return '<span class="cot-signal-badge buy">BUY</span>';
    if (idx <= 20) return '<span class="cot-signal-badge sell">SELL</span>';
    return '<span class="cot-signal-badge neutral">NEUTRAL</span>';
  }

  /* ── Count-up animation ──────────────────────────────────────────────── */

  function countUp(el, target, duration) {
    duration = duration || 600;
    var sign  = target >= 0 ? '+' : '\u2212';
    var abs   = Math.abs(target);
    var start = Date.now();
    function tick() {
      var p = Math.min((Date.now() - start) / duration, 1);
      el.textContent = sign + _spaced(Math.round(abs * (1 - Math.pow(1 - p, 3))));
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  /* ── Zoom sync ───────────────────────────────────────────────────────── */

  var _syncingZoom = false;

  function syncZoom(sourceChart) {
    if (_syncingZoom) return;
    _syncingZoom = true;
    var xs = sourceChart.scales && sourceChart.scales.x;
    if (xs) {
      Object.keys(STATE.charts).forEach(function (k) {
        var c = STATE.charts[k];
        if (!c || c === sourceChart) return;
        if (typeof c.zoomScale === 'function') {
          c.zoomScale('x', { min: xs.min, max: xs.max }, 'none');
        } else {
          c.options.scales.x.min = xs.min;
          c.options.scales.x.max = xs.max;
          c.update('none');
        }
      });
    }
    _syncingZoom = false;
  }

  /* ── Crosshair plugin ────────────────────────────────────────────────── */

  var cotCrosshairPlugin = {
    id: 'cotCrosshair',
    afterDraw: function (chart) {
      if (STATE.crosshairIndex === null) return;
      var ctx = chart.ctx;
      var xs  = chart.scales.x;
      var ys  = chart.scales.y;
      if (!xs || !ys) return;
      var xPx = xs.getPixelForValue(STATE.crosshairIndex);
      if (xPx < xs.left || xPx > xs.right) return;
      ctx.save();
      ctx.beginPath();
      ctx.moveTo(xPx, ys.top);
      ctx.lineTo(xPx, ys.bottom);
      ctx.lineWidth = 1;
      ctx.strokeStyle = 'rgba(255,255,255,0.28)';
      ctx.setLineDash([4, 4]);
      ctx.stroke();
      ctx.restore();
    },
  };

  var _crosshairRAF = null;

  function bindCrosshair(canvas, chartKey) {
    canvas.addEventListener('mousemove', function (e) {
      var chart = STATE.charts[chartKey];
      if (!chart) return;
      var xs = chart.scales.x;
      if (!xs) return;
      var rect = canvas.getBoundingClientRect();
      var idx  = Math.round(xs.getValueForPixel(e.clientX - rect.left));
      var len  = chart.data.labels ? chart.data.labels.length : 0;
      STATE.crosshairIndex = len > 0 ? Math.max(0, Math.min(len - 1, idx)) : null;
      if (_crosshairRAF) return;
      _crosshairRAF = requestAnimationFrame(function () {
        _crosshairRAF = null;
        Object.keys(STATE.charts).forEach(function (k) {
          if (STATE.charts[k]) STATE.charts[k].update('none');
        });
      });
    });
    canvas.addEventListener('mouseleave', function () {
      STATE.crosshairIndex = null;
      Object.keys(STATE.charts).forEach(function (k) {
        if (STATE.charts[k]) STATE.charts[k].update('none');
      });
    });
  }

  /* ── Script loader ───────────────────────────────────────────────────── */

  function loadScript(src, cb) {
    var s = document.createElement('script');
    s.src = src;
    s.onload = cb;
    s.onerror = function () { console.error('COT: failed to load ' + src); cb(); };
    document.head.appendChild(s);
  }

  function loadChartJS(cb) {
    if (window.Chart && window._cotZoomReady) { cb(); return; }
    if (window.Chart && !window._cotZoomReady) {
      loadScript(HAMMER_CDN, function () {
        loadScript(ZOOM_CDN, function () { window._cotZoomReady = true; cb(); });
      });
      return;
    }
    loadScript(CHART_JS_CDN, function () {
      loadScript(HAMMER_CDN, function () {
        loadScript(ZOOM_CDN, function () { window._cotZoomReady = true; cb(); });
      });
    });
  }

  /* ── DOM helpers ─────────────────────────────────────────────────────── */

  function qs(sel, root)  { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return (root || document).querySelectorAll(sel); }

  /* ── Skeleton ────────────────────────────────────────────────────────── */

  function showSkeleton(container) {
    container.innerHTML =
      '<div class="cot-topbar">' +
        '<div class="cot-topbar-left">' +
          '<div class="cot-topbar-status">' +
            '<span class="cot-status-dot loading"></span>' +
            '<span class="cot-status-label" style="color:var(--accent)">LOADING\u2026</span>' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="cot-skeleton-body">' +
        '<div class="cot-skeleton-bar"></div>' +
        '<div class="cot-skeleton-row"></div><div class="cot-skeleton-row"></div>' +
        '<div class="cot-skeleton-row"></div><div class="cot-skeleton-row"></div>' +
        '<div class="cot-skeleton-row"></div><div class="cot-skeleton-row"></div>' +
        '<div class="cot-skeleton-row"></div><div class="cot-skeleton-row"></div>' +
      '</div>';
  }

  /* ── Error state ─────────────────────────────────────────────────────── */

  function showError(container, msg) {
    container.innerHTML =
      '<div class="cot-topbar">' +
        '<div class="cot-topbar-left">' +
          '<div class="cot-topbar-status">' +
            '<span class="cot-status-dot" style="background:var(--red)"></span>' +
            '<span class="cot-status-label" style="color:var(--red)">ERROR</span>' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="cot-error-state">' +
        '<div class="cot-error-icon">\u26a0</div>' +
        '<div class="cot-error-msg">' + msg + '</div>' +
        '<button class="cot-retry-btn">Retry</button>' +
      '</div>';
    var btn = qs('.cot-retry-btn', container);
    if (btn) btn.addEventListener('click', function () { init(container); });
  }

  /* ── Main layout ─────────────────────────────────────────────────────── */

  function buildLayout(container, statusData) {
    var lastDate = statusData && statusData.last_update ? statusData.last_update : '';

    container.innerHTML =
      '<div class="cot-topbar">' +
        '<div class="cot-topbar-left">' +
          '<div class="cot-topbar-status">' +
            '<span class="cot-status-dot live"></span>' +
            '<span class="cot-status-label">LIVE</span>' +
          '</div>' +
          (lastDate
            ? '<span class="cot-status-date">Data as of <strong>' + lastDate + '</strong></span>'
            : '') +
        '</div>' +
        '<div class="cot-topbar-right">' +
          '<input type="text" class="cot-search-input" placeholder="Search market\u2026" autocomplete="off" value="' + STATE.searchQuery + '">' +
        '</div>' +
      '</div>' +
      '<div class="cot-content">' +
        '<div id="cot-overview-inner"></div>' +
        '<div id="cot-detail-inner" style="display:none"></div>' +
      '</div>';

    var searchTimer = null;
    var input = qs('.cot-search-input', container);
    if (input) {
      input.addEventListener('input', function () {
        clearTimeout(searchTimer);
        var val = this.value;
        searchTimer = setTimeout(function () {
          STATE.searchQuery = val.toLowerCase();
          /* If in detail view, exit it so search results are visible */
          var det = qs('#cot-detail-inner',  container);
          var ov  = qs('#cot-overview-inner', container);
          if (det && det.style.display !== 'none') {
            STATE.selectedMarket = null;
            det.style.display = 'none';
            ov.style.display  = 'block';
          }
          renderTable(container, false);
        }, 280);
      });
    }
  }

  /* ── Overview table ──────────────────────────────────────────────────── */

  function renderTable(container, animate) {
    var overview = qs('#cot-overview-inner', container);
    if (!overview) return;

    var q = STATE.searchQuery;

    /* Group markets by category, filtering by search query.
       softs + livestock are merged into grains (all shown as "Commodities"). */
    var grouped = {};
    CATEGORIES.forEach(function (cat) { grouped[cat] = []; });
    STATE.markets.forEach(function (m) {
      if (q && m.name.toLowerCase().indexOf(q) === -1) return;
      var cat = m.category;
      if (cat === 'softs' || cat === 'livestock') cat = 'grains'; // merge
      if (grouped[cat] !== undefined) grouped[cat].push(m);
    });

    var totalVisible = CATEGORIES.reduce(function (sum, cat) {
      return sum + grouped[cat].length;
    }, 0);

    if (totalVisible === 0) {
      overview.innerHTML = '<div class="cot-no-results">No market found for &ldquo;' + q + '&rdquo;</div>';
      return;
    }

    var html =
      '<div class="cot-table-wrap"><table class="cot-table">' +
        '<thead>' +
          '<tr class="cot-group-hdr">' +
            '<th class="cot-th-mkt" rowspan="2">Market</th>' +
            '<th colspan="2" class="g-comm">COMMERCIALS</th>' +
            '<th colspan="2" class="g-spec">LARGE SPECULATORS</th>' +
            '<th colspan="2" class="g-retail">SMALL TRADERS&nbsp;<small>(Retail)</small></th>' +
            '<th rowspan="2" class="cot-col-index g-index">Index 26W</th>' +
            '<th rowspan="2" class="col-signal g-sig">Signal</th>' +
          '</tr>' +
          '<tr class="cot-col-hdr">' +
            '<th class="g-comm-sub">Net</th>' +
            '<th class="g-comm-sub">Change</th>' +
            '<th class="g-spec-sub">Net</th>' +
            '<th class="g-spec-sub">Change</th>' +
            '<th class="g-retail-sub">Net</th>' +
            '<th class="g-retail-sub">Change</th>' +
          '</tr>' +
        '</thead>' +
        '<tbody>';

    CATEGORIES.forEach(function (cat) {
      var rows = grouped[cat];
      if (rows.length === 0) return;

      var catLabel = CATEGORY_LABELS[cat] || cat;
      var count    = rows.length;

      /* Category section header row */
      html +=
        '<tr class="cot-section-hdr" id="cot-section-' + cat + '">' +
          '<td colspan="9">' +
            '<span>' + catLabel.toUpperCase() + '</span>' +
            '<em>' + count + ' market' + (count !== 1 ? 's' : '') + '</em>' +
          '</td>' +
        '</tr>';

      /* Data rows */
      rows.forEach(function (market, idx) {
        /* COT index 26W as primary */
        var idx26  = market.cot_index_26;
        var d26    = (idx26 !== null && idx26 !== undefined) ? Math.round(idx26) : '\u2014';
        var c26    = cotColor(idx26);
        var barW26 = (idx26 !== null && idx26 !== undefined) ? Math.max(0, Math.min(100, idx26)) : 0;

        /* Comm change */
        var chgC   = (market.change_comm !== null && market.change_comm !== undefined) ? market.change_comm : null;
        var chgCls = chgC === null ? '' : (chgC >= 0 ? ' pos' : ' neg');
        var chgTxt = chgC === null ? '\u2014' : (chgC >= 0 ? '+' : '\u2212') + _spaced(chgC);

        /* Spec change */
        var chgS    = (market.change_spec !== null && market.change_spec !== undefined) ? market.change_spec : null;
        var chgSCls = chgS === null ? '' : (chgS >= 0 ? ' pos' : ' neg');
        var chgSTxt = chgS === null ? '\u2014' : (chgS >= 0 ? '+' : '\u2212') + _spaced(chgS);

        /* Small traders (Retail) */
        var smallV   = (market.small_net !== null && market.small_net !== undefined) ? market.small_net : null;
        var smallCls = smallV === null ? '' : (smallV >= 0 ? ' pos' : ' neg');
        var smallTxt = smallV === null ? '\u2014' : formatNet(smallV);

        /* Small traders change */
        var chgSm    = (market.change_small !== null && market.change_small !== undefined) ? market.change_small : null;
        var chgSmCls = chgSm === null ? '' : (chgSm >= 0 ? ' pos' : ' neg');
        var chgSmTxt = chgSm === null ? '\u2014' : (chgSm >= 0 ? '+' : '\u2212') + _spaced(chgSm);

        html +=
          '<tr class="cot-row" data-key="' + market.market_key + '" style="animation-delay:' + (idx * 18) + 'ms">' +
            '<td class="cot-col-market">' +
              '<span class="cot-market-name">' + market.name + '</span>' +
            '</td>' +
            '<td class="cot-num g-comm-cell' + (market.comm_net >= 0 ? ' pos' : ' neg') + '" data-val="' + market.comm_net + '">' +
              formatNet(market.comm_net) +
            '</td>' +
            '<td class="cot-num g-comm-cell' + chgCls + '">' + chgTxt + '</td>' +
            '<td class="cot-num g-spec-cell' + (market.spec_net >= 0 ? ' pos' : ' neg') + '">' +
              formatNet(market.spec_net) +
            '</td>' +
            '<td class="cot-num g-spec-cell' + chgSCls + '">' + chgSTxt + '</td>' +
            '<td class="cot-num g-retail-cell' + smallCls + '">' + smallTxt + '</td>' +
            '<td class="cot-num g-retail-cell' + chgSmCls + '">' + chgSmTxt + '</td>' +
            '<td class="cot-col-index">' +
              '<div class="cot-index-wrap">' +
                '<div class="cot-index-bar-bg">' +
                  '<div class="cot-index-bar-fill" style="width:' + barW26 + '%;background:' + c26 + '"></div>' +
                '</div>' +
                '<span class="cot-index-num" style="color:' + c26 + '">' + d26 + '</span>' +
              '</div>' +
            '</td>' +
            '<td class="col-signal">' + signalBadge(idx26) + '</td>' +
          '</tr>';
      });
    });

    html += '</tbody></table></div>';
    overview.innerHTML = html;

    /* Bind row click → detail view */
    qsa('.cot-row', overview).forEach(function (row) {
      row.addEventListener('click', function () {
        renderDetail(container, this.dataset.key);
      });
    });

    /* Count-up animation on first load */
    if (animate) {
      qsa('.cot-num[data-val]', overview).forEach(function (cell) {
        var v = parseInt(cell.dataset.val, 10);
        if (!isNaN(v)) countUp(cell, v);
      });
    }
  }

  /* ── Detail view ─────────────────────────────────────────────────────── */

  function renderDetail(container, marketKey) {
    STATE.selectedMarket = marketKey;
    var overview = qs('#cot-overview-inner', container);
    var detail   = qs('#cot-detail-inner',   container);
    if (!overview || !detail) return;

    var market = null;
    for (var i = 0; i < STATE.markets.length; i++) {
      if (STATE.markets[i].market_key === marketKey) { market = STATE.markets[i]; break; }
    }
    if (!market) return;

    overview.style.display = 'none';
    detail.style.display   = 'block';

    var catLabel = CATEGORY_LABELS[market.category] || market.category;

    detail.innerHTML =
      '<div class="cot-detail">' +
        '<div class="cot-detail-header">' +
          '<a class="cot-back-btn" href="#">\u2190 All markets</a>' +
          '<span class="cot-breadcrumb-sep"> / </span>' +
          '<span class="cot-breadcrumb-market">' + market.name + '</span>' +
        '</div>' +
        '<h2 class="cot-market-title">' + market.name + '</h2>' +
        '<div class="cot-market-subtitle">' +
          catLabel + ' \u00b7 Data as of ' + (market.last_date || '\u2014') +
        '</div>' +
        '<div class="cot-stats-grid">' +
          _statCard('Commercials',            market.comm_net,  market.change_comm,  '#ef4444') +
          _statCard('Large Speculators',      market.spec_net,  market.change_spec,  '#22c55e') +
          _statCard('Small Traders (Retail)', market.small_net, market.change_small, '#60a5fa') +
          _statCardCotIndex('COT Index 26W', market.cot_index_26) +
          _statCardCotIndex('COT Index 52W', market.cot_index_52) +
        '</div>' +

        '<div class="cot-range-selector">' +
          RANGE_LABELS.map(function (e) {
            return '<button class="cot-range' + (e[0] === STATE.activeRange ? ' active' : '') +
              '" data-weeks="' + e[0] + '">' + e[1] + '</button>';
          }).join('') +
        '</div>' +

        '<div class="cot-charts-wrap">' +

          /* Chart 1: Net Positions (Commercials + Large Speculators) */
          '<div class="cot-chart-card">' +
            '<div class="cot-chart-title-row">' +
              '<span class="cot-chart-title">Net Positions &mdash; Commercials &amp; Large Speculators</span>' +
              '<div class="cot-legend">' +
                '<span class="cot-dot" style="background:#ef4444"></span><span style="color:#ef4444;font-weight:600">Commercials</span>' +
                '<span class="cot-dot" style="background:#22c55e;margin-left:16px"></span><span style="color:#22c55e;font-weight:600">Large Spec.</span>' +
              '</div>' +
              '<button class="cot-reset-zoom" data-chart="net">Reset zoom</button>' +
            '</div>' +
            '<div class="cot-chart-wrap" style="height:280px"><canvas id="cot-chart-net"></canvas></div>' +
          '</div>' +

          /* Chart 1b: Retail (Small Traders) — dedicated, auto-scaled */
          '<div class="cot-chart-card cot-chart-card-retail">' +
            '<div class="cot-chart-title-row">' +
              '<span class="cot-chart-title cot-chart-title-retail">Small Traders (Retail) &mdash; Net Positions</span>' +
              '<div class="cot-legend">' +
                '<span class="cot-dot" style="background:#60a5fa"></span><span style="color:#60a5fa;font-weight:600">Small Traders</span>' +
              '</div>' +
              '<button class="cot-reset-zoom" data-chart="retail">Reset zoom</button>' +
            '</div>' +
            '<div class="cot-chart-wrap" style="height:220px"><canvas id="cot-chart-retail"></canvas></div>' +
          '</div>' +

          /* Chart 2: COT / Retail Index */
          '<div class="cot-chart-card">' +
            '<div class="cot-chart-title-row">' +
              '<span class="cot-chart-title">COT / Retail Index</span>' +
              '<div class="cot-idx-toggle">' +
                '<button class="cot-idx-btn' + (STATE.cotIndexMode === '52w' ? ' active' : '') + '" data-show="52w">52W</button>' +
                '<button class="cot-idx-btn' + (STATE.cotIndexMode === '26w' ? ' active' : '') + '" data-show="26w">26W</button>' +
              '</div>' +
              '<div class="cot-legend" style="display:flex;align-items:center;gap:0">' +
                '<span class="cot-dot" style="background:#f87171"></span><span style="color:#f87171;font-weight:600">COT</span>' +
                '<span class="cot-dot" style="background:#3b82f6;margin-left:12px"></span><span style="color:#3b82f6;font-weight:600">Retail</span>' +
                '<span style="width:1px;height:14px;background:rgba(255,255,255,0.15);margin:0 12px;display:inline-block;vertical-align:middle"></span>' +
                '<span style="color:#ef4444;font-size:11px;opacity:.7">&#8212; 20 sell</span>' +
                '<span style="margin-left:8px;color:#22c55e;font-size:11px;opacity:.7">&#8212; 80 buy</span>' +
              '</div>' +
              '<button class="cot-reset-zoom" data-chart="cot">Reset zoom</button>' +
            '</div>' +
            '<div class="cot-chart-wrap" style="height:200px"><canvas id="cot-chart-cot"></canvas></div>' +
          '</div>' +

          /* Chart 3: Open Interest */
          '<div class="cot-chart-card">' +
            '<div class="cot-chart-title-row">' +
              '<span class="cot-chart-title">Open Interest</span>' +
              '<button class="cot-reset-zoom" data-chart="oi">Reset zoom</button>' +
            '</div>' +
            '<div class="cot-chart-wrap" style="height:160px"><canvas id="cot-chart-oi"></canvas></div>' +
          '</div>' +

        '</div>' +
      '</div>';

    /* Back button */
    qs('.cot-back-btn', detail).addEventListener('click', function (e) {
      e.preventDefault();
      STATE.selectedMarket = null;
      detail.style.display   = 'none';
      overview.style.display = 'block';
      renderTable(container, false);
    });

    /* Range buttons */
    qsa('.cot-range', detail).forEach(function (btn) {
      btn.addEventListener('click', function () {
        STATE.activeRange = parseInt(this.dataset.weeks, 10);
        qsa('.cot-range', detail).forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        fetchAndRenderCharts(marketKey, detail);
      });
    });

    /* COT index toggle */
    qsa('.cot-idx-btn', detail).forEach(function (btn) {
      btn.addEventListener('click', function () {
        STATE.cotIndexMode = this.dataset.show;
        qsa('.cot-idx-btn', detail).forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        var chart = STATE.charts.cot;
        if (!chart) return;
        // [0] Comm 52W  [1] Comm 26W  [2] Retail 52W  [3] Retail 26W  [4] Buy  [5] Sell
        chart.data.datasets[0].hidden = (STATE.cotIndexMode === '26w');
        chart.data.datasets[1].hidden = (STATE.cotIndexMode === '52w');
        chart.data.datasets[2].hidden = (STATE.cotIndexMode === '26w');
        chart.data.datasets[3].hidden = (STATE.cotIndexMode === '52w');
        chart.update();
      });
    });

    /* Reset zoom — resets all charts together */
    qsa('.cot-reset-zoom', detail).forEach(function (btn) {
      btn.addEventListener('click', function () {
        Object.keys(STATE.charts).forEach(function (k) {
          var c = STATE.charts[k];
          if (c && typeof c.resetZoom === 'function') c.resetZoom();
        });
      });
    });

    fetchAndRenderCharts(marketKey, detail);

    setTimeout(function () {
      detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 50);
  }

  /* ── Stat cards ──────────────────────────────────────────────────────── */

  function _statCard(label, value, change, color) {
    var chgHtml = '';
    if (change !== null && change !== undefined) {
      var cls = change >= 0 ? 'pos' : 'neg';
      chgHtml =
        '<div class="cot-stat-change ' + cls + '">' +
          (change >= 0 ? '\u2191' : '\u2193') + ' ' + _spaced(change) +
        '</div>';
    }
    return '<div class="cot-stat-card">' +
      '<div class="cot-stat-label">' + label + '</div>' +
      '<div class="cot-stat-value" style="color:' + color + '">' + formatNet(value) + '</div>' +
      chgHtml +
    '</div>';
  }

  function _statCardCotIndex(label, idx) {
    var display = (idx !== null && idx !== undefined) ? Math.round(idx) : '\u2014';
    var color   = cotColor(idx);
    var barW    = (idx !== null && idx !== undefined) ? Math.max(0, Math.min(100, idx)) : 0;
    var signal  = '';
    if (idx !== null && idx !== undefined) {
      if (idx >= 80)      signal = '<span class="cot-signal buy">BUY</span>';
      else if (idx <= 20) signal = '<span class="cot-signal sell">SELL</span>';
    }
    return '<div class="cot-stat-card">' +
      '<div class="cot-stat-label">' + label + '</div>' +
      '<div class="cot-stat-value" style="color:' + color + '">' + display + signal + '</div>' +
      '<div class="cot-index-bar-bg" style="margin-top:10px">' +
        '<div class="cot-index-bar-fill" style="width:' + barW + '%;background:' + color + ';height:6px;border-radius:3px"></div>' +
      '</div>' +
    '</div>';
  }

  /* ── Charts ──────────────────────────────────────────────────────────── */

  function fetchAndRenderCharts(marketKey, detail) {
    fetch(API_URL + '/api/chart/' + marketKey + '?weeks=' + STATE.activeRange)
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) { renderCharts(data, detail); })
      .catch(function (e) { console.error('COT chart error:', e); });
  }

  function destroyCharts() {
    Object.keys(STATE.charts).forEach(function (k) {
      var c = STATE.charts[k];
      if (c && typeof c.destroy === 'function') c.destroy();
    });
    STATE.charts = {};
  }

  function _scales(zeroLine) {
    return {
      x: {
        ticks:  { maxTicksLimit: 8, color: '#6b7fa3', font: { size: 11 } },
        grid:   { color: 'rgba(255,255,255,0.04)' },
        border: { display: false },
      },
      y: {
        ticks: {
          color: '#6b7fa3',
          font:  { size: 11 },
          callback: function (v) { return formatAxisY(v); },
        },
        grid: {
          color: function (ctx) {
            return (zeroLine && ctx.tick.value === 0)
              ? 'rgba(200,200,200,0.4)'
              : 'rgba(255,255,255,0.04)';
          },
          lineWidth: function (ctx) {
            return (zeroLine && ctx.tick.value === 0) ? 2 : 1;
          },
        },
        border: { display: false },
      },
    };
  }

  var sharedTooltip = {
    backgroundColor: '#1a2540',
    borderColor:     '#f59e0b',
    borderWidth:     1,
    titleFont:       { family: 'Inter' },
    bodyFont:        { family: 'Inter' },
    padding:         10,
  };

  var sharedInteraction = { mode: 'index', intersect: false };

  var zoomOpts = {
    zoom: {
      wheel: { enabled: true, speed: 0.08 },
      pinch: { enabled: true },
      mode:  'x',
      onZoomComplete: function (ctx) { syncZoom(ctx.chart); },
    },
    pan: {
      enabled: true,
      mode:    'x',
      onPanComplete: function (ctx) { syncZoom(ctx.chart); },
    },
  };

  function renderCharts(data, detail) {
    if (!window.Chart) return;
    destroyCharts();

    /* Chart 1: Net Positions — Commercials + Large Speculators only */
    var ctxNet = document.getElementById('cot-chart-net');
    if (ctxNet) {
      STATE.charts.net = new Chart(ctxNet, {
        type: 'line',
        plugins: [cotCrosshairPlugin],
        data: {
          labels: data.dates,
          datasets: [
            { label: 'Commercials', data: data.comm_net, borderColor: '#ef4444', borderWidth: 2, pointRadius: 0, tension: 0.3, fill: false },
            { label: 'Large Spec.', data: data.spec_net, borderColor: '#22c55e', borderWidth: 2, pointRadius: 0, tension: 0.3, fill: false },
          ],
        },
        options: {
          responsive: true, maintainAspectRatio: false, animation: { duration: 400 },
          interaction: sharedInteraction,
          plugins: {
            legend:  { display: false },
            tooltip: Object.assign({}, sharedTooltip, {
              callbacks: { label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + formatNet(ctx.parsed.y); } },
            }),
            zoom: zoomOpts,
          },
          scales: _scales(true),
        },
      });
      bindCrosshair(ctxNet, 'net');
    }

    /* Chart 1b: Small Traders (Retail) — dedicated, auto-scaled */
    var ctxRetail = document.getElementById('cot-chart-retail');
    if (ctxRetail && data.small_net && data.small_net.length) {
      /* Dynamic y-axis: tight to actual data range so movements are clearly visible */
      var validSmall = data.small_net.filter(function(v){ return v !== null && v !== undefined; });
      var rawMin = validSmall.length ? Math.min.apply(null, validSmall) : -1000;
      var rawMax = validSmall.length ? Math.max.apply(null, validSmall) :  1000;
      var dataRange = Math.max(rawMax - rawMin, 1000);
      var padding   = dataRange * 0.15;
      var yMin = rawMin - padding;
      var yMax = rawMax + padding;

      STATE.charts.retail = new Chart(ctxRetail, {
        type: 'line',
        plugins: [cotCrosshairPlugin],
        data: {
          labels: data.dates,
          datasets: [
            {
              label: 'Retail',
              data: data.small_net,
              borderColor: '#60a5fa',
              borderWidth: 2.5,
              pointRadius: 0,
              tension: 0.3,
              fill: 'origin',
              backgroundColor: 'rgba(96,165,250,0.07)',
            },
          ],
        },
        options: {
          responsive: true, maintainAspectRatio: false, animation: { duration: 400 },
          interaction: sharedInteraction,
          plugins: {
            legend: { display: false },
            tooltip: Object.assign({}, sharedTooltip, {
              callbacks: { label: function (ctx) { return ' Retail: ' + formatNet(ctx.parsed.y); } },
            }),
            zoom: zoomOpts,
          },
          scales: Object.assign({}, _scales(true), {
            y: Object.assign({}, _scales(true).y, {
              min: yMin,
              max: yMax,
            }),
          }),
        },
      });
      bindCrosshair(ctxRetail, 'retail');
    }

    /* Chart 2: COT Index */
    var ctxCot = document.getElementById('cot-chart-cot');
    if (ctxCot) {
      var len    = data.dates.length;
      var line80 = Array(len).fill(80);
      var line20 = Array(len).fill(20);

      STATE.charts.cot = new Chart(ctxCot, {
        type: 'line',
        plugins: [cotCrosshairPlugin],
        data: {
          labels: data.dates,
          datasets: [
            {
              label: 'COT 52W', data: data.cot_index,
              hidden: STATE.cotIndexMode === '26w',
              borderColor: '#f87171', borderWidth: 2.5, pointRadius: 0, tension: 0.3, fill: false,
            },
            {
              label: 'COT 26W', data: data.cot_index_26 || [],
              hidden: STATE.cotIndexMode === '52w',
              borderColor: '#f87171', borderWidth: 2.5, pointRadius: 0, tension: 0.3, fill: false,
            },
            {
              label: 'Retail 52W', data: data.retail_index || [],
              hidden: STATE.cotIndexMode === '26w',
              borderColor: '#3b82f6', borderWidth: 1.5, pointRadius: 0, tension: 0.3, fill: false,
            },
            {
              label: 'Retail 26W', data: data.retail_index_26 || [],
              hidden: STATE.cotIndexMode === '52w',
              borderColor: '#3b82f6', borderWidth: 1.5, pointRadius: 0, tension: 0.3, fill: false,
            },
            {
              label: '80 buy',  data: line80,
              borderColor: 'rgba(34,197,94,0.45)', borderWidth: 1,
              borderDash: [4, 4], pointRadius: 0, fill: false,
            },
            {
              label: '20 sell', data: line20,
              borderColor: 'rgba(239,68,68,0.45)', borderWidth: 1,
              borderDash: [4, 4], pointRadius: 0, fill: false,
            },
          ],
        },
        options: {
          responsive: true, maintainAspectRatio: false, animation: { duration: 400 },
          interaction: sharedInteraction,
          plugins: {
            legend:  { display: false },
            tooltip: sharedTooltip,
            zoom:    zoomOpts,
          },
          scales: Object.assign({}, _scales(false), {
            y: Object.assign({}, _scales(false).y, { min: 0, max: 100 }),
          }),
        },
      });
      bindCrosshair(ctxCot, 'cot');

      // Sync hidden state with current mode
      var cotChart = STATE.charts.cot;
      if (cotChart) {
        cotChart.data.datasets[0].hidden = (STATE.cotIndexMode === '26w');
        cotChart.data.datasets[1].hidden = (STATE.cotIndexMode === '52w');
        cotChart.data.datasets[2].hidden = (STATE.cotIndexMode === '26w');
        cotChart.data.datasets[3].hidden = (STATE.cotIndexMode === '52w');
        cotChart.update('none');
      }
      if (detail) {
        qsa('.cot-idx-btn', detail).forEach(function (btn) {
          btn.classList.toggle('active', btn.dataset.show === STATE.cotIndexMode);
        });
      }
    }

    /* Chart 3: Open Interest */
    var ctxOi = document.getElementById('cot-chart-oi');
    if (ctxOi) {
      STATE.charts.oi = new Chart(ctxOi, {
        type: 'line',
        plugins: [cotCrosshairPlugin],
        data: {
          labels: data.dates,
          datasets: [
            {
              label: 'Open Interest', data: data.open_interest,
              borderColor: '#f59e0b', borderWidth: 2, pointRadius: 0, tension: 0.3,
              fill: 'origin', backgroundColor: 'rgba(245,158,11,0.08)',
            },
          ],
        },
        options: {
          responsive: true, maintainAspectRatio: false, animation: { duration: 400 },
          interaction: sharedInteraction,
          plugins: {
            legend:  { display: false },
            tooltip: Object.assign({}, sharedTooltip, {
              callbacks: {
                label: function (ctx) {
                  return ' Open Interest: ' + formatAxisY(ctx.parsed.y);
                },
              },
            }),
            zoom: zoomOpts,
          },
          scales: _scales(false),
        },
      });
      bindCrosshair(ctxOi, 'oi');
    }
  }

  /* ── Resize ──────────────────────────────────────────────────────────── */

  window.addEventListener('resize', function () {
    Object.keys(STATE.charts).forEach(function (k) {
      var c = STATE.charts[k];
      if (c && typeof c.resize === 'function') c.resize();
    });
  });

  /* ── Init ────────────────────────────────────────────────────────────── */

  function init(container) {
    STATE.loading = true;
    showSkeleton(container);

    loadChartJS(function () {
      Promise.all([
        fetch(API_URL + '/api/markets'),
        fetch(API_URL + '/api/status'),
      ])
        .then(function (responses) {
          if (!responses[0].ok) throw new Error('API returned ' + responses[0].status);
          return Promise.all([
            responses[0].json(),
            responses[1].ok ? responses[1].json() : Promise.resolve(null),
          ]);
        })
        .then(function (results) {
          STATE.markets = results[0];
          STATE.loading = false;
          buildLayout(container, results[1]);
          renderTable(container, true);

          /* Expose global nav helper for theme.js —
             allows the fixed nav bar to jump to any section,
             even when the detail view is open. */
          window.cotGoToSection = function (sectionKey) {
            var det = qs('#cot-detail-inner',  container);
            var ov  = qs('#cot-overview-inner', container);

            function scrollTo() {
              var tries = 0;
              (function attempt() {
                var el = document.getElementById('cot-section-' + sectionKey);
                if (el) {
                  var offset = 80 + Math.round(window.innerHeight * 0.12);
                  window.scrollTo({
                    top: Math.max(0, el.getBoundingClientRect().top + window.pageYOffset - offset),
                    behavior: 'smooth',
                  });
                } else if (++tries < 15) {
                  setTimeout(attempt, 300);
                }
              }());
            }

            if (det && det.style.display !== 'none') {
              /* Currently in detail view — go back to overview first */
              STATE.selectedMarket = null;
              det.style.display = 'none';
              ov.style.display  = 'block';
              renderTable(container, false);
              setTimeout(scrollTo, 400);
            } else {
              scrollTo();
            }
          };
        })
        .catch(function (e) {
          STATE.loading = false;
          showError(container, 'API connection error: ' + e.message);
        });
    });
  }

  /* ── Bootstrap ───────────────────────────────────────────────────────── */

  function bootstrap() {
    document.querySelectorAll('.cot-dashboard-container').forEach(function (c) { init(c); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrap);
  } else {
    bootstrap();
  }

}());
