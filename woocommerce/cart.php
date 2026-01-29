<?php
/**
 * The Template for displaying cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

get_header(); ?>

<div class="zs-woocommerce-wrapper zs-cart-page">
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
                    <h1 class="woocommerce-products-header__title page-title">سبد خرید</h1>
                <?php endif; ?>
            </header>

            <?php
            wc_print_notices();

            do_action('woocommerce_before_cart');

            wc_get_template('cart/cart.php');

            do_action('woocommerce_after_cart');
            ?>

            <?php
            // Already removed wrapper hooks above
            ?>

        </main>
    </div>
</div>

<?php
get_footer();

