<?php
/**
 * Google OAuth authentication class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Google_Auth {
    
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $google_auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $google_token_url = 'https://oauth2.googleapis.com/token';
    private $google_userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
    
    public function __construct() {
        $this->client_id = get_option('wcpa_google_client_id', '');
        $this->client_secret = get_option('wcpa_google_client_secret', '');
        $this->redirect_uri = admin_url('admin-ajax.php?action=wcpa_google_callback');
        
        add_action('wp_ajax_wcpa_google_login', array($this, 'google_login'));
        add_action('wp_ajax_nopriv_wcpa_google_login', array($this, 'google_login'));
        add_action('wp_ajax_wcpa_google_callback', array($this, 'google_callback'));
        add_action('wp_ajax_nopriv_wcpa_google_callback', array($this, 'google_callback'));
    }
    
    public function is_enabled() {
        return get_option('wcpa_enable_google_auth', 'no') === 'yes' && 
               !empty($this->client_id) && 
               !empty($this->client_secret);
    }
    
    public function get_auth_url() {
        if (!$this->is_enabled()) {
            return '';
        }
        
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'email profile',
            'response_type' => 'code',
            'access_type' => 'offline',
            'state' => wp_create_nonce('wcpa_google_auth')
        );
        
        return $this->google_auth_url . '?' . http_build_query($params);
    }
    
    public function google_login() {
        if (!$this->is_enabled()) {
            wp_send_json_error(array('message' => __('ورود با گوگل فعال نیست', 'woocommerce-phone-auth')));
        }
        
        $auth_url = $this->get_auth_url();
        if (empty($auth_url)) {
            wp_send_json_error(array('message' => __('خطا در ایجاد لینک گوگل', 'woocommerce-phone-auth')));
        }
        
        wp_send_json_success(array('auth_url' => $auth_url));
    }
    
    public function google_callback() {
        if (!$this->is_enabled()) {
            wp_die(__('ورود با گوگل فعال نیست', 'woocommerce-phone-auth'));
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_GET['state'], 'wcpa_google_auth')) {
            wp_die(__('خطا در تایید درخواست', 'woocommerce-phone-auth'));
        }
        
        $code = sanitize_text_field($_GET['code']);
        if (empty($code)) {
            wp_die(__('کد تایید دریافت نشد', 'woocommerce-phone-auth'));
        }
        
        // Exchange code for access token
        $token_data = $this->get_access_token($code);
        if (!$token_data['success']) {
            wp_die($token_data['message']);
        }
        
        // Get user info
        $user_info = $this->get_user_info($token_data['access_token']);
        if (!$user_info['success']) {
            wp_die($user_info['message']);
        }
        
        // Login or register user
        $result = $this->process_google_user($user_info['user_data']);
        
        if ($result['success']) {
            // Redirect to myaccount page
            $myaccount_url = get_option('wcpa_myaccount_url', '');
            if (empty($myaccount_url)) {
                if (function_exists('wc_get_page_permalink')) {
                    $myaccount_url = wc_get_page_permalink('myaccount');
                } else {
                    $myaccount_id = get_option('woocommerce_myaccount_page_id');
                    $myaccount_url = $myaccount_id ? get_permalink($myaccount_id) : home_url('/my-account/');
                }
            }
            
            wp_redirect($myaccount_url);
            exit;
        } else {
            wp_die($result['message']);
        }
    }
    
    private function get_access_token($code) {
        $data = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirect_uri
        );
        
        $response = wp_remote_post($this->google_token_url, array(
            'body' => $data,
            'headers' => array('Content-Type' => 'application/x-www-form-urlencoded')
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => __('خطا در ارتباط با گوگل', 'woocommerce-phone-auth')
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['error'])) {
            return array(
                'success' => false,
                'message' => $data['error_description'] ?? __('خطا در دریافت توکن', 'woocommerce-phone-auth')
            );
        }
        
        return array(
            'success' => true,
            'access_token' => $data['access_token']
        );
    }
    
    private function get_user_info($access_token) {
        $response = wp_remote_get($this->google_userinfo_url . '?access_token=' . $access_token);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => __('خطا در دریافت اطلاعات کاربر', 'woocommerce-phone-auth')
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $user_data = json_decode($body, true);
        
        if (isset($user_data['error'])) {
            return array(
                'success' => false,
                'message' => $user_data['error']['message'] ?? __('خطا در دریافت اطلاعات کاربر', 'woocommerce-phone-auth')
            );
        }
        
        return array(
            'success' => true,
            'user_data' => $user_data
        );
    }
    
    private function process_google_user($user_data) {
        $email = $user_data['email'] ?? '';
        $name = $user_data['name'] ?? '';
        $google_id = $user_data['id'] ?? '';
        
        if (empty($email)) {
            return array(
                'success' => false,
                'message' => __('ایمیل کاربر دریافت نشد', 'woocommerce-phone-auth')
            );
        }
        
        // Check if user exists by email
        $user = get_user_by('email', $email);
        
        if ($user) {
            // User exists, log them in
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);
            
            // Update Google ID if not set
            if (!get_user_meta($user->ID, 'google_id', true)) {
                update_user_meta($user->ID, 'google_id', $google_id);
            }
            
            return array(
                'success' => true,
                'message' => __('ورود موفقیت‌آمیز', 'woocommerce-phone-auth')
            );
        } else {
            // Create new user
            $username = sanitize_user($email);
            $username = $this->generate_unique_username($username);
            
            $user_id = wp_create_user($username, wp_generate_password(), $email);
            
            if (is_wp_error($user_id)) {
                return array(
                    'success' => false,
                    'message' => $user_id->get_error_message()
                );
            }
            
            // Set user meta
            update_user_meta($user_id, 'google_id', $google_id);
            update_user_meta($user_id, 'first_name', $user_data['given_name'] ?? '');
            update_user_meta($user_id, 'last_name', $user_data['family_name'] ?? '');
            update_user_meta($user_id, 'phone_verified', '1');
            
            // Log user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            return array(
                'success' => true,
                'message' => __('ثبت نام و ورود موفقیت‌آمیز', 'woocommerce-phone-auth')
            );
        }
    }
    
    private function generate_unique_username($username) {
        $original_username = $username;
        $counter = 1;
        
        while (username_exists($username)) {
            $username = $original_username . $counter;
            $counter++;
        }
        
        return $username;
    }
}
