// ============================================
// Navigation JavaScript - اسکریپت ناوبری
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    try {
        initNavigation();
    } catch (error) {
        // Error handled silently
    }
});

function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link[data-page]');
    
    if (!navLinks || navLinks.length === 0) {
        return;
    }
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            try {
                const targetPage = this.getAttribute('data-page');
                const href = this.getAttribute('href');
                
                // Validate href exists
                if (!href || href === '#' || href === '') {
                    e.preventDefault();
                    return false;
                }
                
                // Handle logout - show confirmation
                if (targetPage === 'logout') {
                    if (!confirm('آیا مطمئن هستید که می‌خواهید خارج شوید؟')) {
                        e.preventDefault();
                        return false;
                    }
                    // Allow normal navigation for logout
                    return true;
                }
                
                // Update active state visually (but don't prevent navigation)
                // Remove active class from all nav items
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked nav item
                const navItem = this.closest('.nav-item');
                if (navItem) {
                    navItem.classList.add('active');
                }
                
                // Close sidebar on mobile after navigation
                if (window.innerWidth <= 768) {
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) {
                        sidebar.classList.remove('active');
                    }
                }
                
                // IMPORTANT: Don't prevent default - let WordPress handle normal page navigation
                // This ensures the page loads correctly instead of showing white screen
                return true;
                
            } catch (error) {
                // Don't prevent navigation even on error - let WordPress handle it
                return true;
            }
        });
    });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

