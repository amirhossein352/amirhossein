<?php
/**
 * The sidebar containing the main widget area
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

?>

<aside id="secondary" class="widget-area">
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
    
    <!-- Default sidebar content if no widgets or on single post pages -->
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
</aside>
