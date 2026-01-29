<?php
/**
 * Custom Account Header
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
?>

<header class="modern-account-panel-header header">
    <div class="header-left">
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="منو">
            <i class="fas fa-bars"></i>
        </button>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="جستجو...">
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-item user-profile">
            <div class="profile-dropdown" id="profileDropdown">
                <div class="avatar">
                    <?php echo get_avatar($current_user->ID, 40); ?>
                </div>
                <span class="username"><?php echo esc_html($current_user->display_name); ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="dropdown-menu" id="profileMenu">
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>پروفایل</span>
                </a>
                <a href="<?php echo esc_url(admin_url()); ?>" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>تنظیمات</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>خروج</span>
                </a>
            </div>
        </div>
    </div>
</header>

