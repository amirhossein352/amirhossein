<?php
/**
 * Custom Edit Account Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$customer = new WC_Customer($current_user->ID);
?>

<div class="page" id="account-details" data-page="account-details">
    <div class="page-header">
        <h1 class="page-title">جزئیات حساب</h1>
        <div class="breadcrumb">
            <a href="<?php echo esc_url(home_url()); ?>">خانه</a>
            <span>/</span>
            <span>جزئیات حساب</span>
        </div>
    </div>

    <!-- Account Details Form -->
    <div class="content-card">
        <div class="card-header">
            <h3>اطلاعات شخصی</h3>
            <p class="card-subtitle">اطلاعات حساب کاربری خود را در اینجا ویرایش کنید.</p>
        </div>
        <div class="card-body">
            <?php wc_get_template('myaccount/form-edit-account.php', array('user' => $current_user)); ?>
        </div>
    </div>

    <!-- Account Statistics -->
    <div class="content-grid">
        <div class="content-card">
            <div class="card-header">
                <h3>آمار حساب</h3>
            </div>
            <div class="card-body">
                <div class="account-stats">
                    <div class="stat-item">
                        <span class="stat-label">تاریخ عضویت:</span>
                        <span class="stat-value"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($current_user->user_registered))); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">تعداد سفارش‌ها:</span>
                        <span class="stat-value"><?php echo esc_html(wc_get_customer_order_count($current_user->ID)); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مجموع خرید:</span>
                        <span class="stat-value"><?php echo wc_price(wc_get_customer_total_spent($current_user->ID)); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">وضعیت حساب:</span>
                        <span class="stat-value badge badge-success">فعال</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3>تنظیمات اعلان‌ها</h3>
            </div>
            <div class="card-body">
                <div class="notification-settings">
                    <label class="checkbox-label">
                        <input type="checkbox" name="email_notifications" checked>
                        <span>دریافت ایمیل برای سفارش‌های جدید</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="sms_notifications">
                        <span>دریافت پیامک برای سفارش‌های جدید</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="newsletter" checked>
                        <span>عضویت در خبرنامه</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="promotional_emails">
                        <span>دریافت ایمیل‌های تبلیغاتی</span>
                    </label>
                </div>
                <div class="form-actions" style="margin-top: 1.5rem;">
                    <button type="button" class="btn btn-primary" id="saveNotificationsBtn">ذخیره تنظیمات</button>
                </div>
            </div>
        </div>
    </div>
</div>

