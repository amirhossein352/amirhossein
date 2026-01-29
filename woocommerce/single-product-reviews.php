<?php
/**
 * Single Product Reviews Template (Enhanced UI)
 *
 * @package khane_irani
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

if (!comments_open()) {
    return;
}
?>

<div id="reviews" class="woocommerce-Reviews">
    <?php
    // Fetch approved product reviews explicitly to avoid relying on have_comments()
    $approved_reviews = get_comments(array(
        'post_id' => $product->get_id(),
        'status'  => 'approve',
        'type'    => 'review',
        'number'  => 0,
    ));
    $has_reviews = !empty($approved_reviews);
    ?>

    <div class="zs-reviews-summary">
        <?php
        $average      = $product->get_average_rating();
        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        ?>
        <div class="zs-reviews-summary-card">
            <div class="zs-reviews-average">
                <div class="zs-reviews-average-number"><?php echo esc_html(wc_format_decimal($average, 1)); ?></div>
                <div class="star-rating" role="img" aria-label="<?php printf(esc_attr__('Rated %s out of 5', 'woocommerce'), esc_html($average)); ?>">
                    <span style="width: <?php echo (($average / 5) * 100); ?>%">
                        <?php printf(esc_html__('Rated %s out of 5', 'woocommerce'), esc_html($average)); ?>
                    </span>
                </div>
                <div class="zs-reviews-count"><?php printf(esc_html__('%s نظر', 'woocommerce'), esc_html($review_count)); ?></div>
            </div>
            <div class="zs-reviews-distribution" aria-hidden="true">
                <?php for ($i = 5; $i >= 1; $i--) :
                    $count_for_star = get_comments(array(
                        'post_id' => $product->get_id(),
                        'status'  => 'approve',
                        'count'   => true,
                        'meta_query' => array(
                            array(
                                'key' => 'rating',
                                'value' => $i,
                                'compare' => '='
                            )
                        )
                    ));
                    $percent = $review_count ? round(($count_for_star / $review_count) * 100) : 0;
                ?>
                <div class="zs-distribution-row">
                    <span class="zs-distribution-label"><?php echo esc_html($i); ?> ★</span>
                    <div class="zs-distribution-bar">
                        <div class="zs-distribution-fill" style="width: <?php echo esc_attr($percent); ?>%"></div>
                    </div>
                    <span class="zs-distribution-count"><?php echo esc_html($count_for_star); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="zs-reviews-actions">
            <button class="zs-reviews-write-btn" data-scroll-to="#review_form">
                <i class="fas fa-pen"></i>
                <span><?php esc_html_e('ثبت نظر', 'woocommerce'); ?></span>
            </button>
            <div class="zs-reviews-filters">
                <label>
                    <span><?php esc_html_e('مرتب‌سازی:', 'woocommerce'); ?></span>
                    <select id="zs-reviews-sort">
                        <option value="newest"><?php esc_html_e('جدیدترین', 'woocommerce'); ?></option>
                        <option value="highest"><?php esc_html_e('بالاترین امتیاز', 'woocommerce'); ?></option>
                        <option value="lowest"><?php esc_html_e('پایین‌ترین امتیاز', 'woocommerce'); ?></option>
                    </select>
                </label>
                <label class="zs-filter">
                    <input type="checkbox" id="zs-filter-verified" />
                    <span><?php esc_html_e('فقط خریدهای تایید شده', 'woocommerce'); ?></span>
                </label>
                <label class="zs-filter">
                    <input type="checkbox" id="zs-filter-media" />
                    <span><?php esc_html_e('فقط دارای تصویر/ویدیو', 'woocommerce'); ?></span>
                </label>
            </div>
        </div>
    </div>

    <div id="comments" class="woocommerce-Reviews-list">
        <?php if ($has_reviews) : ?>
            <ol class="commentlist">
                <?php
                // Ensure only WooCommerce reviews are rendered
                wp_list_comments(array(
                    'type'     => 'review',
                    'callback' => 'woocommerce_comments',
                ), $approved_reviews);
                ?>
            </ol>

            <?php if (get_comment_pages_count($approved_reviews) > 1 && get_option('page_comments')) : ?>
                <nav class="woocommerce-pagination">
                    <?php paginate_comments_links(array(
                        'prev_text' => '«',
                        'next_text' => '»'
                    )); ?>
                </nav>
            <?php endif; ?>
        <?php else : ?>
            <p class="woocommerce-noreviews"><?php esc_html_e('هنوز نظری ثبت نشده است.', 'woocommerce'); ?></p>
        <?php endif; ?>
    </div>

    <div id="review_form_wrapper">
        <div id="review_form">
            <?php
            $commenter  = wp_get_current_commenter();
            $comment_form = array(
                'title_reply'          => have_comments() ? esc_html__('افزودن نظر جدید', 'woocommerce') : sprintf(esc_html__('اولین نفری باشید که برای "%s" نظر می‌دهد', 'woocommerce'), get_the_title()),
                'title_reply_to'       => esc_html__('پاسخ به %s', 'woocommerce'),
                'title_reply_before'   => '<span id="reply-title" class="comment-reply-title">',
                'title_reply_after'    => '</span>',
                'comment_notes_after'  => '',
                'label_submit'         => esc_html__('ارسال نظر', 'woocommerce'),
                'logged_in_as'         => '',
                'comment_field'        => '',
            );

            $name_email_required = (bool) get_option('require_name_email', 1);
            $fields = array(
                'author' => '<p class="comment-form-author">'
                    . '<label for="author">' . esc_html__('نام', 'woocommerce') . ( $name_email_required ? ' <span class="required">*</span>' : '' ) . '</label> '
                    . '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" ' . ( $name_email_required ? 'required' : '' ) . ' /></p>',
                'email'  => '<p class="comment-form-email">'
                    . '<label for="email">' . esc_html__('ایمیل', 'woocommerce') . ( $name_email_required ? ' <span class="required">*</span>' : '' ) . '</label> '
                    . '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" ' . ( $name_email_required ? 'required' : '' ) . ' /></p>',
            );
            $comment_form['fields'] = $fields;

            $account_page_url = wc_get_page_permalink('myaccount');
            if (get_option('woocommerce_review_rating_verification_required') === 'yes' && !wc_customer_bought_product('', get_current_user_id(), $product->get_id())) {
                echo '<p class="woocommerce-verification-required">' . wp_kses_post(sprintf(__('تنها مشتریانی که این محصول را خریده‌اند می‌توانند نظر دهند. <a href="%s">وارد شوید</a>', 'woocommerce'), esc_url($account_page_url))) . '</p>';
            } else {
                $comment_form['comment_field'] = '<p class="comment-form-comment">'
                    . '<label for="comment">' . esc_html__('متن نظر شما', 'woocommerce') . '</label>'
                    . '<textarea id="comment" name="comment" cols="45" rows="8" required></textarea>'
                    . '</p>';

                if (wc_review_ratings_enabled()) {
                    $comment_form['comment_field'] .= '<p class="comment-form-rating">'
                        . '<label for="rating">' . esc_html__('امتیاز شما', 'woocommerce') . '</label>'
                        . '<select name="rating" id="rating" required>'
                        . '<option value="">' . esc_html__('انتخاب کنید…', 'woocommerce') . '</option>'
                        . '<option value="5">' . esc_html__('عالی', 'woocommerce') . '</option>'
                        . '<option value="4">' . esc_html__('خوب', 'woocommerce') . '</option>'
                        . '<option value="3">' . esc_html__('معمولی', 'woocommerce') . '</option>'
                        . '<option value="2">' . esc_html__('ضعیف', 'woocommerce') . '</option>'
                        . '<option value="1">' . esc_html__('خیلی بد', 'woocommerce') . '</option>'
                        . '</select>'
                        . '</p>';
                }

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
            }
            ?>
        </div>
    </div>
</div>


