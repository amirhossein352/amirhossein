/**
 * Portfolio Section - Continuous Vertical Image Scroll on Hover
 */
(function(){
    if (typeof document === 'undefined') return;
    var d = document;

    function ready(fn){
        if (d.readyState === 'complete' || d.readyState === 'interactive') fn();
        else d.addEventListener('DOMContentLoaded', fn);
    }

    ready(function(){
        var section = d.querySelector('.zs-portfolio-section');
        if (!section) return;

        var items = section.querySelectorAll('.zs-portfolio-item');
        items.forEach(function(item){
            var container = item.querySelector('.zs-portfolio-image-container');
            var img = container ? container.querySelector('img') : null;
            if (!container || !img) return;

            var animId = null;
            var isHovering = false;
            var scrollPosition = 0;
            var scrollSpeed = 2.0;
            var scrollableDistance = 0;

            function calculateScroll(){
                var containerHeight = container.clientHeight;
                var containerWidth = container.clientWidth;
                var naturalWidth = img.naturalWidth;
                var naturalHeight = img.naturalHeight;

                if (!naturalWidth || !naturalHeight) {
                    return 0;
                }

                var renderedHeight = (containerWidth / naturalWidth) * naturalHeight;
                scrollableDistance = Math.max(0, renderedHeight - containerHeight);
                return scrollableDistance;
            }

            function animate(){
                if (!isHovering) {
                    animId = null;
                    return;
                }

                scrollPosition += scrollSpeed;

                if (scrollPosition >= scrollableDistance) {
                    scrollPosition = 0;
                }

                // Use setProperty with important to override CSS
                img.style.setProperty('transform', 'translateY(-' + scrollPosition + 'px)', 'important');
                img.style.setProperty('transition', 'none', 'important');
                
                animId = requestAnimationFrame(animate);
            }

            function startScroll(){
                var distance = calculateScroll();
                if (distance <= 0) {
                    return;
                }

                if (isHovering) return;
                
                isHovering = true;
                scrollPosition = 0;
                img.style.setProperty('transform', 'translateY(0)', 'important');
                img.style.setProperty('transition', 'none', 'important');
                
                if (!animId) {
                    animate();
                }
            }

            function stopScroll(){
                isHovering = false;
                if (animId) {
                    cancelAnimationFrame(animId);
                    animId = null;
                }
                img.style.setProperty('transition', 'transform 0.6s ease-out', 'important');
                img.style.setProperty('transform', 'translateY(0)', 'important');
                setTimeout(function(){
                    img.style.removeProperty('transition');
                }, 600);
            }

            // Wait for image to load
            if (img.complete && img.naturalHeight > 0) {
                calculateScroll();
            } else {
                img.addEventListener('load', function(){
                    setTimeout(function(){
                        calculateScroll();
                    }, 50);
                }, { once: true });
            }

            // Setup hover events
            item.addEventListener('mouseenter', function(){
                startScroll();
                
                var title = item.getAttribute('data-title') || '';
                if (title && !item.querySelector('.zs-portfolio-tooltip')) {
                    var tip = d.createElement('div');
                    tip.className = 'zs-portfolio-tooltip';
                    tip.textContent = title;
                    item.appendChild(tip);
                }
            });

            item.addEventListener('mouseleave', function(){
                stopScroll();
                var tip = item.querySelector('.zs-portfolio-tooltip');
                if (tip) tip.remove();
            });
        });
    });
})();
