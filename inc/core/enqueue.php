<?php
/**
 * Enqueue front-end assets for theme and WooCommerce
 * @package khane_irani
 */

if (!function_exists('khane_irani_scripts')) {
    function khane_irani_scripts() {
        // Let WordPress handle jQuery normally - don't touch it
        
        // Enqueue Font Awesome - use only one CDN, defer loading
        if (!wp_style_is('font-awesome', 'enqueued') && !wp_style_is('fontawesome', 'enqueued')) {
            // Preconnect to CDN for faster loading
            add_action('wp_head', function() {
                echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>';
                echo '<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">';
            }, 1);
            
            // Load Font Awesome normally - don't defer to prevent FOUC
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        }
        
        // CRITICAL: Load CSS files in correct order to prevent FOUC
        // Priority: style.css -> overrides -> navigation -> mega-menu
        wp_enqueue_style('khane-irani-style', get_stylesheet_uri(), array(), file_exists(get_stylesheet_directory() . '/style.css') ? filemtime(get_stylesheet_directory() . '/style.css') : ( defined('_S_VERSION') ? _S_VERSION : '1.0.0') );
        wp_enqueue_style('khane-irani-overrides', get_template_directory_uri() . '/css/theme-overrides.css', array('khane-irani-style'), khane_irani_asset_version('/css/theme-overrides.css'));
        wp_enqueue_style('khane-irani-navigation-menu', get_template_directory_uri() . '/css/navigation-menu.css', array('khane-irani-overrides'), khane_irani_asset_version('/css/navigation-menu.css'));
        wp_enqueue_style('khane-irani-mega-menu', get_template_directory_uri() . '/css/mega-menu.css', array('khane-irani-navigation-menu'), khane_irani_asset_version('/css/mega-menu.css'));
        
        // Inline critical CSS FIRST in head to prevent FOUC
        add_action('wp_head', function() {
            // Inline fonts.css (local font) - Critical for preventing FOUT
            $fonts_css_path = get_template_directory() . '/css/fonts.css';
            if (file_exists($fonts_css_path)) {
                $fonts_css = file_get_contents($fonts_css_path);
                echo '<style id="khane-irani-fonts-inline">' . $fonts_css . '</style>';
            }
            
            // Inline header-dynamic.css (very small file)
            $header_dynamic_css_path = get_template_directory() . '/css/header-dynamic.css';
            if (file_exists($header_dynamic_css_path)) {
                $header_dynamic_css = file_get_contents($header_dynamic_css_path);
                echo '<style id="khane-irani-header-dynamic-inline">' . $header_dynamic_css . '</style>';
            }
        }, 1);
        
        // Enqueue global theme script with jQuery dependency - defer to prevent blocking
        wp_enqueue_script('khane-irani-global', get_template_directory_uri() . '/js/global.js', array('jquery'), khane_irani_asset_version('/js/global.js'), true);
        
        // Defer non-critical scripts
        add_filter('script_loader_tag', function($tag, $handle) {
            $defer_scripts = array(
                'khane-irani-global',
                'khane-irani-product-actions',
                'khane-irani-navigation',
                'khane-irani-cart-sidebar',
                'khane-irani-fontawesome-fallback',
            );
            
            if (in_array($handle, $defer_scripts)) {
                $tag = str_replace(' src', ' defer src', $tag);
            }
            
            return $tag;
        }, 10, 2);
        
        // Product Actions (Wishlist, Compare, Add to Cart)
        if (class_exists('WooCommerce')) {
            wp_enqueue_script('khane-irani-product-actions', get_template_directory_uri() . '/js/product-actions.js', array('jquery'), khane_irani_asset_version('/js/product-actions.js'), true);
            
            // Enqueue wishlist and compare CSS on account pages
            if (function_exists('is_account_page') && is_account_page()) {
                wp_enqueue_style('khane-irani-wishlist-compare', get_template_directory_uri() . '/css/wishlist-compare.css', array('khane-irani-style'), khane_irani_asset_version('/css/wishlist-compare.css'));
            }
            
            // Get cart items for checking if product is in cart
            $cart_items = array();
            if (function_exists('WC') && WC()->cart) {
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $cart_items[] = $cart_item['product_id'];
                }
            }
            
            // Get wishlist and compare items for logged in users
            $wishlist_items = array();
            $compare_items = array();
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $wishlist = get_user_meta($user_id, 'ki_wishlist', true);
                $compare = get_user_meta($user_id, 'ki_compare', true);
                
                if (is_array($wishlist)) {
                    $wishlist_items = $wishlist;
                }
                if (is_array($compare)) {
                    $compare_items = $compare;
                }
            }
            
            wp_localize_script('khane-irani-product-actions', 'ki_product_actions', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ki_actions_nonce'),
                'is_logged_in' => is_user_logged_in() ? '1' : '0',
                'login_url' => wc_get_page_permalink('myaccount'),
                'cart_items' => $cart_items,
                'wishlist_items' => $wishlist_items,
                'compare_items' => $compare_items,
                'wc_ajax_url' => function_exists('WC') && WC()->ajax ? WC()->ajax->get_endpoint('%%endpoint%%') : '',
            ));
        }
        
        // Swiper CSS and JS (for sliders and carousels) - load normally
        wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');
        wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
        
        if (is_front_page()) {
            wp_enqueue_style('khane-irani-front-page', get_template_directory_uri() . '/css/front-page.css', array('swiper'), khane_irani_asset_version('/css/front-page.css'));
            // Defer front-page JS - not critical for initial render
            wp_enqueue_script('khane-irani-front-page', get_template_directory_uri() . '/js/front-page.js', array('jquery', 'swiper'), khane_irani_asset_version('/js/front-page.js'), true);
            wp_enqueue_script('khane-irani-banner-slider', get_template_directory_uri() . '/js/banner-slider.js', array('swiper'), khane_irani_asset_version('/js/banner-slider.js'), true);
            
            // Defer front-page scripts
            add_filter('script_loader_tag', function($tag, $handle) {
                if (in_array($handle, array('khane-irani-front-page', 'khane-irani-banner-slider'))) {
                    $tag = str_replace(' src', ' defer src', $tag);
                }
                return $tag;
            }, 10, 2);
            
            // Load front-page.css normally - don't defer
        }
        if (is_page('about') || is_page('contact') || is_page_template('page-about.php') || is_page_template('page-contact.php')) {
            wp_enqueue_style('khane-irani-pages', get_template_directory_uri() . '/css/pages.css', array(), khane_irani_asset_version('/css/pages.css'));
            wp_enqueue_script('khane-irani-pages', get_template_directory_uri() . '/js/pages.js', array('jquery'), khane_irani_asset_version('/js/pages.js'), true);
        }
        wp_enqueue_script('khane-irani-navigation', get_template_directory_uri() . '/js/navigation.js', array('jquery'), khane_irani_asset_version('/js/navigation.js'), true);
        
        // Enqueue cart sidebar styles and scripts - load normally
        wp_enqueue_style('khane-irani-cart-sidebar', get_template_directory_uri() . '/css/cart-sidebar.css', array('khane-irani-style'), khane_irani_asset_version('/css/cart-sidebar.css'));
        wp_enqueue_script('khane-irani-cart-sidebar', get_template_directory_uri() . '/js/cart-sidebar.js', array('jquery'), khane_irani_asset_version('/js/cart-sidebar.js'), true);
        
        // Enqueue Font Awesome fallback script
        wp_enqueue_script('khane-irani-fontawesome-fallback', get_template_directory_uri() . '/js/fontawesome-fallback.js', array(), khane_irani_asset_version('/js/fontawesome-fallback.js'), true);
        
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
    add_action('wp_enqueue_scripts', 'khane_irani_scripts');
}

if (!function_exists('khane_irani_woocommerce_scripts')) {
    function khane_irani_woocommerce_scripts() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        // Check global flags set by early detection
        $is_cart_page = isset($GLOBALS['zarin_is_cart']) && $GLOBALS['zarin_is_cart'];
        $is_checkout_page = isset($GLOBALS['zarin_is_checkout']) && $GLOBALS['zarin_is_checkout'];
        
        // Fallback to WooCommerce conditional functions
        if (!$is_cart_page) {
            $is_cart_page = function_exists('is_cart') && is_cart();
        }
        if (!$is_checkout_page) {
            $is_checkout_page = function_exists('is_checkout') && is_checkout() && 
                               (!function_exists('is_wc_endpoint_url') || !is_wc_endpoint_url('order-received'));
        }
        
        // Final fallback: URL detection
        if (!$is_checkout_page || !$is_cart_page) {
            $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
            if (!$is_checkout_page && (strpos($request_uri, '/checkout') !== false || strpos($request_uri, '/checkout/') !== false)) {
                $is_checkout_page = true;
            }
            if (!$is_cart_page && (strpos($request_uri, '/cart') !== false || strpos($request_uri, '/cart/') !== false)) {
                $is_cart_page = true;
            }
        }
        
        // Check for product page from global flag or direct detection
        $is_product_page = isset($GLOBALS['zarin_is_product']) && $GLOBALS['zarin_is_product'];
        if (!$is_product_page) {
            $is_product_page = function_exists('is_product') && is_product();
        }
        // Final fallback: URL detection for product
        if (!$is_product_page) {
            $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
            if (strpos($request_uri, '/product/') !== false) {
                $is_product_page = true;
            }
        }
        
        // Check for shop page from global flag or direct detection
        $is_shop_page = isset($GLOBALS['zarin_is_shop']) && $GLOBALS['zarin_is_shop'];
        if (!$is_shop_page) {
            $is_shop_page = (function_exists('is_shop') && is_shop()) || 
                           (function_exists('is_product_category') && is_product_category()) || 
                           (function_exists('is_product_tag') && is_product_tag());
        }
        // Final fallback: URL detection for shop
        if (!$is_shop_page) {
            $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
            if (strpos($request_uri, '/shop') !== false || strpos($request_uri, '/shop/') !== false) {
                $is_shop_page = true;
            }
        }
        
        $is_product_category_page = function_exists('is_product_category') && is_product_category();
        $is_product_tag_page = function_exists('is_product_tag') && is_product_tag();

        // Single product page - load product-specific styles and scripts
        if ($is_product_page) {
            // Load WooCommerce variation scripts FIRST - CRITICAL for variable products
            if (function_exists('wp_enqueue_script')) {
                wp_enqueue_script('wc-add-to-cart-variation');
                wp_enqueue_script('wc-single-product');
            }

            // Load WooCommerce variation styles - CRITICAL for variable products
            if (function_exists('wp_enqueue_style')) {
                wp_enqueue_style('wc-single-product');
            }

            wp_enqueue_style('khane-irani-single-product', get_template_directory_uri() . '/css/single-product-main.css', array('khane-irani-style'), khane_irani_asset_version('/css/single-product-main.css'));
            wp_enqueue_style('khane-irani-single-product-reviews', get_template_directory_uri() . '/css/single-product/reviews.css', array('khane-irani-single-product'), khane_irani_asset_version('/css/single-product/reviews.css'));
            wp_enqueue_style('khane-irani-single-product-extra', get_template_directory_uri() . '/css/single-product/extra-inline.css', array('khane-irani-single-product'), khane_irani_asset_version('/css/single-product/extra-inline.css'));
            // Load JS files with jQuery dependency - CRITICAL: modal functionality needs jQuery
            // IMPORTANT: single-product-main.js already contains modal functions with AJAX, so we don't need separate modal.js
            wp_enqueue_script('khane-irani-single-product', get_template_directory_uri() . '/js/single-product-main.js', array('jquery', 'wc-add-to-cart-variation', 'wc-single-product'), khane_irani_asset_version('/js/single-product-main.js'), true);
            // Localize script for AJAX - CRITICAL for modal AJAX buttons to work
            // Generate WooCommerce AJAX URL properly - replace placeholder with actual endpoint
            if (function_exists('WC') && WC()->ajax) {
                // Get the endpoint template (WooCommerce returns URL with %%endpoint%%)
                $wc_ajax_url = WC()->ajax->get_endpoint('%%endpoint%%');
                // Replace placeholder with actual endpoint name
                $wc_ajax_url = str_replace('%%endpoint%%', 'add_to_cart', $wc_ajax_url);
            } else {
                // Fallback: build URL manually with endpoint
                $wc_ajax_url = home_url('/?wc-ajax=add_to_cart');
            }
            $ajax_url = admin_url('admin-ajax.php');
            wp_localize_script('khane-irani-single-product', 'wc_add_to_cart_params', array(
                'ajax_url' => $ajax_url,
                'wc_ajax_url' => $wc_ajax_url,
                'i18n_view_cart' => function_exists('__') ? __('View cart', 'woocommerce') : 'View cart',
                'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart'),
                'is_cart' => function_exists('is_cart') ? is_cart() : false,
                'cart_redirect_after_add' => get_option('woocommerce_cart_redirect_after_add') === 'yes' ? 'yes' : 'no'
            ));
            // Note: modal.js is not needed because single-product-main.js already has modal functions with AJAX support
            wp_enqueue_script('khane-irani-single-product-reviews', get_template_directory_uri() . '/js/single-product/reviews.js', array('jquery', 'khane-irani-single-product'), khane_irani_asset_version('/js/single-product/reviews.js'), true);
        }
        
        // Cart page - FORCE LOAD cart-specific assets
        if ($is_cart_page) {
            // Force load base WooCommerce styles first
            wp_enqueue_style('khane-irani-woocommerce-style', get_template_directory_uri() . '/css/woocommerce-force.css', array('khane-irani-style'), khane_irani_asset_version('/css/woocommerce-force.css'));
            // Cart specific styles - ALWAYS load, no file check
            wp_enqueue_style('khane-irani-woo-cart', get_template_directory_uri() . '/css/woocommerce-cart.css', array('khane-irani-woocommerce-style'), khane_irani_asset_version('/css/woocommerce-cart.css'));
            wp_enqueue_script('khane-irani-woo-cart', get_template_directory_uri() . '/js/woocommerce-cart.js', array('jquery'), khane_irani_asset_version('/js/woocommerce-cart.js'), true);
            $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
            wp_localize_script('khane-irani-woo-cart', 'wc_add_to_cart_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'shop_url' => $shop_url,
            ));
        }
        
        // Checkout page - FORCE LOAD checkout-specific assets
        if ($is_checkout_page) {
            // Force load base WooCommerce styles first
            wp_enqueue_style('khane-irani-woocommerce-style', get_template_directory_uri() . '/css/woocommerce-force.css', array('khane-irani-style'), khane_irani_asset_version('/css/woocommerce-force.css'));
            
            // Checkout CSS - ALWAYS load, no file check needed
            wp_enqueue_style(
                'khane-irani-woo-checkout-css',
                get_template_directory_uri() . '/css/woocommerce-checkout.css',
                array('khane-irani-woocommerce-style', 'khane-irani-overrides'),
                khane_irani_asset_version('/css/woocommerce-checkout.css'),
                'all'
            );
            
            // Checkout JS - ALWAYS load, no file check needed
            wp_enqueue_script(
                'khane-irani-woo-checkout-js',
                get_template_directory_uri() . '/js/woocommerce-checkout.js',
                array('jquery'),
                khane_irani_asset_version('/js/woocommerce-checkout.js'),
                true
            );

            // Ensure WooCommerce core checkout scripts are loaded (critical for gateways)
            if (wp_script_is('wc-checkout', 'registered')) {
                wp_enqueue_script('wc-checkout');
            }
            if (wp_script_is('wc-country-select', 'registered')) {
                wp_enqueue_script('wc-country-select');
            }
            if (wp_script_is('wc-address-i18n', 'registered')) {
                wp_enqueue_script('wc-address-i18n');
            }
            if (wp_script_is('selectWoo', 'registered')) {
                wp_enqueue_script('selectWoo');
            }
        }
        
        // Shop/Category/Tag pages - load shop-specific assets
        if ($is_shop_page || $is_product_category_page || $is_product_tag_page) {
            // Load base WooCommerce styles
            wp_enqueue_style('khane-irani-woocommerce-style', get_template_directory_uri() . '/css/woocommerce-force.css', array('khane-irani-style'), khane_irani_asset_version('/css/woocommerce-force.css'));
            // Header styles for shop pages
            wp_enqueue_style('khane-irani-shop-header', get_template_directory_uri() . '/css/single-product/header.css', array('khane-irani-woocommerce-style'), khane_irani_asset_version('/css/single-product/header.css'));
            // Shop specific styles
            wp_enqueue_style('khane-irani-woo-grid', get_template_directory_uri() . '/css/woocommerce-grid.css', array('khane-irani-woocommerce-style'), khane_irani_asset_version('/css/woocommerce-grid.css'));
            wp_enqueue_style('khane-irani-woo-force', get_template_directory_uri() . '/css/woocommerce-force.css', array('khane-irani-woocommerce-style'), khane_irani_asset_version('/css/woocommerce-force.css'));
            wp_enqueue_script('khane-irani-woo-grid', get_template_directory_uri() . '/js/woocommerce-grid.js', array(), khane_irani_asset_version('/js/woocommerce-grid.js'), true);
            wp_enqueue_script('khane-irani-shop-filters', get_template_directory_uri() . '/js/shop-filters.js', array('jquery'), khane_irani_asset_version('/js/shop-filters.js'), true);
        }
    }
    // Primary hook - use early priority to catch all cases
    add_action('wp_enqueue_scripts', 'khane_irani_woocommerce_scripts', 20);
}

// Early detection function that runs before wp_enqueue_scripts
if (!function_exists('khane_irani_woocommerce_early_detect')) {
    function khane_irani_woocommerce_early_detect() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        // Detect by URL first (most reliable before query is parsed)
        $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $is_checkout_by_url = (strpos($request_uri, '/checkout') !== false || strpos($request_uri, '/checkout/') !== false);
        $is_cart_by_url = (strpos($request_uri, '/cart') !== false || strpos($request_uri, '/cart/') !== false);
        
        // Detect product page by URL (products usually have /product/ in URL or are post_type=product)
        $is_product_by_url = (strpos($request_uri, '/product/') !== false);
        // Detect shop page by URL
        $is_shop_by_url = (strpos($request_uri, '/shop') !== false || strpos($request_uri, '/shop/') !== false);
        
        // Also check using WooCommerce functions if available (now query is parsed)
        $is_checkout = $is_checkout_by_url || (function_exists('is_checkout') && is_checkout());
        $is_cart = $is_cart_by_url || (function_exists('is_cart') && is_cart());
        $is_product = $is_product_by_url || (function_exists('is_product') && is_product());
        $is_shop = $is_shop_by_url || (function_exists('is_shop') && is_shop()) || 
                  (function_exists('is_product_category') && is_product_category()) || 
                  (function_exists('is_product_tag') && is_product_tag());
        
        if ($is_checkout) {
            $GLOBALS['zarin_is_checkout'] = true;
        }
        if ($is_cart) {
            $GLOBALS['zarin_is_cart'] = true;
        }
        if ($is_product) {
            $GLOBALS['zarin_is_product'] = true;
        }
        if ($is_shop) {
            $GLOBALS['zarin_is_shop'] = true;
        }
    }
    // Run after query is parsed but before wp_enqueue_scripts
    add_action('template_redirect', 'khane_irani_woocommerce_early_detect', 10);
}

// Fallback: Force load styles inline if still not loaded
if (!function_exists('khane_irani_woocommerce_inline_fallback')) {
    function khane_irani_woocommerce_inline_fallback() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $is_checkout = (strpos($request_uri, '/checkout') !== false || 
                       strpos($request_uri, '/checkout/') !== false ||
                       (function_exists('is_checkout') && is_checkout()));
        $is_cart = (strpos($request_uri, '/cart') !== false || 
                   strpos($request_uri, '/cart/') !== false ||
                   (function_exists('is_cart') && is_cart()));
        $is_product = (strpos($request_uri, '/product/') !== false ||
                      (function_exists('is_product') && is_product()));
        $is_shop = (strpos($request_uri, '/shop') !== false || 
                   strpos($request_uri, '/shop/') !== false ||
                   (function_exists('is_shop') && is_shop()) ||
                   (function_exists('is_product_category') && is_product_category()) ||
                   (function_exists('is_product_tag') && is_product_tag()));
        
        // If styles still not loaded, add them inline as last resort
        if ($is_checkout && !wp_style_is('khane-irani-woo-checkout-css', 'enqueued')) {
            $checkout_css_url = get_template_directory_uri() . '/css/woocommerce-checkout.css';
            echo '<link rel="stylesheet" id="khane-irani-woo-checkout-css-forced" href="' . esc_url($checkout_css_url) . '?ver=' . time() . '" media="all" />';
        }
        
        if ($is_cart && !wp_style_is('khane-irani-woo-cart', 'enqueued')) {
            $cart_css_url = get_template_directory_uri() . '/css/woocommerce-cart.css';
            $woo_base_url = get_template_directory_uri() . '/css/woocommerce-force.css';
            echo '<link rel="stylesheet" id="khane-irani-woocommerce-style-forced" href="' . esc_url($woo_base_url) . '?ver=' . time() . '" media="all" />';
            echo '<link rel="stylesheet" id="khane-irani-woo-cart-forced" href="' . esc_url($cart_css_url) . '?ver=' . time() . '" media="all" />';
        }
        
        if ($is_product && (!wp_style_is('khane-irani-single-product', 'enqueued') || !wp_script_is('khane-irani-single-product', 'enqueued'))) {
            $product_css_url = get_template_directory_uri() . '/css/single-product-main.css';
            $product_reviews_css_url = get_template_directory_uri() . '/css/single-product/reviews.css';
            $product_extra_css_url = get_template_directory_uri() . '/css/single-product/extra-inline.css';
            if (!wp_style_is('khane-irani-single-product', 'enqueued')) {
                echo '<link rel="stylesheet" id="khane-irani-single-product-forced" href="' . esc_url($product_css_url) . '?ver=' . time() . '" media="all" />';
                echo '<link rel="stylesheet" id="khane-irani-single-product-reviews-forced" href="' . esc_url($product_reviews_css_url) . '?ver=' . time() . '" media="all" />';
                echo '<link rel="stylesheet" id="khane-irani-single-product-extra-forced" href="' . esc_url($product_extra_css_url) . '?ver=' . time() . '" media="all" />';
            }
            // Note: JS files will be loaded in wp_footer fallback function
        }
        
        if ($is_shop && !wp_style_is('khane-irani-woo-grid', 'enqueued')) {
            $woo_base_url = get_template_directory_uri() . '/css/woocommerce-force.css';
            $shop_header_url = get_template_directory_uri() . '/css/single-product/header.css';
            $shop_grid_url = get_template_directory_uri() . '/css/woocommerce-grid.css';
            $shop_force_url = get_template_directory_uri() . '/css/woocommerce-force.css';
            echo '<link rel="stylesheet" id="khane-irani-woocommerce-style-shop-forced" href="' . esc_url($woo_base_url) . '?ver=' . time() . '" media="all" />';
            echo '<link rel="stylesheet" id="khane-irani-shop-header-forced" href="' . esc_url($shop_header_url) . '?ver=' . time() . '" media="all" />';
            echo '<link rel="stylesheet" id="khane-irani-woo-grid-forced" href="' . esc_url($shop_grid_url) . '?ver=' . time() . '" media="all" />';
            echo '<link rel="stylesheet" id="khane-irani-woo-force-forced" href="' . esc_url($shop_force_url) . '?ver=' . time() . '" media="all" />';
        }
    }
    // Run in wp_head as absolute last resort for CSS
    add_action('wp_head', 'khane_irani_woocommerce_inline_fallback', 999);
}

// Fallback: Force load JS files in footer if still not loaded
if (!function_exists('khane_irani_woocommerce_js_fallback')) {
    function khane_irani_woocommerce_js_fallback() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $is_product = (strpos($request_uri, '/product/') !== false ||
                      (function_exists('is_product') && is_product()));
        $is_cart = (strpos($request_uri, '/cart') !== false || 
                   strpos($request_uri, '/cart/') !== false ||
                   (function_exists('is_cart') && is_cart()));
        $is_shop = (strpos($request_uri, '/shop') !== false || 
                   strpos($request_uri, '/shop/') !== false ||
                   (function_exists('is_shop') && is_shop()) ||
                   (function_exists('is_product_category') && is_product_category()) ||
                   (function_exists('is_product_tag') && is_product_tag()));
        
        // Force load product JS files if not loaded
        if ($is_product && !wp_script_is('khane-irani-single-product', 'enqueued')) {
            $product_js_url = get_template_directory_uri() . '/js/single-product-main.js';
            $product_reviews_js_url = get_template_directory_uri() . '/js/single-product/reviews.js';
            // Note: single-product-main.js already contains modal functions with AJAX, modal.js is not needed
            echo '<script src="' . esc_url($product_js_url) . '?ver=' . time() . '"></script>';
            echo '<script src="' . esc_url($product_reviews_js_url) . '?ver=' . time() . '"></script>';
        }
        
        // Force load cart JS if not loaded
        if ($is_cart && !wp_script_is('khane-irani-woo-cart', 'enqueued')) {
            $cart_js_url = get_template_directory_uri() . '/js/woocommerce-cart.js';
            echo '<script src="' . esc_url($cart_js_url) . '?ver=' . time() . '"></script>';
        }
        
        // Force load shop JS if not loaded
        if ($is_shop && !wp_script_is('khane-irani-woo-grid', 'enqueued')) {
            $shop_grid_js_url = get_template_directory_uri() . '/js/woocommerce-grid.js';
            $shop_filters_js_url = get_template_directory_uri() . '/js/shop-filters.js';
            echo '<script src="' . esc_url($shop_grid_js_url) . '?ver=' . time() . '"></script>';
            echo '<script src="' . esc_url($shop_filters_js_url) . '?ver=' . time() . '"></script>';
        }
        
        // Force load checkout JS if not loaded
        $request_uri_checkout = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $is_checkout = (strpos($request_uri_checkout, '/checkout') !== false || 
                       strpos($request_uri_checkout, '/checkout/') !== false ||
                       (function_exists('is_checkout') && is_checkout()));
        if ($is_checkout && !wp_script_is('khane-irani-woo-checkout-js', 'enqueued')) {
            $checkout_js_url = get_template_directory_uri() . '/js/woocommerce-checkout.js';
            echo '<script src="' . esc_url($checkout_js_url) . '?ver=' . time() . '"></script>';
        }
    }
    // Run in wp_footer for JS files
    add_action('wp_footer', 'khane_irani_woocommerce_js_fallback', 5);
}


