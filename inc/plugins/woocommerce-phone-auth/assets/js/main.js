jQuery(document).ready(function($) {
    'use strict';
    
    console.log('WCPA Main.js loaded');
    console.log('jQuery version:', $.fn.jquery);
    
    // تابع tryStartWebOTP را global می‌کنیم تا در shortcode ها قابل استفاده باشد
    window.tryStartWebOTP = function(inputSelector) {
        try {
            // بررسی پشتیبانی مرورگر از WebOTP API
            if (!('OTPCredential' in window) || !navigator.credentials) {
                console.log('WebOTP API not supported');
                return;
            }
            
            var $input = $(inputSelector);
            if (!$input.length) return;
            
            // فقط در موبایل فعال شود
            var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (!isMobile) {
                console.log('WebOTP: Desktop detected, skipping');
                return;
            }
            
            console.log('WebOTP: Starting SMS code detection...');
            console.log('WebOTP: Input selector:', inputSelector);
            console.log('WebOTP: Current domain:', window.location.hostname);
            
            var controller = new AbortController();
            
            // Abort after expiry to avoid hanging (حداکثر 3 دقیقه)
            var CODE_EXPIRY = (window.wcpa_ajax && parseInt(window.wcpa_ajax.expiry, 10)) || 300;
            var timeout = Math.min(Math.max(CODE_EXPIRY * 1000, 10000), 180000);
            setTimeout(function(){ 
                controller.abort(); 
                console.log('WebOTP: Timeout reached');
            }, timeout);
            
            // دریافت کد از پیامک
            // WebOTP API به دنبال پیامک‌هایی می‌گردد که در خط آخر شامل @domain.com #code باشند
            console.log('WebOTP: Requesting OTP credential...');
            navigator.credentials.get({
                otp: { 
                    transport: ['sms']
                },
                signal: controller.signal
            }).then(function(otp) {
                console.log('WebOTP: OTP credential received:', otp);
                if (otp && otp.code) {
                    console.log('WebOTP: Code received from SMS:', otp.code);
                    console.log('WebOTP: Code type:', typeof otp.code);
                    console.log('WebOTP: Code length:', otp.code.length);
                    
                    // استخراج فقط اعداد و محدود کردن به 4 رقم
                    var code = otp.code.replace(/\D/g,'').slice(0,4);
                    console.log('WebOTP: Extracted code:', code);
                    console.log('WebOTP: Input element:', $input[0]);
                    
                    if (code && code.length > 0) {
                        $input.val(code);
                        console.log('WebOTP: Code set to input, current value:', $input.val());
                        
                        // Trigger input event برای autosubmit
                        $input.trigger('input');
                        $input.trigger('change');
                        
                        // Focus on input
                        $input[0].focus();
                        
                        // Auto submit اگر کد کامل است
                        if (code.length === 4) {
                            console.log('WebOTP: Code is complete, auto-submitting...');
                            setTimeout(function() {
                                var $form = $input.closest('form');
                                if ($form.length) {
                                    console.log('WebOTP: Submitting form...');
                                    if (typeof $form[0].requestSubmit === 'function') {
                                        $form[0].requestSubmit();
                                    } else {
                                        $form.trigger('submit');
                                    }
                                }
                            }, 500);
                        }
                    } else {
                        console.log('WebOTP: No code extracted from OTP');
                    }
                } else {
                    console.log('WebOTP: No code in OTP credential');
                }
            }).catch(function(err) { 
                // خطاها را log می‌کنیم برای debugging
                console.log('WebOTP error:', err.name, err.message);
                console.log('WebOTP error stack:', err.stack);
                if (err.name !== 'AbortError' && err.name !== 'NotAllowedError') {
                    console.log('WebOTP full error:', err);
                }
            });
        } catch(e) { 
            console.log('WebOTP exception:', e);
        }
    };
    
    // Add modal to page only when needed
    function addModalIfNeeded() {
        if ($('#wcpa-login-modal').length === 0) {
            console.log('Creating modal element');
            var modalHtml = '<div id="wcpa-login-modal" style="display: none !important;">' +
                '<div class="wcpa-modal-content">' +
                    '<div class="wcpa-modal-header">' +
                        '<h3>ورود / ثبت نام</h3>' +
                        '<span class="wcpa-close">&times;</span>' +
                    '</div>' +
                    '<div class="wcpa-modal-body">' +
                        '<form id="wcpa-login-form">' +
                            '<div class="wcpa-form-group">' +
                                '<label>شماره تماس</label>' +
                                '<input type="tel" id="wcpa-phone" placeholder="09123456789" maxlength="11" inputmode="numeric" autocomplete="tel" required>' +
                            '</div>' +
                            '<div class="wcpa-form-group" id="wcpa-code-group" style="display: none;">' +
                                '<label>کد تایید</label>' +
                                '<input type="text" id="wcpa-code" placeholder="1234" maxlength="4" inputmode="numeric" autocomplete="one-time-code" required>' +
                                '<div class="wcpa-resend-row" style="margin-top:8px; display:flex; align-items:center; gap:8px;">' +
                                    '<button type="button" id="wcpa-resend" class="wcpa-submit-btn" disabled>ارسال مجدد</button>' +
                                    '<span id="wcpa-timer" style="font-size:12px;color:#666;"></span>' +
                                '</div>' +
                            '</div>' +
                            '<button type="button" id="wcpa-main-btn" class="wcpa-submit-btn">' +
                                '<span class="wcpa-btn-text">ارسال کد تایید</span>' +
                                '<span class="wcpa-loading-spinner" style="display: none;"></span>' +
                            '</button>' +
                        '</form>' +
                        '<div id="wcpa-message" style="display: none;"></div>' +
                    '</div>' +
                '</div>' +
            '</div>';
            // Append to body, not to any other element
            $('body').append(modalHtml);
            console.log('Modal appended to body');
            console.log('Modal element created, length:', $('#wcpa-login-modal').length);
        } else {
            console.log('Modal element already exists');
        }
    }
    
    // Toggle user menu
    window.toggleUserMenu = function() {
        $('#wcpa-userMenu').toggleClass('show');
    };
    
    // Close user menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.wcpa-user-dropdown').length) {
            $('#wcpa-userMenu').removeClass('show');
        }
    });
    
    // Hover effect for user menu
    $('.wcpa-user-dropdown').hover(
        function() {
            $('#wcpa-userMenu').addClass('show');
        },
        function() {
            $('#wcpa-userMenu').removeClass('show');
        }
    );
    
    // Open modal
    window.openLogin = function() {
        console.log('openLogin function called');
        addModalIfNeeded();
        var $modal = $('#wcpa-login-modal');
        console.log('Modal element found:', $modal.length);
        
        if ($modal.length === 0) {
            console.error('Modal element not found!');
            return;
        }
        
        // Prevent body scroll when modal is open
        $('body').css('overflow', 'hidden');
        
        // Force modal to show with proper styling - especially for mobile
        $modal.removeClass('show').addClass('show');
        $modal.removeAttr('style');
        
        // Set inline styles with !important using CSS method
        $modal[0].style.setProperty('display', 'flex', 'important');
        $modal[0].style.setProperty('position', 'fixed', 'important');
        $modal[0].style.setProperty('z-index', '999999', 'important');
        $modal[0].style.setProperty('left', '0', 'important');
        $modal[0].style.setProperty('top', '0', 'important');
        $modal[0].style.setProperty('right', '0', 'important');
        $modal[0].style.setProperty('bottom', '0', 'important');
        $modal[0].style.setProperty('width', '100%', 'important');
        $modal[0].style.setProperty('height', '100%', 'important');
        $modal[0].style.setProperty('align-items', 'center', 'important');
        $modal[0].style.setProperty('justify-content', 'center', 'important');
        $modal[0].style.setProperty('margin', '0', 'important');
        $modal[0].style.setProperty('padding', '0', 'important');
        
        console.log('Modal display set to flex');
        console.log('Modal computed display:', window.getComputedStyle($modal[0]).display);
        console.log('Modal computed position:', window.getComputedStyle($modal[0]).position);
        console.log('Modal computed z-index:', window.getComputedStyle($modal[0]).zIndex);
        
        setTimeout(function() {
            var $phoneInput = $('#wcpa-phone');
            if ($phoneInput.length) {
                $phoneInput.focus();
            }
        }, 100);
        
        // Hide close button on checkout page
        if (window.location.href.indexOf('checkout') !== -1) {
            $('.wcpa-close').hide();
        } else {
            $('.wcpa-close').show();
        }
        
        // Trigger custom event
        $(document).trigger('wcpa:modal:opened');
    };
    
    console.log('window.openLogin function defined:', typeof window.openLogin);
    
    // Close modal function
    function closeModal() {
        var $modal = $('#wcpa-login-modal');
        if ($modal.length === 0) return;
        
        // Don't allow closing on checkout page
        if (window.location.href.indexOf('checkout') !== -1) {
            return false;
        }
        
        // Remove show class
        $modal.removeClass('show');
        
        // Hide modal using inline style
        $modal[0].style.setProperty('display', 'none', 'important');
        
        // Restore body scroll
        $('body').css('overflow', '');
        
        // Reset form
        resetForm();
        
        // Trigger custom event
        $(document).trigger('wcpa:modal:closed');
    }
    
    // Close modal - handle close button click
    $(document).on('click', '.wcpa-close', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeModal();
        return false;
    });
    
    // Close modal when clicking on backdrop (outside modal content)
    $(document).on('click', '#wcpa-login-modal', function(e) {
        // Don't allow closing on checkout page
        if (window.location.href.indexOf('checkout') !== -1) {
            return false;
        }
        
        // Only close if clicking directly on the modal backdrop, not on modal content
        if (e.target === this || $(e.target).is('#wcpa-login-modal')) {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
            return false;
        }
    });
    
    // Prevent modal content clicks from closing modal
    $(document).on('click', '#wcpa-login-modal .wcpa-modal-content', function(e) {
        e.stopPropagation();
    });
    
    // Close modal with Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            var $modal = $('#wcpa-login-modal');
            if ($modal.length && $modal.hasClass('show')) {
                // Don't allow closing on checkout page
                if (window.location.href.indexOf('checkout') === -1) {
                    closeModal();
                }
            }
        }
    });
    
    var RESEND_COOLDOWN = (window.wcpa_ajax && parseInt(wcpa_ajax.cooldown, 10)) || 60;
    var CODE_EXPIRY = (window.wcpa_ajax && parseInt(wcpa_ajax.expiry, 10)) || 300;

    var wcpaCountdownInterval = null;
    function startCountdown($timerEl, $resendBtn, seconds) {
        clearInterval(wcpaCountdownInterval);
        var remaining = seconds;
        $resendBtn.prop('disabled', true);
        $resendBtn.text('ارسال مجدد');
        $timerEl.text('ارسال مجدد تا ' + remaining + ' ثانیه');
        wcpaCountdownInterval = setInterval(function() {
            remaining -= 1;
            if (remaining <= 0) {
                clearInterval(wcpaCountdownInterval);
                $timerEl.text('');
                $resendBtn.prop('disabled', false).text('ارسال مجدد');
            } else {
                $timerEl.text('ارسال مجدد تا ' + remaining + ' ثانیه');
            }
        }, 1000);
    }

    // Auto-submit helpers
    function autoSubmitIfComplete(codeSelector, formSelector) {
        var $input = $(codeSelector);
        var v = $input.val() || '';
        var digits = (v + '').replace(/\D/g,'').slice(0,5);
        if (digits !== v) $input.val(digits);
        if (digits.length === 5) {
            var $form = $(formSelector);
            if ($form.length) {
                if (typeof $form[0].requestSubmit === 'function') {
                    $form[0].requestSubmit();
                } else {
                    $form.trigger('submit');
                }
                return true;
            }
        }
        return false;
    }

    // Auto-submit when 4 digits are entered (supports OS autofill on iOS/Android)
    function bindAutoSubmitOnInput(codeSelector, formSelector) {
        $(document).on('input keyup change paste', codeSelector, function() {
            autoSubmitIfComplete(codeSelector, formSelector);
        });
    }
    if (wcpa_ajax && wcpa_ajax.flags && wcpa_ajax.flags.autosubmit === true) {
        bindAutoSubmitOnInput('#wcpa-code', '#wcpa-login-form');
        bindAutoSubmitOnInput('#page-code', '#wcpa-page-login-form');
    }

    // Short watcher to catch autofill that may not trigger events
    function startCodeAutofillWatcher(codeSelector, formSelector) {
        var elapsed = 0;
        var stepMs = 300;
        var maxMs = 30000; // 30s
        var timer = setInterval(function() {
            elapsed += stepMs;
            if (autoSubmitIfComplete(codeSelector, formSelector) || elapsed >= maxMs) {
                clearInterval(timer);
            }
        }, stepMs);
    }

    // Main button handler - handles both send code and verify
    $(document).on('click', '#wcpa-main-btn', function(e) {
        e.stopPropagation();
        var $btn = $(this);
        var $btnText = $btn.find('.wcpa-btn-text');
        var $spinner = $btn.find('.wcpa-loading-spinner');
        var phone = $('#wcpa-phone').val();
        var code = $('#wcpa-code').val();
        var codeGroup = $('#wcpa-code-group');
        
        // If code group is hidden, send code
        if (!codeGroup.is(':visible')) {
            if (!phone) {
                showMessage('شماره تماس الزامی است', 'error');
                return;
            }
            
            if (!/^09[0-9]{9}$/.test(phone)) {
                showMessage('فرمت شماره تماس صحیح نیست', 'error');
                return;
            }
            
            // Show loading state
            $btn.prop('disabled', true);
            $btnText.text('در حال ارسال...');
            $spinner.show();
            showMessage('در حال ارسال کد تایید...', 'info');
            
            // AJAX call to send code
            $.ajax({
                url: wcpa_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wcpa_send_verification_code',
                    phone: phone,
                    nonce: wcpa_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('✅ کد تایید با موفقیت ارسال شد', 'success');
                        codeGroup.show();
                        $btnText.text('تایید و ورود');
                        $spinner.hide();
                        $('#wcpa-phone').prop('disabled', true);
                        $('#wcpa-code').focus();
                        // Start cooldown timer for resend
                        startCountdown($('#wcpa-timer'), $('#wcpa-resend'), RESEND_COOLDOWN);
                        // Web OTP - خواندن خودکار کد از پیامک در موبایل
                        tryStartWebOTP('#wcpa-code');
                        // Watcher برای autofill
                        if (wcpa_ajax && wcpa_ajax.flags && wcpa_ajax.flags.autosubmit === true) {
                            startCodeAutofillWatcher('#wcpa-code', '#wcpa-login-form');
                        }
                    } else {
                        var errorMsg = '❌ خطایی رخ داد';
                        if (response.data) {
                            if (typeof response.data === 'string') {
                                errorMsg = '❌ ' + response.data;
                            } else if (response.data.message) {
                                errorMsg = '❌ ' + response.data.message;
                            }
                        }
                        showMessage(errorMsg, 'error');
                        $btnText.text('ارسال کد تایید');
                        $spinner.hide();
                        // If backend says cooldown, start timer immediately with remaining
                        if (response.data && response.data.code === 'cooldown' && typeof response.data.remaining === 'number') {
                            codeGroup.show();
                            $btnText.text('تایید و ورود');
                            startCountdown($('#wcpa-timer'), $('#wcpa-resend'), Math.max(1, response.data.remaining));
                            // Web OTP - خواندن خودکار کد از پیامک در موبایل
                            window.tryStartWebOTP('#wcpa-code');
                            if (wcpa_ajax && wcpa_ajax.flags && wcpa_ajax.flags.autosubmit === true) {
                                startCodeAutofillWatcher('#wcpa-code', '#wcpa-login-form');
                            }
                        }
                    }
                },
                error: function() {
                    showMessage('❌ خطایی رخ داد. لطفاً دوباره تلاش کنید', 'error');
                    $btnText.text('ارسال کد تایید');
                    $spinner.hide();
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        } else {
            // Code group is visible, verify code
            if (!code) {
                showMessage('لطفا کد تایید را وارد کنید', 'error');
                return;
            }
            
            if (code.length !== 4) {
                showMessage('کد تایید باید 4 رقم باشد', 'error');
                return;
            }
            
            // Show loading state
            $btn.prop('disabled', true);
            $btnText.text('در حال تایید...');
            $spinner.show();
            showMessage('در حال تایید کد...', 'info');
            
            // AJAX call to verify and login
            $.ajax({
                url: wcpa_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wcpa_login_register',
                    phone: phone,
                    code: code,
                    action_type: 'auto',
                    nonce: wcpa_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('✅ ورود موفقیت‌آمیز! در حال انتقال...', 'success');
                        setTimeout(function() {
                            $('#wcpa-login-modal').hide();
                            location.reload();
                        }, 1500);
                    } else {
                        var errorMsg = '❌ خطایی رخ داد';
                        if (response.data) {
                            if (typeof response.data === 'string') {
                                errorMsg = '❌ ' + response.data;
                            } else if (response.data.message) {
                                errorMsg = '❌ ' + response.data.message;
                            }
                        }
                        showMessage(errorMsg, 'error');
                        $btnText.text('تایید و ورود');
                        $spinner.hide();
                    }
                },
                error: function() {
                    showMessage('❌ خطایی رخ داد. لطفاً دوباره تلاش کنید', 'error');
                    $btnText.text('تایید و ورود');
                    $spinner.hide();
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        }
    });

    // Resend in modal
    $(document).on('click', '#wcpa-resend', function(e) {
        e.stopPropagation();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;
        $btn.prop('disabled', true).text('در حال ارسال...');
        $('#wcpa-main-btn').trigger('click');
        // Text will be reset by main button success/error handlers via timer or re-enable
    });
    
    // Submit form (for auto-submit when code is entered)
    $(document).on('submit', '#wcpa-login-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $form = $('#wcpa-login-form');
        if ($form.data('submitting') === true) return; // prevent duplicate
        $form.data('submitting', true);
        
        // Trigger main button click to handle verification
        $('#wcpa-main-btn').trigger('click');
        $form.data('submitting', false);
    });
    
    function resetForm() {
        $('#wcpa-phone').val('').prop('disabled', false);
        $('#wcpa-code').val('');
        $('#wcpa-code-group').hide();
        var $mainBtn = $('#wcpa-main-btn');
        $mainBtn.prop('disabled', false);
        $mainBtn.find('.wcpa-btn-text').text('ارسال کد تایید');
        $mainBtn.find('.wcpa-loading-spinner').hide();
        $('#wcpa-message').hide();
        clearInterval(wcpaCountdownInterval);
        $('#wcpa-timer').text('');
        $('#wcpa-resend').prop('disabled', true);
    }
    
    function showMessage(text, type) {
        var message = $('#wcpa-message');
        message.text(text).removeClass('wcpa-success wcpa-error wcpa-info').addClass('wcpa-' + type).show();
        
        setTimeout(function() {
            message.fadeOut();
        }, 5000);
    }
    
    // Page login form handlers
    $('#page-send-code').on('click', function() {
        var phone = $('#page-phone').val();
        
        if (!phone) {
            showPageMessage('شماره تماس الزامی است', 'error');
            return;
        }
        
        if (!/^09[0-9]{9}$/.test(phone)) {
            showPageMessage('فرمت شماره تماس صحیح نیست', 'error');
            return;
        }
        
        // AJAX call
        $.ajax({
            url: wcpa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_send_verification_code',
                phone: phone,
                nonce: wcpa_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showPageMessage('کد تایید ارسال شد', 'success');
                    $('#page-code-group').show();
                    $('#page-send-code').hide();
                    $('#page-submit-form').show();
                    $('#page-code').focus();
                    startCountdown($('#page-timer'), $('#page-resend'), RESEND_COOLDOWN);
                    // Web OTP - خواندن خودکار کد از پیامک در موبایل
                    window.tryStartWebOTP('#page-code');
                    // Watcher برای autofill
                    if (wcpa_ajax && wcpa_ajax.flags && wcpa_ajax.flags.autosubmit === true) {
                        startCodeAutofillWatcher('#page-code', '#wcpa-page-login-form');
                    }
                } else {
                    var errorMsg = 'خطایی رخ داد';
                    if (response.data) {
                        if (typeof response.data === 'string') {
                            errorMsg = response.data;
                        } else if (response.data.message) {
                            errorMsg = response.data.message;
                        }
                    }
                    showPageMessage(errorMsg, 'error');
                }
            },
            error: function() {
                showPageMessage('خطایی رخ داد', 'error');
            }
        });
    });

    // Resend on page
    $(document).on('click', '#page-resend', function() {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;
        $btn.prop('disabled', true).text('در حال ارسال...');
        $('#page-send-code').trigger('click');
    });
    
    // Submit page form
    $('#wcpa-page-login-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $('#wcpa-page-login-form');
        if ($form.data('submitting') === true) return;
        $form.data('submitting', true);
        
        var phone = $('#page-phone').val();
        var code = $('#page-code').val();
        
        if (!phone || !code) {
            showPageMessage('تمام فیلدها الزامی است', 'error');
            $form.data('submitting', false);
            return;
        }
        
        // AJAX call
        $.ajax({
            url: wcpa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_login_register',
                phone: phone,
                code: code,
                action_type: 'auto',
                nonce: wcpa_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showPageMessage('ورود موفقیت‌آمیز - در حال انتقال به حساب کاربری...', 'success');
                    setTimeout(function() {
                        // Get the correct myaccount URL
                        var myaccountUrl = wcpa_ajax.login_url || '/my-account/';
                        // Make sure it's not the phone-login page
                        if (myaccountUrl.indexOf('phone-login') === -1) {
                            window.location.href = myaccountUrl;
                        } else {
                            window.location.href = '/my-account/';
                        }
                    }, 2000);
                } else {
                    var errorMsg = 'خطایی رخ داد';
                    if (response.data) {
                        if (typeof response.data === 'string') {
                            errorMsg = response.data;
                        } else if (response.data.message) {
                            errorMsg = response.data.message;
                        }
                    }
                    showPageMessage(errorMsg, 'error');
                    $form.data('submitting', false);
                }
            },
            error: function() {
                showPageMessage('خطایی رخ داد', 'error');
                $form.data('submitting', false);
            }
        });
    });
    
    function showPageMessage(text, type) {
        var message = $('#page-message');
        message.text(text).removeClass('success error').addClass(type).show();
        
        if (type === 'success') {
            message.css({
                'background-color': '#d4edda',
                'color': '#155724',
                'border': '1px solid #c3e6cb'
            });
        } else {
            message.css({
                'background-color': '#f8d7da',
                'color': '#721c24',
                'border': '1px solid #f5c6cb'
            });
        }
        
        setTimeout(function() {
            message.fadeOut();
        }, 5000);
    }
});