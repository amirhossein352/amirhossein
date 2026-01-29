/**
 * Single Product Page - Purchase Confirmation Modal JavaScript
 * 
 * این فایل شامل عملکردهای JavaScript مربوط به پاپ‌آپ تایید خرید است:
 * - باز کردن و بستن مودال
 * - تایید افزودن به سبد خرید
 * - تایید خرید مستقیم
 * - مدیریت رویدادهای کیبورد و کلیک
 * - انیمیشن‌های ورود و خروج
 * 
 * @package khane_irani
 * @version 1.0.0
 */

// Purchase Confirmation Modal Functions
function zsShowPurchaseConfirmation() {
    var modal = document.getElementById('zs-purchase-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Add entrance animation
        modal.style.opacity = '0';
        setTimeout(function() {
            modal.style.transition = 'opacity 0.3s ease-out';
            modal.style.opacity = '1';
        }, 10);
    }
}

function zsClosePurchaseModal() {
    var modal = document.getElementById('zs-purchase-modal');
    if (modal) {
        modal.style.transition = 'opacity 0.3s ease-out';
        modal.style.opacity = '0';
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

function zsConfirmAddToCart() {
    var modal = document.getElementById('zs-purchase-modal');
    if (!modal) return;
    
    var productId = modal.getAttribute('data-product-id');
    if (!productId) {
        // Fallback: try to get product ID from form
    var addToCartForm = document.querySelector('form.cart');
    if (addToCartForm) {
            var productIdInput = addToCartForm.querySelector('input[name="product_id"]');
            if (productIdInput) {
                productId = productIdInput.value;
            }
        }
    }
    
    if (!productId) {
        zsShowToast('خطا: شناسه محصول پیدا نشد');
        return;
    }
    
    // Get quantity if available
    var quantity = '1';
    var quantityInput = document.querySelector('form.cart input[name="quantity"]');
    if (quantityInput) {
        quantity = quantityInput.value || '1';
    }
    
    // Build AJAX URL - use wc_add_to_cart_params if available, otherwise fallback
    var ajaxUrl;
    if (window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url) {
        ajaxUrl = window.wc_add_to_cart_params.wc_ajax_url;
        // CRITICAL: Replace placeholder if exists (WooCommerce uses %%endpoint%% placeholder)
        ajaxUrl = ajaxUrl.replace(/%%endpoint%%/g, 'add_to_cart');
    } else if (window.wc_ajax_url) {
        ajaxUrl = window.wc_ajax_url;
        // Replace placeholder if exists
        ajaxUrl = ajaxUrl.replace(/%%endpoint%%/g, 'add_to_cart');
    } else {
        ajaxUrl = window.location.origin + '/?wc-ajax=add_to_cart';
    }
    
    // Make sure URL is absolute (starts with http:// or https://)
    if (ajaxUrl.indexOf('http') !== 0) {
        ajaxUrl = window.location.origin + ajaxUrl;
    }
    
    console.log('[Modal zsConfirmAddToCart] AJAX URL:', ajaxUrl);
    console.log('[Modal zsConfirmAddToCart] Product ID:', productId);
    console.log('[Modal zsConfirmAddToCart] Quantity:', quantity);
    console.log('[Modal zsConfirmAddToCart] wc_add_to_cart_params:', window.wc_add_to_cart_params);
    
    var body = new URLSearchParams();
    body.append('product_id', productId);
    body.append('quantity', quantity);
    
    console.log('[Modal zsConfirmAddToCart] Request Body:', body.toString());
    
    // Show loading state
    var addToCartBtn = document.querySelector('.zs-confirm-add-to-cart');
    if (addToCartBtn) {
        var originalText = addToCartBtn.innerHTML;
        addToCartBtn.disabled = true;
        addToCartBtn.innerHTML = '<span>در حال افزودن...</span>';
    }
    
    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString(),
        credentials: 'same-origin'
    }).then(function(res){ 
        console.log('[Modal zsConfirmAddToCart] Response Status:', res.status);
        console.log('[Modal zsConfirmAddToCart] Response OK:', res.ok);
        
        // Check HTTP status
        if (!res.ok) {
            return res.text().then(function(text) {
                console.error('[Modal zsConfirmAddToCart] HTTP Error Response:', text);
                throw new Error('HTTP error: ' + res.status + ' - ' + text);
            });
        }
        // Try to get response as text first to check if it's empty
        return res.text().then(function(text) {
            console.log('[Modal zsConfirmAddToCart] Response Text Length:', text ? text.length : 0);
            console.log('[Modal zsConfirmAddToCart] Response Text (first 500 chars):', text ? text.substring(0, 500) : 'EMPTY');
            
            if (!text || text.trim() === '') {
                console.error('[Modal zsConfirmAddToCart] Empty Response!');
                return { error: true, error_message: 'پاسخ خالی از سرور دریافت شد' };
            }
            try {
                var parsed = JSON.parse(text);
                console.log('[Modal zsConfirmAddToCart] Parsed JSON:', parsed);
                return parsed;
            } catch(e) {
                console.error('[Modal zsConfirmAddToCart] JSON Parse Error:', e);
                console.error('[Modal zsConfirmAddToCart] Response was:', text);
                // If not JSON, it might be HTML error page
                return { error: true, error_message: 'پاسخ نامعتبر از سرور دریافت شد' };
            }
        });
    })
      .then(function(data){
        // Restore button
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = originalText;
        }
        
        // Check if data is valid
        if (!data || typeof data !== 'object') {
            var errorMsg = 'خطا در افزودن به سبد خرید - پاسخ نامعتبر';
            if (typeof zsShowToast === 'function') {
                zsShowToast(errorMsg);
            } else {
                alert(errorMsg);
            }
            return;
        }
        
        // Check if WooCommerce returned an error
        if (data.error === true || data.error_code || (data.error_message && data.error_message !== '')) {
            var errorMsg = data.error_message || 'خطا در افزودن به سبد خرید';
            if (typeof zsShowToast === 'function') {
                zsShowToast(errorMsg);
            } else {
                alert(errorMsg);
            }
            return;
        }
        
        // WooCommerce success response typically has fragments and cart_hash, or error=false
        // If no error flag is set and we have valid data, consider it success
        if (data.error === false || data.fragments || data.cart_hash || (!data.error && !data.error_code)) {
            zsClosePurchaseModal();
            
            // Show success message
            if (typeof zsShowToast === 'function') {
                zsShowToast('محصول به سبد خرید اضافه شد!');
            } else {
                alert('محصول به سبد خرید اضافه شد!');
            }
            
            // Notify header cart to refresh - ENHANCED with multiple events
            try { 
                // Custom event
                document.dispatchEvent(new CustomEvent('zs-cart-updated')); 
                // Window events for broader compatibility
                window.dispatchEvent(new CustomEvent('wc_cart_updated'));
                window.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
                
                // Also trigger WooCommerce cart updated event with jQuery
                if (window.jQuery) {
                    window.jQuery('body').trigger('added_to_cart', [data.fragments || {}, data.cart_hash || '']);
                    window.jQuery('body').trigger('wc_fragment_refresh');
                    window.jQuery('body').trigger('updated_wc_div');
                }
            } catch(e) {
                // Silent fail
            }
        } else {
            // Unknown error
            if (typeof zsShowToast === 'function') {
                zsShowToast('خطا در افزودن به سبد خرید');
            } else {
                alert('خطا در افزودن به سبد خرید');
            }
        }
    }).catch(function(err){
        // Restore button
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = originalText;
        }
        
        // Show error message
        if (typeof zsShowToast === 'function') {
            zsShowToast('خطا در افزودن به سبد خرید');
        } else {
            alert('خطا در افزودن به سبد خرید');
        }
    });
}

function zsConfirmBuyNow() {
    var modal = document.getElementById('zs-purchase-modal');
    if (!modal) return;
    
    var productId = modal.getAttribute('data-product-id');
    var checkoutUrl = modal.getAttribute('data-checkout-url') || '/checkout/';
    
    if (!productId) {
        // Fallback: try to get product ID from form
    var addToCartForm = document.querySelector('form.cart');
    if (addToCartForm) {
            var productIdInput = addToCartForm.querySelector('input[name="product_id"]');
            if (productIdInput) {
                productId = productIdInput.value;
            }
        }
    }
    
    if (!productId) {
        if (typeof zsShowToast === 'function') {
            zsShowToast('خطا: شناسه محصول پیدا نشد');
        } else {
            alert('خطا: شناسه محصول پیدا نشد');
        }
        return;
    }
    
    // Get quantity if available
    var quantity = '1';
    var quantityInput = document.querySelector('form.cart input[name="quantity"]');
    if (quantityInput) {
        quantity = quantityInput.value || '1';
    }
    
    // Build AJAX URL - use wc_add_to_cart_params if available, otherwise fallback
    var ajaxUrl;
    if (window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url) {
        ajaxUrl = window.wc_add_to_cart_params.wc_ajax_url;
        // CRITICAL: Replace placeholder if exists (WooCommerce uses %%endpoint%% placeholder)
        ajaxUrl = ajaxUrl.replace(/%%endpoint%%/g, 'add_to_cart');
    } else if (window.wc_ajax_url) {
        ajaxUrl = window.wc_ajax_url;
        // Replace placeholder if exists
        ajaxUrl = ajaxUrl.replace(/%%endpoint%%/g, 'add_to_cart');
    } else {
        ajaxUrl = window.location.origin + '/?wc-ajax=add_to_cart';
    }
    
    // Make sure URL is absolute (starts with http:// or https://)
    if (ajaxUrl.indexOf('http') !== 0) {
        ajaxUrl = window.location.origin + ajaxUrl;
    }
    
    var body = new URLSearchParams();
    body.append('product_id', productId);
    body.append('quantity', quantity);
    
    // Show loading state
    var buyNowBtn = document.querySelector('.zs-confirm-buy-now');
    if (buyNowBtn) {
        var originalText = buyNowBtn.innerHTML;
        buyNowBtn.disabled = true;
        buyNowBtn.innerHTML = '<span>در حال پردازش...</span>';
    }
    
    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString(),
        credentials: 'same-origin'
    }).then(function(res){ 
        // Check HTTP status
        if (!res.ok) {
            return res.text().then(function(text) {
                throw new Error('HTTP error: ' + res.status + ' - ' + text);
            });
        }
        // Try to get response as text first to check if it's empty
        return res.text().then(function(text) {
            if (!text || text.trim() === '') {
                return { error: true, error_message: 'پاسخ خالی از سرور دریافت شد' };
            }
            try {
                return JSON.parse(text);
            } catch(e) {
                // If not JSON, it might be HTML error page
                return { error: true, error_message: 'پاسخ نامعتبر از سرور دریافت شد' };
            }
        });
    })
      .then(function(data){
        // Restore button
        if (buyNowBtn) {
            buyNowBtn.disabled = false;
            buyNowBtn.innerHTML = originalText;
        }
        
        // Check if data is valid
        if (!data || typeof data !== 'object') {
            var errorMsg = 'خطا در خرید مستقیم - پاسخ نامعتبر';
            if (typeof zsShowToast === 'function') {
                zsShowToast(errorMsg);
            } else {
                alert(errorMsg);
            }
            return;
        }
        
        // Check if WooCommerce returned an error
        if (data.error === true || data.error_code || (data.error_message && data.error_message !== '')) {
            var errorMsg = data.error_message || 'خطا در خرید مستقیم';
            if (typeof zsShowToast === 'function') {
                zsShowToast(errorMsg);
            } else {
                alert(errorMsg);
            }
            return;
        }
        
        // WooCommerce success response typically has fragments and cart_hash, or error=false
        // If no error flag is set and we have valid data, consider it success
        if (data.error === false || data.fragments || data.cart_hash || (!data.error && !data.error_code)) {
            zsClosePurchaseModal();
            
            // Notify header cart to refresh - ENHANCED with multiple events
            try { 
                // Custom event
                document.dispatchEvent(new CustomEvent('zs-cart-updated')); 
                // Window events for broader compatibility
                window.dispatchEvent(new CustomEvent('wc_cart_updated'));
                window.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
                
                // Also trigger WooCommerce cart updated event with jQuery
                if (window.jQuery) {
                    window.jQuery('body').trigger('added_to_cart', [data.fragments || {}, data.cart_hash || '']);
                    window.jQuery('body').trigger('wc_fragment_refresh');
                    window.jQuery('body').trigger('updated_wc_div');
                }
            } catch(e) {
                // Silent fail
            }
            
            // Redirect to checkout after successful add to cart
            setTimeout(function() {
                window.location.href = checkoutUrl;
            }, 300);
        } else {
            // Unknown error
            if (typeof zsShowToast === 'function') {
                zsShowToast('خطا در خرید مستقیم');
            } else {
                alert('خطا در خرید مستقیم');
            }
        }
    }).catch(function(err){
        // Restore button
        if (buyNowBtn) {
            buyNowBtn.disabled = false;
            buyNowBtn.innerHTML = originalText;
        }
        
        // Show error message
        if (typeof zsShowToast === 'function') {
            zsShowToast('خطا در خرید مستقیم');
        } else {
            alert('خطا در خرید مستقیم');
        }
    });
}

// Event Listeners for Modal
(function(){
    if (typeof document === 'undefined') return;
    var root = document;
    
    function onReady(fn){
        if (root.readyState === 'complete' || root.readyState === 'interactive') { fn(); }
        else { root.addEventListener('DOMContentLoaded', fn); }
    }
    
    onReady(function(){
        // Close modal when clicking overlay
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('zs-modal-overlay')) {
                zsClosePurchaseModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var modal = document.getElementById('zs-purchase-modal');
                if (modal && modal.style.display === 'flex') {
                    zsClosePurchaseModal();
                }
            }
        });
        
        // Add click animations to modal buttons
        var modalButtons = document.querySelectorAll('.zs-confirm-add-to-cart, .zs-confirm-buy-now, .zs-cancel-btn');
        modalButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(function() {
                    btn.style.transform = 'scale(1)';
                }, 150);
            });
        });
    });
})();
