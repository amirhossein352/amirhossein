<?php
/**
 * Simple Shortcodes for Login/Register
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCPA_Shortcode {
    
    public function __construct() {
        add_shortcode('login_desktop', array($this, 'desktop_login'));
        add_shortcode('login_mobile', array($this, 'mobile_login'));
        add_shortcode('wcpa_phone_login_form', array($this, 'phone_login_form'));
        add_shortcode('wcpa_login_form', array($this, 'page_login_form'));
        add_shortcode('wcpa_test_myaccount', array($this, 'test_myaccount_access'));
        add_shortcode('wcpa_test_redirect', array($this, 'test_redirect'));
        add_shortcode('wcpa_fix_myaccount', array($this, 'wcpa_fix_myaccount'));
    }
    
    private function get_myaccount_url() {
        // Use WooCommerce function to get myaccount URL
        if (function_exists('wc_get_page_permalink')) {
            $url = wc_get_page_permalink('myaccount');
            if ($url) {
                return $url;
            }
        }
        
        // Fallback: Get WooCommerce myaccount page ID
        $myaccount_id = get_option('woocommerce_myaccount_page_id');
        if ($myaccount_id) {
            $url = get_permalink($myaccount_id);
            if ($url) {
                return $url;
            }
        }
        
        // Last fallback
        return home_url('/my-account/');
    }
    
    public function desktop_login($atts) {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $phone = get_user_meta($user->ID, 'billing_phone', true);
            $name = $phone ? $phone : $user->display_name;
            
            return '<div class="wcpa-user-dropdown">
                <button class="wcpa-user-btn" onclick="toggleUserMenu()">
                <span>سلام ' . esc_html($name) . '</span>
                    <span class="wcpa-dropdown-arrow">▼</span>
                </button>
                <div class="wcpa-user-menu" id="wcpa-userMenu">
                    <a href="' . esc_url($this->get_myaccount_url()) . '">حساب کاربری</a>
                    <a href="' . esc_url(wp_logout_url(home_url())) . '">خروج</a>
                </div>
            </div>';
        } else {
            return '<button class="wcpa-login-btn" onclick="openLogin()">ورود / ثبت نام</button>';
        }
    }
    
    public function mobile_login($atts) {
        if (is_user_logged_in()) {
            return '<a href="' . esc_url($this->get_myaccount_url()) . '" class="wcpa-mobile-btn" title="حساب کاربری">👤</a>';
        } else {
            return '<button class="wcpa-mobile-btn" onclick="openLogin()" title="ورود">🔑</button>';
        }
    }
    
    public function phone_login_form($atts) {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $phone = get_user_meta($user->ID, 'billing_phone', true);
            $name = $phone ? $phone : $user->display_name;
            
            return '<div class="wcpa-logged-in">
                <div class="welcome-message">
                    <h2>خوش آمدید ' . esc_html($name) . '</h2>
                    <p>شما با موفقیت وارد شده‌اید</p>
                </div>
                <div class="user-actions">
                    <a href="' . esc_url($this->get_myaccount_url()) . '" class="btn btn-primary">حساب کاربری</a>
                    <a href="' . esc_url(wc_get_cart_url()) . '" class="btn btn-secondary">سبد خرید</a>
                    <a href="' . esc_url(wp_logout_url(home_url())) . '" class="btn btn-outline">خروج</a>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "' . esc_url($this->get_myaccount_url()) . '";
                    }, 2000);
                </script>
            </div>';
        }
        
        return '<div class="wcpa-login-form">
            <div class="form-container">
                <div class="form-header">
                    <h2>ورود / ثبت نام</h2>
                    <p>با شماره تماس خود وارد شوید یا ثبت نام کنید</p>
                </div>
                
                <form id="wcpa-page-login-form" class="login-form">
                    <div class="form-group">
                        <label for="page-phone">شماره تماس</label>
                        <input type="tel" id="page-phone" name="phone" placeholder="09123456789" maxlength="11" inputmode="numeric" autocomplete="tel" required>
                        <small class="form-text">شماره تماس خود را وارد کنید</small>
                    </div>
                    
                    <div class="form-group" id="page-code-group" style="display: none;">
                        <label for="page-code">کد تایید</label>
                        <input type="text" id="page-code" name="code" placeholder="1234" maxlength="4" inputmode="numeric" autocomplete="one-time-code" required>
                        <div class="wcpa-resend-row" style="margin-top:8px; display:flex; align-items:center; gap:8px;">
                            <button type="button" id="page-resend" class="btn btn-outline" disabled>ارسال مجدد</button>
                            <span id="page-timer" style="font-size:12px;color:#666;"></span>
                        </div>
                        <small class="form-text">کد 4 رقمی ارسال شده به شماره شما</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="page-main-btn" class="btn btn-primary btn-full">
                            <span class="wcpa-btn-text">ارسال کد تایید</span>
                            <span class="wcpa-loading-spinner" style="display: none;"></span>
                        </button>
                    </div>
                </form>
                
                <div id="page-message" class="message" style="display: none;"></div>
                
                <div class="form-footer">
                    <p>با ورود یا ثبت نام، شما با <a href="' . esc_url(get_option('wcpa_terms_url', '#')) . '">قوانین و مقررات</a> موافقت می‌کنید</p>
                </div>
                
                ' . $this->get_google_login_button() . '
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            var phoneInput = $("#page-phone");
            var codeInput = $("#page-code");
            var codeGroup = $("#page-code-group");
            var mainBtn = $("#page-main-btn");
            var resendBtn = $("#page-resend");
            var timerSpan = $("#page-timer");
            var messageDiv = $("#page-message");
            
            // Main button functionality (like popup forms)
            mainBtn.on("click", function() {
                var phone = phoneInput.val().trim();
                var code = codeInput.val().trim();
                var $btnText = mainBtn.find(".wcpa-btn-text");
                var $spinner = mainBtn.find(".wcpa-loading-spinner");
                
                // If code group is hidden, send code
                if (!codeGroup.is(":visible")) {
                    if (!phone) {
                        showMessage("لطفا شماره تماس را وارد کنید", "error");
                        return;
                    }
                    
                    if (phone.length !== 11) {
                        showMessage("شماره تماس باید 11 رقم باشد", "error");
                        return;
                    }
                    
                    mainBtn.prop("disabled", true);
                    $btnText.text("در حال ارسال...");
                    $spinner.show();
                    
                    $.ajax({
                        url: wcpa_ajax.ajax_url,
                        type: "POST",
                        data: {
                            action: "wcpa_send_code",
                            phone: phone,
                            nonce: wcpa_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                codeGroup.show();
                                $btnText.text("تایید و ورود");
                                $spinner.hide();
                                mainBtn.removeClass("btn-primary").addClass("btn-success");
                                phoneInput.prop("disabled", true);
                                startTimer();
                                showMessage("کد تایید ارسال شد", "success");
                                codeInput.focus();
                                // WebOTP - خواندن خودکار کد از پیامک در موبایل
                                if (typeof window.tryStartWebOTP === "function") {
                                    window.tryStartWebOTP("#page-code");
                                }
                                // Auto-submit for desktop when 4 digits entered
                                if (!/Mobile|Android|iPhone|iPad/i.test(navigator.userAgent)) {
                                    codeInput.on("input", function() {
                                        if (codeInput.val().length === 4) {
                                            setTimeout(function() {
                                                mainBtn.trigger("click");
                                            }, 300);
                                        }
                                    });
                                }
                            } else {
                                var errorMsg = "خطا در ارسال کد";
                                if (response.data) {
                                    if (typeof response.data === "string") {
                                        errorMsg = response.data;
                                    } else if (response.data.message) {
                                        errorMsg = response.data.message;
                                    }
                                }
                                showMessage(errorMsg, "error");
                                $btnText.text("ارسال کد تایید");
                                $spinner.hide();
                            }
                        },
                        error: function() {
                            showMessage("خطا در ارتباط با سرور", "error");
                            $btnText.text("ارسال کد تایید");
                            $spinner.hide();
                        },
                        complete: function() {
                            mainBtn.prop("disabled", false);
                        }
                    });
                } else {
                    // Code group is visible, verify code
                    if (!code) {
                        showMessage("لطفا کد تایید را وارد کنید", "error");
                        return;
                    }
                    
                    if (code.length !== 4) {
                        showMessage("کد تایید باید 4 رقم باشد", "error");
                        return;
                    }
                    
                    mainBtn.prop("disabled", true);
                    $btnText.text("در حال تایید...");
                    $spinner.show();
                    
                    $.ajax({
                        url: wcpa_ajax.ajax_url,
                        type: "POST",
                        data: {
                            action: "wcpa_login_register",
                            phone: phone,
                            code: code,
                            action_type: "auto",
                            nonce: wcpa_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage("ورود موفقیت‌آمیز", "success");
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                var errorMsg = "کد تایید اشتباه است";
                                if (response.data) {
                                    if (typeof response.data === "string") {
                                        errorMsg = response.data;
                                    } else if (response.data.message) {
                                        errorMsg = response.data.message;
                                    }
                                }
                                showMessage(errorMsg, "error");
                                $btnText.text("تایید و ورود");
                                $spinner.hide();
                            }
                        },
                        error: function() {
                            showMessage("خطا در ارتباط با سرور", "error");
                            $btnText.text("تایید و ورود");
                            $spinner.hide();
                        },
                        complete: function() {
                            mainBtn.prop("disabled", false);
                        }
                    });
                }
            });
            
            // Resend code functionality
            resendBtn.on("click", function() {
                mainBtn.trigger("click");
            });
            
            function showMessage(text, type) {
                messageDiv.removeClass("success error").addClass(type).text(text).show();
                setTimeout(function() {
                    messageDiv.fadeOut();
                }, 5000);
            }
            
            function startTimer() {
                var timeLeft = 60;
                resendBtn.prop("disabled", true);
                
                var timer = setInterval(function() {
                    timerSpan.text(timeLeft + " ثانیه باقی مانده");
                    timeLeft--;
                    
                    if (timeLeft < 0) {
                        clearInterval(timer);
                        resendBtn.prop("disabled", false);
                        timerSpan.text("");
                    }
                }, 1000);
            }
        });
        </script>';
    }
    
    public function test_myaccount_access($atts) {
        $pages = new WCPA_Pages();
        $test_result = $pages->test_myaccount_access();
        
        $output = '<div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 8px;">';
        $output .= '<h3>تست دسترسی به حساب کاربری</h3>';
        $output .= '<p><strong>شناسه صفحه حساب کاربری:</strong> ' . ($test_result['myaccount_id'] ?: 'تعریف نشده') . '</p>';
        $output .= '<p><strong>وضعیت لاگین:</strong> ' . ($test_result['is_logged_in'] ? 'لاگین کرده' : 'لاگین نکرده') . '</p>';
        $output .= '<p><strong>شناسه کاربر:</strong> ' . ($test_result['user_id'] ?: 'هیچ') . '</p>';
        
        if ($test_result['myaccount_id']) {
            $myaccount_url = get_permalink($test_result['myaccount_id']);
            $output .= '<p><strong>لینک حساب کاربری:</strong> <a href="' . esc_url($myaccount_url) . '" target="_blank">' . esc_url($myaccount_url) . '</a></p>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    public function test_redirect($atts) {
        $pages = new WCPA_Pages();
        $login_page_id = $pages->get_login_page_id();
        $login_page_url = $pages->get_login_page_url();
        $is_logged_in = is_user_logged_in();
        
        $output = '<div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 8px; border: 1px solid #ffeaa7;">';
        $output .= '<h3>تست ریدایرکت</h3>';
        $output .= '<p><strong>وضعیت لاگین:</strong> ' . ($is_logged_in ? 'لاگین کرده' : 'لاگین نکرده') . '</p>';
        $output .= '<p><strong>شناسه صفحه لاگین:</strong> ' . ($login_page_id ?: 'تعریف نشده') . '</p>';
        $output .= '<p><strong>URL صفحه لاگین:</strong> ' . ($login_page_url ?: 'تعریف نشده') . '</p>';
        
        if ($login_page_url) {
            $output .= '<p><a href="' . esc_url($login_page_url) . '" target="_blank">مشاهده صفحه لاگین</a></p>';
        }
        
        $output .= '<p><a href="/my-account/" target="_blank">تست ریدایرکت از my-account</a></p>';
        $output .= '</div>';
        
        return $output;
    }
    
    public function page_login_form($atts) {
        $atts = shortcode_atts(array(
            'title' => 'ورود / ثبت نام',
            'description' => 'با شماره تماس خود وارد شوید یا ثبت نام کنید',
            'show_google' => 'true',
            'redirect' => ''
        ), $atts);
        
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $phone = get_user_meta($user->ID, 'billing_phone', true);
            $name = $phone ? $phone : $user->display_name;
            
            $redirect_url = !empty($atts['redirect']) ? $atts['redirect'] : $this->get_myaccount_url();
            
            return '<div class="wcpa-page-logged-in">
                <div class="welcome-message">
                    <h2>خوش آمدید ' . esc_html($name) . '</h2>
                    <p>شما با موفقیت وارد شده‌اید</p>
                </div>
                <div class="user-actions">
                    <a href="' . esc_url($redirect_url) . '" class="btn btn-primary">حساب کاربری</a>
                    <a href="' . esc_url(wc_get_cart_url()) . '" class="btn btn-secondary">سبد خرید</a>
                    <a href="' . esc_url(wp_logout_url(home_url())) . '" class="btn btn-outline">خروج</a>
                </div>
            </div>';
        }
        
        $form_id = 'wcpa-page-form-' . uniqid();
        
        $output = '<div class="wcpa-page-login-form">
            <div class="form-container">
                <div class="form-header">
                    <h2>' . esc_html($atts['title']) . '</h2>
                    <p>' . esc_html($atts['description']) . '</p>
                </div>
                
                <form id="' . esc_attr($form_id) . '" class="wcpa-page-form">
                    <div class="form-group">
                        <label for="' . esc_attr($form_id) . '-phone">شماره تماس</label>
                        <input type="tel" id="' . esc_attr($form_id) . '-phone" name="phone" placeholder="09123456789" maxlength="11" inputmode="numeric" autocomplete="tel" required>
                        <small class="form-text">شماره تماس خود را وارد کنید</small>
                    </div>
                    
                    <div class="form-group" id="' . esc_attr($form_id) . '-code-group" style="display: none;">
                        <label for="' . esc_attr($form_id) . '-code">کد تایید</label>
                        <input type="text" id="' . esc_attr($form_id) . '-code" name="code" placeholder="1234" maxlength="4" inputmode="numeric" autocomplete="one-time-code" required>
                        <div class="wcpa-resend-row" style="margin-top:8px; display:flex; align-items:center; gap:8px;">
                            <button type="button" id="' . esc_attr($form_id) . '-resend" class="btn btn-outline" disabled>ارسال مجدد</button>
                            <span id="' . esc_attr($form_id) . '-timer" style="font-size:12px;color:#666;"></span>
                        </div>
                        <small class="form-text">کد 4 رقمی ارسال شده به شماره شما</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="' . esc_attr($form_id) . '-main-btn" class="btn btn-primary btn-full">
                            <span class="wcpa-btn-text">ارسال کد تایید</span>
                            <span class="wcpa-loading-spinner" style="display: none;"></span>
                        </button>
                    </div>
                </form>
                
                <div id="' . esc_attr($form_id) . '-message" class="message" style="display: none;"></div>
                
                <div class="form-footer">
                    <p>با ورود یا ثبت نام، شما با <a href="' . esc_url(get_option('wcpa_terms_url', '#')) . '">قوانین و مقررات</a> موافقت می‌کنید</p>
                </div>';
                
        if ($atts['show_google'] === 'true') {
            $output .= $this->get_google_login_button();
        }
        
        $output .= '</div>
        </div>';
        
        // Add JavaScript for this specific form
        $output .= '<script>
        jQuery(document).ready(function($) {
            var formId = "' . esc_js($form_id) . '";
            var phoneInput = $("#" + formId + "-phone");
            var codeInput = $("#" + formId + "-code");
            var codeGroup = $("#" + formId + "-code-group");
            var mainBtn = $("#" + formId + "-main-btn");
            var resendBtn = $("#" + formId + "-resend");
            var timerSpan = $("#" + formId + "-timer");
            var messageDiv = $("#" + formId + "-message");
            var redirectUrl = "' . esc_js($atts['redirect']) . '";
            
            // Main button functionality (like popup forms)
            mainBtn.on("click", function() {
                var phone = phoneInput.val().trim();
                var code = codeInput.val().trim();
                var $btnText = mainBtn.find(".wcpa-btn-text");
                var $spinner = mainBtn.find(".wcpa-loading-spinner");
                
                // If code group is hidden, send code
                if (!codeGroup.is(":visible")) {
                    if (!phone) {
                        showMessage("لطفا شماره تماس را وارد کنید", "error");
                        return;
                    }
                    
                    if (phone.length !== 11) {
                        showMessage("شماره تماس باید 11 رقم باشد", "error");
                        return;
                    }
                    
                    mainBtn.prop("disabled", true);
                    $btnText.text("در حال ارسال...");
                    $spinner.show();
                    
                    $.ajax({
                        url: wcpa_ajax.ajax_url,
                        type: "POST",
                        data: {
                            action: "wcpa_send_code",
                            phone: phone,
                            nonce: wcpa_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                codeGroup.show();
                                $btnText.text("تایید و ورود");
                                $spinner.hide();
                                mainBtn.removeClass("btn-primary").addClass("btn-success");
                                phoneInput.prop("disabled", true);
                                startTimer();
                                showMessage("کد تایید ارسال شد", "success");
                                codeInput.focus();
                                // WebOTP - خواندن خودکار کد از پیامک در موبایل
                                if (typeof window.tryStartWebOTP === "function") {
                                    window.tryStartWebOTP("#" + formId + "-code");
                                }
                                // Auto-submit for desktop when 4 digits entered
                                if (!/Mobile|Android|iPhone|iPad/i.test(navigator.userAgent)) {
                                    codeInput.on("input", function() {
                                        if (codeInput.val().length === 4) {
                                            setTimeout(function() {
                                                mainBtn.trigger("click");
                                            }, 300);
                                        }
                                    });
                                }
                            } else {
                                var errorMsg = "خطا در ارسال کد";
                                if (response.data) {
                                    if (typeof response.data === "string") {
                                        errorMsg = response.data;
                                    } else if (response.data.message) {
                                        errorMsg = response.data.message;
                                    }
                                }
                                showMessage(errorMsg, "error");
                                $btnText.text("ارسال کد تایید");
                                $spinner.hide();
                            }
                        },
                        error: function() {
                            showMessage("خطا در ارتباط با سرور", "error");
                            $btnText.text("ارسال کد تایید");
                            $spinner.hide();
                        },
                        complete: function() {
                            mainBtn.prop("disabled", false);
                        }
                    });
                } else {
                    // Code group is visible, verify code
                    if (!code) {
                        showMessage("لطفا کد تایید را وارد کنید", "error");
                        return;
                    }
                    
                    if (code.length !== 4) {
                        showMessage("کد تایید باید 4 رقم باشد", "error");
                        return;
                    }
                    
                    mainBtn.prop("disabled", true);
                    $btnText.text("در حال تایید...");
                    $spinner.show();
                    
                    $.ajax({
                        url: wcpa_ajax.ajax_url,
                        type: "POST",
                        data: {
                            action: "wcpa_login_register",
                            phone: phone,
                            code: code,
                            action_type: "auto",
                            nonce: wcpa_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage("ورود موفقیت‌آمیز", "success");
                                setTimeout(function() {
                                    if (redirectUrl) {
                                        window.location.href = redirectUrl;
                                    } else {
                                        window.location.reload();
                                    }
                                }, 1000);
                            } else {
                                var errorMsg = "کد تایید اشتباه است";
                                if (response.data) {
                                    if (typeof response.data === "string") {
                                        errorMsg = response.data;
                                    } else if (response.data.message) {
                                        errorMsg = response.data.message;
                                    }
                                }
                                showMessage(errorMsg, "error");
                                $btnText.text("تایید و ورود");
                                $spinner.hide();
                            }
                        },
                        error: function() {
                            showMessage("خطا در ارتباط با سرور", "error");
                            $btnText.text("تایید و ورود");
                            $spinner.hide();
                        },
                        complete: function() {
                            mainBtn.prop("disabled", false);
                        }
                    });
                }
            });
            
            // Resend code functionality
            resendBtn.on("click", function() {
                mainBtn.trigger("click");
            });
            
            function showMessage(text, type) {
                messageDiv.removeClass("success error").addClass(type).text(text).show();
                setTimeout(function() {
                    messageDiv.fadeOut();
                }, 5000);
            }
            
            function startTimer() {
                var timeLeft = 60;
                resendBtn.prop("disabled", true);
                
                var timer = setInterval(function() {
                    timerSpan.text(timeLeft + " ثانیه باقی مانده");
                    timeLeft--;
                    
                    if (timeLeft < 0) {
                        clearInterval(timer);
                        resendBtn.prop("disabled", false);
                        timerSpan.text("");
                    }
                }, 1000);
            }
        });
        </script>';
        
        return $output;
    }
    
    public function wcpa_fix_myaccount() {
        $pages = new WCPA_Pages();
        $result = $pages->force_myaccount_fix();
        
        $output = '<div style="background: #f0f0f0; padding: 20px; border: 1px solid #ccc; margin: 20px 0;">';
        $output .= '<h3>حل مشکل حساب کاربری</h3>';
        
        if ($result) {
            $output .= '<p style="color: green;"><strong>✅ مشکل حل شد! صفحه حساب کاربری اصلاح شد.</strong></p>';
        } else {
            $output .= '<p style="color: red;"><strong>❌ خطا در حل مشکل!</strong></p>';
        }
        
        $output .= '<p><a href="/my-account/" target="_blank">تست صفحه حساب کاربری</a></p>';
        $output .= '</div>';
        
        return $output;
    }
    
    private function get_google_login_button() {
        $google_auth = new WCPA_Google_Auth();
        
        if (!$google_auth->is_enabled()) {
            return '';
        }
        
        $auth_url = $google_auth->get_auth_url();
        if (empty($auth_url)) {
            return '';
        }
        
        return '<div class="google-login-section">
            <div class="divider">
                <span>یا</span>
            </div>
            <button type="button" id="google-login-btn" class="btn btn-google">
                <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#000" fill-rule="evenodd">
                        <path d="M17.64 9.2045c0-.6381-.0573-1.2518-.1636-1.8409H9v3.4814h4.8436c-.2086 1.125-.8427 2.0782-1.7955 2.7164v2.2581h2.9087c1.7018-1.5668 2.6836-3.8741 2.6836-6.615z" fill="#4285F4"/>
                        <path d="M9 18c2.43 0 4.4673-.805 5.9564-2.1805l-2.9087-2.2581c-.8059.54-1.8368.859-3.0477.859-2.344 0-4.3282-1.5831-5.036-3.7104H.9574v2.3318C2.4382 15.9832 5.4818 18 9 18z" fill="#34A853"/>
                        <path d="M3.964 10.71c-.18-.54-.2822-1.1168-.2822-1.71s.1022-1.17.2822-1.71V4.9582H.9574A8.9965 8.9965 0 0 0 0 9c0 1.4523.3477 2.8268.9574 4.0418l3.0066-2.3318z" fill="#FBBC05"/>
                        <path d="M9 3.5795c1.3214 0 2.5077.4541 3.4405 1.346l2.5813-2.5814C13.4672.8918 11.43 0 9 0 5.4818 0 2.4382 2.0168.9574 4.9582L3.964 7.29C4.6718 5.1627 6.656 3.5795 9 3.5795z" fill="#EA4335"/>
                    </g>
                </svg>
                ورود با گوگل
            </button>
        </div>
        <script>
        document.getElementById("google-login-btn").addEventListener("click", function() {
            window.location.href = "' . esc_url($auth_url) . '";
        });
        </script>';
    }
    
}