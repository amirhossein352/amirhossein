<?php
/**
 * Theme Settings Manager
 * 
 * @package Khane_Irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Theme Settings Class
 */
class Khane_Irani_Theme_Settings {
    
    private $settings_page;
    private $tabs = array();
    
    public function __construct() {
        // Initialize tabs first
        $this->init_tabs();
        
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handler for saving settings only
        add_action('wp_ajax_khane_irani_save_settings', array($this, 'ajax_save_settings'));
    }
    
    /**
     * Initialize all settings tabs
     */
    private function init_tabs() {
        // Initialize tabs directly since classes are already loaded
        $this->tabs = array(
            'header' => new Khane_Irani_Header_Settings(),
            'front-page' => new Khane_Irani_Front_Page_Settings(),
            'about' => new Khane_Irani_About_Settings(),
            'faq' => new Khane_Irani_FAQ_Settings(),
            'contact' => new Khane_Irani_Contact_Settings(),
            'footer' => new Khane_Irani_Footer_Settings(),
        );
    }
    
    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        $this->settings_page = add_theme_page(
            'تنظیمات قالب خانه ایرانی',
            'تنظیمات قالب',
            'manage_options',
            'khane-irani-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        // Initialize all tabs settings to ensure they're all registered
        foreach ($this->tabs as $tab) {
            $tab->init_settings();
        }
        
        // Handle form submission redirect with active tab
        add_filter('wp_redirect', array($this, 'redirect_with_tab'));
        
        // Merge settings when updating via Settings API (non-AJAX submission)
        add_filter('pre_update_option_khane-irani-settings', array($this, 'merge_settings_on_update'), 10, 2);
    }
    
    /**
     * Merge settings when updating via Settings API (non-AJAX submission)
     * This ensures settings from other tabs are preserved
     */
    public function merge_settings_on_update($new_value, $old_value) {
        // If old_value is not an array, use empty array
        if (!is_array($old_value)) {
            $old_value = array();
        }
        
        // If new_value is not an array, return old_value (preserve existing settings)
        if (!is_array($new_value)) {
            return $old_value;
        }
        
        // Helper function to check if array is indexed (numeric keys starting from 0)
        $is_indexed_array = function($arr) {
            if (!is_array($arr) || empty($arr)) {
                return false;
            }
            $keys = array_keys($arr);
            return $keys === range(0, count($arr) - 1);
        };
        
        // Recursive merge function - merge new into old (preserve old values)
        $merge_recursive = function($existing, $new) use (&$merge_recursive, &$is_indexed_array) {
            // Start with existing values (preserve all existing settings)
            $result = $existing;
            
            foreach ($new as $key => $value) {
                if (is_array($value) && isset($existing[$key]) && is_array($existing[$key])) {
                    // If it's an indexed array (like faq_items[0], faq_items[1]), replace entirely
                    if ($is_indexed_array($value) || $is_indexed_array($existing[$key])) {
                        $result[$key] = $value;
                    } else {
                        // For associative arrays, merge recursively
                        $result[$key] = $merge_recursive($existing[$key], $value);
                    }
                } else {
                    // Update with new value (even if empty, to allow clearing fields)
                    $result[$key] = $value;
                }
            }
            return $result;
        };
        
        // Merge new values into old values (preserve settings from other tabs)
        return $merge_recursive($old_value, $new_value);
    }
    
    /**
     * Redirect back to settings page with active tab after save
     */
    public function redirect_with_tab($location) {
        // Only handle our settings page redirects
        if (strpos($location, 'khane-irani-settings') !== false || strpos($location, 'page=khane-irani-settings') !== false || (strpos($location, 'themes.php') !== false && isset($_POST['option_page']) && $_POST['option_page'] === 'khane-irani-settings')) {
            // Get active tab from POST or GET
            $active_tab = isset($_POST['active_tab']) ? sanitize_key($_POST['active_tab']) : (isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'header');
            
            // Build redirect URL with tab parameter
            $redirect_url = admin_url('themes.php?page=khane-irani-settings&tab=' . $active_tab . '&settings-updated=true');
            
            return $redirect_url;
        }
        
        return $location;
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== $this->settings_page && strpos($hook, 'khane-irani-settings') === false) {
            return;
        }
        
        wp_enqueue_style('khane-irani-admin', get_template_directory_uri() . '/css/admin-settings.css', array(), '2.1.1');
        
        // Ensure media library scripts are available before our admin script
        wp_enqueue_media();
        
        wp_enqueue_script(
            'khane-irani-admin',
            get_template_directory_uri() . '/js/admin-settings.js',
            array('jquery', 'media-editor', 'wp-util'),
            '2.1.1',
            true
        );
        
        // Localize script with AJAX URL
        wp_localize_script('khane-irani-admin', 'khaneIraniSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('khane_irani_settings_nonce'),
            'activeTab' => isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'header'
        ));
    }
    
    /**
     * Render settings page - Simple tab system like the plugin
     */
    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'header';
        
        // Ensure the active tab is valid
        if (!isset($this->tabs[$active_tab])) {
            $active_tab = 'header';
        }
        
        // Show success message if settings were saved
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            add_settings_error(
                'khane-irani-settings',
                'settings_updated',
                '✅ تنظیمات با موفقیت ذخیره شد.',
                'success'
            );
        }
        
        ?>
        <div class="wrap khane-irani-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php settings_errors('khane-irani-settings'); ?>
            
            <nav class="nav-tab-wrapper">
                <?php foreach ($this->tabs as $tab_key => $tab): ?>
                    <a href="?page=khane-irani-settings&tab=<?php echo esc_attr($tab_key); ?>" 
                       class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($tab->get_tab_title()); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <div class="tab-content">
                <?php
                // Render only the active tab content using if/elseif like the plugin
                foreach ($this->tabs as $tab_key => $tab) {
                    if ($active_tab === $tab_key) {
                        $this->tabs[$tab_key]->render_tab_content();
                        break;
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for saving settings
     */
    public function ajax_save_settings() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'khane_irani_settings_nonce')) {
            wp_send_json_error(array('message' => 'خطای امنیتی: Nonce نامعتبر است.'));
            return;
        }
        
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'شما مجوز دسترسی ندارید.'));
            return;
        }
        
        // Get settings data
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
        $active_tab = isset($_POST['active_tab']) ? sanitize_key($_POST['active_tab']) : 'header';

        // Debug banners_items
        if (isset($settings['banners_items'])) {
            error_log('Banners items received: ' . print_r($settings['banners_items'], true));
        }

        if (empty($settings)) {
            wp_send_json_error(array('message' => 'هیچ داده‌ای برای ذخیره دریافت نشد.'));
            return;
        }

        // Get existing settings
        $existing = get_option('khane-irani-settings', array());
        if (!is_array($existing)) {
            $existing = array();
        }
        
        // Helper function to check if array is indexed (numeric keys starting from 0)
        $is_indexed_array = function($arr) {
            if (!is_array($arr) || empty($arr)) {
                return false;
            }
            $keys = array_keys($arr);
            return $keys === range(0, count($arr) - 1);
        };
        
        // Recursive function to merge arrays deeply
        $merge_recursive = function($existing, $new) use (&$merge_recursive, &$is_indexed_array, $active_tab) {
            // Start with all existing settings
            $result = $existing;

            // Handle both tab-level and root-level settings
            foreach ($new as $key => $value) {
                if (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                    // If it's an indexed array (like faq_items[0], faq_items[1]), replace entirely
                    if ($is_indexed_array($value) || $is_indexed_array($result[$key])) {
                        $result[$key] = $value;
                    } else {
                        // For associative arrays, merge recursively
                        $result[$key] = $merge_recursive($result[$key], $value);
                    }
                } else {
                    // Otherwise, set new value
                    $result[$key] = $value;
                }
            }

            return $result;
        };
        
        // Sanitize settings recursively
        $sanitize_recursive = function($value, $key = '') use (&$sanitize_recursive) {
            if (is_array($value)) {
                $sanitized = array();
                foreach ($value as $k => $v) {
                    $sanitized[$k] = $sanitize_recursive($v, $k);
                }
                return $sanitized;
            } else {
                // Handle checkbox values (0 or 1)
                if ($value === '0' || $value === '1') {
                    return $value;
                } else {
                    // HTML-allowed fields (descriptions, answers, content, maps)
                    if (preg_match('/(description|answer|content|map|html)/i', $key)) {
                        return is_string($value) ? wp_kses_post($value) : '';
                    }
                    // Sanitize text fields
                    return sanitize_text_field($value);
                }
            }
        };
        
        // Sanitize new settings - pass key for content fields
        $sanitized_settings = array();
        foreach ($settings as $key => $value) {
            $sanitized_settings[$key] = $sanitize_recursive($value, $key);
        }
        
        // Merge with existing settings (preserve other tabs' data)
        $merged = $merge_recursive($existing, $sanitized_settings);
        
        // Save settings
        $result = update_option('khane-irani-settings', $merged);
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => '✅ تنظیمات با موفقیت ذخیره شد.',
                'tab' => isset($_POST['active_tab']) ? sanitize_key($_POST['active_tab']) : 'header'
            ));
        } else {
            // Even if update_option returns false, it might be because value didn't change
            // Check if values are actually saved
            $saved = get_option('khane-irani-settings', array());
            if ($saved === $existing) {
                wp_send_json_success(array(
                    'message' => '✅ تنظیمات با موفقیت ذخیره شد.',
                    'tab' => isset($_POST['active_tab']) ? sanitize_key($_POST['active_tab']) : 'header'
                ));
            } else {
                wp_send_json_error(array('message' => 'خطا در ذخیره تنظیمات. لطفاً دوباره تلاش کنید.'));
            }
        }
    }
    
}

// Initialize the theme settings
new Khane_Irani_Theme_Settings();
