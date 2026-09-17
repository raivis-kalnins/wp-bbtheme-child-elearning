<?php
/**
 * WP BBTheme child suite 3.8.11.37 — non-destructive final alignment cleanup.
 *
 * Keeps the v136 visual layer, but replaces its destructive DOM repair with an
 * idempotent pass that preserves editor-owned process/card content, removes
 * exact duplicate finders/placeholders, and restores the canonical 1320px grid.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wpbb_child_v137_theme_key' ) ) {
    function wpbb_child_v137_theme_key() {
        if ( function_exists( 'wpbb_child_v136_theme_key' ) ) return wpbb_child_v136_theme_key();
        $slug = basename( get_stylesheet_directory() );
        $slug = preg_replace( '/^wp-bbtheme-child-/', '', $slug );
        return sanitize_key( $slug );
    }
}

if ( ! function_exists( 'wpbb_child_v137_profile' ) ) {
    function wpbb_child_v137_profile() {
        if ( function_exists( 'wpbb_child_v136_profile' ) ) return wpbb_child_v136_profile();
        return array();
    }
}

if ( ! function_exists( 'wpbb_child_v137_hero_urls' ) ) {
    function wpbb_child_v137_hero_urls() {
        if ( function_exists( 'wpbb_child_v136_hero_urls' ) ) return wpbb_child_v136_hero_urls();
        return array();
    }
}

if ( ! function_exists( 'wpbb_child_v137_enqueue' ) ) {
    function wpbb_child_v137_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();

        // v136's stylesheet remains the hero baseline, but its JavaScript
        // rebuilt process sections by deleting their editable DOM. Replace only
        // that runtime with the non-destructive v137 owner.
        wp_dequeue_script( 'wpbb-suite-v136' );
        wp_deregister_script( 'wpbb-suite-v136' );

        $css = '/assets/suite-v137.css';
        $js  = '/assets/suite-v137.js';
        wp_enqueue_style(
            'wpbb-suite-v137',
            $uri . $css,
            array( 'wpbb-suite-v136' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.37'
        );
        wp_enqueue_script(
            'wpbb-suite-v137',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.37',
            true
        );
        wp_add_inline_script(
            'wpbb-suite-v137',
            'window.wpbbSuiteV137=' . wp_json_encode( array(
                'version'       => '3.8.11.37',
                'themeKey'      => wpbb_child_v137_theme_key(),
                'heroUrls'      => wpbb_child_v137_hero_urls(),
                'fallbackCards' => wpbb_child_v137_profile(),
            ) ) . ';',
            'before'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v137_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v137_body_class' ) ) {
    function wpbb_child_v137_body_class( $classes ) {
        $classes[] = 'wpbb-v137';
        $classes[] = 'wpbb-v137-theme-' . wpbb_child_v137_theme_key();
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v137_body_class', PHP_INT_MAX );
