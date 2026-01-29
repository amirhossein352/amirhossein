/**
 * Single Product Page - Features & Animations JavaScript
 * 
 * این فایل شامل عملکردهای JavaScript مربوط به بخش ویژگی‌ها و انیمیشن‌ها است:
 * - انیمیشن‌های hover برای کارت‌های ویژگی
 * - Intersection Observer برای انیمیشن ورود
 * - انیمیشن‌های FAQ accordion
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
        
        // CTA button animations
        var ctaButtons = root.querySelectorAll('.zs-cta-btn');
        ctaButtons.forEach(function(btn) {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Trust section item animations
        var trustItems = root.querySelectorAll('.trust-item');
        trustItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Portfolio card animations
        var portfolioCards = root.querySelectorAll('.zs-portfolio-card');
        portfolioCards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Counter animations (if counters exist)
        var counters = root.querySelectorAll('.zs-counter');
        if (counters.length > 0) {
            var counterObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var counter = entry.target;
                        var valueElement = counter.querySelector('.zs-counter-value');
                        if (valueElement) {
                            var finalValue = valueElement.textContent;
                            var numericValue = parseInt(finalValue.replace(/\D/g, ''));
                            
                            if (!isNaN(numericValue)) {
                                var currentValue = 0;
                                var increment = numericValue / 50;
                                var timer = setInterval(function() {
                                    currentValue += increment;
                                    if (currentValue >= numericValue) {
                                        currentValue = numericValue;
                                        clearInterval(timer);
                                    }
                                    valueElement.textContent = Math.floor(currentValue) + finalValue.replace(/\d/g, '').replace(/\d/, '');
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
            }
        });
    });
})();
