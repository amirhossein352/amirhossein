<?php
/**
 * Latest Posts Section Template Part - Blog Carousel
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */
?>

<!-- Latest Posts Section -->
<section class="ki-latest-posts-section">
    <div class="container">
        <div class="ki-posts-header">
            <div>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="ki-posts-title-link">
                    <h2 class="ki-posts-title">آخرین مطالب بلاگ</h2>
                </a>
            </div>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="ki-view-all-btn ki-view-all-btn-desktop">
                مشاهده همه مطالب
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        
        <div class="ki-posts-carousel-wrapper">
            <div class="swiper ki-posts-carousel" id="blogPostsCarousel">
                <div class="swiper-wrapper">
                    <?php
                    $args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 12,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    
                    $latest_posts = new WP_Query($args);
                    
                    if ($latest_posts->have_posts()) :
                        while ($latest_posts->have_posts()) : $latest_posts->the_post();
                            $categories = get_the_category();
                            $category_name = !empty($categories) ? esc_html($categories[0]->name) : '';
                            $post_date = get_the_date('j F Y');
                            $excerpt = wp_trim_words(get_the_excerpt(), 18, '...');
                            ?>
                            <div class="swiper-slide ki-post-slide">
                                <article class="ki-post-card">
                                    <div class="ki-post-thumbnail-wrapper">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="ki-post-thumbnail">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('medium_large', array('class' => 'ki-post-image')); ?>
                                                    <div class="ki-post-overlay">
                                                        <span class="ki-read-more-overlay">
                                                            <i class="fas fa-arrow-left"></i>
                                                            مطالعه بیشتر
                                                        </span>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="ki-post-thumbnail">
                                                <a href="<?php the_permalink(); ?>">
                                                    <div class="ki-post-placeholder">
                                                        <i class="fas fa-newspaper"></i>
                                                    </div>
                                                    <div class="ki-post-overlay">
                                                        <span class="ki-read-more-overlay">
                                                            <i class="fas fa-arrow-left"></i>
                                                            مطالعه بیشتر
                                                        </span>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($category_name): ?>
                                            <span class="ki-post-category-badge">
                                                <i class="far fa-folder"></i>
                                                <?php echo $category_name; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="ki-post-content">
                                        <div class="ki-post-meta">
                                            <span class="ki-post-date">
                                                <i class="far fa-calendar-alt"></i>
                                                <?php echo $post_date; ?>
                                            </span>
                                            <span class="ki-post-author">
                                                <i class="far fa-user"></i>
                                                <?php the_author(); ?>
                                            </span>
                                        </div>
                                        
                                        <h3 class="ki-post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        
                                        <div class="ki-post-excerpt">
                                            <?php echo $excerpt; ?>
                                        </div>
                                        
                                        <a href="<?php the_permalink(); ?>" class="ki-read-more-btn">
                                            ادامه مطلب
                                            <i class="fas fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </article>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                        ?>
                        <p class="ki-no-posts">مطالبی یافت نشد.</p>
                        <?php
                    endif;
                    ?>
                </div>
                <div class="swiper-button-prev ki-blog-carousel-prev"></div>
                <div class="swiper-button-next ki-blog-carousel-next"></div>
            </div>
        </div>
    </div>
</section>

<?php
$template_uri = get_template_directory_uri();
$eslimi_lady = esc_url($template_uri . '/images/image-home/' . rawurlencode('بانو.png'));
$eslimi_child = esc_url($template_uri . '/images/image-home/' . rawurlencode('کودک.png'));
?>
<style>
.ki-latest-posts-section {
    padding: 60px 0;
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 20%, #e0f7fa 40%, #e0f2fe 60%, #e0f7fa 80%, #b2ebf2 100%);
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-latest-posts-section::before {
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

.ki-latest-posts-section::after {
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

.ki-latest-posts-section .container {
    position: relative;
    z-index: 1;
}

.ki-posts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 20px;
}

.ki-posts-subtitle {
    display: inline-block;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.ki-posts-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--zs-teal-dark, #00A796);
    margin: 0 0 6px 0;
    line-height: 1.3;
}

.ki-posts-description {
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
}

.ki-view-all-btn:hover {
    transform: translateY(-1px);
    background: var(--zs-teal-medium, #37C3B3);
}

/* Carousel Wrapper */
.ki-posts-carousel-wrapper {
    position: relative;
    padding: 0 40px;
    overflow: hidden;
}

.ki-posts-carousel.swiper {
    overflow: hidden;
}

.ki-posts-carousel .swiper-wrapper {
    display: flex;
    align-items: stretch;
}

.ki-post-slide.swiper-slide {
    height: auto;
    display: flex;
    width: 100%;
}

/* Modern Post Card */
.ki-post-card {
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

.ki-post-card:hover {
    transform: translateY(-4px);
    border-color: #dee2e6;
}

.ki-post-thumbnail-wrapper {
    position: relative;
    width: 100%;
    height: 240px;
    overflow: hidden;
    background: #fafbfc;
}

.ki-post-thumbnail {
    width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.ki-post-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.ki-post-card:hover .ki-post-image {
    transform: scale(1.05);
}

.ki-post-overlay {
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

.ki-post-card:hover .ki-post-overlay {
    opacity: 1;
}

.ki-read-more-overlay {
    color: #ffffff;
    font-size: 15px;
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

.ki-post-card:hover .ki-read-more-overlay {
    transform: translateY(0);
}

.ki-post-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fafbfc;
}

.ki-post-placeholder i {
    font-size: 48px;
    color: var(--zs-teal-medium, #37C3B3);
    opacity: 0.3;
}

.ki-post-category-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #ffffff;
    color: var(--zs-teal-medium, #37C3B3);
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    z-index: 3;
    transition: all 0.3s ease;
}

.ki-post-card:hover .ki-post-category-badge {
    background: var(--zs-teal-medium, #37C3B3);
    color: #ffffff;
    transform: scale(1.03);
}

.ki-post-content {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}

.ki-post-meta {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    font-size: 12px;
    color: #94a3b8;
    flex-wrap: wrap;
}

.ki-post-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.ki-post-meta i {
    font-size: 11px;
}

.ki-post-date {
    color: #64748b;
}

.ki-post-author {
    color: #94a3b8;
}

.ki-post-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 10px 0;
    line-height: 1.5;
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ki-post-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.ki-post-title a:hover {
    color: var(--zs-teal-dark, #00A796);
}

.ki-post-excerpt {
    font-size: 14px;
    color: #64748b;
    line-height: 1.8;
    margin-bottom: 20px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ki-read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-top: auto;
    padding: 10px 0;
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
}

.ki-read-more-btn:hover {
    color: var(--zs-teal-dark, #00A796);
    gap: 12px;
}

.ki-read-more-btn i {
    transition: transform 0.3s ease;
}

.ki-read-more-btn:hover i {
    transform: translateX(-4px);
}

/* Carousel Navigation Buttons - Swiper */
.ki-blog-carousel-prev.swiper-button-prev,
.ki-blog-carousel-next.swiper-button-next {
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
}

.ki-blog-carousel-prev.swiper-button-prev {
    right: 0;
}

.ki-blog-carousel-next.swiper-button-next {
    left: 0;
}

.ki-blog-carousel-prev.swiper-button-prev::after,
.ki-blog-carousel-next.swiper-button-next::after {
    font-size: 16px;
    font-weight: 900;
}

.ki-blog-carousel-prev.swiper-button-prev:hover,
.ki-blog-carousel-next.swiper-button-next:hover {
    background: var(--zs-teal-medium, #37C3B3);
    color: #ffffff;
    border-color: var(--zs-teal-medium, #37C3B3);
}

.ki-no-posts {
    text-align: center;
    color: #94a3b8;
    font-size: 16px;
    padding: 60px 20px;
    grid-column: 1 / -1;
}

/* Responsive Design */
@media (max-width: 992px) {
    .ki-posts-carousel-wrapper {
        padding: 0 40px;
    }
}

@media (max-width: 768px) {
    .ki-latest-posts-section {
        padding: 30px 0;
        background: #ffffff;
    }

    .ki-posts-header {
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: 0 5px;
    }

    /* Hide subtitle and description on mobile */
    .ki-posts-subtitle,
    .ki-posts-description {
        display: none;
    }

    .ki-posts-title {
        font-size: 20px;
        margin: 0;
        font-weight: 600;
    }

    .ki-posts-title-link {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
    }

    .ki-posts-title-link:hover .ki-posts-title {
        color: var(--zs-teal-medium, #37C3B3);
    }

    /* Hide view all button on mobile */
    .ki-view-all-btn-desktop {
        display: none !important;
    }
    
    .ki-posts-carousel-wrapper {
        padding: 0 40px;
    }
    
    .ki-post-thumbnail-wrapper {
        height: 200px;
    }
    
    .ki-blog-carousel-prev.swiper-button-prev,
    .ki-blog-carousel-next.swiper-button-next {
        width: 32px;
        height: 32px;
        font-size: 12px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(55, 195, 179, 0.2);
    }
    
    .ki-blog-carousel-prev.swiper-button-prev::after,
    .ki-blog-carousel-next.swiper-button-next::after {
        font-size: 12px;
    }

    .ki-post-content {
        padding: 16px;
    }

    .ki-post-title {
        font-size: 15px;
        min-height: 44px;
    }

    .ki-post-excerpt {
        font-size: 13px;
        margin-bottom: 16px;
    }
}

@media (max-width: 480px) {
    .ki-posts-title {
        font-size: 24px;
    }
    
    .ki-post-content {
        padding: 20px;
    }
    
    .ki-post-title {
        font-size: 18px;
        min-height: 54px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const blogCarousel = document.getElementById('blogPostsCarousel');
    if (!blogCarousel) return;
    
    const swiper = new Swiper('#blogPostsCarousel', {
        slidesPerView: 1,
        spaceBetween: 24,
        rtl: true,
        navigation: {
            nextEl: '.ki-blog-carousel-next',
            prevEl: '.ki-blog-carousel-prev',
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

