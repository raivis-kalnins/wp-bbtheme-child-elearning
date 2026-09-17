<?php
/**
 * E-Learning 3.8.11.51 - Events geometry recovery.
 *
 * Scope:
 * - retire v150 frontend geometry/runtime on the homepage;
 * - use the Events 3.8.11.40 1180px/24px content rail;
 * - render one deterministic hero slide with Learning media;
 * - keep the stable v147 finder internals;
 * - fix the proof row directly from its real Bootstrap/BBuilder markup;
 * - remap demo media without changing database content or Woo routes.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v151_asset_url' ) ) {
    function wpbb_child_v151_asset_url( $relative ) {
        $relative = ltrim( (string) $relative, '/' );
        $path = trailingslashit( get_stylesheet_directory() ) . $relative;
        $url  = trailingslashit( get_stylesheet_directory_uri() ) . $relative;
        return is_readable( $path ) ? $url . '?v=' . rawurlencode( (string) filemtime( $path ) ) : $url;
    }
}

if ( ! function_exists( 'wpbb_child_v151_media_map' ) ) {
    function wpbb_child_v151_media_map() {
        return array(
            'hero'    => wpbb_child_v151_asset_url( 'assets/img/v150/hero-learning-01.jpg' ),
            'about'   => wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-1.jpg' ),
            'courses' => array(
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-1.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-2.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-3.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-4.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/site-previews/project-2.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/site-previews/project-3.jpg' ),
            ),
            'gallery' => array(
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-1.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-2.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-3.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/gallery/learning-workshop-4.jpg' ),
            ),
            'blog' => array(
                wpbb_child_v151_asset_url( 'assets/img/blog/blog-1.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/blog/blog-2.jpg' ),
                wpbb_child_v151_asset_url( 'assets/img/blog/blog-3.jpg' ),
            ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v151_enqueue' ) ) {
    function wpbb_child_v151_enqueue() {
        if ( ! is_front_page() ) return;

        // v150 caused the current hero/right-shift regression. Its server-side
        // content fallback remains harmless, but its CSS/JS geometry is retired.
        wp_dequeue_style( 'wpbb-suite-v150' );
        wp_dequeue_script( 'wpbb-suite-v150' );
        wp_dequeue_style( 'wpbb-suite-v149' );
        wp_dequeue_script( 'wpbb-suite-v149' );
        wp_dequeue_style( 'wpbb-suite-v148' );
        wp_dequeue_script( 'wpbb-suite-v148' );

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v151.css';
        $js  = '/assets/suite-v151.js';

        $deps = array();
        if ( wp_style_is( 'wpbb-suite-v147', 'registered' ) || wp_style_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $deps[] = 'wpbb-suite-v147';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v151', $uri . $css, $deps, (string) filemtime( $dir . $css ) );
        }
        if ( is_readable( $dir . $js ) ) {
            wp_enqueue_script( 'wpbb-suite-v151', $uri . $js, array(), (string) filemtime( $dir . $js ), true );
            wp_localize_script( 'wpbb-suite-v151', 'wpbbSuiteV151', wpbb_child_v151_media_map() );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v151_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v151_body_class' ) ) {
    function wpbb_child_v151_body_class( $classes ) {
        $classes[] = 'wpbb-v151';
        if ( is_front_page() ) $classes[] = 'wpbb-v151-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v151_body_class', PHP_INT_MAX );

/**
 * Make the homepage hero deterministic at render time. The live source contains
 * two complete hero slide content sets; keeping one authored slide removes the
 * state where Swiper shows one slide's copy with another slide's geometry.
 */
if ( ! function_exists( 'wpbb_child_v151_render_block_data' ) ) {
    function wpbb_child_v151_render_block_data( $parsed_block ) {
        if ( ! is_front_page() || is_admin() ) return $parsed_block;
        if ( empty( $parsed_block['blockName'] ) || 'wpbb/swiper' !== $parsed_block['blockName'] ) return $parsed_block;
        if ( empty( $parsed_block['attrs'] ) || ! is_array( $parsed_block['attrs'] ) ) return $parsed_block;

        $attrs = $parsed_block['attrs'];
        $style = isset( $attrs['demoStyle'] ) ? sanitize_key( (string) $attrs['demoStyle'] ) : '';
        $map   = wpbb_child_v151_media_map();

        if ( 'hero' === $style && ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
            $first = reset( $attrs['slides'] );
            if ( is_array( $first ) ) {
                $first['image'] = $map['hero'];
                $attrs['slides'] = array( $first );
            }
            $attrs['autoplay'] = false;
            $attrs['loop'] = false;
            $attrs['rewind'] = false;
            $attrs['showPagination'] = false;
            $attrs['showNavigation'] = false;
        }

        if ( 'gallery' === $style && ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
            foreach ( $attrs['slides'] as $index => &$slide ) {
                if ( is_array( $slide ) && isset( $map['gallery'][ $index ] ) ) {
                    $slide['image'] = $map['gallery'][ $index ];
                }
            }
            unset( $slide );
            $attrs['autoplay'] = false;
            $attrs['loop'] = false;
            $attrs['rewind'] = true;
        }

        $parsed_block['attrs'] = $attrs;
        return $parsed_block;
    }
}
add_filter( 'render_block_data', 'wpbb_child_v151_render_block_data', PHP_INT_MAX, 1 );
