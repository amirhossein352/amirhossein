<?php
/**
 * خانه ایرانی functions and definitions
 *
 * @package Khane_Irani
 * @author Ali Ilkhani
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

// jQuery is loaded directly in header.php before wp_head()

// Helper/feature modules
require_once get_template_directory() . '/inc/core/helpers-assets.php';
require_once get_template_directory() . '/inc/core/woocommerce-reviews.php';
require_once get_template_directory() . '/inc/core/enqueue.php';
require_once get_template_directory() . '/inc/core/woocommerce-shop.php';
require_once get_template_directory() . '/inc/mega-menu.php';
require_once get_template_directory() . '/inc/mega-menu-admin.php';

// Defer WooCommerce Blocks CSS to prevent render blocking
add_filter('style_loader_tag', function($tag, $handle) {
    // Defer WooCommerce Blocks CSS
    if (strpos($handle, 'wc-blocks') !== false || strpos($handle, 'woocommerce-blocks') !== false) {
        $tag = str_replace("media='all'", "media='print' onload=\"this.media='all'\"", $tag);
        $tag = str_replace('media="all"', 'media="print" onload="this.media=\'all\'"', $tag);
        preg_match('/href=["\']([^"\']+)["\']/', $tag, $matches);
        if (!empty($matches[1])) {
            $tag .= '<noscript><link rel="stylesheet" href="' . esc_url($matches[1]) . '"></noscript>';
        }
    }
    return $tag;
}, 10, 2);

// Product Actions (Wishlist, Compare, Add to Cart)
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/product-actions.php';
}

// Integrated Plugins - Load as part of theme
// WooCommerce Phone Authentication
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/plugins/woocommerce-phone-auth/woocommerce-phone-auth.php';
    // Run activation on theme activation (only once)
    if (!get_option('wcpa_theme_integrated')) {
        wcpa_activate();
        update_option('wcpa_theme_integrated', true);
    }
}

// Modern Account Panel
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/plugins/modern-account-panel/modern-account-panel.php';
    // Run activation on theme activation (only once)
    if (!get_option('modern_account_panel_theme_integrated')) {
        modern_account_panel_activate();
        update_option('modern_account_panel_theme_integrated', true);
    }
}

// moved to inc/helpers-assets.php

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function khane_irani_setup() {
    // Make theme available for translation - moved to init hook

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in one location
    register_nav_menus(array(
        'menu-1' => esc_html__('Primary', 'khane-irani'),
        'footer-menu' => esc_html__('Footer Menu', 'khane-irani'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for Custom Logo
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add support for full and wide align images
    add_theme_support('align-wide');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for custom line height controls
    add_theme_support('custom-line-height');

    // Add support for experimental link color control
    add_theme_support('experimental-link-color');

    // Add support for custom units
    add_theme_support('custom-units');

    // Add support for custom spacing controls
    add_theme_support('custom-spacing');
    
    // Add WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}


add_action('after_setup_theme', 'khane_irani_setup');

/**
 * Load theme textdomain on init hook (required for WordPress 6.7+)
 */
function khane_irani_load_textdomain() {
    load_theme_textdomain('khane-irani', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'khane_irani_load_textdomain');

/**
 * Set the content width in pixels
 */
function khane_irani_content_width() {
    $GLOBALS['content_width'] = apply_filters('khane_irani_content_width', 1200);
}
add_action('after_setup_theme', 'khane_irani_content_width', 0);

/**
 * Register widget area
 */
function khane_irani_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'khane-irani'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'khane-irani'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area', 'khane-irani'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Add widgets here.', 'khane-irani'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'khane_irani_widgets_init');

/**
 * Enqueue scripts and styles
 * Note: This function is now defined in inc/core/enqueue.php to avoid duplication
 */

/**
 * Add dynamic CSS based on theme settings
 * Uses CSS custom properties (CSS variables) - NO inline styles in head
 */
function khane_irani_dynamic_css() {
    // Only run on frontend, not during installation/activation or admin
    if (is_admin() || wp_doing_ajax() || defined('WP_INSTALLING')) {
        return;
    }
    
    // Check if functions exist to avoid errors during installation
    if (!function_exists('khane_irani_get_header_setting') || !function_exists('wp_add_inline_style')) {
        return;
    }
    
    // Only proceed if stylesheet is enqueued
    if (!wp_style_is('khane-irani-style', 'enqueued')) {
        return;
    }
    
    $header_bg_color   = khane_irani_get_header_setting('header_bg_color', '#ffffff');
    $header_text_color = khane_irani_get_header_setting('header_text_color', '#333333');

    // Sanitize colors
    $header_bg_color   = sanitize_hex_color($header_bg_color);
    $header_text_color = sanitize_hex_color($header_text_color);

    // Use CSS custom properties - added to stylesheet, NOT in head
    $css = ":root { --header-bg-color: {$header_bg_color}; --header-text-color: {$header_text_color}; }";
    wp_add_inline_style('khane-irani-style', $css);
}
add_action('wp_enqueue_scripts', 'khane_irani_dynamic_css', 25);

/**
 * Custom template tags for this theme
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Theme Settings - Load base class first
 */
require get_template_directory() . '/inc/settings-base.php';

/**
 * Theme Settings - Load tab classes
 */
require get_template_directory() . '/inc/settings-tabs/header-settings.php';
require get_template_directory() . '/inc/settings-tabs/front-page-settings.php';
require get_template_directory() . '/inc/settings-tabs/about-settings.php';
require get_template_directory() . '/inc/settings-tabs/faq-settings.php';
require get_template_directory() . '/inc/settings-tabs/contact-settings.php';
require get_template_directory() . '/inc/settings-tabs/footer-settings.php';

/**
 * Load main theme settings after all tab classes are loaded
 */
require get_template_directory() . '/inc/theme-settings.php';


/**
 * WooCommerce compatibility
 */
// WooCommerce support is already added in the theme setup function

/**
 * Load WooCommerce price filter
 * غیرفعال شده - باعث خطا می‌شد
 */
// if (class_exists('WooCommerce')) {
//     require get_template_directory() . '/inc/woocommerce-price-filter.php';
// }

/**
 * ZarinPal payment gateway integration
 */
// ZarinPal integration can be added here if needed

/**
 * Fallback menu function
 */
function khane_irani_fallback_menu() {
    echo '<ul id="primary-menu" class="menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('صفحه اصلی', 'khane-irani') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/shop')) . '">' . esc_html__('فروشگاه', 'khane-irani') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">' . esc_html__('درباره ما', 'khane-irani') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . esc_html__('تماس با ما', 'khane-irani') . '</a></li>';
    echo '</ul>';
}

/**
 * Add custom image sizes
 */
add_image_size('product-thumbnail', 300, 300, true);
add_image_size('hero-image', 1200, 600, true);
add_image_size('blog-thumbnail', 400, 250, true);
// Banner slider sizes for responsive images
add_image_size('banner-desktop', 1920, 488, true);
add_image_size('banner-tablet', 1024, 400, true);
add_image_size('banner-mobile', 525, 263, true);
// Logo sizes
add_image_size('logo-retina', 300, 120, false);
add_image_size('logo-standard', 150, 60, false);

/**
 * Note: After adding new image sizes, you may need to regenerate thumbnails
 * for existing images. You can use a plugin like "Regenerate Thumbnails" 
 * or run WP-CLI command: wp media regenerate
 */

/**
 * Image optimization: Enable WebP support and responsive images
 */
function khane_irani_image_quality($quality) {
    return 85; // Good balance between quality and file size
}
add_filter('jpeg_quality', 'khane_irani_image_quality');
add_filter('wp_editor_set_quality', 'khane_irani_image_quality');

/**
 * Add WebP support to WordPress
 */
function khane_irani_add_webp_support($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('mime_types', 'khane_irani_add_webp_support');

/**
 * Generate responsive image attributes for banner images
 */
function khane_irani_get_banner_image_attributes($image_id, $is_lcp = false) {
    if (!$image_id) {
        return array();
    }
    
    $attrs = array();
    
    // Get image URLs for different sizes
    $desktop_url = wp_get_attachment_image_url($image_id, 'banner-desktop');
    $tablet_url = wp_get_attachment_image_url($image_id, 'banner-tablet');
    $mobile_url = wp_get_attachment_image_url($image_id, 'banner-mobile');
    $full_url = wp_get_attachment_image_url($image_id, 'full');
    
    // Build srcset
    $srcset = array();
    if ($mobile_url) {
        $srcset[] = esc_url($mobile_url) . ' 525w';
    }
    if ($tablet_url) {
        $srcset[] = esc_url($tablet_url) . ' 1024w';
    }
    if ($desktop_url) {
        $srcset[] = esc_url($desktop_url) . ' 1920w';
    }
    if ($full_url && !in_array($full_url, array($mobile_url, $tablet_url, $desktop_url))) {
        $image_meta = wp_get_attachment_metadata($image_id);
        if ($image_meta && isset($image_meta['width'])) {
            $srcset[] = esc_url($full_url) . ' ' . $image_meta['width'] . 'w';
        }
    }
    
    if (!empty($srcset)) {
        $attrs['srcset'] = implode(', ', $srcset);
        $attrs['sizes'] = '(max-width: 525px) 525px, (max-width: 1024px) 1024px, 1920px';
    }
    
    // Set default src to mobile for better mobile performance
    $attrs['src'] = $mobile_url ? esc_url($mobile_url) : ($tablet_url ? esc_url($tablet_url) : esc_url($full_url));
    
    // Add fetchpriority for LCP image
    if ($is_lcp) {
        $attrs['fetchpriority'] = 'high';
        $attrs['loading'] = 'eager';
    } else {
        $attrs['loading'] = 'lazy';
    }
    
    // Add decoding
    $attrs['decoding'] = 'async';
    
    // Add width and height for CLS prevention - use actual image dimensions
    $image_meta = wp_get_attachment_metadata($image_id);
    if ($image_meta && isset($image_meta['width']) && isset($image_meta['height'])) {
        // Use actual image dimensions for better aspect ratio calculation
        $attrs['width'] = $image_meta['width'];
        $attrs['height'] = $image_meta['height'];
    }
    
    return $attrs;
}

/**
 * Generate responsive image attributes for logo
 */
function khane_irani_get_logo_image_attributes($image_id) {
    if (!$image_id) {
        return array();
    }
    
    $attrs = array();
    
    // Get image URLs for different sizes - use medium or thumbnail if custom sizes don't exist
    $standard_url = wp_get_attachment_image_url($image_id, 'logo-standard');
    $retina_url = wp_get_attachment_image_url($image_id, 'logo-retina');
    
    // Fallback to medium or thumbnail if custom sizes don't exist
    if (!$standard_url) {
        $standard_url = wp_get_attachment_image_url($image_id, 'medium');
    }
    if (!$standard_url) {
        $standard_url = wp_get_attachment_image_url($image_id, 'thumbnail');
    }
    
    // If still no URL, use full but we'll resize it
    $full_url = wp_get_attachment_image_url($image_id, 'full');
    if (!$standard_url) {
        $standard_url = $full_url;
    }
    
    // Build srcset for retina support
    $srcset = array();
    if ($standard_url) {
        $srcset[] = esc_url($standard_url) . ' 1x';
    }
    if ($retina_url) {
        $srcset[] = esc_url($retina_url) . ' 2x';
    } elseif ($standard_url && $standard_url !== $full_url) {
        // Use full as retina if available
        $srcset[] = esc_url($full_url) . ' 2x';
    }
    
    if (!empty($srcset)) {
        $attrs['srcset'] = implode(', ', $srcset);
    }
    
    // Set default src - prefer smaller size for better performance
    $attrs['src'] = $standard_url ? esc_url($standard_url) : esc_url($full_url);
    
    // Add loading and decoding
    $attrs['loading'] = 'eager';
    $attrs['decoding'] = 'async';
    $attrs['fetchpriority'] = 'high';
    
    // Add width and height - logo is displayed at max 150x60
    $attrs['width'] = '150';
    $attrs['height'] = '60';
    
    return $attrs;
}

/**
 * Prevent lazy loading plugins from affecting LCP images
 */
function khane_irani_prevent_lazy_load_on_lcp($attr, $attachment, $size) {
    // Check if this is a banner slide image (LCP element)
    if (isset($attr['class']) && strpos($attr['class'], 'banner-slide-image') !== false) {
        // Check if it's the first banner (LCP)
        if (isset($attr['fetchpriority']) && $attr['fetchpriority'] === 'high') {
            // Remove lazy loading attributes that plugins might add
            unset($attr['data-lazy-src']);
            unset($attr['data-lazy-srcset']);
            unset($attr['data-lazy-sizes']);
            unset($attr['data-ll-status']);
            unset($attr['data-src']);
            unset($attr['data-srcset']);
            
            // Ensure loading is eager
            $attr['loading'] = 'eager';
            
            // Add skip-lazy class
            $attr['class'] = (isset($attr['class']) ? $attr['class'] . ' ' : '') . 'skip-lazy no-lazy';
            $attr['data-no-lazy'] = '1';
            $attr['data-skip-lazy'] = '1';
        }
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'khane_irani_prevent_lazy_load_on_lcp', 20, 3);

/**
 * Enhance WooCommerce product image with responsive attributes
 */
function khane_irani_enhance_product_image($image, $product, $size) {
    if (!$product || !$image) {
        return $image;
    }
    
    $product_id = $product->get_id();
    $image_id = get_post_thumbnail_id($product_id);
    
    if (!$image_id) {
        return $image;
    }
    
    // Get image metadata
    $image_meta = wp_get_attachment_metadata($image_id);
    if (!$image_meta) {
        return $image;
    }
    
    // Ensure the image has proper width and height attributes
    // Product images are displayed at 300x300 (woocommerce_thumbnail)
    // But we want to ensure proper responsive behavior
    
    // Add decoding="async" if not present
    if (strpos($image, 'decoding=') === false) {
        $image = str_replace('<img ', '<img decoding="async" ', $image);
    }
    
    // Ensure loading="lazy" for product images (not LCP)
    if (strpos($image, 'loading=') === false) {
        $image = str_replace('<img ', '<img loading="lazy" ', $image);
    }
    
    // Ensure proper sizes attribute for responsive images
    if (strpos($image, 'sizes=') === false && strpos($image, 'srcset=') !== false) {
        // Add sizes attribute for product images - displayed at max 315px based on PageSpeed report
        $image = str_replace('srcset=', 'sizes="(max-width: 315px) 100vw, 315px" srcset=', $image);
    }
    
    // Ensure width and height attributes are present for CLS prevention
    if (strpos($image, 'width=') === false || strpos($image, 'height=') === false) {
        // Try to extract dimensions from image meta
        $width = isset($image_meta['width']) ? $image_meta['width'] : 300;
        $height = isset($image_meta['height']) ? $image_meta['height'] : 300;
        
        // Calculate aspect ratio and set displayed dimensions (315x315 based on PageSpeed)
        $display_width = 315;
        $display_height = 315;
        
        if (strpos($image, 'width=') === false) {
            $image = str_replace('<img ', '<img width="' . $display_width . '" ', $image);
        }
        if (strpos($image, 'height=') === false) {
            $image = str_replace('<img ', '<img height="' . $display_height . '" ', $image);
        }
    }
    
    return $image;
}
add_filter('woocommerce_product_get_image', 'khane_irani_enhance_product_image', 10, 3);

/**
 * Custom excerpt length
 */
function khane_irani_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'khane_irani_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function khane_irani_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'khane_irani_excerpt_more');



/**
 * Add theme support for WooCommerce
 */
function khane_irani_woocommerce_setup() {
    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 300,
        'single_image_width'    => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'default_columns' => 4,
            'min_columns'     => 1,
            'max_columns'     => 6,
        ),
    ));
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Ensure WooCommerce image sizes are properly set
    update_option('woocommerce_thumbnail_image_width', 300);
    update_option('woocommerce_thumbnail_image_height', 300);
    update_option('woocommerce_thumbnail_cropping', '1');
    update_option('woocommerce_thumbnail_cropping_custom_width', 300);
    update_option('woocommerce_thumbnail_cropping_custom_height', 300);
}
add_action('after_setup_theme', 'khane_irani_woocommerce_setup');

/**
 * WooCommerce specific scripts & stylesheets
 */
function khane_irani_woocommerce_scripts() {
    if (class_exists('WooCommerce')) {
        // Enqueue global shop styles
        if (!is_product()) {
            wp_enqueue_style('khane-irani-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array('khane-irani-style'), khane_irani_asset_version('/woocommerce.css'));
            // Load header styles for shop page
            wp_enqueue_style('khane-irani-shop-header', get_template_directory_uri() . '/css/single-product/header.css', array('khane-irani-woocommerce-style'), khane_irani_asset_version('/css/single-product/header.css'));
        } else {
            // Enqueue single product stylesheet (ensure it always loads and after base theme style)
            wp_enqueue_style('khane-irani-single-product', get_template_directory_uri() . '/css/single-product-main.css', array('khane-irani-style'), khane_irani_asset_version('/css/single-product-main.css'));
            // Enqueue dedicated reviews stylesheet
            wp_enqueue_style('khane-irani-single-product-reviews', get_template_directory_uri() . '/css/single-product/reviews.css', array('khane-irani-single-product'), khane_irani_asset_version('/css/single-product/reviews.css'));
            // Enqueue single product interactions (FAQ accordion, minor UX)
            wp_enqueue_script('khane-irani-single-product', get_template_directory_uri() . '/js/single-product-main.js', array(), khane_irani_asset_version('/js/single-product-main.js'), true);
            // Enqueue dedicated reviews interactions
            wp_enqueue_script('khane-irani-single-product-reviews', get_template_directory_uri() . '/js/single-product/reviews.js', array('jquery'), khane_irani_asset_version('/js/single-product/reviews.js'), true);
        }
        
        // Add inline CSS only for archives, not single
        if (!is_product()) {
            $custom_css = "
            .woocommerce ul.products {display:grid!important;grid-template-columns:repeat(auto-fill,minmax(300px,1fr))!important;gap:25px!important}
            .zs-shop-layout{display:flex!important;gap:30px!important}
            .zs-shop-sidebar{width:280px!important;background:#f8f9fa!important;padding:25px!important;border-radius:15px!important;display:block!important}
            .zs-shop-content{flex:1!important}
            ";
            wp_add_inline_style('khane-irani-woocommerce-style', $custom_css);
        }
        
        // Enqueue shop filters script on shop pages
        if (is_shop() || is_product_category() || is_product_tag()) {
            wp_enqueue_script('khane-irani-shop-filters', get_template_directory_uri() . '/js/shop-filters.js', array('jquery'), khane_irani_asset_version('/js/shop-filters.js'), true);
        }
    }
}
// Moved to inc/core/enqueue.php to avoid conflicts
// add_action('wp_enqueue_scripts', 'khane_irani_woocommerce_scripts');

/**
 * Customize WooCommerce product review form (fields, labels, classes)
 */
function khane_irani_customize_review_form($args) {
    // Title and submit button
    $args['title_reply']       = __('ثبت نظر شما', 'khane-irani');
    $args['label_submit']      = __('ارسال نظر', 'khane-irani');
    $args['comment_notes_after'] = '';
    $args['class_submit']      = array('button', 'zs-review-submit');
    $args['submit_field']      = '<p class="form-submit">%1$s %2$s</p>';

    // Comment field (textarea)
    $args['comment_field'] =
        '<p class="comment-form-comment">'
        . '<label for="comment" class="zs-field-label">' . __('متن نظر', 'khane-irani') . '</label>'
        . '<textarea id="comment" name="comment" cols="45" rows="6" placeholder="تجربه خود را با دیگران به اشتراک بگذارید..." class="zs-input" required></textarea>'
        . '</p>';

    // Name & Email
    $author_placeholder = __('نام و نام خانوادگی', 'khane-irani');
    $email_placeholder  = __('ایمیل (نمایش داده نمی‌شود)', 'khane-irani');

    $args['fields']['author'] =
        '<p class="comment-form-author">'
        . '<label for="author" class="zs-field-label">' . __('نام', 'khane-irani') . '</label>'
        . '<input id="author" name="author" type="text" placeholder="' . esc_attr($author_placeholder) . '" class="zs-input" required />'
        . '</p>';

    $args['fields']['email'] =
        '<p class="comment-form-email">'
        . '<label for="email" class="zs-field-label">' . __('ایمیل', 'khane-irani') . '</label>'
        . '<input id="email" name="email" type="email" placeholder="' . esc_attr($email_placeholder) . '" class="zs-input" required />'
        . '</p>';

    // Add a wrapper class to the form
    $args['class_form'] = array('comment-form', 'zs-review-form');

    return $args;
}
add_filter('woocommerce_product_review_comment_form_args', 'khane_irani_customize_review_form');

/**
 * Remove WooCommerce default styles completely - Multiple methods
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Dequeue WooCommerce styles with multiple hooks
 */
function khane_irani_dequeue_woocommerce_styles() {
    // Remove all WooCommerce styles
    wp_dequeue_style('woocommerce-general');
    wp_dequeue_style('woocommerce-layout');
    wp_dequeue_style('woocommerce-smallscreen');
    wp_dequeue_style('woocommerce_frontend_styles');
    wp_dequeue_style('woocommerce_fancybox_styles');
    wp_dequeue_style('woocommerce_chosen_styles');
    wp_dequeue_style('woocommerce_prettyPhoto_css');
    wp_dequeue_style('woocommerce-inline');
    
    // Remove any theme's WooCommerce styles
    wp_dequeue_style('storefront-woocommerce-style');
    wp_dequeue_style('twentytwentyone-woocommerce-style');
}
add_action('wp_enqueue_scripts', 'khane_irani_dequeue_woocommerce_styles', 99);
add_action('wp_print_styles', 'khane_irani_dequeue_woocommerce_styles', 100);
add_action('wp_head', 'khane_irani_dequeue_woocommerce_styles', 1);

/**
 * Customize WooCommerce shop loop - حذف نمایش تعداد نتایج و مرتب‌سازی
 */
function khane_irani_customize_shop_loop() {
    // Remove default WooCommerce actions - حذف کامل
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
}
add_action('init', 'khane_irani_customize_shop_loop');

/**
 * Force override WooCommerce templates
 */
function khane_irani_woocommerce_locate_template($template, $template_name, $template_path) {
    $theme_template = get_template_directory() . '/woocommerce/' . $template_name;
    
    // Force use our template if it exists
    if (file_exists($theme_template)) {
        return $theme_template;
    }
    
    return $template;
}
add_filter('woocommerce_locate_template', 'khane_irani_woocommerce_locate_template', 10, 3);

/**
 * Force WooCommerce to use our templates with higher priority
 */
function khane_irani_force_woocommerce_templates() {
    // Remove default WooCommerce template loading
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
    
    // Force our custom templates
    add_filter('template_include', function($template) {
        // Force archive template for shop pages
        if (is_shop() || is_product_category() || is_product_tag()) {
            $custom_template = get_template_directory() . '/woocommerce/archive-product.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        // Force woocommerce.php for cart and checkout to remove sidebar
        if (is_cart() || is_checkout()) {
            $woocommerce_template = get_template_directory() . '/woocommerce.php';
            if (file_exists($woocommerce_template)) {
                return $woocommerce_template;
            }
        }
        
        return $template;
    }, 99);
}
add_action('template_redirect', 'khane_irani_force_woocommerce_templates');

/**
 * NUCLEAR OPTION: Force complete override of WooCommerce output
 */
function khane_irani_nuclear_woocommerce_override() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        // Remove all default WooCommerce actions for shop
        remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        
        // Add our custom actions for shop
        add_action('woocommerce_before_shop_loop_item', 'khane_irani_custom_product_wrapper_start', 5);
        add_action('woocommerce_after_shop_loop_item', 'khane_irani_custom_product_wrapper_end', 25);
    }
}

/**
 * WooCommerce Single Product Customizations (WoodMart Style)
 */
function khane_irani_woocommerce_single_product_hooks() {
    // Remove default breadcrumb from woocommerce_before_main_content
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    
    // Custom templates are automatically loaded from theme/woocommerce/ folder
    
    // Add custom styling to single product elements
    add_action('wp_head', 'khane_irani_single_product_inline_styles');
}
add_action('init', 'khane_irani_woocommerce_single_product_hooks');



/**
 * Add inline styles for single product
 */
function khane_irani_single_product_inline_styles() {
    if (!is_product()) return;
    ?>
    <style>
    /* Additional Single Product Styles */
    .woocommerce-message,
    .woocommerce-error,
    .woocommerce-info {
        background: #f8f9fa !important;
        color: var(--zs-teal-dark, #00A796) !important;
        border: 1px solid #e9ecef !important;
        border-radius: 10px !important;
        padding: 15px 20px !important;
        margin-bottom: 20px !important;
    }
    
    .woocommerce-message {
        border-left: 4px solid #27ae60 !important;
    }
    
    .woocommerce-error {
        border-left: 4px solid var(--zs-teal-medium, #37C3B3) !important;
    }
    
    .woocommerce-info {
        border-left: 4px solid var(--zs-teal-medium, #37C3B3) !important;
    }
    
    /* Product Gallery Improvements */
    .woocommerce div.product div.images .woocommerce-product-gallery__trigger {
        background: rgba(52, 152, 219, 0.9) !important;
        color: white !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 16px !important;
        top: 20px !important;
        right: 20px !important;
    }
    
    .woocommerce div.product div.images .flex-control-thumbs {
        margin-top: 20px !important;
    }
    
    .woocommerce div.product div.images .flex-control-thumbs li {
        margin: 0 10px 10px 0 !important;
    }
    
    .woocommerce div.product div.images .flex-control-thumbs li img {
        border: 2px solid transparent !important;
        border-radius: 8px !important;
        transition: all 0.3s ease !important;
    }
    
    .woocommerce div.product div.images .flex-control-thumbs li img.flex-active,
    .woocommerce div.product div.images .flex-control-thumbs li:hover img {
        border-color: var(--zs-teal-medium, #37C3B3) !important;
    }
    
    /* Variation Form Styling - Like Futurepedia */
    .woocommerce div.product form.variations_form .variations {
        margin: 15px 0 !important;
        padding-top: 15px !important;
        border-top: 1px solid #e5e7eb !important;
    }

    .woocommerce div.product form.variations_form .variations td {
        padding: 10px 0 !important;
    }

    .woocommerce div.product form.variations_form .variations select {
        width: 100% !important;
        padding: 10px 12px !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        background: #ffffff !important;
        color: #374151 !important;
        transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
    }

    .woocommerce div.product form.variations_form .variations select:focus {
        border-color: #3b82f6 !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        background: #ffffff !important;
        color: #374151 !important;
    }

    .woocommerce div.product form.variations_form .variations select option {
        background: #ffffff !important;
        color: #374151 !important;
        padding: 8px !important;
    }

    .woocommerce div.product form.variations_form .variations label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151 !important;
        margin-bottom: 6px !important;
    }
    
    .woocommerce div.product form.variations_form .single_variation_wrap .single_variation {
        background: #f8f9fa !important;
        padding: 20px !important;
        border-radius: 10px !important;
        margin: 20px 0 !important;
    }
    
    /* Stock Status */
    .woocommerce div.product .stock {
        font-weight: 600 !important;
        padding: 8px 15px !important;
        border-radius: 20px !important;
        font-size: 14px !important;
        display: inline-block !important;
        margin: 10px 0 !important;
    }
    
    .woocommerce div.product .stock.in-stock {
        background: rgba(39, 174, 96, 0.1) !important;
        color: #27ae60 !important;
    }
    
    .woocommerce div.product .stock.out-of-stock {
        background: rgba(231, 76, 60, 0.1) !important;
        color: var(--zs-teal-medium, #37C3B3) !important;
    }
    </style>
    <?php
}
// Moved to static CSS: css/single-product/extra-inline.css
// add_action('woocommerce_before_shop_loop', 'khane_irani_nuclear_woocommerce_override');

/**
 * Reorder single product summary elements (title, excerpt, price, CTA, meta)
 */
function khane_irani_reorder_single_summary() {
    if (!is_product()) return;

    // Remove defaults
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

    // Add in desired order
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 15);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 20);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 30);
}
add_action('wp', 'khane_irani_reorder_single_summary');

/**
 * Force grid layout for products
 */
// Moved to static CSS/JS: css/woocommerce-grid.css, js/woocommerce-grid.js

/**
 * Custom product wrapper start - Ultra Modern E-commerce Style
 */
function khane_irani_custom_product_wrapper_start() {
    global $product;
    
    echo '<div class="zs-ultra-modern-card" style="
        background: #ffffff !important; 
        border-radius: 24px !important; 
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important; 
        overflow: hidden !important; 
        height: 520px !important; 
        display: flex !important; 
        flex-direction: column !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        position: relative !important;
        cursor: pointer !important;
        backdrop-filter: blur(20px) !important;
    " onmouseover="
        this.style.transform=\'translateY(-12px) scale(1.02)\'; 
        this.style.boxShadow=\'0 20px 60px rgba(0, 0, 0, 0.25)\';
        this.style.borderColor=\'rgba(52, 152, 219, 0.3)\';
    " onmouseout="
        this.style.transform=\'translateY(0) scale(1)\'; 
        this.style.boxShadow=\'0 8px 32px rgba(0, 0, 0, 0.12)\';
        this.style.borderColor=\'rgba(255, 255, 255, 0.8)\';
    ">';
    
    // Advanced floating elements
    $product_id = $product->get_id();
    echo '<div style="position: absolute !important; top: 15px !important; left: 15px !important; z-index: 20 !important;">
        <div style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
            <button class="ki-action-btn ki-wishlist-btn" data-product-id="' . esc_attr($product_id) . '" title="علاقه‌مندی" style="
                width: 44px !important; 
                height: 44px !important; 
                border-radius: 50% !important; 
                background: rgba(255, 255, 255, 0.95) !important; 
                border: none !important; 
                display: flex !important; 
                align-items: center !important; 
                justify-content: center !important; 
                cursor: pointer !important; 
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important; 
                backdrop-filter: blur(20px) !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
                color: #6c757d !important;
            " onmouseover="
                this.style.background=\'#ff4757\'; 
                this.style.color=\'white\';
                this.style.transform=\'scale(1.1)\';
                this.style.boxShadow=\'0 8px 30px rgba(255, 71, 87, 0.4)\';
            " onmouseout="
                this.style.background=\'rgba(255, 255, 255, 0.95)\'; 
                this.style.color=\'#6c757d\';
                this.style.transform=\'scale(1)\';
                this.style.boxShadow=\'0 4px 20px rgba(0, 0, 0, 0.1)\';
            ">
                <i class="far fa-heart" style="font-size: 18px;"></i>
            </button>
            <button class="ki-action-btn ki-compare-btn" data-product-id="' . esc_attr($product_id) . '" title="مقایسه" style="
                width: 44px !important; 
                height: 44px !important; 
                border-radius: 50% !important; 
                background: rgba(255, 255, 255, 0.95) !important; 
                border: none !important; 
                display: flex !important; 
                align-items: center !important; 
                justify-content: center !important; 
                cursor: pointer !important; 
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important; 
                backdrop-filter: blur(20px) !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
                color: #6c757d !important;
            " onmouseover="
                this.style.background=\'var(--zs-teal-medium, #37C3B3)\'; 
                this.style.color=\'white\';
                this.style.transform=\'scale(1.1)\';
                this.style.boxShadow=\'0 8px 30px rgba(52, 152, 219, 0.4)\';
            " onmouseout="
                this.style.background=\'rgba(255, 255, 255, 0.95)\'; 
                this.style.color=\'#6c757d\';
                this.style.transform=\'scale(1)\';
                this.style.boxShadow=\'0 4px 20px rgba(0, 0, 0, 0.1)\';
            ">
                <i class="fas fa-exchange-alt" style="font-size: 16px;"></i>
            </button>
        </div>
    </div>';
    
    // Premium sale badge
    if ($product->is_on_sale()) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        if ($regular_price && $sale_price) {
            $discount = round((($regular_price - $sale_price) / $regular_price) * 100);
            echo '<div style="
                position: absolute !important; 
                top: 15px !important; 
                right: 15px !important; 
                background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 50%, #ff4757 100%) !important; 
                color: white !important; 
                padding: 12px 18px !important; 
                border-radius: 30px !important; 
                font-size: 13px !important; 
                font-weight: 700 !important; 
                z-index: 15 !important;
                box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4) !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                border: 2px solid rgba(255, 255, 255, 0.3) !important;
                backdrop-filter: blur(10px) !important;
            ">-' . $discount . '% OFF</div>';
        }
    }
    
    // Ultra modern product image with advanced effects
    echo '<div style="
        height: 300px !important; 
        overflow: hidden !important; 
        position: relative !important; 
        background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3) 0%, #764ba2 100%) !important; 
        border-radius: 24px 24px 0 0 !important;
    ">';
    
    echo '<a href="' . get_permalink() . '" style="
        display: block !important; 
        width: 100% !important; 
        height: 100% !important; 
        position: relative !important;
        overflow: hidden !important;
    ">';
    
    // Image with sophisticated hover
    $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large')[0];
    echo '<div style="
        width: 100% !important; 
        height: 100% !important; 
        background-image: url(' . $image_url . ') !important;
        background-size: cover !important;
        background-position: center !important;
        transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        filter: brightness(1.05) contrast(1.1) !important;
    " onmouseover="
        this.style.transform=\'scale(1.15)\';
        this.style.filter=\'brightness(1.2) contrast(1.2) saturate(1.3)\';
    " onmouseout="
        this.style.transform=\'scale(1)\';
        this.style.filter=\'brightness(1.05) contrast(1.1)\';
    "></div>';
    
    // Premium overlay with glass effect
    echo '<div style="
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.9) 0%, rgba(41, 128, 185, 0.8) 100%) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 0 !important;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        backdrop-filter: blur(20px) !important;
        flex-direction: column !important;
        gap: 15px !important;
    " onmouseover="this.style.opacity=\'1\';" onmouseout="this.style.opacity=\'0\';">
        <div style="
            background: rgba(255, 255, 255, 0.2) !important;
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 50% !important;
            width: 70px !important;
            height: 70px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            backdrop-filter: blur(10px) !important;
            transition: transform 0.3s ease !important;
        " onmouseover="this.style.transform=\'scale(1.1)\';" onmouseout="this.style.transform=\'scale(1)\';">
            <svg width="28" height="28" fill="white" viewBox="0 0 16 16">
                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
            </svg>
        </div>
        <span style="
            color: white !important; 
            font-weight: 700 !important; 
            font-size: 16px !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        ">مشاهده سریع</span>
    </div>';
    
    echo '</a>';
    echo '</div>';
    
    // Ultra premium product info
    echo '<div style="
        padding: 28px 24px 24px 24px !important; 
        flex: 1 !important; 
        display: flex !important; 
        flex-direction: column !important; 
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%) !important;
        border-radius: 0 0 24px 24px !important;
    ">';
    
    // Premium category badge
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        echo '<span style="
            color: var(--zs-teal-medium, #37C3B3) !important; 
            font-size: 11px !important; 
            font-weight: 700 !important; 
            text-transform: uppercase !important; 
            letter-spacing: 1.2px !important; 
            margin-bottom: 15px !important;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.15)) !important;
            padding: 8px 16px !important;
            border-radius: 20px !important;
            display: inline-block !important;
            width: fit-content !important;
            border: 1px solid rgba(52, 152, 219, 0.2) !important;
        ">' . $terms[0]->name . '</span>';
    }
    
    // Premium title
    echo '<h3 style="
        font-size: 18px !important; 
        font-weight: 700 !important; 
        color: var(--zs-teal-dark, #00A796) !important; 
        margin: 0 0 15px 0 !important; 
        line-height: 1.4 !important; 
        overflow: hidden !important; 
        display: -webkit-box !important; 
        -webkit-line-clamp: 2 !important; 
        -webkit-box-orient: vertical !important;
        min-height: 50px !important;
        font-family: \'IranYekan\', \'Tahoma\', sans-serif !important;
    "><a href="' . get_permalink() . '" style="
        color: inherit !important; 
        text-decoration: none !important;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        background: linear-gradient(135deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3)) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
    " onmouseover="
        this.style.transform=\'translateX(5px)\';
        this.style.textShadow=\'0 2px 10px rgba(52, 152, 219, 0.3)\';
    " onmouseout="
        this.style.transform=\'translateX(0)\';
        this.style.textShadow=\'none\';
    ">' . get_the_title() . '</a></h3>';
    
    // Premium rating
    if (wc_review_ratings_enabled()) {
        echo '<div style="
            margin-bottom: 18px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        ">';
        echo wc_get_rating_html($product->get_average_rating());
        echo '<span style="
            color: #95a5a6 !important; 
            font-size: 13px !important;
            font-weight: 500 !important;
            background: #f8f9fa !important;
            padding: 4px 10px !important;
            border-radius: 12px !important;
        ">(' . $product->get_review_count() . ' نظر)</span>';
        echo '</div>';
    }
    
    // Premium price with gradient
    echo '<div style="margin-bottom: 25px !important;">
        <span style="
            font-size: 22px !important; 
            font-weight: 800 !important; 
            background: linear-gradient(135deg, #27ae60, #2ecc71) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            text-shadow: 0 2px 10px rgba(39, 174, 96, 0.2) !important;
            font-family: \'IranYekan\', \'Tahoma\', sans-serif !important;
        ">' . $product->get_price_html() . '</span>
    </div>';
    
    // Ultra premium action buttons
    echo '<div style="
        display: flex !important; 
        gap: 12px !important; 
        align-items: center !important;
        margin-top: auto !important;
    ">
        <a href="' . esc_url($product->add_to_cart_url()) . '" 
           class="ki-action-btn ki-cart-btn add_to_cart_button" 
           data-product_id="' . esc_attr($product->get_id()) . '" 
           data-product_sku="' . esc_attr($product->get_sku()) . '"
           style="
            flex: 1 !important;
            background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3) 0%, #764ba2 50%, var(--zs-teal-medium, #37C3B3) 100%) !important;
            background-size: 200% 200% !important;
            color: white !important; 
            border: none !important; 
            padding: 16px 24px !important; 
            border-radius: 16px !important; 
            font-size: 14px !important; 
            font-weight: 700 !important; 
            text-decoration: none !important; 
            text-align: center !important; 
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            position: relative !important;
            overflow: hidden !important;
        " onmouseover="
            this.style.transform=\'translateY(-3px)\'; 
            this.style.boxShadow=\'0 12px 35px rgba(102, 126, 234, 0.6)\';
            this.style.backgroundPosition=\'100% 0\';
        " onmouseout="
            this.style.transform=\'translateY(0)\'; 
            this.style.boxShadow=\'0 8px 25px rgba(102, 126, 234, 0.4)\';
            this.style.backgroundPosition=\'0% 0\';
        ">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            افزودن به سبد
        </a>
        <button style="
            width: 54px !important;
            height: 54px !important;
            border-radius: 16px !important;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
            border: 2px solid #e9ecef !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        " onmouseover="
            this.style.background=\'linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796))\';
            this.style.borderColor=\'var(--zs-teal-medium, #37C3B3)\';
            this.style.color=\'white\';
            this.style.transform=\'scale(1.05)\';
            this.style.boxShadow=\'0 8px 25px rgba(52, 152, 219, 0.3)\';
        " onmouseout="
            this.style.background=\'linear-gradient(135deg, #f8f9fa, #e9ecef)\';
            this.style.borderColor=\'#e9ecef\';
            this.style.color=\'#6c757d\';
            this.style.transform=\'scale(1)\';
            this.style.boxShadow=\'0 4px 15px rgba(0, 0, 0, 0.1)\';
        ">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0z"/>
            </svg>
        </button>
    </div>';
    
    echo '</div>';
    echo '</div>';
}

/**
 * Custom product wrapper end
 */
function khane_irani_custom_product_wrapper_end() {
    echo '</div>';
}


/**
 * Force WooCommerce styles in head
 */
// Moved to css/woocommerce-force.css (enqueued conditionally)

/**
 * Force styles in footer as well
 */
// Replaced by css/woocommerce-force.css and js/woocommerce-grid.js

/**
 * Customize WooCommerce pagination
 */
function khane_irani_woocommerce_pagination_args($args) {
    $args['prev_text'] = 'قبلی';
    $args['next_text'] = 'بعدی';
    return $args;
}
add_filter('woocommerce_pagination_args', 'khane_irani_woocommerce_pagination_args');

/**
 * Add custom fields to WooCommerce checkout
 * NOTE: This function is already defined in inc/core/woocommerce-shop.php
 * Keeping this for backwards compatibility but the main function is in woocommerce-shop.php
 */
// Function moved to inc/core/woocommerce-shop.php

/**
 * Customize WooCommerce messages
 */
// حذف کامل پیام "مشاهده سبد خرید" کنار محصول
function khane_irani_remove_add_to_cart_message($message, $product_id) {
    return false; // پیام را کاملاً حذف می‌کند
}
add_filter('woocommerce_add_to_cart_message', 'khane_irani_remove_add_to_cart_message', 10, 2);

// حذف پیام از output
add_filter('woocommerce_add_to_cart_message_html', '__return_empty_string', 999);

// حذف پیام از fragments (AJAX)
add_filter('woocommerce_add_to_cart_fragments', 'khane_irani_remove_message_from_fragments', 999);
function khane_irani_remove_message_from_fragments($fragments) {
    // حذف تمام fragments که شامل پیام add to cart هستند
    foreach ($fragments as $key => $value) {
        if (is_string($value)) {
            // حذف همه پیام‌های مربوط به سبد خرید
            if (strpos($value, 'woocommerce-message') !== false || 
                strpos($value, 'woocommerce-info') !== false ||
                strpos($value, 'مشاهده سبد خرید') !== false || 
                strpos($value, 'View cart') !== false ||
                strpos($value, 'سبد خرید') !== false ||
                strpos($value, 'cart') !== false ||
                strpos($value, 'added') !== false) {
                unset($fragments[$key]);
            }
        }
    }
    return $fragments;
}

// جلوگیری از نمایش notices در صفحات shop و product
add_action('woocommerce_before_single_product_summary', function() {
    wc_clear_notices();
}, 1);
add_action('woocommerce_before_shop_loop', function() {
    wc_clear_notices();
}, 1);
add_action('woocommerce_after_shop_loop_item', function() {
    wc_clear_notices();
}, 999);
add_action('woocommerce_after_add_to_cart_button', function() {
    wc_clear_notices();
}, 999);

// حذف notices از output در loop
add_action('woocommerce_shop_loop_item_title', function() {
    wc_clear_notices();
}, 999);
add_action('woocommerce_after_shop_loop_item_title', function() {
    wc_clear_notices();
}, 999);

// حذف notices از محصولات در صفحه اصلی
add_filter('woocommerce_loop_add_to_cart_link', function($link, $product) {
    wc_clear_notices();
    return $link;
}, 999, 2);

// جلوگیری کامل از تولید پیام "مشاهده سبد خرید" در WooCommerce - همه فیلترها
add_filter('wc_add_to_cart_message_html', '__return_empty_string', 999);
add_filter('woocommerce_add_to_cart_message', '__return_empty_string', 999);
add_filter('wc_add_to_cart_message_html', function($message, $products, $show_qty) {
    return '';
}, 999, 3);
add_filter('woocommerce_add_to_cart_message', function($message, $product_id) {
    return '';
}, 999, 2);
// حذف از AJAX response
add_filter('woocommerce_ajax_add_to_cart_response', function($response) {
    if (isset($response['message'])) {
        $response['message'] = '';
    }
    // حذف کامل لینک "مشاهده سبد خرید"
    if (isset($response['fragments'])) {
        foreach ($response['fragments'] as $key => $fragment) {
            if (is_string($fragment)) {
                // حذف همه لینک‌های wc-forward
                $response['fragments'][$key] = preg_replace(
                    '/<a[^>]*class="[^"]*wc-forward[^"]*"[^>]*>.*?<\/a>/is',
                    '',
                    $fragment
                );
                // حذف همه المنت‌های added_to_cart
                $response['fragments'][$key] = preg_replace(
                    '/<a[^>]*class="[^"]*added_to_cart[^"]*"[^>]*>.*?<\/a>/is',
                    '',
                    $response['fragments'][$key]
                );
            }
        }
    }
    return $response;
}, 999);

// جلوگیری از نمایش پیام در WooCommerce AJAX
add_filter('woocommerce_add_to_cart_message_html', '__return_empty_string', 999);
add_filter('wc_add_to_cart_message_html', '__return_empty_string', 999);

// حذف notices بعد از add to cart
add_action('woocommerce_add_to_cart', function() {
    wc_clear_notices();
}, 999);

// حذف پیام‌ها از fragments
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    // حذف پیام‌های cart از همه fragments
    foreach ($fragments as $key => $fragment) {
        if (is_string($fragment)) {
            // حذف تمام پیام‌های woocommerce-message و woocommerce-info
            $fragments[$key] = preg_replace(
                [
                    '/<div[^>]*class="[^"]*woocommerce-message[^"]*"[^>]*>.*?<\/div>/is',
                    '/<div[^>]*class="[^"]*woocommerce-info[^"]*"[^>]*>.*?<\/div>/is',
                    '/<li[^>]*class="[^"]*woocommerce-message[^"]*"[^>]*>.*?<\/li>/is',
                    '/<li[^>]*class="[^"]*woocommerce-info[^"]*"[^>]*>.*?<\/li>/is',
                    // حذف لینک "مشاهده سبد خرید"
                    '/<a[^>]*class="[^"]*wc-forward[^"]*"[^>]*>.*?<\/a>/is',
                    '/<a[^>]*class="[^"]*added_to_cart[^"]*"[^>]*>.*?<\/a>/is',
                    '/<a[^>]*href="[^"]*cart[^"]*"[^>]*class="[^"]*wc-forward[^"]*"[^>]*>.*?<\/a>/is',
                ],
                '',
                $fragment
            );
        }
    }
    return $fragments;
}, 999);

// حذف کامل پیام‌های WooCommerce بعد از add to cart
function khane_irani_remove_all_cart_messages() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    ?>
    <style>
        /* حذف کامل تمام پیام‌های add to cart - همه حالات ممکن */
        .woocommerce-message:has(a[href*="cart"]),
        .woocommerce-message:has(a[href*="سبد"]),
        .woocommerce-info:has(a[href*="cart"]),
        .woocommerce-info:has(a[href*="سبد"]),
        .woocommerce-message:has(.wc-forward),
        body.woocommerce-shop .woocommerce-message,
        body.woocommerce-shop .woocommerce-info,
        body.single-product .woocommerce-message,
        body.single-product .woocommerce-info,
        body.archive.woocommerce .woocommerce-message,
        body.archive.woocommerce .woocommerce-info,
        .woocommerce-loop .woocommerce-message,
        .products .woocommerce-message,
        .product .woocommerce-message,
        .ki-product-card .woocommerce-message,
        .ki-product-card .woocommerce-info,
        .woocommerce-message a[href*="cart"],
        .woocommerce-message a[href*="سبد"],
        .woocommerce-info a[href*="cart"],
        .woocommerce-info a[href*="سبد"],
        /* حذف از همه جاها */
        .woocommerce .woocommerce-message,
        .woocommerce .woocommerce-info,
        ul.products .woocommerce-message,
        ul.products .woocommerce-info,
        li.product .woocommerce-message,
        li.product .woocommerce-info,
        /* حذف پیام‌های کنار دکمه‌ها - تمام حالات ممکن */
        .add_to_cart_button + .woocommerce-message,
        .add_to_cart_button ~ .woocommerce-message,
        .add_to_cart_button + * + .woocommerce-message,
        .ki-cart-btn + .woocommerce-message,
        .ki-cart-btn ~ .woocommerce-message,
        .ki-cart-btn + * + .woocommerce-message,
        .add_to_cart_button + .woocommerce-info,
        .add_to_cart_button ~ .woocommerce-info,
        .add_to_cart_button + * + .woocommerce-info,
        .ki-cart-btn + .woocommerce-info,
        .ki-cart-btn ~ .woocommerce-info,
        .ki-cart-btn + * + .woocommerce-info,
        /* حذف از داخل کارت‌های محصول */
        .ki-product-card .woocommerce-message,
        .ki-product-card .woocommerce-info,
        .ki-product-actions + .woocommerce-message,
        .ki-product-actions ~ .woocommerce-message,
        .ki-product-actions + * + .woocommerce-message,
        .ki-product-actions + .woocommerce-info,
        .ki-product-actions ~ .woocommerce-info,
        .ki-product-actions + * + .woocommerce-info,
        li.product .woocommerce-message,
        li.product .woocommerce-info,
        /* حذف همه پیام‌هایی که شامل لینک cart هستند */
        .woocommerce-message a.wc-forward,
        .woocommerce-info a.wc-forward,
        /* حذف از کنار container دکمه‌ها */
        .ki-product-actions-container + .woocommerce-message,
        .ki-product-actions-container ~ .woocommerce-message,
        /* حذف از همه المنت‌های sibling */
        .ki-product-card > * + .woocommerce-message,
        .ki-product-card > * + .woocommerce-info {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            font-size: 0 !important;
            line-height: 0 !important;
            pointer-events: none !important;
        }
    </style>
    <script>
    (function($) {
        'use strict';
        
        function removeAllCartMessages() {
            // حذف همه پیام‌های مربوط به سبد خرید
            $('.woocommerce-message, .woocommerce-info').each(function() {
                var $msg = $(this);
                var text = $msg.text() || '';
                var html = $msg.html() || '';
                
                // چک کردن متن و HTML - حذف همه پیام‌های مربوط به cart
                if (text.indexOf('مشاهده سبد خرید') !== -1 || 
                    text.indexOf('View cart') !== -1 ||
                    text.indexOf('سبد خرید') !== -1 ||
                    text.indexOf('cart') !== -1 ||
                    html.indexOf('cart') !== -1 ||
                    html.indexOf('سبد') !== -1 ||
                    $msg.find('a[href*="cart"]').length > 0 ||
                    $msg.find('a[href*="سبد"]').length > 0 ||
                    $msg.closest('.products').length > 0 ||
                    $msg.closest('.product').length > 0 ||
                    $msg.closest('.ki-product-card').length > 0 ||
                    $msg.closest('.woocommerce-loop').length > 0) {
                    $msg.remove();
                }
            });
            
            // حذف از همه المنت‌های محصول
            $('.products .woocommerce-message, .product .woocommerce-message, .ki-product-card .woocommerce-message, ul.products .woocommerce-message, li.product .woocommerce-message').remove();
            
            // حذف از کنار آیکون‌ها - همه حالات ممکن
            $('.ki-product-actions').nextAll('.woocommerce-message, .woocommerce-info').remove();
            $('.ki-product-actions').siblings('.woocommerce-message, .woocommerce-info').remove();
            $('.ki-cart-btn').nextAll('.woocommerce-message, .woocommerce-info').remove();
            $('.ki-cart-btn').siblings('.woocommerce-message, .woocommerce-info').remove();
            $('.add_to_cart_button').nextAll('.woocommerce-message, .woocommerce-info').remove();
            $('.add_to_cart_button').siblings('.woocommerce-message, .woocommerce-info').remove();
            
            // حذف از داخل container آیکون‌ها
            $('.ki-product-actions').find('.woocommerce-message, .woocommerce-info').remove();
            $('.ki-product-info').find('.woocommerce-message, .woocommerce-info').not('.ki-product-notification').remove();
        }
        
        // حذف فوری
        removeAllCartMessages();
        
        // بعد از add to cart
        $(document.body).on('added_to_cart', function() {
            setTimeout(function() {
                removeAllCartMessages();
            }, 50);
        });
        
        // بعد از fragments refresh
        $(document.body).on('updated_wc_div wc_fragments_refreshed', function() {
            setTimeout(function() {
                removeAllCartMessages();
            }, 50);
        });
        
        // MutationObserver برای حذف فوری پیام‌های جدید
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        if (node.nodeType === 1 && node.classList) {
                            if (node.classList.contains('woocommerce-message') || 
                                node.classList.contains('woocommerce-info')) {
                                var text = node.textContent || '';
                                var html = node.innerHTML || '';
                                
                                if (text.indexOf('مشاهده سبد خرید') !== -1 || 
                                    text.indexOf('View cart') !== -1 ||
                                    text.indexOf('سبد خرید') !== -1 ||
                                    html.indexOf('cart') !== -1 ||
                                    html.indexOf('سبد') !== -1 ||
                                    $(node).find('a[href*="cart"]').length > 0 ||
                                    $(node).find('a[href*="سبد"]').length > 0) {
                                    $(node).remove();
                                }
                            }
                        }
                    }
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // حذف دوره‌ای
        setInterval(function() {
            removeAllCartMessages();
        }, 1000);
        
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'khane_irani_remove_all_cart_messages', 20);

/**
 * Add custom CSS for Elementor compatibility
 */
function khane_irani_elementor_css() {
    if (class_exists('\Elementor\Plugin')) {
        echo '<style>
            .elementor-widget-container { margin-bottom: 2rem; }
            .elementor-section { margin-bottom: 2rem; }
            .elementor-widget-heading .elementor-heading-title { color: var(--zs-teal-dark, #00A796); }
        </style>';
    }
}
add_action('wp_head', 'khane_irani_elementor_css');
/**
 * Checkout: move coupon form under payment methods
 * Remove default coupon toggle above the form
 */
// Remove default coupon toggle above checkout form (we render it in template under payment)
add_action('init', function(){
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
});


/**
 * Add theme support for custom background
 */
add_theme_support('custom-background', array(
    'default-color' => 'f8f9fa',
    'default-image' => '',
));

/**
 * Add theme support for custom header
 */
add_theme_support('custom-header', array(
    'default-image'      => '',
    'default-text-color' => '000000',
    'width'              => 1200,
    'height'             => 250,
    'flex-width'         => true,
    'flex-height'        => true,
));

/**
 * Add theme support for post formats
 */
add_theme_support('post-formats', array(
    'aside',
    'image',
    'video',
    'quote',
    'link',
    'gallery',
    'audio',
));

/**
 * Add theme support for selective refresh for widgets
 */
add_theme_support('customize-selective-refresh-widgets');

/**
 * Add theme support for responsive embeds
 */
add_theme_support('responsive-embeds');

/**
 * Add theme support for editor styles
 */
add_theme_support('editor-styles');
add_editor_style('editor-style.css');

/**
 * Add theme support for wide alignment
 */
add_theme_support('align-wide');

/**
 * Add theme support for custom units
 */
add_theme_support('custom-units');

/**
 * Add theme support for custom spacing
 */
add_theme_support('custom-spacing');

/**
 * Add theme support for custom line height
 */
add_theme_support('custom-line-height');

/**
 * Add theme support for experimental link color
 */
add_theme_support('experimental-link-color');

/**
 * Add theme support for custom font sizes
 */
add_theme_support('custom-font-size');

/**
 * Add theme support for custom color palette
 */
add_theme_support('editor-color-palette', array(
    array(
        'name'  => __('Primary', 'khane-irani'),
        'slug'  => 'primary',
        'color' => 'var(--zs-teal-medium, #37C3B3)',
    ),
    array(
        'name'  => __('Secondary', 'khane-irani'),
        'slug'  => 'secondary',
        'color' => 'var(--zs-teal-medium, #37C3B3)',
    ),
    array(
        'name'  => __('Dark', 'khane-irani'),
        'slug'  => 'dark',
        'color' => 'var(--zs-teal-dark, #00A796)',
    ),
    array(
        'name'  => __('Light', 'khane-irani'),
        'slug'  => 'light',
        'color' => '#f8f9fa',
    ),
));

/**
 * Add theme support for custom gradient presets
 */
add_theme_support('editor-gradient-presets', array(
    array(
        'name'     => __('Primary to Secondary', 'khane-irani'),
        'gradient' => 'linear-gradient(135deg, var(--zs-teal-medium, #37C3B3) 0%, var(--zs-teal-medium, #37C3B3) 100%)',
        'slug'     => 'primary-to-secondary',
    ),
    array(
        'name'     => __('Dark to Light', 'khane-irani'),
        'gradient' => 'linear-gradient(135deg, var(--zs-teal-dark, #00A796) 0%, #f8f9fa 100%)',
        'slug'     => 'dark-to-light',
    ),
));

/**
 * Add theme support for custom font sizes
 */
add_theme_support('editor-font-sizes', array(
    array(
        'name' => __('Small', 'khane-irani'),
        'size' => 14,
        'slug' => 'small',
    ),
    array(
        'name' => __('Normal', 'khane-irani'),
        'size' => 16,
        'slug' => 'normal',
    ),
    array(
        'name' => __('Medium', 'khane-irani'),
        'size' => 20,
        'slug' => 'medium',
    ),
    array(
        'name' => __('Large', 'khane-irani'),
        'size' => 24,
        'slug' => 'large',
    ),
    array(
        'name' => __('Extra Large', 'khane-irani'),
        'size' => 32,
        'slug' => 'extra-large',
    ),
));

/**
 * Helper functions for theme settings
 */

/**
 * Get theme setting value
 */
function khane_irani_get_setting($key, $default = '') {
    $options = get_option('khane-irani-settings', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * Get header settings
 */
function khane_irani_get_header_setting($key, $default = '') {
    return khane_irani_get_setting($key, $default);
}

/**
 * Get footer settings
 */
function khane_irani_get_footer_setting($key, $default = '') {
    return khane_irani_get_setting($key, $default);
}

/**
 * Get about settings
 */
function khane_irani_get_about_setting($key, $default = '') {
    return khane_irani_get_setting($key, $default);
}

/**
 * Get services settings
 */
function khane_irani_get_services_setting($key, $default = '') {
    return khane_irani_get_setting($key, $default);
}

/**
 * Get products settings
 */
function khane_irani_get_products_setting($key, $default = '') {
    return khane_irani_get_setting($key, $default);
}

/**
 * Get contact settings
 */
function khane_irani_get_contact_setting($key, $default = '') {
    return khane_irani_get_setting($key, $default);
}



/**
 * Custom Walker for Navigation Menu
 */
class khane_irani_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $submenu = ($depth > 0) ? ' sub-menu' : '';
        
        // Check if parent is mega menu
        $is_mega_menu = false;
        if (isset($this->current_item)) {
            $classes = empty($this->current_item->classes) ? array() : (array) $this->current_item->classes;
            $is_mega_menu = in_array('mega-menu', $classes) || in_array('mega-menu-item', $classes);
        }
        
        if ($is_mega_menu && $depth == 0) {
            // Mega menu styling will be handled by CSS
            $output .= "\n$indent<ul class=\"dropdown-menu mega-menu-container\">\n";
            // Add "View All" link at the top of mega menu
            $parent_url = !empty($this->current_item->url) ? esc_url($this->current_item->url) : '#';
            $output .= "\n$indent\t<li class=\"mega-menu-view-all\">\n";
            $output .= "\n$indent\t\t<a href=\"{$parent_url}\" class=\"mega-menu-view-all-link\">\n";
            $output .= "\n$indent\t\t\t<span>مشاهده همه محصولات</span>\n";
            $output .= "\n$indent\t\t\t<svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\" style=\"transform: rotate(180deg);\">\n";
            $output .= "\n$indent\t\t\t\t<path d=\"M9 18L15 12L9 6\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n";
            $output .= "\n$indent\t\t\t</svg>\n";
            $output .= "\n$indent\t\t</a>\n";
            $output .= "\n$indent\t</li>\n";
        } else {
            $output .= "\n$indent<ul class=\"dropdown-menu$submenu\">\n";
        }
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $li_attributes = '';
        $class_names = $value = '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $classes[] = 'nav-item';
        
        if ($args->walker->has_children) {
            $classes[] = 'dropdown';
        }
        
        // Check if mega menu is enabled (via CSS class or meta)
        $is_mega_menu = in_array('mega-menu', $classes) || 
                       in_array('mega-menu-item', $classes) ||
                       get_post_meta($item->ID, '_menu_item_mega_menu', true) === '1';
        
        if ($is_mega_menu) {
            $classes[] = 'mega-menu';
            // Get mega menu settings from meta
            $mega_columns = get_post_meta($item->ID, '_menu_item_mega_columns', true);
            if ($mega_columns && in_array($mega_columns, array('2', '3', '4'))) {
                $classes[] = 'mega-menu-' . $mega_columns . 'cols';
            }
            $mega_fullwidth = get_post_meta($item->ID, '_menu_item_mega_fullwidth', true);
            if ($mega_fullwidth === '1') {
                $classes[] = 'mega-menu-fullwidth';
            }
            $mega_image = get_post_meta($item->ID, '_menu_item_mega_image', true);
            if (!empty($mega_image)) {
                $classes[] = 'mega-menu-with-image';
            }
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = ' class="' . esc_attr($class_names) . '"';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';
        
        // Store current item for start_lvl
        $this->current_item = $item;
        
        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ' class="nav-link"';
        
        // Get icon from menu item meta or use default
        $icon = get_post_meta($item->ID, '_menu_item_icon', true);
        if (empty($icon)) {
            // Default icons based on menu item title
            $title = strtolower($item->title);
            if (strpos($title, 'سرویس') !== false) {
                $icon = '🎨';
            } elseif (strpos($title, 'راهبرد') !== false) {
                $icon = '📋';
            } elseif (strpos($title, 'نمونه') !== false) {
                $icon = '💼';
            } elseif (strpos($title, 'درباره') !== false) {
                $icon = 'ℹ️';
            } elseif (strpos($title, 'تماس') !== false) {
                $icon = '📞';
            } else {
            }
        }
        
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= '<span class="nav-icon">' . $icon . '</span>';
        $item_output .= '<span class="nav-text">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</span>';
        if ($args->walker->has_children) {
            $item_output .= ' <span class="caret"></span>';
        }
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
        $this->current_item = null;
    }
}

/**
 * AJAX Handler for Price Filter - فیلتر قیمت با ایجکس
 */
function khane_irani_filter_products_by_price_ajax() {
    // بررسی nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'price_filter_ajax_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    $min_price = isset($_POST['min_price']) && !empty($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
    $max_price = isset($_POST['max_price']) && !empty($_POST['max_price']) ? floatval($_POST['max_price']) : 0;
    
    // ساخت آرگومان‌های کوئری
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => get_option('posts_per_page', 12),
        'post_status' => 'publish',
    );
    
    // اضافه کردن فیلتر قیمت
    if ($min_price > 0 || $max_price > 0) {
        $args['meta_query'] = array(
            array(
                'key' => '_price',
                'value' => array($min_price, $max_price ? $max_price : 999999999),
                'type' => 'DECIMAL',
                'compare' => 'BETWEEN'
            )
        );
    }
    
    // حفظ فیلتر دسته‌بندی
    if (isset($_POST['category']) && !empty($_POST['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => intval($_POST['category'])
            )
        );
    }
    
    // حفظ مرتب‌سازی
    if (isset($_POST['orderby']) && !empty($_POST['orderby'])) {
        switch ($_POST['orderby']) {
            case 'price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'DESC';
                break;
            case 'date':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'popularity':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'total_sales';
                $args['order'] = 'DESC';
                break;
        }
    }
    
    $products = new WP_Query($args);
    
    ob_start();
    if ($products->have_posts()) {
        woocommerce_product_loop_start();
        
        while ($products->have_posts()) {
            $products->the_post();
            do_action('woocommerce_shop_loop');
            wc_get_template_part('content', 'product');
        }
        
        woocommerce_product_loop_end();
    } else {
        echo '<p style="text-align: center; padding: 40px; color: #6c757d;">هیچ محصولی یافت نشد.</p>';
    }
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_filter_products_by_price_ajax', 'khane_irani_filter_products_by_price_ajax');
add_action('wp_ajax_nopriv_filter_products_by_price_ajax', 'khane_irani_filter_products_by_price_ajax');

/**
 * Start session for comment captcha
 */
function khane_irani_start_session() {
    // Only start session if headers haven't been sent yet
    if (!headers_sent() && !session_id()) {
        session_start();
    }
}
add_action('init', 'khane_irani_start_session', 1);

/**
 * Validate comment captcha
 */
function khane_irani_validate_comment_captcha($approved, $commentdata) {
    if (is_user_logged_in()) {
        return $approved;
    }
    
    if (isset($_POST['comment_security']) && isset($_SESSION['comment_captcha'])) {
        $user_answer = intval($_POST['comment_security']);
        $correct_answer = intval($_SESSION['comment_captcha']);
        
        if ($user_answer !== $correct_answer) {
            wp_die('پاسخ پرسش امنیتی اشتباه است. لطفا دوباره تلاش کنید.', 'خطا در ارسال نظر', array('back_link' => true));
        }
        
        unset($_SESSION['comment_captcha']);
    } else {
        wp_die('لطفا پرسش امنیتی را پاسخ دهید.', 'خطا در ارسال نظر', array('back_link' => true));
    }
    
    return $approved;
}
add_filter('pre_comment_approved', 'khane_irani_validate_comment_captcha', 10, 2);

