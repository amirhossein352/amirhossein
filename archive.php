<?php
/**
 * The template for displaying archive pages
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <div class="content-sidebar-wrapper">
            <div class="content-area">
                
                <?php if (have_posts()) : ?>
                    
                    <header class="page-header">
                        <?php
                        the_archive_title('<h1 class="page-title">', '</h1>');
                        the_archive_description('<div class="archive-description">', '</div>');
                        ?>
                    </header>

                    <div class="posts-grid">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                                
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
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
                    
                    <div class="no-posts">
                        <h2>مطلبی یافت نشد</h2>
                        <p>متأسفانه هیچ مطلبی در این بخش وجود ندارد.</p>
                    </div>
                    
                <?php endif; ?>
                
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
