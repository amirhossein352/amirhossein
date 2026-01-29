<?php
/**
 * Test file for IPPanel SMS API based on official documentation
 * https://ippanelcom.github.io/Edge-Document/docs/
 */

// Include WordPress
require_once('../../../wp-config.php');

// Include SMS class
require_once('includes/class-wcpa-sms.php');

echo "<h1>تست پنل ای پنل - بر اساس مستندات رسمی</h1>";
echo "<p>مستندات: <a href='https://ippanelcom.github.io/Edge-Document/docs/' target='_blank'>IPPanel Edge API Documentation</a></p>";

// Test IPanel SMS
$sms = new WCPA_SMS();

// Check settings
$provider = get_option('wcpa_sms_provider', 'faraz');
$api_key = get_option('wcpa_sms_api_key', '');
$sender = get_option('wcpa_sms_sender', '');
$pattern_code = get_option('wcpa_sms_pattern_code', '');

echo "<h2>تنظیمات فعلی:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><td><strong>Provider:</strong></td><td>" . $provider . "</td></tr>";
echo "<tr><td><strong>API Key Length:</strong></td><td>" . strlen($api_key) . "</td></tr>";
echo "<tr><td><strong>API Key (first 10 chars):</strong></td><td>" . substr($api_key, 0, 10) . "...</td></tr>";
echo "<tr><td><strong>Sender:</strong></td><td>" . $sender . "</td></tr>";
echo "<tr><td><strong>Pattern Code:</strong></td><td>" . $pattern_code . "</td></tr>";
echo "</table>";

if ($provider !== 'ipanel') {
    echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border: 1px solid #f44336; border-radius: 4px;'>";
    echo "<h3 style='color: #d32f2f; margin: 0 0 10px 0;'>❌ خطا: Provider باید روی 'ipanel' تنظیم شود</h3>";
    echo "<p>لطفاً در تنظیمات افزونه، Provider را روی 'ipanel' قرار دهید.</p>";
    echo "</div>";
    exit;
}

// Test connection first
echo "<h2>تست اتصال به API:</h2>";
$connection_test = $sms->test_ipanel_connection();
if ($connection_test['success']) {
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border: 1px solid #4caf50; border-radius: 4px;'>";
    echo "<h3 style='color: #2e7d32; margin: 0 0 10px 0;'>✅ " . $connection_test['message'] . "</h3>";
    echo "</div>";
} else {
    echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border: 1px solid #f44336; border-radius: 4px;'>";
    echo "<h3 style='color: #d32f2f; margin: 0 0 10px 0;'>❌ " . $connection_test['message'] . "</h3>";
    echo "</div>";
}

// Test SMS sending
echo "<h2>تست ارسال پیامک:</h2>";
$test_phone = '09123456789'; // Replace with your test phone number
echo "<p><strong>شماره تست:</strong> " . $test_phone . "</p>";
echo "<p><strong>توجه:</strong> این شماره فقط برای تست است. لطفاً شماره واقعی خود را وارد کنید.</p>";

$test_result = $sms->test_sms($test_phone);

if ($test_result['success']) {
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border: 1px solid #4caf50; border-radius: 4px;'>";
    echo "<h3 style='color: #2e7d32; margin: 0 0 10px 0;'>✅ " . $test_result['message'] . "</h3>";
    echo "</div>";
} else {
    echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border: 1px solid #f44336; border-radius: 4px;'>";
    echo "<h3 style='color: #d32f2f; margin: 0 0 10px 0;'>❌ " . $test_result['message'] . "</h3>";
    
    // Show detailed error information
    if (strpos($test_result['message'], '502') !== false) {
        echo "<div style='background: #fff3e0; padding: 15px; margin: 10px 0; border: 1px solid #ff9800; border-radius: 4px;'>";
        echo "<h4 style='color: #e65100; margin: 0 0 10px 0;'>راه‌حل‌های خطای 502:</h4>";
        echo "<ul>";
        echo "<li>مطمئن شوید API Key صحیح است (Bearer Token)</li>";
        echo "<li>بررسی کنید که Bearer Token معتبر باشد</li>";
        echo "<li>ممکن است سرور پنل ای پنل موقتاً در دسترس نباشد</li>";
        echo "<li>سعی کنید بعداً دوباره تست کنید</li>";
        echo "<li>بررسی کنید که شماره فرستنده در پنل تایید شده باشد</li>";
        echo "<li>بررسی کنید که کد پترن در پنل تعریف شده باشد</li>";
        echo "</ul>";
        echo "</div>";
    }
    echo "</div>";
}

// Show validation
echo "<h2>اعتبارسنجی تنظیمات:</h2>";
$validation = $sms->validate_settings();
if ($validation['valid']) {
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border: 1px solid #4caf50; border-radius: 4px;'>";
    echo "<h3 style='color: #2e7d32; margin: 0 0 10px 0;'>✅ تنظیمات صحیح است</h3>";
    echo "</div>";
} else {
    echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border: 1px solid #f44336; border-radius: 4px;'>";
    echo "<h3 style='color: #d32f2f; margin: 0 0 10px 0;'>❌ خطاهای موجود:</h3>";
    echo "<ul>";
    foreach ($validation['errors'] as $error) {
        echo "<li>" . $error . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<h2>نکات مهم بر اساس مستندات رسمی:</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px 0; border: 1px solid #2196f3; border-radius: 4px;'>";
echo "<ul>";
echo "<li><strong>Base URL:</strong> <code>https://edge.ippanel.com/v1</code></li>";
echo "<li><strong>Authentication:</strong> Bearer Token در header <code>Authorization</code></li>";
echo "<li><strong>Token:</strong> باید 30-255 کاراکتر باشد و 10 ساعت اعتبار دارد</li>";
echo "<li><strong>API Key:</strong> از پنل کاربری > Developers > Access Keys قابل دریافت است</li>";
echo "<li><strong>شماره موبایل:</strong> باید با فرمت 98 شروع شود (مثل 989123456789)</li>";
echo "<li><strong>مستندات کامل:</strong> <a href='https://ippanelcom.github.io/Edge-Document/docs/' target='_blank'>IPPanel Edge API Documentation</a></li>";
echo "</ul>";
echo "</div>";

// Show recent error logs
echo "<h2>لاگ‌های اخیر:</h2>";
$log_file = WP_CONTENT_DIR . '/debug.log';
if (file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    $lines = explode("\n", $logs);
    $recent_lines = array_slice($lines, -100); // Last 100 lines
    
    echo "<div style='background: #f5f5f5; padding: 15px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;'>";
    echo "<pre style='max-height: 400px; overflow-y: scroll; font-size: 12px;'>";
    foreach ($recent_lines as $line) {
        if (strpos($line, 'WCPA') !== false || strpos($line, 'IPANEL') !== false) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
    echo "</div>";
} else {
    echo "<p>فایل لاگ یافت نشد</p>";
}

echo "<hr>";
echo "<p><small>تست بر اساس مستندات رسمی پنل ای پنل - <a href='https://ippanelcom.github.io/Edge-Document/docs/' target='_blank'>مستندات کامل</a></small></p>";
?>
