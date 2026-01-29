<?php
/**
 * Banner Slider Section - Full Width Slider
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */
?>
<!-- Banner Slider Section - Hero Slider Enabled: <?php echo khane_irani_get_setting('hero_slider_enabled', '1'); ?> -->
<section class="banner-slider-section">
    <div class="banner-slider-container">
        <div class="banner-slider-wrapper">
            <div class="swiper banner-slider" id="bannerSlider">
                <div class="swiper-wrapper">
                <?php
                // Get banner items from theme settings or use default
                $options = get_option('khane-irani-settings', array());
                $banners = isset($options['banners_items']) ? $options['banners_items'] : array();
                
                // Ensure $banners is always an array
                if (!is_array($banners)) {
                    $banners = array();
                }
                
                // Initialize $valid_banners as array
                $valid_banners = array();

                // Debug: Always show what we have
                echo '<!-- All options: ' . json_encode($options) . ' -->';
                echo '<!-- Banners items: ' . json_encode($banners) . ' -->';

                if (!empty($banners) && is_array($banners)) {
                    // Clean up banners array - remove empty entries
                    $banners = array_filter($banners, function($banner) {
                        return !empty($banner) && is_array($banner);
                    });
                    
                    // Filter only active banners
                    $banners = array_filter($banners, function($banner) {
                        return !isset($banner['is_active']) || $banner['is_active'] === '1';
                    });
                    
                    // Re-index array after filtering
                    $banners = array_values($banners);

                    // Sort banners by sort_order
                    usort($banners, function($a, $b) {
                        $orderA = isset($a['sort_order']) ? intval($a['sort_order']) : 0;
                        $orderB = isset($b['sort_order']) ? intval($b['sort_order']) : 0;
                        return $orderA <=> $orderB;
                    });

                    // Track processed image IDs to prevent duplicates
                    $processed_image_ids = array();
                    $valid_banners = array();

                    foreach ($banners as $index => $banner) {
                        // Debug: Show banner data
                        echo '<!-- Processing banner ' . $index . ': ' . json_encode($banner) . ' -->';

                        // Get image ID - handle both string and integer, also handle empty strings
                        $image_id = 0;
                        $mobile_image_id = 0;
                        if (isset($banner['image']) && !empty($banner['image'])) {
                            // Convert to integer, handle both string and integer
                            $raw_image = $banner['image'];
                            if (is_numeric($raw_image)) {
                                $image_id = absint($raw_image);
                            } elseif (is_string($raw_image) && trim($raw_image) !== '') {
                                // Try to extract number from string
                                $image_id = absint($raw_image);
                            }
                        }

                        echo '<!-- Banner ' . $index . ' image ID: ' . $image_id . ' -->';
                        
                        if (isset($banner['mobile_image']) && !empty($banner['mobile_image'])) {
                            $raw_mobile_image = $banner['mobile_image'];
                            if (is_numeric($raw_mobile_image)) {
                                $mobile_image_id = absint($raw_mobile_image);
                            } elseif (is_string($raw_mobile_image) && trim($raw_mobile_image) !== '') {
                                $mobile_image_id = absint($raw_mobile_image);
                            }
                        }
                        
                        // Skip if image ID is 0
                        if ($image_id === 0) {
                            continue;
                        }
                        
                        // Skip if already processed (prevent duplicates)
                        if (in_array($image_id, $processed_image_ids)) {
                            continue;
                        }
                        
                        $image_url = '';
                        $mobile_image_url = '';
                        
                        if ($image_id > 0) {
                            // Verify attachment exists
                            $attachment = get_post($image_id);
                            if ($attachment && $attachment->post_type === 'attachment') {
                                // Try to get image URL with size
                                $image_url = wp_get_attachment_image_url($image_id, 'full');
                                
                                // Fallback to direct URL if wp_get_attachment_image_url fails
                                if (empty($image_url)) {
                                    $image_url = wp_get_attachment_url($image_id);
                                }
                                
                                // Verify the URL is valid and not empty
                                if (!empty($image_url) && filter_var($image_url, FILTER_VALIDATE_URL)) {
                                    // Mark this image ID as processed
                                    $processed_image_ids[] = $image_id;
                                    // Optional mobile image
                                    if ($mobile_image_id > 0) {
                                        $mobile_attachment = get_post($mobile_image_id);
                                        if ($mobile_attachment && $mobile_attachment->post_type === 'attachment') {
                                            $mobile_image_url = wp_get_attachment_image_url($mobile_image_id, 'full');
                                            if (empty($mobile_image_url)) {
                                                $mobile_image_url = wp_get_attachment_url($mobile_image_id);
                                            }
                                            if (!empty($mobile_image_url) && !filter_var($mobile_image_url, FILTER_VALIDATE_URL)) {
                                                $mobile_image_url = '';
                                            }
                                        }
                                    }

                                    $valid_banners[] = array(
                                        'image_url' => $image_url,
                                        'mobile_image_url' => $mobile_image_url,
                                        'link_url' => !empty($banner['url']) ? esc_url($banner['url']) : '#',
                                        'title' => !empty($banner['title']) ? esc_html($banner['title']) : ''
                                    );
                                }
                            }
                        }
                    }

                    // Output valid banners only if we have any
                    if (!empty($valid_banners)) {
                        // Get dimensions for first banner to set aspect ratio
                        $first_image_id = attachment_url_to_postid($valid_banners[0]['image_url']);
                        $first_mobile_image_id = !empty($valid_banners[0]['mobile_image_url']) ? attachment_url_to_postid($valid_banners[0]['mobile_image_url']) : 0;
                        
                        // Get image dimensions for aspect ratio calculation
                        $desktop_aspect_ratio = '400px'; // Default
                        $mobile_aspect_ratio = '180px'; // Default
                        
                        if ($first_image_id > 0) {
                            $desktop_meta = wp_get_attachment_metadata($first_image_id);
                            if ($desktop_meta && isset($desktop_meta['width']) && isset($desktop_meta['height']) && $desktop_meta['width'] > 0) {
                                // Calculate aspect ratio: height/width * 100vw
                                // Desktop banner is typically 1920x488, so aspect ratio is about 25.4%
                                $desktop_ratio = ($desktop_meta['height'] / $desktop_meta['width']) * 100;
                                $desktop_aspect_ratio = $desktop_ratio . '%';
                            }
                        }
                        
                        if ($first_mobile_image_id > 0) {
                            $mobile_meta = wp_get_attachment_metadata($first_mobile_image_id);
                            if ($mobile_meta && isset($mobile_meta['width']) && isset($mobile_meta['height']) && $mobile_meta['width'] > 0) {
                                // Mobile banner is typically 525x263, so aspect ratio is about 50%
                                $mobile_ratio = ($mobile_meta['height'] / $mobile_meta['width']) * 100;
                                $mobile_aspect_ratio = $mobile_ratio . '%';
                            }
                        }
                        
                        foreach ($valid_banners as $index => $banner_data) : 
                            // Get image ID from URL
                            $image_id = attachment_url_to_postid($banner_data['image_url']);
                            $mobile_image_id = !empty($banner_data['mobile_image_url']) ? attachment_url_to_postid($banner_data['mobile_image_url']) : 0;
                            
                            // First banner is LCP element
                            $is_lcp = ($index === 0);
                            
                            // Get responsive image attributes
                            $img_attrs = $image_id ? khane_irani_get_banner_image_attributes($image_id, $is_lcp) : array();
                            
                            // Fallback if image ID not found
                            if (empty($img_attrs)) {
                                $img_attrs = array(
                                    'src' => esc_url($banner_data['image_url']),
                                    'loading' => $is_lcp ? 'eager' : 'lazy',
                                    'decoding' => 'async',
                                );
                                if ($is_lcp) {
                                    $img_attrs['fetchpriority'] = 'high';
                                }
                            }
                            
                            // Get image dimensions for this specific banner
                            $banner_desktop_ratio = $desktop_aspect_ratio;
                            $banner_mobile_ratio = $mobile_aspect_ratio;
                            
                            if ($image_id > 0) {
                                $img_meta = wp_get_attachment_metadata($image_id);
                                if ($img_meta && isset($img_meta['width']) && isset($img_meta['height']) && $img_meta['width'] > 0) {
                                    $banner_desktop_ratio = (($img_meta['height'] / $img_meta['width']) * 100) . '%';
                                }
                            }
                            
                            if ($mobile_image_id > 0) {
                                $mobile_img_meta = wp_get_attachment_metadata($mobile_image_id);
                                if ($mobile_img_meta && isset($mobile_img_meta['width']) && isset($mobile_img_meta['height']) && $mobile_img_meta['width'] > 0) {
                                    $banner_mobile_ratio = (($mobile_img_meta['height'] / $mobile_img_meta['width']) * 100) . '%';
                                }
                            }
                            
                            // Build img attributes string
                            $img_attr_string = '';
                            foreach ($img_attrs as $key => $value) {
                                $img_attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
                            }
                            
                            // Prevent lazy loading plugins from affecting LCP image
                            $img_class = 'banner-slide-image';
                            if ($is_lcp) {
                                // Add classes/data attributes to prevent lazy loading plugins
                                $img_class .= ' skip-lazy no-lazy';
                                $img_attr_string .= ' data-no-lazy="1" data-skip-lazy="1"';
                            }
                            ?>
                            <div class="swiper-slide banner-slide" 
                                 data-desktop-ratio="<?php echo esc_attr($banner_desktop_ratio); ?>"
                                 data-mobile-ratio="<?php echo esc_attr($banner_mobile_ratio); ?>">
                                <a href="<?php echo $banner_data['link_url']; ?>" class="banner-slide-link">
                                    <picture>
                                        <?php if ($mobile_image_id > 0): 
                                            $mobile_attrs = khane_irani_get_banner_image_attributes($mobile_image_id, false);
                                            if (!empty($mobile_attrs['srcset'])): ?>
                                                <source media="(max-width: 768px)" srcset="<?php echo esc_attr($mobile_attrs['srcset']); ?>" sizes="525px">
                                            <?php elseif (!empty($mobile_attrs['src'])): ?>
                                                <source media="(max-width: 768px)" srcset="<?php echo esc_attr($mobile_attrs['src']); ?>">
                                            <?php endif;
                                        endif; ?>
                                        <img <?php echo $img_attr_string; ?>
                                             alt="<?php echo $banner_data['title'] ? esc_attr($banner_data['title']) : esc_attr__('بنر تبلیغاتی', 'khane-irani'); ?>" 
                                             class="<?php echo esc_attr($img_class); ?>">
                                    </picture>
                                    <?php if ($banner_data['title']): ?>
                                        <div class="banner-slide-overlay">
                                            <h3 class="banner-slide-title"><?php echo $banner_data['title']; ?></h3>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endforeach;
                    }
                }

                // Show default banners if no valid banners were found
                if (empty($banners) || (isset($valid_banners) && empty($valid_banners))) {
                    // Debug: Show why default banners are shown
                    $banners_count = is_array($banners) ? count($banners) : 0;
                    $valid_banners_count = is_array($valid_banners) ? count($valid_banners) : 0;
                    echo '<!-- No valid banners found. Banners count: ' . $banners_count . ', Valid banners count: ' . $valid_banners_count . ' -->';
                    // Default banners - fallback
                    $default_banners = array(
                        array('url' => get_template_directory_uri() . '/images/banner-tarahi-site-.png', 'link' => home_url('/shop')),
                        array('url' => get_template_directory_uri() . '/images/banner3-.png', 'link' => home_url('/shop')),
                        array('url' => get_template_directory_uri() . '/images/banner-2-.png', 'link' => home_url('/shop')),
                    );

                    foreach ($default_banners as $index => $banner) {
                        $is_lcp = ($index === 0);
                        ?>
                        <div class="swiper-slide banner-slide">
                            <a href="<?php echo esc_url($banner['link']); ?>" class="banner-slide-link">
                                <img src="<?php echo esc_url($banner['url']); ?>"
                                     alt="بنر تبلیغاتی"
                                     class="banner-slide-image"
                                     loading="<?php echo $is_lcp ? 'eager' : 'lazy'; ?>"
                                     <?php if ($is_lcp): ?>fetchpriority="high"<?php endif; ?>
                                     decoding="async">
                            </a>
                        </div>
                        <?php
                    }
                }
                ?>
                </div>
            </div>
            
            <!-- Slider Navigation -->
            <button class="banner-slider-prev" id="bannerSliderPrev" aria-label="بنر قبلی">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="banner-slider-next" id="bannerSliderNext" aria-label="بنر بعدی">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <!-- Slider Dots -->
            <div class="banner-slider-dots" id="bannerSliderDots"></div>
        </div>
    </div>
</section>

<script>
// Prevent lazy loading plugins from affecting the first banner (LCP element)
// Note: Height is now set via CSS aspect-ratio to prevent CLS
(function() {
    'use strict';
    
    // Function to ensure first banner image loads immediately
    function ensureLCPImageLoads() {
        const firstBanner = document.querySelector('.banner-slide-image');
        if (!firstBanner) return;
        
        // Remove lazy loading attributes
        firstBanner.removeAttribute('data-lazy-src');
        firstBanner.removeAttribute('data-lazy-srcset');
        firstBanner.removeAttribute('data-lazy-sizes');
        firstBanner.removeAttribute('data-ll-status');
        firstBanner.removeAttribute('data-src');
        firstBanner.removeAttribute('data-srcset');
        
        // Ensure src and srcset are set correctly
        if (firstBanner.hasAttribute('data-lazy-src')) {
            const lazySrc = firstBanner.getAttribute('data-lazy-src');
            if (lazySrc && !firstBanner.src) {
                firstBanner.src = lazySrc;
            }
        }
        
        if (firstBanner.hasAttribute('data-lazy-srcset')) {
            const lazySrcset = firstBanner.getAttribute('data-lazy-srcset');
            if (lazySrcset && !firstBanner.srcset) {
                firstBanner.srcset = lazySrcset;
            }
        }
        
        // Force eager loading
        firstBanner.loading = 'eager';
        firstBanner.setAttribute('fetchpriority', 'high');
        firstBanner.classList.add('skip-lazy', 'no-lazy');
        firstBanner.setAttribute('data-no-lazy', '1');
        firstBanner.setAttribute('data-skip-lazy', '1');
    }
    
    // Run immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureLCPImageLoads);
    } else {
        ensureLCPImageLoads();
    }
    
    // Also run after a short delay to catch lazy loading plugins
    setTimeout(ensureLCPImageLoads, 100);
})();
</script>

