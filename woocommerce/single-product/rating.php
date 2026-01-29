<?php
/**
 * Single Product Rating
 * Custom styling for Zarin Service - Optimized
 *
 * @package khane_irani
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $product;

if (!wc_review_ratings_enabled()) {
    return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();
$review_link  = '#reviews';
?>
<?php if ($rating_count > 0) : ?>
    <div class="woocommerce-product-rating zs-product-rating">
        <?php echo wc_get_rating_html($average, $rating_count); ?>

        <?php if (comments_open()) : ?>
            <a href="<?php echo esc_url($review_link); ?>" class="woocommerce-review-link" rel="nofollow">
                (<?php printf(_n('%s نظر مشتری', '%s نظر مشتری', $review_count, 'woocommerce'), '<span class="count">' . esc_html($review_count) . '</span>'); ?>)
            </a>
        <?php endif ?>
    </div>
<?php endif; ?>

<style>
.zs-product-rating {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-bottom: 20px !important;
    padding: 15px !important;
    background: linear-gradient(135deg, #fef5e7, #fed7aa) !important;
    border-radius: 15px !important;
    border-left: 4px solid #f59e0b !important;
}

.zs-product-rating .star-rating {
    margin: 0 !important;
    font-size: 16px !important;
}

.zs-product-rating .woocommerce-review-link {
    color: #92400e !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    transition: color 0.3s ease !important;
}

.zs-product-rating .woocommerce-review-link:hover {
    color: #b45309 !important;
}
</style>
