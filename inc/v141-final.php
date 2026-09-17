<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.41.
 *
 * Emergency stability hotfix after the v140 regression:
 * - do not load the v140 global ownership layer;
 * - preserve the proven v137 homepage/header/footer/course stack unchanged;
 * - keep WooCommerce improvements strictly route-scoped and CSS-only;
 * - do not rewrite editor content, rebuild DOM grids, or replace page templates.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v141_is_woo_route' ) ) {
    function wpbb_child_v141_is_woo_route() {
        return ( function_exists( 'is_woocommerce' ) && is_woocommerce() )
            || ( function_exists( 'is_cart' ) && is_cart() )
            || ( function_exists( 'is_checkout' ) && is_checkout() )
            || ( function_exists( 'is_account_page' ) && is_account_page() );
    }
}

if ( ! function_exists( 'wpbb_child_v141_woocommerce_support' ) ) {
    function wpbb_child_v141_woocommerce_support() {
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
    }
}
add_action( 'after_setup_theme', 'wpbb_child_v141_woocommerce_support', 30 );

if ( ! function_exists( 'wpbb_child_v141_enqueue' ) ) {
    function wpbb_child_v141_enqueue() {
        if ( ! wpbb_child_v141_is_woo_route() ) return;

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v141.css';
        if ( ! is_readable( $dir . $css ) ) return;

        $deps = array();
        if ( wp_style_is( 'wpbb-suite-v137', 'registered' ) || wp_style_is( 'wpbb-suite-v137', 'enqueued' ) ) {
            $deps[] = 'wpbb-suite-v137';
        }
        wp_enqueue_style(
            'wpbb-suite-v141',
            $uri . $css,
            $deps,
            (string) filemtime( $dir . $css )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v141_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v141_body_class' ) ) {
    function wpbb_child_v141_body_class( $classes ) {
        $classes[] = 'wpbb-v141';
        if ( wpbb_child_v141_is_woo_route() ) $classes[] = 'wpbb-v141-woo';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v141_body_class', PHP_INT_MAX );
