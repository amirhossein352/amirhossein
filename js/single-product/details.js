/**
 * Single Product Page - Details & Interactions JavaScript
 * 
 * این فایل شامل عملکردهای JavaScript مربوط به بخش جزئیات محصول است:
 * - FAQ accordion بهبود یافته
 * - انیمیشن شمارش counters
 * - انیمیشن‌های hover برای portfolio cards
 * - عملکردهای تعاملی مختلف
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
        // Build TOC chips from h2/h3 inside description
        var desc = root.querySelector('.zs-description-text');
        if (desc) {
            var headings = desc.querySelectorAll('h2, h3');
            if (headings.length) {
                var toc = document.createElement('div');
                toc.className = 'zs-desc-toc';
                headings.forEach(function(h, idx){
                    if (!h.id) h.id = 'desc-h-' + (idx+1);
                    var chip = document.createElement('button');
                    chip.className = 'zs-desc-chip';
                    chip.type = 'button';
                    chip.textContent = h.textContent.trim().slice(0, 40);
                    chip.addEventListener('click', function(){
                        document.getElementById(h.id).scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    toc.appendChild(chip);
                });
                var header = root.querySelector('.zs-description-header');
                if (header && header.parentNode) {
                    header.parentNode.insertBefore(toc, header.nextSibling);
                }
                // Highlight active chip on scroll
                var chips = toc.querySelectorAll('.zs-desc-chip');
                var obs = new IntersectionObserver(function(entries){
                    entries.forEach(function(entry){
                        if (entry.isIntersecting) {
                            var id = entry.target.id;
                            chips.forEach(function(c){ c.removeAttribute('aria-current'); });
                            var idx = Array.prototype.indexOf.call(headings, entry.target);
                            if (chips[idx]) chips[idx].setAttribute('aria-current','true');
                        }
                    });
                }, { rootMargin: '-40% 0px -50% 0px', threshold: 0.01 });
                headings.forEach(function(h){ obs.observe(h); });
            }
        }
        // Enhanced FAQ accordion functionality
        var faqItems = root.querySelectorAll('.zs-faq-item');
        if (faqItems && faqItems.length) {
            faqItems.forEach(function(item){
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
        
        // Portfolio item animations
        var portfolioItems = root.querySelectorAll('.zs-portfolio-item');
        portfolioItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-12px)';
            });
            
            item.addEventListener('mouseleave', function() {
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
        
        // Smooth scroll for internal links
        var internalLinks = root.querySelectorAll('a[href^="#"]');
        internalLinks.forEach(function(link) {
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
    });
})();
