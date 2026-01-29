/**
 * Front Page JavaScript Functionality
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // ========================================
    // FAQ Section System (Separate from Hero)
    // ========================================
    
    function initFAQSystem() {
        // Rotating border: small cards from top to bottom, then the large card, then loop
        const faqList = document.querySelector('.faq-cards-left');
        const faqCards = faqList ? Array.from(faqList.querySelectorAll('.faq-card-small')) : [];
        const faqLarge = document.querySelector('.faq-card-large');
        if (!faqCards.length || !faqLarge) return;

        const sequence = [...faqCards, faqLarge];
        let cycleIndex = 0;
        let cycleTimer; // interval handle
        let rafId;      // timer bar animation
        const durationMs = 3500; // per step

        function clearHighlights() {
            sequence.forEach(el => el.classList.remove('cycle-active'));
            // remove any timers
            sequence.forEach(el => {
                const t = el.querySelector('.cycle-timer');
                if (t) t.remove();
            });
        }

        function animateTimerBar(container) {
            const existing = container.querySelector('.cycle-ring');
            if (existing) existing.remove();
            const ring = document.createElement('div');
            ring.className = 'cycle-ring';
            ring.style.setProperty('--turn', 0);
            container.appendChild(ring);
            const start = performance.now();
            function tick(now) {
                const elapsed = Math.min(now - start, durationMs);
                const turn = (elapsed / durationMs);
                ring.style.setProperty('--turn', turn);
                if (elapsed < durationMs) {
                    rafId = requestAnimationFrame(tick);
                }
            }
            rafId = requestAnimationFrame(tick);
        }

        function applyStep() {
            clearHighlights();
            const el = sequence[cycleIndex];
            if (el) {
                el.classList.add('cycle-active');
                animateTimerBar(el);
            }
        }

        function nextStep() {
            cycleIndex = (cycleIndex + 1) % sequence.length;
            applyStep();
        }

        function startCycle() {
            if (cycleTimer) return;
            applyStep();
            cycleTimer = setInterval(nextStep, durationMs);
        }

        function stopCycle() {
            if (cycleTimer) { clearInterval(cycleTimer); cycleTimer = undefined; }
            if (rafId) { cancelAnimationFrame(rafId); rafId = undefined; }
        }

        startCycle();

        // Pause on hover over the FAQ list or the large card
        [faqList, faqLarge].forEach(el => {
            if (!el) return;
            el.addEventListener('mouseenter', stopCycle);
            el.addEventListener('mouseleave', startCycle);
        });
    }
    
    // ========================================
    // Smooth Scroll and Parallax Effects
    // ========================================
    
    function initSmoothEffects() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                // Skip if href is just "#" or empty
                if (!href || href === '#' || href.length <= 1) {
                    return;
                }
                
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ========================================
    // Solutions Accordion
    // ========================================
    function initSolutionsAccordion() {
        const solutionHeaders = document.querySelectorAll('.zs-solution-header');
        
        if (!solutionHeaders.length) return;
        
        solutionHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetContent = document.getElementById(targetId);
                const solutionItem = this.closest('.zs-solution-item');
                const icon = this.querySelector('.zs-solution-icon');
                
                // Close all other items
                document.querySelectorAll('.zs-solution-item').forEach(item => {
                    if (item !== solutionItem) {
                        item.classList.remove('active');
                        const otherIcon = item.querySelector('.zs-solution-icon');
                        if (otherIcon) {
                            otherIcon.textContent = '+';
                        }
                    }
                });
                
                // Toggle current item
                if (solutionItem.classList.contains('active')) {
                    solutionItem.classList.remove('active');
                    icon.textContent = '+';
                } else {
                    solutionItem.classList.add('active');
                    icon.textContent = '−';
                }
            });
        });
        
        console.log('Solutions accordion initialized successfully');
    }

    // ========================================
    // Product Showcase Sliders
    // ========================================
    function initProductShowcaseSliders() {
        const sliders = document.querySelectorAll('.product-showcase-slider');
        sliders.forEach(slider => {
            const track = slider.querySelector('.product-showcase-track');
            if (!track) {
                return;
            }
            const prev = slider.querySelector('.product-slider-nav.prev');
            const next = slider.querySelector('.product-slider-nav.next');
            const scrollCount = parseInt(slider.getAttribute('data-scroll') || '1', 10);

            function getStepSize() {
                const card = track.querySelector('.product-showcase-card');
                if (!card) {
                    return 0;
                }
                const style = window.getComputedStyle(track);
                const gap = parseFloat(style.columnGap || style.gap || 0);
                return card.getBoundingClientRect().width + gap;
            }

            function scroll(direction) {
                const step = getStepSize();
                if (!step) {
                    return;
                }
                track.scrollBy({
                    left: direction * step * scrollCount,
                    behavior: 'smooth',
                });
            }

            if (prev) {
                prev.addEventListener('click', () => scroll(-1));
            }
            if (next) {
                next.addEventListener('click', () => scroll(1));
            }
        });
    }
    
    // ========================================
    // Performance Optimizations
    // ========================================
    
    function optimizePerformance() {
        // Use requestAnimationFrame for smooth animations
        let ticking = false;
        
        function updateAnimations() {
            ticking = false;
            // Animation updates here
        }
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateAnimations);
                ticking = true;
            }
        }
        
        // Throttle scroll events
        window.addEventListener('scroll', requestTick, { passive: true });
    }
    
    // ========================================
    // Initialize All Functions
    // ========================================
    
    function init() {
        initFAQSystem();          // FAQ section
        initSmoothEffects();
        initSolutionsAccordion(); // Solutions accordion
        initProductShowcaseSliders();
        optimizePerformance();
        
        console.log('Front Page JavaScript initialized successfully!');
    }
    
    // Run initialization
    init();
    
    // Add CSS for FAQ animations
    const style = document.createElement('style');
    style.textContent = `
        .faq-card-small.active {
            transform: scale(1.02) !important;
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.2) !important;
            border: 1px solid var(--zs-teal-light, #6DE0D0) !important;
        }

        /* Rotating highlight for small FAQ cards */
        .faq-card-small.cycle-active {
            border: 1px solid var(--zs-teal-medium, #37C3B3) !important;
            box-shadow: inset 0 0 0 1px var(--zs-teal-medium, #37C3B3) !important,
                        0 10px 28px rgba(55, 195, 179, 0.25) !important;
            transform: translateY(-2px);
            transition: border-color .3s ease, box-shadow .3s ease, transform .3s ease;
        }
        .faq-card-large.cycle-active {
            border: 1px solid var(--zs-teal-medium, #37C3B3) !important;
            box-shadow: inset 0 0 0 1px var(--zs-teal-medium, #37C3B3) !important,
                        0 12px 34px rgba(55, 195, 179, 0.25) !important;
            transition: border-color .3s ease, box-shadow .3s ease;
        }
        .faq-card-small, .faq-card-large { position: relative; overflow: visible !important; }
        /* Circular ring timer hugging the rounded border */
        .cycle-ring {
            position: absolute; inset: -1px; border-radius: inherit; pointer-events: none; z-index: 1;
            /* Small moving arc around the border */
            background: conic-gradient(from calc(var(--turn, 0) * 1turn),
                                       var(--zs-teal-medium, #37C3B3) 0turn,
                                       var(--zs-teal-light, #6DE0D0) 0.06turn,
                                       transparent 0);
            /* Ring thickness ~1px */
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 1px), #000 0);
                    mask: radial-gradient(farthest-side, transparent calc(100% - 1px), #000 0);
        }
        /* Remove default border from large card as requested */
        .faq-card-large { border: none !important; }
        /* Ensure card content is above ring */
        .faq-card-small .faq-content, .faq-card-large .faq-content { position: relative; z-index: 2; }
    `;
    document.head.appendChild(style);
    
});
