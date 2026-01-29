<?php
/**
 * Checkout Form
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

?>

<div class="zs-checkout-two-column">
    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
        
        <div class="zs-checkout-left">
            <?php if ($checkout->get_checkout_fields()) : ?>
                <?php do_action('woocommerce_checkout_before_customer_details'); ?>
                <div class="col2-set" id="customer_details">
                    <div class="col-1">
                        <?php do_action('woocommerce_checkout_billing'); ?>
                    </div>
                    <div class="col-2">
                        <?php do_action('woocommerce_checkout_shipping'); ?>
                    </div>
                </div>
                <?php do_action('woocommerce_checkout_after_customer_details'); ?>
            <?php endif; ?>

            <?php
            // Custom Coupon Toggle Button
            ?>
            <div class="zs-custom-coupon-toggle-wrapper">
                <button type="button" class="zs-coupon-toggle-btn" id="zs-coupon-toggle-btn" aria-expanded="false">
                    <span class="zs-coupon-icon">🎁</span>
                    <span class="zs-coupon-text">کد تخفیف دارید؟</span>
                    <span class="zs-coupon-arrow">▼</span>
                </button>
            </div>
            
            <?php
            // Coupon form (hidden by default, shown by JS)
            woocommerce_checkout_coupon_form();
            // Payment methods below form
            wc_get_template('checkout/payment.php');
            ?>

            <?php // Terms + Place order right after payment (kept in left column)
            wc_get_template('checkout/terms.php');
            do_action('woocommerce_review_order_before_submit');
            echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr(__('Place order', 'woocommerce')) . '" data-value="' . esc_attr(__('Place order', 'woocommerce')) . '">' . esc_html__('Place order', 'woocommerce') . '</button>');
            do_action('woocommerce_review_order_after_submit');
            wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce');
            ?>
        </div>

        <div class="zs-checkout-right">
            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
            <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
            <?php do_action('woocommerce_checkout_before_order_review'); ?>
            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php wc_get_template('checkout/review-order.php'); ?>
            </div>
            <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </div>

    </form>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
