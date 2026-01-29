/**
 * Single Product Page - Related Products & Final CTA JavaScript
 * 
 * این فایل شامل عملکردهای JavaScript مربوط به بخش‌های نهایی صفحه تک محصول است:
 * - انیمیشن‌های hover برای کارت‌های محصولات مرتبط
 * - عملکردهای تعاملی CTA نهایی
 * - انیمیشن‌های ورود با AOS
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
        // Related products card animations
        var relatedCards = root.querySelectorAll('.zs-related-card');
        relatedCards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Final CTA button animations
        var finalCtaButtons = root.querySelectorAll('.zs-final-order-btn, .zs-browse-more-btn');
        finalCtaButtons.forEach(function(btn) {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Shop link animation
        var shopLink = root.querySelector('.zs-shop-link');
        if (shopLink) {
            shopLink.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            shopLink.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
        
        // Add click animation to all buttons
        var allButtons = root.querySelectorAll('.zs-final-order-btn, .zs-browse-more-btn, .zs-shop-link');
        allButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(function() {
                    btn.style.transform = '';
                }, 150);
            });
        });
        
        // Intersection Observer for related products animation
        if ('IntersectionObserver' in window) {
            var relatedElements = root.querySelectorAll('.zs-related-card, .zs-no-related');
            var relatedObserver = new IntersectionObserver(function(entries) {
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
            
            relatedElements.forEach(function(element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.6s ease-out';
                relatedObserver.observe(element);
            });
        }
        
        // Add loading animation to related product images
        var relatedImages = root.querySelectorAll('.zs-related-card img');
        relatedImages.forEach(function(img) {
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
        
        // Smooth scroll to top when clicking final order button
        var finalOrderBtn = root.querySelector('.zs-final-order-btn');
        if (finalOrderBtn) {
            finalOrderBtn.addEventListener('click', function() {
                // Scroll to top smoothly after a short delay
                setTimeout(function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 100);
            });
        }
        
        // Add ripple effect to final CTA buttons
        var ctaButtons = root.querySelectorAll('.zs-final-order-btn, .zs-browse-more-btn');
        ctaButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
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
                    background: rgba(255, 255, 255, 0.3);
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
        
        // Add parallax effect to final CTA section
        var finalCtaSection = root.querySelector('.zs-final-cta-section');
        if (finalCtaSection) {
            window.addEventListener('scroll', function() {
                var scrolled = window.pageYOffset;
                var rate = scrolled * -0.5;
                finalCtaSection.style.transform = 'translateY(' + rate + 'px)';
            });
        }
        
        // Add typing animation to final CTA title
        var finalCtaTitle = root.querySelector('.zs-final-cta-title');
        if (finalCtaTitle) {
            var originalText = finalCtaTitle.textContent;
            var words = originalText.split(' ');
            var currentWordIndex = 0;
            
            finalCtaTitle.textContent = '';
            
            function typeWord() {
                if (currentWordIndex < words.length) {
                    finalCtaTitle.textContent += words[currentWordIndex] + ' ';
                    currentWordIndex++;
                    setTimeout(typeWord, 200);
                }
            }
            
            // Start typing animation when section comes into view
            var ctaObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        setTimeout(typeWord, 500);
                        ctaObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            ctaObserver.observe(finalCtaSection);
        }
    });
})();
