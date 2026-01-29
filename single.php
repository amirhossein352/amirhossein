<?php
/**
 * The template for displaying all single posts
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <?php
        // Breadcrumb Navigation
        ?>
        <nav class="zs-breadcrumb-nav single-post-breadcrumb" aria-label="مسیر ناوبری">
            <ol class="zs-breadcrumb-list">
                <li class="zs-breadcrumb-item">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="zs-breadcrumb-link">
                        <i class="fas fa-home"></i> خانه
                    </a>
                </li>
                <li class="zs-breadcrumb-separator">›</li>
                <li class="zs-breadcrumb-item">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="zs-breadcrumb-link">
                        بلاگ
                    </a>
                </li>
                <?php
                $categories = get_the_category();
                if (!empty($categories)) {
                    $main_category = $categories[0];
                    ?>
                    <li class="zs-breadcrumb-separator">›</li>
                    <li class="zs-breadcrumb-item">
                        <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>" class="zs-breadcrumb-link">
                            <?php echo esc_html($main_category->name); ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <li class="zs-breadcrumb-separator">›</li>
                <li class="zs-breadcrumb-item zs-breadcrumb-current">
                    <?php the_title(); ?>
                </li>
            </ol>
        </nav>
        
        <!-- Mobile Sidebar Toggle Button -->
        <button class="mobile-sidebar-toggle" id="mobile-sidebar-toggle" aria-label="باز کردن سایدبار">
            <i class="fas fa-bars"></i>
            <span>سایدبار</span>
        </button>
        
        <div class="content-sidebar-wrapper">
            <div class="content-area">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                        
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                            
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
                            
                            <?php
                            // Social Share Buttons
                            $post_url = urlencode(get_permalink());
                            $post_title = urlencode(get_the_title());
                            ?>
                        </header>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <?php
                            the_content();

                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'khane-irani'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>

                        <footer class="entry-footer">
                            <div class="post-social-share mobile-share-bottom">
                                <span class="share-label">اشتراک گذاری:</span>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $post_url; ?>" target="_blank" class="share-btn share-facebook" title="اشتراک در فیسبوک">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>" target="_blank" class="share-btn share-twitter" title="اشتراک در توییتر">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $post_url; ?>&title=<?php echo $post_title; ?>" target="_blank" class="share-btn share-linkedin" title="اشتراک در لینکدین">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://wa.me/?text=<?php echo $post_title . ' ' . $post_url; ?>" target="_blank" class="share-btn share-whatsapp" title="اشتراک در واتساپ">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://telegram.me/share/url?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>" target="_blank" class="share-btn share-telegram" title="اشتراک در تلگرام">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                            </div>
                            <?php if (has_tag()) : ?>
                                <div class="tags-links">
                                    <i class="fas fa-tags"></i>
                                    <?php the_tags('', ', '); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-navigation">
                                <?php
                                the_post_navigation(array(
                                    'prev_text' => '<span class="nav-subtitle">قبلی</span> <span class="nav-title">%title</span>',
                                    'next_text' => '<span class="nav-subtitle">بعدی</span> <span class="nav-title">%title</span>',
                                ));
                                ?>
                            </div>
                        </footer>
                    </article>

                    <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>

                <?php endwhile; ?>
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" id="mobile-sidebar-overlay"></div>
    
    <!-- Mobile Sidebar Panel -->
    <aside class="mobile-sidebar-panel" id="mobile-sidebar-panel">
        <div class="mobile-sidebar-header">
            <h3>سایدبار</h3>
            <button class="mobile-sidebar-close" id="mobile-sidebar-close" aria-label="بستن سایدبار">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-sidebar-content">
            <?php
            // Display widgets if sidebar is active
            if (is_active_sidebar('sidebar-1')) {
                dynamic_sidebar('sidebar-1');
            }
            ?>
            
            <?php
            // Related Posts Widget (only on single post pages)
            if (is_single() && get_post_type() === 'post') {
                $current_post_id = get_the_ID();
                $categories = wp_get_post_categories($current_post_id);
                
                if (!empty($categories)) {
                    $related_posts = get_posts(array(
                        'category__in' => $categories,
                        'post__not_in' => array($current_post_id),
                        'posts_per_page' => 5,
                        'orderby' => 'rand'
                    ));
                    
                    if (!empty($related_posts)) {
                        ?>
                        <section class="widget widget_related_posts">
                            <h2 class="widget-title">مقالات مرتبط</h2>
                            <ul class="related-posts-list">
                                <?php foreach ($related_posts as $related_post) : ?>
                                    <li class="related-post-item">
                                        <a href="<?php echo get_permalink($related_post->ID); ?>" class="related-post-link">
                                            <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                                <div class="related-post-thumbnail">
                                                    <?php echo get_the_post_thumbnail($related_post->ID, 'thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="related-post-content">
                                                <h3 class="related-post-title"><?php echo get_the_title($related_post->ID); ?></h3>
                                                <span class="related-post-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <?php echo get_the_date('', $related_post->ID); ?>
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                        <?php
                    }
                }
            }
            ?>
            
            <!-- Default sidebar content if no widgets -->
            <?php if (!is_active_sidebar('sidebar-1') || (is_single() && get_post_type() === 'post')) : ?>
                <?php if (!is_active_sidebar('sidebar-1')) : ?>
                    <section class="widget widget_search">
                        <h2 class="widget-title">جستجو</h2>
                        <?php get_search_form(); ?>
                    </section>
                    
                    <section class="widget widget_recent_posts">
                        <h2 class="widget-title">آخرین مطالب</h2>
                        <ul>
                            <?php
                            $recent_posts = wp_get_recent_posts(array(
                                'numberposts' => 5,
                                'post_status' => 'publish'
                            ));
                            
                            foreach ($recent_posts as $post) : ?>
                                <li>
                                    <a href="<?php echo get_permalink($post['ID']); ?>">
                                        <?php echo $post['post_title']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                    
                    <section class="widget widget_categories">
                        <h2 class="widget-title">دسته‌بندی‌ها</h2>
                        <ul>
                            <?php wp_list_categories(array(
                                'title_li' => '',
                                'show_count' => 1,
                                'orderby' => 'count',
                                'order' => 'DESC'
                            )); ?>
                        </ul>
                    </section>
                    
                    <section class="widget widget_tag_cloud">
                        <h2 class="widget-title">برچسب‌ها</h2>
                        <?php wp_tag_cloud(array(
                            'smallest' => 12,
                            'largest' => 18,
                            'unit' => 'px',
                            'number' => 20
                        )); ?>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </aside>
</main>

<?php get_footer(); ?>
