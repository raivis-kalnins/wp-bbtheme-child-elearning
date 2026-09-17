<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.47.
 *
 * Regression-safe homepage repair built from the v145 frontend baseline.
 * v146 is intentionally not part of this package. This layer:
 * - keeps the course finder on its native full-width BBuilder column;
 * - prevents catalogue cards from being turned into generic item galleries;
 * - restores the three proof cards to one responsive row and their authored copy;
 * - normalises section heading rows without measuring/padding nested sections;
 * - leaves WooCommerce v141 routing/layout ownership unchanged.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v147_enqueue' ) ) {
    function wpbb_child_v147_enqueue() {
        if ( ! is_front_page() ) return;

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v147.css';
        $js  = '/assets/suite-v147.js';

        $style_deps = array();
        if ( wp_style_is( 'wpbb-suite-v145', 'registered' ) || wp_style_is( 'wpbb-suite-v145', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v145';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v147', $uri . $css, $style_deps, (string) filemtime( $dir . $css ) );
        }

        if ( is_readable( $dir . $js ) ) {
            $script_deps = array();
            if ( wp_script_is( 'wpbb-suite-v145', 'registered' ) || wp_script_is( 'wpbb-suite-v145', 'enqueued' ) ) {
                $script_deps[] = 'wpbb-suite-v145';
            }
            wp_enqueue_script( 'wpbb-suite-v147', $uri . $js, $script_deps, (string) filemtime( $dir . $js ), true );
            wp_localize_script( 'wpbb-suite-v147', 'wpbbSuiteV147', array(
                'courseImages' => array_map(
                    static function( $index ) use ( $uri ) {
                        return $uri . '/assets/img/v150/course-' . str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) . '.jpg';
                    },
                    range( 1, 6 )
                ),
                'proofCards' => array(
                    array(
                        'title' => 'Course-first architecture',
                        'text'  => 'Courses, lessons and quizzes are separate content types so the system stays manageable.',
                    ),
                    array(
                        'title' => 'Materials alongside lessons',
                        'text'  => 'Video and PDF resource links are shown in the curriculum rather than hidden elsewhere.',
                    ),
                    array(
                        'title' => 'Practical quizzes',
                        'text'  => 'Multiple-choice questions are scored server-side with feedback after submission.',
                    ),
                ),
            ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v147_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v147_body_class' ) ) {
    function wpbb_child_v147_body_class( $classes ) {
        $classes[] = 'wpbb-v147';
        if ( is_front_page() ) $classes[] = 'wpbb-v147-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v147_body_class', PHP_INT_MAX );
