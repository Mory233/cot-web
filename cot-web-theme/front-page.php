<?php get_header(); ?>

<!-- Hero banner -->
<section class="cot-hero">
  <div class="cot-hero-inner">
    <div class="cot-hero-eyebrow">CFTC Commitments of Traders &nbsp;&middot;&nbsp; Free &nbsp;&middot;&nbsp; No Signup Required</div>
    <h1>Track Where <span>Banks&nbsp;&amp;&nbsp;Funds</span> Really Stand</h1>
    <p>
      COT Terminal turns complex CFTC filings into <strong>clear, interactive charts</strong>.
      One dashboard covering 50+ futures markets across Forex, Indices, Metals, Energy and Crypto
      &mdash; with 25&nbsp;years of history and a new update every Friday.
    </p>
    <div class="cot-hero-meta">
      <span class="cot-hero-badge">
        <span class="dot" style="background:#22c55e;box-shadow:0 0 6px rgba(34,197,94,.6)"></span>
        Published every Friday &mdash; data as of prev. Tuesday
      </span>
      <span class="cot-hero-badge">
        <span class="dot" style="background:#00d4ff;box-shadow:0 0 6px rgba(0,212,255,.6)"></span>
        25 years of historical data
      </span>
      <span class="cot-hero-badge">
        <span class="dot" style="background:#a78bfa;box-shadow:0 0 6px rgba(167,139,250,.6)"></span>
        <span class="cot-badge-markets">51 markets</span>
      </span>
    </div>
  </div>
</section>

<!-- COT Dashboard -->
<section class="cot-dashboard-section">
  <?php echo do_shortcode( '[cot_dashboard]' ); ?>
</section>

<?php get_footer(); ?>
