// Account Details Page JavaScript - اسکریپت صفحه جزئیات حساب

document.addEventListener('DOMContentLoaded', function() {
    initAccountDetails();
});

function initAccountDetails() {
    // Form submit
    const accountForm = document.getElementById('accountDetailsForm');
    if (accountForm) {
        accountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAccountDetails();
        });
    }
    
    // Password validation
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    if (newPasswordInput && confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            validatePasswordMatch();
        });
        
        newPasswordInput.addEventListener('input', function() {
            validatePasswordMatch();
            updatePasswordStrength(this.value);
        });
    }
    
    // Save notifications
    const saveNotificationsBtn = document.getElementById('saveNotificationsBtn');
    if (saveNotificationsBtn) {
        saveNotificationsBtn.addEventListener('click', function() {
            saveNotificationSettings();
        });
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelAccountBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('آیا مطمئن هستید که می‌خواهید تغییرات را لغو کنید؟')) {
                loadAccountDetails();
            }
        });
    }
    
    // Load account details
    loadAccountDetails();
}

function saveAccountDetails() {
    const form = document.getElementById('accountDetailsForm');
    if (!form) return;
    
    // Validate passwords if provided
    const currentPassword = document.getElementById('currentPassword')?.value;
    const newPassword = document.getElementById('newPassword')?.value;
    const confirmPassword = document.getElementById('confirmPassword')?.value;
    
    if (newPassword || confirmPassword || currentPassword) {
        if (!currentPassword) {
            alert('لطفاً رمز عبور فعلی را وارد کنید');
            return;
        }
        
        if (newPassword.length < 8) {
            alert('رمز عبور جدید باید حداقل 8 کاراکتر باشد');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            alert('رمز عبور جدید و تکرار آن مطابقت ندارند');
            return;
        }
    }
    
    const formData = new FormData(form);
    const accountData = Object.fromEntries(formData);
    
    // TODO: Save account details via API
    
    // Simulate save
    setTimeout(() => {
        alert('اطلاعات حساب با موفقیت به‌روزرسانی شد');
        loadAccountDetails();
    }, 500);
}

function validatePasswordMatch() {
    const newPassword = document.getElementById('newPassword')?.value;
    const confirmPassword = document.getElementById('confirmPassword')?.value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        const confirmInput = document.getElementById('confirmPassword');
        if (confirmInput) {
            confirmInput.setCustomValidity('رمز عبور و تکرار آن مطابقت ندارند');
        }
    } else {
        const confirmInput = document.getElementById('confirmPassword');
        if (confirmInput) {
            confirmInput.setCustomValidity('');
        }
    }
}

function updatePasswordStrength(password) {
    // Simple password strength checker
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;
    
    // TODO: Add visual password strength indicator
}

function saveNotificationSettings() {
    const checkboxes = document.querySelectorAll('.notification-settings input[type="checkbox"]');
    const settings = {};
    
    checkboxes.forEach(checkbox => {
        settings[checkbox.name] = checkbox.checked;
    });
    
    // TODO: Save notification settings via API
    
    // Simulate save
    setTimeout(() => {
        alert('تنظیمات اعلان‌ها با موفقیت ذخیره شد');
    }, 500);
}

function loadAccountDetails() {
    // TODO: Load account details from API
    
    // In real implementation, populate form with API data
}

// Export functions
window.accountDetailsPage = {
    saveAccountDetails,
    saveNotificationSettings,
    loadAccountDetails
};

