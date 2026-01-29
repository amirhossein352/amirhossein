<?php
/**
 * Custom Products Carousel Section - کاروسل‌های اختیاری محصولات
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!class_exists('WooCommerce')) {
    return;
}

$carousel_data = get_query_var('carousel_data', array());
$carousel_index = get_query_var('carousel_index', 0);

if (empty($carousel_data)) {
    return;
}

$title = isset($carousel_data['title']) ? esc_html($carousel_data['title']) : 'محصولات';
$category_ids = isset($carousel_data['category_ids']) && is_array($carousel_data['category_ids']) ? $carousel_data['category_ids'] : array();
$product_count = isset($carousel_data['product_count']) ? intval($carousel_data['product_count']) : 12;
$show_link = isset($carousel_data['show_link']) ? $carousel_data['show_link'] : '1';
$link_url = isset($carousel_data['link_url']) ? esc_url($carousel_data['link_url']) : home_url('/shop');
$link_text = isset($carousel_data['link_text']) ? esc_html($carousel_data['link_text']) : 'مشاهده همه';

$args = array(
    'post_type'      => 'product',
    'posts_per_page' => $product_count,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
);

// Only filter by stock if WooCommerce is active and we want to show only in-stock products
// For now, let's show all products regardless of stock status to ensure products are displayed
// You can uncomment this if you want to filter by stock status
/*
if (class_exists('WooCommerce')) {
    $args['meta_query'] = array(
        array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ),
    );
}
*/

// Filter by categories if specified
if (!empty($category_ids) && is_array($category_ids)) {
    // Remove empty values
    $category_ids = array_filter(array_map('intval', $category_ids));
    
    if (!empty($category_ids)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_ids,
                'operator' => 'IN',
            ),
        );
    }
}

$products_query = new WP_Query($args);

// Debug: Log query results
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Custom Carousel ' . $carousel_index . ' - Found ' . $products_query->found_posts . ' products');
}

if (!$products_query->have_posts()) {
    wp_reset_postdata();
    // Don't return - show empty state instead
    ?>
    <section class="ki-custom-products-carousel-section" data-carousel-index="<?php echo esc_attr($carousel_index); ?>">
        <div class="container">
            <div class="ki-custom-header">
                <div>
                    <h2 class="ki-custom-title"><?php echo $title; ?></h2>
                </div>
            </div>
            <p style="text-align: center; padding: 40px; color: #94a3b8;">محصولی یافت نشد.</p>
        </div>
    </section>
    <?php
    return;
}
?>

<!-- Custom Products Carousel Section -->
<section class="ki-custom-products-carousel-section" data-carousel-index="<?php echo esc_attr($carousel_index); ?>">
    <div class="container">
        <div class="ki-custom-header">
            <div>
                <?php if ($show_link === '1'): ?>
                    <a href="<?php echo $link_url; ?>" class="ki-custom-title-link">
                        <h2 class="ki-custom-title"><?php echo $title; ?></h2>
                    </a>
                <?php else: ?>
                    <h2 class="ki-custom-title"><?php echo $title; ?></h2>
                <?php endif; ?>
            </div>
            <?php if ($show_link === '1'): ?>
                <a href="<?php echo $link_url; ?>" class="ki-view-all-btn ki-view-all-btn-desktop">
                    <?php echo $link_text; ?>
                    <i class="fas fa-arrow-left"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="ki-custom-carousel-wrapper">
            <div class="swiper ki-custom-carousel" id="customCarousel<?php echo esc_attr($carousel_index); ?>">
                <div class="swiper-wrapper">
                <?php while ($products_query->have_posts()): $products_query->the_post();
                    $product = wc_get_product(get_the_ID());
                    if (!$product) {
                        continue;
                    }
                    
                    $product_id = $product->get_id();
                    $sale_percentage = 0;
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();
                    
                    if ($regular_price && $sale_price && $regular_price > 0) {
                        $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                    }
                ?>
                    <div class="swiper-slide ki-product-slide">
                        <div class="ki-product-card">
                            <div class="ki-product-image-wrapper">
                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="ki-product-link">
                                    <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'ki-product-image')); ?>
                                    <div class="ki-product-overlay">
                                        <span class="ki-view-product-overlay">
                                            <i class="fas fa-eye"></i>
                                            مشاهده محصول
                                        </span>
                                    </div>
                                </a>
                                <?php if ($sale_percentage > 0): ?>
                                    <span class="ki-sale-badge">
                                        <i class="fas fa-percent"></i>
                                        <?php echo esc_html($sale_percentage); ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="ki-product-info">
                                <div class="ki-product-price-header">
                                    <?php if ($sale_price): ?>
                                        <span class="ki-price-sale"><?php echo wc_price($sale_price); ?></span>
                                        <span class="ki-price-regular"><?php echo wc_price($regular_price); ?></span>
                                    <?php else: ?>
                                        <span class="ki-price-current"><?php echo $product->get_price_html(); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="ki-product-title">
                                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                </h3>
                                <div class="ki-product-actions">
                                    <button class="ki-action-btn ki-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" title="علاقه‌مندی" style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid #e5e7eb !important; background: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; padding: 0 !important; color: #6b7280 !important; font-size: 16px !important;">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="ki-action-btn ki-compare-btn" data-product-id="<?php echo esc_attr($product_id); ?>" title="مقایسه" style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid #e5e7eb !important; background: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; padding: 0 !important; color: #6b7280 !important; font-size: 16px !important;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <?php
                                    $add_to_cart_url = $product->add_to_cart_url();
                                    ?>
                                    <a href="<?php echo esc_url($add_to_cart_url); ?>" 
                                       class="ki-action-btn ki-cart-btn add_to_cart_button" 
                                       data-product_id="<?php echo esc_attr($product_id); ?>" 
                                       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                       title="افزودن به سبد خرید" 
                                       style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid var(--zs-teal-medium, #37C3B3) !important; background: var(--zs-teal-medium, #37C3B3) !important; color: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; text-decoration: none !important; font-size: 16px !important;">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            </div>
            <button class="ki-custom-carousel-prev" aria-label="قبلی">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="ki-custom-carousel-next" aria-label="بعدی">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>

<?php
$template_uri = get_template_directory_uri();
$eslimi_house = esc_url($template_uri . '/images/image-home/' . rawurlencode('خانه ایرانی (6).png'));
?>
<style>
.ki-custom-products-carousel-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 30%, #f8fafc 60%, #ffffff 100%);
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-custom-products-carousel-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background-image: url('<?php echo $eslimi_house; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top right;
    opacity: 0.12;
    pointer-events: none;
    z-index: 0;
}

.ki-custom-products-carousel-section::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 250px;
    height: 250px;
    background-image: url('<?php echo $eslimi_house; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom left;
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
    transform: rotate(180deg);
}

.ki-custom-products-carousel-section .container {
    position: relative;
    z-index: 1;
}

.ki-custom-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
    flex-wrap: wrap;
    gap: 24px;
}

.ki-custom-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--zs-teal-dark, #00A796);
    margin: 0;
    line-height: 1.3;
    position: relative;
    padding-right: 20px;
}

.ki-custom-title::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 32px;
    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796));
    border-radius: 2px;
}

.ki-view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796));
    color: #ffffff;
    padding: 14px 28px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(55, 195, 179, 0.2);
}

.ki-view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(55, 195, 179, 0.3);
}

/* Carousel Wrapper */
.ki-custom-carousel-wrapper {
    position: relative;
    padding: 0 50px;
    overflow: hidden;
}

.ki-custom-carousel.swiper {
    overflow: hidden;
}

.ki-custom-carousel .swiper-wrapper {
    display: flex;
    align-items: stretch;
}

.ki-product-slide.swiper-slide {
    height: auto;
    display: flex;
}

/* Enhanced Product Card */
.ki-product-slide.swiper-slide {
    height: auto;
    display: flex;
    width: 100%;
}

.ki-product-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(55, 195, 179, 0.15);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    position: relative;
}

.ki-product-card:hover {
    transform: translateY(-4px);
    border-color: rgba(55, 195, 179, 0.3);
}

.ki-product-image-wrapper {
    position: relative;
    width: 100%;
    height: 280px;
    overflow: hidden;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.ki-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.ki-product-card:hover .ki-product-image {
    transform: scale(1.1);
}

.ki-product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(55, 195, 179, 0.9), rgba(0, 167, 150, 0.85));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.4s ease;
    backdrop-filter: blur(2px);
}

.ki-product-card:hover .ki-product-overlay {
    opacity: 1;
}

.ki-view-product-overlay {
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 30px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    transform: translateY(10px);
    transition: transform 0.3s ease;
}

.ki-product-card:hover .ki-view-product-overlay {
    transform: translateY(0);
}

.ki-product-link {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
    height: 100%;
}

.ki-sale-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    z-index: 3;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.ki-product-card:hover .ki-sale-badge {
    transform: scale(1.1);
}

.ki-product-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #ffffff;
    flex: 1;
}

.ki-product-price-header {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.ki-price-sale {
    font-size: 18px;
    font-weight: 700;
    color: #ef4444;
    direction: ltr;
    font-family: 'IRANSans', 'Tahoma', sans-serif;
}

.ki-price-regular {
    font-size: 14px;
    font-weight: 500;
    color: #9ca3af;
    text-decoration: line-through;
    direction: ltr;
    font-family: 'IRANSans', 'Tahoma', sans-serif;
}

.ki-price-current {
    font-size: 18px;
    font-weight: 700;
    color: var(--zs-teal-dark, #00A796);
    direction: ltr;
    display: inline-block;
    font-family: 'IRANSans', 'Tahoma', sans-serif;
}

.ki-product-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    line-height: 1.5;
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ki-product-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.ki-product-title a:hover {
    color: var(--zs-teal-dark, #00A796);
}

.ki-product-actions {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
    margin-top: auto;
}

.ki-action-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #e5e7eb;
    background: #ffffff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
    color: #6b7280;
    font-size: 16px;
}

.ki-action-btn.ki-cart-btn:hover {
    background: var(--zs-teal-dark, #00A796);
    border-color: var(--zs-teal-dark, #00A796);
    color: #ffffff;
    transform: translateY(-3px);
}
.ki-action-btn.ki-wishlist-btn:hover,
.ki-action-btn.ki-compare-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #374151;
    transform: translateY(-2px);
}

.ki-action-btn i {
    font-size: 16px;
}


/* Carousel Navigation Buttons */
.ki-custom-carousel-prev,
.ki-custom-carousel-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: #ffffff;
    border: 2px solid rgba(55, 195, 179, 0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 10;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 18px;
    user-select: none;
    -webkit-user-select: none;
    outline: none;
}

.ki-custom-carousel-prev {
    right: 0;
}

.ki-custom-carousel-next {
    left: 0;
}

.ki-custom-carousel-prev:hover,
.ki-custom-carousel-next:hover {
    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796));
    color: #ffffff;
    border-color: var(--zs-teal-medium, #37C3B3);
    transform: translateY(-50%) scale(1.15);
}

.ki-custom-carousel-prev:active,
.ki-custom-carousel-next:active {
    transform: translateY(-50%) scale(0.9);
}

.ki-custom-carousel-prev:focus,
.ki-custom-carousel-next:focus {
    outline: 2px solid rgba(55, 195, 179, 0.4);
    outline-offset: 2px;
}

.ki-custom-carousel-prev:disabled,
.ki-custom-carousel-next:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .ki-product-slide {
        flex: 0 0 calc(33.333% - 16px);
    }
}

@media (max-width: 992px) {
    .ki-product-slide {
        flex: 0 0 calc(50% - 12px);
    }
    
    .ki-custom-carousel-wrapper {
        padding: 0 40px;
    }
}

@media (max-width: 768px) {
    .ki-custom-products-carousel-section {
        padding: 30px 0;
        background: #ffffff;
    }

    .ki-custom-header {
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: 0 5px;
    }

    .ki-custom-title {
        font-size: 20px;
        margin: 0;
        font-weight: 600;
        padding-right: 0;
    }

    .ki-custom-title::after {
        display: none;
    }

    .ki-custom-title-link {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
    }

    .ki-custom-title-link:hover .ki-custom-title {
        color: var(--zs-teal-medium, #37C3B3);
    }

    /* Hide view all button on mobile */
    .ki-view-all-btn-desktop {
        display: none !important;
    }

    /* 2 columns on mobile */
    .ki-product-slide {
        flex: 0 0 calc(50% - 6px);
    }
    
    .ki-custom-carousel {
        gap: 10px;
    }
    
    .ki-custom-carousel-wrapper {
        padding: 0 35px;
    }
    
    .ki-product-image-wrapper {
        height: 180px;
    }
    
    .ki-custom-carousel-prev,
    .ki-custom-carousel-next {
        width: 32px;
        height: 32px;
        font-size: 12px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(55, 195, 179, 0.2);
    }
    
    .ki-custom-carousel-prev {
        right: 3px;
    }
    
    .ki-custom-carousel-next {
        left: 3px;
    }

    .ki-product-info {
        padding: 12px;
        gap: 8px;
    }

    .ki-product-title {
        font-size: 13px;
        min-height: 38px;
        line-height: 1.4;
    }

    .ki-product-price-header {
        gap: 6px;
    }

    .ki-price-sale,
    .ki-price-current {
        font-size: 16px;
    }

    .ki-price-regular {
        font-size: 12px;
    }

    .ki-product-actions {
        padding-top: 8px;
        gap: 6px;
    }

    .ki-action-btn {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .ki-custom-title {
        font-size: 24px;
    }
    
    .ki-product-info {
        padding: 16px;
    }
    
    .ki-product-title {
        font-size: 15px;
        min-height: 44px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carouselIndex = <?php echo esc_js($carousel_index); ?>;
    const customCarousel = document.getElementById('customCarousel' + carouselIndex);
    if (!customCarousel) return;
    
    const customWrapper = customCarousel.closest('.ki-custom-carousel-wrapper');
    const customPrevBtn = customWrapper?.querySelector('.ki-custom-carousel-prev');
    const customNextBtn = customWrapper?.querySelector('.ki-custom-carousel-next');
    
    const swiper = new Swiper('#customCarousel' + carouselIndex, {
        slidesPerView: 1,
        spaceBetween: 24,
        rtl: true,
        navigation: {
            nextEl: customNextBtn ? '.ki-custom-carousel-next' : null,
            prevEl: customPrevBtn ? '.ki-custom-carousel-prev' : null,
        },
        breakpoints: {
            480: {
                slidesPerView: 2,
                spaceBetween: 12,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 16,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            1200: {
                slidesPerView: 4,
                spaceBetween: 24,
            },
        },
    });
});
</script>

<!-- WooCommerce handles add to cart automatically via ajax_add_to_cart class -->

