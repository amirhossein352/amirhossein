<?php
/**
 * WooCommerce Reviews customizations (form UI/labels)
 * @package khane_irani
 */

if (!function_exists('khane_irani_customize_review_form')) {
    function khane_irani_customize_review_form($args) {
        $args['title_reply']         = __('ثبت نظر شما', 'khane-irani');
        $args['label_submit']        = __('ارسال نظر', 'khane-irani');
        $args['comment_notes_after'] = '';
        $args['class_submit']        = array('button', 'zs-review-submit');
        $args['submit_field']        = '<p class="form-submit">%1$s %2$s</p>';

        $args['comment_field'] =
            '<p class="comment-form-comment">'
            . '<label for="comment" class="zs-field-label">' . __('متن نظر', 'khane-irani') . '</label>'
            . '<textarea id="comment" name="comment" cols="45" rows="6" placeholder="' . esc_attr__('تجربه خود را با دیگران به اشتراک بگذارید...', 'khane-irani') . '" class="zs-input" required></textarea>'
            . '</p>';

        $author_placeholder = __('نام و نام خانوادگی', 'زarin-service');
        $email_placeholder  = __('ایمیل (نمایش داده نمی‌شود)', 'khane-irani');

        $args['fields']['author'] =
            '<p class="comment-form-author">'
            . '<label for="author" class="zs-field-label">' . __('نام', 'khane-irani') . '</label>'
            . '<input id="author" name="author" type="text" placeholder="' . esc_attr($author_placeholder) . '" class="zs-input" required />'
            . '</p>';

        $args['fields']['email'] =
            '<p class="comment-form-email">'
            . '<label for="email" class="zs-field-label">' . __('ایمیل', 'khane-irani') . '</label>'
            . '<input id="email" name="email" type="email" placeholder="' . esc_attr($email_placeholder) . '" class="zs-input" required />'
            . '</p>';

        $args['class_form'] = array('comment-form', 'zs-review-form');

        return $args;
    }
}

add_filter('woocommerce_product_review_comment_form_args', 'khane_irani_customize_review_form');


