<?php
/**
 * My Account navigation - Modern Account Panel Style
 *
 * This template overrides the default WooCommerce navigation with custom styling
 * while preserving all WooCommerce functionality and menu items.
 *
 * @see     https://woo.com/document/template-structure/
 * @package Modern Account Panel
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_account_navigation');

// Debug: Log navigation template loading
error_log('Modern Account Panel: navigation.php template loaded');

$current_user = wp_get_current_user();

// Get menu items - ensure WooCommerce is loaded
if (!function_exists('wc_get_account_menu_items')) {
    error_log('Modern Account Panel: wc_get_account_menu_items function not found!');
    $menu_items = array();
} else {
    $menu_items = wc_get_account_menu_items();
    // Force refresh menu items if empty
    if (empty($menu_items) && function_exists('WC')) {
        WC()->query->init_query_vars();
        WC()->query->add_endpoints();
        $menu_items = wc_get_account_menu_items();
    }
}

// Get current endpoint
if (!function_exists('WC') || !WC()->query) {
    error_log('Modern Account Panel: WooCommerce query object not found!');
    $current_endpoint = '';
} else {
    $current_endpoint = WC()->query->get_current_endpoint();
    // If endpoint is empty, check if we're on dashboard
    if (empty($current_endpoint)) {
        global $wp;
        if (isset($wp->query_vars) && !empty($wp->query_vars)) {
            $current_endpoint = array_keys($wp->query_vars)[0] ?? 'dashboard';
        } else {
            $current_endpoint = 'dashboard';
        }
    }
}

// Debug: Log menu items
if (empty($menu_items)) {
    error_log('Modern Account Panel: No menu items found. User ID: ' . get_current_user_id());
    error_log('Modern Account Panel: Is user logged in: ' . (is_user_logged_in() ? 'yes' : 'no'));
    error_log('Modern Account Panel: WooCommerce active: ' . (class_exists('WooCommerce') ? 'yes' : 'no'));
    error_log('Modern Account Panel: Current endpoint: ' . $current_endpoint);
} else {
    error_log('Modern Account Panel: Found ' . count($menu_items) . ' menu items: ' . implode(', ', array_keys($menu_items)));
    error_log('Modern Account Panel: Current endpoint: ' . $current_endpoint);
}

if (!function_exists('get_endpoint_icon')) {
    /**
     * Get icon class for endpoint
     */
    function get_endpoint_icon($endpoint) {
        $icons = array(
            'dashboard' => 'fas fa-home',
            'orders' => 'fas fa-shopping-basket',
            'downloads' => 'fas fa-cloud-download-alt',
            'edit-address' => 'fas fa-map-marker-alt',
            'payment-methods' => 'fas fa-credit-card',
            'edit-account' => 'fas fa-user',
        );

        // Support custom endpoints from other plugins
        $custom_icons = apply_filters('modern_account_panel_endpoint_icons', array());
        if (isset($custom_icons[$endpoint])) {
            return $custom_icons[$endpoint];
        }

        return isset($icons[$endpoint]) ? $icons[$endpoint] : 'fas fa-circle';
    }
}
?>

<aside class="modern-account-panel-sidebar sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-cube"></i>
            <span><?php echo esc_html(get_bloginfo('name')); ?></span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <?php 
            if (!empty($menu_items)) {
                foreach ($menu_items as $endpoint => $label) : 
                    $is_active = ($current_endpoint === $endpoint || ($current_endpoint === '' && $endpoint === 'dashboard'));
                    $icon_class = get_endpoint_icon($endpoint);
                ?>
                    <li class="nav-item <?php echo $is_active ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" class="nav-link" data-page="<?php echo esc_attr($endpoint); ?>">
                            <i class="<?php echo esc_attr($icon_class); ?>"></i>
                            <span><?php echo esc_html($label); ?></span>
                        </a>
                    </li>
                <?php 
                endforeach;
            } else {
                // Fallback: Show default WooCommerce menu structure if menu_items is empty
                ?>
                <li class="nav-item">
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>حساب کاربری</span>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
        
        <div class="nav-divider"></div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="nav-link" data-page="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>بیرون رفتن</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<?php
do_action('woocommerce_after_account_navigation');
?>

