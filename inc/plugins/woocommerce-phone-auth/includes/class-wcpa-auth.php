<?php
/**
 * Authentication class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Auth {
    
    public function __construct() {
        // Hook into WooCommerce login/register forms
        add_action('woocommerce_login_form_end', array($this, 'add_phone_login_option'));
        add_action('woocommerce_register_form_end', array($this, 'add_phone_register_option'));
    }
    
    public function add_phone_login_option() {
        echo '<p class="wcpa-phone-login-option">';
        echo '<a href="#" id="wcpa-phone-login-toggle">' . __('ورود با شماره تماس', 'woocommerce-ai-account-manager') . '</a>';
        echo '</p>';
    }
    
    public function add_phone_register_option() {
        echo '<p class="wcpa-phone-register-option">';
        echo '<a href="#" id="wcpa-phone-register-toggle">' . __('ثبت نام با شماره تماس', 'woocommerce-ai-account-manager') . '</a>';
        echo '</p>';
    }
    
    public function register_user($phone) {
        // Check if user already exists
        $user = $this->get_user_by_phone($phone);
        
        
        // Generate username from phone
        $username = 'user_' . $phone;
        
        // Generate random password
        $password = wp_generate_password(12, false);
        
        // Create user
        $user_id = wp_create_user($username, $password, '');
        
        if (is_wp_error($user_id)) {
            return array(
                'success' => false,
                'message' => __('خطا در ایجاد کاربر', 'woocommerce-ai-account-manager')
            );
        }
        
        // Update user meta
        update_user_meta($user_id, 'billing_phone', $phone);
        update_user_meta($user_id, 'phone_verified', true);
        
        // Set user role
        $user = new WP_User($user_id);
        $user->set_role('customer');
        
        // Auto login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        return array(
            'success' => true,
            'message' => __('ثبت نام موفقیت‌آمیز', 'woocommerce-ai-account-manager'),
            'redirect' => wc_get_page_permalink('myaccount')
        );
    }
    
    public function login_user($phone) {
        $user = $this->get_user_by_phone($phone);
        
        if (!$user) {
            return array(
                'success' => false,
                'message' => __('کاربری با این شماره تماس یافت نشد', 'woocommerce-ai-account-manager')
            );
        }
        
        // Check if phone is verified
        if (!get_user_meta($user->ID, 'phone_verified', true)) {
            return array(
                'success' => false,
                'message' => __('شماره تماس تایید نشده است', 'woocommerce-ai-account-manager')
            );
        }
        
        // Auto login
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        
        return array(
            'success' => true,
            'message' => __('ورود موفقیت‌آمیز', 'woocommerce-ai-account-manager'),
            'redirect' => wc_get_page_permalink('myaccount')
        );
    }
    
    public function get_user_by_phone($phone) {
        $users = get_users(array(
            'meta_key' => 'billing_phone',
            'meta_value' => $phone,
            'number' => 1
        ));
        
        return !empty($users) ? $users[0] : false;
    }
    
    public function is_user_logged_in() {
        return is_user_logged_in();
    }
    
    public function get_current_user_display_name() {
        if (!is_user_logged_in()) {
            return '';
        }
        
        $user = wp_get_current_user();
        $phone = get_user_meta($user->ID, 'billing_phone', true);
        
        return $phone ? $phone : $user->display_name;
    }
}
