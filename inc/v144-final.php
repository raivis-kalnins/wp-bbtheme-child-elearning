<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.44.
 *
 * DB-backed homepage recovery based on the stable v141 frontend and the
 * working Woo Events layout conventions. This layer is intentionally narrow:
 * - no DOM rebuilding;
 * - no post-content mutation;
 * - no global dequeue/re-enqueue takeover;
 * - no gallery source replacement;
 * - WooCommerce remains owned by the route-scoped v141 layer.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v144_enqueue' ) ) {
    function wpbb_child_v144_enqueue() {
        if ( ! is_front_page() ) return;

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v144.css';
        if ( ! is_readable( $dir . $css ) ) return;

        $deps = array();
        if ( wp_style_is( 'wpbb-suite-v137', 'registered' ) || wp_style_is( 'wpbb-suite-v137', 'enqueued' ) ) {
            $deps[] = 'wpbb-suite-v137';
        }
        wp_enqueue_style(
            'wpbb-suite-v144',
            $uri . $css,
            $deps,
            (string) filemtime( $dir . $css )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v144_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v144_body_class' ) ) {
    function wpbb_child_v144_body_class( $classes ) {
        $classes[] = 'wpbb-v144';
        if ( is_front_page() ) $classes[] = 'wpbb-v144-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v144_body_class', PHP_INT_MAX );
