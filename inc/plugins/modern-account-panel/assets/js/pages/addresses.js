// Addresses Page JavaScript - اسکریپت صفحه آدرس‌ها

document.addEventListener('DOMContentLoaded', function() {
    initAddresses();
});

function initAddresses() {
    // Add address button
    const addAddressBtn = document.getElementById('addAddressBtn');
    if (addAddressBtn) {
        addAddressBtn.addEventListener('click', function() {
            openAddressModal('add');
        });
    }
    
    // Edit address buttons
    const editButtons = document.querySelectorAll('.edit-address');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const addressType = this.getAttribute('data-type');
            openAddressModal('edit', addressType);
        });
    });
    
    // Delete address buttons
    const deleteButtons = document.querySelectorAll('.delete-address');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const addressType = this.getAttribute('data-type');
            deleteAddress(addressType);
        });
    });
    
    // Modal close
    const closeModalBtn = document.getElementById('closeAddressModal');
    const cancelBtn = document.getElementById('cancelAddressBtn');
    const addressModal = document.getElementById('addressModal');
    
    if (closeModalBtn && addressModal) {
        closeModalBtn.addEventListener('click', function() {
            closeAddressModal();
        });
    }
    
    if (cancelBtn && addressModal) {
        cancelBtn.addEventListener('click', function() {
            closeAddressModal();
        });
    }
    
    if (addressModal) {
        addressModal.addEventListener('click', function(e) {
            if (e.target === addressModal) {
                closeAddressModal();
            }
        });
    }
    
    // Form submit
    const addressForm = document.getElementById('addressForm');
    if (addressForm) {
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAddress();
        });
    }
    
    // Load addresses
    loadAddresses();
}

function openAddressModal(mode, addressType = null) {
    const modal = document.getElementById('addressModal');
    const modalTitle = document.getElementById('addressModalTitle');
    const form = document.getElementById('addressForm');
    const addressTypeInput = document.getElementById('addressType');
    
    if (!modal || !modalTitle || !form) return;
    
    if (mode === 'add') {
        modalTitle.textContent = 'افزودن آدرس جدید';
        form.reset();
        addressTypeInput.value = '';
    } else if (mode === 'edit' && addressType) {
        modalTitle.textContent = `ویرایش آدرس ${addressType === 'billing' ? 'صورتحساب' : 'ارسال'}`;
        addressTypeInput.value = addressType;
        loadAddressData(addressType);
    }
    
    modal.classList.add('active');
}

function closeAddressModal() {
    const modal = document.getElementById('addressModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function loadAddressData(addressType) {
    // TODO: Load address data from API
    // This is mock data
    const addressData = {
        billing: {
            first_name: 'علی',
            last_name: 'احمدی',
            company: '',
            address_1: 'تهران، خیابان ولیعصر، پلاک 123',
            address_2: 'واحد 5، طبقه 2',
            city: 'تهران',
            state: 'tehran',
            postcode: '1234567890',
            phone: '09123456789'
        },
        shipping: {
            first_name: 'علی',
            last_name: 'احمدی',
            company: '',
            address_1: 'تهران، خیابان انقلاب، پلاک 456',
            address_2: 'واحد 10',
            city: 'تهران',
            state: 'tehran',
            postcode: '0987654321',
            phone: '09123456789'
        }
    };
    
    const data = addressData[addressType];
    if (data) {
        Object.keys(data).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
                input.value = data[key];
            }
        });
    }
}

function saveAddress() {
    const form = document.getElementById('addressForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const addressData = Object.fromEntries(formData);
    
    // TODO: Save address via API
    
    // Simulate save
    setTimeout(() => {
        alert('آدرس با موفقیت ذخیره شد');
        closeAddressModal();
        loadAddresses();
    }, 500);
}

function deleteAddress(addressType) {
    if (!confirm(`آیا مطمئن هستید که می‌خواهید آدرس ${addressType === 'billing' ? 'صورتحساب' : 'ارسال'} را حذف کنید؟`)) {
        return;
    }
    
    // TODO: Delete address via API
    
    // Simulate delete
    setTimeout(() => {
        alert('آدرس با موفقیت حذف شد');
        loadAddresses();
    }, 500);
}

function loadAddresses() {
    // TODO: Load addresses from API
}

// Export functions
window.addressesPage = {
    openAddressModal,
    closeAddressModal,
    saveAddress,
    deleteAddress,
    loadAddresses
};

