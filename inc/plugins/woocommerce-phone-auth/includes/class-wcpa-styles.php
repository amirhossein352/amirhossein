<?php
/**
 * Styles and CSS for WooCommerce integration
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Styles {
    
    public function __construct() {
        add_action('wp_head', array($this, 'add_custom_styles'));
        add_action('woocommerce_account_dashboard', array($this, 'customize_account_dashboard'));
        add_filter('woocommerce_account_menu_items', array($this, 'customize_account_menu'));
    }
    
    public function add_custom_styles() {
        ?>
        <style>
        /* Header Auth Styles */
        .wcpa-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .wcpa-user-name {
            font-weight: bold;
            color: #333;
        }
        
        .wcpa-logout-btn {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
        }
        
        .wcpa-logout-btn:hover {
            background: #c0392b;
            color: white;
        }
        
        .wcpa-auth-buttons {
            display: flex;
            gap: 10px;
        }
        
        .wcpa-login-btn, .wcpa-register-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .wcpa-register-btn {
            background: #27ae60;
        }
        
        .wcpa-login-btn:hover {
            background: #2980b9;
        }
        
        .wcpa-register-btn:hover {
            background: #229954;
        }
        
        /* Modal Styles */
        .wcpa-modal {
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .wcpa-modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .wcpa-modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .wcpa-modal-header h3 {
            margin: 0;
            color: #333;
        }
        
        .wcpa-modal-close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .wcpa-modal-close:hover {
            color: #000;
        }
        
        .wcpa-modal-body {
            padding: 20px;
        }
        
        .wcpa-form-group {
            margin-bottom: 15px;
        }
        
        .wcpa-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .wcpa-form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .wcpa-help-text {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        
        .wcpa-resend-btn {
            background: #f39c12;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .wcpa-resend-btn:hover {
            background: #e67e22;
        }
        
        .wcpa-form-actions {
            margin-top: 20px;
        }
        
        .wcpa-send-code-btn, .wcpa-submit-btn {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        
        .wcpa-submit-btn {
            background: #27ae60;
        }
        
        .wcpa-send-code-btn:hover {
            background: #2980b9;
        }
        
        .wcpa-submit-btn:hover {
            background: #229954;
        }
        
        .wcpa-messages {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        
        .wcpa-messages.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .wcpa-messages.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* WooCommerce Account Page Customization */
        .woocommerce-account .woocommerce-MyAccount-navigation {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .woocommerce-account .woocommerce-MyAccount-navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .woocommerce-account .woocommerce-MyAccount-navigation ul li {
            margin-bottom: 10px;
        }
        
        .woocommerce-account .woocommerce-MyAccount-navigation ul li a {
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .woocommerce-account .woocommerce-MyAccount-navigation ul li a:hover,
        .woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active a {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        
        .woocommerce-account .woocommerce-MyAccount-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .woocommerce-account .woocommerce-MyAccount-content h3 {
            color: #333;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        /* Dark theme support */
        @media (prefers-color-scheme: dark) {
            .wcpa-modal-content {
                background-color: #2c3e50;
                color: #ecf0f1;
            }
            
            .wcpa-modal-header h3 {
                color: #ecf0f1;
            }
            
            .wcpa-form-group label {
                color: #ecf0f1;
            }
            
            .wcpa-form-group input {
                background-color: #34495e;
                border-color: #4a5f7a;
                color: #ecf0f1;
            }
            
            .wcpa-help-text {
                color: #bdc3c7;
            }
        }
        </style>
        <?php
    }
    
    public function customize_account_dashboard() {
        ?>
        <div class="wcpa-account-welcome">
            <h2><?php _e('خوش آمدید!', 'woocommerce-phone-auth'); ?></h2>
            <p><?php _e('به پنل کاربری خود خوش آمدید. از منوی سمت راست می‌توانید به بخش‌های مختلف دسترسی داشته باشید.', 'woocommerce-phone-auth'); ?></p>
        </div>
        <?php
    }
    
    public function customize_account_menu($items) {
        // Add custom menu items if needed
        return $items;
    }
}
