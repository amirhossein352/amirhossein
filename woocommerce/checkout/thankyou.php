<?php
/**
 * صفحه تشکر سفارشی - بدون هیچ کش و مشکلی
 */
if (!defined('ABSPATH')) {
    exit;
}

// جلوگیری از کش
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// دریافت سفارش
global $wp;
$order_id = absint($wp->query_vars['order-received']);
$order = wc_get_order($order_id);

// اگر سفارش وجود ندارد یا مشکل دارد
if (!$order) {
    wp_safe_redirect(wc_get_page_permalink('shop'));
    exit;
}

// شروع محتوا
get_header(); ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php if ($order->has_status('failed')) : ?>
                
                <!-- وضعیت ناموفق -->
                <div class="alert alert-danger text-center">
                    <h2>پرداخت ناموفق بود</h2>
                    <p>متأسفانه پرداخت شما موفقیت‌آمیز نبود. لطفاً دوباره تلاش کنید.</p>
                    <div class="mt-4">
                        <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="btn btn-primary">
                            تلاش مجدد پرداخت
                        </a>
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-outline-secondary">
                            بازگشت به فروشگاه
                        </a>
                    </div>
                </div>
                
            <?php else : ?>
                
                <!-- وضعیت موفق -->
                <div class="text-center mb-5">
                    <div class="success-icon mb-4">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                            <circle cx="40" cy="40" r="38" stroke="#28a745" stroke-width="4"/>
                            <path d="M25 40L35 50L55 30" stroke="#28a745" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    
                    <h1 class="text-success">سفارش شما ثبت شد!</h1>
                    <p class="lead">با تشکر از خرید شما. سفارش شما با موفقیت ثبت شد.</p>
                    
                    <div class="order-info bg-light p-4 rounded mt-4">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="info-item">
                                    <div class="info-label">شماره سفارش</div>
                                    <div class="info-value h5"><?php echo $order->get_order_number(); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-item">
                                    <div class="info-label">تاریخ سفارش</div>
                                    <div class="info-value h5"><?php echo wc_format_datetime($order->get_date_created()); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-item">
                                    <div class="info-label">مبلغ پرداختی</div>
                                    <div class="info-value h5"><?php echo $order->get_formatted_order_total(); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- جزئیات سفارش -->
                <div class="order-details card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">جزئیات سفارش</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>اطلاعات صورتحساب</h5>
                                <address>
                                    <?php echo wp_kses_post($order->get_formatted_billing_address()); ?>
                                    <br/>
                                    <?php echo $order->get_billing_phone(); ?>
                                </address>
                            </div>
                            <div class="col-md-6">
                                <h5>روش پرداخت</h5>
                                <p><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                                
                                <h5 class="mt-3">وضعیت سفارش</h5>
                                <p>
                                    <span class="badge bg-success">
                                        <?php echo wc_get_order_status_name($order->get_status()); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- محصولات سفارش -->
                <div class="order-products card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">محصولات سفارش</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>محصول</th>
                                        <th class="text-center">تعداد</th>
                                        <th class="text-end">قیمت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order->get_items() as $item_id => $item) : 
                                        $product = $item->get_product();
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html($item->get_name()); ?>
                                            <?php if ($product && $product->get_sku()) : ?>
                                                <br/><small>کد: <?php echo esc_html($product->get_sku()); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo esc_html($item->get_quantity()); ?></td>
                                        <td class="text-end"><?php echo wc_price($item->get_total()); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>جمع کل:</strong></td>
                                        <td class="text-end"><strong><?php echo $order->get_formatted_order_total(); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- دکمه‌های اقدام -->
                <div class="text-center mt-5">
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="btn btn-primary btn-lg mx-2">
                        مشاهده سفارش در حساب کاربری
                    </a>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-outline-primary btn-lg mx-2">
                        ادامه خرید
                    </a>
                </div>
                
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php get_footer(); ?>