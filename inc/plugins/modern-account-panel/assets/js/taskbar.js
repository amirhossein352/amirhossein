// ============================================
// Taskbar JavaScript - اسکریپت تسک‌بار
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initTaskbar();
    initStartMenu();
    initTaskbarApps();
    initDateTime();
});

// Initialize Taskbar
function initTaskbar() {
    const taskbar = document.getElementById('taskbar');
    if (!taskbar) return;
    
    // Prevent context menu on taskbar
    taskbar.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
}

// Initialize Start Menu
function initStartMenu() {
    const startButton = document.getElementById('taskbarStart');
    const startMenu = document.getElementById('startMenu');
    
    if (!startButton || !startMenu) return;
    
    // Toggle start menu
    startButton.addEventListener('click', function(e) {
        e.stopPropagation();
        startMenu.classList.toggle('active');
        startButton.classList.toggle('active');
    });
    
    // Close start menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!startMenu.contains(e.target) && !startButton.contains(e.target)) {
            startMenu.classList.remove('active');
            startButton.classList.remove('active');
        }
    });
    
    // Handle start menu item clicks
    const startMenuItems = startMenu.querySelectorAll('.start-menu-item[data-page]');
    startMenuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetPage = this.getAttribute('data-page');
            
            // Close start menu
            startMenu.classList.remove('active');
            startButton.classList.remove('active');
            
            // Load page using page loader
            if (window.pageLoader && window.pageLoader.loadPage) {
                window.pageLoader.loadPage(targetPage).catch(() => {
                    // Error handled silently
                });
            } else {
                // Fallback to navigation
                const navLink = document.querySelector(`.nav-link[data-page="${targetPage}"]`);
                if (navLink) {
                    navLink.click();
                }
            }
        });
    });
    
    // Handle start menu footer items
    const footerItems = startMenu.querySelectorAll('.start-menu-footer-item[data-page]');
    footerItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetPage = this.getAttribute('data-page');
            
            if (targetPage === 'logout') {
                if (confirm('آیا مطمئن هستید که می‌خواهید خارج شوید؟')) {
                    // TODO: Implement logout logic
                }
                return;
            }
            
            // Close start menu
            startMenu.classList.remove('active');
            startButton.classList.remove('active');
            
            // Navigate to page
            const navLink = document.querySelector(`.nav-link[data-page="${targetPage}"]`);
            if (navLink) {
                navLink.click();
            }
        });
    });
}

// Initialize Taskbar Apps
function initTaskbarApps() {
    const taskbarApps = document.querySelectorAll('.taskbar-app[data-page]');
    
    taskbarApps.forEach(app => {
        app.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            
            // Remove active class from all apps
            taskbarApps.forEach(a => a.classList.remove('active'));
            
            // Add active class to clicked app
            this.classList.add('active');
            
            // Load page using page loader
            if (window.pageLoader && window.pageLoader.loadPage) {
                window.pageLoader.loadPage(targetPage).catch(() => {
                    // Error handled silently
                });
            } else {
                // Fallback to navigation
                const navLink = document.querySelector(`.nav-link[data-page="${targetPage}"]`);
                if (navLink) {
                    navLink.click();
                }
            }
        });
    });
    
    // Update active state based on current page
    updateTaskbarApps();
}

function updateTaskbarApps() {
    const currentPage = document.querySelector('.page:not(.hidden)');
    if (currentPage) {
        const pageId = currentPage.getAttribute('data-page');
        const taskbarApps = document.querySelectorAll('.taskbar-app[data-page]');
        
        taskbarApps.forEach(app => {
            app.classList.remove('active');
            if (app.getAttribute('data-page') === pageId) {
                app.classList.add('active');
            }
        });
    }
}

// Initialize Date and Time
function initDateTime() {
    const timeDisplay = document.getElementById('timeDisplay');
    const dateDisplay = document.getElementById('dateDisplay');
    
    if (!timeDisplay || !dateDisplay) return;
    
    function updateDateTime() {
        const now = new Date();
        
        // Time
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeDisplay.textContent = `${hours}:${minutes}`;
        
        // Date (Persian/Jalali format - simplified)
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        dateDisplay.textContent = `${year}/${month}/${day}`;
    }
    
    // Update immediately
    updateDateTime();
    
    // Update every minute
    setInterval(updateDateTime, 60000);
    
    // Click on time to show calendar (optional)
    const taskbarTime = document.getElementById('taskbarTime');
    if (taskbarTime) {
        taskbarTime.addEventListener('click', function() {
            // TODO: Show calendar widget
        });
    }
}

// Update taskbar apps when page changes
document.addEventListener('DOMContentLoaded', function() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateTaskbarApps();
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

// Handle taskbar notifications
document.getElementById('taskbarNotifications')?.addEventListener('click', function() {
    // TODO: Show notifications panel
});

// Handle taskbar messages
document.getElementById('taskbarMessages')?.addEventListener('click', function() {
    // TODO: Show messages panel
});

