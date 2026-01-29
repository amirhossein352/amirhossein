jQuery(document).ready(function($) {
    'use strict';
    
    // Test SMS functionality
    $('#test-sms-btn').on('click', function() {
        var phone = $('#test-phone').val();
        var result = $('#test-sms-result');
        var button = $(this);
        
        if (!phone) {
            showTestResult('لطفاً شماره تماس را وارد کنید', 'error');
            return;
        }
        
        if (!/^09[0-9]{9}$/.test(phone)) {
            showTestResult('فرمت شماره تماس صحیح نیست', 'error');
            return;
        }
        
        button.prop('disabled', true).text('در حال ارسال...');
        
        $.ajax({
            url: wcpa_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_test_sms',
                phone: phone,
                nonce: wcpa_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showTestResult('پیامک تست با موفقیت ارسال شد', 'success');
                } else {
                    showTestResult(response.data.message || 'خطا در ارسال پیامک', 'error');
                }
            },
            error: function() {
                showTestResult('خطا در ارتباط با سرور', 'error');
            },
            complete: function() {
                button.prop('disabled', false).text('ارسال تست');
            }
        });
    });
    
    // Validate SMS settings
    $('#validate-settings-btn').on('click', function() {
        var result = $('#settings-validation-result');
        var button = $(this);
        
        button.prop('disabled', true).text('در حال بررسی...');
        
        $.ajax({
            url: wcpa_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_validate_sms_settings',
                nonce: wcpa_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showValidationResult('همه تنظیمات صحیح است', 'success');
                } else {
                    showValidationResult(response.data.message || 'مشکل در تنظیمات', 'error');
                }
            },
            error: function() {
                showValidationResult('خطا در ارتباط با سرور', 'error');
            },
            complete: function() {
                button.prop('disabled', false).text('بررسی تنظیمات');
            }
        });
    });
    
    // Test IPPanel connection
    $('#test-ipanel-connection-btn').on('click', function() {
        var result = $('#settings-validation-result');
        var button = $(this);
        
        button.prop('disabled', true).text('در حال تست...');
        
        $.ajax({
            url: wcpa_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_test_ipanel_connection',
                nonce: wcpa_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showValidationResult('اتصال به IPPanel موفقیت‌آمیز', 'success');
                } else {
                    showValidationResult(response.data.message || 'خطا در اتصال به IPPanel', 'error');
                }
            },
            error: function() {
                showValidationResult('خطا در ارتباط با سرور', 'error');
            },
            complete: function() {
                button.prop('disabled', false).text('تست اتصال IPPanel');
            }
        });
    });
    
    function showTestResult(message, type) {
        var result = $('#test-sms-result');
        result.removeClass('success error').addClass(type).text(message).show();
        
        setTimeout(function() {
            result.fadeOut();
        }, 5000);
    }
    
    function showValidationResult(message, type) {
        var result = $('#settings-validation-result');
        result.removeClass('success error').addClass(type).text(message).show();
        
        setTimeout(function() {
            result.fadeOut();
        }, 5000);
    }
    
    // Phone input formatting
    $('#test-phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        $(this).val(value);
    });
    
    // Update fields visibility and descriptions based on provider
    function updateProviderFields() {
        var provider = $('#wcpa_sms_provider').val();
        var apiKeyRow = $('input[name="wcpa_sms_api_key"]').closest('tr');
        var usernameRow = $('input[name="wcpa_sms_username"]').closest('tr');
        var passwordRow = $('input[name="wcpa_sms_password"]').closest('tr');
        var patternRow = $('input[name="wcpa_sms_pattern_code"]').closest('tr');
        var senderRow = $('input[name="wcpa_sms_sender"]').closest('tr');
        var apiKeyDesc = apiKeyRow.find('.description');
        var patternDesc = patternRow.find('.description');
        var usernameDesc = usernameRow.find('.description');
        var passwordDesc = passwordRow.find('.description');
        
        if (provider === 'melipayamak') {
            // ملی پیامک: نمایش username و password، مخفی کردن API key
            apiKeyRow.hide();
            usernameRow.show();
            passwordRow.show();
            senderRow.show();
            patternRow.hide();
            apiKeyDesc.text('برای ملی پیامک از فیلدهای نام کاربری و رمز عبور استفاده کنید');
            usernameDesc.text('نام کاربری از پنل ملی پیامک');
            passwordDesc.text('رمز عبور از پنل ملی پیامک');
            patternDesc.text('برای ملی پیامک با خط خدماتی نیازی به پترن نیست (خالی بگذارید)');
        } else if (provider === 'ipanel') {
            // IPanel: نمایش API key و pattern
            apiKeyRow.show();
            usernameRow.hide();
            passwordRow.hide();
            senderRow.show();
            patternRow.show();
            apiKeyDesc.text('Bearer Token از پنل IPPanel (User Panel > Developers > Access Keys)');
            patternDesc.text('کد پترن تایید شده در پنل پیامک');
        } else {
            // Faraz: نمایش API key و pattern
            apiKeyRow.show();
            usernameRow.hide();
            passwordRow.hide();
            senderRow.show();
            patternRow.show();
            apiKeyDesc.text('کلید API از پنل فراز اس ام اس');
            patternDesc.text('کد پترن تایید شده در پنل پیامک');
        }
    }
    
    // Update on page load
    updateProviderFields();
    
    // Update on provider change
    $('#wcpa_sms_provider').on('change', function() {
        updateProviderFields();
    });
});
