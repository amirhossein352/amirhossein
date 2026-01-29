<?php
/**
 * Single Product title
 * Custom styling for Zarin Service - Optimized
 *
 * @package khane_irani
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>

<h1 class="product_title entry-title zs-product-title">
    <?php the_title(); ?>
</h1>

<style>
.zs-product-title {
    font-size: 2rem !important;
    font-weight: 700 !important;
    color: #1a202c !important;
    margin: 0 0 20px 0 !important;
    line-height: 1.2 !important;
    letter-spacing: -0.01em !important;
    background: linear-gradient(135deg, #2d3748, #4a5568) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

@media (max-width: 768px) {
    .zs-product-title {
        font-size: 1.5rem !important;
    }
}
</style>
