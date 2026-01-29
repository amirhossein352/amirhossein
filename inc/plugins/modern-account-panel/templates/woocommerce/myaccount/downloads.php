<?php
/**
 * Custom Downloads Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$downloads = wc_get_customer_available_downloads();
?>

<div class="page" id="downloads" data-page="downloads">
    <div class="page-header">
        <h1 class="page-title">دانلودها</h1>
        <div class="breadcrumb">
            <a href="<?php echo esc_url(home_url()); ?>">خانه</a>
            <span>/</span>
            <span>دانلودها</span>
        </div>
    </div>

    <!-- Downloads Info -->
    <div class="content-card">
        <div class="card-header">
            <h3>فایل‌های قابل دانلود</h3>
            <p class="card-subtitle">فایل‌های دیجیتالی که خریداری کرده‌اید در اینجا قابل دسترسی هستند.</p>
        </div>
        <div class="card-body">
            <?php if (!empty($downloads)) : ?>
                <div class="downloads-list" id="downloadsList">
                    <?php foreach ($downloads as $download) : 
                        $order = wc_get_order($download['order_id']);
                        $expires = isset($download['access_expires']) ? $download['access_expires'] : null;
                        $is_expired = $expires && strtotime($expires) < time();
                    ?>
                        <div class="download-item <?php echo $is_expired ? 'expired' : ''; ?>">
                            <div class="download-info">
                                <div class="download-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="download-details">
                                    <h4><?php echo esc_html($download['product_name']); ?></h4>
                                    <p class="download-meta">
                                        <span>سفارش: <a href="<?php echo esc_url($order->get_view_order_url()); ?>" data-order="<?php echo esc_attr($download['order_id']); ?>">#<?php echo esc_html($order->get_order_number()); ?></a></span>
                                        <span>تاریخ خرید: <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></span>
                                    </p>
                                    <p class="download-description"><?php echo esc_html($download['download_name']); ?></p>
                                </div>
                            </div>
                            <div class="download-actions">
                                <?php if (!$is_expired) : ?>
                                    <a href="<?php echo esc_url($download['download_url']); ?>" class="btn btn-primary download-file" data-file="<?php echo esc_attr($download['download_id']); ?>">
                                        <i class="fas fa-download"></i> دانلود
                                    </a>
                                    <?php if ($expires) : ?>
                                        <span class="download-expires">منقضی می‌شود: <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($expires))); ?></span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-ban"></i> منقضی شده
                                    </button>
                                    <span class="download-expires expired-text">منقضی شده: <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($expires))); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state" id="emptyDownloads">
                    <div class="empty-icon">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <h3>فایلی برای دانلود وجود ندارد</h3>
                    <p>هنگامی که محصول دیجیتالی خریداری کنید، فایل‌های قابل دانلود در اینجا نمایش داده می‌شوند.</p>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-primary">مشاهده محصولات</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

