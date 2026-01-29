/**
 * Live Q&A System - پرسش و پاسخ لایو
 * 
 * @package khane_irani
 */

(function() {
    'use strict';
    
    var productId = null;
    var questionsKey = 'zs_live_qa_';
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLiveQA);
    } else {
        initLiveQA();
    }
    
    function initLiveQA() {
        // Setup inputs (bind UI regardless of productId detection)
        var questionInput = document.getElementById('zs-live-question-input');
        var nameInput = document.getElementById('zs-live-name-input');
        var topicSelect = document.getElementById('zs-live-topic-select');
        var progressBar = document.getElementById('zs-question-progress');
        var formSuccess = document.getElementById('zs-live-form-success');
        if (questionInput) {
            // Character counter
            var charCount = document.getElementById('zs-question-char-count');
            var updateChar = function(){
                var len = (questionInput.value || '').length;
                if (charCount) {
                    charCount.textContent = String(len);
                    charCount.style.color = (len > 450) ? 'var(--zs-teal-medium, #37C3B3)' : '';
                }
                if (progressBar) {
                    var pct = Math.min(100, Math.round((len / 500) * 100));
                    progressBar.style.width = pct + '%';
                }
            };
            questionInput.addEventListener('input', updateChar);
            questionInput.addEventListener('keyup', updateChar);
            questionInput.addEventListener('change', updateChar);
            questionInput.addEventListener('paste', function(){ setTimeout(updateChar, 0); });
            // initialize
            updateChar();
            
            // Allow multiline: use Ctrl+Enter to submit, Enter inserts newline
            questionInput.addEventListener('keydown', function(e) {
                if ((e.key === 'Enter' && (e.ctrlKey || e.metaKey))) {
                    e.preventDefault();
                    zsSubmitLiveQuestion();
                }
            });
            // Toolbar emoji buttons
            var toolbarButtons = document.querySelectorAll('.zs-input-toolbar .zs-tool-btn[data-emoji]');
            toolbarButtons.forEach(function(btn){
                btn.addEventListener('click', function(){
                    var emoji = this.getAttribute('data-emoji') || '';
                    questionInput.value = (questionInput.value || '') + (emoji ? (' ' + emoji) : '');
                    questionInput.dispatchEvent(new Event('input'));
                    questionInput.focus();
                });
            });

            // Paste from clipboard
            var pasteBtn = document.querySelector('.zs-input-toolbar .zs-tool-paste');
            if (pasteBtn && navigator.clipboard && navigator.clipboard.readText) {
                pasteBtn.addEventListener('click', function(){
                    navigator.clipboard.readText().then(function(text){
                        if (!text) return;
                        questionInput.value = (questionInput.value || '') + (questionInput.value ? ' ' : '') + text;
                        questionInput.dispatchEvent(new Event('input'));
                        questionInput.focus();
                    });
                });
            }
        }
        
        // Get product ID after binding UI (support /product/ and /service/)
        var productElement = document.querySelector('[data-product-id], .product');
        if (productElement) {
            var idAttr = productElement.getAttribute('data-product-id');
            var productIdMatch = window.location.pathname.match(/\/(product|service)\/([^\/]+)/);
            if (idAttr) {
                productId = idAttr;
            } else if (productIdMatch) {
                productId = productIdMatch[2] || productIdMatch[1];
            } else {
                var bodyClasses = document.body.className;
                var match = bodyClasses.match(/postid-(\d+)/);
                if (match) { productId = match[1]; }
            }
        }
        if (productId) { questionsKey = questionsKey + productId; }
        
        // Load existing questions if productId exists
        if (productId) {
            loadQuestions();
            // Auto-refresh every 10 seconds
            setInterval(loadQuestions, 10000);
        }
    }
    
    // Load questions from server; fallback to localStorage if AJAX fails
    function loadQuestions() {
        var questionsList = document.getElementById('zs-live-questions-list');
        if (!questionsList) return;
        
        if (typeof zs_live_qa !== 'undefined' && productId) {
            var url = zs_live_qa.ajax_url + '?action=zs_get_live_questions&product_id=' + encodeURIComponent(productId);
            fetch(url, { credentials: 'same-origin' })
                .then(function(r){ return r.json(); })
                .then(function(data){
                    if (!data || !data.success || !data.data || !Array.isArray(data.data.items)) throw new Error('bad');
                    var items = data.data.items;
                    if (items.length === 0) {
                        questionsList.innerHTML = '<div class="zs-no-questions"><i class="fas fa-comments"></i><p>هنوز سوالی پرسیده نشده است. اولین سوال را بپرسید!</p></div>';
                        return;
                    }
                    var html = '';
                    items.forEach(function(q, idx){ html += renderQuestion(q, idx); });
                    questionsList.innerHTML = html;
                })
                .catch(function(){
                    // Fallback to localStorage
                    var questions = getStoredQuestions();
                    if (questions.length === 0) {
                        questionsList.innerHTML = '<div class="zs-no-questions"><i class="fas fa-comments"></i><p>هنوز سوالی پرسیده نشده است. اولین سوال را بپرسید!</p></div>';
                        return;
                    }
                    questions.sort(function(a,b){ return b.timestamp - a.timestamp; });
                    var html = '';
                    questions.forEach(function(q, index){ html += renderQuestion(q, index); });
                    questionsList.innerHTML = html;
                });
        }
    }
    
    // Render a single question
    function renderQuestion(q, index) {
        var timeAgo = getTimeAgo(q.timestamp);
        var authorInitial = q.authorName ? q.authorName.charAt(0) : '?';
        var answerHtml = '';
        
        if (q.answer && q.answer.trim()) {
            answerHtml = '<div class="zs-answer-section">' +
                '<div class="zs-answer-item">' +
                '<div class="zs-answer-avatar">پ</div>' +
                '<div class="zs-answer-content-wrapper">' +
                '<span class="zs-answer-badge"><i class="fas fa-check-circle"></i> پاسخ داده شده</span>' +
                '<p class="zs-answer-text">' + escapeHtml(q.answer) + '</p>' +
                '<div class="zs-answer-meta">' +
                '<span class="zs-answer-time"><i class="fas fa-clock"></i> ' + getTimeAgo(q.answerTimestamp || q.timestamp) + '</span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
        }
        
        var topic = q.topic || 'general';
        var topicLabel = (topic === 'pricing') ? 'قیمت‌گذاری' : (topic === 'technical') ? 'فنی' : (topic === 'timeline') ? 'زمان‌بندی' : 'سایر';
        var topicBadge = '<span class="zs-topic-badge zs-topic-' + topic + '">' + topicLabel + '</span>';

        return '<div class="zs-question-item' + (index < 3 ? ' new' : '') + '">' +
            '<div class="zs-question-header">' +
            '<div class="zs-question-avatar">' + authorInitial + '</div>' +
            '<div class="zs-question-content-wrapper">' +
            '<div class="zs-question-author">' + (q.authorName || 'کاربر') + ' ' + topicBadge + '</div>' +
            '<p class="zs-question-text">' + escapeHtml(q.text) + '</p>' +
            '<div class="zs-question-meta">' +
            '<span class="zs-question-time"><i class="fas fa-clock"></i> ' + timeAgo + '</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            answerHtml +
            '</div>';
    }
    
    // Submit question
    window.zsSubmitLiveQuestion = function() {
        var questionInput = document.getElementById('zs-live-question-input');
        if (!questionInput) return;
        
        var nameInput = document.getElementById('zs-live-name-input');
        var topicSelect = document.getElementById('zs-live-topic-select');
        var formSuccess = document.getElementById('zs-live-form-success');
        var questionText = questionInput.value.trim();
        if (!questionText) {
            try { questionInput.classList.add('error'); } catch(e){}
            setTimeout(function(){ try { questionInput.classList.remove('error'); } catch(e){} }, 600);
            showToast('لطفاً سوال خود را وارد کنید');
            return;
        }
        
        if (questionText.length > 500) {
            try { questionInput.classList.add('error'); } catch(e){}
            setTimeout(function(){ try { questionInput.classList.remove('error'); } catch(e){} }, 600);
            showToast('سوال شما نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد');
            return;
        }
        
        // Disable input
        questionInput.disabled = true;
        var submitBtn = document.querySelector('.zs-submit-question-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال ارسال...';
        }
        
        // Create question object
        var question = {
            id: Date.now().toString(),
            text: questionText,
            authorName: (nameInput && nameInput.value.trim()) ? nameInput.value.trim() : 'شما',
            timestamp: Date.now(),
            topic: (topicSelect && topicSelect.value) ? topicSelect.value : 'general',
            answer: null,
            answerTimestamp: null
        };
        
        // Submit to server via AJAX
        if (typeof zs_live_qa !== 'undefined') {
            var formData = new FormData();
            formData.append('action', 'zs_submit_live_question');
            formData.append('nonce', zs_live_qa.nonce);
            formData.append('product_id', productId);
            formData.append('text', questionText);
            formData.append('author', (nameInput && nameInput.value.trim()) ? nameInput.value.trim() : '');
            formData.append('topic', (topicSelect && topicSelect.value) ? topicSelect.value : 'general');
            fetch(zs_live_qa.ajax_url, { method: 'POST', credentials: 'same-origin', body: formData })
                .then(function(r){ return r.json(); })
                .then(function(res){ /* no-op */ })
                .catch(function(){ /* ignore and fallback */ })
                .finally(function(){ loadQuestions(); });
        } else {
            // Fallback to localStorage
            var questions = getStoredQuestions();
            questions.unshift(question);
            saveQuestions(questions);
        }
        
        // Clear input
        questionInput.value = '';
        if (nameInput) { /* keep name */ }
        if (topicSelect) { /* keep selection */ }
        var charCount = document.getElementById('zs-question-char-count');
        if (charCount) {
            charCount.textContent = '0';
        }
        var progressBar = document.getElementById('zs-question-progress');
        if (progressBar) { progressBar.style.width = '0%'; }
        
        // Show success message
        if (formSuccess) {
            formSuccess.style.display = 'flex';
            setTimeout(function(){ formSuccess.style.display = 'none'; }, 2000);
        } else {
            showToast('سوال شما با موفقیت ثبت شد. منتظر پاسخ بمانید...');
        }
        
        // Reload questions
        setTimeout(function() {
            loadQuestions();
            questionInput.disabled = false;
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>ارسال</span>';
            }
        }, 500);
        
        // Remove previous simulation of auto-answer; answers will come from admin replies
    };
    
    // Get stored questions
    function getStoredQuestions() {
        try {
            var stored = localStorage.getItem(questionsKey);
            if (stored) {
                return JSON.parse(stored);
            }
        } catch (e) {
            console.error('Error loading questions:', e);
        }
        return [];
    }
    
    // Save questions
    function saveQuestions(questions) {
        try {
            // Keep only last 20 questions
            if (questions.length > 20) {
                questions = questions.slice(0, 20);
            }
            localStorage.setItem(questionsKey, JSON.stringify(questions));
        } catch (e) {
            console.error('Error saving questions:', e);
        }
    }
    
    // Get time ago string
    function getTimeAgo(timestamp) {
        var now = Date.now();
        var diff = now - timestamp;
        var seconds = Math.floor(diff / 1000);
        var minutes = Math.floor(seconds / 60);
        var hours = Math.floor(minutes / 60);
        var days = Math.floor(hours / 24);
        
        if (seconds < 60) {
            return 'همین الان';
        } else if (minutes < 60) {
            return minutes + ' دقیقه پیش';
        } else if (hours < 24) {
            return hours + ' ساعت پیش';
        } else if (days < 7) {
            return days + ' روز پیش';
        } else {
            var date = new Date(timestamp);
            return date.toLocaleDateString('fa-IR');
        }
    }
    
    // Escape HTML
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Show toast message
    function showToast(message) {
        // Create toast element
        var toast = document.createElement('div');
        toast.className = 'zs-toast-message';
        toast.style.cssText = 'position: fixed; bottom: 100px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3)); color: white; padding: 16px 24px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,54,197,0.4); z-index: 10000; font-weight: 600; animation: zsToastSlideUp 0.3s ease-out;';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(function() {
            toast.style.animation = 'zsToastSlideDown 0.3s ease-out';
            setTimeout(function() {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Add toast animations
    if (!document.getElementById('zs-toast-styles')) {
        var style = document.createElement('style');
        style.id = 'zs-toast-styles';
        style.textContent = '@keyframes zsToastSlideUp { from { opacity: 0; transform: translateX(-50%) translateY(20px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } } @keyframes zsToastSlideDown { from { opacity: 1; transform: translateX(-50%) translateY(0); } to { opacity: 0; transform: translateX(-50%) translateY(20px); } }';
        document.head.appendChild(style);
    }
    
})();

