<?php
/**
 * Custom Account Taskbar
 */

if (!defined('ABSPATH')) {
    exit;
}

$menu_items = wc_get_account_menu_items();
$current_endpoint = WC()->query->get_current_endpoint();

if (!function_exists('get_endpoint_icon')) {
    function get_endpoint_icon($endpoint) {
        $icons = array(
            'dashboard' => '🏠',
            'orders' => '🛒',
            'downloads' => '📥',
            'edit-address' => '📍',
            'payment-methods' => '💳',
            'edit-account' => '👤',
        );
        return isset($icons[$endpoint]) ? $icons[$endpoint] : '⚙️';
    }
}

if (!function_exists('get_endpoint_emoji')) {
    function get_endpoint_emoji($endpoint) {
        $emojis = array(
            'dashboard' => '🏠',
            'orders' => '🛒',
            'downloads' => '📥',
            'edit-address' => '📍',
            'payment-methods' => '💳',
            'edit-account' => '👤',
        );
        return isset($emojis[$endpoint]) ? $emojis[$endpoint] : '⚙️';
    }
}
?>

<div class="modern-account-panel-taskbar taskbar" id="taskbar">
    <div class="taskbar-left">
        <button class="taskbar-item taskbar-start" id="taskbarStart" title="منو">
            <span class="taskbar-emoji">⚡</span>
            <span class="taskbar-start-label">منو</span>
        </button>
    </div>
    
    <div class="taskbar-center">
        <div class="taskbar-apps">
            <?php foreach ($menu_items as $endpoint => $label) : 
                $is_active = ($current_endpoint === $endpoint || ($current_endpoint === '' && $endpoint === 'dashboard'));
                $emoji = get_endpoint_emoji($endpoint);
            ?>
                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" 
                   class="taskbar-app <?php echo $is_active ? 'active' : ''; ?>" 
                   data-page="<?php echo esc_attr($endpoint); ?>" 
                   title="<?php echo esc_attr($label); ?>">
                    <span class="taskbar-emoji"><?php echo $emoji; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="taskbar-right">
        <div class="taskbar-tray">
            <button class="taskbar-icon" id="taskbarNotifications" title="اعلان‌ها">
                <span class="taskbar-emoji">🔔</span>
                <span class="taskbar-badge">0</span>
            </button>
            <button class="taskbar-icon" id="taskbarMessages" title="پیام‌ها">
                <span class="taskbar-emoji">✉️</span>
                <span class="taskbar-badge">0</span>
            </button>
            <div class="taskbar-divider"></div>
            <div class="taskbar-time" id="taskbarTime">
                <div class="time-display" id="timeDisplay">--:--</div>
                <div class="date-display" id="dateDisplay">--/--/----</div>
            </div>
        </div>
    </div>
</div>

