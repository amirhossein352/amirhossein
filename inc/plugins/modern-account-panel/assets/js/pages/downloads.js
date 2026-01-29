// Downloads Page JavaScript - اسکریپت صفحه دانلودها

document.addEventListener('DOMContentLoaded', function() {
    initDownloads();
});

function initDownloads() {
    // Download file buttons
    const downloadButtons = document.querySelectorAll('.download-file');
    downloadButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const fileName = this.getAttribute('data-file');
            downloadFile(fileName);
        });
    });
    
    // Order link clicks
    const orderLinks = document.querySelectorAll('.download-meta a[data-order]');
    orderLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const orderId = this.getAttribute('data-order');
            viewOrder(orderId);
        });
    });
    
    // Load downloads
    loadDownloads();
}

function downloadFile(fileName) {
    // TODO: Implement actual file download
    
    // Simulate download
    const btn = event.target.closest('.download-file');
    if (btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال دانلود...';
        btn.disabled = true;
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            // In real implementation, trigger actual download
            alert(`فایل ${fileName} در حال دانلود است`);
        }, 2000);
    }
}

function viewOrder(orderId) {
    // Navigate to orders page
    const navLink = document.querySelector(`.nav-link[data-page="orders"]`);
    if (navLink) {
        navLink.click();
        // TODO: Show specific order detail
        setTimeout(() => {
            if (window.ordersPage && window.ordersPage.showOrderDetail) {
                window.ordersPage.showOrderDetail(orderId);
            }
        }, 500);
    }
}

function loadDownloads() {
    // TODO: Load downloads from API
    const downloadsList = document.getElementById('downloadsList');
    const emptyState = document.getElementById('emptyDownloads');
    
    // Check if there are any downloads
    if (downloadsList && downloadsList.children.length === 0) {
        if (emptyState) {
            emptyState.classList.remove('hidden');
        }
    } else {
        if (emptyState) {
            emptyState.classList.add('hidden');
        }
    }
    
}

// Export functions
window.downloadsPage = {
    downloadFile,
    viewOrder,
    loadDownloads
};

