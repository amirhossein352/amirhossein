// Payment Methods Page JavaScript - اسکریپت صفحه روش‌های پرداخت

document.addEventListener('DOMContentLoaded', function() {
    initPaymentMethods();
});

function initPaymentMethods() {
    // Add payment method button
    const addPaymentBtn = document.getElementById('addPaymentMethodBtn');
    const addFirstPaymentBtn = document.getElementById('addFirstPaymentBtn');
    
    if (addPaymentBtn) {
        addPaymentBtn.addEventListener('click', function() {
            openPaymentModal('add');
        });
    }
    
    if (addFirstPaymentBtn) {
        addFirstPaymentBtn.addEventListener('click', function() {
            openPaymentModal('add');
        });
    }
    
    // Edit payment method buttons
    const editButtons = document.querySelectorAll('.edit-payment');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const cardId = this.getAttribute('data-card');
            openPaymentModal('edit', cardId);
        });
    });
    
    // Delete payment method buttons
    const deleteButtons = document.querySelectorAll('.delete-payment');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const cardId = this.getAttribute('data-card');
            deletePaymentMethod(cardId);
        });
    });
    
    // Set default buttons
    const setDefaultButtons = document.querySelectorAll('.set-default');
    setDefaultButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const cardId = this.getAttribute('data-card');
            setDefaultPaymentMethod(cardId);
        });
    });
    
    // Modal close
    const closeModalBtn = document.getElementById('closePaymentModal');
    const cancelBtn = document.getElementById('cancelPaymentBtn');
    const paymentModal = document.getElementById('paymentMethodModal');
    
    if (closeModalBtn && paymentModal) {
        closeModalBtn.addEventListener('click', function() {
            closePaymentModal();
        });
    }
    
    if (cancelBtn && paymentModal) {
        cancelBtn.addEventListener('click', function() {
            closePaymentModal();
        });
    }
    
    if (paymentModal) {
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                closePaymentModal();
            }
        });
    }
    
    // Form submit
    const paymentForm = document.getElementById('paymentMethodForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            savePaymentMethod();
        });
    }
    
    // Format card number input
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            formatCardNumber(e.target);
        });
    }
    
    // Format expiry input
    const cardExpiryInput = document.getElementById('cardExpiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function(e) {
            formatCardExpiry(e.target);
        });
    }
    
    // Load payment methods
    loadPaymentMethods();
}

function openPaymentModal(mode, cardId = null) {
    const modal = document.getElementById('paymentMethodModal');
    const modalTitle = document.getElementById('paymentModalTitle');
    const form = document.getElementById('paymentMethodForm');
    
    if (!modal || !modalTitle || !form) return;
    
    if (mode === 'add') {
        modalTitle.textContent = 'افزودن کارت بانکی جدید';
        form.reset();
    } else if (mode === 'edit' && cardId) {
        modalTitle.textContent = 'ویرایش کارت بانکی';
        loadPaymentData(cardId);
    }
    
    modal.classList.add('active');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentMethodModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function formatCardNumber(input) {
    let value = input.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    input.value = formattedValue;
}

function formatCardExpiry(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    input.value = value;
}

function loadPaymentData(cardId) {
    // TODO: Load payment data from API
    // Mock data
    const form = document.getElementById('paymentMethodForm');
    if (form) {
        // In real implementation, load from API
    }
}

function savePaymentMethod() {
    const form = document.getElementById('paymentMethodForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const paymentData = Object.fromEntries(formData);
    
    // TODO: Save payment method via API
    
    // Simulate save
    setTimeout(() => {
        alert('کارت بانکی با موفقیت ذخیره شد');
        closePaymentModal();
        loadPaymentMethods();
    }, 500);
}

function deletePaymentMethod(cardId) {
    if (!confirm('آیا مطمئن هستید که می‌خواهید این کارت بانکی را حذف کنید؟')) {
        return;
    }
    
    // TODO: Delete payment method via API
    
    // Simulate delete
    setTimeout(() => {
        alert('کارت بانکی با موفقیت حذف شد');
        loadPaymentMethods();
    }, 500);
}

function setDefaultPaymentMethod(cardId) {
    // TODO: Set default payment method via API
    
    // Simulate set default
    setTimeout(() => {
        alert('روش پرداخت پیش‌فرض تغییر کرد');
        loadPaymentMethods();
    }, 500);
}

function loadPaymentMethods() {
    // TODO: Load payment methods from API
    const methodsList = document.getElementById('paymentMethodsList');
    const emptyState = document.getElementById('emptyPaymentMethods');
    
    // Check if there are any payment methods
    if (methodsList && methodsList.children.length === 0) {
        if (emptyState) {
            emptyState.classList.remove('hidden');
        }
        if (methodsList) {
            methodsList.style.display = 'none';
        }
    } else {
        if (emptyState) {
            emptyState.classList.add('hidden');
        }
        if (methodsList) {
            methodsList.style.display = 'block';
        }
    }
    
}

// Export functions
window.paymentMethodsPage = {
    openPaymentModal,
    closePaymentModal,
    savePaymentMethod,
    deletePaymentMethod,
    setDefaultPaymentMethod,
    loadPaymentMethods
};

