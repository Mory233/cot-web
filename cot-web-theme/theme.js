// COT Terminal Theme JS
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        var NAV_H = 80; // nav height
        // Extra offset so the section header lands visibly below the nav
        // (roughly 15% of viewport height as breathing room)
        function scrollOffset() {
            return NAV_H + Math.round(window.innerHeight * 0.12);
        }

        // ── Mobile nav toggle ──────────────────────────────────
        var toggle = document.getElementById('cot-nav-toggle');
        var mobile = document.getElementById('cot-nav-mobile');

        if (toggle && mobile) {
            function toggleMenu(e) {
                e.preventDefault();
                e.stopPropagation();
                mobile.classList.toggle('open');
            }
            toggle.addEventListener('click',      toggleMenu);
            toggle.addEventListener('touchstart', toggleMenu, { passive: false });

            function closeMenu(e) {
                if (!toggle.contains(e.target) && !mobile.contains(e.target)) {
                    mobile.classList.remove('open');
                }
            }
            document.addEventListener('click',      closeMenu);
            document.addEventListener('touchstart', closeMenu, { passive: true });
        }

        // ── Section scroll helpers ─────────────────────────────
        function scrollToSection(sectionKey) {
            function tryScroll(attempts) {
                var el = document.getElementById('cot-section-' + sectionKey);
                if (el) {
                    var top = el.getBoundingClientRect().top + window.pageYOffset - scrollOffset();
                    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
                } else if (attempts > 0) {
                    setTimeout(function () { tryScroll(attempts - 1); }, 350);
                }
            }
            tryScroll(20);
        }

        function setActiveNav(sectionKey) {
            document.querySelectorAll('.nav-link[data-section]').forEach(function (l) {
                l.classList.toggle('active', l.dataset.section === sectionKey);
            });
        }

        // ── Nav section link clicks ────────────────────────────
        // On the home page: use cotGoToSection (handles detail→overview transition)
        // Otherwise: browser follows href (redirects home with hash)
        document.querySelectorAll('.nav-link[data-section]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var sec        = this.dataset.section;
                var isHomePage = (window.location.pathname === '/' ||
                                  window.location.pathname === '/index.php' ||
                                  document.querySelector('.cot-dashboard-container') !== null);

                if (isHomePage) {
                    e.preventDefault();
                    setActiveNav(sec);
                    if (typeof window.cotGoToSection === 'function') {
                        // dashboard JS handles back-to-overview + scroll
                        window.cotGoToSection(sec);
                    } else {
                        scrollToSection(sec);
                    }
                }
                // else: different page — browser follows href (redirects home with hash)
                if (mobile) mobile.classList.remove('open');
            });
        });

        // Overview → scroll to top, clear active
        var overview = document.querySelector('.nav-link-overview');
        if (overview) {
            overview.addEventListener('click', function () {
                document.querySelectorAll('.nav-link[data-section]').forEach(function (l) {
                    l.classList.remove('active');
                });
                if (mobile) mobile.classList.remove('open');
            });
        }

        // ── Handle hash on page load (after redirect from another page) ──
        if (window.location.hash && window.location.hash.indexOf('cot-section-') !== -1) {
            var hashKey = window.location.hash.replace('#cot-section-', '');
            // Wait for dashboard to load its data, then scroll
            function tryHashScroll(attempts) {
                var el = document.getElementById('cot-section-' + hashKey);
                if (el) {
                    var top = el.getBoundingClientRect().top + window.pageYOffset - scrollOffset();
                    window.scrollTo({ top: top, behavior: 'smooth' });
                    setActiveNav(hashKey);
                    // Clean up hash without reload
                    if (history.replaceState) {
                        history.replaceState(null, '', window.location.pathname);
                    }
                } else if (attempts > 0) {
                    setTimeout(function () { tryHashScroll(attempts - 1); }, 400);
                }
            }
            setTimeout(function () { tryHashScroll(25); }, 600);
        }

        // ── Highlight active section on scroll ────────────────
        window.addEventListener('scroll', function () {
            var sections = ['forex','indices','bonds','energy','metals','grains','crypto','softs','livestock'];
            var current = null;
            sections.forEach(function (key) {
                var el = document.getElementById('cot-section-' + key);
                if (el) {
                    var rect = el.getBoundingClientRect();
                    if (rect.top <= NAV_H + 20) current = key;
                }
            });
            document.querySelectorAll('.nav-link[data-section]').forEach(function (l) {
                l.classList.toggle('active', l.dataset.section === current);
            });
            if (overview) {
                overview.classList.toggle('active', current === null);
            }
        }, { passive: true });

        // ── Live update date in nav + markets badge ──────────
        if (typeof COT_CONFIG !== 'undefined' && COT_CONFIG.api_url) {
            fetch(COT_CONFIG.api_url.replace(/\/$/, '') + '/api/status')
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    var el = document.getElementById('cot-nav-last-update');
                    if (el && d.last_update) {
                        // COT data date = last Tuesday; published the following Friday
                        var parts = d.last_update.split('-');
                        var tueDt = new Date(Date.UTC(+parts[0], +parts[1] - 1, +parts[2]));
                        var friDt = new Date(tueDt);
                        friDt.setUTCDate(tueDt.getUTCDate() + 3); // Tue → Fri
                        var fmt = { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' };
                        el.innerHTML =
                            'Published <strong>' + friDt.toLocaleDateString('en-US', fmt) + '</strong>' +
                            ' &nbsp;&middot;&nbsp; data as of ' + tueDt.toLocaleDateString('en-US', fmt);
                    }
                    // Update markets count badge dynamically
                    if (d.markets_count) {
                        document.querySelectorAll('.cot-badge-markets').forEach(function (b) {
                            b.textContent = d.markets_count + ' markets';
                        });
                    }
                })
                .catch(function () {});
        }
    });
}());
