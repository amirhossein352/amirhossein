<?php
/**
 * Test file for IPanel SMS API
 * This file helps debug IPanel SMS issues
 */

// Include WordPress
require_once('../../../wp-config.php');

// Include SMS class
require_once('includes/class-wcpa-sms.php');

// Test IPanel SMS
$sms = new WCPA_SMS();

echo "<h2>تست پنل ای پنل</h2>";

// Check settings
$provider = get_option('wcpa_sms_provider', 'faraz');
$api_key = get_option('wcpa_sms_api_key', '');
$sender = get_option('wcpa_sms_sender', '');
$pattern_code = get_option('wcpa_sms_pattern_code', '');

echo "<h3>تنظیمات فعلی:</h3>";
echo "Provider: " . $provider . "<br>";
echo "API Key Length: " . strlen($api_key) . "<br>";
echo "API Key (first 10 chars): " . substr($api_key, 0, 10) . "...<br>";
echo "Sender: " . $sender . "<br>";
echo "Pattern Code: " . $pattern_code . "<br>";

if ($provider !== 'ipanel') {
    echo "<p style='color: red;'>خطا: Provider باید روی 'ipanel' تنظیم شود</p>";
    exit;
}

// Test connection first
echo "<h3>تست اتصال:</h3>";
$connection_test = $sms->test_ipanel_connection();
if ($connection_test['success']) {
    echo "<p style='color: green;'>✓ " . $connection_test['message'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ " . $connection_test['message'] . "</p>";
}

// Test SMS sending
echo "<h3>تست ارسال پیامک:</h3>";
$test_phone = '09123456789'; // Replace with your test phone number
echo "<p>شماره تست: " . $test_phone . "</p>";
$test_result = $sms->test_sms($test_phone);

if ($test_result['success']) {
    echo "<p style='color: green;'>✓ " . $test_result['message'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ " . $test_result['message'] . "</p>";
    
    // Show detailed error information
    if (strpos($test_result['message'], '502') !== false) {
        echo "<div style='background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid #ff9999;'>";
        echo "<h4>راه‌حل‌های خطای 502:</h4>";
        echo "<ul>";
        echo "<li>مطمئن شوید API Key صحیح است</li>";
        echo "<li>بررسی کنید که Bearer Token معتبر باشد</li>";
        echo "<li>ممکن است سرور پنل ای پنل موقتاً در دسترس نباشد</li>";
        echo "<li>سعی کنید بعداً دوباره تست کنید</li>";
        echo "</ul>";
        echo "</div>";
    }
}

// Show validation
echo "<h3>اعتبارسنجی تنظیمات:</h3>";
$validation = $sms->validate_settings();
if ($validation['valid']) {
    echo "<p style='color: green;'>✓ تنظیمات صحیح است</p>";
} else {
    echo "<p style='color: red;'>✗ خطاهای موجود:</p>";
    foreach ($validation['errors'] as $error) {
        echo "<p style='color: red;'>- " . $error . "</p>";
    }
}

echo "<h3>نکات مهم:</h3>";
echo "<ul>";
echo "<li>مطمئن شوید API Key صحیح است (Bearer Token)</li>";
echo "<li>شماره فرستنده باید در پنل ای پنل تایید شده باشد</li>";
echo "<li>کد پترن باید در پنل ای پنل تعریف شده باشد</li>";
echo "<li>شماره موبایل باید با فرمت 98 شروع شود</li>";
echo "</ul>";

// Show recent error logs
echo "<h3>لاگ‌های اخیر:</h3>";
$log_file = WP_CONTENT_DIR . '/debug.log';
if (file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    $lines = explode("\n", $logs);
    $recent_lines = array_slice($lines, -50); // Last 50 lines
    
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 300px; overflow-y: scroll;'>";
    foreach ($recent_lines as $line) {
        if (strpos($line, 'WCPA') !== false || strpos($line, 'IPANEL') !== false) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>فایل لاگ یافت نشد</p>";
}
?>
