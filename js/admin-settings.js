/**
 * Admin Settings JavaScript - Fixed Version
 * 
 * @package khane_irani
 */

jQuery(document).ready(function($) {
    
    // Image Upload Functionality
    $(document).on('click', '.upload-image-button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var input = button.siblings('input[type="hidden"]');
        var preview = button.siblings('.image-preview');
        var removeButton = button.siblings('.remove-image-button');
        
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('WordPress media library is not available.');
            return;
        }
        
        // Create media frame
        var frame = wp.media({
            title: 'انتخاب تصویر',
            button: {
                text: 'انتخاب تصویر'
            },
            multiple: false
        });
        
        // When image is selected
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var imageUrl = '';
            if (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
                imageUrl = attachment.sizes.thumbnail.url;
            } else if (attachment.url) {
                imageUrl = attachment.url;
            }

            input.val(attachment.id);

            if (imageUrl) {
                var altText = attachment.alt || attachment.title || '';
                preview.html('<img src="' + imageUrl + '" alt="' + altText + '">');
            } else {
                preview.html('<span class="no-preview">تصویر انتخاب شد</span>');
            }

            removeButton.show();
        });
        
        // Open media frame
        frame.open();
    });
    
    // Remove Image Functionality
    $(document).on('click', '.remove-image-button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var input = button.siblings('input[type="hidden"]');
        var preview = button.siblings('.image-preview');
        var uploadButton = button.siblings('.upload-image-button');
        
        input.val('');
        preview.html('');
        button.hide();
        uploadButton.show();
    });
    
    // Tab switching - Simple: let browser handle navigation naturally
    // No JavaScript needed for tab switching - server handles it via GET parameter
    
    // AJAX Form Submission - Fixed
    $(document).on('submit', '#zarin-settings-form', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('input[type="submit"], button[type="submit"]');
        var originalText = submitBtn.val() || submitBtn.text();
        var originalDisabled = submitBtn.prop('disabled');
        
        // Sync TinyMCE editors before form submission
        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }
        
        // Get active tab from current URL or hidden input
        var activeTab = $('input[name="active_tab"]').val() || getUrlParameter('tab') || 'header';
        
        // Validate form
        var isValid = true;
        var errorMessages = [];
        
        form.find('input[required], textarea[required], select[required]').each(function() {
            var field = $(this);
            var value = field.val() ? field.val().trim() : '';
            
            if (!value) {
                isValid = false;
                field.addClass('error');
                errorMessages.push('فیلد ' + (field.attr('name') || '') + ' الزامی است');
            } else {
                field.removeClass('error');
            }
        });
        
        // Validate email fields
        form.find('input[type="email"]').each(function() {
            var field = $(this);
            var value = field.val() ? field.val().trim() : '';
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (value && !emailRegex.test(value)) {
                isValid = false;
                field.addClass('error');
                errorMessages.push('فرمت ایمیل صحیح نیست');
            }
        });
        
        if (!isValid) {
            alert('خطا در فرم:\n' + errorMessages.join('\n'));
            return false;
        }
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true);
        if (submitBtn.is('input')) {
            submitBtn.val('در حال ذخیره...');
        } else {
            submitBtn.text('در حال ذخیره...');
        }
        
        // Simple function to parse name like "khane-irani-settings[banners_items][0][image]" 
        // and set value in nested object/array structure
        function setNestedValue(obj, path, value) {
            // Parse path like "banners_items][0][image" or "faq_items][0][question"
            var parts = path.split(/\]\[|\[|\]/).filter(function(p) { return p.length > 0; });
            var current = obj;
            
            // Navigate/create structure
            for (var i = 0; i < parts.length - 1; i++) {
                var part = parts[i];
                var nextPart = parts[i + 1];
                var isNumeric = /^\d+$/.test(part);
                var nextIsNumeric = nextPart && /^\d+$/.test(nextPart);
                
                if (isNumeric) {
                    // Numeric key means we need an array
                    var idx = parseInt(part);
                    if (!Array.isArray(current)) {
                        // Current should be array but it's not - this is an error in structure
                        // Try to fix by converting parent
                        console.warn('Expected array but got object at:', part);
                        // For now, treat as object property (shouldn't happen in correct forms)
                        if (!current.hasOwnProperty(part)) {
                            current[part] = nextIsNumeric ? [] : {};
                        }
                        current = current[part];
                    } else {
                        // Ensure array is large enough
                        while (current.length <= idx) {
                            current.push(nextIsNumeric ? [] : {});
                        }
                        if (current[idx] === null || (typeof current[idx] !== 'object' && !Array.isArray(current[idx]))) {
                            current[idx] = nextIsNumeric ? [] : {};
                        }
                        current = current[idx];
                    }
                } else {
                    // String key means we need an object (or array if next is numeric)
                    if (!current.hasOwnProperty(part)) {
                        current[part] = nextIsNumeric ? [] : {};
                    }
                    if (current[part] === null || (typeof current[part] !== 'object' && !Array.isArray(current[part]))) {
                        current[part] = nextIsNumeric ? [] : {};
                    }
                    current = current[part];
                }
            }
            
            // Set the final value
            var lastPart = parts[parts.length - 1];
            var isLastNumeric = /^\d+$/.test(lastPart);
            
            if (isLastNumeric) {
                var lastIdx = parseInt(lastPart);
                if (Array.isArray(current)) {
                    current[lastIdx] = value;
                } else {
                    current[lastIdx] = value;
                }
            } else {
                current[lastPart] = value;
            }
        }
        
        // Collect ALL form data including checkboxes and nested arrays
        var formData = {};
        
        // Get all inputs including checkboxes
        form.find('input, textarea, select').each(function() {
            var field = $(this);
            var name = field.attr('name');
            
            if (!name) return;
            
            // Handle active_tab separately
            if (name === 'active_tab') {
                activeTab = field.val();
                return;
            }
            
            // Extract field path from name like 'khane-irani-settings[field_name]' or 'khane-irani-settings[faq_items][0][question]'
            var match = name.match(/khane-irani-settings\[(.+)\]/);
            if (match) {
                var fieldPath = match[1]; // This could be 'field_name' or 'faq_items][0][question'
                var fieldType = field.attr('type');
                var value;
                
                if (fieldType === 'checkbox') {
                    // For checkbox, use value 1 if checked, 0 if not
                    value = field.is(':checked') ? '1' : '0';
                } else if (fieldType === 'radio') {
                    if (field.is(':checked')) {
                        value = field.val();
                    } else {
                        return; // Skip unchecked radio buttons
                    }
                } else {
                    value = field.val() || '';
                }
                
                // Set nested value
                setNestedValue(formData, fieldPath, value);
            }
        });
        
        console.log('Form data to send:', formData);
        console.log('Active tab:', activeTab);
        
        // Send AJAX request
        $.ajax({
            url: khaneIraniSettings.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'khane_irani_save_settings',
                nonce: khaneIraniSettings.nonce,
                settings: formData,
                active_tab: activeTab
            },
            success: function(response) {
                submitBtn.prop('disabled', originalDisabled);
                if (submitBtn.is('input')) {
                    submitBtn.val(originalText);
                } else {
                    submitBtn.text(originalText);
                }
                
                if (response.success) {
                    // Show success notification
                    showNotification(response.data.message || '✅ تنظیمات با موفقیت ذخیره شد.', 'success');
                    
                    // Refresh the page after 1.5 seconds to show updated values
                    setTimeout(function() {
                        var savedTab = response.data.tab || activeTab || getUrlParameter('tab') || 'header';
                        window.location.href = '?page=khane-irani-settings&tab=' + savedTab + '&settings-updated=true';
                    }, 1500);
                } else {
                    showNotification(response.data.message || 'خطا در ذخیره تنظیمات', 'error');
                }
            },
            error: function(xhr, status, error) {
                submitBtn.prop('disabled', originalDisabled);
                if (submitBtn.is('input')) {
                    submitBtn.val(originalText);
                } else {
                    submitBtn.text(originalText);
                }
                
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                showNotification('خطا در ارتباط با سرور: ' + error, 'error');
            }
        });
        
        return false;
    });
    
    // Helper function to get URL parameter
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    // Show notification function
    function showNotification(message, type) {
        type = type || 'success';
        var notificationClass = type === 'success' ? 'notice-success' : 'notice-error';
        
        // Remove existing notifications
        $('.zarin-success-notification, .zarin-error-notification').remove();
        
        // Create notification
        var notification = $('<div class="notice ' + notificationClass + ' is-dismissible zarin-' + type + '-notification" style="margin: 20px 0; padding: 12px;"><p><strong>' + message + '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">بستن</span></button></div>');
        
        // Insert after h1
        $('.zarin-settings-wrap h1').after(notification);
        
        // Animate in
        notification.hide().fadeIn();
        
        // Make dismissible
        notification.on('click', '.notice-dismiss', function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        });
    }
    
    // Show notification if settings were saved on page load
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('settings-updated') === 'true') {
        showNotification('✅ تنظیمات با موفقیت ذخیره شد.', 'success');
    }
    
    // Real-time validation
    $(document).on('blur', 'input, textarea, select', function() {
        var field = $(this);
        var value = field.val() ? field.val().trim() : '';
        
        // Remove previous error class
        field.removeClass('error');
        
        // Check if field is required and empty
        if (field.attr('required') && !value) {
            field.addClass('error');
        }
        
        // Check email format
        if (field.attr('type') === 'email' && value) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.addClass('error');
            }
        }
    });
    
    // Color picker enhancement
    $(document).on('change', 'input[type="color"]', function() {
        var color = $(this).val();
        if (color) {
            $(this).css('background-color', color);
        }
    });
    
    // Initialize color pickers
    $('input[type="color"]').each(function() {
        var color = $(this).val();
        if (color) {
            $(this).css('background-color', color);
        }
    });
    
    // Show/hide conditional fields
    $(document).on('change', 'input[type="checkbox"]', function() {
        var checkbox = $(this);
        var fieldId = checkbox.attr('id');
        var conditionalFields = $('[data-depends-on="' + fieldId + '"]');
        
        if (checkbox.is(':checked')) {
            conditionalFields.show();
        } else {
            conditionalFields.hide();
        }
    });
    
    // Initialize conditional fields
    $('input[type="checkbox"]').each(function() {
        var checkbox = $(this);
        var fieldId = checkbox.attr('id');
        var conditionalFields = $('[data-depends-on="' + fieldId + '"]');
        
        if (checkbox.is(':checked')) {
            conditionalFields.show();
        } else {
            conditionalFields.hide();
        }
    });
    
    // Character counter for textareas
    $(document).on('input', 'textarea[maxlength]', function() {
        var textarea = $(this);
        var maxLength = parseInt(textarea.attr('maxlength'));
        var currentLength = textarea.val().length;
        var remaining = maxLength - currentLength;
        
        var counter = textarea.siblings('.char-counter');
        if (counter.length === 0) {
            counter = $('<div class="char-counter"></div>');
            textarea.after(counter);
        }
        
        counter.text(remaining + ' کاراکتر باقی مانده');
        
        if (remaining < 10) {
            counter.addClass('warning');
        } else {
            counter.removeClass('warning');
        }
    });
    
    // Initialize character counters
    $('textarea[maxlength]').each(function() {
        var textarea = $(this);
        var maxLength = parseInt(textarea.attr('maxlength'));
        var currentLength = textarea.val().length;
        var remaining = maxLength - currentLength;

        var counter = $('<div class="char-counter"></div>');
        textarea.after(counter);
        counter.text(remaining + ' کاراکتر باقی مانده');
    });

    // Values Items Management
    var valuesItemIndex = $('#values-items-container .values-item').length;

    // Add new values item
    $(document).on('click', '#add-values-item', function(e) {
        e.preventDefault();

        var newItem = createValuesItem(valuesItemIndex);
        $('#values-items-container').append(newItem);
        valuesItemIndex++;

        // Focus on the new emoji field
        newItem.find('.values-emoji').focus();
    });

    // Remove values item
    $(document).on('click', '.remove-values-item', function(e) {
        e.preventDefault();

        var item = $(this).closest('.values-item');
        var confirmDelete = confirm('آیا مطمئن هستید که می‌خواهید این ارزش را حذف کنید؟');

        if (confirmDelete) {
            item.fadeOut(300, function() {
                $(this).remove();
                updateValuesItemIndices();
            });
        }
    });

    // Function to create a new values item HTML
    function createValuesItem(index) {
        return `
            <div class="values-item" data-index="${index}">
                <div class="values-item-fields">
                    <input type="text"
                           name="khane-irani-settings[values_items][${index}][emoji]"
                           value=""
                           placeholder="ایموجی"
                           class="values-emoji"
                           maxlength="2" />
                    <input type="text"
                           name="khane-irani-settings[values_items][${index}][title]"
                           value=""
                           placeholder="عنوان ارزش"
                           class="values-title" />
                    <textarea name="khane-irani-settings[values_items][${index}][description]"
                              placeholder="توضیحات ارزش"
                              class="values-description"
                              rows="2"></textarea>
                </div>
                <button type="button" class="button remove-values-item" title="حذف این ارزش">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        `;
    }

    // Function to update indices after removing items
    function updateValuesItemIndices() {
        $('#values-items-container .values-item').each(function(newIndex) {
            var item = $(this);
            var oldIndex = item.data('index');

            // Update data-index
            item.attr('data-index', newIndex);

            // Update input names
            item.find('input, textarea').each(function() {
                var input = $(this);
                var name = input.attr('name');
                if (name) {
                    var newName = name.replace(/\[\d+\]/, '[' + newIndex + ']');
                    input.attr('name', newName);
                }
            });
        });

        // Update global index
        valuesItemIndex = $('#values-items-container .values-item').length;
    }
    
});

// CSS for error states and tooltips
jQuery(document).ready(function($) {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .error {
                border-color: var(--zs-teal-dark, #00A796) !important;
                box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.1) !important;
            }
            
            .char-counter {
                font-size: 12px;
                color: #666;
                margin-top: 5px;
            }
            
            .char-counter.warning {
                color: var(--zs-teal-dark, #00A796);
                font-weight: bold;
            }
        `)
        .appendTo('head');
});
