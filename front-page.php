<?php
/** * Template Name: home

 * The front page template file
 * 
 * ساختار صفحه اصلی بر اساس نیازمندی‌های مستند:
 * 1. اسلایدر اصلی (Hero Slider)
 * 2. بخش ارزش‌های خرید از خانه ایرانی
 * 3. دسته‌بندی محصولات
 * 4. کاروسل محصولات تخفیف‌دار
 * 5. کاروسل جدیدترین محصولات
 * 6. سکشن سوالات متداول (FAQ)
 * 7. کاروسل‌های اختیاری محصولات (تا ۴ کاروسل)
 * 8. آخرین مطالب بلاگ
 * 9. بخش محتوای سئو (قبل از فوتر)
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    
    <?php
    // 1. اسلایدر اصلی (Hero Slider)
    $hero_enabled = khane_irani_get_setting('hero_slider_enabled', '1');
    echo '<!-- Hero slider enabled: ' . $hero_enabled . ' -->';
    if ($hero_enabled === '1') {
        get_template_part('template-parts/front-page/banner-slider-section');
    }

    // 2. بخش ارزش‌های خرید از خانه ایرانی
    if (khane_irani_get_setting('values_enabled', '1') === '1') {
        get_template_part('template-parts/front-page/values-section');
    }

    // 3. دسته‌بندی محصولات (با کاروسل در موبایل)
    get_template_part('template-parts/front-page/categories-section');

    // 4. کاروسل محصولات تخفیف‌دار (با تایمر)
    if (khane_irani_get_setting('on_sale_products_enabled', '1') === '1') {
        get_template_part('template-parts/front-page/on-sale-products-section');
    }

    // 5. کاروسل جدیدترین محصولات
    if (khane_irani_get_setting('latest_products_enabled', '1') === '1') {
        get_template_part('template-parts/front-page/latest-products-section-new');
    }

    // 6. سکشن سوالات متداول (FAQ)
    if (khane_irani_get_setting('faq_enabled', '1') === '1') {
        get_template_part('template-parts/front-page/faq-section');
    }

    // 7. کاروسل‌های اختیاری محصولات (تا ۴ کاروسل)
    if (khane_irani_get_setting('custom_carousels_enabled', '0') === '1') {
        $custom_carousels = khane_irani_get_setting('custom_product_carousels', array());
        if (!empty($custom_carousels) && is_array($custom_carousels)) {
            $active_carousels = array_filter($custom_carousels, function($carousel) {
                return !isset($carousel['is_active']) || $carousel['is_active'] === '1';
            });
            
            // Sort by sort_order
            usort($active_carousels, function($a, $b) {
                $orderA = isset($a['sort_order']) ? intval($a['sort_order']) : 0;
                $orderB = isset($b['sort_order']) ? intval($b['sort_order']) : 0;
                return $orderA <=> $orderB;
            });
            
            // محدود به ۴ کاروسل
            $active_carousels = array_slice($active_carousels, 0, 4);
            
            foreach ($active_carousels as $index => $carousel) {
                set_query_var('carousel_data', $carousel);
                set_query_var('carousel_index', $index);
                get_template_part('template-parts/front-page/custom-products-carousel');
            }
        }
    }

    // 8. آخرین مطالب بلاگ
    if (khane_irani_get_setting('latest_posts_enabled', '1') === '1') {
        get_template_part('template-parts/front-page/latest-posts-section');
    }

    // Note: SEO Content Section is now displayed in footer.php for all pages
    ?>

</main>

<?php get_footer(); ?>
