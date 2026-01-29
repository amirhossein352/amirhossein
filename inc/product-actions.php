<?php
/**
 * Product Actions - Wishlist, Compare, Add to Cart
 * فقط handler های AJAX - بدون تغییر در قالب
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Wishlist and Compare endpoints to My Account
 */
function ki_add_account_endpoints() {
    add_rewrite_endpoint('wishlist', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('compare', EP_ROOT | EP_PAGES);
}
add_action('init', 'ki_add_account_endpoints');

/**
 * Add Wishlist and Compare to My Account menu
 */
function ki_add_account_menu_items($items) {
    // Insert before logout
    $logout = isset($items['customer-logout']) ? $items['customer-logout'] : '';
    if ($logout) {
        unset($items['customer-logout']);
    }
    
    $items['wishlist'] = __('علاقه‌مندی‌ها', 'khane-irani');
    $items['compare'] = __('مقایسه', 'khane-irani');
    
    if ($logout) {
        $items['customer-logout'] = $logout;
    }
    
    return $items;
}
add_filter('woocommerce_account_menu_items', 'ki_add_account_menu_items');

/**
 * Add content for Wishlist endpoint
 */
function ki_wishlist_content() {
    $user_id = get_current_user_id();
    $wishlist = get_user_meta($user_id, 'ki_wishlist', true);
    
    if (!is_array($wishlist)) {
        $wishlist = array();
    }
    
    if (empty($wishlist)) {
        echo '<div class="ki-empty-state" style="text-align: center; padding: 60px 20px;">';
        echo '<i class="far fa-heart" style="font-size: 64px; color: #e5e7eb; margin-bottom: 20px;"></i>';
        echo '<h3 style="color: #6b7280; margin-bottom: 10px;">لیست علاقه‌مندی‌های شما خالی است</h3>';
        echo '<p style="color: #9ca3af; margin-bottom: 30px;">محصولات مورد علاقه خود را اضافه کنید</p>';
        echo '<a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="button" style="background: var(--zs-teal-medium, #37C3B3); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block;">مشاهده فروشگاه</a>';
        echo '</div>';
        return;
    }
    
    echo '<div class="ki-wishlist-content">';
    echo '<h2 style="margin-bottom: 30px; color: #1f2937;">علاقه‌مندی‌های من</h2>';
    
    $args = array(
        'post_type' => 'product',
        'post__in' => $wishlist,
        'posts_per_page' => -1,
    );
    
    $products = new WP_Query($args);
    
    if ($products->have_posts()) {
        echo '<div class="woocommerce columns-4">';
        echo '<ul class="products columns-4">';
        
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            $product_id = $product->get_id();
            
            echo '<li class="product">';
            echo '<div class="ki-wishlist-item-actions">';
            echo '<button class="ki-remove-wishlist-btn" data-product-id="' . esc_attr($product_id) . '" title="حذف از علاقه‌مندی‌ها">';
            echo '<i class="fas fa-times"></i>';
            echo '</button>';
            echo '</div>';
            wc_get_template_part('content', 'product');
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
    
    wp_reset_postdata();
    echo '</div>';
}
add_action('woocommerce_account_wishlist_endpoint', 'ki_wishlist_content');

/**
 * Add content for Compare endpoint
 */
function ki_compare_content() {
    $user_id = get_current_user_id();
    $compare = get_user_meta($user_id, 'ki_compare', true);
    
    if (!is_array($compare)) {
        $compare = array();
    }
    
    if (empty($compare)) {
        echo '<div class="ki-empty-state" style="text-align: center; padding: 60px 20px;">';
        echo '<i class="fas fa-exchange-alt" style="font-size: 64px; color: #e5e7eb; margin-bottom: 20px;"></i>';
        echo '<h3 style="color: #6b7280; margin-bottom: 10px;">لیست مقایسه شما خالی است</h3>';
        echo '<p style="color: #9ca3af; margin-bottom: 30px;">محصولات را برای مقایسه اضافه کنید</p>';
        echo '<a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="button" style="background: var(--zs-teal-medium, #37C3B3); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block;">مشاهده فروشگاه</a>';
        echo '</div>';
        return;
    }
    
    echo '<div class="ki-compare-content">';
    echo '<h2 style="margin-bottom: 30px; color: #1f2937;">مقایسه محصولات</h2>';
    
    // نمایش محصولات در جدول مقایسه
    echo '<div class="ki-compare-table-wrapper" style="overflow-x: auto;">';
    echo '<table class="ki-compare-table" style="width: 100%; border-collapse: collapse; background: #ffffff; border-radius: 12px; overflow: hidden;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="padding: 20px; background: #f8f9fa; border-bottom: 2px solid #e9ecef; text-align: right; font-weight: 600; color: #1f2937;">ویژگی</th>';
    
    $products_data = array();
    foreach ($compare as $product_id) {
        $product = wc_get_product($product_id);
        if ($product && $product->is_visible()) {
            $products_data[$product_id] = $product;
            echo '<th>';
            echo '<div class="ki-compare-product">';
            echo '<a href="' . esc_url(get_permalink($product_id)) . '">';
            echo $product->get_image('woocommerce_thumbnail', array('class' => 'ki-compare-product-image'));
            echo '<h3 class="ki-compare-product-title">' . esc_html($product->get_name()) . '</h3>';
            echo '<div class="ki-compare-product-price">' . $product->get_price_html() . '</div>';
            echo '</a>';
            echo '<button class="ki-remove-compare-btn" data-product-id="' . esc_attr($product_id) . '">حذف از مقایسه</button>';
            echo '</div>';
            echo '</th>';
        }
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // ویژگی‌های مقایسه
    $features = array(
        'price' => 'قیمت',
        'stock_status' => 'وضعیت موجودی',
        'sku' => 'کد محصول',
        'weight' => 'وزن',
        'dimensions' => 'ابعاد',
    );
    
    foreach ($features as $key => $label) {
        echo '<tr>';
        echo '<td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef; font-weight: 600; color: #6b7280; background: #fafbfc;">' . esc_html($label) . '</td>';
        
        foreach ($products_data as $product_id => $product) {
            echo '<td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef; text-align: center;">';
            
            switch ($key) {
                case 'price':
                    echo $product->get_price_html();
                    break;
                case 'stock_status':
                    $stock_status = $product->get_stock_status();
                    $status_text = $stock_status === 'instock' ? 'موجود' : 'ناموجود';
                    $status_class = $stock_status === 'instock' ? 'in-stock' : 'out-of-stock';
                    echo '<span class="ki-stock-status ' . esc_attr($status_class) . '">' . esc_html($status_text) . '</span>';
                    break;
                case 'sku':
                    echo $product->get_sku() ? esc_html($product->get_sku()) : '-';
                    break;
                case 'weight':
                    echo $product->get_weight() ? esc_html($product->get_weight() . ' ' . get_option('woocommerce_weight_unit')) : '-';
                    break;
                case 'dimensions':
                    $dimensions = $product->get_dimensions(false);
                    echo $dimensions ? esc_html($dimensions) : '-';
                    break;
            }
            
            echo '</td>';
        }
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    echo '</div>';
}
add_action('woocommerce_account_compare_endpoint', 'ki_compare_content');

/**
 * Flush rewrite rules when needed
 */
function ki_flush_rewrite_rules_on_init() {
    if (get_option('ki_wishlist_compare_flush_rewrite_rules') === 'yes') {
        flush_rewrite_rules();
        delete_option('ki_wishlist_compare_flush_rewrite_rules');
    }
}
add_action('init', 'ki_flush_rewrite_rules_on_init', 999);

/**
 * Set flag to flush rewrite rules
 */
function ki_set_flush_rewrite_rules_flag() {
    update_option('ki_wishlist_compare_flush_rewrite_rules', 'yes');
}
add_action('after_switch_theme', 'ki_set_flush_rewrite_rules_flag');

// AJAX: Wishlist
function ki_ajax_wishlist() {
    check_ajax_referer('ki_actions_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'لطفا ابتدا وارد حساب کاربری خود شوید'));
        return;
    }
    
    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();
    
    if (!$product_id) {
        wp_send_json_error(array('message' => 'محصول یافت نشد'));
        return;
    }
    
    $wishlist = get_user_meta($user_id, 'ki_wishlist', true);
    if (!is_array($wishlist)) {
        $wishlist = array();
    }
    
    $action = 'added';
    if (in_array($product_id, $wishlist)) {
        $wishlist = array_diff($wishlist, array($product_id));
        $action = 'removed';
    } else {
        $wishlist[] = $product_id;
    }
    
    $wishlist = array_values(array_unique($wishlist));
    update_user_meta($user_id, 'ki_wishlist', $wishlist);
    
    wp_send_json_success(array(
        'action' => $action,
        'message' => $action === 'added' ? 'به علاقه‌مندی‌ها اضافه شد' : 'از علاقه‌مندی‌ها حذف شد'
    ));
}
add_action('wp_ajax_ki_wishlist', 'ki_ajax_wishlist');
add_action('wp_ajax_nopriv_ki_wishlist', 'ki_ajax_wishlist');

// AJAX: Compare
function ki_ajax_compare() {
    check_ajax_referer('ki_actions_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'لطفا ابتدا وارد حساب کاربری خود شوید'));
        return;
    }
    
    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();
    
    if (!$product_id) {
        wp_send_json_error(array('message' => 'محصول یافت نشد'));
        return;
    }
    
    $compare = get_user_meta($user_id, 'ki_compare', true);
    if (!is_array($compare)) {
        $compare = array();
    }
    
    if (!in_array($product_id, $compare) && count($compare) >= 4) {
        wp_send_json_error(array('message' => 'حداکثر 4 محصول می‌توانید مقایسه کنید'));
        return;
    }
    
    $action = 'added';
    if (in_array($product_id, $compare)) {
        $compare = array_diff($compare, array($product_id));
        $action = 'removed';
    } else {
        $compare[] = $product_id;
    }
    
    $compare = array_values(array_unique($compare));
    update_user_meta($user_id, 'ki_compare', $compare);
    
    wp_send_json_success(array(
        'action' => $action,
        'message' => $action === 'added' ? 'به لیست مقایسه اضافه شد' : 'از لیست مقایسه حذف شد'
    ));
}
add_action('wp_ajax_ki_compare', 'ki_ajax_compare');
add_action('wp_ajax_nopriv_ki_compare', 'ki_ajax_compare');

// AJAX: Get Cart Contents
function ki_ajax_get_cart_contents() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce فعال نیست'));
        return;
    }
    
    $cart_contents = WC()->cart->get_cart();
    $items = array();
    $cart_count = 0;
    
    foreach ($cart_contents as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $quantity = $cart_item['quantity'];
        
        // دریافت تصویر محصول
        $image_id = $product->get_image_id();
        $image_url = '';
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        }
        if (!$image_url) {
            $image_url = wc_placeholder_img_src('thumbnail');
        }
        
        // محاسبه قیمت کل برای این آیتم
        $line_total = $product->get_price() * $quantity;
        
        $items[] = array(
            'key' => $cart_item_key,
            'name' => $product->get_name(),
            'quantity' => $quantity,
            'price' => wc_price($line_total),
            'image' => $image_url,
            'product_id' => $cart_item['product_id'],
        );
        
        // شمارش تعداد آیتم‌ها (نه مجموع quantity) - هر آیتم = 1
        $cart_count += 1;
    }
    
    wp_send_json_success(array(
        'items' => $items,
        'cart_count' => $cart_count,
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_hash' => WC()->cart->get_cart_hash()
    ));
}
add_action('wp_ajax_ki_get_cart_contents', 'ki_ajax_get_cart_contents');
add_action('wp_ajax_nopriv_ki_get_cart_contents', 'ki_ajax_get_cart_contents');

// AJAX: Custom Add to Cart (مشابه custom-dollar-order)
function ki_ajax_add_to_cart() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce فعال نیست'));
        return;
    }
    
    // بررسی nonce (اختیاری - برای امنیت)
    if (isset($_POST['nonce']) && !empty($_POST['nonce'])) {
        if (!wp_verify_nonce($_POST['nonce'], 'ki_actions_nonce')) {
            wp_send_json_error(array('message' => 'خطای امنیتی'));
            return;
        }
    }
    
    // Clear notices قبل از شروع
    wc_clear_notices();
    
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if (!$product_id) {
        wc_clear_notices();
        wp_send_json_error(array('message' => 'شناسه محصول نامعتبر'));
        return;
    }
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wc_clear_notices();
        wp_send_json_error(array('message' => 'محصول یافت نشد'));
        return;
    }
    
    // بررسی موجودی
    if (!$product->is_in_stock()) {
        wc_clear_notices();
        wp_send_json_error(array('message' => 'محصول موجود نیست'));
        return;
    }
    
    // بررسی موجودی کافی
    if ($product->managing_stock() && $product->get_stock_quantity() < $quantity) {
        wc_clear_notices();
        wp_send_json_error(array('message' => 'موجودی کافی نیست'));
        return;
    }
    
    // بررسی قابل خرید بودن
    if (!$product->is_purchasable()) {
        wc_clear_notices();
        wp_send_json_error(array('message' => 'محصول قابل خرید نیست'));
        return;
    }
    
    // محصول ساده
    if ($product->is_type('simple')) {
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
        
        if ($cart_item_key) {
            WC()->cart->calculate_totals();
            
            // Clear notices بعد از موفقیت
            wc_clear_notices();
            
            // گرفتن fragments بدون messages
            ob_start();
            woocommerce_mini_cart();
            $mini_cart = ob_get_clean();
            
            wp_send_json_success(array(
                'success' => true,
                'message' => 'محصول با موفقیت اضافه شد',
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'fragments' => array(
                    'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
                )
            ));
        } else {
            // بررسی خطاها
            $notices = wc_get_notices('error');
            $error_message = 'خطا در اضافه کردن محصول به سبد خرید';
            if (!empty($notices)) {
                $error_message = $notices[0]['notice'];
            }
            wc_clear_notices();
            wp_send_json_error(array('message' => $error_message));
        }
        return;
    }
    
    // محصول متغیر - باید به صفحه محصول برود
    if ($product->is_type('variable')) {
        wc_clear_notices();
        wp_send_json(array(
            'error' => true,
            'product_url' => get_permalink($product_id)
        ));
        return;
    }
    
    // سایر انواع محصول
    wc_clear_notices();
    wp_send_json_error(array('message' => 'نوع محصول پشتیبانی نمی‌شود'));
}
add_action('wp_ajax_ki_add_to_cart', 'ki_ajax_add_to_cart');
add_action('wp_ajax_nopriv_ki_add_to_cart', 'ki_ajax_add_to_cart');

