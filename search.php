<?php
/**
 * The template for displaying search results pages
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <div class="content-sidebar-wrapper">
            <div class="content-area">
                
                <header class="page-header">
                    <h1 class="page-title">
                        <?php
                        printf(
                            esc_html__('نتایج جستجو برای: %s', 'khane-irani'),
                            '<span>' . get_search_query() . '</span>'
                        );
                        ?>
                    </h1>
                </header>

                <?php if (have_posts()) : ?>
                    
                    <div class="search-results">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                                
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="result-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="result-content">
                                    <header class="entry-header">
                                        <h2 class="entry-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="entry-meta">
                                            <span class="posted-on">
                                                <i class="fas fa-calendar"></i>
                                                <?php echo get_the_date(); ?>
                                            </span>
                                            <span class="byline">
                                                <i class="fas fa-user"></i>
                                                <?php echo get_the_author(); ?>
                                            </span>
                                            <?php if (has_category()) : ?>
                                                <span class="cat-links">
                                                    <i class="fas fa-folder"></i>
                                                    <?php the_category(', '); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </header>
                                    
                                    <div class="entry-summary">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <footer class="entry-footer">
                                        <a href="<?php the_permalink(); ?>" class="read-more">
                                            ادامه مطلب
                                        </a>
                                    </footer>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php
                    // Pagination
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => 'قبلی',
                        'next_text' => 'بعدی',
                    ));
                    ?>
                    
                <?php else : ?>
                    
                    <div class="no-results">
                        <h2>نتیجه‌ای یافت نشد</h2>
                        <p>متأسفانه هیچ نتیجه‌ای برای جستجوی شما یافت نشد. لطفاً کلمات کلیدی دیگری را امتحان کنید.</p>
                        
                        <div class="search-suggestions">
                            <h3>پیشنهادات جستجو:</h3>
                            <ul>
                                <li>کلمات کلیدی خود را بررسی کنید</li>
                                <li>از کلمات کلیدی عمومی‌تر استفاده کنید</li>
                                <li>کلمات کلیدی را به صورت جداگانه جستجو کنید</li>
                            </ul>
                        </div>
                        
                        <div class="search-form-container">
                            <h3>جستجوی جدید:</h3>
                            <?php get_search_form(); ?>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
