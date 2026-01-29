<?php
/**
 * Custom Pages Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Pages {
    
    private $login_page_id;
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        // Don't redirect - show form on my-account page instead
        // add_action('template_redirect', array($this, 'redirect_woocommerce_pages'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_page_scripts'));
        add_action('wp_logout', array($this, 'redirect_after_logout'));
        
        // Override WooCommerce myaccount content for non-logged-in users
        add_action('woocommerce_before_customer_login_form', array($this, 'override_myaccount_content'), 5);
        add_filter('woocommerce_locate_template', array($this, 'override_login_template'), 10, 3);
    }
    
    public function init() {
        // Ensure WooCommerce myaccount page exists
        $this->ensure_woocommerce_myaccount_page();
        
        // Create custom login page if it doesn't exist
        $this->create_login_page();
        
        // Add rewrite rules for custom pages
        add_rewrite_rule('^phone-login/?$', 'index.php?wcpa_phone_login=1', 'top');
        add_rewrite_rule('^phone-register/?$', 'index.php?wcpa_phone_register=1', 'top');
        
        // Add query vars
        add_filter('query_vars', array($this, 'add_query_vars'));
        
        // Handle custom page templates
        add_filter('template_include', array($this, 'custom_page_template'));
        
        // Ensure WooCommerce is properly loaded
        add_action('wp_loaded', array($this, 'ensure_woocommerce_loaded'));
    }
    
    public function ensure_woocommerce_loaded() {
        // Make sure WooCommerce is loaded and myaccount page is accessible
        if (function_exists('wc_get_page_permalink')) {
            $myaccount_url = wc_get_page_permalink('myaccount');
            if ($myaccount_url) {
                error_log('WCPA: WooCommerce myaccount URL: ' . $myaccount_url);
            }
        }
        
        // Check if WooCommerce shortcodes are available
        if (shortcode_exists('woocommerce_my_account')) {
            error_log('WCPA: WooCommerce myaccount shortcode is available');
        } else {
            error_log('WCPA: WooCommerce myaccount shortcode is NOT available');
        }
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'wcpa_phone_login';
        $vars[] = 'wcpa_phone_register';
        return $vars;
    }
    
    public function create_login_page() {
        // Check if page already exists
        $existing_page = get_page_by_path('phone-login');
        
        if (!$existing_page) {
            // Create the page
            $page_data = array(
                'post_title' => 'ورود و ثبت نام',
                'post_content' => '[wcpa_phone_login_form]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'phone-login',
                'post_author' => 1
            );
            
            $this->login_page_id = wp_insert_post($page_data);
            
            if ($this->login_page_id) {
                error_log('WCPA: Created custom login page with ID: ' . $this->login_page_id);
                // Flush rewrite rules
                flush_rewrite_rules();
            } else {
                error_log('WCPA: Failed to create custom login page');
            }
        } else {
            $this->login_page_id = $existing_page->ID;
            error_log('WCPA: Using existing custom login page with ID: ' . $this->login_page_id);
        }
    }
    
    public function redirect_woocommerce_pages() {
        // Only redirect if user is not logged in and trying to access my-account
        if (is_page('my-account') && !is_user_logged_in()) {
            // Always redirect to our custom login page
            $custom_page_url = get_permalink($this->login_page_id);
            if ($custom_page_url) {
                error_log('WCPA: Redirecting to custom login page: ' . $custom_page_url);
                wp_redirect($custom_page_url);
                exit;
            } else {
                error_log('WCPA: Custom login page URL not found. Page ID: ' . $this->login_page_id);
            }
        }
        
        // If user is logged in and on my-account page, let WooCommerce handle it normally
        if (is_page('my-account') && is_user_logged_in()) {
            // Don't redirect - let WooCommerce handle the page normally
            return;
        }
        
        // Prevent infinite redirect on phone-login page
        if (is_page('phone-login')) {
            // If user is logged in, redirect to myaccount
            if (is_user_logged_in()) {
                $myaccount_url = '';
                if (function_exists('wc_get_page_permalink')) {
                    $myaccount_url = wc_get_page_permalink('myaccount');
                }
                if (!$myaccount_url) {
                    $myaccount_id = get_option('woocommerce_myaccount_page_id');
                    $myaccount_url = $myaccount_id ? get_permalink($myaccount_id) : home_url('/my-account/');
                }
                if ($myaccount_url && strpos($myaccount_url, 'phone-login') === false) {
                    wp_redirect($myaccount_url);
                    exit;
                }
            }
            
            // Remove redirected parameter if exists
            if (isset($_GET['redirected'])) {
                $clean_url = remove_query_arg('redirected');
                if ($clean_url !== $_SERVER['REQUEST_URI']) {
                    wp_redirect($clean_url);
                    exit;
                }
            }
        }
    }
    
    public function custom_page_template($template) {
        if (get_query_var('wcpa_phone_login') || get_query_var('wcpa_phone_register')) {
            $custom_template = WCPA_PLUGIN_PATH . 'templates/page-phone-login.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    public function enqueue_page_scripts() {
        if (is_page($this->login_page_id) || get_query_var('wcpa_phone_login') || get_query_var('wcpa_phone_register')) {
            wp_enqueue_script('wcpa-main');
            wp_enqueue_style('wcpa-style');
        }
        
        // Also enqueue on my-account page for non-logged-in users
        if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in()) {
            wp_enqueue_script('wcpa-main');
            wp_enqueue_style('wcpa-style');
        }
    }
    
    public function get_login_page_url() {
        if ($this->login_page_id) {
            return get_permalink($this->login_page_id);
        }
        return home_url('/phone-login/');
    }
    
    public function get_login_page_id() {
        return $this->login_page_id;
    }
    
    public function ensure_woocommerce_myaccount_page() {
        // Just ensure WooCommerce myaccount page exists - don't modify content
        $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
        
        if (!$myaccount_page_id || !get_post($myaccount_page_id)) {
            // Create WooCommerce myaccount page if it doesn't exist
            $page_data = array(
                'post_title' => 'حساب کاربری',
                'post_content' => '[woocommerce_my_account]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'my-account',
                'post_author' => 1
            );
            
            $myaccount_page_id = wp_insert_post($page_data);
            
            if ($myaccount_page_id) {
                update_option('woocommerce_myaccount_page_id', $myaccount_page_id);
                error_log('WCPA: Created new myaccount page with ID: ' . $myaccount_page_id);
            }
        } else {
            // Ensure the page has the correct content
            $page = get_post($myaccount_page_id);
            if ($page && strpos($page->post_content, '[woocommerce_my_account]') === false) {
                $page->post_content = '[woocommerce_my_account]';
                wp_update_post($page);
                error_log('WCPA: Updated myaccount page content');
            }
        }
        
        return $myaccount_page_id;
    }
    
    public function redirect_after_logout() {
        // Redirect to home page after logout
        wp_redirect(home_url());
        exit;
    }
    
    public function test_myaccount_access() {
        // Test function to check myaccount page access
        $myaccount_id = get_option('woocommerce_myaccount_page_id');
        $is_logged_in = is_user_logged_in();
        $current_user_id = get_current_user_id();
        
        error_log('WCPA Test - Myaccount ID: ' . $myaccount_id);
        error_log('WCPA Test - User logged in: ' . ($is_logged_in ? 'YES' : 'NO'));
        error_log('WCPA Test - Current user ID: ' . $current_user_id);
        
        if ($myaccount_id) {
            $page = get_post($myaccount_id);
            if ($page) {
                error_log('WCPA Test - Page exists: YES, Status: ' . $page->post_status);
                error_log('WCPA Test - Page content: ' . substr($page->post_content, 0, 100));
            } else {
                error_log('WCPA Test - Page exists: NO');
            }
        }
        
        return array(
            'myaccount_id' => $myaccount_id,
            'is_logged_in' => $is_logged_in,
            'user_id' => $current_user_id
        );
    }
    
    public function force_myaccount_fix() {
        // Force fix myaccount page
        $myaccount_id = get_option('woocommerce_myaccount_page_id');
        
        if ($myaccount_id) {
            $page = get_post($myaccount_id);
            if ($page) {
                $page->post_content = '[woocommerce_my_account]';
                wp_update_post($page);
                error_log('WCPA: Force fixed myaccount page content');
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Override WooCommerce myaccount content for non-logged-in users
     * Show phone auth form instead of default WooCommerce login form
     */
    public function override_myaccount_content() {
        // Only override if user is not logged in and on my-account page
        if (!is_user_logged_in() && function_exists('is_account_page') && is_account_page()) {
            // Remove default WooCommerce login form output
            remove_action('woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10);
            
            // Output the phone login form instead
            echo '<div class="wcpa-myaccount-login-wrapper" style="max-width: 500px; margin: 40px auto; padding: 20px;">';
            echo do_shortcode('[wcpa_login_form]');
            echo '</div>';
            
            // Prevent default WooCommerce login form from showing
            add_filter('woocommerce_locate_template', array($this, 'override_login_template'), 10, 3);
        }
    }
    
    /**
     * Override WooCommerce login template to prevent default form from showing
     */
    public function override_login_template($template, $template_name, $template_path) {
        // Only override the login form template for non-logged-in users
        if ($template_name === 'myaccount/form-login.php' && !is_user_logged_in() && function_exists('is_account_page') && is_account_page()) {
            // Return empty template to prevent default form
            $empty_template = WCPA_PLUGIN_PATH . 'templates/empty-login-form.php';
            if (!file_exists($empty_template)) {
                // Create empty template file
                $template_dir = WCPA_PLUGIN_PATH . 'templates/';
                if (!file_exists($template_dir)) {
                    wp_mkdir_p($template_dir);
                }
                file_put_contents($empty_template, '<?php // Empty template to prevent default WooCommerce login form ?>');
            }
            return $empty_template;
        }
        
        return $template;
    }
}
