<?php get_header(); ?>

<!-- Hero banner -->
<section class="cot-hero">
  <div class="cot-hero-inner">
    <div class="cot-hero-eyebrow">Czech COT Terminal</div>
    <h1>COT Reports <span>Overview</span></h1>
    <p>
      Weekly Commitments of Traders data from CFTC.gov &mdash;
      positions of <strong>banks</strong>, <strong>hedge funds</strong> and <strong>retail</strong>
      across futures markets of all major instruments.
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
