<?php
/**
 * The template for displaying product content in the single-product.php template
 * Premium Modern E-commerce Design - Optimized
 *
 * @package khane_irani
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('zs-premium-single-product', $product); ?> data-product-id="<?php echo esc_attr(get_the_ID()); ?>">

    <!-- Enhanced Product Header -->
    <header class="zs-product-page-header">
        <!-- Eslimi Decorative Patterns -->
        <div class="zs-header-eslimi-pattern zs-header-eslimi-top-left"></div>
        <div class="zs-header-eslimi-pattern zs-header-eslimi-bottom-right"></div>
        <div class="zs-header-eslimi-pattern zs-header-eslimi-center"></div>
        
        <div class="container">
            <!-- Breadcrumb Navigation -->
            <nav class="zs-breadcrumb-nav" aria-label="مسیر ناوبری">
                <ol class="zs-breadcrumb-list">
                    <li class="zs-breadcrumb-item">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="zs-breadcrumb-link">
                            <i class="fas fa-home"></i>
                            خانه
                        </a>
                    </li>
                    <li class="zs-breadcrumb-separator">›</li>
                    <li class="zs-breadcrumb-item">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="zs-breadcrumb-link">
                            فروشگاه
                        </a>
                    </li>
                    <li class="zs-breadcrumb-separator">›</li>
                    <li class="zs-breadcrumb-item zs-breadcrumb-current">
                        <?php the_title(); ?>
                    </li>
                </ol>
            </nav>

            <!-- Product Title and Meta -->
            <div class="zs-header-content">
                <div class="zs-title-section">
                    <h1 class="zs-product-title-lg"><?php the_title(); ?></h1>
                </div>

            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="zs-header-decoration">
            <div class="zs-floating-shapes">
                <div class="zs-shape zs-shape-1"></div>
                <div class="zs-shape zs-shape-2"></div>
                <div class="zs-shape zs-shape-3"></div>
            </div>
        </div>
    </header>

    <!-- Premium Product Hero Section -->
    <section class="zs-product-hero-section">
        <div class="container">
            
            <?php woocommerce_output_all_notices(); ?>
            
            <div class="service-hero-grid">
                
                <!-- Product Gallery Section -->
                <div class="zs-gallery-section">
                    <div class="zs-gallery-card">
                        <?php
                        global $product;
                        $attachment_ids = $product->get_gallery_image_ids();
                        $post_thumbnail_id = $product->get_image_id();
                        
                        if ($post_thumbnail_id || !empty($attachment_ids)) :
                            ?>
                            <div class="zs-product-gallery">
                                <!-- Main Image -->
                                <div class="zs-gallery-main">
                                    <div class="zs-featured-image-container">
                                        <?php
                                        if ($post_thumbnail_id) {
                                            $main_image = wp_get_attachment_image_url($post_thumbnail_id, 'large');
                                            $main_image_full = wp_get_attachment_image_url($post_thumbnail_id, 'full');
                                        } else {
                                            $main_image = wc_placeholder_img_src('large');
                                            $main_image_full = wc_placeholder_img_src('full');
                                        }
                                        ?>
                                        <img src="<?php echo esc_url($main_image); ?>" 
                                             alt="<?php echo esc_attr(get_the_title()); ?>" 
                                             class="zs-featured-image zs-main-image"
                                             data-full="<?php echo esc_url($main_image_full); ?>"
                                             loading="eager" />
                                    </div>
                                </div>
                                
                                <!-- Thumbnail Gallery -->
                                <?php if (!empty($attachment_ids) || $post_thumbnail_id) : ?>
                                <div class="zs-gallery-thumbnails">
                                    <?php
                                    // Show featured image as first thumbnail
                                    if ($post_thumbnail_id) {
                                        $thumb_url = wp_get_attachment_image_url($post_thumbnail_id, 'thumbnail');
                                        $thumb_full = wp_get_attachment_image_url($post_thumbnail_id, 'full');
                                        ?>
                                        <div class="zs-thumbnail-item active" data-image="<?php echo esc_url($thumb_full); ?>">
                                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                        </div>
                                        <?php
                                    }
                                    
                                    // Show gallery images
                                    foreach ($attachment_ids as $attachment_id) {
                                        $thumb_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                                        $thumb_full = wp_get_attachment_image_url($attachment_id, 'full');
                                        $thumb_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true) ?: get_the_title();
                                        ?>
                                        <div class="zs-thumbnail-item" data-image="<?php echo esc_url($thumb_full); ?>">
                                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($thumb_alt); ?>" />
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="zs-no-image-placeholder">
                                <i class="fas fa-image"></i>
                                <p>تصویر محصول موجود نیست</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Service Info Section -->
<div class="service-info-section">
    <div class="service-info-container">
        <?php
        /**
         * Hook: woocommerce_single_product_summary.
         */
        do_action('woocommerce_single_product_summary');
        
        global $product;
        $product_id = $product->get_id();
        ?>
        
        <!-- Enhanced Add to Cart Section -->
        <div class="zs-enhanced-cart-section">
            <div class="zs-cart-actions">
                <button type="button" class="zs-add-to-cart-btn zs-primary-btn" id="zs-custom-add-to-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span>افزودن به سبد خرید</span>
                </button>
            </div>
            
            <!-- Product Quick Info -->
            <div class="zs-quick-info">
                <div class="zs-info-item">
                    <i class="fas fa-clock"></i>
                    <span>تحویل فوری</span>
                </div>
                <div class="zs-info-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>ضمانت کیفیت</span>
                </div>
                <div class="zs-info-item">
                    <i class="fas fa-headset"></i>
                    <span>پشتیبانی ۲۴/۷</span>
                </div>
            </div>
        </div>
        
        <!-- Hidden field for product ID -->
        <input type="hidden" id="zs-current-product-id" value="<?php echo esc_attr($product_id); ?>">
    </div>
</div>

<script>
// اطلاعات محصول را به صورت global منتقل می‌کنیم
window.zsProductData = {
    productId: <?php echo $product_id; ?>,
    isVariable: <?php echo $product->is_type('variable') ? 'true' : 'false'; ?>
};
</script>

            </div>
        </div>
    </section>

    <!-- Portfolio Section - Above Description -->
    <?php
    $portfolio = get_post_meta(get_the_ID(), '_zs_service_portfolio_arr', true);
    
    if (!is_array($portfolio) || empty($portfolio)) {
        $portfolio = [];
    }
    
    if (is_array($portfolio) && !empty($portfolio)):
    ?>
    <div class="zs-portfolio-section">
        <div class="container">
            <div class="zs-portfolio-header">
                <h3 class="zs-portfolio-title">نمونه‌کارهای مرتبط<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M1 2a1 1 0 0 1 1-1h3v4H1V2zm5 0h4v4H6V2zm5-1h3a1 1 0 0 1 1 1v3h-4V1zM1 7h4v4H1V7zm5 0h4v4H6V7zm5 0h4v4h-4V7zM1 12h4v3H2a1 1 0 0 1-1-1v-2zm5 0h4v4H6v-4zm5 0h4v2a1 1 0 0 1-1 1h-3v-3z"/></svg></span></h3>
                <p class="zs-portfolio-subtitle">مشاهده پروژه‌های موفق ما</p>
            </div>
            <div class="zs-portfolio-showcase">
                <?php foreach ($portfolio as $index => $pf):
                    $pt = isset($pf['title']) ? esc_html($pf['title']) : '';
                    $pi = isset($pf['image']) ? esc_url($pf['image']) : '';
                    ?>
                    <div class="zs-portfolio-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>" data-title="<?php echo esc_attr($pt); ?>">
                        <div class="zs-portfolio-link">
                            <div class="zs-portfolio-image-container">
                                <?php if ($pi): ?>
                                    <img src="<?php echo $pi; ?>" alt="<?php echo $pt; ?>" loading="lazy" />
                                    <div class="zs-portfolio-gradient"></div>
                                <?php else: ?>
                                    <div class="zs-portfolio-no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Enhanced Product Description Section -->
    <section class="zs-description-section">
        <div class="container">
            <div class="zs-description-container">
                <div class="zs-description-header">
                    <h3 class="zs-description-title">معرفی محصول : <?php echo esc_html(get_the_title()); ?><span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M4 0h5.5L14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5V5h3.5L9.5 1.5z"/><path d="M5 7.5h6v1H5v-1zm0 2.5h6v1H5v-1zm0 2.5h6v1H5v-1z"/></svg></span></h3>
                    <p class="zs-description-subtitle">جزئیات کامل این محصول</p>
                </div>
                
                <div class="zs-description-content">
                    <div class="zs-description-text">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Steps Section - Below Description -->
    <?php
    $process_steps = get_post_meta(get_the_ID(), '_zs_service_process_arr', true);
    if (is_array($process_steps) && !empty($process_steps)):
    ?>
    <section class="zs-process-section">
        <div class="container">
            <div class="zs-process-header">
                <h3 class="zs-process-title">مراحل انجام پروژه<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-6zm2.5.5v5h5v-5h-5z"/><path d="M2.5 5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-6z"/><path d="M2 9.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-3zm2.5.5v2h5v-2h-5z"/></svg></span></h3>
                <p class="zs-process-subtitle">روند کار ما در چند مرحله ساده</p>
            </div>
            
            <div class="zs-process-container">
                <div class="zs-process-steps">
                    <?php foreach ($process_steps as $index => $step):
                        $step_num = isset($step['step']) ? intval($step['step']) : ($index + 1);
                        $step_title = isset($step['title']) ? esc_html($step['title']) : '';
                        $step_desc = isset($step['description']) ? esc_html($step['description']) : '';
                        $step_image = isset($step['image']) ? esc_url($step['image']) : '';
                        if ($step_title === '' && $step_desc === '' && $step_image === '') continue;
                        $is_last = ($index === count($process_steps) - 1);
                        ?>
                        <div class="zs-process-step-wrapper" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="zs-process-step">
                                <div class="zs-process-step-header">
                                    <?php if ($step_image): ?>
                                        <div class="zs-process-step-image">
                                            <img src="<?php echo $step_image; ?>" alt="<?php echo $step_title; ?>" loading="lazy" />
                                        </div>
                                    <?php endif; ?>
                                    <div class="zs-process-step-number"><?php echo $step_num; ?></div>
                                </div>
                                <div class="zs-process-step-content">
                                    <?php if ($step_title): ?>
                                        <h4 class="zs-process-step-title"><?php echo $step_title; ?></h4>
                                    <?php endif; ?>
                                    <?php if ($step_desc): ?>
                                        <p class="zs-process-step-description"><?php echo $step_desc; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Premium Product Details Section -->
    <section class="zs-product-details-section">
        <div class="container">
            <div class="details-container">
                <?php
                // Enhanced FAQ Section (accordion)
                $faqs = get_post_meta(get_the_ID(), '_zs_service_faq_arr', true);
                
                // Add test FAQ if no FAQs exist
                if (!is_array($faqs) || empty($faqs)) {
                    $faqs = [
                        [
                            'q' => 'سوال تست اول',
                            'a' => 'پاسخ تست اول - این یک سوال تستی است برای بررسی عملکرد FAQ'
                        ],
                        [
                            'q' => 'سوال تست دوم', 
                            'a' => 'پاسخ تست دوم - این هم یک سوال تستی دیگر است'
                        ]
                    ];
                }
                
                if (is_array($faqs) && !empty($faqs)):
                ?>
                <div class="zs-faq-section">
                    <div class="zs-faq-header">
                        <h3 class="zs-faq-title">سوالات متداول<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z" fill-opacity=".1"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.5-1.206 1.066-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.001zM8 12a.905.905 0 1 0 0-1.81A.905.905 0 0 0 8 12z"/></svg></span></h3>
                        <p class="zs-faq-subtitle">پاسخ سوالات رایج شما</p>
                    </div>
                    <div class="zs-faq-container">
                        <div class="zs-faq-list">
                            <?php foreach ($faqs as $index => $fq):
                                $q = isset($fq['q']) ? esc_html($fq['q']) : '';
                                $a = isset($fq['a']) ? esc_html($fq['a']) : '';
                                if ($q === '' && $a === '') continue; ?>
                                <div class="zs-faq-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                                    <div class="zs-faq-card">
                                        <button class="zs-faq-question" type="button">
                                            <div class="zs-faq-question-content">
                                                <span class="zs-faq-question-text"><?php echo $q; ?></span>
                                                <div class="zs-faq-question-icon">
                                                    <i class="fas fa-plus"></i>
                                                </div>
                                            </div>
                                        </button>
                                        <div class="zs-faq-answer">
                                            <div class="zs-faq-answer-content">
                                                <p><?php echo $a; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Related Products Section -->
                <section class="zs-related-products-section" id="related-products">
                    <div class="container">
                        <div class="zs-related-header">
                            <h3 class="zs-related-title">محصولات مرتبط<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M1 2a1 1 0 0 1 1-1h3v4H1V2zm5 0h4v4H6V2zm5-1h3a1 1 0 0 1 1 1v3h-4V1zM1 7h4v4H1V7zm5 0h4v4H6V7zm5 0h4v4h-4V7zM1 12h4v3H2a1 1 0 0 1-1-1v-2zm5 0h4v4H6v-4zm5 0h4v2a1 1 0 0 1-1 1h-3v-3z"/></svg></span></h3>
                            <p class="zs-related-subtitle">محصولات مشابه که ممکن است علاقه‌مند باشید</p>
                        </div>
                        
                        <?php
                        global $product;
                        $related_products = wc_get_related_products($product->get_id(), 8);
                        
                        if (!empty($related_products)) :
                        ?>
                        <div class="zs-related-carousel-wrapper">
                            <div class="swiper zs-related-swiper" id="relatedProductsCarousel">
                                <div class="swiper-wrapper">
                                    <?php
                                    $related_index = 0;
                                    foreach ($related_products as $related_product_id) {
                                        $related_product = wc_get_product($related_product_id);
                                        if (!$related_product || !$related_product->is_visible()) continue;
                                        
                                        $related_image = get_the_post_thumbnail_url($related_product_id, 'medium');
                                        $related_title = $related_product->get_name();
                                        $related_price = $related_product->get_price_html();
                                        $related_link = get_permalink($related_product_id);
                                        ?>
                                        <div class="swiper-slide">
                                            <div class="zs-related-card" data-aos="fade-up" data-aos-delay="<?php echo $related_index * 100; ?>">
                                                <a href="<?php echo esc_url($related_link); ?>" class="zs-related-link">
                                                    <div class="zs-related-image-wrapper">
                                                        <?php if ($related_image) : ?>
                                                            <img src="<?php echo esc_url($related_image); ?>" alt="<?php echo esc_attr($related_title); ?>" loading="lazy" />
                                                            <div class="zs-related-overlay">
                                                                <i class="fas fa-eye"></i>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="zs-related-placeholder">
                                                                <i class="fas fa-image"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="zs-related-content">
                                                        <h4 class="zs-related-card-title"><?php echo esc_html($related_title); ?></h4>
                                                        <div class="zs-related-price"><?php echo $related_price; ?></div>
                                                        <div class="zs-related-action">
                                                            <span class="zs-related-btn-text">مشاهده محصول</span>
                                                            <i class="fas fa-arrow-left"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <?php
                                        $related_index++;
                                    }
                                    ?>
                                </div>
                                <div class="zs-related-nav zs-related-prev"><i class="fas fa-chevron-right"></i></div>
                                <div class="zs-related-nav zs-related-next"><i class="fas fa-chevron-left"></i></div>
                            </div>
                        </div>
                        <?php else : ?>
                        <div class="zs-no-related">
                            <div class="zs-no-related-content">
                                <i class="fas fa-box-open"></i>
                                <h4>محصول مرتبطی یافت نشد</h4>
                                <p>در حال حاضر محصول مرتبطی برای نمایش وجود ندارد</p>
                                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="zs-shop-link">
                                    <span>مشاهده فروشگاه</span>
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php
                // Enhanced Reviews Section
                ?>
                <section class="zs-reviews-section" id="reviews">
                    <div class="zs-reviews-header">
                        <h3 class="zs-reviews-title">نظرات مشتریان<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3.612 15.443 8 12.973l4.388 2.47-1.17-4.73 3.64-3.045-4.822-.393L8 .792 6.964 7.275l-4.822.393 3.64 3.045-1.17 4.73z"/></svg></span></h3>
                        <p class="zs-reviews-subtitle">تجربیات واقعی مشتریان ما</p>
                    </div>
                    <div class="zs-reviews-container">
                        <div class="zs-reviews-wrapper">
                            <?php wc_get_template('single-product-reviews.php'); ?>
                        </div>
                    </div>
                </section>

                <?php
                // Enhanced Counters Section
                $counters = get_post_meta(get_the_ID(), '_zs_service_counters_arr', true);
                if (is_array($counters) && !empty($counters)):
                ?>
                <div class="zs-counters-section">
                    <div class="zs-counters-header">
                        <h3 class="zs-counters-title">آمار و ارقام<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h1v15h15v1H0V0z"/><path d="M10 3.5a.5.5 0 0 1 .832-.374l3 2.5a.5.5 0 0 1 .01.748l-6.5 6a.5.5 0 0 1-.69-.02L4.5 9.707 2.354 11.854a.5.5 0 1 1-.708-.708l2.5-2.5a.5.5 0 0 1 .708 0L7 12.293l6.147-5.671L10 4.5V3.5z"/></svg></span></h3>
                        <p class="zs-counters-subtitle">دستاوردهای ما در اعداد</p>
                    </div>
                    <div class="zs-counters-grid">
                        <?php foreach ($counters as $index => $ct):
                            $label = isset($ct['label']) ? esc_html($ct['label']) : '';
                            $value = isset($ct['value']) ? esc_html($ct['value']) : '';
                            $suffix = isset($ct['suffix']) ? esc_html($ct['suffix']) : '';
                            if ($label === '' && $value === '') continue; ?>
                            <div class="zs-counter" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                                <div class="zs-counter-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="zs-counter-content">
                                    <div class="zs-counter-value" data-count="<?php echo $value; ?>">0<?php echo $suffix ? ' ' . $suffix : ''; ?></div>
                                    <div class="zs-counter-label"><?php echo $label; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Enhanced Trust Section -->
    <section class="zs-trust-section" id="trust">
        <div class="container">
            <div class="zs-trust-header">
                <h3 class="zs-trust-title">چرا به ما اعتماد کنید؟<span class="zs-title-icon"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M5.072 1.106a1 1 0 0 1 .856-.06l2.51.964 2.51-.964a1 1 0 0 1 1.374.928V6.5c0 3.07-1.64 5.788-4.5 7.5-2.86-1.712-4.5-4.43-4.5-7.5V1.974a1 1 0 0 1 .75-.968z"/></svg></span></h3>
                <p class="zs-trust-subtitle">تجربه‌ای که ارزش اعتماد دارد</p>
            </div>
            
            <div class="trust-grid">
                <?php
                $trust = get_post_meta(get_the_ID(), '_zs_service_trust_arr', true);
                if (is_array($trust) && !empty($trust)):
                    foreach ($trust as $index => $tp):
                        $icRaw = isset($tp['icon']) ? $tp['icon'] : '';
                        $ic = $icRaw !== '' ? wp_staticize_emoji_for_email($icRaw) : '';
                        $tt = isset($tp['title']) ? esc_html($tp['title']) : '';
                        $tx = isset($tp['text']) ? esc_html($tp['text']) : '';
                        if ($tt === '' && $tx === '' && $ic === '') continue;
                        ?>
                        <div class="trust-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="trust-card">
                                <div class="trust-icon-wrapper">
                                    <div class="trust-icon"><?php echo $ic; ?></div>
                                    <div class="trust-icon-bg"></div>
                                </div>
                                <div class="trust-content">
                                    <?php if ($tt): ?><h4 class="trust-title"><?php echo $tt; ?></h4><?php endif; ?>
                                    <?php if ($tx): ?><p class="trust-description"><?php echo $tx; ?></p><?php endif; ?>
                                </div>
                                <div class="trust-hover-effect"></div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                    <!-- Default trust items when no custom trust data is set -->
                    <div class="trust-item" data-aos="fade-up" data-aos-delay="0">
                        <div class="trust-card">
                            <div class="trust-icon-wrapper">
                                <div class="trust-icon">🛡️</div>
                                <div class="trust-icon-bg"></div>
                            </div>
                            <div class="trust-content">
                                <h4 class="trust-title">اعتماد و کیفیت</h4>
                                <p class="trust-description">پشتیبانی همیشگی و اصالت محصولات</p>
                            </div>
                            <div class="trust-hover-effect"></div>
                        </div>
                    </div>
                    
                    <div class="trust-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="trust-card">
                            <div class="trust-icon-wrapper">
                                <div class="trust-icon">⚡</div>
                                <div class="trust-icon-bg"></div>
                            </div>
                            <div class="trust-content">
                                <h4 class="trust-title">سرعت و دقت</h4>
                                <p class="trust-description">تحویل سریع با بالاترین کیفیت</p>
                            </div>
                            <div class="trust-hover-effect"></div>
                        </div>
                    </div>
                    
                    <div class="trust-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="trust-card">
                            <div class="trust-icon-wrapper">
                                <div class="trust-icon">🎯</div>
                                <div class="trust-icon-bg"></div>
                            </div>
                            <div class="trust-content">
                                <h4 class="trust-title">تخصص و تجربه</h4>
                                <p class="trust-description">سال‌ها تجربه در ارائه محصولات تخصصی</p>
                            </div>
                            <div class="trust-hover-effect"></div>
                        </div>
                    </div>
                    
                    <div class="trust-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="trust-card">
                            <div class="trust-icon-wrapper">
                                <div class="trust-icon">💎</div>
                                <div class="trust-icon-bg"></div>
                            </div>
                            <div class="trust-content">
                                <h4 class="trust-title">کیفیت برتر</h4>
                                <p class="trust-description">استانداردهای بالای کیفیت در تمام محصولات</p>
                            </div>
                            <div class="trust-hover-effect"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


</div><!-- #product-<?php the_ID(); ?> -->

<!-- Floating Order Button - Sticky -->
<div id="zs-floating-order-btn" class="zs-floating-order-btn" style="display: none;">
    <div class="zs-floating-order-content">
        <div class="zs-floating-product-info">
            <?php 
            $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
            if ($featured_image) :
            ?>
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="zs-floating-product-image" />
            <?php else : ?>
                <div class="zs-floating-product-image zs-floating-no-image">
                    <i class="fas fa-image"></i>
                </div>
            <?php endif; ?>
            <div class="zs-floating-product-details">
                    <p class="zs-floating-product-price">
                    <?php 
                    global $product;
                    echo $product->get_price_html();
                    ?>
                </p>
            </div>
        </div>
        <div class="zs-floating-actions">
            <!-- Mobile: Quick Action Icons -->
            <div class="zs-floating-quick-actions">
                <button type="button" class="zs-floating-quick-btn" onclick="zsScrollToSection('faq')" title="سوالات متداول">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
            <button type="button" class="zs-floating-order-button" onclick="zsAddToCartDirect()">
                <i class="fas fa-shopping-cart"></i>
                <span>افزودن به سبد خرید</span>
            </button>
        </div>
    </div>
</div>

<!-- Desktop Sidebar Navigation - Minimal -->
<nav id="zs-sidebar-nav" class="zs-sidebar-nav">
    <ul class="zs-sidebar-nav-list">
        <li><a href="#order" class="zs-nav-link" data-section="order" data-tooltip="سفارش">
            <i class="fas fa-shopping-cart"></i>
        </a></li>
        <li><a href="#description" class="zs-nav-link" data-section="description" data-tooltip="توضیحات">
            <i class="fas fa-file-alt"></i>
        </a></li>
        <li><a href="#faq" class="zs-nav-link" data-section="faq" data-tooltip="سوالات متداول">
            <i class="fas fa-question-circle"></i>
        </a></li>
        <li><a href="#related-products" class="zs-nav-link" data-section="related-products" data-tooltip="محصولات مرتبط">
            <i class="fas fa-th-large"></i>
        </a></li>
        <li><a href="#reviews" class="zs-nav-link" data-section="reviews" data-tooltip="نظرات">
            <i class="fas fa-comments"></i>
        </a></li>
        <li><a href="#trust" class="zs-nav-link" data-section="trust" data-tooltip="اعتماد">
            <i class="fas fa-shield-alt"></i>
        </a></li>
    </ul>
</nav>



<?php do_action('woocommerce_after_single_product'); ?>
