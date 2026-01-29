/**
 * Cart Sidebar Functionality
 * Handles cart sidebar open/close and cart refresh
 */
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function(){
        var btn = document.getElementById('zs-cart-btn');
        var closeBtn = document.getElementById('zs-cart-close');
        var sidebar = document.getElementById('zs-cart-sidebar');
        var countEl = document.getElementById('zs-header-cart-count');
        var itemsEl = document.getElementById('zs-cart-items');
        var totalEl = document.getElementById('zs-cart-total');
        
        // Error tracking برای جلوگیری از spam خطا
        var cartRefreshErrors = 0;
        var maxCartErrors = 5;
        var cartRefreshInterval = null;
        var isCartRefreshing = false;

        function openCart(){ 
            if (sidebar) {
                sidebar.style.right = '0'; 
                document.body.style.overflow = 'hidden'; 
                document.body.classList.add('cart-open');
            }
        }
        
        function closeCart(){ 
            if (sidebar) {
                sidebar.style.right = '-420px'; 
                document.body.style.overflow = ''; 
                document.body.classList.remove('cart-open');
            }
        }
        
        if (btn) {
            btn.addEventListener('click', function(){ 
                refreshCart(); 
                openCart(); 
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeCart);
        }
        
        // Close cart when clicking outside (on sidebar backdrop)
        if (sidebar) {
            sidebar.addEventListener('click', function(e) {
                if (e.target === sidebar || e.target.classList.contains('zs-cart-sidebar')) {
                    closeCart();
                }
            });
        }

                        function renderCartItems(items, cartItemKeys){
            if (!itemsEl) return;
            var html = '';
            if (items && items.length > 0) {
                (items || []).forEach(function(it, index){
                    var cartItemKey = (cartItemKeys && cartItemKeys[index]) ? cartItemKeys[index] : '';
                    var imageHtml = it.image ? '<img src="' + escapeHtml(it.image) + '" alt="" width="48" height="48" style="border-radius:8px;object-fit:cover;">' : '';
                    html += '<div class="zs-cart-item" data-cart-item-key="' + escapeHtml(cartItemKey) + '" style="display:flex;align-items:center;gap:10px;border-bottom:1px solid #eee;padding:10px 0;position:relative;">' +
                        imageHtml +
                        '<div style="flex:1;">' +
                            '<div style="font-weight:700;color:#333;">' + escapeHtml(it.name) + '</div>' +
                            '<div style="font-size:12px;color:#666;">تعداد: ' + escapeHtml(String(it.quantity)) + '</div>' +
                        '</div>' +
                        '<div style="font-weight:700;color:#333;white-space:nowrap;margin-left:10px;">' + it.price + '</div>' +
                        '<button type="button" class="zs-cart-remove-item" data-cart-item-key="' + escapeHtml(cartItemKey) + '" style="background:#dc3545;color:#fff;border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;line-height:1;margin-right:10px;transition:all 0.2s;" title="حذف">×</button>' +
                    '</div>';
                });
            } else {
                html = '<div style="padding:20px;color:#666;text-align:center;">سبد خرید خالی است.</div>';
            }
            itemsEl.innerHTML = html;
            
            // اضافه کردن event listener برای دکمه‌های حذف
            var removeButtons = itemsEl.querySelectorAll('.zs-cart-remove-item');
            removeButtons.forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var cartItemKey = btn.getAttribute('data-cart-item-key');
                    if (cartItemKey) {
                        removeCartItem(cartItemKey, btn.closest('.zs-cart-item'));
                    }
                });
            });
        }
        
        function removeCartItem(cartItemKey, itemElement){
            if (!cartItemKey) return;
            
            // Disable button
            var btn = itemElement ? itemElement.querySelector('.zs-cart-remove-item') : null;
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
            }
            
            // Send AJAX request
            var ajaxUrl = (window.wc_add_to_cart_params && window.wc_add_to_cart_params.ajax_url) 
                ? window.wc_add_to_cart_params.ajax_url 
                : (window.location.origin + '/wp-admin/admin-ajax.php');
            
            var formData = new FormData();
            formData.append('action', 'woocommerce_remove_from_cart');
            formData.append('cart_item_key', cartItemKey);
            formData.append('_wpnonce', (window.wc_add_to_cart_params && window.wc_add_to_cart_params.remove_item_nonce) ? window.wc_add_to_cart_params.remove_item_nonce : '');
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxUrl, true);
            xhr.onload = function(){
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response && response.fragments) {
                            // Refresh cart
                            refreshCart();
                            // Trigger cart updated event
                            document.dispatchEvent(new CustomEvent('zs-cart-updated'));
                            if (window.jQuery) {
                                jQuery(document.body).trigger('updated_wc_div');
                            }
                        }
                    } catch(e) {
                        console.warn('Error parsing remove cart response:', e);
                        refreshCart();
                    }
                } else {
                    console.warn('Remove cart item failed:', xhr.status);
                    refreshCart();
                }
            };
            xhr.onerror = function(){
                console.warn('Remove cart item network error');
                refreshCart();
            };
            xhr.send(formData);
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Export refreshCart globally - استفاده از endpoint سفارشی
        window.refreshCart = function(){
            // پیدا کردن elements در هر بار فراخوانی
            if (!countEl) countEl = document.getElementById('zs-header-cart-count');
            if (!itemsEl) itemsEl = document.getElementById('zs-cart-items');
            if (!totalEl) totalEl = document.getElementById('zs-cart-total');
            
            // روش 1: استفاده از endpoint سفارشی - مستقیم از WooCommerce
            var ajaxUrl = (typeof ki_product_actions !== 'undefined' && ki_product_actions.ajax_url)
                ? ki_product_actions.ajax_url
                : (window.location.origin + '/wp-admin/admin-ajax.php');
            
            var customRequest = new XMLHttpRequest();
            customRequest.open('POST', ajaxUrl, true);
            customRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            
            customRequest.onload = function(){
                if (customRequest.status === 200) {
                    try {
                        var response = JSON.parse(customRequest.responseText);
                        if (response.success && response.data) {
                            var cartCount = parseInt(response.data.cart_count) || 0;
                            var cartTotal = response.data.cart_total || '0';
                            var cartItems = response.data.items || [];
                            
                            // تبدیل به فرمت مورد نیاز
                            var items = [];
                            var cartItemKeys = [];
                            
                            cartItems.forEach(function(item){
                                if (item && item.name) {
                                    items.push({
                                        name: item.name || '',
                                        quantity: parseInt(item.quantity) || 1,
                                        price: item.price || '',
                                        image: item.image || ''
                                    });
                                    cartItemKeys.push(item.key || '');
                                }
                            });
                            
                            // استفاده از تعداد آیتم‌ها به جای cart_count از سرور
                            // چون باید تعداد آیتم‌های منحصر به فرد را نشان دهیم نه مجموع quantity
                            var actualItemCount = items.length;
                            
                            // به‌روزرسانی UI
                            if (countEl) {
                                countEl.textContent = String(actualItemCount);
                                if (actualItemCount > 0) {
                                    countEl.style.display = 'inline-flex';
                                } else {
                                    countEl.style.display = 'none';
                                }
                            }
                            if (totalEl) totalEl.innerHTML = cartTotal;
                            renderCartItems(items, cartItemKeys);
                            return;
                        }
                    } catch(e) {
                        console.warn('Custom cart API error:', e);
                    }
                }
                
                // Fallback: استفاده از WooCommerce fragments
                refreshCartFromFragments();
            };
            
            customRequest.onerror = function(){
                // اگر خطاهای زیادی داشتیم، skip کن
                if (cartRefreshErrors < maxCartErrors) {
                    refreshCartFromFragments();
                }
            };
            
            var formData = 'action=ki_get_cart_contents';
            if (typeof ki_product_actions !== 'undefined' && ki_product_actions.nonce) {
                formData += '&nonce=' + encodeURIComponent(ki_product_actions.nonce);
            }
            customRequest.send(formData);
        };
        
        // Error tracking برای جلوگیری از spam خطا
        var cartRefreshErrors = 0;
        var maxCartErrors = 5;
        var cartRefreshInterval = null;
        var isCartRefreshing = false;
        
        // تابع fallback برای fragments
        function refreshCartFromFragments(){
            // اگر در حال refresh است، skip کن
            if (isCartRefreshing) {
                return;
            }
            
            // اگر خطاهای زیادی داشتیم، skip کن
            if (cartRefreshErrors >= maxCartErrors) {
                console.warn('Cart refresh disabled due to too many errors');
                if (cartRefreshInterval) {
                    clearInterval(cartRefreshInterval);
                    cartRefreshInterval = null;
                }
                return;
            }
            
            isCartRefreshing = true;
            var fragUrl = (window.wc_cart_fragments_params && window.wc_cart_fragments_params.wc_ajax_url)
                ? window.wc_cart_fragments_params.wc_ajax_url.replace(/%%endpoint%%/, 'get_refreshed_fragments')
                : (window.location.origin + '/?wc-ajax=get_refreshed_fragments');
            
            var f = new XMLHttpRequest();
            f.open('POST', fragUrl, true);
            f.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            f.onload = function(){
                isCartRefreshing = false;
                
                // اگر response HTML است (خطا)، skip کن
                if (f.status >= 400 || f.responseText.trim().startsWith('<!DOCTYPE') || f.responseText.trim().startsWith('<html')) {
                    cartRefreshErrors++;
                    console.warn('Cart refresh error: Server returned HTML instead of JSON (status: ' + f.status + ')');
                    return;
                }
                
                try {
                    var data = JSON.parse(f.responseText);
                    // اگر موفق بود، خطاها را reset کن
                    cartRefreshErrors = 0;
                    var cartCount = 0;
                    var cartTotal = '0';
                    var items = [];
                    var cartItemKeys = [];
                    
                    if (data && data.fragments){
                        var mini = data.fragments['div.widget_shopping_cart_content'] || '';
                        if (mini) {
                            var tmp = document.createElement('div');
                            tmp.innerHTML = mini;
                            var nodes = tmp.querySelectorAll('.mini_cart_item, li[data-cart-item-key]');
                            
                            nodes.forEach(function(n){
                                var nameEl = n.querySelector('.mini-cart-item-name, a, .product-name');
                                var name = nameEl ? nameEl.textContent.trim() : '';
                                if (!name) return;
                                
                                var cartItemKey = '';
                                var removeLink = n.querySelector('a.remove');
                                if (removeLink) {
                                    var href = removeLink.getAttribute('href') || '';
                                    var match = href.match(/remove_item=([^&]+)/);
                                    if (match && match[1]) {
                                        cartItemKey = decodeURIComponent(match[1]);
                                    }
                                }
                                if (!cartItemKey) {
                                    cartItemKey = n.getAttribute('data-cart-item-key') || '';
                                }
                                
                                var qty = 1;
                                var qtyInput = n.querySelector('input.qty, .quantity input[type="number"]');
                                if (qtyInput && qtyInput.value) {
                                    var parsedQty = parseInt(qtyInput.value, 10);
                                    if (parsedQty && parsedQty > 0) {
                                        qty = parsedQty;
                                    }
                                }
                                
                                var priceNode = n.querySelector('.quantity .amount, .woocommerce-Price-amount');
                                var price = priceNode ? priceNode.outerHTML : '';
                                var img = (n.querySelector('img')||{}).src || '';
                                
                                items.push({ name: name, quantity: qty, price: price, image: img });
                                cartItemKeys.push(cartItemKey);
                                // شمارش تعداد آیتم‌ها (نه مجموع quantity)
                                cartCount += 1;
                            });
                            
                            var totalNode = tmp.querySelector('.woocommerce-mini-cart__total .woocommerce-Price-amount');
                            if (totalNode) {
                                cartTotal = totalNode.outerHTML;
                            }
                        }
                    }
                    
                    // استفاده از تعداد آیتم‌ها به جای مجموع quantity
                    var actualItemCount = items.length;
                    
                    if (countEl) {
                        countEl.textContent = String(actualItemCount || 0);
                        countEl.style.display = (actualItemCount > 0) ? 'inline-flex' : 'none';
                    }
                    if (totalEl) totalEl.innerHTML = cartTotal || '0';
                    renderCartItems(items, cartItemKeys);
                    
                } catch(e){
                    cartRefreshErrors++;
                    // فقط خطا را log کن اگر خطاهای کمی داشتیم
                    if (cartRefreshErrors < 3) {
                        console.warn('Cart refresh error:', e);
                    }
                }
            };
            f.onerror = function(){
                isCartRefreshing = false;
                cartRefreshErrors++;
                // فقط خطا را log کن اگر خطاهای کمی داشتیم
                if (cartRefreshErrors < 3) {
                    console.warn('Cart refresh network error');
                }
            };
            f.send('');
        }

        // Initial load on page - با تاخیر برای اطمینان از بارگذاری کامل
        setTimeout(function() {
            refreshCart();
        }, 500);

        // Listen to custom and WooCommerce events - ENHANCED with immediate refresh
        document.addEventListener('zs-cart-updated', function(){ 
            setTimeout(function(){ refreshCart(); }, 300); 
        });
        
        // Listen to WooCommerce events with multiple listeners
        if (window.jQuery && jQuery(document.body)){
            jQuery(document.body).on('added_to_cart wc_fragments_refreshed wc_fragments_loaded updated_wc_div', function(event, fragments){ 
                setTimeout(function(){ refreshCart(); }, 300); 
            });
        }
        
        // Also listen to cart updates via native events
        window.addEventListener('storage', function(e){
            if (e.key === 'wc_fragments_refreshed' || e.key === 'wc_cart_hash'){
                refreshCart();
            }
        });
        
        // Force refresh on focus (when user comes back to tab)
        window.addEventListener('focus', function(){
            refreshCart();
        });
        
        // Listen to custom cart update events from any source
        if (typeof window.addEventListener === 'function'){
            window.addEventListener('wc_cart_updated', function(){ 
                setTimeout(function(){ refreshCart(); }, 200); 
            });
            window.addEventListener('wc_fragment_refresh', function(){ 
                setTimeout(function(){ refreshCart(); }, 200); 
            });
        }
        
        // به‌روزرسانی دوره‌ای برای اطمینان (هر 10 ثانیه) - فقط اگر خطا نداشتیم
        cartRefreshInterval = setInterval(function() {
            // اگر خطاهای زیادی داشتیم، interval را متوقف کن
            if (cartRefreshErrors >= maxCartErrors) {
                clearInterval(cartRefreshInterval);
                cartRefreshInterval = null;
                return;
            }
            refreshCart();
        }, 10000); // افزایش به 10 ثانیه برای کاهش load
    });
})();

