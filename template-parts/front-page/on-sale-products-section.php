<?php
/**
 * On Sale Products Carousel Section - کاروسل محصولات تخفیف‌دار با تایمر
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!class_exists('WooCommerce')) {
    return;
}

// Get product count from settings
$product_count = intval(khane_irani_get_setting('on_sale_products_count', '12'));

// کوئری ساده: دریافت تمام محصولات منتشر شده
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1, // ابتدا همه را بگیر
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
);

$all_products = new WP_Query($args);

// فیلتر کردن: فقط محصولاتی که واقعاً تخفیف دارند
$on_sale_product_ids = array();
if ($all_products->have_posts()) {
    while ($all_products->have_posts()) {
        $all_products->the_post();
        $product = wc_get_product(get_the_ID());
        
        if ($product && $product->is_on_sale()) {
            $on_sale_product_ids[] = get_the_ID();
        }
    }
    wp_reset_postdata();
}

// اگر محصول تخفیف‌داری پیدا نشد، خروج
if (empty($on_sale_product_ids)) {
    return;
}

// محدود کردن به تعداد مورد نظر
$on_sale_product_ids = array_slice($on_sale_product_ids, 0, $product_count);

// کوئری نهایی با محصولات تخفیف‌دار
$final_args = array(
    'post_type'      => 'product',
    'post__in'       => $on_sale_product_ids,
    'posts_per_page' => $product_count,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
);

$on_sale_products = new WP_Query($final_args);

if (!$on_sale_products->have_posts()) {
    wp_reset_postdata();
    return;
}

// Get sale end date from settings (default: 7 days from now)
$sale_end_timestamp = khane_irani_get_setting('sale_end_date', '');
if (empty($sale_end_timestamp)) {
    $sale_end_timestamp = strtotime('+7 days');
} else {
    $sale_end_timestamp = strtotime($sale_end_timestamp);
}
?>

<!-- On Sale Products Carousel Section -->
<section class="ki-on-sale-products-section">
    <div class="container">
        <div class="ki-on-sale-header">
            <div class="ki-on-sale-title-wrapper">
                <h2 class="ki-on-sale-title">تا پایان تخفیف محصولات</h2>
                <p class="ki-on-sale-subtitle">محصولات با تخفیف ویژه را از دست ندهید</p>
            </div>
            <div class="ki-sale-countdown" id="saleCountdown" data-end="<?php echo esc_attr($sale_end_timestamp); ?>">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">00</span>
                    <span class="countdown-label">روز</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">00</span>
                    <span class="countdown-label">ساعت</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">00</span>
                    <span class="countdown-label">دقیقه</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="seconds">00</span>
                    <span class="countdown-label">ثانیه</span>
                </div>
            </div>
        </div>

        <div class="ki-on-sale-carousel-wrapper">
            <div class="swiper ki-on-sale-carousel" id="onSaleCarousel">
                <div class="swiper-wrapper">
                <?php 
                $found_products = 0;
                while ($on_sale_products->have_posts()): 
                    $on_sale_products->the_post();
                    global $product;
                    $product = wc_get_product(get_the_ID());
                    
                    if (!$product) {
                        continue;
                    }
                    
                    $found_products++;
                    $product_id = $product->get_id();
                    $sale_percentage = 0;
                    $is_variable = $product->is_type('variable');
                    
                    // Calculate sale percentage
                    if ($is_variable) {
                        // برای محصولات متغیر، کمترین قیمت را بگیر
                        $min_price = $product->get_variation_price('min', true);
                        $min_regular_price = $product->get_variation_regular_price('min', true);
                        $min_sale_price = $product->get_variation_sale_price('min', true);
                        
                        if ($min_sale_price && $min_sale_price < $min_regular_price) {
                            $sale_price = $min_sale_price;
                            $regular_price = $min_regular_price;
                            $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                        } else {
                            $regular_price = $min_price;
                            $sale_price = '';
                        }
                    } else {
                        $regular_price = (float) $product->get_regular_price();
                        $sale_price = (float) $product->get_sale_price();
                        
                        if ($regular_price > 0 && $sale_price > 0) {
                            $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                        }
                    }
                ?>
                    <div class="swiper-slide">
                        <div class="ki-product-card">
                            <div class="ki-product-image-wrapper">
                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="ki-product-link" style="text-decoration: none !important; color: inherit !important; display: block !important; width: 100% !important; height: 100% !important;">
                                    <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'ki-product-image')); ?>
                                </a>
                                <?php if ($sale_percentage > 0): ?>
                                    <span class="ki-sale-badge"><?php echo esc_html($sale_percentage); ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="ki-product-info">
                                <div class="ki-product-price-header">
                                    <?php if ($is_variable): ?>
                                        <?php if ($sale_price && $sale_price < $regular_price): ?>
                                            <span class="ki-price-sale"><?php echo wc_price($sale_price); ?></span>
                                            <?php if ($regular_price > 0): ?>
                                                <span class="ki-price-regular"><?php echo wc_price($regular_price); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="ki-price-current"><?php echo wc_price($regular_price); ?></span>
                                        <?php endif; ?>
                                    <?php elseif ($product->is_on_sale() && $sale_price > 0): ?>
                                        <span class="ki-price-sale"><?php echo wc_price($sale_price); ?></span>
                                        <?php if ($regular_price > 0): ?>
                                            <span class="ki-price-regular"><?php echo wc_price($regular_price); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="ki-price-current"><?php echo $product->get_price_html(); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="ki-product-title">
                                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" style="color: inherit !important; text-decoration: none !important;">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                </h3>
                                <div class="ki-product-actions" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 10px !important; padding-top: 12px !important; border-top: 1px solid #f1f5f9 !important; margin-top: auto !important;">
                                    <button class="ki-action-btn ki-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" title="علاقه‌مندی" style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid #e5e7eb !important; background: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; padding: 0 !important; color: #6b7280 !important; font-size: 16px !important;">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="ki-action-btn ki-compare-btn" data-product-id="<?php echo esc_attr($product_id); ?>" title="مقایسه" style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid #e5e7eb !important; background: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; padding: 0 !important; color: #6b7280 !important; font-size: 16px !important;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <?php
                                    // برای محصولات متغیر، به صفحه محصول لینک بده
                                    $is_variable = $product->is_type('variable');
                                    if ($is_variable) {
                                        $add_to_cart_url = get_permalink($product_id);
                                        $cart_button_class = '';
                                        $cart_button_text = '<i class="fas fa-eye"></i>';
                                        $cart_button_title = 'مشاهده محصول';
                                    } else {
                                        $add_to_cart_url = $product->add_to_cart_url();
                                        $cart_button_class = 'add_to_cart_button'; // حذف ajax_add_to_cart
                                        $cart_button_text = '<i class="fas fa-shopping-cart"></i>';
                                        $cart_button_title = 'افزودن به سبد خرید';
                                    }
                                    ?>
                                    <a href="<?php echo esc_url($add_to_cart_url); ?>" 
                                       class="ki-action-btn ki-cart-btn <?php echo esc_attr($cart_button_class); ?>" 
                                       data-product_id="<?php echo esc_attr($product_id); ?>" 
                                       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                       title="<?php echo esc_attr($cart_button_title); ?>" 
                                       style="width: 40px !important; height: 40px !important; min-width: 40px !important; flex-shrink: 0 !important; border: 2px solid var(--zs-teal-medium, #37C3B3) !important; background: var(--zs-teal-medium, #37C3B3) !important; color: #ffffff !important; border-radius: 10px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; transition: all 0.3s ease !important; text-decoration: none !important; font-size: 16px !important;">
                                        <?php echo $cart_button_text; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
                
                <?php if ($found_products > 1): ?>
                    <div class="swiper-button-prev ki-carousel-prev"></div>
                    <div class="swiper-button-next ki-carousel-next"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>

<?php
$template_uri = get_template_directory_uri();
$eslimi_lady = esc_url($template_uri . '/images/image-home/' . rawurlencode('بانو.png'));
$eslimi_child = esc_url($template_uri . '/images/image-home/' . rawurlencode('کودک.png'));
?>
<style>
/* استایل‌های قبلی بدون تغییر */
.ki-on-sale-products-section {
    padding: 60px 0;
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 20%, #e0f7fa 40%, #f0fdfa 60%, #e0f2fe 80%, #dbeafe 100%);
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-on-sale-products-section::before {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 250px;
    height: 250px;
    background-image: url('<?php echo $eslimi_lady; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top right;
    opacity: 0.15;
    pointer-events: none;
    z-index: 0;
}

.ki-on-sale-products-section::after {
    content: '';
    position: absolute;
    bottom: 20px;
    left: 20px;
    width: 220px;
    height: 220px;
    background-image: url('<?php echo $eslimi_child; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom left;
    opacity: 0.12;
    pointer-events: none;
    z-index: 0;
}

.ki-on-sale-products-section .container {
    position: relative;
    z-index: 1;
}

.ki-on-sale-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.ki-on-sale-title-wrapper {
    flex: 1;
}

.ki-on-sale-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--zs-teal-dark, #00A796);
    margin: 0 0 6px 0;
    line-height: 1.3;
}

.ki-on-sale-subtitle {
    font-size: 13px;
    color: #64748b;
    margin: 0;
}

.ki-sale-countdown {
    display: flex;
    gap: 12px;
    background: var(--zs-teal-dark, #00A796);
    padding: 12px 20px;
    border-radius: 6px;
}

.countdown-item {
    text-align: center;
    min-width: 60px;
}

.countdown-number {
    display: block;
    font-size: 22px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1;
    margin-bottom: 4px;
}

.countdown-label {
    display: block;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.ki-on-sale-carousel-wrapper {
    position: relative;
    padding: 0 24px;
}

.ki-on-sale-carousel {
    width: 100%;
    overflow: hidden;
}

.ki-product-card {
    background: #ffffff;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
}

.ki-product-card:hover {
    transform: translateY(-3px);
    border-color: #dee2e6;
}

/* Force 2-column width on mobile */
@media (max-width: 768px) {
    #onSaleCarousel .swiper-slide {
        width: 50% !important;
    }
}

.ki-product-link {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
    height: 100%;
}

.ki-product-image-wrapper {
    position: relative;
    width: 100%;
    height: 260px;
    overflow: hidden;
    background: #f9fafb;
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

.ki-sale-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #ef4444;
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    z-index: 2;
    line-height: 1.4;
}

.ki-product-info {
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-grow: 1;
    height: 100%;
}

.ki-product-price-header {
    text-align: right;
    margin-bottom: 4px;
}

.ki-price-sale {
    font-size: 16px;
    font-weight: 700;
    color: #ef4444;
    direction: ltr;
    display: inline-block;
    font-family: 'IranYekan', 'Tahoma', sans-serif;
    margin-left: 8px;
}

.ki-price-regular {
    font-size: 13px;
    font-weight: 500;
    color: #9ca3af;
    text-decoration: line-through;
    direction: ltr;
    font-family: 'IranYekan', 'Tahoma', sans-serif;
}

.ki-price-current {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    direction: ltr;
    display: inline-block;
    font-family: 'IranYekan', 'Tahoma', sans-serif;
}

.ki-product-title {
    font-size: 13px;
    font-weight: 500;
    color: #4b5563;
    margin: 0;
    line-height: 1.5;
    text-align: center;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 38px;
    padding: 4px 0;
}

.ki-product-actions {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
    gap: 12px;
    padding-top: 8px;
    border-top: 1px solid #f3f4f6;
    margin-top: auto;
}

.ki-action-btn {
    width: 36px;
    height: 36px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    color: #6b7280;
    font-size: 14px;
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
    font-size: 14px;
}


/* Carousel Navigation Buttons - Swiper */
.ki-carousel-prev.swiper-button-prev,
.ki-carousel-next.swiper-button-next {
    width: 44px;
    height: 44px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 16px;
    transition: all 0.3s ease;
    margin-top: 0;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.ki-carousel-prev.swiper-button-prev {
    right: 0;
    left: auto;
}

.ki-carousel-next.swiper-button-next {
    left: 0;
    right: auto;
}

.ki-carousel-prev.swiper-button-prev::after,
.ki-carousel-next.swiper-button-next::after {
    font-size: 16px;
    font-weight: 900;
}

.ki-carousel-prev.swiper-button-prev:hover,
.ki-carousel-next.swiper-button-next:hover {
    background: var(--zs-teal-medium, #37C3B3);
    color: #ffffff;
    border-color: var(--zs-teal-medium, #37C3B3);
}

/* رسپانسیو */
@media (max-width: 768px) {
    .ki-on-sale-products-section {
        padding: 30px 0;
    }

    .ki-on-sale-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .ki-on-sale-title {
        font-size: 20px;
    }

    .ki-sale-countdown {
        width: 100%;
        justify-content: center;
        padding: 16px 20px;
    }

    .countdown-item {
        min-width: 50px;
    }

    .countdown-number {
        font-size: 24px;
    }
    
    .ki-on-sale-carousel-wrapper {
        padding: 0 0px;
    }
    #onSaleCarousel .swiper-slide {
        width: 50% !important;
        padding: 0 6px;
        box-sizing: border-box;
    }
    
    .ki-product-image-wrapper {
        height: 200px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    const countdownEl = document.getElementById('saleCountdown');
    if (countdownEl) {
        const endTime = parseInt(countdownEl.dataset.end) * 1000;
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
    
    // Initialize Swiper - فقط یک بار اجرا شود
    const carousel = document.getElementById('onSaleCarousel');
    if (carousel) {
        // منتظر بمان تا DOM کاملاً لود شود
        setTimeout(() => {
            const swiper = new Swiper('#onSaleCarousel', {
                slidesPerView: 1,
                spaceBetween: 20,
                rtl: true,
                navigation: {
                    nextEl: '.ki-carousel-next',
                    prevEl: '.ki-carousel-prev',
                },
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    992: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    },
                    1200: {
                        slidesPerView: 4,
                        spaceBetween: 24
                    }
                }
            });
            
            console.log('Swiper initialized with', swiper.slides.length, 'slides');
        }, 100);
    }
    
    // WooCommerce handles add to cart automatically via ajax_add_to_cart class
});
</script>