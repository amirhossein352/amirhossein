<?php
/**
 * Categories Section - دسته‌بندی محصولات به صورت دایره‌ای
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!class_exists('WooCommerce')) {
    return;
}

// Get main product categories
$main_categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true, // فقط دسته‌هایی که محصول دارند
    'parent' => 0,
    'number' => 15, // حداکثر 15 دسته
    'orderby' => 'count',
    'order' => 'DESC'
));

if (empty($main_categories) || is_wp_error($main_categories)) {
    return;
}

// محدود کردن به 10 دسته
$main_categories = array_slice($main_categories, 0, 10);
?>

<!-- Categories Section -->
<section class="ki-categories-section">
    <div class="container">
        <div class="ki-categories-carousel-wrapper">
            <div class="ki-categories-row" id="categoriesCarousel">
                <?php foreach ($main_categories as $category): 
                    // Get category image
                    $category_image_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $category_link = get_term_link($category);
                    
                    // Use thumbnail size (100x100 max) for category images
                    if ($category_image_id) {
                        // Get responsive image with proper size
                        $category_image_url = wp_get_attachment_image_url($category_image_id, 'thumbnail');
                        $category_image_srcset = wp_get_attachment_image_srcset($category_image_id, 'thumbnail');
                        $category_image_sizes = '100px';
                        $category_image_meta = wp_get_attachment_metadata($category_image_id);
                    } else {
                        $category_image_url = wc_placeholder_img_src();
                        $category_image_srcset = '';
                        $category_image_sizes = '';
                        $category_image_meta = null;
                    }
                ?>
                    <div class="ki-category-item">
                        <a href="<?php echo esc_url($category_link); ?>" class="ki-category-link">
                            <div class="ki-category-circle-wrapper">
                                <div class="ki-category-circle">
                                    <img src="<?php echo esc_url($category_image_url); ?>" 
                                         <?php if ($category_image_srcset): ?>
                                         srcset="<?php echo esc_attr($category_image_srcset); ?>"
                                         sizes="<?php echo esc_attr($category_image_sizes); ?>"
                                         <?php endif; ?>
                                         width="100" 
                                         height="100"
                                         alt="<?php echo esc_attr($category->name); ?>" 
                                         class="ki-category-circle-img"
                                         loading="lazy"
                                         decoding="async">
                                </div>
                            </div>
                            <div class="ki-category-label">
                                <span class="ki-category-name"><?php echo esc_html($category->name); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="ki-categories-carousel-prev" aria-label="قبلی">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="ki-categories-carousel-next" aria-label="بعدی">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
    </div>
</section>

<?php
$template_uri = get_template_directory_uri();
$eslimi_teen = esc_url($template_uri . '/images/image-home/' . rawurlencode('نوجوان.png'));
$eslimi_house = esc_url($template_uri . '/images/image-home/' . rawurlencode('خانه ایرانی (6).png'));
?>
<style>
/* Categories Section Styles */
.ki-categories-section {
    padding: 60px 0;
    background: #f1f3f5;
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-categories-section::before {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 180px;
    height: 180px;
    background-image: url('<?php echo $eslimi_teen; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top right;
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
}

.ki-categories-section::after {
    content: '';
    position: absolute;
    bottom: 20px;
    left: 20px;
    width: 160px;
    height: 160px;
    background-image: url('<?php echo $eslimi_house; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom left;
    opacity: 0.07;
    pointer-events: none;
    z-index: 0;
}

.ki-categories-section .container {
    position: relative;
    z-index: 1;
}

.ki-categories-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 20px;
}

.ki-categories-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--zs-teal-dark, #00A796);
    margin: 0;
}

.ki-view-all-categories-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--zs-teal-dark, #00A796);
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
}

.ki-view-all-categories-btn:hover {
    color: var(--zs-teal-medium, #37C3B3);
    gap: 12px;
}

/* Categories Carousel */
.ki-categories-carousel-wrapper {
    position: relative;
}

.ki-categories-row {
    display: flex;
    gap: 24px;
    overflow-x: auto;
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding: 10px 0 20px;
    margin: 0 -10px;
    padding-left: 10px;
    padding-right: 10px;
}

.ki-categories-row::-webkit-scrollbar {
    display: none;
}

.ki-category-item {
    flex-shrink: 0;
    scroll-snap-align: start;
    min-width: 100px;
    max-width: 100px;
}

.ki-category-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
}

.ki-category-link:hover {
    transform: translateY(-4px);
}

.ki-category-circle-wrapper {
    width: 100px;
    height: 100px;
    margin-bottom: 12px;
    position: relative;
}

.ki-category-circle {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #e5e7eb;
    background: #ffffff;
    box-shadow: none;
    transition: all 0.3s ease;
    position: relative;
}

.ki-category-link:hover .ki-category-circle {
    border-color: var(--zs-teal-medium, #37C3B3);
    box-shadow: 0 2px 8px rgba(55, 195, 179, 0.15);
    transform: scale(1.03);
}

.ki-category-circle-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.ki-category-link:hover .ki-category-circle-img {
    transform: scale(1.1);
}

.ki-category-label {
    text-align: center;
    width: 100%;
}

.ki-category-name {
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    line-height: 1.4;
    display: block;
    transition: color 0.3s ease;
}

.ki-category-link:hover .ki-category-name {
    color: var(--zs-teal-dark, #00A796);
}

/* Carousel Navigation Buttons */
.ki-categories-carousel-prev,
.ki-categories-carousel-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: #ffffff;
    border: 2px solid rgba(55, 195, 179, 0.2);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    color: var(--zs-teal-dark, #00A796);
    font-size: 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.ki-categories-carousel-prev {
    right: -22px;
}

.ki-categories-carousel-next {
    left: -22px;
}

.ki-categories-carousel-prev:hover,
.ki-categories-carousel-next:hover {
    background: var(--zs-teal-medium, #37C3B3);
    color: #ffffff;
    border-color: var(--zs-teal-medium, #37C3B3);
    transform: translateY(-50%) scale(1.1);
}

/* Featured/Highlighted Categories (Optional - for future use) */
.ki-category-item.featured .ki-category-circle {
    border-color: #ec4899;
    border-width: 4px;
}

/* Responsive */
@media (max-width: 1024px) {
    .ki-categories-row {
        gap: 20px;
    }
    
    .ki-category-item {
        min-width: 90px;
        max-width: 90px;
    }
    
    .ki-category-circle-wrapper {
        width: 90px;
        height: 90px;
    }
}

@media (max-width: 768px) {
    .ki-categories-section {
        padding: 45px 0;
    }
    
    .ki-categories-header {
        flex-direction: row;
        align-items: center;
        margin-bottom: 28px;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .ki-categories-title {
        font-size: 18px;
    }
    
    .ki-view-all-categories-btn {
        width: auto;
        justify-content: flex-start;
        font-size: 14px;
    }
    
    .ki-categories-row {
        gap: 16px;
        padding: 10px 0;
    }
    
    .ki-category-item {
        min-width: 80px;
        max-width: 80px;
    }
    
    .ki-category-circle-wrapper {
        width: 80px;
        height: 80px;
        margin-bottom: 10px;
    }
    
    .ki-category-name {
        font-size: 12px;
    }
    
    .ki-categories-carousel-prev,
    .ki-categories-carousel-next {
        display: none;
    }
}

@media (max-width: 480px) {
    .ki-categories-row {
        gap: 12px;
    }
    
    .ki-category-item {
        min-width: 70px;
        max-width: 70px;
    }
    
    .ki-category-circle-wrapper {
        width: 70px;
        height: 70px;
        margin-bottom: 8px;
    }
    
    .ki-category-name {
        font-size: 11px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriesCarousel = document.getElementById('categoriesCarousel');
    const prevBtn = document.querySelector('.ki-categories-carousel-prev');
    const nextBtn = document.querySelector('.ki-categories-carousel-next');
    
    // Show navigation buttons on desktop
    if (categoriesCarousel && prevBtn && nextBtn && window.innerWidth > 768) {
        // Check if scroll is needed
        const checkScroll = () => {
            const hasScroll = categoriesCarousel.scrollWidth > categoriesCarousel.clientWidth;
            prevBtn.style.display = hasScroll ? 'flex' : 'none';
            nextBtn.style.display = hasScroll ? 'flex' : 'none';
        };
        
        checkScroll();
        window.addEventListener('resize', checkScroll);
        
        prevBtn.addEventListener('click', function() {
            categoriesCarousel.scrollBy({ left: -120, behavior: 'smooth' });
        });
        
        nextBtn.addEventListener('click', function() {
            categoriesCarousel.scrollBy({ left: 120, behavior: 'smooth' });
        });
        
        // Show/hide buttons based on scroll position
        categoriesCarousel.addEventListener('scroll', function() {
            const isAtStart = categoriesCarousel.scrollLeft <= 10;
            const isAtEnd = categoriesCarousel.scrollLeft >= categoriesCarousel.scrollWidth - categoriesCarousel.clientWidth - 10;
            
            prevBtn.style.opacity = isAtStart ? '0.5' : '1';
            nextBtn.style.opacity = isAtEnd ? '0.5' : '1';
        });
    }
});
</script>
