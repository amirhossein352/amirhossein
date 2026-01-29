<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function khane_irani_body_classes($classes) {
    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if (!is_active_sidebar('sidebar-1')) {
        $classes[] = 'no-sidebar';
    }
    
    // Add WooCommerce specific classes
    if (class_exists('WooCommerce') && is_woocommerce()) {
        $classes[] = 'woocommerce-page';
    }
    
    // Add singular class for single pages/posts
    if (is_singular()) {
        $classes[] = 'singular';
    }

    return $classes;
}
add_filter('body_class', 'khane_irani_body_classes');

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function khane_irani_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'khane_irani_pingback_header');

/**
 * Change the excerpt more string
 */
// function khane_irani_excerpt_more($more) {
//     return '...';
// }
// add_filter('excerpt_more', 'khane_irani_excerpt_more');

/**
 * Change the excerpt length
 */
// function khane_irani_excerpt_length($length) {
//     return 20;
// }
// add_filter('excerpt_length', 'khane_irani_excerpt_length', 999);

/**
 * Add custom image sizes
 */
function khane_irani_image_sizes() {
    add_image_size('hero-image', 1200, 600, true);
    add_image_size('product-thumbnail', 300, 300, true);
    add_image_size('blog-thumbnail', 400, 250, true);
}
add_action('after_setup_theme', 'khane_irani_image_sizes');

/**
 * Add custom logo support
 */
function khane_irani_custom_logo_setup() {
    $defaults = array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    );
    add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', 'khane_irani_custom_logo_setup');

/**
 * Add custom header support
 */
function khane_irani_custom_header_setup() {
    $defaults = array(
        'default-image'      => '',
        'default-text-color' => '000000',
        'width'              => 1200,
        'height'             => 250,
        'flex-width'         => true,
        'flex-height'        => true,
    );
    add_theme_support('custom-header', $defaults);
}
add_action('after_setup_theme', 'khane_irani_custom_header_setup');

/**
 * Add custom background support
 */
function khane_irani_custom_background_setup() {
    $defaults = array(
        'default-color' => 'f8f9fa',
        'default-image' => '',
    );
    add_theme_support('custom-background', $defaults);
}
add_action('after_setup_theme', 'khane_irani_custom_background_setup');

/**
 * Add post format support
 */
function khane_irani_post_formats() {
    add_theme_support('post-formats', array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio',
    ));
}
add_action('after_setup_theme', 'khane_irani_post_formats');

/**
 * Add HTML5 support
 */
function khane_irani_html5_support() {
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
}
add_action('after_setup_theme', 'khane_irani_html5_support');

/**
 * Add selective refresh support
 */
function khane_irani_selective_refresh() {
    add_theme_support('customize-selective-refresh-widgets');
}
add_action('after_setup_theme', 'khane_irani_selective_refresh');

/**
 * Add responsive embeds support
 */
function khane_irani_responsive_embeds() {
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'khane_irani_responsive_embeds');

/**
 * Add editor styles support
 */
function khane_irani_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'khane_irani_editor_styles');

/**
 * Add wide alignment support
 */
function khane_irani_wide_alignment() {
    add_theme_support('align-wide');
}
add_action('after_setup_theme', 'khane_irani_wide_alignment');

/**
 * Add custom units support
 */
function khane_irani_custom_units() {
    add_theme_support('custom-units');
}
add_action('after_setup_theme', 'khane_irani_custom_units');

/**
 * Add custom spacing support
 */
function khane_irani_custom_spacing() {
    add_theme_support('custom-spacing');
}
add_action('after_setup_theme', 'khane_irani_custom_spacing');

/**
 * Add custom line height support
 */
function khane_irani_custom_line_height() {
    add_theme_support('custom-line-height');
}
add_action('after_setup_theme', 'khane_irani_custom_line_height');

/**
 * Add experimental link color support
 */
function khane_irani_experimental_link_color() {
    add_theme_support('experimental-link-color');
}
add_action('after_setup_theme', 'khane_irani_experimental_link_color');

/**
 * Add custom font size support
 */
function khane_irani_custom_font_size() {
    add_theme_support('custom-font-size');
}
add_action('after_setup_theme', 'khane_irani_custom_font_size');

/**
 * Add custom color palette support
 */
function khane_irani_custom_color_palette() {
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
}
add_action('after_setup_theme', 'khane_irani_custom_color_palette');

/**
 * Add custom gradient presets support
 */
function khane_irani_custom_gradient_presets() {
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
}
add_action('after_setup_theme', 'khane_irani_custom_gradient_presets');

/**
 * Add custom font sizes support
 */
function khane_irani_custom_font_sizes() {
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
}
add_action('after_setup_theme', 'khane_irani_custom_font_sizes');

/**
 * Add custom image sizes
 */
function khane_irani_custom_image_sizes() {
    add_image_size('hero-image', 1200, 600, true);
    add_image_size('product-thumbnail', 300, 300, true);
    add_image_size('blog-thumbnail', 400, 250, true);
}
add_action('after_setup_theme', 'khane_irani_custom_image_sizes');

/**
 * Add custom logo support
 */
function khane_irani_custom_logo() {
    $defaults = array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    );
    add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', 'khane_irani_custom_logo');

/**
 * Add custom header support
 */
function khane_irani_custom_header() {
    $defaults = array(
        'default-image'      => '',
        'default-text-color' => '000000',
        'width'              => 1200,
        'height'             => 250,
        'flex-width'         => true,
        'flex-height'        => true,
    );
    add_theme_support('custom-header', $defaults);
}
add_action('after_setup_theme', 'khane_irani_custom_header');

/**
 * Add custom background support
 */
function khane_irani_custom_background() {
    $defaults = array(
        'default-color' => 'f8f9fa',
        'default-image' => '',
    );
    add_theme_support('custom-background', $defaults);
}
add_action('after_setup_theme', 'khane_irani_custom_background');

/**
 * Add post format support
 */
function khane_irani_post_formats_support() {
    add_theme_support('post-formats', array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio',
    ));
}
add_action('after_setup_theme', 'khane_irani_post_formats_support');

/**
 * Add HTML5 support
 */
function khane_irani_html5_support_setup() {
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
}
add_action('after_setup_theme', 'khane_irani_html5_support_setup');

/**
 * Add selective refresh support
 */
function khane_irani_selective_refresh_setup() {
    add_theme_support('customize-selective-refresh-widgets');
}
add_action('after_setup_theme', 'khane_irani_selective_refresh_setup');

/**
 * Add responsive embeds support
 */
function khane_irani_responsive_embeds_setup() {
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'khane_irani_responsive_embeds_setup');

/**
 * Add editor styles support
 */
function khane_irani_editor_styles_setup() {
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'khane_irani_editor_styles_setup');

/**
 * Add wide alignment support
 */
function khane_irani_wide_alignment_setup() {
    add_theme_support('align-wide');
}
add_action('after_setup_theme', 'khane_irani_wide_alignment_setup');

/**
 * Add custom units support
 */
function khane_irani_custom_units_setup() {
    add_theme_support('custom-units');
}
add_action('after_setup_theme', 'khane_irani_custom_units_setup');

/**
 * Add custom spacing support
 */
function khane_irani_custom_spacing_setup() {
    add_theme_support('custom-spacing');
}
add_action('after_setup_theme', 'khane_irani_custom_spacing_setup');

/**
 * Add custom line height support
 */
function khane_irani_custom_line_height_setup() {
    add_theme_support('custom-line-height');
}
add_action('after_setup_theme', 'khane_irani_custom_line_height_setup');

/**
 * Add experimental link color support
 */
function khane_irani_experimental_link_color_setup() {
    add_theme_support('experimental-link-color');
}
add_action('after_setup_theme', 'khane_irani_experimental_link_color_setup');

/**
 * Add custom font size support
 */
function khane_irani_custom_font_size_setup() {
    add_theme_support('custom-font-size');
}
add_action('after_setup_theme', 'khane_irani_custom_font_size_setup');

/**
 * Add custom color palette support
 */
function khane_irani_custom_color_palette_setup() {
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
}
add_action('after_setup_theme', 'khane_irani_custom_color_palette_setup');

/**
 * Add custom gradient presets support
 */
function khane_irani_custom_gradient_presets_setup() {
    add_theme_support('editor-gradient-presets', array(
        array(
            'name'     => __('Primary to Secondary', 'khane-irani'),
            'slug'     => 'primary-to-secondary',
            'gradient' => 'linear-gradient(135deg, var(--zs-teal-medium, #37C3B3) 0%, var(--zs-teal-medium, #37C3B3) 100%)',
        ),
        array(
            'name'     => __('Dark to Light', 'khane-irani'),
            'slug'     => 'dark-to-light',
            'gradient' => 'linear-gradient(135deg, var(--zs-teal-dark, #00A796) 0%, #f8f9fa 100%)',
        ),
    ));
}
add_action('after_setup_theme', 'khane_irani_custom_gradient_presets_setup');

/**
 * Add custom font sizes support
 */
function khane_irani_custom_font_sizes_setup() {
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
}
add_action('after_setup_theme', 'khane_irani_custom_font_sizes_setup');
