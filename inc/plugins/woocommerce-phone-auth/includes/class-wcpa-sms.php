<?php
/**
 * SMS service class for Faraz SMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_SMS {
    
    private $provider;
    private $api_key;
    private $sender;
    private $pattern_code;
    private $username;
    private $password;
    private $faraz_api_url = 'http://edge.ippanel.com/v1/api/send';
    private $ipanel_api_url = 'https://edge.ippanel.com/v1/sms/pattern/normal/send';
    private $ipanel_direct_api_url = 'https://edge.ippanel.com/v1/messages/pattern/normal/send';
    private $melipayamak_api_url = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
    
    /**
     * ساخت متن پیامک با فرمت مناسب برای WebOTP API
     * فرمت باید شامل کلمه "code" و domain باشد: "Your code is 1234 @domain.com"
     */
    private function get_webotp_formatted_message($verification_code) {
        // دریافت domain سایت برای WebOTP API
        $site_url = home_url();
        $domain = parse_url($site_url, PHP_URL_HOST);
        if (!$domain) {
            $domain = str_replace(['http://', 'https://'], '', $site_url);
            $domain = explode('/', $domain)[0];
        }
        // حذف www از domain
        $domain = preg_replace('/^www\./', '', $domain);
        
        // اگر domain خالی است یا درست استخراج نشده، از khaneirani.com استفاده کن
        if (empty($domain) || !preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain)) {
            $domain = 'khaneirani.com';
        }
        
        // فرمت پیامک برای WebOTP API:
        // خط اول: code:2414 (کلمه code به انگلیسی و بعد کد)
        // خط دوم: کدتایید شما
        // خط سوم: خانه ایرانی
        // خط آخر: @domain.com #code (الزامی برای WebOTP API)
        $message = sprintf("code:%s\nکدتایید شما\nخانه ایرانی\n@%s #%s", $verification_code, $domain, $verification_code);
        
        // Log برای debugging
        error_log('WebOTP SMS Format: ' . $message);
        error_log('WebOTP Domain: ' . $domain);
        error_log('WebOTP Site URL: ' . $site_url);
        
        return $message;
    }
    
    public function __construct() {
        $this->provider = get_option('wcpa_sms_provider', 'faraz');
        $this->api_key = get_option('wcpa_sms_api_key', '');
        $this->sender = get_option('wcpa_sms_sender', '');
        $this->pattern_code = get_option('wcpa_sms_pattern_code', '');
        $this->username = get_option('wcpa_sms_username', '');
        $this->password = get_option('wcpa_sms_password', '');
    }
    
    public function send_verification_code($phone) {
        // Enforce resend cooldown
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcpa_verification_codes';
        $cooldown = intval(get_option('wcpa_resend_cooldown', 60));
        if ($cooldown > 0) {
            // Compute remaining using DB time to avoid PHP/DB timezone skew
            $recent = $wpdb->get_row($wpdb->prepare(
                "SELECT created_at, GREATEST(0, %d - TIMESTAMPDIFF(SECOND, created_at, NOW())) AS remaining FROM $table_name WHERE phone = %s ORDER BY created_at DESC LIMIT 1",
                $cooldown,
                $phone
            ));
            if ($recent && isset($recent->remaining) && intval($recent->remaining) > 0) {
                $remaining = intval($recent->remaining);
                return array(
                    'success' => false,
                    'code' => 'cooldown',
                    'remaining' => $remaining,
                    'message' => sprintf(__('لطفاً %d ثانیه صبر کنید سپس دوباره تلاش کنید', 'woocommerce-phone-auth'), $remaining)
                );
            }
        }

        // Generate 4-digit verification code
        $code = sprintf('%04d', wp_rand(1000, 9999));
        
        // Store code in database
        $this->store_verification_code($phone, $code);
        
        // Send SMS using pattern
        $result = $this->send_pattern_sms($phone, $code);
        
        if ($result['success']) {
            return array(
                'success' => true,
                'message' => __('کد تایید ارسال شد', 'woocommerce-ai-account-manager')
            );
        } else {
            return array(
                'success' => false,
                'message' => $result['message']
            );
        }
    }
    
    public function verify_code($phone, $code) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcpa_verification_codes';
        
        // Find latest matching unused code for this phone
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE phone = %s AND code = %s ORDER BY created_at DESC LIMIT 1",
            $phone,
            $code
        ));
        
        if (!$result) {
            return array(
                'success' => false,
                'message' => __('کد وارد شده صحیح نیست', 'woocommerce-phone-auth')
            );
        }
        
        // Check if already used
        if (intval($result->used) === 1) {
            return array(
                'success' => false,
                'message' => __('این کد قبلاً استفاده شده است', 'woocommerce-phone-auth')
            );
        }
        
        // Check expiry
        $now = time();
        $expires_at_ts = strtotime($result->expires_at);
        if ($expires_at_ts !== false && $expires_at_ts < $now) {
            return array(
                'success' => false,
                'message' => __('کد منقضی شده است. لطفاً ارسال مجدد را بزنید', 'woocommerce-phone-auth')
            );
        }
        
        // Mark code as used
        $wpdb->update(
            $table_name,
            array('used' => 1),
            array('id' => $result->id)
        );
        
        // Mark user's phone as verified if a user exists with this phone
        $users = get_users(array(
            'meta_key' => 'billing_phone',
            'meta_value' => $phone,
            'number' => 1,
            'fields' => 'ids'
        ));
        if (!empty($users)) {
            update_user_meta($users[0], 'phone_verified', 1);
        }
        
        return array(
            'success' => true,
            'message' => __('کد تایید شد', 'woocommerce-phone-auth')
        );
    }
    
    private function store_verification_code($phone, $code) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcpa_verification_codes';
        $expiry_seconds = intval(get_option('wcpa_code_expiry', 300));
        if ($expiry_seconds <= 0) { $expiry_seconds = 300; }
        $expires_at = date('Y-m-d H:i:s', time() + $expiry_seconds);
        
        // Normalize code: remove any non-digit characters and pad to 4 digits
        $code = preg_replace('/\D/', '', $code);
        $code = str_pad($code, 4, '0', STR_PAD_LEFT);
        
        $wpdb->insert(
            $table_name,
            array(
                'phone' => $phone,
                'code' => $code,
                'expires_at' => $expires_at,
                'used' => 0
            ),
            array('%s', '%s', '%s', '%d')
        );
    }
    
    private function send_pattern_sms($phone, $verification_code) {
        // Prepare phone number (add 98 prefix if needed)
        $phone = $this->prepare_phone_number($phone);
        
        if ($this->provider === 'melipayamak') {
            // برای ملی پیامک نیازی به pattern نیست
            if (empty($this->username) || empty($this->password) || empty($this->sender)) {
                return array(
                    'success' => false,
                    'message' => __('تنظیمات پیامک کامل نیست (نام کاربری، رمز عبور و شماره فرستنده الزامی است)', 'woocommerce-phone-auth')
                );
            }
            return $this->send_melipayamak_sms($phone, $verification_code);
        } elseif ($this->provider === 'ipanel') {
            // اگر pattern_code موجود است، از pattern استفاده کن، در غیر این صورت متن مستقیم
            if (!empty($this->pattern_code) && !empty($this->api_key) && !empty($this->sender)) {
                return $this->send_ipanel_sms($phone, $verification_code);
            } elseif (!empty($this->api_key) && !empty($this->sender)) {
                // ارسال مستقیم بدون pattern
                return $this->send_ipanel_direct_sms($phone, $verification_code);
            } else {
                return array(
                    'success' => false,
                    'message' => __('تنظیمات پیامک کامل نیست', 'woocommerce-phone-auth')
                );
            }
        } else {
            // Faraz SMS
            if (!empty($this->pattern_code) && !empty($this->api_key) && !empty($this->sender)) {
                return $this->send_faraz_sms($phone, $verification_code);
            } elseif (!empty($this->api_key) && !empty($this->sender)) {
                // ارسال مستقیم بدون pattern
                return $this->send_faraz_direct_sms($phone, $verification_code);
            } else {
                return array(
                    'success' => false,
                    'message' => __('تنظیمات پیامک کامل نیست', 'woocommerce-phone-auth')
                );
            }
        }
    }
    
    private function send_faraz_sms($phone, $verification_code) {
        $url = $this->faraz_api_url;
        
        // HTTP headers for Faraz SMS
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->api_key
        ];
        
        $param = [
            "sending_type" => "pattern",
            "from_number" => $this->sender,
            "code" => $this->pattern_code,
            "recipients" => [$phone],
            "params" => [
                "code" => $verification_code
            ]
        ];
        
        error_log('=== FARAZ SMS SEND (Pattern) ===');
        error_log('Faraz URL: ' . $url);
        error_log('Faraz Params: ' . json_encode($param));
        error_log('=== END FARAZ SMS SEND ===');
        
        return $this->make_http_request($url, $headers, $param);
    }
    
    /**
     * ارسال مستقیم SMS از Faraz بدون استفاده از pattern
     */
    private function send_faraz_direct_sms($phone, $verification_code) {
        $url = $this->faraz_api_url;
        
        // HTTP headers for Faraz SMS
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->api_key
        ];
        
        // متن پیامک با فرمت مناسب برای WebOTP API
        $message = $this->get_webotp_formatted_message($verification_code);
        
        // Faraz Direct SMS API format
        $param = [
            "sending_type" => "simple",
            "from_number" => $this->sender,
            "recipients" => [$phone],
            "message" => $message
        ];
        
        error_log('=== FARAZ DIRECT SMS SEND ===');
        error_log('Faraz URL: ' . $url);
        error_log('Faraz Message: ' . $message);
        error_log('Faraz Params: ' . json_encode($param));
        error_log('=== END FARAZ DIRECT SMS SEND ===');
        
        return $this->make_http_request($url, $headers, $param);
    }
    
    private function send_ipanel_sms($phone, $verification_code) {
        $url = $this->ipanel_api_url;
        
        // HTTP headers for IPanel - using Bearer token
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];
        
        // IPanel API format based on official documentation
        $param = [
            "pattern_code" => $this->pattern_code,
            "originator" => $this->sender,
            "recipient" => $phone,
            "values" => [
                "code" => $verification_code
            ]
        ];
        
        error_log('=== IPANEL SMS SEND (Pattern) ===');
        error_log('IPanel URL: ' . $url);
        error_log('IPanel Headers: ' . json_encode($headers));
        error_log('IPanel Params: ' . json_encode($param));
        error_log('=== END IPANEL SMS SEND ===');
        
        return $this->make_http_request_with_retry($url, $headers, $param);
    }
    
    /**
     * ارسال مستقیم SMS از IPanel بدون استفاده از pattern
     */
    private function send_ipanel_direct_sms($phone, $verification_code) {
        $url = 'https://edge.ippanel.com/v1/messages';
        
        // HTTP headers for IPanel - using Bearer token
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];
        
        // متن پیامک با فرمت مناسب برای WebOTP API
        $message = $this->get_webotp_formatted_message($verification_code);
        
        // IPanel Direct SMS API format
        $param = [
            "originator" => $this->sender,
            "recipients" => [$phone],
            "message" => $message
        ];
        
        error_log('=== IPANEL DIRECT SMS SEND ===');
        error_log('IPanel URL: ' . $url);
        error_log('IPanel Headers: ' . json_encode($headers));
        error_log('IPanel Message: ' . $message);
        error_log('IPanel Params: ' . json_encode($param));
        error_log('=== END IPANEL DIRECT SMS SEND ===');
        
        return $this->make_http_request_with_retry($url, $headers, $param);
    }
    
    private function send_melipayamak_sms($phone, $verification_code) {
        $url = $this->melipayamak_api_url;
        
        // متن پیامک با فرمت مناسب برای WebOTP API
        $text = $this->get_webotp_formatted_message($verification_code);
        
        // آماده‌سازی داده‌ها برای ارسال
        $data = array(
            'username' => $this->username,
            'password' => $this->password,
            'to' => $phone,
            'from' => $this->sender,
            'text' => $text
        );
        
        // تبدیل به query string
        $post_data = http_build_query($data);
        
        error_log('=== MELIPAYAMAK SMS SEND ===');
        error_log('Melipayamak URL: ' . $url);
        error_log('Melipayamak Data: ' . $post_data);
        error_log('=== END MELIPAYAMAK SMS SEND ===');
        
        // ارسال با cURL
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            'content-type' => 'application/x-www-form-urlencoded'
        ));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
        
        $response = curl_exec($handle);
        $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($handle);
        curl_close($handle);
        
        error_log('Melipayamak Response: ' . $response);
        error_log('Melipayamak HTTP Code: ' . $http_code);
        error_log('Melipayamak cURL Error: ' . $curl_error);
        
        if ($response === false || !empty($curl_error)) {
            return array(
                'success' => false,
                'message' => __('خطا در ارسال پیامک: ' . $curl_error, 'woocommerce-phone-auth')
            );
        }
        
        // بررسی پاسخ
        // پاسخ ملی پیامک می‌تواند یک رشته یا JSON باشد
        if ($http_code == 200) {
            $response_str = trim($response);
            
            // بررسی اگر پاسخ JSON است
            $json_response = json_decode($response_str, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json_response)) {
                // پاسخ JSON است
                // بررسی RetStatus یا status یا success
                if (isset($json_response['RetStatus']) && intval($json_response['RetStatus']) > 0) {
                    return array(
                        'success' => true,
                        'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                    );
                } elseif (isset($json_response['StrRetStatus']) && stripos($json_response['StrRetStatus'], 'ok') !== false) {
                    return array(
                        'success' => true,
                        'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                    );
                } elseif (isset($json_response['status']) && ($json_response['status'] === 'success' || $json_response['status'] === 1)) {
                    return array(
                        'success' => true,
                        'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                    );
                } elseif (isset($json_response['success']) && $json_response['success'] === true) {
                    return array(
                        'success' => true,
                        'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                    );
                } else {
                    // JSON است اما موفقیت‌آمیز نیست
                    return array(
                        'success' => false,
                        'message' => __('خطا در ارسال پیامک. پاسخ: ' . $response_str, 'woocommerce-phone-auth')
                    );
                }
            }
            
            // اگر JSON نیست، بررسی رشته ساده
            // کدهای خطا معمولاً با اعداد منفی شروع می‌شوند
            if (is_numeric($response_str) && intval($response_str) > 0) {
                return array(
                    'success' => true,
                    'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                );
            } elseif (stripos($response_str, 'ارسال') !== false || stripos($response_str, 'success') !== false || stripos($response_str, 'ok') !== false) {
                return array(
                    'success' => true,
                    'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                );
            } else {
                // ممکن است کد خطا باشد
                return array(
                    'success' => false,
                    'message' => __('خطا در ارسال پیامک. پاسخ: ' . $response_str, 'woocommerce-phone-auth')
                );
            }
        } else {
            return array(
                'success' => false,
                'message' => __('خطا در ارسال پیامک - کد خطا: ' . $http_code, 'woocommerce-phone-auth')
            );
        }
    }
    
    private function make_http_request($url, $headers, $param) {
        
        // Convert array to JSON
        $json_data = json_encode($param);
        
        // Initialize CURL with better settings for IPanel
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $json_data);    
        curl_setopt($handler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handler, CURLOPT_TIMEOUT, 30);
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handler, CURLOPT_VERBOSE, true);
        curl_setopt($handler, CURLOPT_USERAGENT, 'WooCommerce Phone Auth Plugin');
        curl_setopt($handler, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        
        $response = curl_exec($handler);
        $http_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($handler);
        $curl_info = curl_getinfo($handler);
        curl_close($handler);
        
        // Log the request and response for debugging
        error_log('=== WCPA SMS DEBUG ===');
        error_log('WCPA SMS Provider: ' . $this->provider);
        error_log('WCPA SMS API Key Length: ' . strlen($this->api_key));
        error_log('WCPA SMS API Key First 10 chars: ' . substr($this->api_key, 0, 10));
        error_log('WCPA SMS API Key Last 10 chars: ' . substr($this->api_key, -10));
        error_log('WCPA SMS Sender: ' . $this->sender);
        error_log('WCPA SMS Pattern: ' . $this->pattern_code);
        error_log('WCPA SMS Phone: ' . $phone);
        error_log('WCPA SMS URL: ' . $url);
        error_log('WCPA SMS Request: ' . $json_data);
        error_log('WCPA SMS Headers: ' . json_encode($headers));
        error_log('WCPA SMS Response: ' . $response);
        error_log('WCPA SMS HTTP Code: ' . $http_code);
        error_log('WCPA SMS cURL Error: ' . $curl_error);
        error_log('WCPA SMS cURL Info: ' . json_encode($curl_info));
        error_log('=== END WCPA SMS DEBUG ===');
        
        if ($response === false || !empty($curl_error)) {
            return array(
                'success' => false,
                'message' => __('خطا در ارسال پیامک: ' . $curl_error, 'woocommerce-phone-auth')
            );
        }
        
        $result = json_decode($response, true);
        
        if ($http_code == 200) {
            // Check for different possible success indicators from SMS APIs
            if ((isset($result['status']) && $result['status'] == 'ok') || 
                (isset($result['status']) && $result['status'] == 'success') ||
                (isset($result['success']) && $result['success'] == true) ||
                (isset($result['code']) && $result['code'] == 200) ||
                (isset($result['result']) && $result['result'] == 'success') ||
                (isset($result['RetStatus']) && intval($result['RetStatus']) > 0) ||
                (isset($result['StrRetStatus']) && stripos($result['StrRetStatus'], 'ok') !== false)) {
                return array(
                    'success' => true,
                    'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                );
            } else {
                // If we get here but SMS was actually sent, it might be a response format issue
                // Check if response has RetStatus with value > 0 (IPanel/Faraz success format)
                if (isset($result['RetStatus']) && intval($result['RetStatus']) > 0) {
                    return array(
                        'success' => true,
                        'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                    );
                }
                // Let's be more lenient and consider it successful if HTTP code is 200 and no error field
                if (empty($result) || (!isset($result['status']) && !isset($result['error']) && !isset($result['RetStatus']))) {
                    return array(
                        'success' => true,
                        'message' => __('پیامک ارسال شد', 'woocommerce-phone-auth')
                    );
                }
                
                $error_message = isset($result['message']) ? $result['message'] : 
                                (isset($result['error']) ? $result['error'] : 
                                __('خطا در ارسال پیامک', 'woocommerce-phone-auth'));
                return array(
                    'success' => false,
                    'message' => $error_message
                );
            }
        } else {
            $error_message = isset($result['message']) ? $result['message'] : 
                            (isset($result['error']) ? $result['error'] : 
                            __('خطا در ارسال پیامک - کد خطا: ' . $http_code, 'woocommerce-phone-auth'));
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
    }
    
    private function make_http_request_with_retry($url, $headers, $param, $max_retries = 3) {
        for ($i = 0; $i < $max_retries; $i++) {
            $result = $this->make_http_request($url, $headers, $param);
            
            // If successful or not a 502 error, return result
            if ($result['success'] || !isset($result['message']) || strpos($result['message'], '502') === false) {
                return $result;
            }
            
            // If 502 error, wait and retry
            if ($i < $max_retries - 1) {
                error_log('WCPA SMS: 502 error, retrying in ' . (($i + 1) * 2) . ' seconds...');
                sleep(($i + 1) * 2); // Wait 2, 4, 6 seconds between retries
            }
        }
        
        return $result;
    }
    
    public function test_sms($phone) {
        $test_code = "1234";
        
        // Log test attempt
        error_log('=== WCPA SMS TEST START ===');
        error_log('WCPA SMS Test - Provider: ' . $this->provider);
        error_log('WCPA SMS Test - API Key exists: ' . (!empty($this->api_key) ? 'YES' : 'NO'));
        error_log('WCPA SMS Test - Sender exists: ' . (!empty($this->sender) ? 'YES' : 'NO'));
        error_log('WCPA SMS Test - Pattern exists: ' . (!empty($this->pattern_code) ? 'YES' : 'NO'));
        error_log('WCPA SMS Test - API Key length: ' . strlen($this->api_key));
        error_log('=== WCPA SMS TEST END ===');
        
        return $this->send_pattern_sms($phone, $test_code);
    }
    
    private function prepare_phone_number($phone) {
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // If phone starts with 09, convert to 989 format for Faraz SMS
        if (preg_match('/^09/', $phone)) {
            $phone = '98' . substr($phone, 1);
        }
        
        // If phone doesn't start with 98, add it
        if (!preg_match('/^98/', $phone)) {
            $phone = '98' . $phone;
        }
        
        return $phone;
    }
    
    public function validate_settings() {
        $errors = array();
        
        if ($this->provider === 'melipayamak') {
            // اعتبارسنجی برای ملی پیامک
            if (empty($this->username)) {
                $errors[] = 'نام کاربری ملی پیامک تنظیم نشده';
            }
            
            if (empty($this->password)) {
                $errors[] = 'رمز عبور ملی پیامک تنظیم نشده';
            }
            
            if (empty($this->sender)) {
                $errors[] = 'شماره فرستنده تنظیم نشده';
            }
        } else {
            // اعتبارسنجی برای سایر سرویس‌ها
            if (empty($this->api_key)) {
                $errors[] = 'کلید API تنظیم نشده';
            }
            
            if (empty($this->sender)) {
                $errors[] = 'شماره فرستنده تنظیم نشده';
            }
            
            if (empty($this->pattern_code)) {
                $errors[] = 'کد پترن تنظیم نشده';
            }
            
            // Additional validation for IPanel
            if ($this->provider === 'ipanel' && !empty($this->api_key)) {
                // Check if it looks like a Bearer token (usually longer and contains specific characters)
                if (strlen($this->api_key) < 30) {
                    $errors[] = 'Bearer Token برای IPPanel باید حداقل 30 کاراکتر باشد';
                }
            }
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
    
    public function test_ipanel_connection() {
        if ($this->provider !== 'ipanel') {
            return array(
                'success' => false,
                'message' => 'این تست فقط برای IPPanel است'
            );
        }
        
        // Test connection to IPPanel API
        $url = 'https://edge.ippanel.com/v1/user';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];
        
        error_log('=== IPANEL CONNECTION TEST ===');
        error_log('IPanel URL: ' . $url);
        error_log('IPanel Headers: ' . json_encode($headers));
        error_log('IPanel API Key Length: ' . strlen($this->api_key));
        error_log('IPanel API Key: ' . substr($this->api_key, 0, 20) . '...');
        
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handler, CURLOPT_TIMEOUT, 10);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($handler);
        $http_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($handler);
        curl_close($handler);
        
        error_log('IPanel Connection Test - HTTP Code: ' . $http_code);
        error_log('IPanel Connection Test - Response: ' . $response);
        error_log('IPanel Connection Test - cURL Error: ' . $curl_error);
        error_log('=== END IPANEL CONNECTION TEST ===');
        
        if ($http_code === 200) {
            return array(
                'success' => true,
                'message' => 'اتصال به IPPanel موفقیت‌آمیز'
            );
        } else {
            return array(
                'success' => false,
                'message' => 'خطا در اتصال به IPPanel - کد: ' . $http_code . ' - پاسخ: ' . $response
            );
        }
    }
}
