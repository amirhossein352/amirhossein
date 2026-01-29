<?php
/**
 * Custom Account Sidebar
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$menu_items = wc_get_account_menu_items();
$current_endpoint = WC()->query->get_current_endpoint();

// Debug: Log if menu items are empty
if (empty($menu_items)) {
    error_log('Modern Account Panel: No menu items found. User ID: ' . get_current_user_id());
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
            <?php foreach ($menu_items as $endpoint => $label) : 
                $is_active = ($current_endpoint === $endpoint || ($current_endpoint === '' && $endpoint === 'dashboard'));
                $icon_class = get_endpoint_icon($endpoint);
            ?>
                <li class="nav-item <?php echo $is_active ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" class="nav-link" data-page="<?php echo esc_attr($endpoint); ?>">
                        <i class="<?php echo esc_attr($icon_class); ?>"></i>
                        <span><?php echo esc_html($label); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
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
        
        return isset($icons[$endpoint]) ? $icons[$endpoint] : 'fas fa-circle';
    }
}

