<?php
/**
 * Custom Checkout Payment Methods (grid cards)
 * @package khane_irani
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_ajax() ) {
    do_action( 'woocommerce_review_order_before_payment' );
}

$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
WC()->payment_gateways()->set_current_gateway( $available_gateways );
?>

<div id="payment" class="woocommerce-checkout-payment">
    <?php if ( $available_gateways ) : ?>
        <ul class="wc_payment_methods payment_methods methods zs-pay-grid">
            <?php foreach ( $available_gateways as $gateway ) : ?>
                <li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                    <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
                    <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="zs-pay-label">
                        <span class="zs-pay-title"><?php echo wp_kses_post( $gateway->get_title() ); ?></span>
                    </label>
                    <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                        <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) echo 'style="display:none;"'; ?>>
                            <?php $gateway->payment_fields(); ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p class="woocommerce-notice woocommerce-notice--info woocommerce-info"><?php esc_html_e( 'No payment methods available.', 'woocommerce' ); ?></p>
    <?php endif; ?>

    <!-- place order moved to form-checkout.php after order summary -->
</div>

<?php if ( ! is_ajax() ) {
    do_action( 'woocommerce_review_order_after_payment' );
}

