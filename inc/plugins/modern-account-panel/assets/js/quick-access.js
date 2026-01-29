// ============================================
// Quick Access Menu JavaScript - منوی دسترسی سریع
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initQuickAccessMenu();
});

function initQuickAccessMenu() {
    const quickAccessMenu = document.getElementById('quickAccessMenu');
    const quickAccessToggle = document.getElementById('quickAccessToggle');
    const quickAccessItems = document.querySelectorAll('.quick-access-item[data-page]');
    
    // Toggle quick access menu
    if (quickAccessToggle) {
        quickAccessToggle.addEventListener('click', function() {
            quickAccessMenu.classList.toggle('collapsed');
            const isCollapsed = quickAccessMenu.classList.contains('collapsed');
            localStorage.setItem('quickAccessCollapsed', isCollapsed);
        });
    }
    
    // Restore collapsed state
    const savedState = localStorage.getItem('quickAccessCollapsed');
    if (savedState === 'true' && quickAccessMenu) {
        quickAccessMenu.classList.add('collapsed');
    }
    
    // Handle quick access item clicks
    quickAccessItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetPage = this.getAttribute('data-page');
            
            // Remove active class from all items
            quickAccessItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Trigger navigation
            const navLink = document.querySelector(`.nav-link[data-page="${targetPage}"]`);
            if (navLink) {
                navLink.click();
            }
        });
    });
    
    // Update active state based on current page
    updateQuickAccessActive();
}

function updateQuickAccessActive() {
    const currentPage = document.querySelector('.page:not(.hidden)');
    if (currentPage) {
        const pageId = currentPage.getAttribute('data-page');
        const quickAccessItems = document.querySelectorAll('.quick-access-item[data-page]');
        
        quickAccessItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-page') === pageId) {
                item.classList.add('active');
            }
        });
    }
}

// Update active state when page changes
document.addEventListener('DOMContentLoaded', function() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateQuickAccessActive();
            }
        });
    });
    
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => {
        observer.observe(page, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
});

