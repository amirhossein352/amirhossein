/**
 * WooCommerce Cart Page JavaScript
 * Professional cart functionality and interactions
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        /**
         * Cart Quantity Update Handler
         */
        var cartUpdateTimeout;
        
        $(document).on('change', '.woocommerce-cart-form input[name*="[qty]"]', function() {
            var $button = $('button[name="update_cart"]');
            var originalText = $button.text();
            
            // Clear previous timeout
            clearTimeout(cartUpdateTimeout);
            
            // Disable button and show loading state
            $button.prop('disabled', true);
            $button.text('در حال به‌روزرسانی...');
            
            // Set timeout to allow user to change multiple quantities
            cartUpdateTimeout = setTimeout(function() {
                $button.css('opacity', '1');
                $button.text('به‌روزرسانی سبد خرید');
            }, 500);
            
            // Auto-update cart after 1 second of inactivity
            cartUpdateTimeout = setTimeout(function() {
                $button.click();
            }, 1000);
        });

        /**
         * Remove Item Animation
         */
        $(document).on('click', '.product-remove a', function(e) {
            var $row = $(this).closest('tr');
            
            // Add fade out animation
            $row.fadeOut(300, function() {
                // Row will be removed by WooCommerce
            });
        });

        /**
         * Coupon Code Enter Key Handler
         */
        $(document).on('keypress', '#coupon_code', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('button[name="apply_coupon"]').click();
            }
        });

        /**
         * Smooth Scroll to Cart Totals on Mobile
         */
        if ($(window).width() <= 768) {
            $('.cart-collaterals').on('mouseenter', function() {
                var offset = $(this).offset().top - 20;
                $('html, body').animate({
                    scrollTop: offset
                }, 300);
            });
        }

        /**
         * Empty Cart Message Enhancement
         */
        if ($('.cart-empty').length) {
            // Add return to shop button if not exists
            if (!$('.return-to-shop').length) {
                $('.cart-empty').after(
                    '<p class="return-to-shop"><a class="button wc-backward" href="' + 
                    wc_add_to_cart_params.shop_url + 
                    '">بازگشت به فروشگاه</a></p>'
                );
            }
        }

        /**
         * Update Cart Button Loading State
         */
        $(document).on('click', 'button[name="update_cart"]', function(e) {
            var $button = $(this);
            var originalText = $button.text();
            
            $button.prop('disabled', true);
            $button.text('در حال به‌روزرسانی...');
            
            // Re-enable after form submission
            setTimeout(function() {
                $button.prop('disabled', false);
                $button.text(originalText);
            }, 2000);
        });

        /**
         * Responsive Table Enhancement
         */
        if ($(window).width() <= 768) {
            // Add data-title attributes for responsive display
            $('.woocommerce-cart-form__contents tbody td').each(function() {
                var $cell = $(this);
                var headerText = $cell.closest('table')
                    .find('thead th')
                    .eq($cell.index())
                    .text();
                
                if (headerText && !$cell.attr('data-title')) {
                    $cell.attr('data-title', headerText);
                }
            });
        }

        /**
         * Cart Totals Sticky on Scroll (Desktop)
         */
        if ($(window).width() > 768) {
            var $cartTotals = $('.cart_totals');
            var originalTop = $cartTotals.offset().top;
            var stickyOffset = 100;
            
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > originalTop - stickyOffset) {
                    $cartTotals.css({
                        'position': 'sticky',
                        'top': stickyOffset + 'px'
                    });
                } else {
                    $cartTotals.css({
                        'position': 'static'
                    });
                }
            });
        }

        /**
         * Loading Animation on Form Submit
         */
        $('.woocommerce-cart-form').on('submit', function() {
            var $form = $(this);
            $form.addClass('cart-updating');
            
            // Add loading overlay
            if (!$('.cart-loading-overlay').length) {
                $form.append('<div class="cart-loading-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:999;border-radius:20px;"><div style="text-align:center;"><div style="border:4px solid #f3f3f3;border-top:4px solid var(--zs-teal-medium, #37C3B3);border-radius:50%;width:50px;height:50px;animation:spin 1s linear infinite;margin:0 auto 10px;"></div><p>در حال به‌روزرسانی...</p></div></div>');
            }
            
            $('.cart-loading-overlay').fadeIn(200);
        });

        // Add spin animation
        if (!$('#cart-spin-animation').length) {
            $('head').append('<style id="cart-spin-animation">@keyframes spin {0% { transform: rotate(0deg); }100% { transform: rotate(360deg); }}</style>');
        }

        /**
         * Quantity Increment/Decrement Buttons (if using custom quantity controls)
         */
        $(document).on('click', '.quantity button', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $input = $button.siblings('input[type="number"]');
            var currentVal = parseInt($input.val()) || 0;
            var max = parseInt($input.attr('max')) || 999;
            var min = parseInt($input.attr('min')) || 0;
            
            if ($button.hasClass('plus')) {
                if (currentVal < max) {
                    $input.val(currentVal + 1).trigger('change');
                }
            } else if ($button.hasClass('minus')) {
                if (currentVal > min) {
                    $input.val(currentVal - 1).trigger('change');
                }
            }
        });

    });

})(jQuery);

