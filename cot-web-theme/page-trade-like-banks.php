<?php get_header(); ?>

<style>
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

.tlb-signal{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;margin:24px 0;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.tlb-signal-side{padding:20px 24px;text-align:center}
.tlb-signal-side.red{border-right:1px solid var(--border)}
.tlb-signal-side.amber{border-left:1px solid var(--border)}
.tlb-signal-icon{font-size:28px;margin-bottom:8px}
.tlb-signal-label{font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.tlb-signal-value{font-size:16px;font-weight:800}
.tlb-signal-arrow{padding:20px 12px;font-size:22px;color:var(--text-muted);text-align:center}

.tlb-callout{background:rgba(245,158,11,.06);border-left:3px solid var(--accent);border-radius:0 8px 8px 0;padding:16px 20px;margin:20px 0}
.tlb-callout p{font-size:14px;color:var(--text-secondary);line-height:1.7;margin:0}
.tlb-callout strong{color:var(--accent)}

/* ── Case study data block ── */
.tlb-snapshot{background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin:24px 0}
.tlb-snapshot-hdr{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.tlb-snapshot-hdr .s-tag{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:rgba(245,158,11,.15);color:var(--accent);border-radius:4px;padding:3px 8px;white-space:nowrap}
.tlb-snapshot-hdr .s-title{font-size:14px;font-weight:700;color:var(--text-primary)}
.tlb-snapshot-hdr .s-date{font-size:12px;color:var(--text-muted);margin-left:auto}
.tlb-snapshot-body{padding:20px;display:flex;flex-direction:column;gap:14px}

.tlb-pos-row{display:flex;flex-direction:column;gap:6px}
.tlb-pos-label{display:flex;justify-content:space-between;align-items:baseline}
.tlb-pos-name{font-size:13px;font-weight:600}
.tlb-pos-num{font-size:15px;font-weight:800}
.tlb-bar-track{height:8px;background:rgba(255,255,255,.07);border-radius:4px;overflow:hidden;position:relative}
.tlb-bar-fill{height:100%;border-radius:4px;transition:width .4s ease}
.tlb-pos-note{font-size:11px;color:var(--text-muted);margin-top:2px}

.tlb-divider{height:1px;background:var(--border)}

.tlb-result-big{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.tlb-result-cell{background:rgba(0,0,0,.25);border-radius:8px;padding:16px;text-align:center}
.tlb-result-cell .rc-val{font-size:32px;font-weight:900;line-height:1}
.tlb-result-cell .rc-lab{font-size:11px;color:var(--text-muted);margin-top:5px}

.tlb-steps{display:flex;flex-direction:column;gap:10px;margin:20px 0}
.tlb-step{display:flex;gap:14px;align-items:flex-start;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:14px 18px}
.tlb-step-num{width:28px;height:28px;border-radius:50%;background:var(--accent);color:#000;font-weight:800;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tlb-step-text{font-size:14px;color:var(--text-secondary);line-height:1.65;padding-top:3px}
.tlb-step-text strong{color:var(--text-primary)}

@media(max-width:700px){
  .tlb-cards{grid-template-columns:1fr}
  .tlb-signal{grid-template-columns:1fr}
  .tlb-signal-side.red{border-right:none;border-bottom:1px solid var(--border)}
  .tlb-signal-side.amber{border-left:none;border-top:1px solid var(--border)}
  .tlb-signal-arrow{display:none}
  .tlb-result-big{grid-template-columns:1fr}
}
</style>

<main class="cot-info-page">

  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cot-back-home">&#8592; Back to overview</a>

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
        <div class="tlb-card-desc">Banks, exporters, producers. They hedge real exposure and accumulate <strong>against the trend</strong> — buying low, selling high. Almost always on the right side at reversals.</div>
      </div>
      <div class="tlb-card green">
        <div class="tlb-card-tag">Trend Followers &mdash; Often late</div>
        <div class="tlb-card-name" style="color:#22c55e">&#9632; Large Speculators</div>
        <div class="tlb-card-desc">Hedge funds chasing momentum. They ride trends but miss reversals — maximally bullish near tops, bearish near bottoms.</div>
      </div>
      <div class="tlb-card blue">
        <div class="tlb-card-tag">Contrarian Signal &mdash; Fade</div>
        <div class="tlb-card-name" style="color:#60a5fa">&#9632; Small Traders (Retail)</div>
        <div class="tlb-card-desc">Retail traders consistently caught at extremes. When they hit maximum long or short — treat it as a warning flag.</div>
      </div>
    </div>
  </div>

  <!-- The divergence signal -->
  <div class="cot-info-section">
    <h2>The Divergence Signal</h2>
    <p>The most powerful setup: <strong>Commercials and retail at opposite extremes at the same time.</strong></p>
    <div class="tlb-signal">
      <div class="tlb-signal-side red">
        <div class="tlb-signal-icon">🏦</div>
        <div class="tlb-signal-label">Commercials (Banks)</div>
        <div class="tlb-signal-value" style="color:#ef4444">Extreme Short &darr;</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:6px">Distributing at the top</div>
      </div>
      <div class="tlb-signal-arrow">&#8644;</div>
      <div class="tlb-signal-side amber">
        <div class="tlb-signal-icon">👥</div>
        <div class="tlb-signal-label">Retail Traders</div>
        <div class="tlb-signal-value" style="color:#22c55e">Extreme Long &uarr;</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:6px">Buying at the wrong time</div>
      </div>
    </div>
    <div class="tlb-callout">
      <p><strong>The logic:</strong> Banks are selling to an eager crowd. When this gap reaches a multi-year extreme, the market is typically close to a significant reversal.</p>
    </div>
  </div>

  <!-- Real example — data only, no images -->
  <div class="cot-info-section">
    <h2>Real Example: AUD/USD &mdash; September 2017</h2>
    <p>Every condition for the divergence setup was present. The COT data was sending a clear warning — weeks before the move.</p>

    <div class="tlb-snapshot">
      <div class="tlb-snapshot-hdr">
        <span class="s-tag">Live Data Snapshot</span>
        <span class="s-title">AUD/USD Futures &mdash; Net Positions</span>
        <span class="s-date">Week of 2017-09-12</span>
      </div>
      <div class="tlb-snapshot-body">

        <!-- Commercials -->
        <div class="tlb-pos-row">
          <div class="tlb-pos-label">
            <span class="tlb-pos-name" style="color:#ef4444">&#9632; Commercials</span>
            <span class="tlb-pos-num" style="color:#ef4444">&minus;83&thinsp;253</span>
          </div>
          <div class="tlb-bar-track">
            <div class="tlb-bar-fill" style="width:100%;background:#ef4444;opacity:.7"></div>
          </div>
          <div class="tlb-pos-note">Extreme short — near multi-year maximum &nbsp;|&nbsp; COT Index: ~0 &nbsp;&#x2192;&nbsp; SELL signal</div>
        </div>

        <!-- Large Spec -->
        <div class="tlb-pos-row">
          <div class="tlb-pos-label">
            <span class="tlb-pos-name" style="color:#22c55e">&#9632; Large Speculators</span>
            <span class="tlb-pos-num" style="color:#22c55e">+63&thinsp;033</span>
          </div>
          <div class="tlb-bar-track">
            <div class="tlb-bar-fill" style="width:76%;background:#22c55e;opacity:.7"></div>
          </div>
          <div class="tlb-pos-note">Heavily long — chasing the uptrend, arriving late</div>
        </div>

        <!-- Retail -->
        <div class="tlb-pos-row">
          <div class="tlb-pos-label">
            <span class="tlb-pos-name" style="color:#60a5fa">&#9632; Small Traders (Retail)</span>
            <span class="tlb-pos-num" style="color:#60a5fa">+20&thinsp;220</span>
          </div>
          <div class="tlb-bar-track">
            <div class="tlb-bar-fill" style="width:86%;background:#60a5fa;opacity:.7"></div>
          </div>
          <div class="tlb-pos-note">Near multi-year extreme long — retail fully committed to the upside</div>
        </div>

        <div class="tlb-divider"></div>

        <!-- Result -->
        <div>
          <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:10px">What happened next &mdash; 91 days later</div>
          <div class="tlb-result-big">
            <div class="tlb-result-cell">
              <div class="rc-val" style="color:#ef4444">&minus;7.65%</div>
              <div class="rc-lab">AUD/USD price move</div>
            </div>
            <div class="tlb-result-cell">
              <div class="rc-val" style="color:var(--text-primary)">91</div>
              <div class="rc-lab">days &nbsp;|&nbsp; 64 bars</div>
            </div>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin-top:12px;line-height:1.65">
            Banks were short. Retail was long. Price followed the banks — exactly as the COT data had warned.
            Retail traders holding longs from the peak absorbed the entire move down.
          </p>
        </div>

      </div>
    </div>

    <div class="tlb-callout">
      <p><strong>One line summary:</strong> Commercials &minus;83K &plus; Retail &plus;20K &plus; COT Index ~0 &equals; Divergence confirmed &rarr; &minus;7.65% in 91 days. This pattern repeats across markets and decades.</p>
    </div>
  </div>

  <!-- How to use -->
  <div class="cot-info-section">
    <h2>How to Apply This Today</h2>
    <div class="tlb-steps">
      <div class="tlb-step">
        <div class="tlb-step-num">1</div>
        <div class="tlb-step-text"><strong>Scan COT Index extremes.</strong> On the dashboard, look for markets with Index 26W below 20 (SELL) or above 80 (BUY).</div>
      </div>
      <div class="tlb-step">
        <div class="tlb-step-num">2</div>
        <div class="tlb-step-text"><strong>Confirm on the chart.</strong> Open the market detail and check that Commercials are at a multi-year extreme — not just a short-term swing.</div>
      </div>
      <div class="tlb-step">
        <div class="tlb-step-num">3</div>
        <div class="tlb-step-text"><strong>Check retail.</strong> Commercials extreme short &plus; retail extreme long &equals; divergence confirmed. The wider the gap, the stronger the signal.</div>
      </div>
      <div class="tlb-step">
        <div class="tlb-step-num">4</div>
        <div class="tlb-step-text"><strong>Use as directional bias.</strong> COT signals can lead price by 4–8 weeks. Combine with technical analysis for precise entry — never trade COT data alone.</div>
      </div>
    </div>
    <div class="tlb-callout">
      <p><strong>Disclaimer:</strong> COT data is an analytical tool, not a guaranteed signal. All trading involves risk. For educational purposes only.</p>
    </div>
  </div>

</main>

<?php get_footer(); ?>
