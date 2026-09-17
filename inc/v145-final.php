<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.45.
 *
 * Focused live homepage repair layered after v144. This file deliberately
 * avoids post/content mutation and global ownership changes. It only loads a
 * small front-page CSS/JS layer that repairs wrappers emitted by BBuilder.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v145_enqueue' ) ) {
    function wpbb_child_v145_enqueue() {
        if ( ! is_front_page() ) return;

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v145.css';
        $js  = '/assets/suite-v145.js';

        $style_deps = array();
        if ( wp_style_is( 'wpbb-suite-v144', 'registered' ) || wp_style_is( 'wpbb-suite-v144', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v144';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v145', $uri . $css, $style_deps, (string) filemtime( $dir . $css ) );
        }

        if ( is_readable( $dir . $js ) ) {
            $script_deps = array();
            if ( wp_script_is( 'wpbb-suite-v137', 'registered' ) || wp_script_is( 'wpbb-suite-v137', 'enqueued' ) ) {
                $script_deps[] = 'wpbb-suite-v137';
            }
            wp_enqueue_script( 'wpbb-suite-v145', $uri . $js, $script_deps, (string) filemtime( $dir . $js ), true );

            wp_localize_script( 'wpbb-suite-v145', 'wpbbSuiteV145', array(
                'hero' => array(
                    $uri . '/assets/img/v150/hero-learning-01.jpg',
                    $uri . '/assets/img/v150/hero-learning-02.jpg',
                    $uri . '/assets/img/v150/hero-learning-01.jpg',
                ),
                'gallery' => array(
                    $uri . '/assets/img/v150/gallery-01.jpg',
                    $uri . '/assets/img/v150/gallery-02.jpg',
                    $uri . '/assets/img/v150/gallery-03.jpg',
                    $uri . '/assets/img/v150/gallery-04.jpg',
                ),
            ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v145_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v145_body_class' ) ) {
    function wpbb_child_v145_body_class( $classes ) {
        $classes[] = 'wpbb-v145';
        if ( is_front_page() ) $classes[] = 'wpbb-v145-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v145_body_class', PHP_INT_MAX );
