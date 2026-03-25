<?php get_header(); ?>

<style>
/* ── Page-specific styles ── */
.tlb-hero{background:linear-gradient(135deg,rgba(239,68,68,.08) 0%,rgba(245,158,11,.06) 100%);border:1px solid var(--border);border-radius:12px;padding:32px;margin-bottom:40px;position:relative;overflow:hidden}
.tlb-hero::before{content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(245,158,11,.12) 0%,transparent 70%);pointer-events:none}
.tlb-hero-tag{font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);margin-bottom:12px}
.tlb-hero-title{font-size:clamp(22px,4vw,32px);font-weight:800;color:var(--text-primary);margin-bottom:12px;letter-spacing:-.02em;line-height:1.2}
.tlb-hero-title span{color:var(--accent)}
.tlb-hero-desc{font-size:15px;color:var(--text-secondary);line-height:1.7;max-width:620px}

.tlb-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:24px 0}
.tlb-card{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:20px;position:relative;overflow:hidden}
.tlb-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.tlb-card.red::before{background:linear-gradient(90deg,#ef4444,#f87171)}
.tlb-card.green::before{background:linear-gradient(90deg,#22c55e,#4ade80)}
.tlb-card.blue::before{background:linear-gradient(90deg,#3b82f6,#60a5fa)}
.tlb-card-tag{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:8px}
.tlb-card-name{font-size:15px;font-weight:700;margin-bottom:8px}
.tlb-card-desc{font-size:13px;color:var(--text-secondary);line-height:1.6}

.tlb-signal{display:grid;grid-template-columns:1fr auto 1fr;gap:0;align-items:center;margin:24px 0;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.tlb-signal-side{padding:20px 24px;text-align:center}
.tlb-signal-side.red{border-right:1px solid var(--border)}
.tlb-signal-side.amber{border-left:1px solid var(--border)}
.tlb-signal-icon{font-size:28px;margin-bottom:8px}
.tlb-signal-label{font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.tlb-signal-value{font-size:16px;font-weight:800}
.tlb-signal-arrow{padding:20px 16px;font-size:24px;color:var(--text-muted);text-align:center}

.tlb-example{background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin:24px 0}
.tlb-example-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.tlb-example-header .ex-tag{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:rgba(245,158,11,.15);color:var(--accent);border-radius:4px;padding:3px 8px}
.tlb-example-header .ex-title{font-size:14px;font-weight:700;color:var(--text-primary)}
.tlb-example-body{padding:20px}
.tlb-example-body p{font-size:14px;color:var(--text-secondary);line-height:1.7;margin-bottom:16px}
.tlb-example-body p:last-child{margin-bottom:0}

.tlb-img-wrap{border-radius:8px;overflow:hidden;border:1px solid var(--border);margin:16px 0}
.tlb-img-wrap img{width:100%;display:block}
.tlb-img-caption{padding:8px 14px;font-size:12px;color:var(--text-muted);background:rgba(0,0,0,.2);line-height:1.5}

.tlb-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:16px 0}
.tlb-stat{background:rgba(0,0,0,.2);border-radius:8px;padding:14px;text-align:center}
.tlb-stat .s-val{font-size:22px;font-weight:800;margin-bottom:2px}
.tlb-stat .s-lab{font-size:11px;color:var(--text-muted)}

.tlb-result{background:linear-gradient(135deg,rgba(239,68,68,.1),rgba(239,68,68,.04));border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:20px;margin:16px 0;text-align:center}
.tlb-result .r-num{font-size:48px;font-weight:900;color:#ef4444;line-height:1}
.tlb-result .r-sub{font-size:13px;color:var(--text-secondary);margin-top:6px}

.tlb-callout{background:rgba(245,158,11,.06);border-left:3px solid var(--accent);border-radius:0 8px 8px 0;padding:16px 20px;margin:20px 0}
.tlb-callout p{font-size:14px;color:var(--text-secondary);line-height:1.7;margin:0}
.tlb-callout strong{color:var(--accent)}

.tlb-steps{display:flex;flex-direction:column;gap:10px;margin:20px 0}
.tlb-step{display:flex;gap:14px;align-items:flex-start;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:14px 18px}
.tlb-step-num{width:28px;height:28px;border-radius:50%;background:var(--accent);color:#000;font-weight:800;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tlb-step-text{font-size:14px;color:var(--text-secondary);line-height:1.65;padding-top:3px}
.tlb-step-text strong{color:var(--text-primary)}

@media(max-width:700px){
  .tlb-cards{grid-template-columns:1fr}
  .tlb-signal{grid-template-columns:1fr;border-radius:10px}
  .tlb-signal-side.red{border-right:none;border-bottom:1px solid var(--border)}
  .tlb-signal-side.amber{border-left:none;border-top:1px solid var(--border)}
  .tlb-signal-arrow{display:none}
  .tlb-stats{grid-template-columns:1fr}
}
</style>

<main class="cot-info-page">

  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cot-back-home">&#8592; Back to overview</a>

  <!-- Hero -->
  <div class="tlb-hero">
    <div class="tlb-hero-tag">Smart Money Strategy</div>
    <div class="tlb-hero-title">Trade Like <span>Banks</span></div>
    <p class="tlb-hero-desc">
      Every week, commercial banks report their exact futures positions to the CFTC.
      This data is public — and it reveals where the smartest money in the market
      is positioned. Here's how to read it.
    </p>
  </div>

  <!-- Three groups -->
  <div class="cot-info-section">
    <h2>Who Moves the Markets?</h2>
    <p>The COT report divides futures traders into three groups — and they don't all behave the same way.</p>

    <div class="tlb-cards">
      <div class="tlb-card red">
        <div class="tlb-card-tag">Smart Money &mdash; Follow</div>
        <div class="tlb-card-name" style="color:#ef4444">&#9632; Commercials</div>
        <div class="tlb-card-desc">
          Banks, exporters, producers. They hedge real business exposure and
          accumulate <strong>against the trend</strong> — buying low, selling high.
          Almost always on the right side of major reversals.
        </div>
      </div>
      <div class="tlb-card green">
        <div class="tlb-card-tag">Trend Followers &mdash; Often late</div>
        <div class="tlb-card-name" style="color:#22c55e">&#9632; Large Speculators</div>
        <div class="tlb-card-desc">
          Hedge funds chasing momentum. They ride trends well but miss reversals —
          turning maximally bullish near tops and bearish near bottoms.
        </div>
      </div>
      <div class="tlb-card blue">
        <div class="tlb-card-tag">Contrarian Signal &mdash; Fade</div>
        <div class="tlb-card-name" style="color:#60a5fa">&#9632; Small Traders</div>
        <div class="tlb-card-desc">
          Retail traders caught at extremes. When they reach maximum long or short,
          treat it as a warning — they're the last ones in.
        </div>
      </div>
    </div>
  </div>

  <!-- The signal -->
  <div class="cot-info-section">
    <h2>The Divergence Signal</h2>
    <p>The highest-probability COT setup occurs when these two groups are at <strong>opposite extremes at the same time</strong>:</p>

    <div class="tlb-signal">
      <div class="tlb-signal-side red">
        <div class="tlb-signal-icon">🏦</div>
        <div class="tlb-signal-label">Commercials (Banks)</div>
        <div class="tlb-signal-value" style="color:#ef4444">Extreme Short &darr;</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:6px">Selling heavily at the top</div>
      </div>
      <div class="tlb-signal-arrow">&#8644;</div>
      <div class="tlb-signal-side amber">
        <div class="tlb-signal-icon">👥</div>
        <div class="tlb-signal-label">Retail Traders</div>
        <div class="tlb-signal-value" style="color:#22c55e">Extreme Long &uarr;</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:6px">Buying at the exact wrong time</div>
      </div>
    </div>

    <div class="tlb-callout">
      <p><strong>The logic:</strong> Banks are distributing their positions to an eager crowd. When this gap between the two groups reaches an extreme, the market is typically close to a significant reversal.</p>
    </div>
  </div>

  <!-- Real example -->
  <div class="cot-info-section">
    <h2>Real Example: AUD/USD &mdash; September 2017</h2>

    <div class="tlb-example">
      <div class="tlb-example-header">
        <span class="ex-tag">Case Study</span>
        <span class="ex-title">AUD/USD Futures &nbsp;&middot;&nbsp; 2017-09-12</span>
      </div>
      <div class="tlb-example-body">

        <div class="tlb-stats">
          <div class="tlb-stat">
            <div class="s-val" style="color:#ef4444">&minus;83,253</div>
            <div class="s-lab">Commercials net (extreme short)</div>
          </div>
          <div class="tlb-stat">
            <div class="s-val" style="color:#60a5fa">+20,220</div>
            <div class="s-lab">Retail net (extreme long)</div>
          </div>
          <div class="tlb-stat">
            <div class="s-val" style="color:#f59e0b">~0</div>
            <div class="s-lab">COT Index (maximum short)</div>
          </div>
        </div>

        <p>Commercials were at one of their deepest short positions in years. Simultaneously, retail traders were near their highest long reading — fully committed to the upside.</p>

        <div class="tlb-img-wrap">
          <img src="<?php echo esc_url( get_template_directory_uri() . '/img/aud-commercials-short.png' ); ?>"
               alt="AUD Net Positions: Commercials -83,253 — 2017-09-12" loading="lazy">
          <div class="tlb-img-caption">
            Net Positions — AUD/USD &nbsp;|&nbsp;
            <span style="color:#ef4444">&#9632; Commercials: &minus;83,253</span> &nbsp;&middot;&nbsp;
            <span style="color:#22c55e">&#9632; Large Spec.: +63,033</span>
          </div>
        </div>

        <div class="tlb-img-wrap">
          <img src="<?php echo esc_url( get_template_directory_uri() . '/img/aud-retail-long.png' ); ?>"
               alt="AUD Retail net +20,220 — 2017-09-12" loading="lazy">
          <div class="tlb-img-caption">
            Small Traders (Retail) — AUD/USD &nbsp;|&nbsp;
            <span style="color:#60a5fa">&#9632; Retail: +20,220</span> &nbsp;&middot;&nbsp; Near multi-year high
          </div>
        </div>

        <p>The divergence was clear. What happened next confirmed what the COT data had been warning:</p>

        <div class="tlb-result">
          <div class="r-num">&minus;7.65%</div>
          <div class="r-sub">AUD/USD fell over the following 91 days &nbsp;&middot;&nbsp; Banks were right. Retail was wrong.</div>
        </div>

        <div class="tlb-img-wrap">
          <img src="<?php echo esc_url( get_template_directory_uri() . '/img/aud-price-drop.png' ); ?>"
               alt="AUD/USD -7.65% drop over 91 days" loading="lazy">
          <div class="tlb-img-caption">
            AUD/USD Price &nbsp;|&nbsp; <span style="color:#ef4444">&minus;7.65%</span> over 91 days &nbsp;&middot;&nbsp; Following the COT divergence peak
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- How to use -->
  <div class="cot-info-section">
    <h2>How to Apply This Today</h2>
    <div class="tlb-steps">
      <div class="tlb-step">
        <div class="tlb-step-num">1</div>
        <div class="tlb-step-text"><strong>Scan for COT Index extremes.</strong> On the main dashboard, look for markets with COT Index 26W below 20 (SELL) or above 80 (BUY).</div>
      </div>
      <div class="tlb-step">
        <div class="tlb-step-num">2</div>
        <div class="tlb-step-text"><strong>Open the detail chart.</strong> Confirm Commercials are at a multi-year extreme — not just a short-term shift.</div>
      </div>
      <div class="tlb-step">
        <div class="tlb-step-num">3</div>
        <div class="tlb-step-text"><strong>Check retail.</strong> Commercials extreme short + retail extreme long = divergence confirmed. The wider the gap, the stronger the signal.</div>
      </div>
      <div class="tlb-step">
        <div class="tlb-step-num">4</div>
        <div class="tlb-step-text"><strong>Use as directional bias.</strong> COT signals can lead price by 4–8 weeks. Combine with your technical analysis for precise entry timing — never trade COT data in isolation.</div>
      </div>
    </div>
    <div class="tlb-callout">
      <p><strong>Disclaimer:</strong> COT data is an analytical tool, not a guaranteed signal. All trading involves risk of loss. For educational purposes only.</p>
    </div>
  </div>

</main>

<?php get_footer(); ?>
