/**
 * Single Product Page - Header JavaScript
 * 
 * این فایل شامل عملکردهای JavaScript مربوط به هدر صفحه تک محصول است:
 * - انیمیشن‌های ورود هدر
 * - انیمیشن‌های کلیک برای breadcrumb links
 * - انیمیشن‌های hover برای action buttons
 * - عملکرد اشتراک‌گذاری محصول
 * - Toast notifications
 * 
 * @package khane_irani
 * @version 1.0.0
 */

(function(){
    if (typeof document === 'undefined') return;
    var root = document;
    
    function onReady(fn){
        if (root.readyState === 'complete' || root.readyState === 'interactive') { fn(); }
        else { root.addEventListener('DOMContentLoaded', fn); }
    }
    
    onReady(function(){
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
