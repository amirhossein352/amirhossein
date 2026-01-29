<?php
/**
 * Custom template tags for this theme
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!function_exists('khane_irani_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function khane_irani_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x('منتشر شده در %s', 'post date', 'khane-irani'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>';
    }
endif;

if (!function_exists('khane_irani_posted_by')) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function khane_irani_posted_by() {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x('نوشته شده توسط %s', 'post author', 'khane-irani'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>';
    }
endif;

if (!function_exists('khane_irani_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function khane_irani_entry_footer() {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'khane-irani'));
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . esc_html__('دسته‌بندی: %1$s', 'khane-irani') . '</span>', $categories_list);
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'khane-irani'));
            if ($tags_list) {
                /* translators: 1: list of tags. */
                printf('<span class="tags-links">' . esc_html__('برچسب‌ها: %1$s', 'khane-irani') . '</span>', $tags_list);
            }
        }

        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('نظر دهید <span class="screen-reader-text">در %s</span>', 'khane-irani'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('ویرایش <span class="screen-reader-text">%s</span>', 'khane-irani'),
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
    }
endif;

if (!function_exists('khane_irani_post_thumbnail')) :
    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function khane_irani_post_thumbnail() {
        if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
            return;
        }

        if (is_singular()) :
            ?>

            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div><!-- .post-thumbnail -->

        <?php else : ?>

            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    'post-thumbnail',
                    array(
                        'alt' => the_title_attribute(
                            array(
                                'echo' => false,
                            )
                        ),
                    )
                );
                ?>
            </a>

            <?php
        endif; // End is_singular().
    }
endif;

if (!function_exists('khane_irani_comment')) :
    /**
     * Template for comments and pingbacks.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     */
    function khane_irani_comment($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback':
            case 'trackback':
                ?>
                <li class="post pingback">
                    <p><?php _e('Pingback:', 'khane-irani'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)', 'khane-irani'), ' '); ?></p>
                <?php
                break;
            default:
                ?>
                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                    <article id="comment-<?php comment_ID(); ?>" class="comment-body">
                        <footer class="comment-meta">
                            <div class="comment-author vcard">
                                <?php echo get_avatar($comment, 60); ?>
                                <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>', 'khane-irani'), get_comment_author_link()); ?>
                            </div><!-- .comment-author .vcard -->

                            <?php if ($comment->comment_approved == '0') : ?>
                                <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'khane-irani'); ?></em>
                                <br />
                            <?php endif; ?>

                            <div class="comment-metadata">
                                <a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>">
                                    <?php printf(__('%1$s at %2$s', 'khane-irani'), get_comment_date(), get_comment_time()); ?>
                                </a>
                                <?php edit_comment_link(__('(Edit)', 'khane-irani'), '  ', ''); ?>
                            </div><!-- .comment-metadata -->
                        </footer>

                        <div class="comment-content"><?php comment_text(); ?></div>

                        <div class="reply">
                            <?php comment_reply_link(array_merge($args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        </div><!-- .reply -->
                    </article><!-- #comment-## -->

                <?php
                break;
        endswitch;
    }
endif;
