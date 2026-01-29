<?php
/**
 * WooCommerce shop/archive customizations and template overrides
 * @package khane_irani
 */

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

if (!function_exists('khane_irani_dequeue_woocommerce_styles')) {
function khane_irani_dequeue_woocommerce_styles() {
    wp_dequeue_style('woocommerce-general');
    wp_dequeue_style('woocommerce-layout');
    wp_dequeue_style('woocommerce-smallscreen');
    wp_dequeue_style('woocommerce_frontend_styles');
    wp_dequeue_style('woocommerce_fancybox_styles');
    wp_dequeue_style('woocommerce_chosen_styles');
    wp_dequeue_style('woocommerce_prettyPhoto_css');
    wp_dequeue_style('woocommerce-inline');
    wp_dequeue_style('storefront-woocommerce-style');
    wp_dequeue_style('twentytwentyone-woocommerce-style');
}
}
add_action('wp_enqueue_scripts', 'khane_irani_dequeue_woocommerce_styles', 99);
add_action('wp_print_styles', 'khane_irani_dequeue_woocommerce_styles', 100);
add_action('wp_head', 'khane_irani_dequeue_woocommerce_styles', 1);

if (!function_exists('khane_irani_woocommerce_locate_template')) {
function khane_irani_woocommerce_locate_template($template, $template_name, $template_path) {
    $theme_template = get_template_directory() . '/woocommerce/' . $template_name;
    if (file_exists($theme_template)) { return $theme_template; }
    return $template;
}
}
add_filter('woocommerce_locate_template', 'khane_irani_woocommerce_locate_template', 10, 3);

if (!function_exists('khane_irani_customize_shop_loop')) {
function khane_irani_customize_shop_loop() {
    // حذف کامل نمایش تعداد نتایج و مرتب‌سازی
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
}
}
add_action('init', 'khane_irani_customize_shop_loop');

// تابع khane_irani_shop_controls حذف شده - دیگر نیازی به نمایش تعداد نتایج و مرتب‌سازی نیست

if (!function_exists('khane_irani_woocommerce_pagination_args')) {
function khane_irani_woocommerce_pagination_args($args) {
    $args['prev_text'] = 'قبلی';
    $args['next_text'] = 'بعدی';
    return $args;
}
}
add_filter('woocommerce_pagination_args', 'khane_irani_woocommerce_pagination_args');

if (!function_exists('khane_irani_checkout_fields')) {
function khane_irani_checkout_fields($fields) {
    // تنظیمات فیلدهای فرم checkout
    // نام - اجباری
    if (isset($fields['billing']['billing_first_name'])) {
        $fields['billing']['billing_first_name']['label'] = 'نام';
        $fields['billing']['billing_first_name']['placeholder'] = 'نام خود را وارد کنید';
        $fields['billing']['billing_first_name']['required'] = true;
        $fields['billing']['billing_first_name']['class'] = array('form-row-first');
    }
    
    // نام خانوادگی - اجباری
    if (isset($fields['billing']['billing_last_name'])) {
        $fields['billing']['billing_last_name']['label'] = 'نام خانوادگی';
        $fields['billing']['billing_last_name']['placeholder'] = 'نام خانوادگی خود را وارد کنید';
        $fields['billing']['billing_last_name']['required'] = true;
        $fields['billing']['billing_last_name']['class'] = array('form-row-last');
    }
    
    // شماره تماس - اجباری
    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['label'] = 'شماره تماس';
        $fields['billing']['billing_phone']['placeholder'] = 'شماره تماس خود را وارد کنید';
        $fields['billing']['billing_phone']['required'] = true;
        $fields['billing']['billing_phone']['class'] = array('form-row-wide');
    }
    
    // ایمیل - اجباری
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['label'] = 'ایمیل';
        $fields['billing']['billing_email']['placeholder'] = 'ایمیل خود را وارد کنید';
        $fields['billing']['billing_email']['required'] = true;
        $fields['billing']['billing_email']['class'] = array('form-row-wide');
    }
    
    return $fields;
}
}
add_filter('woocommerce_checkout_fields', 'khane_irani_checkout_fields');

/**
 * Ensure payment gateways are available when customer country is empty
 */
if (!function_exists('khane_irani_fix_available_payment_gateways')) {
function khane_irani_fix_available_payment_gateways($gateways) {
    if (!class_exists('WooCommerce')) {
        return $gateways;
    }

    $is_checkout = function_exists('is_checkout') && is_checkout();
    $is_order_pay = function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-pay');

    if (!$is_checkout && !$is_order_pay) {
        return $gateways;
    }

    if (!empty($gateways)) {
        return $gateways;
    }

    static $running = false;
    if ($running) {
        return $gateways;
    }
    $running = true;

    // For order-pay, align customer location with order location if possible
    if ($is_order_pay) {
        $order_id = absint(get_query_var('order-pay'));
        if ($order_id) {
            $order = wc_get_order($order_id);
            if ($order instanceof WC_Order && WC()->customer) {
                if ($order->get_billing_country()) {
                    WC()->customer->set_billing_country($order->get_billing_country());
                    WC()->customer->set_billing_state($order->get_billing_state());
                }
                if ($order->get_shipping_country()) {
                    WC()->customer->set_shipping_country($order->get_shipping_country());
                    WC()->customer->set_shipping_state($order->get_shipping_state());
                }
            }
        }
    }

    // Fallback: set base country if customer country is empty
    if (WC()->customer && !WC()->customer->get_billing_country()) {
        $base_country = WC()->countries->get_base_country();
        if ($base_country) {
            WC()->customer->set_billing_country($base_country);
            WC()->customer->set_shipping_country($base_country);
        }
    }

    // Re-fetch gateways after fixing customer location
    $gateways = WC()->payment_gateways()->get_available_payment_gateways();
    $running = false;

    return $gateways;
}
}
add_filter('woocommerce_available_payment_gateways', 'khane_irani_fix_available_payment_gateways', 5);

// Shipping and address fields are now enabled for physical products

// حذف پیام "مشاهده سبد خرید" کنار محصول
// این فیلتر در functions.php تعریف شده است

