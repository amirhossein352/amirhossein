<?php
/**
 * Admin settings page
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('تنظیمات ورود با شماره تماس', 'woocommerce-ai-account-manager'),
            __('ورود با شماره تماس', 'woocommerce-ai-account-manager'),
            'manage_options',
            'wcpa-settings',
            array($this, 'admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('wcpa_settings', 'wcpa_sms_provider');
        register_setting('wcpa_settings', 'wcpa_sms_api_key');
        register_setting('wcpa_settings', 'wcpa_sms_sender');
        register_setting('wcpa_settings', 'wcpa_sms_pattern_code');
        register_setting('wcpa_settings', 'wcpa_sms_username');
        register_setting('wcpa_settings', 'wcpa_sms_password');
        register_setting('wcpa_settings', 'wcpa_code_expiry');
        register_setting('wcpa_settings', 'wcpa_myaccount_url');
        register_setting('wcpa_settings', 'wcpa_require_registration');
        register_setting('wcpa_settings', 'wcpa_google_client_id');
        register_setting('wcpa_settings', 'wcpa_google_client_secret');
        register_setting('wcpa_settings', 'wcpa_enable_google_auth');
        register_setting('wcpa_settings', 'wcpa_resend_cooldown');
        register_setting('wcpa_settings', 'wcpa_primary_color');
        register_setting('wcpa_settings', 'wcpa_terms_url');
        register_setting('wcpa_settings', 'wcpa_allow_registration');
        
        add_settings_section(
            'wcpa_sms_section',
            __('تنظیمات پیامک', 'woocommerce-phone-auth'),
            array($this, 'sms_section_callback'),
            'wcpa-settings'
        );
        
        add_settings_section(
            'wcpa_general_section',
            __('تنظیمات عمومی', 'woocommerce-phone-auth'),
            array($this, 'general_section_callback'),
            'wcpa-settings'
        );
        
        add_settings_section(
            'wcpa_google_section',
            __('تنظیمات گوگل', 'woocommerce-phone-auth'),
            array($this, 'google_section_callback'),
            'wcpa-settings'
        );
        
        add_settings_field(
            'wcpa_sms_provider',
            __('ارائه‌دهنده پیامک', 'woocommerce-phone-auth'),
            array($this, 'sms_provider_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        add_settings_field(
            'wcpa_sms_api_key',
            __('کلید API', 'woocommerce-phone-auth'),
            array($this, 'api_key_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        add_settings_field(
            'wcpa_sms_sender',
            __('شماره فرستنده', 'woocommerce-phone-auth'),
            array($this, 'sender_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        add_settings_field(
            'wcpa_sms_pattern_code',
            __('کد پترن', 'woocommerce-phone-auth'),
            array($this, 'pattern_code_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        add_settings_field(
            'wcpa_sms_username',
            __('نام کاربری (ملی پیامک)', 'woocommerce-phone-auth'),
            array($this, 'sms_username_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        add_settings_field(
            'wcpa_sms_password',
            __('رمز عبور (ملی پیامک)', 'woocommerce-phone-auth'),
            array($this, 'sms_password_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        add_settings_field(
            'wcpa_code_expiry',
            __('مدت اعتبار کد (ثانیه)', 'woocommerce-phone-auth'),
            array($this, 'expiry_callback'),
            'wcpa-settings',
            'wcpa_sms_section'
        );
        
        // General settings
        add_settings_field(
            'wcpa_myaccount_url',
            __('لینک حساب کاربری', 'woocommerce-phone-auth'),
            array($this, 'myaccount_url_callback'),
            'wcpa-settings',
            'wcpa_general_section'
        );
        
        add_settings_field(
            'wcpa_require_registration',
            __('اجبار ثبت نام برای پرداخت', 'woocommerce-phone-auth'),
            array($this, 'require_registration_callback'),
            'wcpa-settings',
            'wcpa_general_section'
        );

        add_settings_field(
            'wcpa_resend_cooldown',
            __('فاصله ارسال مجدد (ثانیه)', 'woocommerce-phone-auth'),
            array($this, 'resend_cooldown_callback'),
            'wcpa-settings',
            'wcpa_general_section'
        );

        add_settings_field(
            'wcpa_primary_color',
            __('رنگ اصلی افزونه', 'woocommerce-phone-auth'),
            array($this, 'primary_color_callback'),
            'wcpa-settings',
            'wcpa_general_section'
        );

        add_settings_field(
            'wcpa_terms_url',
            __('لینک قوانین و مقررات', 'woocommerce-phone-auth'),
            array($this, 'terms_url_callback'),
            'wcpa-settings',
            'wcpa_general_section'
        );

        add_settings_field(
            'wcpa_allow_registration',
            __('اجازه ثبت نام از فرم', 'woocommerce-phone-auth'),
            array($this, 'allow_registration_callback'),
            'wcpa-settings',
            'wcpa_general_section'
        );
        
        // Google settings
        add_settings_field(
            'wcpa_enable_google_auth',
            __('فعال‌سازی ورود با گوگل', 'woocommerce-phone-auth'),
            array($this, 'enable_google_auth_callback'),
            'wcpa-settings',
            'wcpa_google_section'
        );
        
        add_settings_field(
            'wcpa_google_client_id',
            __('کلید مشتری گوگل', 'woocommerce-phone-auth'),
            array($this, 'google_client_id_callback'),
            'wcpa-settings',
            'wcpa_google_section'
        );
        
        add_settings_field(
            'wcpa_google_client_secret',
            __('رمز مشتری گوگل', 'woocommerce-phone-auth'),
            array($this, 'google_client_secret_callback'),
            'wcpa-settings',
            'wcpa_google_section'
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook === 'settings_page_wcpa-settings') {
            wp_enqueue_script('wcpa-admin', WCPA_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), WCPA_VERSION, true);
            wp_localize_script('wcpa-admin', 'wcpa_admin_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wcpa_admin_nonce')
            ));
        }
        
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('تنظیمات ورود با شماره تماس', 'woocommerce-phone-auth'); ?></h1>
            
            <div class="wcpa-admin-content">
                <div class="wcpa-admin-main">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('wcpa_settings');
                        do_settings_sections('wcpa-settings');
                        submit_button();
                        ?>
                    </form>
                </div>
                
                <div class="wcpa-admin-sidebar">
                    <div class="wcpa-admin-box">
                        <h3><?php _e('شورت کدهای موجود', 'woocommerce-phone-auth'); ?></h3>
                        
                        <h4><?php _e('1. فرم ورود/ثبت نام در صفحه:', 'woocommerce-phone-auth'); ?></h4>
                        <code>[wcpa_login_form]</code>
                        <p class="description"><?php _e('فرم کامل ورود و ثبت نام برای استفاده در صفحات. اگر کاربر موجود باشد ورود می‌کند، در غیر این صورت ثبت نام می‌کند.', 'woocommerce-phone-auth'); ?></p>
                        
                        <h5><?php _e('پارامترهای اختیاری:', 'woocommerce-phone-auth'); ?></h5>
                        <ul>
                            <li><code>title="عنوان دلخواه"</code> - <?php _e('عنوان فرم', 'woocommerce-phone-auth'); ?></li>
                            <li><code>description="توضیحات"</code> - <?php _e('توضیحات زیر عنوان', 'woocommerce-phone-auth'); ?></li>
                            <li><code>show_google="true/false"</code> - <?php _e('نمایش دکمه ورود با گوگل', 'woocommerce-phone-auth'); ?></li>
                            <li><code>redirect="URL"</code> - <?php _e('آدرس ریدایرکت پس از ورود', 'woocommerce-phone-auth'); ?></li>
                        </ul>
                        
                        <h4><?php _e('2. دکمه ورود دسکتاب:', 'woocommerce-phone-auth'); ?></h4>
                        <code>[login_desktop]</code>
                        <p class="description"><?php _e('دکمه ورود برای نمایش در هدر دسکتاب', 'woocommerce-phone-auth'); ?></p>
                        
                        <h4><?php _e('3. دکمه ورود موبایل:', 'woocommerce-phone-auth'); ?></h4>
                        <code>[login_mobile]</code>
                        <p class="description"><?php _e('دکمه ورود برای نمایش در هدر موبایل', 'woocommerce-phone-auth'); ?></p>
                        
                        <h4><?php _e('4. فرم پاپ آپ:', 'woocommerce-phone-auth'); ?></h4>
                        <code>[wcpa_phone_login_form]</code>
                        <p class="description"><?php _e('فرم ورود برای استفاده در پاپ آپ', 'woocommerce-phone-auth'); ?></p>
                        
                        <h4><?php _e('مثال استفاده:', 'woocommerce-phone-auth'); ?></h4>
                        <code>[wcpa_login_form title="ورود به سایت" redirect="/dashboard/"]</code>
                    </div>
                    
                    <div class="wcpa-admin-box">
                        <h3><?php _e('تست پیامک', 'woocommerce-phone-auth'); ?></h3>
                        <p><?php _e('برای تست ارسال پیامک شماره تماس خود را وارد کنید:', 'woocommerce-phone-auth'); ?></p>
                        <input type="tel" id="test-phone" placeholder="09123456789" maxlength="11">
                        <button type="button" id="test-sms-btn" class="button"><?php _e('ارسال تست', 'woocommerce-phone-auth'); ?></button>
                        <button type="button" id="validate-settings-btn" class="button"><?php _e('بررسی تنظیمات', 'woocommerce-phone-auth'); ?></button>
                        <button type="button" id="test-ipanel-connection-btn" class="button"><?php _e('تست اتصال IPPanel', 'woocommerce-phone-auth'); ?></button>
                        <div id="test-sms-result"></div>
                        <div id="settings-validation-result"></div>
                    </div>
                    
                    <div class="wcpa-admin-box">
                        <h3><?php _e('آمار', 'woocommerce-ai-account-manager'); ?></h3>
                        <?php $this->display_stats(); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .wcpa-admin-content {
            display: flex;
            gap: 20px;
        }
        
        .wcpa-admin-main {
            flex: 2;
        }
        
        .wcpa-admin-sidebar {
            flex: 1;
        }
        
        .wcpa-admin-box {
            background: white;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .wcpa-admin-box h3 {
            margin-top: 0;
            color: #23282d;
        }
        
        .wcpa-admin-box code {
            background: #f1f1f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        
        .wcpa-admin-box ul {
            margin: 10px 0;
            padding-right: 20px;
        }
        
        .wcpa-admin-box li {
            margin-bottom: 5px;
        }
        
        #test-phone {
            width: 100%;
            margin-bottom: 10px;
        }
        
        #test-sms-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        
        #test-sms-result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        #test-sms-result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        #settings-validation-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        
        #settings-validation-result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        #settings-validation-result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        </style>
        <?php
    }
    
    public function sms_section_callback() {
        echo '<p>' . __('تنظیمات مربوط به سرویس پیامک', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function general_section_callback() {
        echo '<p>' . __('تنظیمات عمومی افزونه', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function google_section_callback() {
        echo '<p>' . __('تنظیمات ورود با گوگل', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function sms_provider_callback() {
        $value = get_option('wcpa_sms_provider', 'faraz');
        echo '<select name="wcpa_sms_provider" id="wcpa_sms_provider">';
        echo '<option value="faraz"' . selected($value, 'faraz', false) . '>' . __('فراز اس ام اس', 'woocommerce-phone-auth') . '</option>';
        echo '<option value="ipanel"' . selected($value, 'ipanel', false) . '>' . __('آی پنل', 'woocommerce-phone-auth') . '</option>';
        echo '<option value="melipayamak"' . selected($value, 'melipayamak', false) . '>' . __('ملی پیامک', 'woocommerce-phone-auth') . '</option>';
        echo '</select>';
        echo '<p class="description">' . __('ارائه‌دهنده سرویس پیامک را انتخاب کنید', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function api_key_callback() {
        $value = get_option('wcpa_sms_api_key', '');
        $provider = get_option('wcpa_sms_provider', 'faraz');
        
        echo '<input type="text" name="wcpa_sms_api_key" value="' . esc_attr($value) . '" class="regular-text" id="wcpa_sms_api_key" />';
        
        if ($provider === 'ipanel') {
            echo '<p class="description">' . __('Bearer Token از پنل IPPanel (User Panel > Developers > Access Keys)', 'woocommerce-phone-auth') . '</p>';
        } elseif ($provider === 'melipayamak') {
            echo '<p class="description">' . __('برای ملی پیامک از فیلدهای نام کاربری و رمز عبور استفاده کنید', 'woocommerce-phone-auth') . '</p>';
        } else {
            echo '<p class="description">' . __('کلید API از پنل فراز اس ام اس', 'woocommerce-phone-auth') . '</p>';
        }
    }
    
    public function sender_callback() {
        $value = get_option('wcpa_sms_sender', '');
        echo '<input type="text" name="wcpa_sms_sender" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('شماره فرستنده تایید شده در پنل پیامک', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function pattern_code_callback() {
        $value = get_option('wcpa_sms_pattern_code', '');
        $provider = get_option('wcpa_sms_provider', 'faraz');
        echo '<input type="text" name="wcpa_sms_pattern_code" value="' . esc_attr($value) . '" class="regular-text" />';
        if ($provider === 'melipayamak') {
            echo '<p class="description">' . __('برای ملی پیامک با خط خدماتی نیازی به پترن نیست (خالی بگذارید)', 'woocommerce-phone-auth') . '</p>';
        } else {
            echo '<p class="description">' . __('کد پترن تایید شده در پنل پیامک', 'woocommerce-phone-auth') . '</p>';
        }
    }
    
    public function sms_username_callback() {
        $value = get_option('wcpa_sms_username', '');
        $provider = get_option('wcpa_sms_provider', 'faraz');
        echo '<input type="text" name="wcpa_sms_username" value="' . esc_attr($value) . '" class="regular-text" id="wcpa_sms_username" />';
        if ($provider === 'melipayamak') {
            echo '<p class="description">' . __('نام کاربری از پنل ملی پیامک', 'woocommerce-phone-auth') . '</p>';
        } else {
            echo '<p class="description">' . __('فقط برای ملی پیامک استفاده می‌شود', 'woocommerce-phone-auth') . '</p>';
        }
    }
    
    public function sms_password_callback() {
        $value = get_option('wcpa_sms_password', '');
        $provider = get_option('wcpa_sms_provider', 'faraz');
        echo '<input type="password" name="wcpa_sms_password" value="' . esc_attr($value) . '" class="regular-text" id="wcpa_sms_password" />';
        if ($provider === 'melipayamak') {
            echo '<p class="description">' . __('رمز عبور از پنل ملی پیامک', 'woocommerce-phone-auth') . '</p>';
        } else {
            echo '<p class="description">' . __('فقط برای ملی پیامک استفاده می‌شود', 'woocommerce-phone-auth') . '</p>';
        }
    }
    
    public function expiry_callback() {
        $value = get_option('wcpa_code_expiry', 300);
        echo '<input type="number" name="wcpa_code_expiry" value="' . esc_attr($value) . '" min="60" max="3600" />';
        echo '<p class="description">' . __('مدت اعتبار کد تایید به ثانیه (پیش‌فرض: 300 ثانیه)', 'woocommerce-phone-auth') . '</p>';
    }

    public function resend_cooldown_callback() {
        $value = get_option('wcpa_resend_cooldown', 60);
        echo '<input type="number" name="wcpa_resend_cooldown" value="' . esc_attr($value) . '" min="10" max="600" />';
        echo '<p class="description">' . __('حداقل فاصله بین دو ارسال کد برای یک شماره (پیش‌فرض: 60 ثانیه)', 'woocommerce-phone-auth') . '</p>';
    }

    public function primary_color_callback() {
        $value = get_option('wcpa_primary_color', '#224190');
        echo '<input type="color" name="wcpa_primary_color" value="' . esc_attr($value) . '" />';
        echo '<p class="description">' . __('این رنگ روی دکمه‌ها، هدر مودال و مرزبندی‌ها اعمال می‌شود', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function myaccount_url_callback() {
        $value = get_option('wcpa_myaccount_url', '');
        echo '<input type="url" name="wcpa_myaccount_url" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('لینک صفحه حساب کاربری (خالی بگذارید تا از تنظیمات ووکامرس استفاده شود)', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function require_registration_callback() {
        $value = get_option('wcpa_require_registration', 'yes');
        echo '<input type="checkbox" name="wcpa_require_registration" value="yes"' . checked($value, 'yes', false) . ' />';
        echo '<p class="description">' . __('اگر فعال باشد، کاربران باید قبل از پرداخت ثبت نام کنند', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function enable_google_auth_callback() {
        $value = get_option('wcpa_enable_google_auth', 'no');
        echo '<input type="checkbox" name="wcpa_enable_google_auth" value="yes"' . checked($value, 'yes', false) . ' />';
        echo '<p class="description">' . __('فعال‌سازی ورود و ثبت نام با گوگل', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function google_client_id_callback() {
        $value = get_option('wcpa_google_client_id', '');
        echo '<input type="text" name="wcpa_google_client_id" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('کلید مشتری گوگل از Google Cloud Console', 'woocommerce-phone-auth') . '</p>';
    }
    
    public function google_client_secret_callback() {
        $value = get_option('wcpa_google_client_secret', '');
        echo '<input type="password" name="wcpa_google_client_secret" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('رمز مشتری گوگل از Google Cloud Console', 'woocommerce-phone-auth') . '</p>';
    }

    public function terms_url_callback() {
        $value = get_option('wcpa_terms_url', '');
        echo '<input type="url" name="wcpa_terms_url" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('لینک صفحه قوانین و مقررات (اختیاری)', 'woocommerce-phone-auth') . '</p>';
    }

    public function allow_registration_callback() {
        $value = get_option('wcpa_allow_registration', 'yes');
        echo '<select name="wcpa_allow_registration">';
        echo '<option value="yes"' . selected($value, 'yes', false) . '>' . __('بله - اجازه ثبت نام و ورود', 'woocommerce-phone-auth') . '</option>';
        echo '<option value="login_only"' . selected($value, 'login_only', false) . '>' . __('فقط ورود - فقط کاربران موجود', 'woocommerce-phone-auth') . '</option>';
        echo '</select>';
        echo '<p class="description">' . __('کنترل دسترسی کاربران به فرم ورود/ثبت نام', 'woocommerce-phone-auth') . '</p>';
    }
    
    private function display_stats() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcpa_verification_codes';
        
        // Total codes sent today
        $today_codes = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM $table_name 
            WHERE DATE(created_at) = CURDATE()
        ");
        
        // Total users with phone verification
        $verified_users = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$wpdb->usermeta} 
            WHERE meta_key = 'phone_verified' AND meta_value = '1'
        ");
        
        echo '<ul>';
        echo '<li><strong>' . __('کدهای ارسالی امروز:', 'woocommerce-phone-auth') . '</strong> ' . $today_codes . '</li>';
        echo '<li><strong>' . __('کاربران تایید شده:', 'woocommerce-phone-auth') . '</strong> ' . $verified_users . '</li>';
        echo '</ul>';
    }
    
}
