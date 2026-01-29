<?php
/**
 * Live Q&A backed by WordPress comments API (no CPT)
 * - Stores questions as comments with comment_type = 'live_qa' under the product (post_type=product)
 * - Admin can reply via Comments screen using threaded replies; child replies are also tagged 'live_qa'
 * - AJAX endpoints for submit and fetch
 * @package khane_irani
 */

if (!defined('ABSPATH')) { exit; }

// AJAX: Submit a live QA question
add_action('wp_ajax_zs_submit_live_question', 'zs_ajax_submit_live_question');
add_action('wp_ajax_nopriv_zs_submit_live_question', 'zs_ajax_submit_live_question');

function zs_ajax_submit_live_question() {
    check_ajax_referer('zs_live_qa', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $text       = isset($_POST['text']) ? wp_strip_all_tags(wp_unslash($_POST['text'])) : '';
    $author     = isset($_POST['author']) ? sanitize_text_field(wp_unslash($_POST['author'])) : '';
    $topic      = isset($_POST['topic']) ? sanitize_text_field(wp_unslash($_POST['topic'])) : 'general';

    if (!$product_id || empty($text)) {
        wp_send_json_error(array('message' => __('پارامترها نامعتبر است.', 'khane-irani')), 400);
    }

    // Build comment data
    $comment_data = array(
        'comment_post_ID'      => $product_id,
        'comment_author'       => $author ?: __('کاربر', 'khane-irani'),
        'comment_author_email' => '',
        'comment_content'      => $text,
        'comment_type'         => 'live_qa',
        'comment_parent'       => 0,
        'user_id'              => get_current_user_id(),
        'comment_approved'     => 1, // optional: set 0 to require moderation
        'comment_agent'        => 'zs-live-qa',
    );

    $comment_id = wp_insert_comment(wp_filter_comment($comment_data));
    if (!$comment_id || is_wp_error($comment_id)) {
        wp_send_json_error(array('message' => __('خطا در ثبت پرسش.', 'khane-irani')), 500);
    }

    // Save topic meta
    if (!empty($topic)) {
        add_comment_meta($comment_id, 'zs_live_topic', $topic, true);
    }

    wp_send_json_success(array('id' => $comment_id));
}

// AJAX: Get live QA list for a product
add_action('wp_ajax_zs_get_live_questions', 'zs_ajax_get_live_questions');
add_action('wp_ajax_nopriv_zs_get_live_questions', 'zs_ajax_get_live_questions');

function zs_ajax_get_live_questions() {
    $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
    if (!$product_id) {
        wp_send_json_error(array('message' => __('شناسه محصول نامعتبر است.', 'khane-irani')), 400);
    }

    $questions = get_comments(array(
        'post_id' => $product_id,
        'status'  => 'approve',
        'type'    => 'live_qa',
        'number'  => 20,
        'orderby' => 'comment_date_gmt',
        'order'   => 'DESC',
        'parent'  => 0,
    ));

    $items = array();
    foreach ($questions as $q) {
        // Fetch first admin reply (if any)
        $answers = get_comments(array(
            'post_id' => $product_id,
            'status'  => 'approve',
            'type'    => 'live_qa',
            'parent'  => $q->comment_ID,
            'number'  => 1,
            'orderby' => 'comment_date_gmt',
            'order'   => 'ASC',
        ));

        $topic = get_comment_meta($q->comment_ID, 'zs_live_topic', true);
        $items[] = array(
            'id'         => $q->comment_ID,
            'authorName' => $q->comment_author,
            'text'       => $q->comment_content,
            'timestamp'  => strtotime($q->comment_date_gmt) * 1000,
            'topic'      => $topic ?: 'general',
            'answer'     => isset($answers[0]) ? $answers[0]->comment_content : null,
            'answerTimestamp' => isset($answers[0]) ? (strtotime($answers[0]->comment_date_gmt) * 1000) : null,
        );
    }

    wp_send_json_success(array('items' => $items));
}

// Ensure replies to Live QA inherit type 'live_qa'
add_action('comment_post', function($comment_ID, $approved, $commentdata){
    $parent_id = isset($commentdata['comment_parent']) ? (int)$commentdata['comment_parent'] : 0;
    if ($parent_id > 0) {
        $parent = get_comment($parent_id);
        if ($parent && $parent->comment_type === 'live_qa') {
            wp_update_comment(array(
                'comment_ID'   => $comment_ID,
                'comment_type' => 'live_qa',
            ));
        }
    }
}, 10, 3);

// Admin: add submenu under Comments for Live Q&A filter
add_action('admin_menu', function(){
    add_comments_page(
        __('پرسش و پاسخ لایو', 'khane-irani'),
        __('پرسش و پاسخ لایو', 'khane-irani'),
        'moderate_comments',
        'edit-comments.php?comment_type=live_qa'
    );
});

// Admin: add Product column and Topic column when viewing live_qa
add_filter('manage_edit-comments_columns', function($columns){
    $screen = get_current_screen();
    if ($screen && $screen->id === 'edit-comments' && isset($_GET['comment_type']) && $_GET['comment_type'] === 'live_qa') {
        $columns['zs_product'] = __('محصول', 'khane-irani');
        $columns['zs_topic']   = __('موضوع', 'khane-irani');
    }
    return $columns;
});

add_action('manage_comments_custom_column', function($column, $comment_ID){
    if ($column === 'zs_product') {
        $c = get_comment($comment_ID);
        if ($c && $c->comment_post_ID) {
            $title = get_the_title($c->comment_post_ID);
            $link  = get_edit_post_link($c->comment_post_ID);
            echo $link ? '<a href="' . esc_url($link) . '">' . esc_html($title) . '</a>' : esc_html($title);
        }
    } elseif ($column === 'zs_topic') {
        $topic = get_comment_meta($comment_ID, 'zs_live_topic', true);
        echo esc_html($topic ?: '-');
    }
}, 10, 2);


