<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.49.
 *
 * Events-reference final visual parity layer:
 * - use the actual header inner rail as the homepage horizontal owner;
 * - restore the pre-v148 hero vertical geometry while aligning it to that rail;
 * - keep nested finder/card components native-width;
 * - refresh demo course/blog attachments after replacing the bundled media set;
 * - expose cache-busted role-specific image URLs to the small front-page runtime.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v149_asset_url' ) ) {
    function wpbb_child_v149_asset_url( $relative ) {
        $relative = ltrim( (string) $relative, '/' );
        $path = trailingslashit( get_stylesheet_directory() ) . $relative;
        $url  = trailingslashit( get_stylesheet_directory_uri() ) . $relative;
        return is_readable( $path ) ? $url . '?v=' . rawurlencode( (string) filemtime( $path ) ) : $url;
    }
}

if ( ! function_exists( 'wpbb_child_v149_enqueue' ) ) {
    function wpbb_child_v149_enqueue() {
        if ( ! is_front_page() ) return;

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v149.css';
        $js  = '/assets/suite-v149.js';

        $style_deps = array();
        if ( wp_style_is( 'wpbb-suite-v148', 'registered' ) || wp_style_is( 'wpbb-suite-v148', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v148';
        } elseif ( wp_style_is( 'wpbb-suite-v147', 'registered' ) || wp_style_is( 'wpbb-suite-v147', 'enqueued' ) ) {
            $style_deps[] = 'wpbb-suite-v147';
        }
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v149', $uri . $css, $style_deps, (string) filemtime( $dir . $css ) );
        }

        if ( is_readable( $dir . $js ) ) {
            $script_deps = array();
            if ( wp_script_is( 'wpbb-suite-v148', 'registered' ) || wp_script_is( 'wpbb-suite-v148', 'enqueued' ) ) {
                $script_deps[] = 'wpbb-suite-v148';
            } elseif ( wp_script_is( 'wpbb-suite-v147', 'registered' ) || wp_script_is( 'wpbb-suite-v147', 'enqueued' ) ) {
                $script_deps[] = 'wpbb-suite-v147';
            }
            wp_enqueue_script( 'wpbb-suite-v149', $uri . $js, $script_deps, (string) filemtime( $dir . $js ), true );
            wp_localize_script( 'wpbb-suite-v149', 'wpbbSuiteV149', array(
                'hero' => array(
                    wpbb_child_v149_asset_url( 'assets/img/demo/hero-v114.jpg' ),
                    wpbb_child_v149_asset_url( 'assets/img/demo/hero-v113.jpg' ),
                    wpbb_child_v149_asset_url( 'assets/img/demo/hero-v112.jpg' ),
                ),
                'about' => wpbb_child_v149_asset_url( 'assets/img/demo/about-photo.jpg' ),
                'courses' => array_map(
                    static function( $index ) {
                        return wpbb_child_v149_asset_url( 'assets/img/demo/item-' . $index . '.jpg' );
                    },
                    range( 1, 6 )
                ),
                'gallery' => array_map(
                    static function( $index ) {
                        return wpbb_child_v149_asset_url( 'assets/img/gallery/learning-workshop-' . $index . '.jpg' );
                    },
                    range( 1, 4 )
                ),
                'blog' => array_map(
                    static function( $index ) {
                        return wpbb_child_v149_asset_url( 'assets/img/blog/blog-' . $index . '.jpg' );
                    },
                    range( 1, 6 )
                ),
            ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v149_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v149_body_class' ) ) {
    function wpbb_child_v149_body_class( $classes ) {
        $classes[] = 'wpbb-v149';
        if ( is_front_page() ) $classes[] = 'wpbb-v149-home';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v149_body_class', PHP_INT_MAX );

/**
 * Refresh an already-imported attachment from a bundled replacement asset.
 */
if ( ! function_exists( 'wpbb_child_v149_refresh_attachment' ) ) {
    function wpbb_child_v149_refresh_attachment( $attachment_id, $relative ) {
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) return false;

        $source = trailingslashit( get_stylesheet_directory() ) . ltrim( (string) $relative, '/' );
        $target = get_attached_file( $attachment_id );
        if ( ! is_readable( $source ) || ! $target ) return false;

        $dir = dirname( $target );
        if ( ! is_dir( $dir ) ) wp_mkdir_p( $dir );
        if ( ! is_writable( $dir ) ) return false;

        $source_ext = strtolower( (string) pathinfo( $source, PATHINFO_EXTENSION ) );
        $target_ext = strtolower( (string) pathinfo( $target, PATHINFO_EXTENSION ) );

        // Never copy JPEG bytes over an optimiser-created WebP/AVIF filename.
        // The long-lived Learning helper below handles format conversion and
        // stale generated sizes. This small fallback is intentionally same-ext only.
        if ( $source_ext !== $target_ext || ! @copy( $source, $target ) ) return false;

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $meta = wp_generate_attachment_metadata( $attachment_id, $target );
        if ( is_array( $meta ) && $meta ) wp_update_attachment_metadata( $attachment_id, $meta );
        clean_attachment_cache( $attachment_id );
        return true;
    }
}

/**
 * Refresh only the managed demo media. User-authored media is never touched.
 */
if ( ! function_exists( 'wpbb_child_v149_sync_demo_media' ) ) {
    function wpbb_child_v149_sync_demo_media() {
        $version = '3.8.11.49';
        if ( (string) get_option( 'wpbb_child_v149_media_version', '' ) === $version ) return;

        $course_ids = get_posts( array(
            'post_type'      => 'course',
            'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => 12,
            'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_key'       => '_wp_theme_demo_course',
            'meta_value'     => '1',
        ) );
        foreach ( array_values( $course_ids ) as $index => $post_id ) {
            if ( $index > 5 ) break;
            $thumbnail_id = absint( get_post_thumbnail_id( $post_id ) );
            if ( $thumbnail_id ) {
                if ( function_exists( 'wpbb_elearning_refresh_bundled_attachment_v381041' ) ) {
                    wpbb_elearning_refresh_bundled_attachment_v381041( $thumbnail_id, 'assets/img/demo' );
                } else {
                    wpbb_child_v149_refresh_attachment( $thumbnail_id, 'assets/img/demo/item-' . ( $index + 1 ) . '.jpg' );
                }
                update_post_meta( $post_id, '_wpbb_child_v149_media', $version );
            }
        }

        foreach ( range( 1, 6 ) as $index ) {
            $attachment = get_page_by_path( 'elearning-blog-blog-' . $index, OBJECT, 'attachment' );
            if ( $attachment instanceof WP_Post ) {
                if ( function_exists( 'wpbb_elearning_refresh_bundled_attachment_v381041' ) ) {
                    wpbb_elearning_refresh_bundled_attachment_v381041( (int) $attachment->ID, 'assets/img/blog' );
                } else {
                    wpbb_child_v149_refresh_attachment( (int) $attachment->ID, 'assets/img/blog/blog-' . $index . '.jpg' );
                }
            }
        }

        update_option( 'wpbb_child_v149_media_version', $version, false );
    }
}
add_action( 'init', 'wpbb_child_v149_sync_demo_media', 90 );
add_action( 'wp_theme_after_demo_import', 'wpbb_child_v149_sync_demo_media', 1490 );
