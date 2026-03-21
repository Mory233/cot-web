<?php
/**
 * Plugin Name: COT Dashboard
 * Description: Commitments of Traders dashboard with automatic data updates from CFTC.gov
 * Version:     1.0
 * Author:      COT Terminal
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'COT_OPTION_API_URL', 'cot_api_url' );
define( 'COT_OPTION_SECRET',  'cot_secret_key' );
define( 'COT_PLUGIN_URL',     plugin_dir_url( __FILE__ ) );
define( 'COT_PLUGIN_VER',     '1.0' );

// ── Settings registration ──────────────────────────────────────────────────

add_action( 'admin_init', function () {
    register_setting( 'cot_settings_group', COT_OPTION_API_URL, [
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ] );
    register_setting( 'cot_settings_group', COT_OPTION_SECRET, [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'COT_SECRET_2024',
    ] );
} );

// ── Admin menu ─────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
    add_options_page(
        'COT Dashboard Settings',
        'COT Dashboard',
        'manage_options',
        'cot-dashboard',
        'cot_render_settings_page'
    );
} );

function cot_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $api_url = get_option( COT_OPTION_API_URL, '' );
    $secret  = get_option( COT_OPTION_SECRET,  'COT_SECRET_2024' );
    ?>
    <div class="wrap">
        <h1>COT Dashboard — Settings</h1>

        <?php settings_errors( 'cot_settings_group' ); ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'cot_settings_group' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="cot_api_url">Railway API URL</label>
                    </th>
                    <td>
                        <input
                            type="url"
                            id="cot_api_url"
                            name="<?php echo esc_attr( COT_OPTION_API_URL ); ?>"
                            value="<?php echo esc_attr( $api_url ); ?>"
                            class="regular-text"
                            placeholder="https://your-app.railway.app"
                        >
                        <button type="button" id="cot-test-btn" class="button button-secondary"
                                style="margin-left:8px">
                            Test Connection
                        </button>
                        <span id="cot-test-result" style="margin-left:10px;font-weight:600"></span>
                        <p class="description">
                            Backend URL deployed on Railway.app (no trailing slash).
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="cot_secret">Secret Token</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="cot_secret"
                            name="<?php echo esc_attr( COT_OPTION_SECRET ); ?>"
                            value="<?php echo esc_attr( $secret ); ?>"
                            class="regular-text"
                        >
                        <button type="button" id="cot-refresh-btn" class="button button-secondary"
                                style="margin-left:8px">
                            Manual Data Update
                        </button>
                        <span id="cot-refresh-result" style="margin-left:10px;font-weight:600"></span>
                        <p class="description">
                            Must match the <code>SECRET_KEY</code> environment variable on Railway.
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button( 'Save Settings' ); ?>
        </form>

        <hr>
        <h2>Usage</h2>
        <p>Insert the shortcode <code>[cot_dashboard]</code> on any page or post.</p>
    </div>

    <script>
    (function () {
        function apiUrl() {
            return document.getElementById('cot_api_url').value.replace(/\/$/, '');
        }
        function secret() {
            return document.getElementById('cot_secret').value;
        }

        document.getElementById('cot-test-btn').addEventListener('click', function () {
            var url    = apiUrl();
            var result = document.getElementById('cot-test-result');
            if ( ! url ) { result.textContent = '⚠ Enter URL'; result.style.color = 'orange'; return; }
            result.textContent = 'Testing…'; result.style.color = '#777';
            fetch(url + '/health', { signal: AbortSignal.timeout(8000) })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    if (d.status === 'ok') {
                        result.textContent = '✓ Connection OK'; result.style.color = 'green';
                    } else {
                        result.textContent = '✗ Unexpected response'; result.style.color = 'red';
                    }
                })
                .catch(function () { result.textContent = '✗ Cannot connect'; result.style.color = 'red'; });
        });

        document.getElementById('cot-refresh-btn').addEventListener('click', function () {
            var url    = apiUrl();
            var result = document.getElementById('cot-refresh-result');
            if ( ! url ) { result.textContent = '⚠ Enter URL'; result.style.color = 'orange'; return; }
            result.textContent = 'Starting…'; result.style.color = '#777';
            fetch(url + '/api/refresh?secret=' + encodeURIComponent(secret()), {
                method: 'POST',
                signal: AbortSignal.timeout(15000)
            })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    result.textContent = '✓ ' + (d.message || 'Started'); result.style.color = 'green';
                })
                .catch(function () { result.textContent = '✗ Request error'; result.style.color = 'red'; });
        });
    }());
    </script>
    <?php
}

// ── Shortcode — inlines CSS + JS to bypass server 403 on static files ──────

add_shortcode( 'cot_dashboard', function ( $atts ) {
    static $already_output = false;

    $uid     = esc_attr( uniqid( 'cot-root-', true ) );
    $api_url = esc_url( get_option( COT_OPTION_API_URL, '' ) );
    $out     = '';

    if ( ! $already_output ) {
        $already_output = true;

        $css_path = plugin_dir_path( __FILE__ ) . 'cot-dashboard.css';
        $js_path  = plugin_dir_path( __FILE__ ) . 'cot-dashboard.js';

        if ( file_exists( $css_path ) ) {
            $out .= '<style>' . file_get_contents( $css_path ) . '</style>';
        }

        $out .= '<script>var COT_CONFIG = ' . wp_json_encode( [ 'api_url' => $api_url ] ) . ';</script>';

        if ( file_exists( $js_path ) ) {
            $out .= '<script>' . file_get_contents( $js_path ) . '</script>';
        }
    }

    $out .= '<div id="' . $uid . '" class="cot-dashboard-container"></div>';
    return $out;
} );
