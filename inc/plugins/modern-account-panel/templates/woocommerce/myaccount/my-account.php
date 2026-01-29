<?php
/**
 * My Account page - Modern Account Panel Override
 *
 * This template preserves all WooCommerce functionality while adding custom styling.
 *
 * @see     https://woo.com/document/template-structure/
 * @package Modern Account Panel
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Debug: Log template loading
error_log('Modern Account Panel: my-account.php template loaded');

/**
 * My Account navigation.
 * This will use our custom navigation.php template with modern styling.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_navigation');

/**
 * My Account content.
 * This preserves all WooCommerce endpoint content and custom plugin integrations.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_content');

/**
 * After account content - for taskbar and other elements
 */
do_action('woocommerce_after_account_content');