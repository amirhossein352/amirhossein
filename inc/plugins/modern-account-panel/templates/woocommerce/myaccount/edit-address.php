<?php
/**
 * Custom Edit Address Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$customer = new WC_Customer($current_user->ID);
$load_address = isset($_GET['address']) ? wc_edit_address_i18n(sanitize_title($_GET['address']), true) : 'billing';
$page_title = ('billing' === $load_address) ? __('Billing address', 'woocommerce') : __('Shipping address', 'woocommerce');
?>

<div class="page" id="addresses" data-page="addresses">
    <div class="page-header">
        <h1 class="page-title">آدرس‌ها</h1>
        <div class="breadcrumb">
            <a href="<?php echo esc_url(home_url()); ?>">خانه</a>
            <span>/</span>
            <span>آدرس‌ها</span>
        </div>
    </div>

    <!-- Addresses Info -->
    <div class="content-card">
        <div class="card-header">
            <h3>آدرس‌های ثبت شده</h3>
            <button class="btn btn-primary" id="addAddressBtn">
                <i class="fas fa-plus"></i> افزودن آدرس جدید
            </button>
        </div>
        <div class="card-body">
            <div class="addresses-grid" id="addressesGrid">
                <!-- Billing Address -->
                <div class="address-card">
                    <div class="address-header">
                        <h4>آدرس صورتحساب</h4>
                        <div class="address-actions">
                            <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address') . 'billing/'); ?>" class="btn-icon edit-address" data-type="billing">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    <div class="address-content">
                        <?php if ($customer->get_billing_address_1()) : ?>
                            <p><strong><?php echo esc_html($customer->get_billing_first_name() . ' ' . $customer->get_billing_last_name()); ?></strong></p>
                            <p><?php echo esc_html($customer->get_billing_address_1()); ?></p>
                            <?php if ($customer->get_billing_address_2()) : ?>
                                <p><?php echo esc_html($customer->get_billing_address_2()); ?></p>
                            <?php endif; ?>
                            <p><?php echo esc_html($customer->get_billing_city() . ', ' . $customer->get_billing_state() . ' ' . $customer->get_billing_postcode()); ?></p>
                            <p>کد پستی: <?php echo esc_html($customer->get_billing_postcode()); ?></p>
                            <p>تلفن: <?php echo esc_html($customer->get_billing_phone()); ?></p>
                        <?php else : ?>
                            <p>آدرس صورتحساب ثبت نشده است.</p>
                        <?php endif; ?>
                    </div>
                    <div class="address-footer">
                        <span class="address-type-badge billing">صورتحساب</span>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="address-card">
                    <div class="address-header">
                        <h4>آدرس ارسال</h4>
                        <div class="address-actions">
                            <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address') . 'shipping/'); ?>" class="btn-icon edit-address" data-type="shipping">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    <div class="address-content">
                        <?php if ($customer->get_shipping_address_1()) : ?>
                            <p><strong><?php echo esc_html($customer->get_shipping_first_name() . ' ' . $customer->get_shipping_last_name()); ?></strong></p>
                            <p><?php echo esc_html($customer->get_shipping_address_1()); ?></p>
                            <?php if ($customer->get_shipping_address_2()) : ?>
                                <p><?php echo esc_html($customer->get_shipping_address_2()); ?></p>
                            <?php endif; ?>
                            <p><?php echo esc_html($customer->get_shipping_city() . ', ' . $customer->get_shipping_state() . ' ' . $customer->get_shipping_postcode()); ?></p>
                            <p>کد پستی: <?php echo esc_html($customer->get_shipping_postcode()); ?></p>
                            <p>تلفن: <?php echo esc_html($customer->get_shipping_phone()); ?></p>
                        <?php else : ?>
                            <p>آدرس ارسال ثبت نشده است.</p>
                        <?php endif; ?>
                    </div>
                    <div class="address-footer">
                        <span class="address-type-badge shipping">ارسال</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Address Form (if editing) -->
    <?php if (isset($_GET['address'])) : ?>
        <div class="content-card">
            <div class="card-header">
                <h3>ویرایش آدرس <?php echo ('billing' === $load_address) ? 'صورتحساب' : 'ارسال'; ?></h3>
            </div>
            <div class="card-body">
                <?php wc_get_template('myaccount/form-edit-address.php', array('load_address' => $load_address)); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

