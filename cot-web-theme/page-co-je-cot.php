<?php get_header(); ?>

<main class="cot-info-page">

  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cot-back-home">
    &#8592; Back to market overview
  </a>

  <h1>What is the COT Report?</h1>
  <p class="cot-lead">
    Commitments of Traders (COT) is a weekly report published by the U.S. regulator
    CFTC that reveals the positions of major market participants on futures markets &mdash;
    hedge funds, banks, and small speculators.
  </p>

  <!-- Section 1 -->
  <div class="cot-info-section">
    <h2>What is the COT Report?</h2>
    <p>
      <strong>Commitments of Traders (COT)</strong> is a report published every Friday
      by the U.S. Commodity Futures Trading Commission (CFTC). It contains data on the
      positions of traders on U.S. futures exchanges &mdash; Chicago Mercantile Exchange (CME),
      ICE Futures, and others.
    </p>
    <p>
      This website displays the <strong>Disaggregated &amp; Financial Traders Futures</strong>
      reports &mdash; the most detailed versions published by CFTC, covering both commodity
      and financial futures markets.
    </p>

    <div class="cot-timeline">
      <div class="cot-tl-item">
        <div class="cot-tl-dot" style="background:#60a5fa"></div>
        <div class="cot-tl-label">Tuesday</div>
        <div class="cot-tl-desc">CFTC collects position data from all reportable traders</div>
      </div>
      <div class="cot-tl-arrow">&#8594;</div>
      <div class="cot-tl-item">
        <div class="cot-tl-dot" style="background:#f59e0b"></div>
        <div class="cot-tl-label">Friday 3:30 PM ET</div>
        <div class="cot-tl-desc">CFTC publishes the report (3-day processing lag)</div>
      </div>
      <div class="cot-tl-arrow">&#8594;</div>
      <div class="cot-tl-item">
        <div class="cot-tl-dot" style="background:#22c55e"></div>
        <div class="cot-tl-label">Friday evening</div>
        <div class="cot-tl-desc">This dashboard updates automatically with new data</div>
      </div>
    </div>

    <p style="margin-top:16px">
      <strong>Example:</strong> The report published on <strong>Friday, March 20, 2026</strong>
      contains data as of <strong>Tuesday, March 17, 2026</strong>. The 3-day lag is a statutory
      deadline set by regulation and is the industry standard.
    </p>
  </div>

  <!-- Section 2 -->
  <div class="cot-info-section">
    <h2>Three Groups of Traders</h2>
    <p>The Legacy report divides all traders into three categories:</p>

    <div class="cot-group-cards">
      <div class="cot-group-card">
        <div class="gc-label">Group 1</div>
        <div class="gc-name" style="color:#ff4560">Commercials</div>
        <div class="gc-desc">
          Large corporations, banks and producers (e.g. gold miners, oil companies,
          exporters). They use futures primarily for <strong>hedging</strong> real
          business risk. They are considered "smart money".
        </div>
      </div>
      <div class="cot-group-card">
        <div class="gc-label">Group 2</div>
        <div class="gc-name" style="color:#00e396">Large Speculators</div>
        <div class="gc-desc">
          Hedge funds, CTA funds and large institutional speculators. They trade
          exclusively for <strong>speculative profit</strong>. Their positions are
          the most watched indicator of market sentiment.
        </div>
      </div>
      <div class="cot-group-card">
        <div class="gc-label">Group 3</div>
        <div class="gc-name" style="color:#008ffb">Small Traders</div>
        <div class="gc-desc">
          Retail traders and small entities whose positions do not meet the reporting
          threshold. Generally less followed, but can signal extreme sentiment.
        </div>
      </div>
    </div>
  </div>

  <!-- Section 3 -->
  <div class="cot-info-section">
    <h2>What is the Net Position?</h2>
    <p>
      <strong>Net Position = Long contracts &minus; Short contracts</strong>
    </p>
    <p>
      A positive net position means the group holds more long contracts
      (betting on a rise). A negative position means more short contracts (betting on a fall).
      The week-over-week change in net position indicates whether large players are
      increasing or reducing their exposure.
    </p>
    <p>
      The dashboard shows net positions and weekly changes for <strong>all three groups</strong>
      &mdash; Commercials, Large Speculators and Small Traders (Retail). Tracking the weekly
      change in positions is one of the most important signals.
    </p>
  </div>

  <!-- Section 4 -->
  <div class="cot-info-section">
    <h2>COT Index &mdash; How to Read It?</h2>
    <p>
      The <strong>COT Index</strong> is a normalized indicator (0&ndash;100) expressing
      where the current net position of Commercials stands relative to the past
      <strong>26 or 52 weeks</strong>. Index 100 = Commercials most long over the
      observed period. Index 0 = most short.
    </p>
    <p>
      Formula: <em>(current &minus; minimum) &divide; (maximum &minus; minimum) &times; 100</em>
    </p>

    <div class="cot-index-scale">
      <div class="cot-scale-item bearish">
        <div class="si-range">0&ndash;20</div>
        <div class="si-label"><strong>SELL</strong><br>Commercials extremely short<br>&rarr; prices are high, market near a top</div>
      </div>
      <div class="cot-scale-item neutral">
        <div class="si-range">20&ndash;80</div>
        <div class="si-label"><strong>NEUTRAL</strong><br>Neutral zone<br>&rarr; no clear extreme signal</div>
      </div>
      <div class="cot-scale-item bullish">
        <div class="si-range">80&ndash;100</div>
        <div class="si-label"><strong>BUY</strong><br>Commercials extremely long<br>&rarr; prices are low, market near a bottom</div>
      </div>
    </div>

    <p style="margin-top:16px">
      <strong>Signal logic:</strong> Commercials (banks, producers) are natural
      hedgers &mdash; they buy futures when prices are <em>low</em> and sell them
      when prices are <em>high</em>. Therefore:
      <strong>index &ge; 80 = BUY</strong> (Commercials buying heavily = prices low),
      <strong>index &le; 20 = SELL</strong> (Commercials selling heavily = prices high).
      The 26W signal reacts faster, the 52W is more robust.
    </p>
  </div>

  <!-- Section 5 -->
  <div class="cot-info-section">
    <h2>How to Use This Dashboard?</h2>
    <p>
      The dashboard shows current data for more than <strong>50 markets</strong> &mdash;
      currency pairs, commodities, equity indices, bonds and crypto. Data is updated
      automatically every Friday after the CFTC report is released.
    </p>
    <p>The table shows for each market:</p>
    <p>
      &bull; <strong>Commercials</strong> &mdash; Position + Change (banks, producers)<br>
      &bull; <strong>Large Speculators</strong> &mdash; Position + Change (hedge funds)<br>
      &bull; <strong>Small Traders (Retail)</strong> &mdash; Position + Change (retail)<br>
      &bull; <strong>COT Index 26W</strong> &mdash; normalized indicator 0&ndash;100<br>
      &bull; <strong>Signal</strong> &mdash; BUY / NEUTRAL / SELL based on COT Index
    </p>
    <p>Navigation:</p>
    <p>
      &bull; <strong>Top bar</strong> (Forex, Indices&hellip;) &mdash; jumps directly to that section in the table<br>
      &bull; <strong>Search</strong> &mdash; quickly find a specific market<br>
      &bull; <strong>Click on a market</strong> &mdash; opens detail with position charts, COT index and Open Interest<br>
      &bull; <strong>Time ranges</strong> (1Y / 2Y / 5Y / 10Y / 25Y) &mdash; historical data up to 25 years back
    </p>
  </div>

  <!-- FAQ -->
  <div class="cot-info-section">
    <h2>Frequently Asked Questions</h2>
    <div class="cot-faq">
      <div class="cot-faq-item">
        <div class="cot-faq-q">When is the new COT report released?</div>
        <div class="cot-faq-a">
          The CFTC publishes the report every Friday at 3:30 PM EST (9:30 PM CET).
          The data in the report is as of the previous Tuesday &mdash; there is a 3-day lag.
          The dashboard updates automatically every Friday evening.
        </div>
      </div>
      <div class="cot-faq-item">
        <div class="cot-faq-q">Why is the data 3 days delayed?</div>
        <div class="cot-faq-a">
          The CFTC collects data every Tuesday and processes it by Friday. This delay
          is a statutory deadline set by regulation. It is the industry standard.
        </div>
      </div>
      <div class="cot-faq-item">
        <div class="cot-faq-q">Which markets are available?</div>
        <div class="cot-faq-a">
          EUR/USD, GBP/USD, JPY, CHF, CAD, AUD, NZD, MXN, USD Index,
          Gold, Silver, Copper, Crude Oil WTI, Natural Gas,
          Corn, Wheat, Soybeans, S&amp;P 500, NASDAQ 100, Dow Jones,
          10Y T-Notes and Bitcoin.
        </div>
      </div>
      <div class="cot-faq-item">
        <div class="cot-faq-q">How far back does the data go?</div>
        <div class="cot-faq-a">
          Data goes back to the year 2000, over 25 years of historical data.
          In the chart you can switch between 1Y, 2Y, 5Y, 10Y and 25Y ranges.
        </div>
      </div>
      <div class="cot-faq-item">
        <div class="cot-faq-q">Is the COT report a reliable trading signal?</div>
        <div class="cot-faq-a">
          COT data is a valuable tool for understanding the sentiment of institutional
          players, but it is not a direct trading signal. It serves as a supplementary
          indicator to technical and fundamental analysis. Past results do not guarantee
          future performance.
        </div>
      </div>
    </div>
  </div>

</main>

<?php get_footer(); ?>
