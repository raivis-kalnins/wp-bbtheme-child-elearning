<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.52.
 *
 * Focused live repair after v151:
 * - restore the authored two-slide hero and a deterministic pagination owner;
 * - add sufficient hero top/bottom breathing room for the Learning search content;
 * - keep the Events 1180px / 24px rail without freezing Swiper transforms;
 * - force the exact #wpbb-row-31 proof row to three columns on desktop;
 * - retain the stable v147 finder and existing route-scoped Woo fixes.
 */
defined( 'ABSPATH' ) || exit;

// v151 intentionally reduced the hero to one slide. The current live request
// needs the authored carousel/pagination back, so retire only that data filter.
if ( function_exists( 'wpbb_child_v151_render_block_data' ) ) {
    remove_filter( 'render_block_data', 'wpbb_child_v151_render_block_data', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v152_media_map' ) ) {
    function wpbb_child_v152_media_map() {
        if ( function_exists( 'wpbb_child_v150_media_map' ) ) {
            return wpbb_child_v150_media_map();
        }
        return array( 'hero' => array(), 'courses' => array(), 'gallery' => array(), 'blog' => array() );
    }
}

if ( ! function_exists( 'wpbb_child_v152_enqueue' ) ) {
    function wpbb_child_v152_enqueue() {
        if ( ! is_front_page() ) return;

        // v152 supersedes the experimental homepage owners only. Keep v147 and
        // the route-scoped v141 Woo layer as the stable foundation.
        foreach ( array( 148, 149, 150, 151 ) as $n ) {
            wp_dequeue_style( 'wpbb-suite-v' . $n );
            wp_dequeue_script( 'wpbb-suite-v' . $n );
        }

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v152.css';
        $js  = '/assets/suite-v152.js';

        $style_deps = array();
        if ( wp_style_is( 'wpbb-suite-v147', 'registered' ) || wp_style_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v147';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v152', $uri . $css, $style_deps, (string) filemtime( $dir . $css ) );
        }

        $script_deps = array();
        if ( wp_script_is( 'wpbb-suite-v147', 'registered' ) || wp_script_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $script_deps[] = 'wpbb-suite-v147';
        }
        if ( is_readable( $dir . $js ) ) {
            wp_enqueue_script( 'wpbb-suite-v152', $uri . $js, $script_deps, (string) filemtime( $dir . $js ), true );
            wp_localize_script( 'wpbb-suite-v152', 'wpbbSuiteV152', wpbb_child_v152_media_map() );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v152_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v152_body_class' ) ) {
    function wpbb_child_v152_body_class( $classes ) {
        $classes[] = 'wpbb-v152';
        if ( is_front_page() ) $classes[] = 'wpbb-v152-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v152_body_class', PHP_INT_MAX );

/**
 * Keep both authored hero slides and let the normal Swiper renderer own the
 * movement. v152 owns only the pager presentation/runtime synchronization.
 */
if ( ! function_exists( 'wpbb_child_v152_render_block_data' ) ) {
    function wpbb_child_v152_render_block_data( $parsed_block ) {
        if ( ! is_front_page() || is_admin() ) return $parsed_block;
        if ( empty( $parsed_block['blockName'] ) || 'wpbb/swiper' !== $parsed_block['blockName'] ) return $parsed_block;
        if ( empty( $parsed_block['attrs'] ) || ! is_array( $parsed_block['attrs'] ) ) return $parsed_block;

        $attrs = $parsed_block['attrs'];
        $style = isset( $attrs['demoStyle'] ) ? sanitize_key( (string) $attrs['demoStyle'] ) : '';
        if ( 'hero' !== $style || empty( $attrs['slides'] ) || ! is_array( $attrs['slides'] ) ) return $parsed_block;

        $map = wpbb_child_v152_media_map();
        $hero_images = isset( $map['hero'] ) && is_array( $map['hero'] ) ? array_values( array_filter( $map['hero'] ) ) : array();
        foreach ( $attrs['slides'] as $index => &$slide ) {
            if ( is_array( $slide ) && ! empty( $hero_images ) ) {
                $slide['image'] = $hero_images[ $index % count( $hero_images ) ];
            }
        }
        unset( $slide );

        $count = count( $attrs['slides'] );
        $attrs['slidesPerView']  = 1;
        $attrs['slidesTablet']   = 1;
        $attrs['slidesMobile']   = 1;
        $attrs['spaceBetween']   = 0;
        $attrs['speed']          = 700;
        $attrs['autoplay']       = $count > 1;
        $attrs['autoplayDelay']  = 8500;
        $attrs['pauseOnHover']   = true;
        $attrs['loop']           = $count > 1;
        $attrs['rewind']         = true;
        $attrs['showPagination'] = true;
        $attrs['showNavigation'] = false;

        if ( isset( $attrs['slidesJson'] ) ) {
            $attrs['slidesJson'] = wp_json_encode( $attrs['slides'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        }

        $parsed_block['attrs'] = $attrs;
        return $parsed_block;
    }
}
add_filter( 'render_block_data', 'wpbb_child_v152_render_block_data', PHP_INT_MAX, 1 );
