<?php get_header(); ?>

<style>
.cot-case-img-wrap{border:1px solid var(--border);border-radius:10px;overflow:hidden;background:var(--bg-card);margin:20px 0}
.cot-case-img-wrap img{width:100%;display:block}
.cot-case-img-label{padding:8px 14px;font-size:12px;color:var(--text-secondary);border-top:1px solid var(--border)}
.cot-signal-row{display:flex;gap:10px;flex-wrap:wrap;margin:20px 0}
.cot-signal-pill{display:flex;align-items:center;gap:10px;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:12px 16px;flex:1;min-width:160px}
.cot-signal-pill .sp-label{font-size:11px;color:var(--text-muted);margin-bottom:2px}
.cot-signal-pill .sp-value{font-size:13px;font-weight:700}
.cot-highlight-box{background:rgba(245,158,11,.06);border-left:3px solid var(--accent);border-radius:0 8px 8px 0;padding:16px 20px;margin:20px 0}
.cot-highlight-box p{color:var(--text-secondary);font-size:14px;line-height:1.7;margin:0}
.cot-highlight-box strong{color:var(--accent)}
.cot-checklist{margin:16px 0;display:flex;flex-direction:column;gap:8px}
.cot-checklist-item{display:flex;gap:12px;align-items:flex-start;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:14px 16px}
.cot-checklist-item .ci-num{width:24px;height:24px;border-radius:50%;background:var(--accent);color:#000;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.cot-checklist-item .ci-text{font-size:14px;color:var(--text-secondary);line-height:1.65}
.cot-checklist-item .ci-text strong{color:var(--text-primary)}
@media(max-width:600px){.cot-signal-row{flex-direction:column}}
</style>

<main class="cot-info-page">

  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cot-back-home">
    &#8592; Back to market overview
  </a>

  <h1>Trade Like Banks</h1>
  <p class="cot-lead">
    Every week, the world's biggest banks report their exact futures positions to the CFTC —
    and this data is public. Here's how to read it and trade on the same side as the smart money.
  </p>

  <!-- Who is who -->
  <div class="cot-info-section">
    <h2>Three Groups — One Clear Edge</h2>
    <div class="cot-group-cards">
      <div class="cot-group-card">
        <div class="gc-label">Smart Money — Follow them</div>
        <div class="gc-name" style="color:#ef4444">&#9632; Commercials</div>
        <div class="gc-desc">
          Banks, exporters, producers. They hedge real business risk and accumulate
          positions <strong>against the trend</strong> — buying low, selling high.
          Almost always on the right side of major moves.
        </div>
      </div>
      <div class="cot-group-card">
        <div class="gc-label">Trend Followers — Often late</div>
        <div class="gc-name" style="color:#22c55e">&#9632; Large Speculators</div>
        <div class="gc-desc">
          Hedge funds chasing momentum. They arrive late — maximally bullish near
          tops, maximally bearish near bottoms.
        </div>
      </div>
      <div class="cot-group-card">
        <div class="gc-label">Contrarian Signal — Fade them</div>
        <div class="gc-name" style="color:#60a5fa">&#9632; Small Traders (Retail)</div>
        <div class="gc-desc">
          Retail traders consistently caught at extremes. When they reach maximum
          long or short — treat it as a warning flag.
        </div>
      </div>
    </div>
  </div>

  <!-- The signal -->
  <div class="cot-info-section">
    <h2>The Divergence Signal</h2>
    <p>The most powerful COT setup: <strong>Commercials and retail at opposite extremes simultaneously.</strong></p>

    <div class="cot-signal-row">
      <div class="cot-signal-pill">
        <span style="font-size:20px">🏦</span>
        <div>
          <div class="sp-label">Commercials</div>
          <div class="sp-value" style="color:#ef4444">Extreme Short &darr;</div>
        </div>
      </div>
      <div class="cot-signal-pill">
        <span style="font-size:20px">👥</span>
        <div>
          <div class="sp-label">Retail Traders</div>
          <div class="sp-value" style="color:#22c55e">Extreme Long &uarr;</div>
        </div>
      </div>
      <div class="cot-signal-pill">
        <span style="font-size:20px">⚠️</span>
        <div>
          <div class="sp-label">Signal</div>
          <div class="sp-value" style="color:#f59e0b">Top Likely — SELL</div>
        </div>
      </div>
    </div>

    <p>Banks are selling. The crowd is buying. One of them is always wrong at extremes — and history shows it's rarely the banks.</p>
  </div>

  <!-- Real example -->
  <div class="cot-info-section">
    <h2>Real Example: AUD/USD — September 2017</h2>
    <p>
      On <strong>2017-09-12</strong>, Commercials were net
      <strong style="color:#ef4444">&minus;83,253</strong> (extreme short) while retail
      was net <strong style="color:#60a5fa">+20,220</strong> (extreme long).
      A textbook divergence.
    </p>

    <div class="cot-case-img-wrap">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/img/aud-commercials-short.png' ); ?>"
           alt="AUD Net Positions: Commercials -83,253 vs Large Spec +63,033 on 2017-09-12" loading="lazy">
      <div class="cot-case-img-label">
        Commercials (red): <strong style="color:#ef4444">&minus;83,253</strong> &nbsp;&middot;&nbsp;
        Large Spec. (green): <strong style="color:#22c55e">+63,033</strong> &nbsp;&middot;&nbsp; 2017-09-12
      </div>
    </div>

    <div class="cot-case-img-wrap">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/img/aud-retail-long.png' ); ?>"
           alt="AUD Retail net positions +20,220 on 2017-09-12" loading="lazy">
      <div class="cot-case-img-label">
        Retail (Small Traders): <strong style="color:#60a5fa">+20,220</strong> &nbsp;&middot;&nbsp;
        Near a multi-year extreme long &nbsp;&middot;&nbsp; 2017-09-12
      </div>
    </div>

    <p style="margin-top:20px">
      Over the next <strong>91 days</strong>, AUD/USD dropped
      <strong style="color:#ef4444">&minus;7.65%</strong>. The banks were right. Retail was wrong.
    </p>

    <div class="cot-case-img-wrap">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/img/aud-price-drop.png' ); ?>"
           alt="AUD/USD price drop -7.65% over 91 days" loading="lazy">
      <div class="cot-case-img-label">
        AUD/USD price: <strong style="color:#ef4444">&minus;7.65%</strong> over 91 days &nbsp;&middot;&nbsp;
        Following the COT divergence peak
      </div>
    </div>

    <div class="cot-highlight-box">
      <p>
        <strong>The setup in one line:</strong> Commercials &minus;83K + Retail +20K + COT Index near 0
        &rarr; &minus;7.65% in 91 days. This pattern repeats across markets and decades.
      </p>
    </div>
  </div>

  <!-- Checklist -->
  <div class="cot-info-section">
    <h2>How to Use This on the Dashboard</h2>
    <div class="cot-checklist">
      <div class="cot-checklist-item">
        <div class="ci-num">1</div>
        <div class="ci-text"><strong>Find COT Index extremes.</strong> Look for markets where the Index 26W is below 20 (SELL) or above 80 (BUY).</div>
      </div>
      <div class="cot-checklist-item">
        <div class="ci-num">2</div>
        <div class="ci-text"><strong>Confirm on the chart.</strong> Open the market detail and check that Commercials are at a multi-year extreme — not just a weekly blip.</div>
      </div>
      <div class="cot-checklist-item">
        <div class="ci-num">3</div>
        <div class="ci-text"><strong>Check retail.</strong> Commercials extreme short + retail extreme long = the divergence is confirmed. The wider the gap, the stronger the signal.</div>
      </div>
      <div class="cot-checklist-item">
        <div class="ci-num">4</div>
        <div class="ci-text"><strong>Use COT as directional bias.</strong> Signals can lead price by 4–8 weeks. Combine with technical analysis for precise entry timing.</div>
      </div>
    </div>

    <div class="cot-highlight-box">
      <p>
        <strong>Disclaimer:</strong> COT data is an analytical tool, not a guaranteed signal.
        All trading involves risk. Use this as one input among several in your process.
      </p>
    </div>
  </div>

</main>

<?php get_footer(); ?>
