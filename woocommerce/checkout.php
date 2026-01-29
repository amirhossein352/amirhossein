<?php
/**
 * The Template for displaying checkout page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

get_header(); ?>

<div class="zs-woocommerce-wrapper zs-checkout-page">
    <div class="container">
        <main id="primary" class="site-main woocommerce-main">
            
            <?php
            /**
             * woocommerce_before_main_content hook.
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             */
            // Remove wrapper output hooks for custom layout
            remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
            remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
            ?>

            <header class="woocommerce-products-header">
                <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                    <h1 class="woocommerce-products-header__title page-title"><?php esc_html_e('Checkout', 'woocommerce'); ?></h1>
                <?php endif; ?>
            </header>

            <?php
            wc_print_notices();

            $checkout = WC()->checkout();

            if ( is_wc_endpoint_url( 'order-pay' ) ) {
                $order_id = absint( get_query_var( 'order-pay' ) );
                $order    = wc_get_order( $order_id );

                if ( $order ) {
                    wc_get_template( 'checkout/form-pay.php', array(
                        'order'    => $order,
                        'order_id' => $order_id,
                    ) );
                } else {
                    wc_print_notice( esc_html__( 'Sorry, this order is invalid.', 'woocommerce' ), 'error' );
                }
            } elseif ( is_wc_endpoint_url( 'order-received' ) ) {
                $order_id = absint( get_query_var( 'order-received' ) );
                $order    = wc_get_order( $order_id );

                wc_get_template( 'checkout/thankyou.php', array(
                    'order'    => $order,
                    'order_id' => $order_id,
                ) );
            } else {
                do_action( 'woocommerce_before_checkout_form', $checkout );

                wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

                do_action( 'woocommerce_after_checkout_form', $checkout );
            }
            ?>

            <?php
            // Already removed wrapper hooks above
            ?>

        </main>
    </div>
</div>

<?php
get_footer();

