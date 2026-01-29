<?php
/**
 * Plugin Name: Modern Account Panel
 * Plugin URI: https://example.com
 * Description: یک پنل حساب کاربری حرفه‌ای و مدرن برای ووکامرس با طراحی زیبا و واکنش‌گرا
 * Version: 1.0.0
 * Author: Ali ilkhani
 * Author URI: https://aliilkhani.ir
 * Text Domain: modern-account-panel
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants - Updated for theme integration
define('MODERN_ACCOUNT_PANEL_VERSION', '1.0.0');
define('MODERN_ACCOUNT_PANEL_PATH', get_template_directory() . '/inc/plugins/modern-account-panel/');
define('MODERN_ACCOUNT_PANEL_URL', get_template_directory_uri() . '/inc/plugins/modern-account-panel/');
define('MODERN_ACCOUNT_PANEL_BASENAME', 'modern-account-panel');

/**
 * Check if WooCommerce is active
 */
function modern_account_panel_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Modern Account Panel requires WooCommerce to be installed and activated.', 'modern-account-panel'); ?></p>
            </div>
            <?php
        });
        return false;
    }
    return true;
}

/**
 * Declare WooCommerce compatibility
 */
add_action('before_woocommerce_init', function() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', get_template_directory() . '/inc/plugins/modern-account-panel/modern-account-panel.php', true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_product_tables', get_template_directory() . '/inc/plugins/modern-account-panel/modern-account-panel.php', true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', get_template_directory() . '/inc/plugins/modern-account-panel/modern-account-panel.php', true);
    }
});

/**
 * Initialize the plugin
 */
function modern_account_panel_init() {
    if (!modern_account_panel_check_woocommerce()) {
        return;
    }

    // Load the main plugin class
    require_once MODERN_ACCOUNT_PANEL_PATH . 'includes/class-modern-account-panel.php';
    
    // Initialize the plugin
    $plugin = Modern_Account_Panel::get_instance();
    $plugin->run();
}

// Initialize immediately if WooCommerce is already loaded, otherwise wait for plugins_loaded
if (class_exists('WooCommerce')) {
    modern_account_panel_init();
} else {
    // Initialize after WooCommerce is loaded
    add_action('plugins_loaded', 'modern_account_panel_init', 10);
}

/**
 * Activation function (called from theme)
 */
function modern_account_panel_activate() {
    if (!modern_account_panel_check_woocommerce()) {
        // Don't deactivate - just return silently
        return;
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Deactivation function (called from theme)
 */
function modern_account_panel_deactivate() {
    flush_rewrite_rules();
}

