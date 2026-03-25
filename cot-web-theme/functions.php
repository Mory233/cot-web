<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Theme setup ────────────────────────────────────────────────────────────

add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
    register_nav_menus( [ 'primary' => 'Primary Navigation' ] );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
} );

// ── Enqueue ────────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', function () {
    // Theme JS — nav toggle + filter integration
    wp_enqueue_script(
        'cot-theme-js',
        get_template_directory_uri() . '/theme.js',
        [],
        '1.0',
        true  // footer
    );

    // Dequeue heavy WP block styles
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
}, 100 );

// Remove generator tag
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// ── Auto-create pages (on activation AND on every load if missing) ─────────

add_action( 'after_switch_theme', 'cot_theme_setup_pages' );
add_action( 'wp_loaded',          'cot_theme_setup_pages' );

function cot_theme_setup_pages() {
    $home = get_page_by_path( 'cot-overview' );
    if ( ! $home ) {
        $home_id = wp_insert_post( [
            'post_title'   => 'COT Overview',
            'post_name'    => 'cot-overview',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '[cot_dashboard]',
        ] );
    } else {
        $home_id = $home->ID;
    }

    $info = get_page_by_path( 'co-je-cot' );
    if ( ! $info ) {
        wp_insert_post( [
            'post_title'  => 'What is COT?',
            'post_name'   => 'co-je-cot',
            'post_status' => 'publish',
            'post_type'   => 'page',
        ] );
    }

    $banks = get_page_by_path( 'trade-like-banks' );
    if ( ! $banks ) {
        wp_insert_post( [
            'post_title'  => 'Trade Like Banks',
            'post_name'   => 'trade-like-banks',
            'post_status' => 'publish',
            'post_type'   => 'page',
        ] );
    }

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $home_id );
}
