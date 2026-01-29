<?php
/**
 * The template for displaying product content within loops
 * شبیه صفحه اصلی - استایل ki-product-card
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

$product_id = $product->get_id();
$sale_percentage = 0;
$regular_price = $product->get_regular_price();
$sale_price = $product->get_sale_price();
$is_variable = $product->is_type('variable');

// برای محصولات متغیر، کمترین قیمت را بگیر
if ($is_variable) {
    $min_price = $product->get_variation_price('min', true);
    $min_regular_price = $product->get_variation_regular_price('min', true);
    $min_sale_price = $product->get_variation_sale_price('min', true);
    
    if ($min_sale_price && $min_sale_price < $min_regular_price) {
        $sale_price = $min_sale_price;
        $regular_price = $min_regular_price;
        $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
    } else {
        $regular_price = $min_price;
        $sale_price = '';
    }
}

if ($regular_price && $sale_price) {
    $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
}
?>

<li <?php wc_product_class('', $product); ?> style="margin: 0 !important; padding: 0 !important; list-style: none !important;">
    <div class="ki-product-card" style="background: #ffffff !important; border-radius: 12px !important; overflow: hidden !important; border: 1px solid #e9ecef !important; transition: all 0.3s ease !important; display: flex !important; flex-direction: column !important; height: 100% !important; width: 100% !important;">
        <div class="ki-product-image-wrapper" style="position: relative !important; width: 100% !important; height: 280px !important; overflow: hidden !important; background: #fafbfc !important;">
            <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="ki-product-link" style="text-decoration: none !important; color: inherit !important; display: block !important; width: 100% !important; height: 100% !important;">
                <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'ki-product-image', 'style' => 'width: 100% !important; height: 100% !important; object-fit: cover !important; transition: transform 0.3s ease !important;')); ?>
                <div class="ki-product-overlay" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(55, 195, 179, 0.85) !important; display: flex !important; align-items: center !important; justify-content: center !important; opacity: 0 !important; transition: opacity 0.3s ease !important;">
                    <span class="ki-view-product-overlay" style="color: #ffffff !important; font-size: 14px !important; font-weight: 600 !important; display: flex !important; align-items: center !important; gap: 8px !important; padding: 10px 20px !important; background: rgba(255, 255, 255, 0.2) !important; border-radius: 6px !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;">
                        <i class="fas fa-eye"></i>
                        مشاهده محصول
                    </span>
                </div>
            </a>
            <?php if ($sale_percentage > 0): ?>
                <span class="ki-sale-badge" style="position: absolute !important; top: 12px !important; right: 12px !important; background: #ef4444 !important; color: #ffffff !important; padding: 6px 12px !important; border-radius: 6px !important; font-size: 11px !important; font-weight: 600 !important; z-index: 3 !important; display: inline-flex !important; align-items: center !important; gap: 4px !important;">
                    <i class="fas fa-percent"></i>
                    <?php echo esc_html($sale_percentage); ?>%
                </span>
            <?php endif; ?>
        </div>
        <div class="ki-product-info" style="padding: 20px !important; display: flex !important; flex-direction: column !important; gap: 12px !important; background: #ffffff !important; flex: 1 !important;">
            <div class="ki-product-price-header" style="display: flex !important; align-items: center !important; gap: 10px !important; flex-wrap: wrap !important;">
                <?php if ($sale_price): ?>
                    <span class="ki-price-sale" style="font-size: 18px !important; font-weight: 700 !important; color: #ef4444 !important; direction: ltr !important; font-family: 'IranYekan', 'Tahoma', sans-serif !important;">
                        <?php echo wc_price($sale_price); ?>
                    </span>
                    <span class="ki-price-regular" style="font-size: 14px !important; font-weight: 500 !important; color: #9ca3af !important; text-decoration: line-through !important; direction: ltr !important; font-family: 'IranYekan', 'Tahoma', sans-serif !important;">
                        <?php echo wc_price($regular_price); ?>
                    </span>
                <?php else: ?>
                    <span class="ki-price-current" style="font-size: 18px !important; font-weight: 700 !important; color: var(--zs-teal-dark, #00A796) !important; direction: ltr !important; display: inline-block !important; font-family: 'IranYekan', 'Tahoma', sans-serif !important;">
                        <?php echo $product->get_price_html(); ?>
                    </span>
                <?php endif; ?>
            </div>
            <h3 class="ki-product-title" style="font-size: 14px !important; font-weight: 500 !important; color: #1f2937 !important; margin: 0 !important; line-height: 1.5 !important; min-height: 42px !important; display: -webkit-box !important; -webkit-line-clamp: 2 !important; -webkit-box-orient: vertical !important; overflow: hidden !important;">
                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" style="color: inherit !important; text-decoration: none !important; transition: color 0.3s ease !important;">
                    <?php echo esc_html($product->get_name()); ?>
                </a>
            </h3>
            <div class="ki-product-actions" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 10px !important; padding-top: 12px !important; border-top: 1px solid #f1f5f9 !important; margin-top: auto !important;">
                <button class="ki-action-btn ki-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" title="علاقه‌مندی" style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid #e5e7eb !important; background: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; padding: 0 !important; color: #6b7280 !important; font-size: 16px !important;">
                    <i class="far fa-heart"></i>
                </button>
                <button class="ki-action-btn ki-compare-btn" data-product-id="<?php echo esc_attr($product_id); ?>" title="مقایسه" style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid #e5e7eb !important; background: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; padding: 0 !important; color: #6b7280 !important; font-size: 16px !important;">
                    <i class="fas fa-exchange-alt"></i>
                </button>
                <?php
                // برای محصولات متغیر، به صفحه محصول لینک بده
                if ($is_variable) {
                    $add_to_cart_url = get_permalink($product_id);
                    $cart_button_class = '';
                    $cart_button_text = '<i class="fas fa-eye"></i>';
                    $cart_button_title = 'مشاهده محصول';
                } else {
                    $add_to_cart_url = $product->add_to_cart_url();
                    $cart_button_class = 'add_to_cart_button'; // حذف ajax_add_to_cart - استفاده از endpoint سفارشی
                    $cart_button_text = '<i class="fas fa-shopping-cart"></i>';
                    $cart_button_title = 'افزودن به سبد خرید';
                }
                ?>
                <a href="javascript:void(0);"
                   class="ki-action-btn ki-cart-btn <?php echo esc_attr($cart_button_class); ?>" 
                   data-product_id="<?php echo esc_attr($product_id); ?>"
                   data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                   data-product_type="<?php echo $is_variable ? 'variable' : 'simple'; ?>"
                   title="<?php echo esc_attr($cart_button_title); ?>" 
                   style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid var(--zs-teal-medium, #37C3B3) !important; background: var(--zs-teal-medium, #37C3B3) !important; color: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; text-decoration: none !important; font-size: 16px !important;">
                    <?php echo $cart_button_text; ?>
                </a>
            </div>
        </div>
    </div>
</li>

<style>
.ki-product-card:hover {
    transform: translateY(-4px) !important;
    border-color: #dee2e6 !important;
}
.ki-product-card:hover .ki-product-image {
    transform: scale(1.05) !important;
}
.ki-product-card:hover .ki-product-overlay {
    opacity: 1 !important;
}
.ki-action-btn.ki-cart-btn:hover {
    background: var(--zs-teal-dark, #00A796) !important;
    border-color: var(--zs-teal-dark, #00A796) !important;
    color: #ffffff !important;
    transform: translateY(-3px) !important;
}
.ki-action-btn.ki-wishlist-btn:hover,
.ki-action-btn.ki-compare-btn:hover {
    background: #f3f4f6 !important;
    border-color: #d1d5db !important;
    color: #374151 !important;
    transform: translateY(-2px) !important;
}
.ki-product-title a:hover {
    color: var(--zs-teal-dark, #00A796) !important;
}
</style>
