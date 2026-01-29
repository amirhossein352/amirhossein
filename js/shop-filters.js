/**
 * Modern Shop Filters with Ajax
 * For Zarin Service Theme
 */

(function($) {
    'use strict';

    const ShopFilters = {
        // Configuration
        config: {
            ajaxUrl: wc_add_to_cart_params?.ajax_url || '/wp-admin/admin-ajax.php',
            nonce: '',
            container: '.zs-products-grid',
            loadingClass: 'zs-loading',
            noResultsClass: 'zs-no-results'
        },

        // Current filters state
        filters: {
            categories: [],
            priceMin: '',
            priceMax: '',
            rating: '',
            stock: [],
            orderby: 'menu_order'
        },

        // Initialize
        init: function() {
            this.bindEvents();
            this.setupMobileToggle();
            console.log('Shop Filters initialized');
        },

        // Bind events
        bindEvents: function() {
            // Category checkboxes
            $(document).on('change', '.category-filter input[type="checkbox"]', this.handleCategoryFilter.bind(this));
            
            // Price filter
            $(document).on('click', '.apply-price-filter', this.handlePriceFilter.bind(this));
            $(document).on('keypress', '.price-filter input', function(e) {
                if (e.which === 13) {
                    ShopFilters.handlePriceFilter();
                }
            });
            
            // Rating filter
            $(document).on('change', '.rating-filter input[type="radio"]', this.handleRatingFilter.bind(this));
            
            // Stock filter
            $(document).on('change', '.stock-filter input[type="checkbox"]', this.handleStockFilter.bind(this));
            
            // Sort filter
            $(document).on('change', '.sort-select', this.handleSortFilter.bind(this));
            
            // Clear filters
            $(document).on('click', '.clear-filters', this.clearAllFilters.bind(this));
            
            // Quick view
            $(document).on('click', '.quick-view', this.handleQuickView.bind(this));
            
            // Add to wishlist
            $(document).on('click', '.add-to-wishlist', this.handleWishlist.bind(this));
        },

        // Handle category filter
        handleCategoryFilter: function() {
            this.filters.categories = [];
            $('.category-filter input[type="checkbox"]:checked').each(function() {
                ShopFilters.filters.categories.push($(this).val());
            });
            this.applyFilters();
        },

        // Handle price filter
        handlePriceFilter: function() {
            this.filters.priceMin = $('#min-price').val();
            this.filters.priceMax = $('#max-price').val();
            this.applyFilters();
        },

        // Handle rating filter
        handleRatingFilter: function() {
            this.filters.rating = $('.rating-filter input[type="radio"]:checked').val() || '';
            this.applyFilters();
        },

        // Handle stock filter
        handleStockFilter: function() {
            this.filters.stock = [];
            $('.stock-filter input[type="checkbox"]:checked').each(function() {
                ShopFilters.filters.stock.push($(this).val());
            });
            this.applyFilters();
        },

        // Handle sort filter
        handleSortFilter: function() {
            this.filters.orderby = $('.sort-select').val();
            this.applyFilters();
        },

        // Apply all filters with Ajax
        applyFilters: function() {
            const $container = $(this.config.container);
            const $sidebar = $('.zs-shop-sidebar');
            
            // Show loading
            this.showLoading();
            
            // Prepare filter data
            const filterData = {
                action: 'zs_filter_products',
                categories: this.filters.categories,
                price_min: this.filters.priceMin,
                price_max: this.filters.priceMax,
                rating: this.filters.rating,
                stock: this.filters.stock,
                orderby: this.filters.orderby,
                nonce: this.config.nonce
            };

            // Ajax request
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: filterData,
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data.products);
                        ShopFilters.updateResultsCount(response.data.found_posts);
                        ShopFilters.hideLoading();
                        
                        // Trigger custom event for other scripts
                        $(document).trigger('shop_filters_applied', [response.data]);
                    } else {
                        console.error('Filter error:', response.data);
                        ShopFilters.hideLoading();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    ShopFilters.hideLoading();
                }
            });
        },

        // Clear all filters
        clearAllFilters: function() {
            // Reset filter state
            this.filters = {
                categories: [],
                priceMin: '',
                priceMax: '',
                rating: '',
                stock: [],
                orderby: 'menu_order'
            };
            
            // Reset form elements
            $('.category-filter input[type="checkbox"]').prop('checked', false);
            $('#min-price, #max-price').val('');
            $('.rating-filter input[type="radio"]').prop('checked', false);
            $('.stock-filter input[type="checkbox"]').prop('checked', false);
            $('.sort-select').val('menu_order');
            
            // Apply filters (will show all products)
            this.applyFilters();
        },

        // Show loading state
        showLoading: function() {
            const $container = $(this.config.container);
            $container.addClass(this.config.loadingClass);
            
            // Add loading overlay
            if (!$container.find('.loading-overlay').length) {
                $container.append('<div class="loading-overlay"><div class="loading-spinner">🔄</div><p>در حال بارگذاری...</p></div>');
            }
        },

        // Hide loading state
        hideLoading: function() {
            const $container = $(this.config.container);
            $container.removeClass(this.config.loadingClass);
            $container.find('.loading-overlay').remove();
        },

        // Update results count
        updateResultsCount: function(count) {
            $('.woocommerce-result-count').text(count + ' محصول یافت شد');
        },

        // Handle quick view
        handleQuickView: function(e) {
            e.preventDefault();
            const productId = $(e.currentTarget).data('product-id');
            
            // Simple quick view - open in new tab for now
            // Can be enhanced with modal later
            const productUrl = $(e.currentTarget).closest('.zs-product-card').find('.zs-product-link').attr('href');
            window.open(productUrl, '_blank');
        },

        // Handle wishlist
        handleWishlist: function(e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const productId = $button.data('product-id');
            
            // Toggle wishlist state
            if ($button.hasClass('added')) {
                $button.removeClass('added').attr('title', 'افزودن به علاقه‌مندی‌ها');
                $button.text('❤️');
            } else {
                $button.addClass('added').attr('title', 'حذف از علاقه‌مندی‌ها');
                $button.text('💖');
            }
            
            // Here you can add actual wishlist functionality
            console.log('Wishlist toggled for product:', productId);
        },

        // Setup mobile toggle for sidebar
        setupMobileToggle: function() {
            // Add mobile filter toggle button
            // Ensure button is visible on mobile
            if ($('.mobile-filter-toggle').length) {
                const showOrHide = function(){
                    if (window.matchMedia('(max-width: 768px)').matches) {
                        $('.mobile-filter-toggle').css('display', 'inline-flex');
                    } else {
                        $('.mobile-filter-toggle').css('display', 'none');
                    }
                };
                showOrHide();
                $(window).on('resize', function(){ showOrHide(); });
            } else if ($('.zs-products-grid').length) {
                $('.zs-products-grid').before('<div class="zs-shop-controls"><button class="mobile-filter-toggle">🔍 فیلترها</button></div>');
            }
            
            // Handle mobile toggle
            $(document).on('click', '.mobile-filter-toggle', function() {
                const $sidebar = $('.zs-ultra-modern-sidebar, .zs-shop-sidebar');
                const isOpen = $sidebar.hasClass('mobile-open');
                if (!isOpen) {
                    // open: add overlay
                    if (!$('.sidebar-overlay').length) {
                        $('body').append('<div class="sidebar-overlay"></div>');
                    }
                    $sidebar.addClass('mobile-open');
                    $('body').addClass('sidebar-open');
                } else {
                    $sidebar.removeClass('mobile-open');
                    $('body').removeClass('sidebar-open');
                    $('.sidebar-overlay').remove();
                }
            });
            
            // Close on overlay click
            $(document).on('click', '.sidebar-overlay', function() {
                $('.zs-ultra-modern-sidebar, .zs-shop-sidebar').removeClass('mobile-open');
                $('body').removeClass('sidebar-open');
                $('.sidebar-overlay').remove();
            });

            // Close on Escape
            $(document).on('keydown', function(e){
                if (e.key === 'Escape') {
                    $('.zs-ultra-modern-sidebar, .zs-shop-sidebar').removeClass('mobile-open');
                    $('body').removeClass('sidebar-open');
                    $('.sidebar-overlay').remove();
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        ShopFilters.init();
        
        // Force apply our styles immediately
        ForceShopStyles.apply();

        // Re-apply on resize (debounced)
        let zsResizeTimer;
        $(window).on('resize', function(){
            clearTimeout(zsResizeTimer);
            zsResizeTimer = setTimeout(function(){ ForceShopStyles.apply(); }, 150);
        });
    });

    // Force Shop Styles Object
    const ForceShopStyles = {
        apply: function() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            // Layout
            const layout = document.querySelector('.zs-shop-layout');
            if (layout && !isMobile) {
                layout.style.display = 'flex';
                layout.style.gap = '30px';
                layout.style.margin = '40px 0';
            }

            // Sidebar
            const sidebar = document.querySelector('.zs-ultra-modern-sidebar') || document.querySelector('.zs-shop-sidebar');
            if (sidebar) {
                if (isMobile) {
                    // clear inline styles that break off-canvas
                    try {
                        sidebar.removeAttribute('style');
                    } catch(e){}
                    // ensure starts closed
                    sidebar.classList.remove('mobile-open');
                    document.body.classList.remove('sidebar-open');
                } else {
                    sidebar.style.width = '280px';
                    sidebar.style.background = '#f8f9fa';
                    sidebar.style.padding = '25px';
                    sidebar.style.borderRadius = '15px';
                    sidebar.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                    sidebar.style.display = 'block';
                    sidebar.style.flexShrink = '0';
                }
            }

            // Content area (desktop only)
            const content = document.querySelector('.zs-shop-content');
            if (content && !isMobile) {
                content.style.flex = '1';
            }

            // Products grid (desktop hint only; CSS handles mobile)
            const productsGrid = document.querySelector('.woocommerce ul.products');
            if (productsGrid && !isMobile) {
                productsGrid.style.display = 'grid';
                productsGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                productsGrid.style.gap = '25px';
                productsGrid.style.listStyle = 'none';
                productsGrid.style.padding = '0';
                productsGrid.style.margin = '0';
            }

            // Product cards (desktop aesthetics)
            if (!isMobile) {
                const productCards = document.querySelectorAll('.zs-simple-product-card');
                productCards.forEach(card => {
                    card.style.background = '#ffffff';
                    card.style.borderRadius = '15px';
                    card.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
                    card.style.overflow = 'hidden';
                    card.style.height = '100%';
                    card.style.display = 'flex';
                    card.style.flexDirection = 'column';
                });
            }
        }
    };

    // Expose to global scope
    window.ZarinShopFilters = ShopFilters;

})(jQuery);
