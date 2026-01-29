<?php
/**
 * Custom Checkout Payment Methods (grid cards)
 * @package khane_irani
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_ajax() ) {
    do_action( 'woocommerce_review_order_before_payment' );
}

if ( WC()->cart->needs_payment() ) {
    $available_gateways = isset( $available_gateways ) ? $available_gateways : WC()->payment_gateways()->get_available_payment_gateways();
    WC()->payment_gateways()->set_current_gateway( $available_gateways );
} else {
    $available_gateways = array();
}

$order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );
?>

<div id="payment" class="woocommerce-checkout-payment">
    <div class="zs-payment-section">
        <?php if ( WC()->cart->needs_payment() ) : ?>
            <h3 class="zs-section-title"><?php esc_html_e( 'انتخاب روش پرداخت', 'woocommerce' ); ?></h3>

            <?php if ( ! empty( $available_gateways ) ) : ?>
                <ul class="wc_payment_methods payment_methods methods zs-pay-grid">
                    <?php
                    $first_gateway = true;
                    foreach ( $available_gateways as $gateway ) :
                        $is_chosen = $gateway->chosen || $first_gateway;
                    ?>
                        <li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                            <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
                                   type="radio"
                                   class="input-radio"
                                   name="payment_method"
                                   value="<?php echo esc_attr( $gateway->id ); ?>"
                                   <?php checked( $is_chosen, true ); ?>
                                   data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
                            <?php $first_gateway = false; ?>

                            <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="zs-pay-label">
                                <?php if ( $gateway->get_icon() ) : ?>
                                    <span class="zs-pay-icon">
                                        <?php echo $gateway->get_icon(); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="zs-pay-title"><?php echo wp_kses_post( $gateway->get_title() ); ?></span>
                                <?php if ( $gateway->get_description() ) : ?>
                                    <span class="zs-pay-desc"><?php echo wp_kses_post( $gateway->get_description() ); ?></span>
                                <?php endif; ?>
                            </label>

                            <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                                <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>"
                                    <?php if ( ! $is_chosen ) : ?>style="display:none;"<?php endif; ?>>
                                    <?php $gateway->payment_fields(); ?>
                                </div>
                            <?php endif; ?>

                            <?php do_action( 'woocommerce_after_payment_method', $gateway ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="woocommerce-notice woocommerce-notice--info woocommerce-info">
                    <?php
                    echo wp_kses_post(
                        apply_filters(
                            'woocommerce_no_available_payment_methods_message',
                            __( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance.', 'woocommerce' )
                        )
                    );
                    ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <div class="form-row place-order">
            <noscript>
                <?php
                printf(
                    esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ),
                    '<em>',
                    '</em>'
                );
                ?>
                <br/>
                <button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>">
                    <?php esc_html_e( 'Update totals', 'woocommerce' ); ?>
                </button>
            </noscript>

            <?php wc_get_template( 'checkout/terms.php' ); ?>

            <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

            <?php
            echo apply_filters(
                'woocommerce_order_button_html',
                '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>'
            );
            ?>

            <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

            <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
        </div>
    </div>
</div>

<?php if ( ! is_ajax() ) {
    do_action( 'woocommerce_review_order_after_payment' );
}

