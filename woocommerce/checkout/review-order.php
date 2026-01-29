<?php
/**
 * Custom Checkout Review Order (Card layout)
 * @package khane_irani
 */

defined('ABSPATH') || exit;

do_action('woocommerce_review_order_before_cart_contents');
?>

<div class="zs-order-summary" role="region" aria-label="<?php echo esc_attr__('Order summary', 'woocommerce'); ?>">
    <ul class="zs-order-items">
        <?php $i = 0; $total_items = WC()->cart->get_cart_contents_count(); ?>
        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			if ( $_product && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                if ( $i >= 4 ) { continue; } $i++;
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                $product_price = WC()->cart->get_product_price( $_product );
				$product_subtotal = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] );
                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'thumbnail' ), $cart_item, $cart_item_key );
                $remove_url = wc_get_cart_remove_url( $cart_item_key );
                $sku = $_product->get_sku();
                $short_desc = wp_trim_words( wp_strip_all_tags( $_product->get_short_description() ), 18, '…' );
                $permalink = $_product->is_visible() ? $_product->get_permalink() : '';
                $item_data_html = wc_get_formatted_cart_item_data( $cart_item, false ); // variation/attributes
                $terms = get_the_terms( $_product->get_id(), 'product_cat' );
		?>
        <li class="zs-order-item zs-card">
            <div class="zs-card-media"><?php echo $thumbnail; // phpcs:ignore ?></div>
            <div class="zs-card-body">
                <div class="zs-item-title">
                    <?php if ( $permalink ) : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="zs-item-link"><?php echo wp_kses_post( $product_name ); ?></a>
                    <?php else : ?>
                        <?php echo wp_kses_post( $product_name ); ?>
                    <?php endif; ?>
                </div>
                <?php if ( $item_data_html ) : ?>
                    <div class="zs-item-attrs"><?php echo wp_kses_post( $item_data_html ); ?></div>
                <?php endif; ?>
                <?php if ( $sku ) : ?>
                    <div class="zs-item-sku">کد کالا: <?php echo esc_html( $sku ); ?></div>
                <?php endif; ?>
                <?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
                    <div class="zs-item-cats">
                        <?php foreach ( $terms as $term ) : ?>
                            <span class="zs-chip"><?php echo esc_html( $term->name ); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ( $short_desc ) : ?>
                    <div class="zs-item-desc"><?php echo esc_html( $short_desc ); ?></div>
                <?php endif; ?>
            </div>
            <div class="zs-card-footer">
                <div class="zs-meta">
                    <span class="zs-chip zs-qty">× <?php echo esc_html( $cart_item['quantity'] ); ?></span>
                    <span class="zs-chip zs-subtotal"><?php echo wp_kses_post( $product_subtotal ); ?></span>
                </div>
                <a href="<?php echo esc_url( $remove_url ); ?>" class="zs-remove-item" data-remove-url="<?php echo esc_url( $remove_url ); ?>" aria-label="<?php esc_attr_e('Remove', 'woocommerce'); ?>">×</a>
            </div>
        </li>
		<?php endif; endforeach; ?>
	</ul>

    <?php if ( $total_items > $i ) : ?>
        <div class="zs-more-items">+<?php echo esc_html( $total_items - $i ); ?> مورد دیگر</div>
    <?php endif; ?>

	<div class="zs-order-totals">
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="zs-total-row zs-discount">
				<span class="label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span class="value"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php wc_cart_totals_shipping_html(); ?>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="zs-total-row">
				<span class="label"><?php echo esc_html( $fee->name ); ?></span>
				<span class="value"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="zs-total-row">
						<span class="label"><?php echo esc_html( $tax->label ); ?></span>
						<span class="value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="zs-total-row">
					<span class="label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="value"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="zs-total-row zs-total">
			<span class="label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
			<span class="value"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>
	</div>
</div>

<?php do_action('woocommerce_review_order_after_cart_contents'); ?>

