// ============================================
// Main JavaScript - اسکریپت اصلی
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initProfileDropdown();
    initNotifications();
    initMessages();
    initThemeFont();
});

// Get font from theme and apply to account panel
function initThemeFont() {
    if (!document.body.classList.contains('modern-account-panel-page')) {
        return;
    }
    
    // Wait for styles to load
    setTimeout(function() {
        let themeFont = '';
        
        // Method 1: Check if CSS variable is already set by PHP
        const rootStyle = window.getComputedStyle(document.documentElement);
        const cssVar = rootStyle.getPropertyValue('--theme-font');
        if (cssVar && cssVar.trim() !== '' && cssVar !== 'inherit' && !cssVar.includes('var(')) {
            themeFont = cssVar.trim();
        }
        
        // Method 2: Get from body's computed style (before our overrides)
        if (!themeFont || themeFont === 'inherit' || themeFont.trim() === '') {
            // Create temp element to get original body font
            const temp = document.createElement('div');
            temp.style.cssText = 'position: absolute; visibility: hidden; font-family: inherit;';
            document.body.insertBefore(temp, document.body.firstChild);
            const tempStyle = window.getComputedStyle(temp);
            const tempFont = tempStyle.getPropertyValue('font-family');
            if (tempFont && tempFont.trim() !== '' && tempFont !== 'inherit') {
                themeFont = tempFont;
            }
            document.body.removeChild(temp);
        }
        
        // Method 3: Get from html element
        if (!themeFont || themeFont === 'inherit' || themeFont.trim() === '') {
            const htmlStyle = window.getComputedStyle(document.documentElement);
            themeFont = htmlStyle.getPropertyValue('font-family');
        }
        
        // Clean up
        if (themeFont) {
            themeFont = themeFont.trim().replace(/^["']|["']$/g, '');
        }
        
        // Apply if valid (not generic fonts)
        if (themeFont && themeFont !== 'inherit' && themeFont.trim() !== '' && 
            !themeFont.match(/^(sans-serif|serif|monospace)$/i) && 
            !themeFont.includes('var(')) {
            document.documentElement.style.setProperty('--theme-font', themeFont);
            document.body.style.fontFamily = themeFont;
        }
    }, 200);
}

// Profile Dropdown
function initProfileDropdown() {
    const profileDropdown = document.getElementById('profileDropdown');
    const profileMenu = document.getElementById('profileMenu');
    
    if (profileDropdown && profileMenu) {
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });
    }
}

// Notifications
function initNotifications() {
    const notificationsBtn = document.getElementById('notificationsBtn');
    
    if (notificationsBtn) {
        notificationsBtn.addEventListener('click', function() {
            // TODO: Show notifications dropdown
        });
    }
}

// Messages
function initMessages() {
    const messagesBtn = document.getElementById('messagesBtn');
    
    if (messagesBtn) {
        messagesBtn.addEventListener('click', function() {
            // TODO: Show messages dropdown
        });
    }
}

// Utility Functions
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 20px;
        padding: 1rem 1.5rem;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(-100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

