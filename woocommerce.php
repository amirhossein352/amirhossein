<?php
/**
 * The template for displaying WooCommerce pages
 * Enhanced for Zarin Service Modern Design
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

// For cart and checkout pages, we use their specific templates
if (is_cart()) {
    $cart_template = get_template_directory() . '/woocommerce/cart.php';
    if (file_exists($cart_template)) {
        include $cart_template;
        return;
    }
}

if (is_checkout()) {
    $checkout_template = get_template_directory() . '/woocommerce/checkout.php';
    if (file_exists($checkout_template)) {
        include $checkout_template;
        return;
    }
}

get_header(); ?>

<div class="zs-woocommerce-wrapper">
    <div class="container">
        <main id="primary" class="site-main woocommerce-main">
            <?php woocommerce_content(); ?>
        </main>
    </div>
</div>

<?php
get_footer();
