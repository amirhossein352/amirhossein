<?php
/**
 * Latest Products Carousel Section - کاروسل جدیدترین محصولات
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!class_exists('WooCommerce')) {
    return;
}

// Get product count from settings
$product_count = intval(khane_irani_get_setting('latest_products_count', '12'));

$args = array(
    'post_type'      => 'product',
    'posts_per_page' => $product_count,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ),
    ),
);

$latest_products = new WP_Query($args);

if (!$latest_products->have_posts()) {
    wp_reset_postdata();
    return;
}
?>

<!-- Latest Products Carousel Section -->
<section class="ki-latest-products-section">
    <div class="container">
        <div class="ki-latest-header">
            <div>
                <a href="<?php echo esc_url(home_url('/shop')); ?>" class="ki-latest-title-link">
                    <h2 class="ki-latest-title">جدیدترین محصولات</h2>
                </a>
            </div>
            <a href="<?php echo esc_url(home_url('/shop')); ?>" class="ki-view-all-btn ki-view-all-btn-desktop">
                مشاهده همه
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <div class="ki-latest-carousel-wrapper">
            <div class="swiper ki-latest-carousel" id="latestProductsCarousel">
                <div class="swiper-wrapper">
                <?php while ($latest_products->have_posts()): $latest_products->the_post();
                    global $product;
                    $product_id = $product->get_id();
                    $sale_percentage = 0;
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();
                    
                    if ($regular_price && $sale_price) {
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
                                <span class="ki-new-badge">
                                    <i class="fas fa-sparkles"></i>
                                    جدید
                                </span>
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
            <button class="ki-products-carousel-prev" aria-label="قبلی">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="ki-products-carousel-next" aria-label="بعدی">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>

<?php
$template_uri = get_template_directory_uri();
$eslimi_child = esc_url($template_uri . '/images/image-home/' . rawurlencode('کودک.png'));
$eslimi_lady = esc_url($template_uri . '/images/image-home/' . rawurlencode('بانو.png'));
?>
<style>
.ki-latest-products-section {
    padding: 60px 0;
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 20%, #d1fae5 40%, #ecfdf5 60%, #f0fdfa 80%, #ccfbf1 100%);
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-latest-products-section::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 20px;
    width: 250px;
    height: 250px;
    background-image: url('<?php echo $eslimi_child; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top left;
    opacity: 0.15;
    pointer-events: none;
    z-index: 0;
}

.ki-latest-products-section::after {
    content: '';
    position: absolute;
    bottom: 20px;
    right: 20px;
    width: 230px;
    height: 230px;
    background-image: url('<?php echo $eslimi_lady; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom right;
    opacity: 0.12;
    pointer-events: none;
    z-index: 0;
}

.ki-latest-products-section .container {
    position: relative;
    z-index: 1;
}

.ki-latest-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 20px;
}

.ki-latest-subtitle {
    display: inline-block;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.ki-latest-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--zs-teal-dark, #00A796);
    margin: 0 0 6px 0;
}

.ki-latest-description {
    font-size: 13px;
    color: #64748b;
    margin: 0;
}

.ki-view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--zs-teal-dark, #00A796);
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0, 167, 150, 0.15);
}

.ki-view-all-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 167, 150, 0.2);
    background: var(--zs-teal-medium, #37C3B3);
}

.ki-product-link {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
    height: 100%;
}

/* Carousel Wrapper */
.ki-latest-carousel-wrapper {
    position: relative;
    padding: 0 40px;
    overflow: hidden;
}

.ki-latest-carousel.swiper {
    overflow: hidden;
}

.ki-latest-carousel .swiper-wrapper {
    display: flex;
    align-items: stretch;
}

.ki-product-slide.swiper-slide {
    height: auto;
    display: flex;
    width: 100%;
}

/* Enhanced Product Card */
.ki-product-card {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    position: relative;
}

.ki-product-card:hover {
    transform: translateY(-4px);
    border-color: #dee2e6;
}

.ki-product-image-wrapper {
    position: relative;
    width: 100%;
    height: 280px;
    overflow: hidden;
    background: #fafbfc;
}

.ki-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.ki-product-card:hover .ki-product-image {
    transform: scale(1.05);
}

.ki-product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(55, 195, 179, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ki-product-card:hover .ki-product-overlay {
    opacity: 1;
}

.ki-view-product-overlay {
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transform: translateY(5px);
    transition: transform 0.3s ease;
}

.ki-product-card:hover .ki-view-product-overlay {
    transform: translateY(0);
}

.ki-sale-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #ef4444;
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    z-index: 3;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.3s ease;
}

.ki-product-card:hover .ki-sale-badge {
    transform: scale(1.05);
}

/* .ki-new-badge styles moved to css/front-page.css */

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
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    margin: 0;
    line-height: 1.5;
    min-height: 42px;
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
.ki-products-carousel-prev,
.ki-products-carousel-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 16px;
}

.ki-products-carousel-prev {
    right: 0;
}

.ki-products-carousel-next {
    left: 0;
}

.ki-products-carousel-prev:hover,
.ki-products-carousel-next:hover {
    background: var(--zs-teal-medium, #37C3B3);
    color: #ffffff;
    border-color: var(--zs-teal-medium, #37C3B3);
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
    
    .ki-latest-carousel-wrapper {
        padding: 0 40px;
    }
}

@media (max-width: 768px) {
    .ki-latest-products-section {
        padding: 30px 0;
        background: #ffffff;
    }

    .ki-latest-header {
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: 0 5px;
    }

    /* Hide subtitle and description on mobile */
    .ki-latest-subtitle,
    .ki-latest-description {
        display: none;
    }

    .ki-latest-title {
        font-size: 20px;
        margin: 0;
        font-weight: 600;
    }

    .ki-latest-title-link {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
    }

    .ki-latest-title-link:hover .ki-latest-title {
        color: var(--zs-teal-medium, #37C3B3);
    }

    /* Hide view all button on mobile */
    .ki-view-all-btn-desktop {
        display: none !important;
    }

    
    .ki-latest-carousel-wrapper {
        padding: 0 40px;
    }
    
    .ki-product-image-wrapper {
        height: 200px;
    }
    
    .ki-products-carousel-prev,
    .ki-products-carousel-next {
        width: 32px;
        height: 32px;
        font-size: 12px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(55, 195, 179, 0.2);
    }
    
    .ki-products-carousel-prev {
        right: 3px;
    }
    
    .ki-products-carousel-next {
        left: 3px;
    }
}

@media (max-width: 480px) {
    .ki-latest-title {
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
    const latestCarousel = document.getElementById('latestProductsCarousel');
    if (!latestCarousel) return;
    
    const swiper = new Swiper('#latestProductsCarousel', {
        slidesPerView: 1,
        spaceBetween: 24,
        rtl: true,
        navigation: {
            nextEl: '.ki-products-carousel-next',
            prevEl: '.ki-products-carousel-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 4,
            },
        },
    });
});
</script>

<!-- WooCommerce handles add to cart automatically via ajax_add_to_cart class -->

