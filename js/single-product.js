// Enhanced Single Product Page JavaScript
(function(){
    if (typeof document === 'undefined') return;
    var root = document;
    
    function onReady(fn){
        if (root.readyState === 'complete' || root.readyState === 'interactive') { fn(); }
        else { root.addEventListener('DOMContentLoaded', fn); }
    }
    
    onReady(function(){
        // FAQ accordion functionality
        var items = root.querySelectorAll('.zs-faq-item');
        if (items && items.length) {
            items.forEach(function(item){
                var btn = item.querySelector('.zs-faq-q');
                if (!btn) return;
                btn.addEventListener('click', function(){
                    var isActive = item.classList.contains('active');
                    // close others
                    items.forEach(function(it){ it.classList.remove('active'); });
                    // toggle current
                    if (!isActive) item.classList.add('active');
                });
            });
        }
        
        // Enhanced header animations
        var header = root.querySelector('.zs-product-page-header');
        if (header) {
            // Add entrance animation
            header.style.opacity = '0';
            header.style.transform = 'translateY(30px)';
            
            setTimeout(function() {
                header.style.transition = 'all 0.8s ease-out';
                header.style.opacity = '1';
                header.style.transform = 'translateY(0)';
            }, 100);
        }
        
        // Smooth scroll for breadcrumb links
        var breadcrumbLinks = root.querySelectorAll('.zs-breadcrumb-link');
        breadcrumbLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Add click animation
                this.style.transform = 'scale(0.95)';
                setTimeout(function() {
                    link.style.transform = 'scale(1)';
                }, 150);
            });
        });
        
        // Action button animations
        var actionBtns = root.querySelectorAll('.zs-action-btn');
        actionBtns.forEach(function(btn) {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Enhanced features animations
        var featureCards = root.querySelectorAll('.feature-card');
        featureCards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Intersection Observer for features animation
        if ('IntersectionObserver' in window) {
            var featureItems = root.querySelectorAll('.feature-item');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            featureItems.forEach(function(item) {
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';
                item.style.transition = 'all 0.6s ease-out';
                observer.observe(item);
            });
        }
    });
})();

// Share Product Function
function zsShareProduct() {
    if (navigator.share) {
        // Use native Web Share API if available
        navigator.share({
            title: document.title,
            text: document.querySelector('.zs-product-subtitle')?.textContent || '',
            url: window.location.href
        }).catch(function(err) {
            console.log('Error sharing:', err);
            zsFallbackShare();
        });
    } else {
        // Fallback to custom share modal
        zsFallbackShare();
    }
}

// Fallback Share Function
function zsFallbackShare() {
    var url = window.location.href;
    var title = document.title;
    var text = document.querySelector('.zs-product-subtitle')?.textContent || '';
    
    // Create share modal
    var modal = document.createElement('div');
    modal.className = 'zs-share-modal';
    modal.innerHTML = `
        <div class="zs-share-overlay"></div>
        <div class="zs-share-content">
            <div class="zs-share-header">
                <h3>اشتراک‌گذاری محصول</h3>
                <button class="zs-share-close">&times;</button>
            </div>
            <div class="zs-share-body">
                <div class="zs-share-input-group">
                    <label>لینک محصول:</label>
                    <div class="zs-share-input-wrapper">
                        <input type="text" value="${url}" readonly class="zs-share-input">
                        <button class="zs-copy-btn" onclick="zsCopyToClipboard('${url}')">کپی</button>
                    </div>
                </div>
                <div class="zs-share-social">
                    <a href="https://telegram.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}" target="_blank" class="zs-social-btn zs-telegram">
                        <i class="fab fa-telegram"></i>
                        تلگرام
                    </a>
                    <a href="https://wa.me/?text=${encodeURIComponent(title + ' - ' + url)}" target="_blank" class="zs-social-btn zs-whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        واتساپ
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}" target="_blank" class="zs-social-btn zs-twitter">
                        <i class="fab fa-twitter"></i>
                        توییتر
                    </a>
                </div>
            </div>
        </div>
    `;
    
    // Add styles
    var style = document.createElement('style');
    style.textContent = `
        .zs-share-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .zs-share-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        .zs-share-content {
            position: relative;
            background: white;
            border-radius: 16px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: zsModalSlideIn 0.3s ease-out;
        }
        .zs-share-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .zs-share-header h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .zs-share-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }
        .zs-share-body {
            padding: 20px;
        }
        .zs-share-input-group {
            margin-bottom: 20px;
        }
        .zs-share-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .zs-share-input-wrapper {
            display: flex;
            gap: 8px;
        }
        .zs-share-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .zs-copy-btn {
            padding: 10px 16px;
            background: #007cba;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .zs-share-social {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .zs-social-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .zs-telegram { background: #0088cc; color: white; }
        .zs-whatsapp { background: #25d366; color: white; }
        .zs-twitter { background: #1da1f2; color: white; }
        .zs-social-btn:hover { transform: translateY(-2px); }
        @keyframes zsModalSlideIn {
            from { opacity: 0; transform: scale(0.9) translateY(-20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(modal);
    
    // Close modal functionality
    modal.querySelector('.zs-share-close').addEventListener('click', function() {
        document.body.removeChild(modal);
        document.head.removeChild(style);
    });
    
    modal.querySelector('.zs-share-overlay').addEventListener('click', function() {
        document.body.removeChild(modal);
        document.head.removeChild(style);
    });
}

// Copy to Clipboard Function
function zsCopyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            zsShowToast('لینک کپی شد!');
        });
    } else {
        // Fallback for older browsers
        var textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        zsShowToast('لینک کپی شد!');
    }
}

// Toast Notification Function
function zsShowToast(message) {
    var toast = document.createElement('div');
    toast.className = 'zs-toast';
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4caf50;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10001;
        animation: zsToastSlideIn 0.3s ease-out;
    `;
    
    var style = document.createElement('style');
    style.textContent = `
        @keyframes zsToastSlideIn {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.style.animation = 'zsToastSlideIn 0.3s ease-out reverse';
        setTimeout(function() {
            document.body.removeChild(toast);
            document.head.removeChild(style);
        }, 300);
    }, 2000);
}

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
    zsClosePurchaseModal();
    
    // Find the original WooCommerce add to cart form
    var addToCartForm = document.querySelector('form.cart');
    if (addToCartForm) {
        // Trigger the form submission
        var submitButton = addToCartForm.querySelector('.single_add_to_cart_button');
        if (submitButton) {
            submitButton.click();
        } else {
            // Fallback: submit the form directly
            addToCartForm.submit();
        }
    } else {
        zsShowToast('خطا در افزودن به سبد خرید');
    }
}

function zsConfirmBuyNow() {
    zsClosePurchaseModal();
    
    // Add to cart first, then redirect to checkout
    var addToCartForm = document.querySelector('form.cart');
    if (addToCartForm) {
        // Create a hidden input to indicate direct purchase
        var directPurchaseInput = document.createElement('input');
        directPurchaseInput.type = 'hidden';
        directPurchaseInput.name = 'direct_purchase';
        directPurchaseInput.value = '1';
        addToCartForm.appendChild(directPurchaseInput);
        
        // Submit the form
        var submitButton = addToCartForm.querySelector('.single_add_to_cart_button');
        if (submitButton) {
            submitButton.click();
        } else {
            addToCartForm.submit();
        }
        
        // Remove the hidden input after submission
        setTimeout(function() {
            if (addToCartForm.contains(directPurchaseInput)) {
                addToCartForm.removeChild(directPurchaseInput);
            }
        }, 100);
    } else {
        zsShowToast('خطا در پردازش خرید مستقیم');
    }
}

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


