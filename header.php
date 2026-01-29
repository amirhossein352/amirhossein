<?php
$header_logo_id      = khane_irani_get_setting('header_logo', '');
$header_logo_width   = absint(khane_irani_get_setting('header_logo_width', '150'));
$header_logo_height  = absint(khane_irani_get_setting('header_logo_height', '60'));
$header_phone_raw    = khane_irani_get_setting('header_phone', '');
$header_email_raw    = khane_irani_get_setting('header_email', '');
$header_address_raw  = khane_irani_get_setting('header_address', '');
$show_search         = khane_irani_get_setting('show_search', '1');
$header_bg_color     = khane_irani_get_setting('header_bg_color', '#ffffff');
$header_text_color   = khane_irani_get_setting('header_text_color', '#333333');
$sticky_header       = khane_irani_get_setting('sticky_header', '1');

$site_name    = get_bloginfo('name');
$site_tagline = $header_address_raw ? sanitize_textarea_field($header_address_raw) : get_bloginfo('description');
$site_url     = home_url('/');

$header_classes = array('site-header');
if ('1' !== $sticky_header) {
    $header_classes[] = 'header-static';
}

$header_style_parts = array();
if (!empty($header_bg_color)) {
    $header_style_parts[] = '--zs-header-bg:' . $header_bg_color;
    $header_style_parts[] = 'background-color:' . $header_bg_color;
}
if (!empty($header_text_color)) {
    $header_style_parts[] = '--zs-header-text:' . $header_text_color;
    $header_style_parts[] = '--zs-nav-text:' . $header_text_color;
    $header_style_parts[] = 'color:' . $header_text_color;
}
$header_style_attr = '';
if (!empty($header_style_parts)) {
    $header_style_attr = ' style="' . esc_attr(implode(';', $header_style_parts)) . '"';
}

$body_extra_classes = array();
if ('1' !== $sticky_header) {
    $body_extra_classes[] = 'no-sticky-header';
}

$logo_html = '';
if ($header_logo_id) {
    $logo_styles = array();
    if (!empty($header_logo_width)) {
        $logo_styles[] = 'max-width:' . $header_logo_width . 'px';
    }
    if (!empty($header_logo_height)) {
        $logo_styles[] = 'max-height:' . $header_logo_height . 'px';
    }
    
    // Get responsive image attributes
    $logo_img_attrs = khane_irani_get_logo_image_attributes($header_logo_id);
    
    // Build attributes array
    $logo_attrs = array('class' => 'header-logo-image');
    if (!empty($logo_styles)) {
        $logo_attrs['style'] = implode(';', $logo_styles);
    }
    
    // Merge responsive attributes
    $logo_attrs = array_merge($logo_attrs, $logo_img_attrs);
    
    // Use appropriate size for logo - prefer smaller sizes
    $logo_size = 'logo-standard';
    
    // Try to get a smaller size first
    $logo_thumbnail_url = wp_get_attachment_image_url($header_logo_id, 'thumbnail');
    $logo_medium_url = wp_get_attachment_image_url($header_logo_id, 'medium');
    $logo_custom_url = wp_get_attachment_image_url($header_logo_id, $logo_size);
    
    // Use the smallest available size that's still good quality
    $preferred_logo_url = $logo_custom_url ?: ($logo_medium_url ?: $logo_thumbnail_url);
    
    if (!empty($logo_img_attrs['src']) && $preferred_logo_url) {
        // Override src with preferred smaller size
        $logo_img_attrs['src'] = $preferred_logo_url;
        
        // Use custom HTML with responsive attributes
        $logo_src = esc_url($logo_img_attrs['src']);
        $logo_srcset = !empty($logo_img_attrs['srcset']) ? ' srcset="' . esc_attr($logo_img_attrs['srcset']) . '"' : '';
        $logo_width = !empty($logo_img_attrs['width']) ? ' width="' . esc_attr($logo_img_attrs['width']) . '"' : '';
        $logo_height = !empty($logo_img_attrs['height']) ? ' height="' . esc_attr($logo_img_attrs['height']) . '"' : '';
        $logo_loading = !empty($logo_img_attrs['loading']) ? ' loading="' . esc_attr($logo_img_attrs['loading']) . '"' : '';
        $logo_decoding = !empty($logo_img_attrs['decoding']) ? ' decoding="' . esc_attr($logo_img_attrs['decoding']) . '"' : '';
        $logo_fetchpriority = !empty($logo_img_attrs['fetchpriority']) ? ' fetchpriority="' . esc_attr($logo_img_attrs['fetchpriority']) . '"' : '';
        $logo_style = !empty($logo_attrs['style']) ? ' style="' . esc_attr($logo_attrs['style']) . '"' : '';
        
        $logo_html = '<img src="' . $logo_src . '"' 
                   . $logo_srcset 
                   . $logo_width 
                   . $logo_height 
                   . ' class="' . esc_attr($logo_attrs['class']) . '"'
                   . $logo_loading 
                   . $logo_decoding 
                   . $logo_fetchpriority
                   . $logo_style
                   . ' alt="' . esc_attr(get_bloginfo('name')) . '">';
    } else {
        // Fallback to standard WordPress function with smaller size
        $fallback_size = $logo_medium_url ? 'medium' : ($logo_thumbnail_url ? 'thumbnail' : $logo_size);
        $logo_html = wp_get_attachment_image($header_logo_id, $fallback_size, false, $logo_attrs);
    }
}

$header_phone_display = $header_phone_raw ? sanitize_text_field($header_phone_raw) : '';
$header_phone_href    = $header_phone_display ? preg_replace('/[^0-9\+\#\*]/', '', $header_phone_display) : '';
$header_email_display = $header_email_raw ? sanitize_email($header_email_raw) : '';

$search_query = get_search_query();
$selected_product_cat = isset($_GET['product_cat']) ? sanitize_text_field(wp_unslash($_GET['product_cat'])) : '';
$product_categories = array();
if (class_exists('WooCommerce')) {
    $product_categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'number'     => 12,
    ));
}

$header_data_attrs = ' data-sticky="' . ('1' === $sticky_header ? 'true' : 'false') . '"';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Critical CSS to prevent CLS on mobile -->
    <style>
        /* Prevent CLS - Set mobile padding immediately */
        @media (max-width: 768px) {
            body {
                padding-top: 110px !important;
                min-height: calc(100vh + 110px) !important;
            }
            .site-header {
                min-height: auto !important;
                max-height: none !important;
                height: auto !important;
            }
        }
        @media (max-width: 480px) {
            body {
                padding-top: 100px !important;
                min-height: calc(100vh + 100px) !important;
            }
            .site-header {
                min-height: auto !important;
                max-height: none !important;
                height: auto !important;
            }
        }
        /* Prevent font-related CLS */
        html, body {
            font-family: 'IranYekan', 'Tahoma', sans-serif !important;
            font-size: 16px !important;
        }
        /* Prevent CLS for header elements */
        .header-auth {
            min-width: 120px !important;
            min-height: 40px !important;
        }
        .btn-auth {
            min-width: 40px !important;
            min-height: 40px !important;
        }
        .header-search {
            min-height: 40px !important;
        }
        .search-input {
            min-height: 40px !important;
            height: 40px !important;
        }
        /* Critical CSS for Banner Slider - Prevent CLS */
        .banner-slider-section {
            width: 100%;
            position: relative;
            padding: 28px 0 18px;
        }
        .banner-slider-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0;
        }
        .banner-slider-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 15px;
        }
        .banner-slider.swiper {
            width: 100%;
            height: auto;
            aspect-ratio: 1920 / 488;
            min-height: 300px;
            max-height: 600px;
        }
        .banner-slide {
            position: relative;
            height: auto;
            aspect-ratio: 1920 / 488;
            overflow: hidden;
            display: flex;
            align-items: stretch;
        }
        .banner-slide-link {
            display: block;
            width: 100%;
            height: 100%;
            position: relative;
        }
        .banner-slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        @media (max-width: 768px) {
            .banner-slider-section {
                padding: 6px 0 14px !important;
                margin-top: 0 !important;
            }
            .banner-slider.swiper {
                height: auto !important;
                aspect-ratio: 525 / 263 !important;
                min-height: 200px !important;
                max-height: 400px !important;
            }
            .banner-slide {
                height: auto !important;
                aspect-ratio: 525 / 263 !important;
            }
            .banner-slide-image {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center !important;
                display: block !important;
            }
        }
        /* Prevent CLS for main content */
        .site-main {
            min-height: 200px;
            contain: layout style;
            position: relative;
        }
        body.home .site-main {
            min-height: 800px;
        }
        /* Prevent CLS for mobile menu toggle */
        .mobile-menu-toggle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            min-height: 36px !important;
            padding: 0.5rem !important;
            box-sizing: border-box !important;
            contain: layout style !important;
        }
        .mobile-menu-toggle .hamburger-line {
            width: 20px;
            height: 2px;
            min-width: 20px;
            min-height: 2px;
            display: block;
            contain: layout style;
        }
    </style>
    
    <!-- Load jQuery FIRST before everything else -->
    <script src="<?php echo includes_url('/js/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo includes_url('/js/jquery/jquery-migrate.min.js'); ?>"></script>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class($body_extra_classes); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'khane-irani'); ?></a>

    <header id="masthead" class="<?php echo esc_attr(implode(' ', $header_classes)); ?>"<?php echo $header_style_attr; ?><?php echo $header_data_attrs; ?>>
        <div class="container">
            <div class="header-content">
                <!-- خط اول: لوگو + دکمه ورود/ثبت نام -->
                <div class="header-top">
                    <div class="header-left">
                        <div class="site-branding">
                            <div class="logo-container <?php echo esc_attr($logo_html ? 'has-logo-image' : 'no-logo-image'); ?>">
                                <a class="logo-link" href="<?php echo esc_url($site_url); ?>" rel="home">
                                    <?php if ($logo_html) : ?>
                                        <span class="logo-image"><?php echo wp_kses_post($logo_html); ?></span>
                                    <?php else : ?>
                                        <span class="logo-symbol" aria-hidden="true"></span>
                                        <span class="logo-text"><?php echo esc_html($site_name); ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="header-auth">
                        <?php if (is_user_logged_in()) : ?>
                            <?php 
                            $current_user = wp_get_current_user();
                            $account_url = class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : admin_url('profile.php');
                            ?>
                            <a href="<?php echo esc_url($account_url); ?>" class="btn-auth btn-primary">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <span><?php echo esc_html($current_user->display_name); ?></span>
                            </a>
                        <?php else : ?>
                            <?php
                            // Use modal login from WooCommerce Phone Auth plugin
                            // The plugin's JavaScript will handle the modal opening
                            ?>
                            <button type="button" class="btn-auth btn-primary wcpa-header-login-btn" id="header-login-btn" data-fallback-url="<?php echo esc_attr(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : wp_login_url()); ?>">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                <span>ورود / ثبت نام</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- خط دوم: منو (دسکتاپ) + جستجو + سبد خرید -->
                <div class="header-middle">
                    <!-- منو - در دسکتاپ نمایش داده می‌شود -->
                    <nav class="main-navigation desktop-nav">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'nav-menu',
                            'container'      => false,
                            'fallback_cb'    => 'khane_irani_fallback_menu',
                            'walker'         => new khane_irani_Walker_Nav_Menu(),
                        ));
                        ?>
                    </nav>

                    <?php if ('1' === $show_search) : ?>
                        <div class="header-center">
                            <div class="header-search">
                                <form role="search" method="get" class="header-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                                    <div class="search-field-wrapper">
                                        <span class="search-icon" aria-hidden="true">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4.5C7.41015 4.5 4.5 7.41015 4.5 11C4.5 14.5899 7.41015 17.5 11 17.5C12.7941 17.5 14.4297 16.7888 15.6533 15.6271L19.3137 19.2875C19.7043 19.678 20.3374 19.678 20.7279 19.2875C21.1185 18.8969 21.1185 18.2638 20.7279 17.8733L17.0675 14.2129C18.2292 12.9893 18.9404 11.3537 18.9404 9.55957C18.9404 5.96973 16.0303 3.05957 12.4404 3.05957C8.85059 3.05957 5.94043 5.96973 5.94043 9.55957C5.94043 13.1494 8.85059 16.0596 12.4404 16.0596C14.2346 16.0596 15.8701 15.3483 17.0938 14.1865L20.7542 17.8469" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <input type="search" class="search-input" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="به دنبال چه محصولی هستید؟" />
                                    </div>
                                    <button type="submit" class="search-submit-btn">
                                        <span>جستجو</span>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="header-actions">
                        <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </button>

                        <?php if ($header_phone_display || $header_email_display) : ?>
                            <div class="header-contact">
                                <?php if ($header_phone_display) : ?>
                                    <?php if ($header_phone_href) : ?>
                                        <a class="header-contact-item" href="<?php echo esc_url('tel:' . $header_phone_href); ?>">
                                            <span class="contact-icon" aria-hidden="true">📞</span>
                                            <span class="contact-text"><?php echo esc_html($header_phone_display); ?></span>
                                        </a>
                                    <?php else : ?>
                                        <span class="header-contact-item">
                                            <span class="contact-icon" aria-hidden="true">📞</span>
                                            <span class="contact-text"><?php echo esc_html($header_phone_display); ?></span>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($header_email_display) : ?>
                                    <a class="header-contact-item" href="<?php echo esc_url('mailto:' . $header_email_display); ?>">
                                        <span class="contact-icon" aria-hidden="true">✉️</span>
                                        <span class="contact-text"><?php echo esc_html($header_email_display); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="header-buttons">
                            <div class="zs-header-cart" id="zs-header-cart" style="position: relative; display: inline-block; margin-left: 12px;">
                                <button type="button" id="zs-cart-btn" aria-label="سبد خرید" style="background: none; border: none; cursor: pointer; padding: 10px; position: relative;">
                                    <span class="zs-cart-icon" aria-hidden="true" style="font-size: 22px;">🛒</span>
                                    <span class="zs-header-cart-count" id="zs-header-cart-count" style="position: absolute; top: 4px; right: 4px; background: #dc3545; color: #fff; border-radius: 999px; min-width: 18px; height: 18px; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; padding: 0 5px; font-weight: 700;">0</span>
                                </button>

                                <div class="zs-cart-sidebar" id="zs-cart-sidebar" style="position: fixed; top: 0; right: -420px; width: 380px; height: 100vh; background: #fff; box-shadow: -6px 0 18px rgba(0,0,0,.1); z-index: 100000; transition: right .3s ease; display: flex; flex-direction: column;">
                                    <div class="zs-cart-header" style="padding: 14px 16px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
                                        <h3 style="margin: 0; font-size: 16px; color: #333;">سبد خرید</h3>
                                        <button type="button" id="zs-cart-close" aria-label="بستن" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #666;">×</button>
                                    </div>
                                    <div id="zs-cart-items" style="padding: 16px; flex: 1 1 auto; overflow-y: auto;"></div>
                                    <div class="zs-cart-footer" style="padding: 16px; background: #f8f9fa; border-top: 1px solid #eee; flex: 0 0 auto;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; font-weight: 800; margin-bottom: 12px; font-size: 15px;">
                                            <span>مجموع</span>
                                            <span id="zs-cart-total">0</span>
                                        </div>
                                        <div class="zs-cart-footer-actions" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="zs-cart-footer-btn zs-cart-footer-btn-cart" style="display: inline-block; text-align: center; background: #e9ecef; color: #212529; padding: 12px 10px; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 14px;">سبد خرید</a>
                                            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="zs-cart-footer-btn zs-cart-footer-btn-checkout" style="display: inline-block; text-align: center; background: #28a745; color: #fff; padding: 12px 10px; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 14px;">پرداخت</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- WooCommerce Notices Container - بالای صفحه -->
    <div id="wc-top-notices" class="wc-top-notices-container">
        <div class="container"></div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>

    <!-- Mobile Menu Panel -->
    <div class="mobile-menu-panel" id="mobile-menu-panel">
        <div class="mobile-menu-header">
            <button class="mobile-menu-close" id="mobile-menu-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-menu-content">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'menu-1',
                'menu_id'        => 'mobile-primary-menu',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => 'khane_irani_fallback_menu',
                'walker'         => new khane_irani_Walker_Nav_Menu(),
            ));
            ?>
        </div>
        
        <div class="mobile-menu-buttons">
            <?php if ($header_phone_href) : ?>
                <a href="<?php echo esc_url('tel:' . $header_phone_href); ?>" class="btn-secondary"><?php echo esc_html__('تماس:', 'khane-irani'); ?> <?php echo esc_html($header_phone_display); ?></a>
            <?php elseif ($header_email_display) : ?>
                <a href="<?php echo esc_url('mailto:' . $header_email_display); ?>" class="btn-secondary"><?php echo esc_html__('ارسال ایمیل', 'khane-irani'); ?></a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-secondary"><?php echo esc_html__('تماس با ما', 'khane-irani'); ?></a>
            <?php endif; ?>
        </div>

        <?php if ($header_phone_display || $header_email_display || !empty($site_tagline)) : ?>
            <div class="mobile-contact-info">
                <?php if ($header_phone_display) : ?>
                    <?php if ($header_phone_href) : ?>
                        <a href="<?php echo esc_url('tel:' . $header_phone_href); ?>">
                            <span class="contact-icon" aria-hidden="true">📞</span>
                            <span class="contact-text"><?php echo esc_html($header_phone_display); ?></span>
                        </a>
                    <?php else : ?>
                        <span>
                            <span class="contact-icon" aria-hidden="true">📞</span>
                            <span class="contact-text"><?php echo esc_html($header_phone_display); ?></span>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($header_email_display) : ?>
                    <a href="<?php echo esc_url('mailto:' . $header_email_display); ?>">
                        <span class="contact-icon" aria-hidden="true">✉️</span>
                        <span class="contact-text"><?php echo esc_html($header_email_display); ?></span>
                    </a>
                <?php endif; ?>

                <?php if (!empty($site_tagline)) : ?>
                    <span>
                        <span class="contact-icon" aria-hidden="true">📍</span>
                        <span class="contact-text"><?php echo esc_html($site_tagline); ?></span>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>

