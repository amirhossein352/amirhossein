/**
 * Pages JavaScript - About, Contact, etc.
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // FAQ Toggle Functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const answer = faqItem.querySelector('.faq-answer');
            
            // Close all other FAQ items
            faqQuestions.forEach(otherQuestion => {
                if (otherQuestion !== this) {
                    const otherItem = otherQuestion.parentElement;
                    const otherAnswer = otherItem.querySelector('.faq-answer');
                    otherItem.classList.remove('active');
                    otherAnswer.style.display = 'none';
                }
            });
            
            // Toggle current FAQ item
            if (faqItem.classList.contains('active')) {
                faqItem.classList.remove('active');
                answer.style.display = 'none';
            } else {
                faqItem.classList.add('active');
                answer.style.display = 'block';
            }
        });
    });
    
    // Contact Form Validation
    const contactForm = document.querySelector('.contact-form form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const name = formData.get('name');
            const email = formData.get('email');
            const subject = formData.get('subject');
            const message = formData.get('message');
            
            // Validate required fields
            let isValid = true;
            const errors = [];
            
            if (!name || name.trim() === '') {
                errors.push('نام و نام خانوادگی الزامی است');
                isValid = false;
            }
            
            if (!email || email.trim() === '') {
                errors.push('ایمیل الزامی است');
                isValid = false;
            } else if (!isValidEmail(email)) {
                errors.push('فرمت ایمیل صحیح نیست');
                isValid = false;
            }
            
            if (!subject || subject.trim() === '') {
                errors.push('موضوع الزامی است');
                isValid = false;
            }
            
            if (!message || message.trim() === '') {
                errors.push('پیام الزامی است');
                isValid = false;
            }
            
            if (isValid) {
                // Show success message
                showMessage('پیام شما با موفقیت ارسال شد. به زودی با شما تماس خواهیم گرفت.', 'success');
                this.reset();
            } else {
                // Show error messages
                showMessage('خطا در فرم:\n' + errors.join('\n'), 'error');
            }
        });
    }
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.team-member, .stat-item, .contact-item, .faq-item');
    animateElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
    
    // Counter animation for stats
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const finalNumber = stat.textContent;
        const isNumber = !isNaN(parseInt(finalNumber));
        
        if (isNumber) {
            const targetNumber = parseInt(finalNumber);
            let currentNumber = 0;
            const increment = targetNumber / 50;
            const timer = setInterval(() => {
                currentNumber += increment;
                if (currentNumber >= targetNumber) {
                    stat.textContent = finalNumber;
                    clearInterval(timer);
                } else {
                    stat.textContent = Math.floor(currentNumber);
                }
            }, 30);
        }
    });
    
    // Social media link tracking
    const socialLinks = document.querySelectorAll('.social-icon');
    socialLinks.forEach(link => {
        link.addEventListener('click', function() {
            const platform = this.querySelector('i').className.split('fa-')[1];
            console.log('Social media clicked:', platform);
            // You can add analytics tracking here
        });
    });
    
    // Form field focus effects
    const formFields = document.querySelectorAll('.form-group input, .form-group textarea');
    formFields.forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        field.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
    
});

// Helper Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showMessage(message, type) {
    // Remove existing messages
    const existingMessage = document.querySelector('.form-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message ${type}`;
    messageDiv.textContent = message;
    
    // Style the message
    messageDiv.style.cssText = `
        padding: 15px 20px;
        margin: 20px 0;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        ${type === 'success' ? 
            'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;' : 
            'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;'
        }
    `;
    
    // Insert message
    const form = document.querySelector('.contact-form form');
    if (form) {
        form.insertBefore(messageDiv, form.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

// Add CSS for form focus effects
const style = document.createElement('style');
style.textContent = `
    .form-group.focused label {
        color: var(--zs-teal-dark, #00A796);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .form-group.focused input,
    .form-group.focused textarea {
        border-color: var(--zs-teal-dark, #00A796);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-message {
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
