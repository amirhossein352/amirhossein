<?php
/**
 * Single Product Meta
 * Custom styling for Zarin Service - Optimized
 *
 * @package khane_irani
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $product;
?>

<div class="product_meta zs-product-meta">
    <?php do_action('woocommerce_product_meta_start'); ?>

    <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
        <?php $sku = $product->get_sku(); ?>
        <span class="sku_wrapper">
            <span class="meta-label">
                <?php esc_html_e('کد محصول:', 'woocommerce'); ?>
            </span>
            <span class="sku">
                <?php echo $sku ? $sku : esc_html__('ندارد', 'woocommerce'); ?>
            </span>
        </span>
    <?php endif; ?>

    <?php echo wc_get_product_category_list($product->get_id(), '<span class="meta-sep">, </span>', '<span class="posted_in"><span class="meta-label">' . _n('دسته‌بندی:', 'دسته‌بندی‌ها:', count($product->get_category_ids()), 'woocommerce') . '</span> ', '</span>'); ?>

    <?php echo wc_get_product_tag_list($product->get_id(), '<span class="meta-sep">, </span>', '<span class="tagged_as"><span class="meta-label">' . _n('برچسب:', 'برچسب‌ها:', count($product->get_tag_ids()), 'woocommerce') . '</span> ', '</span>'); ?>

    <?php do_action('woocommerce_product_meta_end'); ?>
</div>

<style>
.zs-product-meta {
    padding: 20px !important;
    background: #f1f5f9 !important;
    border-radius: 15px !important;
    margin-top: 30px !important;
    border-left: 4px solid #64748b !important;
    font-size: 14px !important;
    color: #475569 !important;
}

.zs-product-meta > span {
    display: block !important;
    margin-bottom: 10px !important;
}

.zs-product-meta > span:last-child {
    margin-bottom: 0 !important;
}

.zs-product-meta .meta-label {
    font-weight: 600 !important;
    color: var(--zs-teal-dark, #00A796) !important;
    margin-left: 5px !important;
}

.zs-product-meta a {
    color: var(--zs-teal-medium, #37C3B3) !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    background: rgba(59, 130, 246, 0.1) !important;
    padding: 4px 8px !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
}

.zs-product-meta a:hover {
    background: rgba(59, 130, 246, 0.2) !important;
    transform: translateY(-1px) !important;
}

.zs-product-meta .sku {
    background: #f8f9fa !important;
    padding: 4px 8px !important;
    border-radius: 6px !important;
    font-family: monospace !important;
    font-weight: 600 !important;
    color: var(--zs-teal-dark, #00A796) !important;
    font-size: 13px !important;
}
</style>
