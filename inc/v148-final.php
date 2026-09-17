<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.48.
 *
 * Final homepage rail/marked-area repair after live 3.8.11.47 verification.
 * - restores the original 1440px sector/header rail to homepage sections;
 * - keeps nested course-finder content at 100% instead of re-aligning it;
 * - renders the authored proof-card copy server-side and owns its 3/2/1 grid;
 * - supplies four verified workshop/learning gallery images;
 * - preserves the v141 WooCommerce route-scoped ownership unchanged.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v148_enqueue' ) ) {
    function wpbb_child_v148_enqueue() {
        if ( ! is_front_page() ) return;

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v148.css';
        $js  = '/assets/suite-v148.js';

        $style_deps = array();
        if ( wp_style_is( 'wpbb-suite-v147', 'registered' ) || wp_style_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v147';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v148', $uri . $css, $style_deps, (string) filemtime( $dir . $css ) );
        }

        if ( is_readable( $dir . $js ) ) {
            $script_deps = array();
            if ( wp_script_is( 'wpbb-suite-v147', 'registered' ) || wp_script_is( 'wpbb-suite-v147', 'enqueued' ) ) {
                $script_deps[] = 'wpbb-suite-v147';
            } elseif ( wp_script_is( 'wpbb-suite-v137', 'registered' ) || wp_script_is( 'wpbb-suite-v137', 'enqueued' ) ) {
                $script_deps[] = 'wpbb-suite-v137';
            }
            wp_enqueue_script( 'wpbb-suite-v148', $uri . $js, $script_deps, (string) filemtime( $dir . $js ), true );
            wp_localize_script( 'wpbb-suite-v148', 'wpbbSuiteV148', array(
                'gallery' => array(
                    $uri . '/assets/img/v150/gallery-01.jpg',
                    $uri . '/assets/img/v150/gallery-02.jpg',
                    $uri . '/assets/img/v150/gallery-03.jpg',
                    $uri . '/assets/img/v150/gallery-04.jpg',
                ),
                'courseImages' => array_map(
                    static function( $index ) use ( $uri ) {
                        return $uri . '/assets/img/v150/course-' . str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) . '.jpg';
                    },
                    range( 1, 6 )
                ),
            ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v148_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v148_body_class' ) ) {
    function wpbb_child_v148_body_class( $classes ) {
        $classes[] = 'wpbb-v148';
        if ( is_front_page() ) $classes[] = 'wpbb-v148-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v148_body_class', PHP_INT_MAX );

/**
 * The supplied front-page DB stores the correct proof titles/descriptions in
 * the wpbb/icon-card attrs, while the current block renderer outputs its demo
 * placeholders. Render only these three explicitly marked cards from attrs so
 * the correct copy also exists in server HTML (not only after JavaScript).
 */
if ( ! function_exists( 'wpbb_child_v148_render_proof_card' ) ) {
    function wpbb_child_v148_render_proof_card( $block_content, $block ) {
        if ( ! is_front_page() || is_admin() ) return $block_content;
        if ( empty( $block['blockName'] ) || 'wpbb/icon-card' !== $block['blockName'] ) return $block_content;

        $attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
        $class = isset( $attrs['className'] ) ? (string) $attrs['className'] : '';
        if ( false === strpos( ' ' . $class . ' ', ' wpbb-sector-proof-card ' ) ) return $block_content;

        $title = isset( $attrs['title'] ) ? trim( wp_strip_all_tags( (string) $attrs['title'] ) ) : '';
        $text  = isset( $attrs['text'] ) ? trim( wp_strip_all_tags( (string) $attrs['text'] ) ) : '';
        if ( '' === $title && '' === $text ) return $block_content;

        $classes = implode( ' ', array_unique( array_filter( preg_split( '/\s+/', trim( 'wpbb-icon-card wpbb-sector-proof-card wpbb-v148-proof-card ' . $class ) ) ) ) );
        return '<article class="' . esc_attr( $classes ) . '">'
            . '<span class="wpbb-v148-proof-icon" aria-hidden="true">+</span>'
            . ( '' !== $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' )
            . ( '' !== $text ? '<p>' . esc_html( $text ) . '</p>' : '' )
            . '</article>';
    }
}
add_filter( 'render_block', 'wpbb_child_v148_render_proof_card', PHP_INT_MAX, 2 );
