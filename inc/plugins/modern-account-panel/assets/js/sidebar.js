// ============================================
// Sidebar JavaScript - اسکریپت سایدبار
// ============================================

(function() {
    'use strict';
    
    let sidebarInitialized = false;
    
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (!sidebar) {
            return false;
        }
        
        if (!mainContent) {
            return false;
        }
        
        // Toggle collapsed class
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        if (isCollapsed) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
        } else {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
        
        return true;
    }
    
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) {
            return;
        }
        
        sidebar.classList.toggle('active');
        
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
    
    function initSidebar() {
        if (sidebarInitialized) {
            return;
        }
        
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainContent = document.getElementById('mainContent');
        
        if (!sidebar) {
            setTimeout(initSidebar, 100);
            return;
        }
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileSidebar();
            });
        }
        
        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('active')) {
                const isClickInsideSidebar = sidebar.contains(e.target);
                const isClickOnToggle = mobileMenuToggle && mobileMenuToggle.contains(e.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
        
        if (!sidebarToggle) {
            setTimeout(initSidebar, 100);
            return;
        }
        
        if (!mainContent) {
            setTimeout(initSidebar, 100);
            return;
        }
        
        // Remove any existing event listeners by cloning and replacing
        const newToggle = sidebarToggle.cloneNode(true);
        sidebarToggle.parentNode.replaceChild(newToggle, sidebarToggle);
        
        // Unified toggle handler for both desktop and mobile
        function handleToggle(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
            
            // Mobile: toggle active class
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('active');
            } else {
                // Desktop: toggle collapsed class
                toggleSidebar();
            }
            return false;
        }
        
        // Add click event to toggle button with multiple methods
        newToggle.addEventListener('click', handleToggle, true); // Use capture phase
        
        // Also add as direct onclick as fallback
        newToggle.onclick = handleToggle;
        
        // Close sidebar on mobile when clicking outside
        if (window.innerWidth <= 768) {
            document.addEventListener('click', function(e) {
                if (sidebar && sidebar.classList.contains('active') && 
                    !sidebar.contains(e.target) && 
                    newToggle && !newToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            });
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
            }
        });
        
        // Restore sidebar state on page load
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed');
        }
        
        sidebarInitialized = true;
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        // DOM already loaded
        initSidebar();
    }
    
    // Also try after a short delay as fallback
    setTimeout(initSidebar, 500);
    
    // Make toggleSidebar available globally for debugging
    window.toggleSidebar = toggleSidebar;
})();

