/**
 * Product Actions - فقط JavaScript برای دکمه‌ها
 */
(function($) {
    'use strict';

    // تابع نمایش نوتیفیکیشن (مشابه landing-page-manager)
    function showNotification(message, type) {
        // حذف نوتیفیکیشن قبلی اگر وجود دارد
        const existingNotification = document.querySelector('.ki-product-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // ایجاد نوتیفیکیشن جدید
        const notification = document.createElement('div');
        notification.className = 'ki-product-notification ' + type;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'info' ? 'ℹ️' : '⚠️'}</span>
                <span class="notification-text">${message}</span>
            </div>
        `;
        
        // اضافه کردن استایل
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : type === 'info' ? '#2196F3' : '#ff9800'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 99999;
            font-family: 'IranYekan', 'Tahoma', sans-serif;
            font-size: 14px;
            font-weight: 600;
            animation: kiSlideInRight 0.3s ease;
            max-width: 350px;
            direction: rtl;
        `;
        
        document.body.appendChild(notification);
        
        // حذف خودکار بعد از 3 ثانیه
        setTimeout(() => {
            notification.style.animation = 'kiSlideOutRight 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // اضافه کردن CSS انیمیشن
    if (!document.getElementById('ki-notification-styles')) {
        const style = document.createElement('style');
        style.id = 'ki-notification-styles';
        style.textContent = `
            @keyframes kiSlideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes kiSlideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .ki-product-notification .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .ki-product-notification .notification-icon {
                font-size: 18px;
                flex-shrink: 0;
            }
            
            .ki-product-notification .notification-text {
                flex: 1;
                color: #ffffff !important;
            }
            
            .ki-product-notification {
                color: #ffffff !important;
            }
            
            .ki-product-notification * {
                color: #ffffff !important;
            }
        `;
        document.head.appendChild(style);
    }

    // حذف پیام‌های WooCommerce بعد از add to cart
    function removeWooCommerceMessages() {
        // حذف همه پیام‌های مربوط به سبد خرید
        $('.woocommerce-message, .woocommerce-info').not('.ki-product-notification').each(function() {
            var $msg = $(this);
            var text = $msg.text() || '';
            var html = $msg.html() || '';
            
            // حذف همه پیام‌های مربوط به سبد خرید
            if (text.indexOf('مشاهده سبد خرید') !== -1 || 
                text.indexOf('View cart') !== -1 ||
                text.indexOf('سبد خرید') !== -1 ||
                text.indexOf('اضافه') !== -1 ||
                html.indexOf('cart') !== -1 ||
                html.indexOf('سبد') !== -1 ||
                $msg.find('a[href*="cart"]').length > 0 ||
                $msg.find('a[href*="سبد"]').length > 0 ||
                $msg.find('a.wc-forward').length > 0) {
                $msg.remove();
            }
        });
        
        // حذف از همه جاها - حذف کامل همه پیام‌های cart
        $('.woocommerce-loop .woocommerce-message, .products .woocommerce-message, .product .woocommerce-message, .ki-product-card .woocommerce-message').each(function() {
            var $msg = $(this);
            var text = $msg.text() || '';
            if (text.indexOf('سبد') !== -1 || text.indexOf('cart') !== -1 || text.indexOf('مشاهده') !== -1) {
                $msg.remove();
            }
        });
        
        // حذف از کنار دکمه‌های add to cart
        $('.add_to_cart_button').siblings('.woocommerce-message, .woocommerce-info').remove();
        $('.ki-cart-btn').siblings('.woocommerce-message, .woocommerce-info').remove();
        $('.ki-product-actions').siblings('.woocommerce-message, .woocommerce-info').remove();
        $('.ki-product-actions').next('.woocommerce-message, .woocommerce-info').remove();
        $('.ki-product-actions').closest('.ki-product-info, .ki-product-card').find('.woocommerce-message, .woocommerce-info').not('.ki-product-notification').remove();
        $('.ki-cart-btn').closest('.product, .ki-product-card').find('.woocommerce-message, .woocommerce-info').not('.ki-product-notification').remove();
        
        // حذف از بعد از container دکمه‌ها
        $('.ki-product-actions').after().find('.woocommerce-message, .woocommerce-info').remove();
        
        // حذف همه المنت‌هایی که شامل لینک cart هستند
        $('a[href*="cart"], a[href*="سبد"]').closest('.woocommerce-message, .woocommerce-info').remove();
        
        // حذف از همه المنت‌های بعد از actions container
        $('.ki-product-actions').parent().find('.woocommerce-message, .woocommerce-info').not('.ki-product-notification').remove();
        $('.ki-product-actions').closest('li.product, .ki-product-card').find('.woocommerce-message, .woocommerce-info').not('.ki-product-notification').remove();
    }

    // چک کردن اینکه آیا محصول در سبد خرید هست
    function isProductInCart(productId) {
        if (typeof wc_add_to_cart_params === 'undefined') {
            return false;
        }
        
        // چک کردن از طریق fragments
        var cartItems = [];
        if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.cart_url) {
            // اینجا می‌توانیم از AJAX استفاده کنیم
        }
        
        // چک کردن از طریق ki_product_actions
        if (typeof ki_product_actions !== 'undefined' && ki_product_actions.cart_items) {
            cartItems = ki_product_actions.cart_items;
        }
        
        return cartItems.indexOf(parseInt(productId)) !== -1;
    }

    // به‌روزرسانی رنگ دکمه سبد خرید بر اساس اینکه در cart هست یا نه
    function updateCartButtonsColor() {
        if (typeof ki_product_actions === 'undefined' || !ki_product_actions.cart_items) {
            return;
        }
        
        var cartItems = ki_product_actions.cart_items.map(function(id) {
            return parseInt(id);
        });
        
        $('.ki-cart-btn.add_to_cart_button').each(function() {
            var $btn = $(this);
            var productId = parseInt($btn.data('product_id') || $btn.attr('data-product_id'));
            
            if (!productId) return;
            
            if (cartItems.indexOf(productId) !== -1) {
                // محصول در cart هست
                $btn.css({
                    'background': '#27ae60',
                    'border-color': '#27ae60',
                    'color': '#ffffff'
                });
                $btn.addClass('in-cart');
            } else {
                // محصول در cart نیست
                $btn.css({
                    'background': '',
                    'border-color': '',
                    'color': ''
                });
                $btn.removeClass('in-cart');
            }
        });
    }

    // به‌روزرسانی رنگ دکمه‌های wishlist و compare
    function updateWishlistCompareButtons() {
        if (typeof ki_product_actions === 'undefined') {
            return;
        }
        
        // Wishlist
        var wishlistItems = [];
        if (ki_product_actions.wishlist_items && Array.isArray(ki_product_actions.wishlist_items)) {
            wishlistItems = ki_product_actions.wishlist_items.map(function(id) {
                return parseInt(id);
            });
        }
        
        $('.ki-wishlist-btn').each(function() {
            var $btn = $(this);
            var productId = parseInt($btn.data('product-id'));
            
            if (wishlistItems.indexOf(productId) !== -1) {
                $btn.css({
                    'background': '#ef4444',
                    'border-color': '#ef4444',
                    'color': '#ffffff'
                });
                $btn.find('i').removeClass('far fa-heart').addClass('fas fa-heart');
            } else {
                $btn.css({
                    'background': '',
                    'border-color': '',
                    'color': ''
                });
                $btn.find('i').removeClass('fas fa-heart').addClass('far fa-heart');
            }
        });
        
        // Compare
        var compareItems = [];
        if (ki_product_actions.compare_items && Array.isArray(ki_product_actions.compare_items)) {
            compareItems = ki_product_actions.compare_items.map(function(id) {
                return parseInt(id);
            });
        }
        
        $('.ki-compare-btn').each(function() {
            var $btn = $(this);
            var productId = parseInt($btn.data('product-id'));
            
            if (compareItems.indexOf(productId) !== -1) {
                $btn.css({
                    'background': '#3b82f6',
                    'border-color': '#3b82f6',
                    'color': '#ffffff'
                });
            } else {
                $btn.css({
                    'background': '',
                    'border-color': '',
                    'color': ''
                });
            }
        });
    }

    // به‌روزرسانی cart_items از fragments
    function updateCartItemsFromFragments(fragments) {
        if (!fragments) return;
        
        var cartItems = [];
        
        // استخراج product_id از fragments
        $.each(fragments, function(key, value) {
            if (typeof value === 'string') {
                var $fragment = $('<div>').html(value);
                
                // پیدا کردن همه product_id ها
                $fragment.find('[data-product_id], [data-product-id]').each(function() {
                    var pid = $(this).attr('data-product_id') || $(this).attr('data-product-id');
                    if (pid) {
                        var pidNum = parseInt(pid);
                        if (pidNum && cartItems.indexOf(pidNum) === -1) {
                            cartItems.push(pidNum);
                        }
                    }
                });
                
                // همچنین از cart_item_key استخراج کنیم
                $fragment.find('.cart_item, .mini_cart_item').each(function() {
                    var $item = $(this);
                    var pid = $item.attr('data-product_id') || 
                             $item.find('[data-product_id]').attr('data-product_id') ||
                             $item.find('[data-product-id]').attr('data-product-id');
                    
                    if (pid) {
                        var pidNum = parseInt(pid);
                        if (pidNum && cartItems.indexOf(pidNum) === -1) {
                            cartItems.push(pidNum);
                        }
                    }
                });
            }
        });
        
        // به‌روزرسانی ki_product_actions
        if (typeof ki_product_actions !== 'undefined') {
            ki_product_actions.cart_items = cartItems;
        }
    }

    // وقتی DOM آماده شد
    $(document).ready(function() {
        
        // تغییر href همه دکمه‌های add to cart برای جلوگیری از refresh
        $('.ki-cart-btn.add_to_cart_button').each(function() {
            var $btn = $(this);
            var currentHref = $btn.attr('href');
            // اگر href به add-to-cart اشاره می‌کند، آن را به javascript:void(0) تغییر بده
            if (currentHref && (currentHref.indexOf('add-to-cart') !== -1 || currentHref.indexOf('?') !== -1)) {
                $btn.attr('href', 'javascript:void(0);');
            }
        });
        
        // حذف handler های WooCommerce
        $(document.body).off('click', '.ki-cart-btn.add_to_cart_button');
        
        // Add to Cart - استفاده از capture phase برای intercept قبل از WooCommerce
        document.addEventListener('click', function(e) {
            var target = e.target.closest('.ki-cart-btn.add_to_cart_button');
            if (!target) return;
            
            // جلوگیری از ریدایرکت و اجرای سایر handlers
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $btn = $(target);
            var productId = $btn.data('product_id') || $btn.attr('data-product_id');
            
            if (!productId) {
                console.warn('Product ID not found');
                return;
            }
            
            // اگر loading است، جلوگیری
            if ($btn.hasClass('loading') || $btn.prop('disabled')) {
                return;
            }
            
            $btn.prop('disabled', true).addClass('loading');
            var originalHtml = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin"></i>');
            
            // استفاده از endpoint سفارشی (مشابه custom-dollar-order)
            var ajaxUrl = (typeof ki_product_actions !== 'undefined' && ki_product_actions.ajax_url)
                ? ki_product_actions.ajax_url
                : (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                    ? wc_add_to_cart_params.ajax_url
                    : (window.location.origin + '/wp-admin/admin-ajax.php');
            
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ki_add_to_cart',
                    product_id: productId,
                    quantity: 1,
                    nonce: (typeof ki_product_actions !== 'undefined' && ki_product_actions.nonce) 
                        ? ki_product_actions.nonce 
                        : ''
                },
                success: function(response) {
                    console.log('Add to cart response:', response);
                    
                    // بررسی response
                    var data = (response.success && response.data) ? response.data : response;
                    
                    // بررسی خطا
                    if (data.error === true && data.product_url) {
                        showNotification('لطفاً به صفحه محصول بروید تا خرید را تکمیل کنید', 'info');
                        setTimeout(function() {
                            window.location.href = data.product_url;
                        }, 1500);
                        $btn.prop('disabled', false).removeClass('loading');
                        $btn.html(originalHtml);
                        return;
                    }
                    
                    // بررسی موفقیت
                    var isSuccess = false;
                    
                    if (response.success === true || data.success === true) {
                        isSuccess = true;
                    } else if (data.fragments && Object.keys(data.fragments).length > 0) {
                        isSuccess = true;
                    } else if (data.cart_hash) {
                        isSuccess = true;
                    } else if (data.error === false || response.error === false) {
                        isSuccess = true;
                    } else if (!data.error && !data.error_code && !response.error) {
                        isSuccess = true;
                    }
                    
                    if (isSuccess) {
                        // Update cart fragments
                        var fragments = data.fragments || response.fragments;
                        if (fragments) {
                            $.each(fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                            updateCartItemsFromFragments(fragments);
                        }
                        
                        // اضافه کردن product_id به cart_items
                        if (typeof ki_product_actions !== 'undefined') {
                            if (!ki_product_actions.cart_items) {
                                ki_product_actions.cart_items = [];
                            }
                            
                            var productIdNum = parseInt(productId);
                            var cartItemsNumeric = ki_product_actions.cart_items.map(function(id) {
                                return parseInt(id);
                            });
                            
                            if (cartItemsNumeric.indexOf(productIdNum) === -1) {
                                ki_product_actions.cart_items.push(productIdNum);
                            }
                        }
                        
                        // حذف فوری پیام‌های WooCommerce
                        removeWooCommerceMessages();
                        
                        // Trigger WooCommerce events
                        var cartHash = data.cart_hash || response.cart_hash;
                        $(document.body).trigger('added_to_cart', [fragments || {}, cartHash || '', $btn]);
                        
                        // Trigger events برای به‌روزرسانی cart sidebar
                        if (typeof window.dispatchEvent === 'function') {
                            window.dispatchEvent(new CustomEvent('zs-cart-updated'));
                            window.dispatchEvent(new CustomEvent('wc_cart_updated'));
                            window.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
                        }
                        
                        // به‌روزرسانی مودال و آیکون سبد خرید در هدر
                        if (typeof window.refreshCart === 'function') {
                            setTimeout(function() {
                                window.refreshCart();
                            }, 100);
                        }
                        
                        // به‌روزرسانی رنگ همه آیکون‌های سبد خرید
                        setTimeout(function() {
                            updateCartButtonsColor();
                            removeWooCommerceMessages();
                        }, 200);
                        
                        // نمایش پیام موفقیت
                        showNotification('محصول به سبد خرید اضافه شد', 'success');
                    } else {
                        // خطا
                        var errorMsg = 'خطا در افزودن محصول به سبد خرید';
                        if (data.data && typeof data.data === 'string') {
                            errorMsg = data.data;
                        } else if (data.message) {
                            errorMsg = data.message;
                        } else if (response.data && typeof response.data === 'string') {
                            errorMsg = response.data;
                        } else if (response.message) {
                            errorMsg = response.message;
                        }
                        showNotification(errorMsg, 'error');
                        $btn.removeClass('in-cart');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Add to cart error:', xhr, status, error);
                    showNotification('خطا در افزودن به سبد خرید', 'error');
                    $btn.prop('disabled', false).removeClass('loading');
                    $btn.html(originalHtml);
                },
                complete: function() {
                    $btn.prop('disabled', false).removeClass('loading');
                    $btn.html(originalHtml);
                }
            });
        }, true); // استفاده از capture phase (true)

        // Wishlist
        $(document).on('click', '.ki-wishlist-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var productId = $btn.data('product-id');
            
            if (!productId) return;
            
            // چک کردن login
            if (typeof ki_product_actions !== 'undefined' && !ki_product_actions.is_logged_in) {
                showNotification('لطفاً ابتدا وارد حساب کاربری خود شوید', 'warning');
                setTimeout(function() {
                    window.location.href = ki_product_actions.login_url;
                }, 1500);
                return;
            }
            
            $.ajax({
                url: ki_product_actions.ajax_url,
                type: 'POST',
                data: {
                    action: 'ki_wishlist',
                    product_id: productId,
                    nonce: ki_product_actions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // به‌روزرسانی آرایه wishlist
                        if (typeof ki_product_actions !== 'undefined') {
                            if (!ki_product_actions.wishlist_items) {
                                ki_product_actions.wishlist_items = [];
                            }
                            
                            var productIdNum = parseInt(productId);
                            var wishlistItemsNumeric = ki_product_actions.wishlist_items.map(function(id) {
                                return parseInt(id);
                            });
                            
                            if (response.data.action === 'added') {
                                if (wishlistItemsNumeric.indexOf(productIdNum) === -1) {
                                    ki_product_actions.wishlist_items.push(productIdNum);
                                }
                                $btn.css({
                                    'background': '#ef4444',
                                    'border-color': '#ef4444',
                                    'color': '#ffffff'
                                });
                                $btn.find('i').removeClass('far fa-heart').addClass('fas fa-heart');
                            } else {
                                ki_product_actions.wishlist_items = ki_product_actions.wishlist_items.filter(function(id) {
                                    return parseInt(id) !== productIdNum;
                                });
                                $btn.css({
                                    'background': '',
                                    'border-color': '',
                                    'color': ''
                                });
                                $btn.find('i').removeClass('fas fa-heart').addClass('far fa-heart');
                            }
                        }
                        
                        showNotification(response.data.message, 'success');
                    } else {
                        showNotification(response.data.message || 'خطا در انجام عملیات', 'error');
                    }
                },
                error: function() {
                    showNotification(response.data.message || 'خطا در انجام عملیات', 'error');
                }
            });
        });

        // Compare
        $(document).on('click', '.ki-compare-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var productId = $btn.data('product-id');
            
            if (!productId) return;
            
            // چک کردن login
            if (typeof ki_product_actions !== 'undefined' && !ki_product_actions.is_logged_in) {
                showNotification('لطفاً ابتدا وارد حساب کاربری خود شوید', 'warning');
                setTimeout(function() {
                    window.location.href = ki_product_actions.login_url;
                }, 1500);
                return;
            }
            
            $.ajax({
                url: ki_product_actions.ajax_url,
                type: 'POST',
                data: {
                    action: 'ki_compare',
                    product_id: productId,
                    nonce: ki_product_actions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // به‌روزرسانی آرایه compare
                        if (typeof ki_product_actions !== 'undefined') {
                            if (!ki_product_actions.compare_items) {
                                ki_product_actions.compare_items = [];
                            }
                            
                            var productIdNum = parseInt(productId);
                            var compareItemsNumeric = ki_product_actions.compare_items.map(function(id) {
                                return parseInt(id);
                            });
                            
                            if (response.data.action === 'added') {
                                if (compareItemsNumeric.indexOf(productIdNum) === -1) {
                                    ki_product_actions.compare_items.push(productIdNum);
                                }
                                $btn.css({
                                    'background': '#3b82f6',
                                    'border-color': '#3b82f6',
                                    'color': '#ffffff'
                                });
                            } else {
                                ki_product_actions.compare_items = ki_product_actions.compare_items.filter(function(id) {
                                    return parseInt(id) !== productIdNum;
                                });
                                $btn.css({
                                    'background': '',
                                    'border-color': '',
                                    'color': ''
                                });
                            }
                        }
                        
                        showNotification(response.data.message, 'success');
                    } else {
                        showNotification(response.data.message || 'خطا در انجام عملیات', 'error');
                    }
                },
                error: function() {
                    showNotification(response.data.message || 'خطا در انجام عملیات', 'error');
                }
            });
        });

        // به‌روزرسانی رنگ دکمه‌ها بعد از load صفحه
        updateCartButtonsColor();
        updateWishlistCompareButtons();
        
        // به‌روزرسانی بعد از fragments refresh
        $(document.body).on('added_to_cart', function(event, fragments) {
            if (fragments) {
                updateCartItemsFromFragments(fragments);
                updateCartButtonsColor();
            }
        });
        
        // به‌روزرسانی بعد از remove from cart
        $(document.body).on('removed_from_cart', function() {
            updateCartButtonsColor();
        });
        
        // به‌روزرسانی دوره‌ای برای اطمینان
        setInterval(function() {
            updateWishlistCompareButtons();
            updateCartButtonsColor();
        }, 2000);

        // حذف دوره‌ای پیام‌های WooCommerce
        setInterval(function() {
            removeWooCommerceMessages();
        }, 500);

        // MutationObserver برای حذف فوری پیام‌های جدید
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        if (node.nodeType === 1 && node.classList) {
                            if (node.classList.contains('woocommerce-message') || 
                                node.classList.contains('woocommerce-info')) {
                                var text = node.textContent || '';
                                var html = node.innerHTML || '';
                                
                                if (text.indexOf('مشاهده سبد خرید') !== -1 || 
                                    text.indexOf('View cart') !== -1 ||
                                    text.indexOf('سبد خرید') !== -1 ||
                                    html.indexOf('cart') !== -1 ||
                                    html.indexOf('سبد') !== -1 ||
                                    $(node).find('a[href*="cart"]').length > 0 ||
                                    $(node).find('a[href*="سبد"]').length > 0) {
                                    $(node).remove();
                                }
                            }
                        }
                    }
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Remove from Wishlist
        $(document).on('click', '.ki-remove-wishlist-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var productId = $btn.data('product-id');
            var $item = $btn.closest('.ki-wishlist-item');
            
            $.ajax({
                url: ki_product_actions.ajax_url,
                type: 'POST',
                data: {
                    action: 'ki_wishlist',
                    product_id: productId,
                    nonce: ki_product_actions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            
                            // اگر لیست خالی شد، صفحه را refresh کن
                            if ($('.ki-wishlist-item').length === 0) {
                                location.reload();
                            }
                        });
                        
                        // به‌روزرسانی آرایه
                        if (typeof ki_product_actions !== 'undefined' && ki_product_actions.wishlist_items) {
                            ki_product_actions.wishlist_items = ki_product_actions.wishlist_items.filter(function(id) {
                                return parseInt(id) !== parseInt(productId);
                            });
                        }
                        
                        showNotification(response.data.message, 'success');
                    } else {
                        showNotification(response.data.message || 'خطا در حذف', 'error');
                    }
                },
                error: function() {
                    showNotification('خطا در ارتباط با سرور', 'error');
                }
            });
        });

        // Remove from Compare
        $(document).on('click', '.ki-remove-compare-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var productId = $btn.data('product-id');
            
            $.ajax({
                url: ki_product_actions.ajax_url,
                type: 'POST',
                data: {
                    action: 'ki_compare',
                    product_id: productId,
                    nonce: ki_product_actions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // حذف ستون از جدول مقایسه
                        $('.ki-compare-table th, .ki-compare-table td').filter(function() {
                            return $(this).find('[data-product-id="' + productId + '"]').length > 0;
                        }).fadeOut(300, function() {
                            $(this).remove();
                            
                            // اگر جدول خالی شد، صفحه را refresh کن
                            if ($('.ki-compare-table th').length <= 1) {
                                location.reload();
                            }
                        });
                        
                        // به‌روزرسانی آرایه
                        if (typeof ki_product_actions !== 'undefined' && ki_product_actions.compare_items) {
                            ki_product_actions.compare_items = ki_product_actions.compare_items.filter(function(id) {
                                return parseInt(id) !== parseInt(productId);
                            });
                        }
                        
                        showNotification(response.data.message, 'success');
                    } else {
                        showNotification(response.data.message || 'خطا در حذف محصول', 'error');
                    }
                },
                error: function() {
                    showNotification('خطا در ارتباط با سرور', 'error');
                }
            });
        });
    });

})(jQuery);
