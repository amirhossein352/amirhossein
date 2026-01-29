// Orders Page JavaScript - اسکریپت صفحه سفارش‌ها

document.addEventListener('DOMContentLoaded', function() {
    initOrders();
});

function initOrders() {
    // Filter functionality
    const applyFilterBtn = document.getElementById('applyFilter');
    const resetFilterBtn = document.getElementById('resetFilter');
    
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', applyFilters);
    }
    
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', resetFilters);
    }
    
    // Order detail buttons
    const viewOrderButtons = document.querySelectorAll('.view-order-detail');
    viewOrderButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order');
            showOrderDetail(orderId);
        });
    });
    
    // Download invoice buttons
    const downloadInvoiceButtons = document.querySelectorAll('.download-invoice');
    downloadInvoiceButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order');
            downloadInvoice(orderId);
        });
    });
    
    // Pay order buttons
    const payOrderButtons = document.querySelectorAll('.pay-order');
    payOrderButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order');
            payOrder(orderId);
        });
    });
    
    // Order link clicks
    const orderLinks = document.querySelectorAll('.order-link');
    orderLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const orderId = this.getAttribute('data-order');
            showOrderDetail(orderId);
        });
    });
    
    // Modal close
    const closeModalBtn = document.getElementById('closeOrderModal');
    const orderModal = document.getElementById('orderDetailModal');
    
    if (closeModalBtn && orderModal) {
        closeModalBtn.addEventListener('click', function() {
            orderModal.classList.remove('active');
        });
        
        orderModal.addEventListener('click', function(e) {
            if (e.target === orderModal) {
                orderModal.classList.remove('active');
            }
        });
    }
    
    // Refresh orders
    const refreshBtn = document.getElementById('refreshOrders');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            loadOrders();
        });
    }
    
    // Pagination
    const paginationButtons = document.querySelectorAll('.pagination-btn:not(:disabled)');
    paginationButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                const page = this.textContent.trim();
                loadOrders(page);
            }
        });
    });
    
    // Load initial orders
    loadOrders();
}

function applyFilters() {
    const search = document.getElementById('orderSearch')?.value || '';
    const status = document.getElementById('orderStatus')?.value || '';
    const dateRange = document.getElementById('orderDateRange')?.value || '';
    
    // TODO: Implement filter logic
    loadOrders(1, { search, status, dateRange });
}

function resetFilters() {
    const searchInput = document.getElementById('orderSearch');
    const statusSelect = document.getElementById('orderStatus');
    const dateRangeSelect = document.getElementById('orderDateRange');
    
    if (searchInput) searchInput.value = '';
    if (statusSelect) statusSelect.value = '';
    if (dateRangeSelect) dateRangeSelect.value = '';
    
    loadOrders();
}

function showOrderDetail(orderId) {
    const modal = document.getElementById('orderDetailModal');
    const modalOrderId = document.getElementById('modalOrderId');
    const modalContent = document.getElementById('orderDetailContent');
    
    if (!modal || !modalOrderId || !modalContent) return;
    
    modalOrderId.textContent = orderId;
    
    // Load order details
    loadOrderDetail(orderId).then(details => {
        modalContent.innerHTML = formatOrderDetail(details);
        modal.classList.add('active');
    }).catch(error => {
        modalContent.innerHTML = '<p>خطا در بارگذاری جزئیات سفارش</p>';
        modal.classList.add('active');
    });
}

function loadOrderDetail(orderId) {
    // TODO: Load order details from API
    return new Promise((resolve) => {
        // Mock data
        setTimeout(() => {
            resolve({
                id: orderId,
                date: '1403/01/15',
                status: 'تکمیل شده',
                total: '1,250,000 تومان',
                paymentMethod: 'آنلاین',
                items: [
                    { name: 'محصول 1', quantity: 2, price: '500,000 تومان' },
                    { name: 'محصول 2', quantity: 1, price: '250,000 تومان' }
                ],
                shipping: {
                    name: 'علی احمدی',
                    address: 'تهران، خیابان ولیعصر، پلاک 123',
                    phone: '09123456789'
                }
            });
        }, 500);
    });
}

function formatOrderDetail(details) {
    return `
        <div class="order-detail">
            <div class="detail-section">
                <h4>اطلاعات سفارش</h4>
                <div class="detail-item">
                    <span class="detail-label">تاریخ:</span>
                    <span class="detail-value">${details.date}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">وضعیت:</span>
                    <span class="detail-value">${details.status}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">مبلغ کل:</span>
                    <span class="detail-value">${details.total}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">روش پرداخت:</span>
                    <span class="detail-value">${details.paymentMethod}</span>
                </div>
            </div>
            
            <div class="detail-section">
                <h4>محصولات</h4>
                <div class="order-items">
                    ${details.items.map(item => `
                        <div class="order-item">
                            <span>${item.name}</span>
                            <span>${item.quantity} عدد</span>
                            <span>${item.price}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <div class="detail-section">
                <h4>آدرس ارسال</h4>
                <div class="shipping-info">
                    <p><strong>${details.shipping.name}</strong></p>
                    <p>${details.shipping.address}</p>
                    <p>${details.shipping.phone}</p>
                </div>
            </div>
        </div>
    `;
}

function downloadInvoice(orderId) {
    // TODO: Implement invoice download
    // Simulate download
    alert(`دریافت فاکتور سفارش #${orderId}`);
}

function payOrder(orderId) {
    // TODO: Implement payment
    // Navigate to payment page
    alert(`پرداخت سفارش #${orderId}`);
}

function loadOrders(page = 1, filters = {}) {
    // TODO: Load orders from API
    
    // Simulate loading
    const tableBody = document.getElementById('ordersTableBody');
    if (tableBody) {
        // In real implementation, update table with API data
    }
}

// Export functions
window.ordersPage = {
    showOrderDetail,
    loadOrders,
    applyFilters,
    resetFilters
};

