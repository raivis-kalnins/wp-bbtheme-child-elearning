<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.50.
 *
 * Deterministic homepage owner based on the working Woo Events 3.8.11.40
 * geometry, but mapped to the Learning front page from the supplied DB.
 *
 * Goals:
 * - one top-level header/section/footer rail;
 * - stable first hero slide with cache-safe learning media;
 * - no page-level padding on nested finder/card components;
 * - deterministic stats/proof/case/gallery/process/blog rows;
 * - cache-safe demo images using v150-specific filenames;
 * - no WooCommerce ownership changes.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v150_asset_url' ) ) {
    function wpbb_child_v150_asset_url( $relative ) {
        $relative = ltrim( (string) $relative, '/' );
        $path = trailingslashit( get_stylesheet_directory() ) . $relative;
        $url  = trailingslashit( get_stylesheet_directory_uri() ) . $relative;
        return is_readable( $path ) ? $url . '?v=' . rawurlencode( (string) filemtime( $path ) ) : $url;
    }
}

if ( ! function_exists( 'wpbb_child_v150_media_map' ) ) {
    function wpbb_child_v150_media_map() {
        return array(
            'hero' => array(
                wpbb_child_v150_asset_url( 'assets/img/v150/hero-learning-01.jpg' ),
                wpbb_child_v150_asset_url( 'assets/img/v150/hero-learning-02.jpg' ),
            ),
            'about' => wpbb_child_v150_asset_url( 'assets/img/v150/about-learning.jpg' ),
            'courses' => array_map(
                static function( $index ) {
                    return wpbb_child_v150_asset_url( 'assets/img/v150/course-' . str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) . '.jpg' );
                },
                range( 1, 6 )
            ),
            'gallery' => array_map(
                static function( $index ) {
                    return wpbb_child_v150_asset_url( 'assets/img/v150/gallery-' . str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) . '.jpg' );
                },
                range( 1, 4 )
            ),
            'blog' => array_map(
                static function( $index ) {
                    return wpbb_child_v150_asset_url( 'assets/img/v150/blog-' . str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) . '.jpg' );
                },
                range( 1, 3 )
            ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v150_enqueue' ) ) {
    function wpbb_child_v150_enqueue() {
        if ( ! is_front_page() ) return;

        // v148 was a temporary homepage rail/gallery owner. Keep its server-side
        // proof renderer, but retire only its frontend CSS/JS in favour of v150.
        wp_dequeue_style( 'wpbb-suite-v148' );
        wp_dequeue_script( 'wpbb-suite-v148' );
        wp_dequeue_style( 'wpbb-suite-v149' );
        wp_dequeue_script( 'wpbb-suite-v149' );

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v150.css';
        $js  = '/assets/suite-v150.js';

        $style_deps = array();
        if ( wp_style_is( 'wpbb-suite-v147', 'registered' ) || wp_style_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v147';
        } elseif ( wp_style_is( 'wpbb-suite-v144', 'registered' ) || wp_style_is( 'wpbb-suite-v144', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v144';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v150', $uri . $css, $style_deps, (string) filemtime( $dir . $css ) );
        }

        $script_deps = array();
        if ( wp_script_is( 'wpbb-suite-v147', 'registered' ) || wp_script_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $script_deps[] = 'wpbb-suite-v147';
        }
        if ( is_readable( $dir . $js ) ) {
            wp_enqueue_script( 'wpbb-suite-v150', $uri . $js, $script_deps, (string) filemtime( $dir . $js ), true );
            wp_localize_script( 'wpbb-suite-v150', 'wpbbSuiteV150', wpbb_child_v150_media_map() );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v150_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v150_body_class' ) ) {
    function wpbb_child_v150_body_class( $classes ) {
        $classes[] = 'wpbb-v150';
        if ( is_front_page() ) $classes[] = 'wpbb-v150-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v150_body_class', PHP_INT_MAX );

/**
 * Replace the two hero slide images and four gallery slide images before the
 * block renders. The new filenames are unique to this release, so stale CDN
 * copies of the old demo filenames cannot win.
 */
if ( ! function_exists( 'wpbb_child_v150_render_block_data' ) ) {
    function wpbb_child_v150_render_block_data( $parsed_block ) {
        if ( ! is_front_page() || is_admin() ) return $parsed_block;
        if ( empty( $parsed_block['blockName'] ) || 'wpbb/swiper' !== $parsed_block['blockName'] ) return $parsed_block;
        if ( empty( $parsed_block['attrs'] ) || ! is_array( $parsed_block['attrs'] ) ) return $parsed_block;

        $attrs = $parsed_block['attrs'];
        $style = isset( $attrs['demoStyle'] ) ? sanitize_key( (string) $attrs['demoStyle'] ) : '';
        $map   = wpbb_child_v150_media_map();

        if ( 'hero' === $style && ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
            foreach ( $attrs['slides'] as $index => &$slide ) {
                if ( ! is_array( $slide ) ) continue;
                $slide['image'] = $map['hero'][ $index % count( $map['hero'] ) ];
            }
            unset( $slide );
            // Keep the hero stable at the first slide. Navigation remains usable.
            $attrs['autoplay'] = false;
            $attrs['loop']     = false;
            $attrs['rewind']   = true;
            $attrs['showPagination'] = true;
        }

        if ( 'gallery' === $style && ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
            foreach ( $attrs['slides'] as $index => &$slide ) {
                if ( ! is_array( $slide ) ) continue;
                if ( isset( $map['gallery'][ $index ] ) ) $slide['image'] = $map['gallery'][ $index ];
            }
            unset( $slide );
            $attrs['autoplay'] = false;
            $attrs['loop']     = false;
            $attrs['rewind']   = true;
        }

        $parsed_block['attrs'] = $attrs;
        return $parsed_block;
    }
}
add_filter( 'render_block_data', 'wpbb_child_v150_render_block_data', PHP_INT_MAX, 1 );

/**
 * The DB contains authored proof-card copy but the legacy icon-card renderer can
 * still emit demo placeholders. Repair those strings in rendered front-page
 * content as a server-side fallback, matching the supplied DB.
 */
if ( ! function_exists( 'wpbb_child_v150_repair_proof_content' ) ) {
    function wpbb_child_v150_repair_proof_content( $content ) {
        if ( ! is_front_page() || is_admin() || ! is_string( $content ) ) return $content;
        if ( false === strpos( $content, 'Card title' ) && false === strpos( $content, 'Add a short description.' ) ) return $content;

        $titles = array( 'Course-first architecture', 'Materials alongside lessons', 'Practical quizzes' );
        $texts  = array(
            'Courses, lessons and quizzes are separate content types so the system stays manageable.',
            'Video and PDF resource links are shown in the curriculum rather than hidden elsewhere.',
            'Multiple-choice questions are scored server-side with feedback after submission.',
        );

        foreach ( $titles as $replacement ) {
            $pos = strpos( $content, 'Card title' );
            if ( false === $pos ) break;
            $content = substr_replace( $content, esc_html( $replacement ), $pos, strlen( 'Card title' ) );
        }
        foreach ( $texts as $replacement ) {
            $pos = strpos( $content, 'Add a short description.' );
            if ( false === $pos ) break;
            $content = substr_replace( $content, esc_html( $replacement ), $pos, strlen( 'Add a short description.' ) );
        }
        return $content;
    }
}
add_filter( 'the_content', 'wpbb_child_v150_repair_proof_content', PHP_INT_MAX );
