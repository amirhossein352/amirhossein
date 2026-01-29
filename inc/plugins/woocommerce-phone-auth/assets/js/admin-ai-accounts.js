jQuery(document).ready(function($) {
    'use strict';
    
    // Show/hide AI account management based on checkbox
    $('#_is_ai_account_product').change(function() {
        if ($(this).is(':checked')) {
            $('#ai-accounts-management').show();
            loadAIAccounts();
        } else {
            $('#ai-accounts-management').hide();
        }
    });
    
    // Initial check
    if ($('#_is_ai_account_product').is(':checked')) {
        $('#ai-accounts-management').show();
        loadAIAccounts();
    } else {
        $('#ai-accounts-management').hide();
    }
    
    // Add AI account
    $('#add-ai-account').click(function() {
        var username = prompt('نام کاربری:');
        if (!username) return;
        
        var password = prompt('رمز عبور:');
        if (!password) return;
        
        var maxUsers = prompt('حداکثر کاربران (پیش‌فرض: 1):');
        maxUsers = maxUsers ? parseInt(maxUsers) : 1;
        
        var productId = $('#post_ID').val();
        
        $.ajax({
            url: wcpa_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_add_ai_account',
                nonce: wcpa_admin_ajax.nonce,
                product_id: productId,
                username: username,
                password: password,
                max_users: maxUsers
            },
            success: function(response) {
                if (response.success) {
                    alert('اکانت با موفقیت اضافه شد!');
                    loadAIAccounts();
                } else {
                    alert('خطا: ' + response.data.message);
                }
            },
            error: function() {
                alert('خطایی در افزودن اکانت رخ داد.');
            }
        });
    });
    
    // Load AI accounts
    function loadAIAccounts() {
        var productId = $('#post_ID').val();
        
        $.ajax({
            url: wcpa_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_get_ai_accounts',
                nonce: wcpa_admin_ajax.nonce,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    displayAIAccounts(response.data.accounts);
                } else {
                    $('#ai-accounts-list').html('<p>خطا در بارگذاری اکانت‌ها: ' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('#ai-accounts-list').html('<p>خطا در بارگذاری اکانت‌ها.</p>');
            }
        });
    }
    
    // Display AI accounts
    function displayAIAccounts(accounts) {
        var html = '';
        
        if (accounts.length === 0) {
            html = '<p>هیچ اکانتی یافت نشد. روی "افزودن اکانت" کلیک کنید.</p>';
        } else {
            html = '<table class="widefat">';
            html += '<thead><tr><th>نام کاربری</th><th>رمز عبور</th><th>حداکثر کاربران</th><th>کاربران فعلی</th><th>عملیات</th></tr></thead>';
            html += '<tbody>';
            
            accounts.forEach(function(account) {
                html += '<tr>';
                html += '<td>' + account.username + '</td>';
                html += '<td>' + account.password + '</td>';
                html += '<td>' + account.max_users + '</td>';
                html += '<td>' + account.current_users + '</td>';
                html += '<td><button class="button delete-ai-account" data-account-id="' + account.id + '">حذف</button></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
        }
        
        $('#ai-accounts-list').html(html);
    }
    
    // Delete AI account
    $(document).on('click', '.delete-ai-account', function() {
        if (!confirm('آیا مطمئن هستید که می‌خواهید این اکانت را حذف کنید؟')) {
            return;
        }
        
        var accountId = $(this).data('account-id');
        
        $.ajax({
            url: wcpa_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wcpa_delete_ai_account',
                nonce: wcpa_admin_ajax.nonce,
                account_id: accountId
            },
            success: function(response) {
                if (response.success) {
                    alert('اکانت با موفقیت حذف شد!');
                    loadAIAccounts();
                } else {
                    alert('خطا: ' + response.data.message);
                }
            },
            error: function() {
                alert('خطایی در حذف اکانت رخ داد.');
            }
        });
    });
});
