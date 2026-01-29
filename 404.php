<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <div class="error-404 not-found">
            <div class="error-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <h1 class="page-title">صفحه مورد نظر یافت نشد!</h1>
                
                <div class="page-content">
                    <p>متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد یا حذف شده است.</p>
                    
                    <div class="error-actions">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                            <i class="fas fa-home"></i>
                            بازگشت به صفحه اصلی
                        </a>
                        
                        <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn btn-secondary">
                            <i class="fas fa-shopping-cart"></i>
                            مشاهده فروشگاه
                        </a>
                    </div>
                    
                    <div class="search-section">
                        <h3>جستجو در سایت:</h3>
                        <?php get_search_form(); ?>
                    </div>
                    
                    <div class="helpful-links">
                        <h3>صفحات مفید:</h3>
                        <ul>
                            <li><a href="<?php echo esc_url(home_url('/about')); ?>">درباره ما</a></li>
                            <li><a href="<?php echo esc_url(home_url('/contact')); ?>">تماس با ما</a></li>
                            <li><a href="<?php echo esc_url(home_url('/shop')); ?>">فروشگاه</a></li>
                            <li><a href="<?php echo esc_url(home_url('/blog')); ?>">وبلاگ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
