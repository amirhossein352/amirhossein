<?php
/**
 * Custom Orders Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$customer_orders = wc_get_orders(array(
    'customer' => $current_user->ID,
    'limit' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<div class="page" id="orders" data-page="orders">
    <div class="page-header">
        <h1 class="page-title">سفارش‌ها</h1>
        <div class="breadcrumb">
            <a href="<?php echo esc_url(home_url()); ?>">خانه</a>
            <span>/</span>
            <span>سفارش‌ها</span>
        </div>
    </div>

    <!-- Orders Filter -->
    <div class="content-card">
        <div class="card-header">
            <h3>فیلتر سفارش‌ها</h3>
        </div>
        <div class="card-body">
            <div class="orders-filter">
                <div class="filter-group">
                    <label>جستجو:</label>
                    <input type="text" id="orderSearch" placeholder="شماره سفارش، نام محصول...">
                </div>
                <div class="filter-group">
                    <label>وضعیت:</label>
                    <select id="orderStatus">
                        <option value="">همه</option>
                        <option value="pending">در انتظار پرداخت</option>
                        <option value="processing">در حال پردازش</option>
                        <option value="completed">تکمیل شده</option>
                        <option value="cancelled">لغو شده</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary" id="applyFilter">اعمال فیلتر</button>
                    <button class="btn btn-secondary" id="resetFilter">پاک کردن</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="content-card">
        <div class="card-header">
            <h3>لیست سفارش‌ها</h3>
            <div class="card-actions">
                <button class="btn-icon" id="refreshOrders">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($customer_orders)) : ?>
                <div class="table-responsive">
                    <table class="data-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>شماره سفارش</th>
                                <th>تاریخ</th>
                                <th>وضعیت</th>
                                <th>مبلغ کل</th>
                                <th>روش پرداخت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <?php foreach ($customer_orders as $order) : ?>
                                <tr>
                                    <td><a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="order-link" data-order="<?php echo esc_attr($order->get_id()); ?>">#<?php echo esc_html($order->get_order_number()); ?></a></td>
                                    <td><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></td>
                                    <td><span class="badge badge-<?php echo esc_attr($order->get_status()); ?>"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span></td>
                                    <td><?php echo $order->get_formatted_order_total(); ?></td>
                                    <td><?php echo esc_html($order->get_payment_method_title()); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="btn btn-sm btn-primary view-order-detail" data-order="<?php echo esc_attr($order->get_id()); ?>">
                                                <i class="fas fa-eye"></i> مشاهده
                                            </a>
                                            <?php if ($order->is_download_permitted()) : ?>
                                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="btn btn-sm btn-secondary download-invoice" data-order="<?php echo esc_attr($order->get_id()); ?>">
                                                    <i class="fas fa-download"></i> فاکتور
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p>هنوز سفارشی ثبت نکرده‌اید.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

