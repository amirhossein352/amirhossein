// Page Loader - لودر صفحات جداگانه

const pageConfig = {
    'dashboard': {
        html: 'pages/dashboard.html',
        css: 'pages/css/dashboard.css',
        js: 'pages/js/dashboard.js'
    },
    'orders': {
        html: 'pages/orders.html',
        css: 'pages/css/orders.css',
        js: 'pages/js/orders.js'
    },
    'downloads': {
        html: 'pages/downloads.html',
        css: 'pages/css/downloads.css',
        js: 'pages/js/downloads.js'
    },
    'addresses': {
        html: 'pages/addresses.html',
        css: 'pages/css/addresses.css',
        js: 'pages/js/addresses.js'
    },
    'payment-methods': {
        html: 'pages/payment-methods.html',
        css: 'pages/css/payment-methods.css',
        js: 'pages/js/payment-methods.js'
    },
    'account-details': {
        html: 'pages/account-details.html',
        css: 'pages/css/account-details.css',
        js: 'pages/js/account-details.js'
    }
};

const loadedPages = new Set();
const loadedStyles = new Set();
const loadedScripts = new Set();

async function loadPage(pageId) {
    const config = pageConfig[pageId];
    if (!config) {
        showError('صفحه مورد نظر یافت نشد');
        return;
    }
    
    const pageContainer = document.getElementById('pageContent');
    if (!pageContainer) {
        return;
    }
    
    // Show loading state
    showLoading(pageContainer);
    
    // Hide all pages
    const allPages = document.querySelectorAll('.page');
    allPages.forEach(page => {
        page.classList.add('hidden');
    });
    
    // Check if page already exists
    let pageElement = document.getElementById(pageId);
    
    if (!pageElement) {
        // Load HTML using fetch or XMLHttpRequest as fallback
        try {
            let html = '';
            
            // Try fetch first
            try {
                const response = await fetch(config.html);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                html = await response.text();
            } catch (fetchError) {
                // Fallback to XMLHttpRequest
                html = await loadWithXHR(config.html);
            }
            
            if (!html || html.trim() === '') {
                throw new Error('Page HTML is empty');
            }
            
            // Create page element
            pageElement = document.createElement('div');
            pageElement.id = pageId;
            pageElement.className = 'page';
            pageElement.setAttribute('data-page', pageId);
            pageElement.innerHTML = html;
            
            pageContainer.appendChild(pageElement);
            loadedPages.add(pageId);
        } catch (error) {
            hideLoading(pageContainer);
            showError(`خطا در بارگذاری صفحه: ${error.message}. لطفاً مطمئن شوید که فایل‌ها در مسیر صحیح هستند.`);
            return;
        }
    }
    
    // Load CSS
    if (!loadedStyles.has(pageId)) {
        const existingLink = document.getElementById(`page-css-${pageId}`);
        if (!existingLink) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = config.css;
            link.id = `page-css-${pageId}`;
            document.head.appendChild(link);
            loadedStyles.add(pageId);
        }
    }
    
    // Load JS
    if (!loadedScripts.has(pageId)) {
        const existingScript = document.getElementById(`page-js-${pageId}`);
        if (!existingScript) {
            const script = document.createElement('script');
            script.src = config.js;
            script.id = `page-js-${pageId}`;
            script.async = true;
            document.body.appendChild(script);
            loadedScripts.add(pageId);
        }
    }
    
    // Hide loading and show page
    hideLoading(pageContainer);
    if (pageElement) {
        pageElement.classList.remove('hidden');
    }
}

function showLoading(container) {
    const loading = document.createElement('div');
    loading.id = 'pageLoading';
    loading.className = 'page-loading';
    loading.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
        <p>در حال بارگذاری...</p>
    `;
    container.appendChild(loading);
}

function hideLoading(container) {
    const loading = document.getElementById('pageLoading');
    if (loading) {
        loading.remove();
    }
}

function loadWithXHR(url) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error(`XHR failed with status: ${xhr.status}`));
                }
            }
        };
        xhr.onerror = function() {
            reject(new Error('XHR network error'));
        };
        xhr.send();
    });
}

function showError(message) {
    const pageContainer = document.getElementById('pageContent');
    if (!pageContainer) return;
    
    hideLoading(pageContainer);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'page-error';
    errorDiv.innerHTML = `
        <div class="error-content">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>خطا</h3>
            <p>${message}</p>
            <button class="btn btn-primary" onclick="location.reload()">بارگذاری مجدد</button>
        </div>
    `;
    
    // Remove existing error
    const existingError = pageContainer.querySelector('.page-error');
    if (existingError) {
        existingError.remove();
    }
    
    pageContainer.appendChild(errorDiv);
}

// Initialize page loader
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure all scripts are loaded
    setTimeout(function() {
        // Load default page (dashboard)
        const defaultPage = 'dashboard';
        
        // Check if dashboard page already has content
        const dashboardPage = document.getElementById('dashboard');
        if (dashboardPage && dashboardPage.innerHTML.trim() === '') {
            loadPage(defaultPage).catch(() => {
                showError('خطا در بارگذاری صفحه پیشخوان. لطفاً صفحه را رفرش کنید.');
            });
        } else if (dashboardPage) {
            // Dashboard already has content, just show it
            dashboardPage.classList.remove('hidden');
        }
    }, 100);
    
    // Listen for navigation
    document.addEventListener('click', function(e) {
        const navLink = e.target.closest('.nav-link[data-page]');
        if (navLink) {
            const pageId = navLink.getAttribute('data-page');
            if (pageId && pageConfig[pageId]) {
                e.preventDefault();
                loadPage(pageId).catch(() => {
                    showError(`خطا در بارگذاری صفحه ${pageId}`);
                });
            }
        }
        
        // Handle quick action links
        const quickAction = e.target.closest('[data-page]');
        if (quickAction && quickAction !== navLink) {
            const pageId = quickAction.getAttribute('data-page');
            if (pageId && pageConfig[pageId]) {
                e.preventDefault();
                loadPage(pageId).catch(() => {
                    showError(`خطا در بارگذاری صفحه ${pageId}`);
                });
            }
        }
    });
});

// Export for use in other scripts
window.pageLoader = {
    loadPage,
    pageConfig
};

