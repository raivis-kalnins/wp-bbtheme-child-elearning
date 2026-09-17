<?php
/**
 * WP BBTheme Child E-Learning 3.8.11.40.
 *
 * Consolidated frontend owner based on the stable Automotive 3.8.11.39 pass:
 * - retire conflicting v119-v139 layout/runtime layers and keep v118 as base;
 * - one measured content edge and responsive grid owner for the homepage/course UI;
 * - force the child WooCommerce legacy shells when WooCommerce is active;
 * - repair Shop/Product/Cart/Checkout/Account geometry and account endpoints.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v140_is_woo_route' ) ) {
    function wpbb_child_v140_is_woo_route() {
        return ( function_exists( 'is_woocommerce' ) && is_woocommerce() )
            || ( function_exists( 'is_cart' ) && is_cart() )
            || ( function_exists( 'is_checkout' ) && is_checkout() )
            || ( function_exists( 'is_account_page' ) && is_account_page() );
    }
}

if ( ! function_exists( 'wpbb_child_v140_woocommerce_support' ) ) {
    function wpbb_child_v140_woocommerce_support() {
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
    }
}
add_action( 'after_setup_theme', 'wpbb_child_v140_woocommerce_support', 30 );

/**
 * Retire late pre-v140 runtime owners as well as their assets. Those files are
 * still loaded for backwards-compatible helper functions, but v140 must be the
 * only frontend geometry/WooCommerce routing owner.
 */
if ( ! function_exists( 'wpbb_child_v140_retire_late_runtime' ) ) {
    function wpbb_child_v140_retire_late_runtime() {
        foreach ( range( 119, 137 ) as $n ) {
            remove_action( 'wp_enqueue_scripts', 'wpbb_child_v' . $n . '_enqueue', PHP_INT_MAX );
        }

        foreach ( array( 134, 135, 136, 137 ) as $n ) {
            remove_filter( 'body_class', 'wpbb_child_v' . $n . '_body_class', PHP_INT_MAX );
        }

        // Earlier Woo route owners point at the same legacy files, but keeping
        // three template_include filters makes ownership/order harder to audit.
        remove_filter( 'template_include', 'wpbb_child_v75_force_woo_legacy_template', PHP_INT_MAX );
        remove_filter( 'template_include', 'wpbb_child_v108_force_woo_template', PHP_INT_MAX );

        // v134 had its own checkout and account rewrite/redirect behaviour.
        remove_filter( 'woocommerce_checkout_fields', 'wpbb_child_v134_checkout_fields', 9999 );
        remove_action( 'init', 'wpbb_child_v134_account_endpoints', 99 );
        remove_action( 'wp_loaded', 'wpbb_child_v134_flush_rules_once', 999 );
        remove_action( 'template_redirect', 'wpbb_child_v134_account_404_redirect', 1 );

        // v135 altered rendered editor content. v140 fixes layout/duplicates in
        // the DOM without replacing or stripping authored process/card content.
        remove_filter( 'the_content', 'wpbb_child_v135_clean_demo_content', 9999 );

        // Stop the older runtime-disabler callbacks from changing ownership
        // again later in the request. Their one-time include-time work has
        // already completed by the time this final file is loaded.
        remove_action( 'after_setup_theme', 'wpbb_child_v119_disable_superseded_runtime', PHP_INT_MAX );
        remove_action( 'init', 'wpbb_child_v119_disable_superseded_runtime', -9999 );
        remove_action( 'after_setup_theme', 'wpbb_child_v120_disable_v119_runtime', PHP_INT_MAX );
        remove_action( 'init', 'wpbb_child_v120_disable_v119_runtime', PHP_INT_MAX );
        remove_action( 'after_setup_theme', 'wpbb_child_v121_disable_regression_runtime', PHP_INT_MAX );
        remove_action( 'init', 'wpbb_child_v121_disable_regression_runtime', PHP_INT_MAX );
    }
}
wpbb_child_v140_retire_late_runtime();

if ( ! function_exists( 'wpbb_child_v140_enqueue' ) ) {
    function wpbb_child_v140_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();

        /*
         * v119+ contains multiple emergency layout owners with incompatible
         * 1180/1240/1280/1320px assumptions. v118 is the last stable common
         * base. This final layer owns everything after it.
         */
        foreach ( range( 119, 139 ) as $n ) {
            $handle = 'wpbb-suite-v' . $n;
            wp_dequeue_style( $handle );
            wp_deregister_style( $handle );
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $base_css = '/assets/suite-v118.css';
        $base_js  = '/assets/suite-v118.js';
        if ( is_readable( $dir . $base_css ) ) {
            wp_enqueue_style( 'wpbb-suite-v118', $uri . $base_css, array(), (string) filemtime( $dir . $base_css ) );
        }
        if ( is_readable( $dir . $base_js ) ) {
            wp_enqueue_script( 'wpbb-suite-v118', $uri . $base_js, array(), (string) filemtime( $dir . $base_js ), false );
        }

        $css = '/assets/suite-v140.css';
        $js  = '/assets/suite-v140.js';
        if ( is_readable( $dir . $css ) ) {
            wp_enqueue_style(
                'wpbb-suite-v140',
                $uri . $css,
                wp_style_is( 'wpbb-suite-v118', 'registered' ) || wp_style_is( 'wpbb-suite-v118', 'enqueued' ) ? array( 'wpbb-suite-v118' ) : array(),
                (string) filemtime( $dir . $css )
            );
        }
        if ( is_readable( $dir . $js ) ) {
            wp_enqueue_script(
                'wpbb-suite-v140',
                $uri . $js,
                wp_script_is( 'wpbb-suite-v118', 'registered' ) || wp_script_is( 'wpbb-suite-v118', 'enqueued' ) ? array( 'wpbb-suite-v118' ) : array(),
                (string) filemtime( $dir . $js ),
                true
            );
            wp_add_inline_script(
                'wpbb-suite-v140',
                'window.wpbbSuiteV140=' . wp_json_encode(
                    array(
                        'version' => '3.8.11.40',
                        'theme'   => 'elearning',
                    ),
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                ) . ';',
                'before'
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v140_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v140_body_class' ) ) {
    function wpbb_child_v140_body_class( $classes ) {
        $classes[] = 'wpbb-v140';
        $classes[] = 'wpbb-v140-theme-elearning';
        if ( wpbb_child_v140_is_woo_route() ) $classes[] = 'wp-theme-uses-woo-legacy-shell';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v140_body_class', PHP_INT_MAX );

/** Force one child-owned WooCommerce shell instead of mixing page/block output. */
if ( ! function_exists( 'wpbb_child_v140_force_woo_template' ) ) {
    function wpbb_child_v140_force_woo_template( $template ) {
        if ( is_admin() || is_feed() || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) return $template;
        if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) return $template;

        $candidate = '';
        if ( function_exists( 'is_cart' ) && is_cart() ) {
            $candidate = 'cart.php';
        } elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
            $candidate = 'checkout.php';
        } elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
            $candidate = 'account.php';
        } elseif ( function_exists( 'is_product' ) && is_product() ) {
            $candidate = 'product.php';
        } elseif ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) {
            $candidate = 'catalog.php';
        }

        if ( '' === $candidate ) return $template;
        $path = trailingslashit( get_stylesheet_directory() ) . 'woocommerce-legacy/' . $candidate;
        return is_readable( $path ) ? $path : $template;
    }
}
add_filter( 'template_include', 'wpbb_child_v140_force_woo_template', PHP_INT_MAX );

/** Keep WooCommerce page ownership pointed at published canonical demo pages. */
if ( ! function_exists( 'wpbb_child_v140_ensure_woo_pages' ) ) {
    function wpbb_child_v140_ensure_woo_pages() {
        if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) return;
        $map = array(
            'shop'       => 'woocommerce_shop_page_id',
            'cart'       => 'woocommerce_cart_page_id',
            'checkout'   => 'woocommerce_checkout_page_id',
            'my-account' => 'woocommerce_myaccount_page_id',
        );
        foreach ( $map as $slug => $option ) {
            $page = get_page_by_path( $slug, OBJECT, 'page' );
            if ( ! $page || 'publish' !== $page->post_status ) continue;
            if ( (int) get_option( $option ) !== (int) $page->ID ) update_option( $option, (int) $page->ID, false );
        }
    }
}
add_action( 'init', 'wpbb_child_v140_ensure_woo_pages', 95 );

if ( ! function_exists( 'wpbb_child_v140_account_endpoint_map' ) ) {
    function wpbb_child_v140_account_endpoint_map() {
        $map = array(
            'orders'                    => 'orders',
            'view-order'                => 'view-order',
            'downloads'                 => 'downloads',
            'edit-address'              => 'edit-address',
            'payment-methods'           => 'payment-methods',
            'add-payment-method'        => 'add-payment-method',
            'delete-payment-method'     => 'delete-payment-method',
            'set-default-payment-method'=> 'set-default-payment-method',
            'edit-account'              => 'edit-account',
            'lost-password'             => 'lost-password',
            'customer-logout'           => 'customer-logout',
        );
        if ( function_exists( 'WC' ) && WC() && isset( WC()->query ) && is_object( WC()->query ) && method_exists( WC()->query, 'get_query_vars' ) ) {
            foreach ( (array) WC()->query->get_query_vars() as $key => $slug ) {
                if ( isset( $map[ $key ] ) && '' !== trim( (string) $slug ) ) $map[ $key ] = sanitize_title( (string) $slug );
            }
        }
        return array_filter( $map, 'strlen' );
    }
}

if ( ! function_exists( 'wpbb_child_v140_account_rewrites' ) ) {
    function wpbb_child_v140_account_rewrites() {
        if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) return;
        foreach ( wpbb_child_v140_account_endpoint_map() as $slug ) {
            $slug = sanitize_title( (string) $slug );
            if ( '' === $slug ) continue;
            add_rewrite_endpoint( $slug, EP_PAGES );
            add_rewrite_rule( '^my-account/' . preg_quote( $slug, '~' ) . '/?$', 'index.php?pagename=my-account&' . $slug . '=', 'top' );
            add_rewrite_rule( '^my-account/' . preg_quote( $slug, '~' ) . '/([^/]+)/?$', 'index.php?pagename=my-account&' . $slug . '=$matches[1]', 'top' );
        }
    }
}
add_action( 'init', 'wpbb_child_v140_account_rewrites', 120 );

if ( ! function_exists( 'wpbb_child_v140_query_vars' ) ) {
    function wpbb_child_v140_query_vars( $vars ) {
        foreach ( wpbb_child_v140_account_endpoint_map() as $slug ) {
            if ( ! in_array( $slug, $vars, true ) ) $vars[] = $slug;
        }
        return $vars;
    }
}
add_filter( 'query_vars', 'wpbb_child_v140_query_vars', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v140_account_request' ) ) {
    function wpbb_child_v140_account_request( $query_vars ) {
        if ( is_admin() || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) return $query_vars;
        $request_path = (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
        $home_path    = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
        if ( $home_path && '/' !== $home_path && 0 === strpos( $request_path, $home_path ) ) $request_path = substr( $request_path, strlen( $home_path ) );
        $path = trim( $request_path, '/' );
        if ( 'my-account' === $path || 0 !== strpos( $path, 'my-account/' ) ) return $query_vars;

        $parts = array_values( array_filter( explode( '/', substr( $path, strlen( 'my-account/' ) ) ), 'strlen' ) );
        if ( empty( $parts ) ) return $query_vars;
        $requested_slug = sanitize_title( rawurldecode( (string) $parts[0] ) );
        if ( ! in_array( $requested_slug, wpbb_child_v140_account_endpoint_map(), true ) ) return $query_vars;

        $query_vars['pagename'] = 'my-account';
        $query_vars[ $requested_slug ] = isset( $parts[1] ) ? sanitize_text_field( rawurldecode( (string) $parts[1] ) ) : '';
        unset( $query_vars['error'], $query_vars['name'] );
        return $query_vars;
    }
}
add_filter( 'request', 'wpbb_child_v140_account_request', 1 );

if ( ! function_exists( 'wpbb_child_v140_endpoint_url' ) ) {
    function wpbb_child_v140_endpoint_url( $url, $endpoint, $value, $permalink ) {
        $page = get_page_by_path( 'my-account', OBJECT, 'page' );
        if ( ! $page || 'publish' !== $page->post_status ) return $url;
        $map  = wpbb_child_v140_account_endpoint_map();
        $slug = isset( $map[ $endpoint ] ) ? $map[ $endpoint ] : ( in_array( $endpoint, $map, true ) ? $endpoint : '' );
        if ( '' === $slug ) return $url;
        $target = trailingslashit( get_permalink( $page ) ) . trailingslashit( $slug );
        if ( '' !== (string) $value ) $target .= trailingslashit( rawurlencode( (string) $value ) );
        return $target;
    }
}
add_filter( 'woocommerce_get_endpoint_url', 'wpbb_child_v140_endpoint_url', PHP_INT_MAX, 4 );

if ( ! function_exists( 'wpbb_child_v140_flush_rewrites_once' ) ) {
    function wpbb_child_v140_flush_rewrites_once() {
        if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) return;
        $key = 'wpbb_child_v140_rewrites_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.40' === (string) get_option( $key ) ) return;
        flush_rewrite_rules( false );
        update_option( $key, '3.8.11.40', false );
    }
}
add_action( 'wp_loaded', 'wpbb_child_v140_flush_rewrites_once', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v140_related_products' ) ) {
    function wpbb_child_v140_related_products( $args ) {
        if ( ! is_array( $args ) ) return $args;
        $args['posts_per_page'] = max( 3, isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : 3 );
        $args['columns'] = 3;
        return $args;
    }
}
add_filter( 'woocommerce_output_related_products_args', 'wpbb_child_v140_related_products', PHP_INT_MAX );
