<?php
/**
 * WooCommerce Price Filter
 * فیلتر قیمت برای صفحات آرشیو محصول
 *
 * @package khane_irani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * فیلتر کردن محصولات بر اساس قیمت
 * غیرفعال شده - باعث خطا می‌شد
 */
// function khane_irani_price_filter_query($query) {
//     if (is_admin() || !$query->is_main_query()) {
//         return;
//     }
//     
//     if (!(is_shop() || is_product_category() || is_product_tag())) {
//         return;
//     }
//     
//     // فقط اگر پارامترهای قیمت در URL وجود داشته باشند
//     if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
//         $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
//         $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 999999999;
//         
//         if ($min_price > 0 || $max_price < 999999999) {
//             $meta_query = $query->get('meta_query');
//             if (!is_array($meta_query)) {
//                 $meta_query = array('relation' => 'AND');
//             }
//             
//             $meta_query[] = array(
//                 'key' => '_price',
//                 'value' => array($min_price, $max_price),
//                 'type' => 'DECIMAL',
//                 'compare' => 'BETWEEN'
//             );
//             
//             $query->set('meta_query', $meta_query);
//         }
//     }
// }
// add_action('woocommerce_product_query', 'khane_irani_price_filter_query', 20);

