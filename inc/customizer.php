<?php
/**
 * خانه ایرانی Theme Customizer
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function khane_irani_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport         = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport  = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('blogname', array(
            'selector'        => '.site-title a',
            'render_callback' => 'khane_irani_customize_partial_blogname',
        ));
        $wp_customize->selective_refresh->add_partial('blogdescription', array(
            'selector'        => '.site-description',
            'render_callback' => 'khane_irani_customize_partial_blogdescription',
        ));
    }

    // Colors Section
    $wp_customize->add_setting('primary_color', array(
        'default'           => 'var(--zs-teal-medium, #37C3B3)',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label'   => __('Primary Color', 'khane-irani'),
        'section' => 'colors',
    )));

    $wp_customize->add_setting('secondary_color', array(
        'default'           => 'var(--zs-teal-medium, #37C3B3)',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label'   => __('Secondary Color', 'khane-irani'),
        'section' => 'colors',
    )));

    $wp_customize->add_setting('accent_color', array(
        'default'           => '#27ae60',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'accent_color', array(
        'label'   => __('Accent Color', 'khane-irani'),
        'section' => 'colors',
    )));

    // Typography Section
    $wp_customize->add_section('typography_section', array(
        'title'    => __('Typography', 'khane-irani'),
        'priority' => 35,
    ));

    $wp_customize->add_setting('body_font_family', array(
        'default'           => 'IranYekan',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('body_font_family', array(
        'label'   => __('Body Font Family', 'khane-irani'),
        'section' => 'typography_section',
        'type'    => 'select',
        'choices' => array(
            'IranYekan'  => __('Iran Yekan (Local)', 'khane-irani'),
            'Tahoma'  => 'Tahoma',
            'Arial'   => 'Arial',
            'Helvetica' => 'Helvetica',
        ),
    ));

    $wp_customize->add_setting('heading_font_family', array(
        'default'           => 'IranYekan',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('heading_font_family', array(
        'label'   => __('Heading Font Family', 'khane-irani'),
        'section' => 'typography_section',
        'type'    => 'select',
        'choices' => array(
            'IranYekan'  => __('Iran Yekan (Local)', 'khane-irani'),
            'Tahoma'  => 'Tahoma',
            'Arial'   => 'Arial',
            'Helvetica' => 'Helvetica',
        ),
    ));

    // Layout Section
    $wp_customize->add_section('layout_section', array(
        'title'    => __('Layout', 'khane-irani'),
        'priority' => 40,
    ));

    $wp_customize->add_setting('container_width', array(
        'default'           => '1200',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('container_width', array(
        'label'   => __('Container Width (px)', 'khane-irani'),
        'section' => 'layout_section',
        'type'    => 'number',
        'input_attrs' => array(
            'min' => 800,
            'max' => 1600,
            'step' => 50,
        ),
    ));

    $wp_customize->add_setting('sidebar_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'khane_irani_sanitize_select',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('sidebar_position', array(
        'label'   => __('Sidebar Position', 'khane-irani'),
        'section' => 'layout_section',
        'type'    => 'select',
        'choices' => array(
            'left' => __('Left', 'khane-irani'),
            'right' => __('Right', 'khane-irani'),
            'none' => __('No Sidebar', 'khane-irani'),
        ),
    ));

    // Footer Section
    $wp_customize->add_section('footer_section', array(
        'title'    => __('Footer', 'khane-irani'),
        'priority' => 45,
    ));

    $wp_customize->add_setting('footer_text', array(
        'default'           => '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. تمامی حقوق محفوظ است.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('footer_text', array(
        'label'   => __('Footer Text', 'khane-irani'),
        'section' => 'footer_section',
        'type'    => 'textarea',
    ));

    $wp_customize->add_setting('show_social_links', array(
        'default'           => true,
        'sanitize_callback' => 'khane_irani_sanitize_checkbox',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('show_social_links', array(
        'label'   => __('Show Social Links', 'khane-irani'),
        'section' => 'footer_section',
        'type'    => 'checkbox',
    ));

    // Social Media Section
    $wp_customize->add_section('social_section', array(
        'title'    => __('Social Media', 'khane-irani'),
        'priority' => 50,
    ));

    $wp_customize->add_setting('facebook_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('facebook_url', array(
        'label'   => __('Facebook URL', 'khane-irani'),
        'section' => 'social_section',
        'type'    => 'url',
    ));

    $wp_customize->add_setting('instagram_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('instagram_url', array(
        'label'   => __('Instagram URL', 'khane-irani'),
        'section' => 'social_section',
        'type'    => 'url',
    ));

    $wp_customize->add_setting('telegram_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('telegram_url', array(
        'label'   => __('Telegram URL', 'khane-irani'),
        'section' => 'social_section',
        'type'    => 'url',
    ));

    $wp_customize->add_setting('whatsapp_number', array(
        'default'           => '09903491529',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('whatsapp_number', array(
        'label'   => __('WhatsApp Number', 'khane-irani'),
        'section' => 'social_section',
        'type'    => 'text',
    ));

    // WooCommerce Section
    if (class_exists('WooCommerce')) {
        $wp_customize->add_section('woocommerce_section', array(
            'title'    => __('WooCommerce', 'khane-irani'),
            'priority' => 55,
        ));

        $wp_customize->add_setting('products_per_page', array(
            'default'           => 12,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ));
        
        $wp_customize->add_control('products_per_page', array(
            'label'   => __('Products per Page', 'khane-irani'),
            'section' => 'woocommerce_section',
            'type'    => 'number',
            'input_attrs' => array(
                'min' => 4,
                'max' => 48,
                'step' => 4,
            ),
        ));

        $wp_customize->add_setting('show_product_rating', array(
            'default'           => true,
            'sanitize_callback' => 'khane_irani_sanitize_checkbox',
            'transport'         => 'postMessage',
        ));
        
        $wp_customize->add_control('show_product_rating', array(
            'label'   => __('Show Product Rating', 'khane-irani'),
            'section' => 'woocommerce_section',
            'type'    => 'checkbox',
        ));

        $wp_customize->add_setting('show_product_price', array(
            'default'           => true,
            'sanitize_callback' => 'khane_irani_sanitize_checkbox',
            'transport'         => 'postMessage',
        ));
        
        $wp_customize->add_control('show_product_price', array(
            'label'   => __('Show Product Price', 'khane-irani'),
            'section' => 'woocommerce_section',
            'type'    => 'checkbox',
        ));
    }
}
add_action('customize_register', 'khane_irani_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function khane_irani_customize_partial_blogname() {
    bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function khane_irani_customize_partial_blogdescription() {
    bloginfo('description');
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function khane_irani_customize_preview_js() {
    wp_enqueue_script('khane-irani-customizer', get_template_directory_uri() . '/js/customizer.js', array('customize-preview'), _S_VERSION, true);
}
add_action('customize_preview_init', 'khane_irani_customize_preview_js');

/**
 * Sanitize select
 */
function khane_irani_sanitize_select($input, $setting) {
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

/**
 * Sanitize checkbox
 */
function khane_irani_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Sanitize number
 */
function khane_irani_sanitize_number($number, $setting) {
    $number = absint($number);
    return ($number ? $number : $setting->default);
}

/**
 * Sanitize text
 */
function khane_irani_sanitize_text($text) {
    return sanitize_text_field($text);
}

/**
 * Sanitize textarea
 */
function khane_irani_sanitize_textarea($text) {
    return sanitize_textarea_field($text);
}

/**
 * Sanitize URL
 */
function khane_irani_sanitize_url($url) {
    return esc_url_raw($url);
}

/**
 * Sanitize email
 */
function khane_irani_sanitize_email($email) {
    return sanitize_email($email);
}

/**
 * Sanitize hex color
 */
function khane_irani_sanitize_hex_color($color) {
    if ('' === $color) {
        return '';
    }

    // 3 or 6 hex digits, or the empty string.
    if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color)) {
        return $color;
    }

    return '';
}

/**
 * Sanitize image
 */
function khane_irani_sanitize_image($image, $setting) {
    $mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          => 'image/gif',
        'png'          => 'image/png',
        'bmp'          => 'image/bmp',
        'tif|tiff'     => 'image/tiff',
        'ico'          => 'image/x-icon'
    );

    // Return an array with file extension and mime_type.
    $file = wp_check_filetype($image, $mimes);

    // If $image has a valid mime_type, return it; otherwise, return the default.
    return ($file['ext'] ? $image : $setting->default);
}
