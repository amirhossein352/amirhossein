// Dashboard Page JavaScript - اسکریپت صفحه پیشخوان

// Initialize when DOM is ready
function initDashboardPage() {
    initDashboard();
    
    // Try to initialize carousel immediately
    initProductsCarousel();
    
    // If carousel elements don't exist yet, wait for them
    if (!document.querySelector('.products-track')) {
        // Use MutationObserver to wait for carousel elements
        const observer = new MutationObserver(function(mutations, obs) {
            const carouselTrack = document.querySelector('.products-track');
            if (carouselTrack) {
                initProductsCarousel();
                obs.disconnect(); // Stop observing once found
            }
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Also try after a delay as fallback
        setTimeout(function() {
            if (document.querySelector('.products-track')) {
                initProductsCarousel();
            }
            observer.disconnect();
        }, 1000);
    }
}

// Run on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardPage);
} else {
    // DOM is already ready
    initDashboardPage();
}

// Also run when page becomes visible (for dynamic loading)
document.addEventListener('DOMContentLoaded', function() {
    // Re-initialize carousel when dashboard page is shown
    const dashboardPage = document.getElementById('dashboard');
    if (dashboardPage) {
        const pageObserver = new MutationObserver(function() {
            if (!dashboardPage.classList.contains('hidden')) {
                setTimeout(function() {
                    initProductsCarousel();
                }, 100);
            }
        });
        
        pageObserver.observe(dashboardPage, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});

function initDashboard() {
    // Handle quick action clicks
    const quickActions = document.querySelectorAll('.quick-action-item[data-page]');
    quickActions.forEach(action => {
        action.addEventListener('click', function(e) {
            e.preventDefault();
            const targetPage = this.getAttribute('data-page');
            navigateToPage(targetPage);
        });
    });
    
    // Handle order view buttons
    const viewOrderButtons = document.querySelectorAll('.view-order');
    viewOrderButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order');
            viewOrder(orderId);
        });
    });
    
    // Handle order link clicks
    const orderLinks = document.querySelectorAll('.order-link');
    orderLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToPage('orders');
        });
    });
    
    // Load recent orders (if needed)
    loadRecentOrders();
}

// Products Carousel
function initProductsCarousel() {
    const carouselTrack = document.querySelector('.products-track');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    
    if (!carouselTrack || !prevBtn || !nextBtn) {
        return;
    }
    
    // Prevent multiple initializations
    if (carouselTrack.dataset.initialized === 'true') {
        return;
    }
    carouselTrack.dataset.initialized = 'true';
    
    let currentIndex = 0;
    let slideWidth = 0;
    let slidesToShow = 0;
    let totalSlides = carouselTrack.children.length;
    
    function updateCarousel() {
        const container = carouselTrack.parentElement;
        if (!container) return;
        
        const containerWidth = container.offsetWidth;
        
        // Calculate slides to show based on container width
        if (containerWidth >= 1200) {
            slidesToShow = 5;
            slideWidth = (containerWidth - (4 * 20)) / 5; // 4 gaps of 20px (1.25rem)
        } else if (containerWidth >= 992) {
            slidesToShow = 4;
            slideWidth = (containerWidth - (3 * 20)) / 4;
        } else if (containerWidth >= 768) {
            slidesToShow = 3;
            slideWidth = (containerWidth - (2 * 20)) / 3;
        } else if (containerWidth >= 480) {
            slidesToShow = 2;
            slideWidth = (containerWidth - 20) / 2;
        } else {
            slidesToShow = 1;
            slideWidth = containerWidth;
        }
        
        // Update slide widths
        const slides = carouselTrack.querySelectorAll('.product-slide');
        slides.forEach(slide => {
            slide.style.width = slideWidth + 'px';
        });
        
        // Update buttons state
        const currentPrevBtn = document.querySelector('.carousel-prev');
        const currentNextBtn = document.querySelector('.carousel-next');
        if (currentPrevBtn) currentPrevBtn.disabled = currentIndex === 0;
        if (currentNextBtn) currentNextBtn.disabled = currentIndex >= totalSlides - slidesToShow;
        
        // Update track position
        updateTrackPosition();
    }
    
    function updateTrackPosition() {
        const offset = -currentIndex * (slideWidth + 20); // 20px gap
        carouselTrack.style.transform = `translateX(${offset}px)`;
    }
    
    function nextSlide() {
        if (currentIndex < totalSlides - slidesToShow) {
            currentIndex++;
            updateCarousel();
        }
    }
    
    function prevSlide() {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    }
    
    // Remove existing event listeners by cloning buttons
    const newNextBtn = nextBtn.cloneNode(true);
    const newPrevBtn = prevBtn.cloneNode(true);
    nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
    prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
    
    // Event listeners
    newNextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        nextSlide();
    });
    newPrevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        prevSlide();
    });
    
    // Touch/swipe support
    let startX = 0;
    let currentX = 0;
    let isDragging = false;
    
    carouselTrack.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        isDragging = true;
    });
    
    carouselTrack.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
    });
    
    carouselTrack.addEventListener('touchend', function() {
        if (!isDragging) return;
        isDragging = false;
        
        const diff = startX - currentX;
        if (Math.abs(diff) > 50) { // Minimum swipe distance
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }
    });
    
    // Mouse drag support
    let mouseStartX = 0;
    let mouseCurrentX = 0;
    let isMouseDragging = false;
    
    carouselTrack.addEventListener('mousedown', function(e) {
        mouseStartX = e.clientX;
        isMouseDragging = true;
        carouselTrack.style.cursor = 'grabbing';
        e.preventDefault();
    });
    
    carouselTrack.addEventListener('mousemove', function(e) {
        if (!isMouseDragging) return;
        mouseCurrentX = e.clientX;
    });
    
    carouselTrack.addEventListener('mouseup', function() {
        if (!isMouseDragging) return;
        isMouseDragging = false;
        carouselTrack.style.cursor = 'grab';
        
        const diff = mouseStartX - mouseCurrentX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }
    });
    
    carouselTrack.addEventListener('mouseleave', function() {
        isMouseDragging = false;
        carouselTrack.style.cursor = 'grab';
    });
    
    // Initialize
    updateCarousel();
    
    // Update on resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            updateCarousel();
        }, 250);
    });
}

function navigateToPage(pageId) {
    const navLink = document.querySelector(`.nav-link[data-page="${pageId}"]`);
    if (navLink) {
        navLink.click();
    }
}

function viewOrder(orderId) {
    // Navigate to orders page and show specific order
    navigateToPage('orders');
    // TODO: Implement order detail view
}

function loadRecentOrders() {
    // TODO: Load recent orders from API
    // This is a placeholder for future API integration
}

// Export functions for use in other scripts
window.dashboardPage = {
    navigateToPage,
    viewOrder,
    loadRecentOrders,
    initProductsCarousel
};

