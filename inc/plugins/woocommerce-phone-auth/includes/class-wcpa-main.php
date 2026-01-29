<?php
/**
 * Main plugin class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Main {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_wcpa_send_verification_code', array($this, 'send_verification_code'));
        add_action('wp_ajax_nopriv_wcpa_send_verification_code', array($this, 'send_verification_code'));
        add_action('wp_ajax_wcpa_verify_code', array($this, 'verify_code'));
        add_action('wp_ajax_nopriv_wcpa_verify_code', array($this, 'verify_code'));
        add_action('wp_ajax_wcpa_send_code', array($this, 'send_verification_code'));
        add_action('wp_ajax_nopriv_wcpa_send_code', array($this, 'send_verification_code'));
        add_action('wp_ajax_wcpa_login_register', array($this, 'login_register'));
        add_action('wp_ajax_nopriv_wcpa_login_register', array($this, 'login_register'));
        add_action('wp_ajax_wcpa_test_sms', array($this, 'test_sms'));
        add_action('wp_ajax_wcpa_validate_sms_settings', array($this, 'validate_sms_settings'));
        add_action('wp_ajax_wcpa_test_ipanel_connection', array($this, 'test_ipanel_connection'));
        
        // Checkout hooks
        add_action('woocommerce_before_checkout_form', array($this, 'replace_checkout_form'));
        add_action('wp_footer', array($this, 'add_checkout_scripts'));
        add_filter('woocommerce_checkout_get_value', array($this, 'set_verified_phone_in_checkout'), 10, 2);
        add_action('woocommerce_after_checkout_form', array($this, 'show_checkout_after_verification'));
        add_action('woocommerce_checkout_process', array($this, 'validate_phone_required'));
        add_filter('woocommerce_checkout_fields', array($this, 'make_phone_required_and_readonly'));
        
        // Schedule cleanup of expired codes
        if (!wp_next_scheduled('wcpa_cleanup_expired_codes')) {
            wp_schedule_event(time(), 'hourly', 'wcpa_cleanup_expired_codes');
        }
        add_action('wcpa_cleanup_expired_codes', array($this, 'cleanup_expired_codes'));
    }
    
    public function init() {
        // Load text domain - Updated for theme integration
        load_plugin_textdomain('woocommerce-phone-auth', false, get_template_directory() . '/inc/plugins/woocommerce-phone-auth/languages');
        
        // Initialize components
        new WCPA_Auth();
        $shortcode = new WCPA_Shortcode();
        error_log('WCPA_Shortcode initialized: ' . (is_object($shortcode) ? 'YES' : 'NO'));
        new WCPA_Pages();
        new WCPA_Google_Auth();
        
        if (is_admin()) {
            new WCPA_Admin();
        }
    }
    
    public function enqueue_scripts() {
        // Ensure jQuery is loaded
        wp_enqueue_script('jquery');
        
        // Enqueue main script with proper dependencies
        wp_enqueue_script('wcpa-main', WCPA_PLUGIN_URL . 'assets/js/main.js', array('jquery'), WCPA_VERSION, true);
        
        // Enqueue styles
        wp_enqueue_style('wcpa-style', WCPA_PLUGIN_URL . 'assets/css/style.css', array(), WCPA_VERSION);
        
        // Apply primary color from settings via CSS variables
        $primary_color = get_option('wcpa_primary_color', '#224190');
        $inline_css = ':root{--wcpa-primary: ' . esc_attr($primary_color) . '; --wcpa-primary-dark: ' . esc_attr($primary_color) . ';}';
        wp_add_inline_style('wcpa-style', $inline_css);

        // Get correct myaccount URL
        $myaccount_url = get_option('wcpa_myaccount_url', '');
        
        if (empty($myaccount_url)) {
            if (function_exists('wc_get_page_permalink')) {
                $myaccount_url = wc_get_page_permalink('myaccount');
            }
            
            if (!$myaccount_url) {
                $myaccount_id = get_option('woocommerce_myaccount_page_id');
                $myaccount_url = $myaccount_id ? get_permalink($myaccount_id) : home_url('/my-account/');
            }
        }
        
        // Make sure it's not the phone-login page
        if (strpos($myaccount_url, 'phone-login') !== false) {
            $myaccount_url = home_url('/my-account/');
        }
        
        // Localize script
        wp_localize_script('wcpa-main', 'wcpa_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcpa_nonce'),
            'login_url' => $myaccount_url,
            'cooldown' => intval(get_option('wcpa_resend_cooldown', 60)),
            'expiry' => intval(get_option('wcpa_code_expiry', 300)),
            'flags' => array(
                'webotp' => true, // فعال کردن WebOTP برای خواندن خودکار کد از پیامک
                'autosubmit' => true
            ),
            'strings' => array(
                'sending_code' => __('در حال ارسال کد...', 'woocommerce-phone-auth'),
                'code_sent' => __('کد تایید ارسال شد', 'woocommerce-phone-auth'),
                'verifying' => __('در حال تایید...', 'woocommerce-phone-auth'),
                'invalid_code' => __('کد وارد شده صحیح نیست', 'woocommerce-phone-auth'),
                'phone_required' => __('شماره تماس الزامی است', 'woocommerce-phone-auth'),
                'code_required' => __('کد تایید الزامی است', 'woocommerce-phone-auth'),
                'login_success' => __('ورود موفقیت‌آمیز', 'woocommerce-phone-auth'),
                'register_success' => __('ثبت نام موفقیت‌آمیز', 'woocommerce-phone-auth'),
                'error_occurred' => __('خطایی رخ داد', 'woocommerce-phone-auth')
            )
        ));
    }
    
    public function send_verification_code() {
        check_ajax_referer('wcpa_nonce', 'nonce');
        
        $phone = sanitize_text_field($_POST['phone']);
        
        if (empty($phone)) {
            wp_send_json_error(array('message' => __('شماره تماس الزامی است', 'woocommerce-phone-auth')));
        }
        
        // Validate phone number format
        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            wp_send_json_error(array('message' => __('فرمت شماره تماس صحیح نیست', 'woocommerce-phone-auth')));
        }
        
        $sms = new WCPA_SMS();
        $result = $sms->send_verification_code($phone);
        
        if ($result['success']) {
            wp_send_json_success(array('message' => __('کد تایید ارسال شد', 'woocommerce-phone-auth')));
        } else {
            // Pass through cooldown info when available
            $payload = array('message' => $result['message']);
            if (isset($result['code']) && $result['code'] === 'cooldown' && isset($result['remaining'])) {
                $payload['code'] = 'cooldown';
                $payload['remaining'] = intval($result['remaining']);
            }
            wp_send_json_error($payload);
        }
    }
    
    public function verify_code() {
        check_ajax_referer('wcpa_nonce', 'nonce');
        
        $phone = sanitize_text_field($_POST['phone']);
        $code = sanitize_text_field($_POST['code']);
        
        if (empty($phone) || empty($code)) {
            wp_send_json_error(array('message' => __('تمام فیلدها الزامی است', 'woocommerce-phone-auth')));
        }
        
        $sms = new WCPA_SMS();
        $result = $sms->verify_code($phone, $code);
        
        if ($result['success']) {
            wp_send_json_success(array('message' => __('کد تایید شد', 'woocommerce-phone-auth')));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
    
    public function login_register() {
        check_ajax_referer('wcpa_nonce', 'nonce');
        
        $phone = sanitize_text_field($_POST['phone']);
        $code = sanitize_text_field($_POST['code']);
        $action = sanitize_text_field($_POST['action_type']); // 'login' or 'register'
        
        if (empty($phone) || empty($code)) {
            wp_send_json_error(array('message' => __('تمام فیلدها الزامی است', 'woocommerce-phone-auth')));
        }
        
        $sms = new WCPA_SMS();
        $verify_result = $sms->verify_code($phone, $code);
        
        if (!$verify_result['success']) {
            wp_send_json_error(array('message' => $verify_result['message']));
        }
        
        $auth = new WCPA_Auth();
        
        if ($action === 'auto') {
            // Auto-detect: try login first, if fails then register
            $result = $auth->login_user($phone);
            if (!$result['success']) {
                $result = $auth->register_user($phone);
            }
        } elseif ($action === 'register') {
            $result = $auth->register_user($phone);
        } else {
            $result = $auth->login_user($phone);
        }
        
        if ($result['success']) {
            wp_send_json_success(array(
                'message' => $result['message'],
                'redirect' => $result['redirect']
            ));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
    
    public function test_sms() {
        check_ajax_referer('wcpa_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز لازم را ندارید', 'woocommerce-phone-auth')));
        }
        
        $phone = sanitize_text_field($_POST['phone']);
        
        if (empty($phone)) {
            wp_send_json_error(array('message' => __('شماره تماس الزامی است', 'woocommerce-phone-auth')));
        }
        
        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            wp_send_json_error(array('message' => __('فرمت شماره تماس صحیح نیست', 'woocommerce-phone-auth')));
        }
        
        $sms = new WCPA_SMS();
        $result = $sms->test_sms($phone);
        
        if ($result['success']) {
            wp_send_json_success(array('message' => __('پیامک تست با موفقیت ارسال شد', 'woocommerce-phone-auth')));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
    
    public function validate_sms_settings() {
        check_ajax_referer('wcpa_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز لازم را ندارید', 'woocommerce-phone-auth')));
        }
        
        $sms = new WCPA_SMS();
        $validation = $sms->validate_settings();
        
        if ($validation['valid']) {
            wp_send_json_success(array('message' => __('همه تنظیمات صحیح است', 'woocommerce-phone-auth')));
        } else {
            wp_send_json_error(array('message' => __('مشکلات موجود: ' . implode(', ', $validation['errors']), 'woocommerce-phone-auth')));
        }
    }
    
    public function test_ipanel_connection() {
        check_ajax_referer('wcpa_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز لازم را ندارید', 'woocommerce-phone-auth')));
        }
        
        $sms = new WCPA_SMS();
        $result = $sms->test_ipanel_connection();
        
        if ($result['success']) {
            wp_send_json_success(array('message' => $result['message']));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
    
    public function replace_checkout_form() {
        // Only show on checkout page and if user is not logged in
        if (!is_checkout() || is_user_logged_in()) {
            return;
        }
        
        // Check if registration is required
        $require_registration = get_option('wcpa_require_registration', 'yes');
        if ($require_registration !== 'yes') {
            return;
        }
        
        // Checkout page: Auto open popup and prevent closing
        if (is_checkout() && !is_user_logged_in()) {
            echo '<script>
            jQuery(document).ready(function($) {
                // Auto open login popup on checkout page only
                setTimeout(function() {
                    if (typeof window.openLogin === "function") {
                        window.openLogin();
                    }
                }, 500);
                
                // Make modal non-closable on checkout page
                $(document).on("click", ".wcpa-close", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
                
                // Prevent clicking on modal background to close
                $(document).on("click", "#wcpa-login-modal", function(e) {
                    if (e.target === this) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
                
                // Prevent ESC key from closing modal
                $(document).on("keydown", function(e) {
                    if (e.keyCode === 27) { // ESC key
                        e.preventDefault();
                        return false;
                    }
                });
                
                // Ensure form buttons work
                $(document).on("click", "#wcpa-send-code, #wcpa-submit-form", function(e) {
                    e.stopPropagation();
                });
            });
            </script>';
            
            // Hide checkout form initially
            echo '<style>
                .woocommerce-checkout form.checkout { 
                    display: none !important; 
                }
            </style>';
        }
    }
    
    public function show_checkout_after_verification() {
        // Only on checkout page
        if (!is_checkout()) {
            return;
        }
        
        // If user is logged in, show checkout form
        if (is_user_logged_in()) {
            echo '<style>
                .woocommerce-checkout form.checkout { 
                    display: flex !important; 
                }
            </style>';
        }
    }
    
    public function add_checkout_scripts() {
        if (!is_checkout()) {
            return;
        }
        
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Check if user is logged in on page load
            if (is_user_logged_in) {
                $('.woocommerce-checkout form.checkout').show();
            }
        });
        </script>
        <?php
    }
    
    public function set_verified_phone_in_checkout($value, $input) {
        // Only for billing_phone field
        if ($input === 'billing_phone') {
            // If user is logged in, get phone from user meta
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                $phone = get_user_meta($user->ID, 'billing_phone', true);
                if ($phone) {
                    return $phone;
                }
            }
        }
        
        return $value;
    }
    
    public function make_phone_required_and_readonly($fields) {
        // Only apply if registration is required and user is logged in
        $require_registration = get_option('wcpa_require_registration', 'yes');
        if ($require_registration === 'yes' && is_user_logged_in()) {
            // Make billing phone required and readonly
            if (isset($fields['billing']['billing_phone'])) {
                $fields['billing']['billing_phone']['required'] = true;
                $fields['billing']['billing_phone']['custom_attributes'] = array(
                    'readonly' => 'readonly',
                    'style' => 'background-color: #f5f5f5;'
                );
            }
        }
        
        return $fields;
    }
    
    public function validate_phone_required() {
        $require_registration = get_option('wcpa_require_registration', 'yes');
        if ($require_registration === 'yes' && is_user_logged_in()) {
            $user = wp_get_current_user();
            $phone = get_user_meta($user->ID, 'billing_phone', true);
            
            if (empty($phone)) {
                wc_add_notice('شماره تماس الزامی است. لطفاً ابتدا وارد شوید.', 'error');
            }
        }
    }
    
    public function cleanup_expired_codes() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcpa_verification_codes';
        $wpdb->query("DELETE FROM $table_name WHERE expires_at < NOW() OR used = 1");
    }
}
