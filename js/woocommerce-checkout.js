/**
 * WooCommerce Checkout Page JavaScript
 * Professional checkout functionality and interactions
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

(function($) {
    'use strict';

    // Wait for both jQuery and DOM to be ready
    function initCheckoutCoupon() {
        // Debug: check if function is called
        console.log('Zarin Service: Checkout coupon init started');
        
        /**
         * Coupon toggle: make the banner click reveal the coupon form
         * Works for both standard form.checkout_coupon and fallback p.form-row markup
         */
        // Ensure coupon UI is hidden on load (both standard form and fallback rows)
        function hideCouponUI() {
            var $stdForm = $('form.checkout_coupon, #woocommerce-checkout-form-coupon');
            if ($stdForm.length) { 
                $stdForm.removeClass('show-coupon').hide().css({
                    'visibility': 'hidden',
                    'opacity': '0',
                    'display': 'none'
                });
            }
            // Fallback rows near the toggle (robust scan within checkout form)
            var $container = $('form.woocommerce-checkout');
            if ($container.length) {
                var $fallbackRows = $container.find('p.form-row').filter(function() {
                    var $row = $(this);
                    return $row.find('#coupon_code').length || $row.find('[name="apply_coupon"]').length || $row.find('button[name="apply_coupon"]').length;
                });
                if ($fallbackRows.length) {
                    console.log('Zarin Service: Found ' + $fallbackRows.length + ' coupon rows, hiding them');
                    $fallbackRows.hide().css({
                        'visibility': 'hidden',
                        'opacity': '0',
                        'display': 'none'
                    });
                }
            }
        }
        
        // Initialize aria-expanded attribute for custom toggle button
        $('#zs-coupon-toggle-btn').each(function() {
            if (!$(this).attr('aria-expanded')) {
                $(this).attr('aria-expanded', 'false');
            }
        });
        
        // Also initialize default WooCommerce toggle (if exists)
        $('.woocommerce-form-coupon-toggle .showcoupon').each(function() {
            if (!$(this).attr('aria-expanded')) {
                $(this).attr('aria-expanded', 'false');
            }
        });
        
        // Hide on page load
        hideCouponUI();
        // Also hide after DOM is ready (with multiple timeouts for WooCommerce AJAX)
        setTimeout(hideCouponUI, 100);
        setTimeout(hideCouponUI, 500);
        // Hide on checkout updates
        $(document.body).on('updated_checkout init_checkout', hideCouponUI);

        // Handle click on custom coupon toggle button
        $(document).on('click', '#zs-coupon-toggle-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Zarin Service: Custom coupon toggle clicked'); // Debug
            
            var $btn = $(this);
            var currentExpanded = $btn.attr('aria-expanded');
            var isExpanded = currentExpanded === 'true';
            
            // Find coupon form (standard form)
            var $form = $('form.checkout_coupon, #woocommerce-checkout-form-coupon');
            
            // Find coupon rows (p.form-row elements with coupon_code or apply_coupon)
            var $container = $('form.woocommerce-checkout');
            var $rows = $();
            if ($container.length) {
                $rows = $container.find('p.form-row').filter(function() {
                    var $row = $(this);
                    return $row.find('#coupon_code').length || $row.find('[name="apply_coupon"]').length || $row.find('button[name="apply_coupon"]').length;
                });
            }
            
            // If no form found, try siblings after toggle wrapper
            if (!$form.length && !$rows.length) {
                var $wrap = $btn.closest('.zs-custom-coupon-toggle-wrapper');
                $rows = $wrap.nextAll('p.form-row').filter(function() {
                    var $row = $(this);
                    return $row.find('#coupon_code').length || $row.find('[name="apply_coupon"]').length || $row.find('button[name="apply_coupon"]').length;
                });
            }
            
            var toggled = false;
            
            // Toggle form
            if ($form.length) {
                console.log('Zarin Service: Toggling coupon form');
                if (isExpanded) {
                    // Hide form
                    $form.removeClass('show-coupon').stop(true, true).slideUp(250, function() {
                        $(this).css({
                            'visibility': 'hidden',
                            'opacity': '0',
                            'display': 'none'
                        });
                    });
                } else {
                    // Show form
                    $form.addClass('show-coupon').css({
                        'visibility': 'visible',
                        'opacity': '0',
                        'display': 'flex'
                    }).stop(true, true).animate({
                        'opacity': '1'
                    }, 250);
                }
                toggled = true;
            } 
            // Toggle rows
            else if ($rows.length) {
                console.log('Zarin Service: Toggling coupon rows (' + $rows.length + ' found)');
                if (isExpanded) {
                    // Hide rows - simple hide
                    console.log('Zarin Service: Hiding coupon rows');
                    $rows.each(function() {
                        var $row = $(this);
                        $row.removeClass('show-coupon');
                        var currentStyle = $row.attr('style') || '';
                        // Remove display/visibility/opacity from style
                        currentStyle = currentStyle.replace(/display\s*:\s*[^;]+;?/gi, '');
                        currentStyle = currentStyle.replace(/visibility\s*:\s*[^;]+;?/gi, '');
                        currentStyle = currentStyle.replace(/opacity\s*:\s*[^;]+;?/gi, '');
                        $row.attr('style', currentStyle + 'display: none !important;');
                    });
                } else {
                    // Show rows - simple show with !important override
                    console.log('Zarin Service: Showing coupon rows');
                    $rows.each(function() {
                        var $row = $(this);
                        $row.addClass('show-coupon');
                        // Use attr to set inline style with !important
                        var currentStyle = $row.attr('style') || '';
                        $row.attr('style', currentStyle + 'display: block !important; visibility: visible !important; opacity: 1 !important;');
                    });
                    console.log('Zarin Service: Rows after show:', $rows.length, $rows.is(':visible'));
                }
                toggled = true;
            }
            
            // Toggle aria-expanded
            $btn.attr('aria-expanded', (!isExpanded).toString());
            
            if (!toggled) {
                console.warn('Zarin Service: Could not find coupon form to toggle');
            }
        });

        // Also handle default WooCommerce toggle (if exists) as fallback
        $(document).on('click', '.woocommerce-form-coupon-toggle .showcoupon', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Zarin Service: Default coupon toggle clicked'); // Debug
            
            var $link = $(this);
            var $container = $('form.woocommerce-checkout');
            var $form = $('form.checkout_coupon, #woocommerce-checkout-form-coupon');
            var toggled = false;
            var currentExpanded = $link.attr('aria-expanded');
            var isExpanded = currentExpanded === 'true';
            
            if ($form.length) {
                if (isExpanded) {
                    // Hide form
                    $form.removeClass('show-coupon').stop(true, true).slideUp(250, function() {
                        $(this).css({
                            'visibility': 'hidden',
                            'opacity': '0',
                            'display': 'none'
                        });
                    });
                } else {
                    // Show form
                    $form.addClass('show-coupon').css({
                        'visibility': 'visible',
                        'opacity': '0',
                        'display': 'flex'
                    }).stop(true, true).animate({
                        'opacity': '1'
                    }, 250);
                }
                toggled = true;
            }
            // Fallback rows anywhere inside checkout form (input + button rows)
            if ($container.length && !toggled) {
                var $fallbackRows = $container.find('p.form-row').filter(function() {
                    var $row = $(this);
                    return $row.find('#coupon_code').length || $row.find('[name="apply_coupon"]').length;
                });
                if ($fallbackRows.length) {
                    if (isExpanded) {
                        $fallbackRows.stop(true, true).slideUp(250, function() {
                            $(this).css({
                                'visibility': 'hidden',
                                'opacity': '0',
                                'display': 'none'
                            });
                        });
                    } else {
                        $fallbackRows.css({
                            'visibility': 'visible',
                            'opacity': '0',
                            'display': 'block'
                        }).stop(true, true).animate({
                            'opacity': '1'
                        }, 250).slideDown(250);
                    }
                    toggled = true;
                }
            }
            if (!toggled) {
                // Last resort: try immediate siblings after toggle wrapper
                var $wrap = $link.closest('.woocommerce-form-coupon-toggle');
                var $siblings = $wrap.nextAll('p.form-row').slice(0, 2);
                if ($siblings.length) {
                    if (isExpanded) {
                        $siblings.stop(true, true).slideUp(250);
                    } else {
                        $siblings.stop(true, true).slideDown(250);
                    }
                }
            }
            // Toggle aria-expanded
            $link.attr('aria-expanded', (!isExpanded).toString());
        });
    }

    // Run when DOM is ready
    $(document).ready(function() {
        console.log('Zarin Service: Checkout JS loaded and ready'); // Debug
        initCheckoutCoupon();
        
        /**
         * AJAX remove item from checkout order list
         */
        $(document.body).on('click', '.zs-remove-item', function(e) {
            e.preventDefault();
            var $link = $(this);
            var url = $link.data('remove-url');
            if (!url) return;
            $link.addClass('is-loading');
            $.get(url)
                .always(function() {
                    // refresh fragments / totals
                    $(document.body).trigger('update_checkout');
                });
        });

        /**
         * Form Validation Enhancement
         */
        function validateCheckoutForm() {
            var isValid = true;
            var firstErrorField = null;

            // Validate required fields
            $('.woocommerce-checkout input[required], .woocommerce-checkout select[required]').each(function() {
                var $field = $(this);
                var value = $field.val();

                // Remove previous error styling
                $field.removeClass('error-field');

                if (!value || value.trim() === '') {
                    isValid = false;
                    $field.addClass('error-field');
                    
                    if (!firstErrorField) {
                        firstErrorField = $field;
                    }
                } else {
                    // Validate email format
                    if ($field.attr('type') === 'email' && value) {
                        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            isValid = false;
                            $field.addClass('error-field');
                            if (!firstErrorField) {
                                firstErrorField = $field;
                            }
                        }
                    }

                    // Validate phone format (Iranian)
                    if ($field.attr('type') === 'tel' && value) {
                        var phoneRegex = /^(\+98|0)?9\d{9}$/;
                        var cleanPhone = value.replace(/[\s\-\(\)]/g, '');
                        if (!phoneRegex.test(cleanPhone)) {
                            isValid = false;
                            $field.addClass('error-field');
                            if (!firstErrorField) {
                                firstErrorField = $field;
                            }
                        }
                    }
                }
            });

            // Scroll to first error
            if (!isValid && firstErrorField) {
                $('html, body').animate({
                    scrollTop: firstErrorField.offset().top - 100
                }, 500);
                
                firstErrorField.focus();
            }

            return isValid;
        }

        /**
         * Real-time Field Validation
         */
        $('.woocommerce-checkout input, .woocommerce-checkout select').on('blur', function() {
            var $field = $(this);
            var value = $field.val();

            if ($field.attr('required') && (!value || value.trim() === '')) {
                $field.addClass('error-field');
            } else {
                $field.removeClass('error-field');
            }

            // Email validation
            if ($field.attr('type') === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    $field.addClass('error-field');
                } else {
                    $field.removeClass('error-field');
                }
            }
        });

        /**
         * Place Order Button Handler
         */
        $('#place_order, .wc-block-components-checkout-place-order-button').on('click', function(e) {
            if (!validateCheckoutForm()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Show error message
                if (!$('.checkout-validation-error').length) {
                    $('.woocommerce-checkout').prepend(
                        '<div class="checkout-validation-error woocommerce-error" style="display:block;margin-bottom:20px;">' +
                        'لطفاً تمام فیلدهای الزامی را به درستی پر کنید.' +
                        '</div>'
                    );
                    
                    setTimeout(function() {
                        $('.checkout-validation-error').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 5000);
                }
                
                return false;
            }
        });

        /**
         * Copy Billing to Shipping Address
         */
        if ($('#ship-to-different-address-checkbox').length) {
            $('#ship-to-different-address-checkbox').on('change', function() {
                if (!$(this).is(':checked')) {
                    // Copy billing fields to shipping
                    $('.woocommerce-billing-fields input, .woocommerce-billing-fields select').each(function() {
                        var name = $(this).attr('name');
                        if (name) {
                            var shippingName = name.replace('billing_', 'shipping_');
                            var $shippingField = $('[name="' + shippingName + '"]');
                            if ($shippingField.length) {
                                $shippingField.val($(this).val()).trigger('change');
                            }
                        }
                    });
                }
            });
        }

        /**
         * Payment Method Selection Animation
         */
        $('.wc_payment_methods input[type="radio"]').on('change', function() {
            // Hide all payment boxes
            $('.payment_box').slideUp(200);
            
            // Show selected payment box
            var $selectedBox = $(this).closest('.wc_payment_method').find('.payment_box');
            if ($selectedBox.length) {
                $selectedBox.slideDown(300);
            }
        });

        // Show initially selected payment method box
        $('.wc_payment_methods input[type="radio"]:checked').each(function() {
            $(this).closest('.wc_payment_method').find('.payment_box').show();
        });

        /**
         * Order Review Sticky Position (Desktop)
         */
        if ($(window).width() > 992) {
            var $orderReview = $('#order_review, .wc-block-checkout__sidebar');
            var originalTop = $orderReview.offset().top;
            var stickyOffset = 100;
            
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > originalTop - stickyOffset) {
                    $orderReview.css({
                        'position': 'sticky',
                        'top': stickyOffset + 'px'
                    });
                } else {
                    $orderReview.css({
                        'position': 'static'
                    });
                }
            });
        }

        /**
         * Smooth Scroll to Payment Section
         */
        $('.wc_payment_methods input[type="radio"]').on('change', function() {
            if ($(window).width() <= 992) {
                setTimeout(function() {
                    var offset = $('.woocommerce-checkout-payment').offset().top - 80;
                    $('html, body').animate({
                        scrollTop: offset
                    }, 300);
                }, 350);
            }
        });

        /**
         * Terms and Conditions Checkbox Handler
         */
        $('#terms').on('change', function() {
            var $placeOrder = $('#place_order');
            if ($(this).is(':checked')) {
                $placeOrder.prop('disabled', false);
            } else {
                $placeOrder.prop('disabled', true);
            }
        });

        /**
         * Loading State on Form Submit
         */
        $('.woocommerce-checkout').on('submit', function() {
            var $form = $(this);
            var $submitButton = $('#place_order, .wc-block-components-checkout-place-order-button');
            
            // Disable submit button
            $submitButton.prop('disabled', true);
            $submitButton.text('در حال پردازش...');
            
            // Add loading overlay
            if (!$('.checkout-loading-overlay').length) {
                $form.append(
                    '<div class="checkout-loading-overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;">' +
                    '<div style="background:#fff;padding:30px;border-radius:15px;text-align:center;max-width:300px;">' +
                    '<div style="border:4px solid #f3f3f3;border-top:4px solid var(--zs-teal-medium, #37C3B3);border-radius:50%;width:50px;height:50px;animation:spin 1s linear infinite;margin:0 auto 20px;"></div>' +
                    '<p style="font-weight:600;color:var(--zs-teal-dark, #00A796);">در حال پردازش سفارش...</p>' +
                    '<p style="font-size:14px;color:#6c757d;margin-top:10px;">لطفاً صبر کنید</p>' +
                    '</div></div>'
                );
            }
            
            $('.checkout-loading-overlay').fadeIn(200);
        });

        // Add spin animation
        if (!$('#checkout-spin-animation').length) {
            $('head').append(
                '<style id="checkout-spin-animation">' +
                '@keyframes spin {' +
                '  0% { transform: rotate(0deg); }' +
                '  100% { transform: rotate(360deg); }' +
                '}' +
                '.error-field {' +
                '  border-color: #dc3545 !important;' +
                '  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;' +
                '}' +
                '</style>'
            );
        }

        /**
         * Address Autocomplete Enhancement (if Google Places API is available)
         */
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            var addressFields = [
                '#billing_address_1',
                '#shipping_address_1'
            ];

            addressFields.forEach(function(fieldId) {
                var $field = $(fieldId);
                if ($field.length) {
                    var autocomplete = new google.maps.places.Autocomplete($field[0], {
                        componentRestrictions: {country: 'ir'}
                    });

                    autocomplete.addListener('place_changed', function() {
                        var place = autocomplete.getPlace();
                        // You can populate other address fields here if needed
                    });
                }
            });
        }

        /**
         * Mobile Navigation for Checkout Steps
         */
        if ($(window).width() <= 768) {
            $('.wc-block-components-checkout-step__title').on('click', function() {
                $(this).next('.wc-block-components-checkout-step__container').slideToggle(300);
            });
        }

        /**
         * Form Field Auto-fill from Browser (enhancement)
         */
        $('.woocommerce-checkout input').on('input', function() {
            $(this).removeClass('error-field');
        });
        
        // Also run after WooCommerce initializes (for AJAX updates)
        $(document.body).on('init_checkout updated_checkout', function() {
            setTimeout(initCheckoutCoupon, 100);
        });
    });

    /**
     * Window Resize Handler
     */
    $(window).on('resize', function() {
        // Reset sticky positions on resize
        $('#order_review, .wc-block-checkout__sidebar').css({
            'position': 'static'
        });
    });

})(jQuery);

