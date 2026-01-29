    <?php
    // دریافت تصویر بکگراند فوتر
    $footer_bg_image_id = khane_irani_get_footer_setting('footer_bg_image', '');
    $footer_bg_image_url = '';
    
    // تبدیل به عدد صحیح
    $footer_bg_image_id = absint($footer_bg_image_id);
    
    if ($footer_bg_image_id > 0) {
        // دریافت URL تصویر از attachment ID
        $footer_bg_image_url = wp_get_attachment_image_url($footer_bg_image_id, 'full');
        
        // اگر URL دریافت نشد، سعی کن از wp_get_attachment_url استفاده کنی
        if (!$footer_bg_image_url) {
            $footer_bg_image_url = wp_get_attachment_url($footer_bg_image_id);
        }
        
        // اگر هنوز URL نداریم، بررسی کن که attachment وجود دارد
        if (!$footer_bg_image_url) {
            $attachment = get_post($footer_bg_image_id);
            if ($attachment && $attachment->post_type === 'attachment') {
                $footer_bg_image_url = wp_get_attachment_url($footer_bg_image_id);
            }
        }
    }
    ?>
    <?php
    // SEO Content Section - نمایش در همه صفحات قبل از فوتر
    if (khane_irani_get_setting('seo_content_enabled', '1') === '1') {
        get_template_part('template-parts/front-page/seo-content-section');
    }
    ?>

    <footer id="colophon" class="site-footer">
        <div class="footer-background">
            <div class="container">
                <!-- باکس آبی با چهار ستون -->
                <div class="footer-blue-box" 
                     <?php if ($footer_bg_image_url): ?>data-bg-image="<?php echo esc_url($footer_bg_image_url); ?>"<?php endif; ?>
                     <?php if ($footer_bg_image_url): ?>
                     style="background-image: url('<?php echo esc_url($footer_bg_image_url); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"
                     <?php endif; ?>>
                    <div class="footer-content">
                        <!-- ستون اول: اطلاعات شرکت -->
                        <div class="footer-column company-info">
                            <div class="footer-logo">
                                <div class="logo-symbol"></div>
                                <h3><?php echo esc_html__('خانه ایرانی', 'khane-irani'); ?></h3>
                                <p class="website-url"><?php echo esc_url(str_replace(array('http://', 'https://'), '', home_url())); ?></p>
                            </div>
                            <div class="company-description-wrapper">
                                <?php 
                                $footer_description = khane_irani_get_footer_setting('footer_description', 'خانه ایرانی یک فروشگاه آنلاین حرفه‌ای است که محصولات متنوعی با کیفیت بالا و قیمت مناسب به مشتریان ارائه می‌دهد.');
                                // Remove extra line breaks and normalize whitespace
                                $footer_description = preg_replace('/\s+/', ' ', trim($footer_description));
                                ?>
                                <p class="company-description" id="footer-description">
                                    <?php echo wp_kses_post($footer_description); ?>
                                </p>
                                <button class="footer-show-more-btn" id="footer-show-more-btn" style="display: none;">
                                    <span class="show-more-text">نمایش بیشتر</span>
                                    <span class="show-less-text" style="display: none;">نمایش کمتر</span>
                                </button>
                            </div>
                            <div class="social-icons">
                                <a href="#" class="social-icon">📱</a>
                                <a href="#" class="social-icon">💬</a>
                                <a href="#" class="social-icon">📧</a>
                                <a href="#" class="social-icon">🌐</a>
                            </div>
                        </div>

                        <!-- ستون دوم: صفحات -->
                        <div class="footer-column">
                            <h3><?php echo esc_html__('صفحات', 'khane-irani'); ?></h3>
                            <ul class="footer-links">
                                <li><a href="#"><span class="link-icon">💼</span><?php echo esc_html__('نمونه کارها', 'khane-irani'); ?></a></li>
                                <li><a href="#"><span class="link-icon">📋</span><?php echo esc_html__('راهبردها', 'khane-irani'); ?></a></li>
                                <li><a href="#"><span class="link-icon">🎨</span><?php echo esc_html__('محصولات', 'khane-irani'); ?></a></li>
                            </ul>
                        </div>

                        <!-- ستون سوم: ارتباط با ما -->
                        <div class="footer-column">
                            <h3><?php echo esc_html__('ارتباط با ما', 'khane-irani'); ?></h3>
                            <div class="contact-item">
                                <span class="contact-icon">📧</span>
                                <span><?php echo esc_html(khane_irani_get_contact_setting('contact_email', 'text@gmail.com')); ?></span>
                            </div>
                            <div class="contact-item">
                                <span class="contact-icon">📍</span>
                                <span><?php echo esc_html(khane_irani_get_contact_setting('contact_address', __('تهران. خانه ایرانی...', 'khane-irani'))); ?></span>
                            </div>
                        </div>

                        <!-- ستون چهارم: پیگیری سفارش -->
                        <div class="footer-column">
                            <h3><?php echo esc_html__('پیگیری سفارش', 'khane-irani'); ?></h3>
                            <form class="tracking-form">
                                <input type="text" class="tracking-input" placeholder="<?php echo esc_attr__('کد پیگیری را وارد کنید', 'khane-irani'); ?>">
                                <button type="submit" class="tracking-button"><?php echo esc_html__('پیگیری', 'khane-irani'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بخش کپی‌رایت -->
        <div class="footer-copyright">
            <div class="container">
                <p><?php echo esc_html__('کلیه حقوق مادی و معنوی خانه ایرانی متعلق به خانه ایرانی بوده و هرگونه کپی برداری پیگرد قانونی دارد.', 'khane-irani'); ?></p>
            </div>
        </div>
    </footer>

<?php wp_footer(); ?>

<!-- WooCommerce Phone Auth Modal Handler -->
<script>
jQuery(document).ready(function($) {
    'use strict';
    
    function initHeaderLoginButton() {
        var $btn = $('#header-login-btn');
        if ($btn.length === 0) return;
        
        // Remove any existing handlers
        $btn.off('click.header-login');
        
        // Add click handler
        $btn.on('click.header-login', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Always try to open modal first (never redirect)
            if (typeof window.openLogin === 'function') {
                window.openLogin();
            } else {
                // If modal function doesn't exist, wait a bit and try again
                setTimeout(function() {
                    if (typeof window.openLogin === 'function') {
                        window.openLogin();
                    } else {
                        console.warn('WCPA: openLogin function not found. Modal may not work.');
                    }
                }, 500);
            }
        });
    }
    
    // Try to initialize immediately
    initHeaderLoginButton();
    
    // Also try after delays to ensure WCPA script is loaded
    setTimeout(initHeaderLoginButton, 500);
    setTimeout(initHeaderLoginButton, 1000);
    setTimeout(initHeaderLoginButton, 2000);
    
    // Try again after window load
    $(window).on('load', function() {
        setTimeout(initHeaderLoginButton, 100);
    });
});

// Footer Show More/Less Functionality
document.addEventListener('DOMContentLoaded', function() {
    const descriptionEl = document.getElementById('footer-description');
    const showMoreBtn = document.getElementById('footer-show-more-btn');
    const showMoreText = showMoreBtn?.querySelector('.show-more-text');
    const showLessText = showMoreBtn?.querySelector('.show-less-text');
    
    if (!descriptionEl || !showMoreBtn) return;
    
    // Check if text is longer than 3 lines (mobile only)
    function checkIfNeedsButton() {
        if (window.innerWidth > 768) {
            showMoreBtn.style.display = 'none';
            descriptionEl.classList.remove('expanded');
            return;
        }
        
        // Temporarily remove expanded class to measure original height
        const wasExpanded = descriptionEl.classList.contains('expanded');
        descriptionEl.classList.remove('expanded');
        
        // Force reflow to get accurate measurements
        void descriptionEl.offsetHeight;
        
        // Get computed styles
        const computedStyle = window.getComputedStyle(descriptionEl);
        const fontSize = parseFloat(computedStyle.fontSize);
        const lineHeightValue = computedStyle.lineHeight;
        let lineHeight;
        
        if (lineHeightValue === 'normal') {
            lineHeight = fontSize * 1.6;
        } else if (lineHeightValue.includes('px')) {
            lineHeight = parseFloat(lineHeightValue);
        } else {
            lineHeight = parseFloat(lineHeightValue) * fontSize;
        }
        
        const maxHeight = lineHeight * 3;
        const actualHeight = descriptionEl.scrollHeight;
        
        // Restore expanded state if it was expanded
        if (wasExpanded) {
            descriptionEl.classList.add('expanded');
        }
        
        // Show button if content exceeds 3 lines
        if (actualHeight > maxHeight + 5) {
            showMoreBtn.style.display = 'block';
        } else {
            showMoreBtn.style.display = 'none';
        }
    }
    
    // Toggle expand/collapse
    showMoreBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const isExpanded = descriptionEl.classList.contains('expanded');
        
        if (isExpanded) {
            descriptionEl.classList.remove('expanded');
            if (showMoreText) showMoreText.style.display = 'inline';
            if (showLessText) showLessText.style.display = 'none';
        } else {
            descriptionEl.classList.add('expanded');
            if (showMoreText) showMoreText.style.display = 'none';
            if (showLessText) showLessText.style.display = 'inline';
        }
    });
    
    // Check on load and resize
    setTimeout(checkIfNeedsButton, 100);
    window.addEventListener('resize', function() {
        setTimeout(checkIfNeedsButton, 100);
    });
});
</script>

</div><!-- #page -->

</body>
</html>
