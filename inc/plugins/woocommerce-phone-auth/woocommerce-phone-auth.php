<?php
/**
 * Plugin Name: WooCommerce Phone Authentication
 * Plugin URI: https://example.com
 * Description: افزونه تخصصی ورود و ثبت نام با شماره تماس و گوگل برای ووکامرس
 * Version: 3.0.0
 * Author: ali ilkhani
 * Text Domain: woocommerce-phone-auth
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * Requires Plugins: woocommerce
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants - Updated for theme integration
define('WCPA_PLUGIN_URL', get_template_directory_uri() . '/inc/plugins/woocommerce-phone-auth/');
define('WCPA_PLUGIN_PATH', get_template_directory() . '/inc/plugins/woocommerce-phone-auth/');
define('WCPA_VERSION', '3.1.0');

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    // Don't show notice if WooCommerce is not active - just return silently
    return;
}

function wcpa_woocommerce_missing_notice() {
    echo '<div class="error"><p><strong>' . __('WooCommerce Phone Authentication requires WooCommerce to be installed and active.', 'woocommerce-phone-auth') . '</strong></p></div>';
}

// Include required files
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-main.php';
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-auth.php';
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-sms.php';
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-shortcode.php';
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-styles.php';
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-pages.php';
require_once WCPA_PLUGIN_PATH . 'includes/class-wcpa-google-auth.php';
require_once WCPA_PLUGIN_PATH . 'admin/class-wcpa-admin.php';

// Initialize the plugin
function wcpa_init() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    new WCPA_Main();
    
    // Debug: Test shortcodes
    add_action('wp_footer', function() {
        global $shortcode_tags;
        echo '<!-- Shortcodes: ' . (isset($shortcode_tags['wcpa_auth_desktop']) ? 'Desktop YES' : 'Desktop NO') . ', ' . (isset($shortcode_tags['wcpa_auth_mobile']) ? 'Mobile YES' : 'Mobile NO') . ' -->';
    });
}

// Initialize immediately if WooCommerce is already loaded, otherwise wait for plugins_loaded
if (class_exists('WooCommerce')) {
    wcpa_init();
} else {
    // Initialize after WooCommerce is loaded
    add_action('plugins_loaded', 'wcpa_init', 20);
}

// Declare HPOS compatibility
add_action('before_woocommerce_init', function() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', get_template_directory() . '/inc/plugins/woocommerce-phone-auth/woocommerce-phone-auth.php', true);
    }
});

// Activation function (called from theme)
function wcpa_activate() {
    // Create necessary database tables
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Verification codes table
    $verification_table = $wpdb->prefix . 'wcpa_verification_codes';
    $verification_sql = "CREATE TABLE $verification_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        phone varchar(20) NOT NULL,
        code varchar(10) NOT NULL,
        expires_at datetime NOT NULL,
        used tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY code (code)
    ) $charset_collate;";
    
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($verification_sql);
    
    // Set default options
    add_option('wcpa_sms_provider', 'faraz'); // faraz, ipanel, or melipayamak
    add_option('wcpa_sms_api_key', '');
    add_option('wcpa_sms_sender', '');
    add_option('wcpa_sms_pattern_code', '');
    add_option('wcpa_sms_username', '');
    add_option('wcpa_sms_password', '');
    add_option('wcpa_code_expiry', 300); // 5 minutes
    add_option('wcpa_resend_cooldown', 60); // 60 seconds
    add_option('wcpa_myaccount_url', ''); // Custom myaccount URL
    add_option('wcpa_require_registration', 'yes'); // Require registration for checkout
    add_option('wcpa_google_client_id', ''); // Google OAuth client ID
    add_option('wcpa_google_client_secret', ''); // Google OAuth client secret
    add_option('wcpa_enable_google_auth', 'no'); // Enable Google authentication
    
    // Create custom login page
    $existing_page = get_page_by_path('phone-login');
    
    if (!$existing_page) {
        $page_data = array(
            'post_title' => 'ورود و ثبت نام',
            'post_content' => '[wcpa_phone_login_form]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => 'phone-login',
            'post_author' => 1
        );
        
        $login_page_id = wp_insert_post($page_data);
        
        if ($login_page_id) {
            // Don't replace WooCommerce myaccount page, just create our custom login page
            // The custom page will be used for login/register, but myaccount stays as WooCommerce default
        }
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation function (called from theme)
function wcpa_deactivate() {
    // Clean up scheduled events
    wp_clear_scheduled_hook('wcpa_cleanup_expired_codes');
}
