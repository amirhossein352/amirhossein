<?php
/**
 * The Template for displaying product archives, including the main shop page
 * Modern Minimal Design inspired by RTL-Theme
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

defined('ABSPATH') || exit;

// Debug: Check if our template is being used
echo '<!-- DEBUG: Custom archive-product.php template is being used -->';

get_header('shop'); ?>

<style>
/* Minimal archive header override */
.zs-product-page-header.zs-shop-page-header {
    background: #f9fafb;
    color: #0f172a;
    padding: 18px 0 22px;
    margin: 0 0 20px;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1px solid #e5e7eb;
    min-height: auto;
}
.zs-product-page-header.zs-shop-page-header::after,
.zs-header-decoration,
.zs-floating-shapes,
.zs-header-actions,
.zs-product-subtitle,
.zs-product-meta {
    display: none !important;
}
.zs-product-page-header .container {
    padding: 0 16px;
}
.zs-breadcrumb-nav {
    margin-bottom: 12px;
    display: block;
}
.zs-breadcrumb-list{
    display:flex;
    gap:6px;
    flex-wrap:wrap;
    padding:0;
    margin:0;
    list-style:none;
}
.zs-breadcrumb-item{
    color:#475569;
    font-size:14px;
    font-weight:600;
}
.zs-breadcrumb-link{
    color:#475569;
    text-decoration:none;
    font-weight:600;
}
.zs-breadcrumb-link:hover{color:#0f172a;}
.zs-breadcrumb-separator{color:#cbd5e1;font-weight:700;}
.zs-breadcrumb-current{color:#0f172a;font-weight:700;}
.zs-product-title-lg {
    font-size: 24px;
    margin: 0;
    color: #0f172a;
    font-weight: 700;
}
@media (max-width: 768px) {
    .zs-product-title-lg { font-size: 20px; }
    .zs-product-page-header .container { padding: 0 12px; }
}
</style>

<div class="zs-shop-wrapper">
    <header class="zs-product-page-header zs-shop-page-header">
        <div class="container">
            <nav class="zs-breadcrumb-nav" aria-label="مسیر ناوبری">
                <ol class="zs-breadcrumb-list">
                    <li class="zs-breadcrumb-item">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="zs-breadcrumb-link">
                            <i class="fas fa-home"></i>
                            خانه
                        </a>
                    </li>
                    <li class="zs-breadcrumb-separator">›</li>
                    <li class="zs-breadcrumb-item zs-breadcrumb-current">
                        <?php woocommerce_page_title(); ?>
                    </li>
                </ol>
            </nav>

            <div class="zs-header-content">
                <div class="zs-title-section">
                    <h1 class="zs-product-title-lg"><?php woocommerce_page_title(); ?></h1>
                </div>
            </div>

            <?php
            /**
             * Hook: woocommerce_archive_description.
             */
            do_action('woocommerce_archive_description');
            ?>
        </div>
    </header>

    <div class="container">
        <!-- Shop Content with Sidebar -->
        <div class="zs-shop-layout">
            
            <!-- Ultra Modern Sidebar -->
            <aside class="zs-ultra-modern-sidebar">
                
                <!-- Premium Search -->
                <div style="margin-bottom: 40px !important;">
                    <div style="position: relative !important;">
                        <input type="text" placeholder="جستجو در محصولات..." style="
                            width: 100% !important;
                            padding: 16px 20px 16px 50px !important;
                            border: 2px solid #e9ecef !important;
                            border-radius: 15px !important;
                            font-size: 14px !important;
                            background: #ffffff !important;
                            transition: all 0.3s ease !important;
                            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05) !important;
                        " onfocus="this.style.borderColor='var(--zs-teal-medium, #37C3B3)'; this.style.boxShadow='0 0 0 3px rgba(52, 152, 219, 0.1)';" onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='inset 0 2px 10px rgba(0, 0, 0, 0.05)';">
                        <svg style="position: absolute !important; right: 18px !important; top: 50% !important; transform: translateY(-50%) !important; color: #adb5bd !important;" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Price Filter - اولین فیلتر -->
                <div style="margin-bottom: 40px !important;">
                    <h4 style="
                        font-size: 19px !important; 
                        font-weight: 800 !important; 
                        background: linear-gradient(135deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3)) !important;
                        -webkit-background-clip: text !important;
                        -webkit-text-fill-color: transparent !important;
                        background-clip: text !important;
                        margin-bottom: 25px !important;
                    ">💰 فیلتر قیمت</h4>
                    <div id="price-filter" style="
                        background: #ffffff !important;
                        padding: 20px !important;
                        border-radius: 15px !important;
                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
                        overflow: hidden !important;
                        box-sizing: border-box !important;
                    ">
                        <?php
                        // دریافت محدوده قیمت از محصولات
                        global $wpdb;
                        $price_range = $wpdb->get_row("
                            SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price, 
                                   MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price
                            FROM {$wpdb->postmeta}
                            WHERE meta_key = '_price' AND meta_value != ''
                        ");
                        
                        $min_range = $price_range ? floatval($price_range->min_price) : 0;
                        $max_range = $price_range ? floatval($price_range->max_price) : 1000000;
                        
                        // دریافت مقادیر فعلی از URL
                        $current_min = isset($_GET['min_price']) ? floatval($_GET['min_price']) : $min_range;
                        $current_max = isset($_GET['max_price']) ? floatval($_GET['max_price']) : $max_range;
                        ?>
                        
                        <!-- Range Slider Container -->
                        <div style="margin-bottom: 20px !important; width: 100% !important; box-sizing: border-box !important;">
                            <!-- نمایش مقادیر -->
                            <div style="display: flex !important; justify-content: space-between !important; align-items: center !important; margin-bottom: 15px !important; flex-wrap: wrap !important; gap: 10px !important;">
                                <div style="display: flex !important; align-items: center !important; gap: 8px !important; flex: 1 !important; min-width: 120px !important;">
                                    <span style="font-size: 13px !important; color: #6c757d !important; font-weight: 600 !important; white-space: nowrap !important;">حداقل:</span>
                                    <span id="price-min-display" style="
                                        font-size: 16px !important;
                                        font-weight: 700 !important;
                                        color: var(--zs-teal-dark, #00A796) !important;
                                        direction: ltr !important;
                                        font-family: 'IRANSans', 'Tahoma', sans-serif !important;
                                        white-space: nowrap !important;
                                    "><?php echo number_format($current_min); ?></span>
                                    <span style="font-size: 12px !important; color: #9ca3af !important; white-space: nowrap !important;">تومان</span>
                                </div>
                                <div style="display: flex !important; align-items: center !important; gap: 8px !important; flex: 1 !important; min-width: 120px !important;">
                                    <span style="font-size: 13px !important; color: #6c757d !important; font-weight: 600 !important; white-space: nowrap !important;">حداکثر:</span>
                                    <span id="price-max-display" style="
                                        font-size: 16px !important;
                                        font-weight: 700 !important;
                                        color: var(--zs-teal-dark, #00A796) !important;
                                        direction: ltr !important;
                                        font-family: 'IRANSans', 'Tahoma', sans-serif !important;
                                        white-space: nowrap !important;
                                    "><?php echo number_format($current_max); ?></span>
                                    <span style="font-size: 12px !important; color: #9ca3af !important; white-space: nowrap !important;">تومان</span>
                                </div>
                            </div>
                            
                            <!-- Range Slider Wrapper -->
                            <div id="price-range-wrapper" style="position: relative !important; height: 40px !important; margin-bottom: 15px !important; width: 100% !important; box-sizing: border-box !important; overflow: visible !important;">
                                <div style="position: absolute !important; top: 50% !important; left: 0 !important; right: 0 !important; height: 6px !important; background: #e9ecef !important; border-radius: 5px !important; transform: translateY(-50%) !important; width: 100% !important;"></div>
                                <input type="range" 
                                       id="price-range-min" 
                                       min="<?php echo esc_attr($min_range); ?>" 
                                       max="<?php echo esc_attr($max_range); ?>" 
                                       value="<?php echo esc_attr($current_min); ?>"
                                       step="<?php echo esc_attr(max(1, ($max_range - $min_range) / 100)); ?>"
                                       style="
                                           position: absolute !important;
                                           top: 50% !important;
                                           left: 0 !important;
                                           right: 0 !important;
                                           width: 100% !important;
                                           height: 6px !important;
                                           background: transparent !important;
                                           outline: none !important;
                                           -webkit-appearance: none !important;
                                           z-index: 2 !important;
                                           transform: translateY(-50%) !important;
                                           margin: 0 !important;
                                           padding: 0 !important;
                                       ">
                                <input type="range" 
                                       id="price-range-max" 
                                       min="<?php echo esc_attr($min_range); ?>" 
                                       max="<?php echo esc_attr($max_range); ?>" 
                                       value="<?php echo esc_attr($current_max); ?>"
                                       step="<?php echo esc_attr(max(1, ($max_range - $min_range) / 100)); ?>"
                                       style="
                                           position: absolute !important;
                                           top: 50% !important;
                                           left: 0 !important;
                                           right: 0 !important;
                                           width: 100% !important;
                                           height: 6px !important;
                                           background: transparent !important;
                                           outline: none !important;
                                           -webkit-appearance: none !important;
                                           z-index: 3 !important;
                                           transform: translateY(-50%) !important;
                                           margin: 0 !important;
                                           padding: 0 !important;
                                       ">
                            </div>
                            
                            <!-- Hidden inputs برای ارسال -->
                            <input type="hidden" id="min_price" name="min_price" value="<?php echo esc_attr($current_min); ?>">
                            <input type="hidden" id="max_price" name="max_price" value="<?php echo esc_attr($current_max); ?>">
                        </div>
                        
                        <button type="button" 
                                id="apply-price-filter" 
                                style="
                                    width: 100% !important;
                                    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796)) !important;
                                    color: white !important;
                                    padding: 14px 20px !important;
                                    border: none !important;
                                    border-radius: 10px !important;
                                    font-size: 15px !important;
                                    font-weight: 700 !important;
                                    cursor: pointer !important;
                                    transition: all 0.3s ease !important;
                                    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3) !important;
                                "
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(52, 152, 219, 0.5)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(52, 152, 219, 0.3)';">
                            اعمال فیلتر
                        </button>
                        <?php if ($current_min > $min_range || $current_max < $max_range): ?>
                        <button type="button" 
                                id="clear-price-filter" 
                                style="
                                    width: 100% !important;
                                    background: #f8f9fa !important;
                                    color: #6c757d !important;
                                    padding: 10px 20px !important;
                                    border: 2px solid #e9ecef !important;
                                    border-radius: 10px !important;
                                    font-size: 14px !important;
                                    font-weight: 600 !important;
                                    cursor: pointer !important;
                                    margin-top: 10px !important;
                                    transition: all 0.3s ease !important;
                                "
                                onmouseover="this.style.background='#e9ecef';"
                                onmouseout="this.style.background='#f8f9fa';">
                            پاک کردن فیلتر
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Premium Category Filter -->
                <div style="margin-bottom: 40px !important;">
                    <h4 style="
                        font-size: 19px !important; 
                        font-weight: 800 !important; 
                        background: linear-gradient(135deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3)) !important;
                        -webkit-background-clip: text !important;
                        -webkit-text-fill-color: transparent !important;
                        background-clip: text !important;
                        margin-bottom: 25px !important;
                    ">🏷️ دسته‌بندی‌ها</h4>
                    <div style="display: flex !important; flex-direction: column !important; gap: 12px !important;">
                        <?php
                        $product_categories = get_terms('product_cat', array('hide_empty' => true));
                        if ($product_categories) {
                            foreach ($product_categories as $category) {
                                echo '<a href="' . get_term_link($category) . '" style="
                                    display: flex !important; 
                                    align-items: center !important; 
                                    justify-content: space-between !important;
                                    background: #ffffff !important;
                                    padding: 15px 20px !important;
                                    border-radius: 15px !important;
                                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
                                    transition: all 0.3s ease !important;
                                    border: 2px solid transparent !important;
                                    text-decoration: none !important;
                                    color: var(--zs-teal-dark, #00A796) !important;
                                    font-weight: 600 !important;
                                " onmouseover="
                                    this.style.borderColor=\'var(--zs-teal-medium, #37C3B3)\';
                                    this.style.transform=\'translateY(-2px)\';
                                    this.style.boxShadow=\'0 8px 25px rgba(52, 152, 219, 0.2)\';
                                    this.style.color=\'var(--zs-teal-medium, #37C3B3)\';
                                " onmouseout="
                                    this.style.borderColor=\'transparent\';
                                    this.style.transform=\'translateY(0)\';
                                    this.style.boxShadow=\'0 4px 15px rgba(0, 0, 0, 0.08)\';
                                    this.style.color=\'var(--zs-teal-dark, #00A796)\';
                                ">';
                                echo '<span>' . $category->name . '</span>';
                                echo '<span style="
                                    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796)) !important;
                                    color: white !important;
                                    padding: 6px 12px !important;
                                    border-radius: 15px !important;
                                    font-size: 12px !important;
                                    font-weight: 700 !important;
                                ">' . $category->count . '</span>';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Premium Quick Access -->
                <div style="margin-bottom: 40px !important;">
                    <h4 style="
                        font-size: 19px !important; 
                        font-weight: 800 !important; 
                        background: linear-gradient(135deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3)) !important;
                        -webkit-background-clip: text !important;
                        -webkit-text-fill-color: transparent !important;
                        background-clip: text !important;
                        margin-bottom: 25px !important;
                    ">⚡ دسترسی سریع</h4>
                    <div style="display: flex !important; flex-direction: column !important; gap: 12px !important;">
                        <a href="<?php echo home_url('/shop'); ?>" style="
                            display: flex !important;
                            align-items: center !important;
                            gap: 15px !important;
                            background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796)) !important; 
                            color: white !important; 
                            padding: 16px 20px !important; 
                            text-align: center !important; 
                            border-radius: 15px !important; 
                            text-decoration: none !important; 
                            font-size: 15px !important;
                            font-weight: 700 !important;
                            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4) !important;
                            transition: all 0.3s ease !important;
                        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 35px rgba(52, 152, 219, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(52, 152, 219, 0.4)';">
                            <span style="font-size: 20px;">💼</span>
                            <span>همه محصولات</span>
                        </a>
                        <a href="<?php echo home_url('/shop?orderby=date'); ?>" style="
                            display: flex !important;
                            align-items: center !important;
                            gap: 15px !important;
                            background: linear-gradient(135deg, #27ae60, #2ecc71) !important; 
                            color: white !important; 
                            padding: 16px 20px !important; 
                            text-align: center !important; 
                            border-radius: 15px !important; 
                            text-decoration: none !important; 
                            font-size: 15px !important;
                            font-weight: 700 !important;
                            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.4) !important;
                            transition: all 0.3s ease !important;
                        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 35px rgba(39, 174, 96, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(39, 174, 96, 0.4)';">
                            <span style="font-size: 20px;">🆕</span>
                            <span>جدیدترین‌ها</span>
                        </a>
                        <a href="<?php echo home_url('/shop?featured=yes'); ?>" style="
                            display: flex !important;
                            align-items: center !important;
                            gap: 15px !important;
                            background: linear-gradient(135deg, var(--zs-teal-light, #6DE0D0), #e67e22) !important; 
                            color: white !important; 
                            padding: 16px 20px !important; 
                            text-align: center !important; 
                            border-radius: 15px !important; 
                            text-decoration: none !important; 
                            font-size: 15px !important;
                            font-weight: 700 !important;
                            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4) !important;
                            transition: all 0.3s ease !important;
                        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 35px rgba(243, 156, 18, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(243, 156, 18, 0.4)';">
                            <span style="font-size: 20px;">⭐</span>
                            <span>محصولات ویژه</span>
                        </a>
                    </div>
                </div>
                
                <!-- Premium Support Section -->
                <div style="
                    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3) 0%, #764ba2 100%) !important;
                    padding: 25px !important;
                    border-radius: 20px !important;
                    color: white !important;
                    text-align: center !important;
                    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
                ">
                    <div style="font-size: 40px !important; margin-bottom: 15px !important;">🎧</div>
                    <h4 style="font-size: 18px !important; font-weight: 700 !important; margin-bottom: 10px !important;">نیاز به کمک؟</h4>
                    <p style="font-size: 14px !important; margin-bottom: 20px !important; opacity: 0.9 !important;">تیم پشتیبانی ما آماده کمک به شماست</p>
                    <a href="<?php echo home_url('/contact'); ?>" style="
                        background: rgba(255, 255, 255, 0.2) !important;
                        border: 2px solid rgba(255, 255, 255, 0.3) !important;
                        color: white !important;
                        padding: 12px 20px !important;
                        border-radius: 12px !important;
                        text-decoration: none !important;
                        font-weight: 700 !important;
                        display: inline-block !important;
                        transition: all 0.3s ease !important;
                        backdrop-filter: blur(10px) !important;
                    " onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='scale(1)';">
                        تماس با ما
                    </a>
                </div>
                
            </aside>

            <!-- Products Content -->
            <div class="zs-shop-content">
            <?php 
            // استفاده از روش استاندارد WooCommerce - مطابق فایل اصلی
            if (woocommerce_product_loop()) : ?>
                
                <!-- Shop Controls - حذف شده -->

                <!-- Products Grid -->
                <div class="zs-products-grid" id="products-container">
                    <?php
                    woocommerce_product_loop_start();
                    
                    if (wc_get_loop_prop('total')) {
                        while (have_posts()) {
                            the_post();
                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action('woocommerce_shop_loop');
                            
                            wc_get_template_part('content', 'product');
                        }
                    }

                    woocommerce_product_loop_end();
                    ?>
                </div>

                <!-- Pagination -->
                <div class="zs-shop-pagination">
                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop.
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>
                </div>

            <?php else : ?>
                
                <!-- No Products Found -->
                <div class="zs-no-products">
                    <div class="no-products-icon">💼</div>
                    <h3>هیچ محصولی یافت نشد</h3>
                    <p>متأسفانه محصولی با این مشخصات پیدا نکردیم. لطفاً فیلترها را تغییر دهید یا دوباره جستجو کنید.</p>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn-primary">بازگشت به فروشگاه</a>
                </div>
                
                <?php
                /**
                 * Hook: woocommerce_no_products_found.
                 */
                do_action('woocommerce_no_products_found');
                ?>

            <?php endif; ?>
            </div> <!-- End zs-shop-content -->
        </div> <!-- End zs-shop-layout -->

        <!-- Shop Features -->
        <section class="zs-shop-features">
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <h4>تحویل سریع</h4>
                    <p>ارائه سریع و با کیفیت تمامی محصولات</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🛡️</div>
                    <h4>ضمانت کیفیت</h4>
                    <p>تضمین اصالت و کیفیت تمامی محصولات</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">💳</div>
                    <h4>پرداخت امن</h4>
                    <p>پرداخت آنلاین امن با درگاه زرین‌پال</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📞</div>
                    <h4>پشتیبانی 24/7</h4>
                    <p>پاسخگویی در تمام ساعات شبانه‌روز</p>
                </div>
            </div>
        </section>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyBtn = document.getElementById('apply-price-filter');
    const clearBtn = document.getElementById('clear-price-filter');
    const minRange = document.getElementById('price-range-min');
    const maxRange = document.getElementById('price-range-max');
    const minPriceInput = document.getElementById('min_price');
    const maxPriceInput = document.getElementById('max_price');
    const minDisplay = document.getElementById('price-min-display');
    const maxDisplay = document.getElementById('price-max-display');
    
    // به‌روزرسانی نمایش مقادیر هنگام تغییر range
    function updatePriceDisplay() {
        if (minRange && maxRange && minDisplay && maxDisplay && minPriceInput && maxPriceInput) {
            let minVal = parseFloat(minRange.value);
            let maxVal = parseFloat(maxRange.value);
            
            // اطمینان از اینکه min کمتر از max باشد
            if (minVal > maxVal) {
                const temp = minVal;
                minVal = maxVal;
                maxVal = temp;
                minRange.value = minVal;
                maxRange.value = maxVal;
            }
            
            // به‌روزرسانی نمایش
            minDisplay.textContent = new Intl.NumberFormat('fa-IR').format(Math.round(minVal));
            maxDisplay.textContent = new Intl.NumberFormat('fa-IR').format(Math.round(maxVal));
            minPriceInput.value = Math.round(minVal);
            maxPriceInput.value = Math.round(maxVal);
        }
    }
    
    // رویدادهای range slider
    if (minRange) {
        minRange.addEventListener('input', updatePriceDisplay);
    }
    
    if (maxRange) {
        maxRange.addEventListener('input', updatePriceDisplay);
    }
    
    // اعمال فیلتر قیمت با ایجکس
    if (applyBtn) {
        applyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const minPrice = minPriceInput.value;
            const maxPrice = maxPriceInput.value;
            const productsContainer = document.getElementById('products-container');
            
            // نمایش loading
            if (productsContainer) {
                productsContainer.style.opacity = '0.5';
                productsContainer.style.pointerEvents = 'none';
            }
            applyBtn.disabled = true;
            applyBtn.textContent = 'در حال بارگذاری...';
            
            // ارسال درخواست ایجکس
            const formData = new FormData();
            formData.append('action', 'filter_products_by_price_ajax');
            formData.append('min_price', minPrice);
            formData.append('max_price', maxPrice);
            formData.append('nonce', '<?php echo wp_create_nonce("price_filter_ajax_nonce"); ?>');
            
            // حفظ پارامترهای دیگر از URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('product_cat')) {
                formData.append('category', urlParams.get('product_cat'));
            }
            if (urlParams.get('orderby')) {
                formData.append('orderby', urlParams.get('orderby'));
            }
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.html) {
                    // به‌روزرسانی محصولات
                    if (productsContainer) {
                        productsContainer.innerHTML = data.html;
                    }
                    
                    // به‌روزرسانی URL بدون رفرش
                    const url = new URL(window.location.href);
                    url.searchParams.delete('min_price');
                    url.searchParams.delete('max_price');
                    if (minPrice && parseFloat(minPrice) > 0) {
                        url.searchParams.set('min_price', minPrice);
                    }
                    if (maxPrice && parseFloat(maxPrice) > 0) {
                        url.searchParams.set('max_price', maxPrice);
                    }
                    window.history.pushState({}, '', url.toString());
                } else {
                    alert('خطا در فیلتر کردن محصولات');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('خطا در ارتباط با سرور');
            })
            .finally(() => {
                if (productsContainer) {
                    productsContainer.style.opacity = '1';
                    productsContainer.style.pointerEvents = 'auto';
                }
                applyBtn.disabled = false;
                applyBtn.textContent = 'اعمال فیلتر';
            });
        });
    }
    
    // پاک کردن فیلتر با ایجکس
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const productsContainer = document.getElementById('products-container');
            
            // نمایش loading
            if (productsContainer) {
                productsContainer.style.opacity = '0.5';
                productsContainer.style.pointerEvents = 'none';
            }
            clearBtn.disabled = true;
            clearBtn.textContent = 'در حال بارگذاری...';
            
            // ارسال درخواست ایجکس بدون فیلتر قیمت
            const formData = new FormData();
            formData.append('action', 'filter_products_by_price_ajax');
            formData.append('min_price', '');
            formData.append('max_price', '');
            formData.append('nonce', '<?php echo wp_create_nonce("price_filter_ajax_nonce"); ?>');
            
            // حفظ پارامترهای دیگر از URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('product_cat')) {
                formData.append('category', urlParams.get('product_cat'));
            }
            if (urlParams.get('orderby')) {
                formData.append('orderby', urlParams.get('orderby'));
            }
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.html) {
                    // به‌روزرسانی محصولات
                    if (productsContainer) {
                        productsContainer.innerHTML = data.html;
                    }
                    
                    // به‌روزرسانی URL بدون رفرش
                    const url = new URL(window.location.href);
                    url.searchParams.delete('min_price');
                    url.searchParams.delete('max_price');
                    window.history.pushState({}, '', url.toString());
                    
                    // ریست کردن range slider
                    if (minRange && maxRange) {
                        minRange.value = minRange.min;
                        maxRange.value = maxRange.max;
                        updatePriceDisplay();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('خطا در ارتباط با سرور');
            })
            .finally(() => {
                if (productsContainer) {
                    productsContainer.style.opacity = '1';
                    productsContainer.style.pointerEvents = 'auto';
                }
                clearBtn.disabled = false;
                clearBtn.textContent = 'پاک کردن فیلتر';
            });
        });
    }
});
</script>

<style>
/* استایل range slider */
#price-filter {
    overflow: visible !important;
}

#price-range-wrapper {
    overflow: visible !important;
    padding: 10px 0 !important;
}

#price-range-min,
#price-range-max {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    outline: none;
    pointer-events: none;
    margin: 0 !important;
    padding: 0 !important;
}

#price-range-min::-webkit-slider-thumb,
#price-range-max::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: var(--zs-teal-medium, #37C3B3);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    pointer-events: all;
    border: 3px solid #ffffff;
    margin-top: -6px;
}

#price-range-min::-webkit-slider-thumb:hover,
#price-range-max::-webkit-slider-thumb:hover {
    background: var(--zs-teal-dark, #00A796);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(55, 195, 179, 0.4);
}

#price-range-min::-moz-range-thumb,
#price-range-max::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: var(--zs-teal-medium, #37C3B3);
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    pointer-events: all;
}

#price-range-min::-moz-range-thumb:hover,
#price-range-max::-moz-range-thumb:hover {
    background: var(--zs-teal-dark, #00A796);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(55, 195, 179, 0.4);
}

#price-range-min::-moz-range-track,
#price-range-max::-moz-range-track {
    background: transparent;
    height: 6px;
}
</style>

<?php
get_footer('shop');
