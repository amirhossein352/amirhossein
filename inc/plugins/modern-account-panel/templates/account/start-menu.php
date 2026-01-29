<?php
/**
 * Custom Account Start Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

$menu_items = wc_get_account_menu_items();
?>

<div class="start-menu" id="startMenu">
    <div class="start-menu-header">
        <div class="start-menu-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="جستجو...">
        </div>
    </div>
    <div class="start-menu-content">
        <div class="start-menu-section">
            <h3>پین شده</h3>
            <div class="start-menu-grid">
                <?php foreach ($menu_items as $endpoint => $label) : 
                    $icon_class = get_endpoint_icon($endpoint);
                ?>
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" 
                       class="start-menu-item" 
                       data-page="<?php echo esc_attr($endpoint); ?>">
                        <div class="start-menu-icon">
                            <i class="<?php echo esc_attr($icon_class); ?>"></i>
                        </div>
                        <span><?php echo esc_html($label); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="start-menu-footer">
            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="start-menu-footer-item" data-page="logout">
                <i class="fas fa-power-off"></i>
                <span>بیرون رفتن</span>
            </a>
        </div>
    </div>
</div>

