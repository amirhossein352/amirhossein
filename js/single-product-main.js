/**
 * Single Product Page - Main JavaScript File
 * 
 * این فایل اصلی JavaScript صفحه تک محصول است که فایل‌های JS مختلف را ترکیب می‌کند:
 * - header.js: عملکردهای هدر و breadcrumb
 * - modal.js: عملکردهای پاپ‌آپ تایید خرید
 * - animations.js: انیمیشن‌ها و تعاملات
 * - details.js: عملکردهای بخش جزئیات محصول
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
        // Header entrance animation
        var header = root.querySelector('.zs-product-page-header');
        if (header) {
            header.style.opacity = '0';
            header.style.transform = 'translateY(-30px)';
            header.style.transition = 'all 0.8s ease-out';
            
            setTimeout(function() {
                header.style.opacity = '1';
                header.style.transform = 'translateY(0)';
            }, 100);
        }
        
        // Breadcrumb link animations
        var breadcrumbLinks = root.querySelectorAll('.zs-breadcrumb-link');
        breadcrumbLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(function() {
                    link.style.transform = 'scale(1)';
                }, 150);
            });
        });
        
        // Action button animations
        var actionButtons = root.querySelectorAll('.zs-action-btn');
        actionButtons.forEach(function(btn) {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Enhanced FAQ accordion functionality
        var faqItems = root.querySelectorAll('.zs-faq-item');
        
        if (faqItems && faqItems.length) {
            faqItems.forEach(function(item, index){
                var btn = item.querySelector('.zs-faq-question');
                if (!btn) return;
                
                btn.addEventListener('click', function(){
                    var isActive = item.classList.contains('active');
                    
                    // Close all other items
                    faqItems.forEach(function(it){ 
                        it.classList.remove('active');
                    });
                    
                    // Toggle current item
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
                
                // Add hover effects
                var card = item.querySelector('.zs-faq-card');
                if (card) {
                    card.addEventListener('mouseenter', function() {
                        if (!item.classList.contains('active')) {
                            this.style.transform = 'translateY(-2px)';
                        }
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                    });
                }
            });
        }
        
        // Enhanced Portfolio animations
        var portfolioItems = root.querySelectorAll('.zs-portfolio-item');
        portfolioItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Enhanced Reviews animations
        var reviewItems = root.querySelectorAll('.commentlist .review');
        reviewItems.forEach(function(review) {
            review.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            review.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Review form enhancements
        var reviewForm = root.querySelector('.comment-respond');
        if (reviewForm) {
            var textarea = reviewForm.querySelector('textarea');
            var submitBtn = reviewForm.querySelector('input[type="submit"]');
            
            if (textarea) {
                textarea.addEventListener('focus', function() {
                    this.style.borderColor = 'var(--zs-accent-primary)';
                    this.style.boxShadow = '0 0 0 3px rgba(0, 54, 197, 0.1)';
                });
                
                textarea.addEventListener('blur', function() {
                    this.style.borderColor = 'var(--zs-border-color)';
                    this.style.boxShadow = 'none';
                });
            }
            
            if (submitBtn) {
                submitBtn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 20px rgba(0, 54, 197, 0.3)';
                });
                
                submitBtn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            }
        }
        
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
        
        // Trust card animations
        var trustCards = root.querySelectorAll('.trust-card');
        trustCards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Enhanced counter animations
        var counters = root.querySelectorAll('.zs-counter');
        if (counters.length > 0) {
            var counterObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var counter = entry.target;
                        var valueElement = counter.querySelector('.zs-counter-value');
                        if (valueElement) {
                            var finalValue = valueElement.getAttribute('data-count');
                            var suffix = valueElement.textContent.replace(/^\d+/, '');
                            
                            if (finalValue && !isNaN(finalValue)) {
                                var numericValue = parseInt(finalValue);
                                var currentValue = 0;
                                var increment = Math.ceil(numericValue / 50);
                                var timer = setInterval(function() {
                                    currentValue += increment;
                                    if (currentValue >= numericValue) {
                                        currentValue = numericValue;
                                        clearInterval(timer);
                                    }
                                    valueElement.textContent = currentValue + suffix;
                                }, 30);
                            }
                        }
                        counterObserver.unobserve(counter);
                    }
                });
            }, { threshold: 0.5 });
            
            counters.forEach(function(counter) {
                counterObserver.observe(counter);
            });
        }
        
        // Smooth scroll for anchor links
        var anchorLinks = root.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var targetId = this.getAttribute('href').substring(1);
                var targetElement = root.getElementById(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add loading animation to images
        var images = root.querySelectorAll('img');
        images.forEach(function(img) {
            if (!img.complete) {
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease';
                
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
                
                img.addEventListener('error', function() {
                    this.style.opacity = '0.5';
                });
            }
        });
        
        // Add click animation to buttons
        var buttons = root.querySelectorAll('button, .zs-portfolio-item, .trust-card');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(function() {
                    btn.style.transform = '';
                }, 150);
            });
        });
        
        // Intersection Observer for general animations
        if ('IntersectionObserver' in window) {
            var animatedElements = root.querySelectorAll('[data-aos]');
            var animationObserver = new IntersectionObserver(function(entries) {
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
            
            animatedElements.forEach(function(element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.6s ease-out';
                animationObserver.observe(element);
            });
        }
        
        // Add ripple effect to clickable elements
        var clickableElements = root.querySelectorAll('.zs-faq-question, .zs-portfolio-item, .trust-card');
        clickableElements.forEach(function(element) {
            element.addEventListener('click', function(e) {
                var ripple = document.createElement('span');
                var rect = this.getBoundingClientRect();
                var size = Math.max(rect.width, rect.height);
                var x = e.clientX - rect.left - size / 2;
                var y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(0, 54, 197, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(function() {
                    if (ripple.parentNode) {
                        ripple.parentNode.removeChild(ripple);
                    }
                }, 600);
            });
        });
        
        // Add ripple animation CSS
        var style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Lazy-load dedicated reviews script if reviews section exists
        var hasReviews = root.querySelector('#reviews, .woocommerce-Reviews');
        if (hasReviews) {
            var script = document.createElement('script');
            script.src = (window.zsThemeBase || (document.querySelector('link[rel="stylesheet"][href*="single-product-main.css"]') ? document.querySelector('link[rel="stylesheet"][href*="single-product-main.css"]').href.replace(/\/css\/single-product-main\.css.*/, '') : (document.currentScript ? document.currentScript.src.replace(/\/js\/single-product-main\.js.*/, '') : '')) ) + '/js/single-product/reviews.js';
            script.defer = true;
            document.body.appendChild(script);
        }

        // Lazy-load portfolio script if portfolio section exists
        var hasPortfolio = root.querySelector('.zs-portfolio-section');
        if (hasPortfolio) {
            var pscript = document.createElement('script');
            pscript.src = (window.zsThemeBase || (document.querySelector('link[rel="stylesheet"][href*="single-product-main.css"]') ? document.querySelector('link[rel="stylesheet"][href*="single-product-main.css"]').href.replace(/\/css\/single-product-main\.css.*/, '') : (document.currentScript ? document.currentScript.src.replace(/\/js\/single-product-main\.js.*/, '') : '')) ) + '/js/single-product/portfolio.js?v=2.1';
            pscript.defer = true;
            document.body.appendChild(pscript);
        }

        // Lazy-load features script if features section exists
        var hasFeatures = root.querySelector('.service-features-section');
        if (hasFeatures) {
            var fscript = document.createElement('script');
            fscript.src = (window.zsThemeBase || (document.querySelector('link[rel="stylesheet"][href*="single-product-main.css"]') ? document.querySelector('link[rel="stylesheet"][href*="single-product-main.css"]').href.replace(/\/css\/single-product-main\.css.*/, '') : (document.currentScript ? document.currentScript.src.replace(/\/js\/single-product-main\.js.*/, '') : '')) ) + '/js/single-product/features.js';
            fscript.defer = true;
            document.body.appendChild(fscript);
        }

        // ========== ADD TO CART FUNCTIONALITY ==========
        
        // دکمه سفارشی ما
        // var customButton = document.getElementById('zs-custom-add-to-cart');
        
        // if (customButton) {
        //     customButton.addEventListener('click', function() {
        //         zsAddToCartHandler();
        //     });
        // }
        
        // // اگر محصول متغیر است، روی تغییرات attributeها گوش دهیم
        // if (window.zsProductData && window.zsProductData.isVariable) {
        //     var variationSelects = document.querySelectorAll('.variations select');
        //     variationSelects.forEach(function(select) {
        //         select.addEventListener('change', function() {
        //             checkVariationSelection();
        //         });
        //     });
            
        //     // بررسی اولیه وضعیت انتخاب
        //     setTimeout(checkVariationSelection, 500);
        // }
    });
})();

// ========== ADD TO CART FUNCTIONS ==========

/**
 * تابع اصلی افزودن به سبد خرید
 */
function zsAddToCartHandler() {
    // ابتدا چک کنید که آیا محصول متغیر است یا ساده
    const isVariable = window.zsProductData?.isVariable === true;
    
    if (isVariable) {
        handleVariableProduct();
    } else {
        handleSimpleProduct();
    }
}

/**
 * مدیریت محصولات ساده
 */
function handleSimpleProduct() {
    console.log('مدیریت محصول ساده...');
    
    const productId = getSimpleProductId();
    if (!productId) {
        zsShowToast('شناسه محصول پیدا نشد');
        return;
    }
    
    const quantity = getQuantity();
    
    // ارسال درخواست ایجکس
    addToCartAjax(productId, quantity);
}

/**
 * مدیریت محصولات متغیر
 */
function handleVariableProduct() {
    console.log('مدیریت محصول متغیر...');
    
    // استفاده از فرم اصلی ووکامرس
    const form = document.querySelector('form.variations_form');
    if (!form) {
        zsShowToast('فرم محصول پیدا نشد');
        return;
    }

    const variationIdInput = form.querySelector('input[name="variation_id"]');
    if (!variationIdInput || !variationIdInput.value || variationIdInput.value === '0') {
        zsShowToast('لطفاً گزینه‌های محصول را انتخاب کنید');
        highlightVariationSelects();
        return;
    }

    // استفاده از داده‌های فرم ووکامرس
    const formData = new FormData(form);
    
    // اضافه کردن داده‌های ضروری
    formData.append('add-to-cart', window.zsProductData?.productId || '');
    formData.append('quantity', getQuantity());
    
    // ارسال درخواست ایجکس
    addToCartAjaxViaForm(formData);
}

/**
 * روش جایگزین برای محصولات متغیر - استفاده از فرم اصلی
 */
function addToCartAjaxViaForm(formData) {
    const ajaxUrl = wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart');

    // نمایش لودینگ
    const addToCartBtn = document.getElementById('zs-custom-add-to-cart');
    const originalText = addToCartBtn?.querySelector('span')?.textContent;
    
    if (addToCartBtn) {
        addToCartBtn.disabled = true;
        if (originalText) {
            addToCartBtn.querySelector('span').textContent = 'در حال افزودن...';
        }
    }

    // ارسال درخواست با داده‌های دقیق فرم
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        // بازگرداندن وضعیت دکمه
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            if (originalText) {
                addToCartBtn.querySelector('span').textContent = originalText;
            }
        }

        console.log('پاسخ ووکامرس:', data);
        
        if (data.error && data.product_url) {
            // اگر ووکامرس می‌خواهد ما را به صفحه دیگری هدایت کند
            zsShowToast('لطفاً گزینه‌ها را دوباره بررسی کنید');
            console.error('خطای ووکامرس:', data);
            return;
        }

        if (data.error) {
            zsShowToast(data.error_message || 'خطا در افزودن به سبد خرید');
            console.error('خطای ووکامرس:', data);
            return;
        }

        if (data.fragments) {
            updateCartFragments(data);
            zsShowToast('✅ محصول با موفقیت به سبد خرید اضافه شد');
            triggerCartUpdatedEvent(data);
        } else {
            // اگر fragments وجود نداشت، باز هم سبد خرید را به‌روزرسانی کن
            zsShowToast('✅ محصول اضافه شد');
            refreshCartFragments();
        }
    })
    .catch(err => {
        console.error('خطای شبکه:', err);
        zsShowToast('خطا در ارتباط با سرور');
        
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            if (originalText) {
                addToCartBtn.querySelector('span').textContent = originalText;
            }
        }
    });
}

/**
 * تابع AJAX برای افزودن به سبد خرید (برای محصولات ساده)
 */
function addToCartAjax(productId, quantity, variationAttributes = null) {
    const ajaxUrl = wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart');
    const formData = new FormData();

    if (variationAttributes) {
        // محصول متغیر - روش قبلی
        const parentProductId = window.zsProductData?.productId || productId;

        formData.append('product_id', parentProductId);
        formData.append('variation_id', productId);
        formData.append('quantity', quantity);
        formData.append('add-to-cart', parentProductId);

        // اضافه کردن attributes با فرمت صحیح
        for (const attr in variationAttributes) {
            if (variationAttributes.hasOwnProperty(attr)) {
                // حذف "attribute_" از ابتدای نام اگر وجود دارد
                const cleanAttrName = attr.replace(/^attribute_/, '');
                formData.append(`attribute_${cleanAttrName}`, variationAttributes[attr]);
            }
        }
    } else {
        // محصول ساده
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('add-to-cart', productId);
    }

    // نمایش لودینگ
    const addToCartBtn = document.getElementById('zs-custom-add-to-cart');
    const originalText = addToCartBtn?.querySelector('span')?.textContent;
    
    if (addToCartBtn) {
        addToCartBtn.disabled = true;
        if (originalText) {
            addToCartBtn.querySelector('span').textContent = 'در حال افزودن...';
        }
    }

    // ارسال درخواست
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        // بازگرداندن وضعیت دکمه
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            if (originalText) {
                addToCartBtn.querySelector('span').textContent = originalText;
            }
        }

        console.log('پاسخ ووکامرس:', data);
        
        if (data.error && data.product_url) {
            zsShowToast('لطفاً گزینه‌ها را دوباره انتخاب کنید');
            console.error('خطای ووکامرس:', data);
            return;
        }

        if (data.error) {
            zsShowToast(data.error_message || 'خطا در افزودن به سبد خرید');
            console.error('خطای ووکامرس:', data);
            return;
        }

        if (data.fragments) {
            updateCartFragments(data);
            zsShowToast('✅ محصول با موفقیت به سبد خرید اضافه شد');
            triggerCartUpdatedEvent(data);
        } else {
            zsShowToast('✅ محصول اضافه شد');
            refreshCartFragments();
        }
    })
    .catch(err => {
        console.error('خطای شبکه:', err);
        zsShowToast('خطا در ارتباط با سرور');
        
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            if (originalText) {
                addToCartBtn.querySelector('span').textContent = originalText;
            }
        }
    });
}

/**
 * به‌روزرسانی fragments سبد خرید
 */
function updateCartFragments(data) {
    if (!data || !data.fragments) return;
    
    console.log('به‌روزرسانی fragments:', data.fragments);
    
    // به‌روزرسانی قسمت‌های مختلف
    for (const key in data.fragments) {
        if (data.fragments.hasOwnProperty(key)) {
            const element = document.querySelector(key);
            if (element) {
                element.innerHTML = data.fragments[key];
            }
        }
    }
}

/**
 * به‌روزرسانی دستی fragments در صورت نیاز
 */
function refreshCartFragments() {
    if (typeof jQuery !== 'undefined') {
        // درخواست به‌روزرسانی fragments از ووکامرس
        jQuery('body').trigger('wc_fragment_refresh');
    }
    
    // همچنین می‌توانیم مستقیماً درخواست بدهیم
    const ajaxUrl = wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'get_refreshed_fragments');
    
    fetch(ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if (data.fragments) {
            updateCartFragments(data);
        }
    });
}

/**
 * دریافت شناسه محصول ساده
 */
function getSimpleProductId() {
    let productId = null;
    
    // از فیلد hidden
    const hiddenField = document.getElementById('zs-current-product-id');
    if (hiddenField && hiddenField.value) {
        productId = hiddenField.value;
    }
    
    // از window.zsProductData
    if (!productId && window.zsProductData && window.zsProductData.productId) {
        productId = window.zsProductData.productId;
    }
    
    // از input اصلی ووکامرس
    if (!productId) {
        const addToCartInput = document.querySelector('input[name="add-to-cart"]');
        if (addToCartInput && addToCartInput.value) {
            productId = addToCartInput.value;
        }
    }
    
    console.log('شناسه محصول ساده:', productId);
    return productId;
}

/**
 * دریافت مقدار quantity
 */
function getQuantity() {
    let quantity = 1;
    
    const quantityInput = document.querySelector('input[name="quantity"], input.qty');
    if (quantityInput && quantityInput.value) {
        quantity = parseInt(quantityInput.value);
        if (isNaN(quantity) || quantity < 1) quantity = 1;
    }
    
    return quantity;
}

/**
 * هایلایت کردن selectهای واریاسیون
 */
function highlightVariationSelects() {
    const selects = document.querySelectorAll('.variations select');
    selects.forEach(function(select) {
        if (!select.value) {
            select.style.borderColor = 'red';
            select.style.boxShadow = '0 0 5px rgba(255, 0, 0, 0.3)';
            
            setTimeout(function() {
                select.style.borderColor = '';
                select.style.boxShadow = '';
            }, 2000);
        }
    });
}

/**
 * نمایش toast
 */
function zsShowToast(message) {
    // ایجاد یا پیدا کردن toast container
    let toastContainer = document.getElementById('zs-toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'zs-toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999999;
            max-width: 350px;
        `;
        document.body.appendChild(toastContainer);
    }
    
    // ایجاد toast
    const toast = document.createElement('div');
    toast.style.cssText = `
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        animation: zsFadeIn 0.3s, zsFadeOut 0.3s 2.7s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    // آیکون مناسب بر اساس نوع پیام
    let icon = 'ℹ️';
    if (message.includes('✅') || message.includes('موفق')) {
        icon = '✅';
        toast.style.background = 'linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)';
    } else if (message.includes('خطا') || message.includes('❌')) {
        icon = '❌';
        toast.style.background = 'linear-gradient(135deg, #f44336 0%, #c62828 100%)';
    }
    
    toast.innerHTML = `<span>${icon}</span><span>${message.replace(/[✅❌]/g, '')}</span>`;
    
    toastContainer.appendChild(toast);
    
    // حذف toast بعد از 3 ثانیه
    setTimeout(() => {
        if (toast.parentNode === toastContainer) {
            toastContainer.removeChild(toast);
        }
    }, 3000);
}

/**
 * ارسال event برای به‌روزرسانی سبد خرید
 */
function triggerCartUpdatedEvent(data) {
    // ارسال event jQuery برای ووکامرس
    if (typeof jQuery !== 'undefined') {
        jQuery('body').trigger('added_to_cart', [data.fragments, data.cart_hash]);
        jQuery('body').trigger('wc_fragment_refresh');
    }
    
    // ارسال event custom
    const event = new CustomEvent('zs_cart_updated', { detail: data });
    document.dispatchEvent(event);
    
    // ارسال event برای به‌روزرسانی minicart
    const miniCartEvent = new CustomEvent('update_mini_cart');
    document.dispatchEvent(miniCartEvent);
}

// ========== INITIALIZATION ==========

// اضافه کردن استایل‌های CSS برای انیمیشن
if (!document.querySelector('#zs-toast-styles')) {
    const style = document.createElement('style');
    style.id = 'zs-toast-styles';
    style.textContent = `
        @keyframes zsFadeIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        @keyframes zsFadeOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-10px) scale(0.9); }
        }
    `;
    document.head.appendChild(style);
}

// event listener برای دکمه
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('zs-custom-add-to-cart');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            zsAddToCartHandler();
        });
    }
    
    // disable کردن دکمه برای محصولات متغیر تا زمانی که واریاسیون انتخاب نشده
    if (window.zsProductData?.isVariable) {
        checkVariationSelection();
        
        // گوش دادن به تغییرات در selectهای واریاسیون
        document.querySelectorAll('.variations select').forEach(select => {
            select.addEventListener('change', checkVariationSelection);
        });
        
        // همچنین به تغییرات واریاسیون گوش دهیم
        if (typeof jQuery !== 'undefined') {
            jQuery('.variations_form').on('found_variation', function() {
                setTimeout(checkVariationSelection, 100);
            });
            jQuery('.variations_form').on('reset_data', function() {
                setTimeout(checkVariationSelection, 100);
            });
        }
    }
});

/**
 * بررسی وضعیت انتخاب واریاسیون
 */
function checkVariationSelection() {
    const variationIdInput = document.querySelector('input[name="variation_id"]');
    const addToCartBtn = document.getElementById('zs-custom-add-to-cart');
    
    if (variationIdInput && variationIdInput.value && variationIdInput.value !== '0' && addToCartBtn) {
        // واریاسیون انتخاب شده
        addToCartBtn.disabled = false;
        addToCartBtn.style.opacity = '1';
        addToCartBtn.style.cursor = 'pointer';
    } else if (addToCartBtn && window.zsProductData?.isVariable) {
        // واریاسیون انتخاب نشده
        addToCartBtn.disabled = true;
        addToCartBtn.style.opacity = '0.5';
        addToCartBtn.style.cursor = 'not-allowed';
    }
}

// بررسی وجود پارامترهای ووکامرس
if (typeof wc_add_to_cart_params === 'undefined') {
    console.warn('wc_add_to_cart_params not found, trying to find alternative');
    
    // تلاش برای پیدا کردن URL ایجکس ووکامرس
    const ajaxUrl = document.querySelector('link[rel="https://api.w.org/"]')?.getAttribute('href');
    if (ajaxUrl) {
        window.wc_add_to_cart_params = {
            wc_ajax_url: ajaxUrl + 'wp-json/wc/v3/' // این ممکن است نیاز به تنظیم داشته باشد
        };
    } else {
        // استفاده از URL پیش‌فرض
        window.wc_add_to_cart_params = {
            wc_ajax_url: '/?wc-ajax=%%endpoint%%'
        };
    }
}

// Share Product Function
function zsShareProduct() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: 'این محصول را ببینید',
            url: window.location.href
        }).catch(function(err) {
            console.log('Error sharing:', err);
            zsFallbackShare();
        });
    } else {
        zsFallbackShare();
    }
}

// Fallback share function
function zsFallbackShare() {
    var url = window.location.href;
    var title = document.title;
    
    // Create share modal
    var modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    var content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        max-width: 400px;
        width: 90%;
    `;
    
    content.innerHTML = `
        <h3 style="margin: 0 0 20px 0; color: #333;">اشتراک‌گذاری</h3>
        <p style="margin: 0 0 20px 0; color: #666;">لینک محصول:</p>
        <input type="text" value="${url}" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="zsCopyToClipboard('${url}')" style="padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer;">کپی لینک</button>
            <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #ccc; color: #333; border: none; border-radius: 5px; cursor: pointer;">بستن</button>
        </div>
    `;
    
    modal.appendChild(content);
    modal.className = 'modal';
    document.body.appendChild(modal);
    
    // Close on click outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Copy to clipboard function
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

/**
 * نمایش پیام toast
 */
function zsShowToast(message, isError = false) {
    // حذف toast قبلی
    var oldToast = document.querySelector('.zs-toast');
    if (oldToast) oldToast.remove();
    
    // ایجاد toast جدید
    var toast = document.createElement('div');
    toast.className = 'zs-toast';
    toast.textContent = message;
    
    // استایل‌دهی
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${isError ? '#ff4444' : '#4CAF50'};
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 99999;
        font-family: Tahoma, sans-serif;
        animation: toastSlideIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // حذف خودکار بعد از 3 ثانیه
    setTimeout(function() {
        toast.style.animation = 'toastSlideOut 0.3s ease';
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
    
    // اضافه کردن استایل‌های انیمیشن
    if (!document.querySelector('#zs-toast-animations')) {
        var style = document.createElement('style');
        style.id = 'zs-toast-animations';
        style.textContent = `
            @keyframes toastSlideIn {
                from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
                to { opacity: 1; transform: translateX(-50%) translateY(0); }
            }
            @keyframes toastSlideOut {
                from { opacity: 1; transform: translateX(-50%) translateY(0); }
                to { opacity: 0; transform: translateX(-50%) translateY(-20px); }
            }
        `;
        document.head.appendChild(style);
    }
}

// Add toast animations (legacy function - use zsShowToast instead)
var toastStyle = document.createElement('style');
toastStyle.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(toastStyle);

// Purchase Confirmation Modal Functions
function zsShowPurchaseConfirmation() {
    var modal = document.getElementById('zs-purchase-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Animate modal
        setTimeout(function() {
            modal.style.opacity = '1';
        }, 10);
    }
}

function zsClosePurchaseModal() {
    var modal = document.getElementById('zs-purchase-modal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

// Close modal on overlay click and escape key
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('zs-modal-overlay')) {
        zsClosePurchaseModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        zsClosePurchaseModal();
    }
});

// Floating Order Button - Sticky functionality
(function() {
    var floatingBtn = document.getElementById('zs-floating-order-btn');
    if (!floatingBtn) return;
    
    var orderSection = document.querySelector('.zs-enhanced-cart-section, .zs-product-hero-section');
    var mainOrderButton = document.querySelector('#place_order, button[name="woocommerce_checkout_place_order"], .single_add_to_cart_button, button[type="submit"].single_add_to_cart_button');
    var lastScrollTop = 0;
    var isScrollingDown = false;
    var scrollThreshold = 300; // Show button after scrolling 300px
    
    function isElementInViewport(el) {
        if (!el) return false;
        var rect = el.getBoundingClientRect();
        var windowHeight = window.innerHeight || document.documentElement.clientHeight;
        var windowWidth = window.innerWidth || document.documentElement.clientWidth;
        
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= windowHeight &&
            rect.right <= windowWidth
        );
    }
    
    function isElementPartiallyInViewport(el) {
        if (!el) return false;
        var rect = el.getBoundingClientRect();
        var windowHeight = window.innerHeight || document.documentElement.clientHeight;
        var windowWidth = window.innerWidth || document.documentElement.clientWidth;
        
        return (
            rect.top < windowHeight &&
            rect.bottom > 0 &&
            rect.left < windowWidth &&
            rect.right > 0
        );
    }
    
    function updateFloatingButton() {
        if (!floatingBtn) return;
        
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var windowHeight = window.innerHeight;
        var documentHeight = document.documentElement.scrollHeight;
        
        // Check if scrolled past threshold
        var shouldShow = scrollTop > scrollThreshold;
        
        // Check if near bottom (hide near footer)
        var nearBottom = (scrollTop + windowHeight) >= (documentHeight - 100);
        
        // Check if main order button is visible in viewport
        var mainButtonVisible = false;
        if (mainOrderButton) {
            mainButtonVisible = isElementPartiallyInViewport(mainOrderButton);
        }
        
        // Check if order section is visible
        var orderSectionVisible = false;
        if (orderSection) {
            var rect = orderSection.getBoundingClientRect();
            orderSectionVisible = rect.top >= 0 && rect.top <= windowHeight;
        }
        
        // Determine if should show button
        // Hide if main button is visible OR if order section is visible OR near bottom
        if (shouldShow && !nearBottom && !orderSectionVisible && !mainButtonVisible) {
            if (!floatingBtn.classList.contains('show')) {
                floatingBtn.style.display = 'flex';
                setTimeout(function() {
                    floatingBtn.classList.add('show');
                    floatingBtn.classList.remove('hide');
                }, 10);
            }
        } else {
            if (floatingBtn.classList.contains('show')) {
                floatingBtn.classList.remove('show');
                floatingBtn.classList.add('hide');
                setTimeout(function() {
                    if (floatingBtn.classList.contains('hide')) {
                        floatingBtn.style.display = 'none';
                    }
                }, 300);
            }
        }
        
        lastScrollTop = scrollTop;
    }
    
    // Throttle scroll event
    var scrollTimer = null;
    window.addEventListener('scroll', function() {
        if (scrollTimer !== null) {
            clearTimeout(scrollTimer);
        }
        scrollTimer = setTimeout(updateFloatingButton, 10);
    }, { passive: true });
    
    // Initial check
    setTimeout(updateFloatingButton, 500);
    
    // Update on resize
    window.addEventListener('resize', function() {
        updateFloatingButton();
    });
    
    // Re-check when content changes (WooCommerce AJAX)
    var observer = new MutationObserver(function() {
        updateFloatingButton();
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();

// Related products carousel
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper === 'undefined') return;
    var relatedEl = document.getElementById('relatedProductsCarousel');
    if (relatedEl) {
        new Swiper(relatedEl, {
            slidesPerView: 4,
            spaceBetween: 16,
            navigation: {
                nextEl: '.zs-related-next',
                prevEl: '.zs-related-prev',
            },
            breakpoints: {
                0: { slidesPerView: 2, spaceBetween: 10 },
                480: { slidesPerView: 2, spaceBetween: 12 },
                768: { slidesPerView: 3, spaceBetween: 14 },
                1024: { slidesPerView: 4, spaceBetween: 16 }
            }
        });
    }
});

// Scroll to Section Function
window.zsScrollToSection = function(sectionId) {
    var section = document.getElementById(sectionId);
    if (!section) {
        // Try to find by class
        var sections = {
            'order': '.zs-enhanced-cart-section',
            'faq': '.zs-faq-section',
            'reviews': '.zs-reviews-section',
            'trust': '.zs-trust-section',
            'description': '.zs-description-section',
            'related-products': '.zs-related-products-section'
        };
        
        if (sections[sectionId]) {
            section = document.querySelector(sections[sectionId]);
        }
    }
    
    if (section) {
        var offset = 80; // Header offset
        var elementPosition = section.getBoundingClientRect().top;
        var offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
};

// Desktop Sidebar Navigation
(function() {
    if (window.innerWidth <= 768) return; // Only for desktop
    
    var sidebarNav = document.getElementById('zs-sidebar-nav');
    if (!sidebarNav) return;
    
    var navLinks = sidebarNav.querySelectorAll('.zs-nav-link');
    var sections = [];
    var lastScrollTop = 0;
    var scrollTimeout = null;
    var isScrolling = false;
    
    // Get all sections
    navLinks.forEach(function(link) {
        var sectionId = link.getAttribute('data-section');
        var sectionElement = null;
        
        // Map section IDs to selectors
        var sectionSelectors = {
            'order': '.zs-enhanced-cart-section',
            'trust': '.zs-trust-section',
            'description': '.zs-description-section',
            'faq': '.zs-faq-section',
            'related-products': '.zs-related-products-section',
            'reviews': '.zs-reviews-section'
        };
        
        if (sectionSelectors[sectionId]) {
            sectionElement = document.querySelector(sectionSelectors[sectionId]);
        }
        
        if (sectionElement) {
            sections.push({
                id: sectionId,
                element: sectionElement,
                link: link
            });
        }
    });
    
    // Reorder nav items based on actual section order in DOM
    (function reorderNavBySectionPosition(){
        try {
            var list = sidebarNav.querySelector('.zs-sidebar-nav-list');
            if (!list) return;
            var ordered = sections
                .map(function(s){
                    return {
                        top: s.element.getBoundingClientRect().top + window.pageYOffset,
                        link: s.link
                    };
                })
                .sort(function(a,b){ return a.top - b.top; });
            ordered.forEach(function(item){
                var li = item.link.closest('li');
                if (li) list.appendChild(li);
            });
        } catch(e) { /* ignore */ }
    })();

    // Smooth scroll on click
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var sectionId = this.getAttribute('data-section');
            zsScrollToSection(sectionId);
        });
    });
    
    // Update active section
    function updateActiveSection() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var windowHeight = window.innerHeight;
        var currentSection = null;
        
        // Find current section in viewport
        for (var i = sections.length - 1; i >= 0; i--) {
            var section = sections[i];
            var rect = section.element.getBoundingClientRect();
            
            if (rect.top <= windowHeight * 0.3 && rect.bottom >= windowHeight * 0.3) {
                currentSection = section;
                break;
            }
        }
        
        // If no section found, check which one is closest to top
        if (!currentSection) {
            for (var i = 0; i < sections.length; i++) {
                var section = sections[i];
                var rect = section.element.getBoundingClientRect();
                
                if (rect.top <= windowHeight && rect.bottom > 0) {
                    currentSection = section;
                    break;
                }
            }
        }
        
        // Update active link
        navLinks.forEach(function(link) {
            link.classList.remove('active');
        });
        
        if (currentSection) {
            currentSection.link.classList.add('active');
        }
    }
    
    // Always keep sidebar visible on desktop
    function handleSidebarVisibility() {
        sidebarNav.classList.add('show');
        sidebarNav.classList.remove('hide');
        lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
    }
    
    // Initial check
    setTimeout(function() {
        updateActiveSection();
        handleSidebarVisibility();
    }, 500);
    
    // Throttle scroll events
    var scrollTimer = null;
    window.addEventListener('scroll', function() {
        if (scrollTimer !== null) {
            clearTimeout(scrollTimer);
        }
        scrollTimer = setTimeout(function() {
            updateActiveSection();
            handleSidebarVisibility();
        }, 10);
    }, { passive: true });
    
    // Update on resize
    window.addEventListener('resize', function() {
        updateActiveSection();
    });
})();

// Gallery thumbnail click handler
(function() {
    if (typeof document === 'undefined') return;
    
    document.addEventListener('DOMContentLoaded', function() {
        var thumbnails = document.querySelectorAll('.zs-thumbnail-item');
        var mainImage = document.querySelector('.zs-main-image');
        
        if (thumbnails.length > 0 && mainImage) {
            thumbnails.forEach(function(thumb) {
                thumb.addEventListener('click', function() {
                    var imageUrl = this.getAttribute('data-image');
                    if (imageUrl) {
                        mainImage.src = imageUrl;
                        mainImage.setAttribute('data-full', imageUrl);
                        
                        thumbnails.forEach(function(t) {
                            t.classList.remove('active');
                        });
                        this.classList.add('active');
                    }
                });
            });
        }
    });
})();