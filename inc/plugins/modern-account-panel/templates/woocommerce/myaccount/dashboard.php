<?php
/**
 * Custom Dashboard Template - Modern & Minimal Design
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$customer = new WC_Customer($current_user->ID);

// Get stats
$orders_count = wc_get_customer_order_count($current_user->ID);
$total_spent = wc_get_customer_total_spent($current_user->ID);

// Count addresses
$addresses_count = 0;
if ($customer->get_billing_address_1()) {
    $addresses_count++;
}
if ($customer->get_shipping_address_1()) {
    $addresses_count++;
}

// Get recent orders
$recent_orders = wc_get_orders(array(
    'customer' => $current_user->ID,
    'limit' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
));

// Get best selling products - واقعی از ووکامرس بر اساس total_sales
global $wpdb;
$best_selling_product_ids = $wpdb->get_col($wpdb->prepare("
    SELECT postmeta.post_id 
    FROM {$wpdb->postmeta} AS postmeta
    INNER JOIN {$wpdb->posts} AS posts ON postmeta.post_id = posts.ID
    WHERE postmeta.meta_key = 'total_sales'
    AND posts.post_type = 'product'
    AND posts.post_status = 'publish'
    AND postmeta.meta_value > 0
    ORDER BY CAST(postmeta.meta_value AS SIGNED) DESC
    LIMIT %d
", 10));

$best_selling_products = array();
if (!empty($best_selling_product_ids)) {
    foreach ($best_selling_product_ids as $product_id) {
        $product = wc_get_product($product_id);
        if ($product && $product->is_visible()) {
            $best_selling_products[] = $product;
        }
    }
}
?>

<div class="page modern-dashboard" id="dashboard" data-page="dashboard">
    <!-- Hero Welcome Section -->
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="welcome-text">
                <h1>خوش آمدید، <span class="username"><?php echo esc_html($current_user->display_name); ?></span></h1>
                <p>همه چیز در یکجا برای مدیریت بهتر حساب کاربری شما</p>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-item">
            <div class="stat-icon-wrapper">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo esc_html($orders_count); ?></div>
                <div class="stat-label">سفارش‌ها</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo wc_price($total_spent); ?></div>
                <div class="stat-label">مجموع خریدها</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Recent Orders -->
        <div class="dashboard-card orders-card">
            <div class="card-header-modern">
                <h2>سفارش‌های اخیر</h2>
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="view-all-link" data-page="orders">
                    مشاهده همه
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <div class="card-body-modern">
                <?php if (!empty($recent_orders)) : ?>
                    <div class="orders-list">
                        <?php foreach ($recent_orders as $order) : ?>
                            <div class="order-item">
                                <div class="order-number">
                                    <span class="order-label">سفارش</span>
                                    <span class="order-id">#<?php echo esc_html($order->get_order_number()); ?></span>
                                </div>
                                <div class="order-details">
                                    <div class="order-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
                                    </div>
                                    <div class="order-status">
                                        <span class="status-badge status-<?php echo esc_attr($order->get_status()); ?>">
                                            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-amount">
                                    <?php echo $order->get_formatted_order_total(); ?>
                                </div>
                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="order-action-btn" data-order="<?php echo esc_attr($order->get_id()); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <p>هنوز سفارشی ثبت نکرده‌اید</p>
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn-modern">مشاهده محصولات</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card quick-actions-card">
            <div class="card-header-modern">
                <h2>دسترسی سریع</h2>
            </div>
            <div class="card-body-modern">
                <div class="quick-actions-grid">
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="quick-action-btn" data-page="orders">
                        <i class="fas fa-shopping-basket"></i>
                        <span>سفارش‌ها</span>
                    </a>
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="quick-action-btn" data-page="account-details">
                        <i class="fas fa-user"></i>
                        <span>پروفایل</span>
                    </a>
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url('customer-logout')); ?>" class="quick-action-btn logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>خروج</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Selling Products -->
    <?php if (!empty($best_selling_products)) : ?>
    <div class="products-section">
        <div class="section-header">
            <h2 class="section-title">پرفروش‌ترین محصولات</h2>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="view-all-link">
                مشاهده همه
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="products-carousel-wrapper">
            <div class="products-carousel-container">
                <button class="carousel-btn carousel-prev" aria-label="قبلی">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="products-carousel">
                    <div class="products-track">
                        <?php foreach ($best_selling_products as $product) : 
                            $product_id = $product->get_id();
                            $product_url = $product->get_permalink();
                            $product_image = $product->get_image('woocommerce_thumbnail');
                            $product_title = $product->get_name();
                            $product_price = $product->get_price_html();
                            $total_sales = $product->get_total_sales();
                        ?>
                            <div class="product-slide">
                                <a href="<?php echo esc_url($product_url); ?>" class="product-card-modern">
                                    <div class="product-image-modern">
                                        <?php echo $product_image ? $product_image : '<div class="product-placeholder"><i class="fas fa-image"></i></div>'; ?>
                                    </div>
                                    <div class="product-info-modern">
                                        <h3 class="product-title-modern"><?php echo esc_html($product_title); ?></h3>
                                        <div class="product-footer">
                                            <div class="product-price-modern"><?php echo $product_price; ?></div>
                                            <?php if ($total_sales > 0) : ?>
                                            <div class="product-sales-badge">
                                                <i class="fas fa-fire"></i>
                                                <?php echo esc_html($total_sales); ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-next" aria-label="بعدی">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
