<?php
/**
 * The template for displaying comments
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $khane_irani_comment_count = get_comments_number();
            if ('1' === $khane_irani_comment_count) {
                printf(
                    esc_html__('یک نظر برای "%1$s"', 'khane-irani'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    esc_html(_nx('%1$s نظر برای "%2$s"', '%1$s نظر برای "%2$s"', $khane_irani_comment_count, 'comments title', 'khane-irani')),
                    number_format_i18n($khane_irani_comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 60,
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation();

        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('نظرات بسته شده‌اند.', 'khane-irani'); ?></p>
            <?php
        endif;

    endif;

    // Generate simple math captcha
    if (!session_id()) {
        session_start();
    }
    $num1 = rand(5, 15);
    $num2 = rand(1, 10);
    $captcha_answer = $num1 - $num2;
    $_SESSION['comment_captcha'] = $captcha_answer;
    
    comment_form(array(
        'title_reply' => 'دیدگاه شما',
        'title_reply_to' => 'پاسخ به %s',
        'cancel_reply_link' => 'لغو پاسخ',
        'label_submit' => 'ارسال دیدگاه',
        'comment_field' => '<div class="comment-form-row"><p class="comment-form-comment"><label for="comment">دیدگاه دیدگاه <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required="required" placeholder="دیدگاه خود را بنویسید..."></textarea></p></div>',
        'comment_notes_before' => '',
        'comment_notes_after' => '',
        'fields' => array(
            'author' => '<div class="comment-form-row comment-form-row-top"><p class="comment-form-author"><label for="author">نام <span class="required">*</span></label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" required="required" placeholder="نام شما" /></p>',
            'email' => '<p class="comment-form-email"><label for="email">ایمیل <span class="required">*</span></label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" required="required" placeholder="ایمیل شما" /></p></div>',
            'url' => '',
        ),
        'submit_field' => '<div class="comment-form-row comment-form-row-bottom"><p class="comment-form-security"><label for="comment-security">پرسش امنیتی <span class="required">*</span></label><span class="security-question">' . $num1 . ' = <input type="number" id="comment-security" name="comment_security" size="5" required="required" /> + ' . $num2 . '</span></p><p class="form-submit">%1$s %2$s</p></div>',
    ));
    ?>

</div>
