<?php
/**
 * The template for displaying all pages
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <div class="content-sidebar-wrapper">
            <div class="content-area">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                        
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="page-thumbnail">
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

                        <?php if (get_edit_post_link()) : ?>
                            <footer class="entry-footer">
                                <?php
                                edit_post_link(
                                    sprintf(
                                        wp_kses(
                                            __('Edit <span class="screen-reader-text">"%s"</span>', 'khane-irani'),
                                            array(
                                                'span' => array(
                                                    'class' => array(),
                                                ),
                                            )
                                        ),
                                        wp_kses_post(get_the_title())
                                    ),
                                    '<span class="edit-link">',
                                    '</span>'
                                );
                                ?>
                            </footer>
                        <?php endif; ?>
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
</main>

<?php get_footer(); ?>
