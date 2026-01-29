<?php
/**
 * SEO Content Section - بخش محتوای سئو (قبل از فوتر)
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

// Get SEO content from theme settings
$seo_content = khane_irani_get_setting('seo_content', '');
$seo_content_title = khane_irani_get_setting('seo_content_title', 'درباره خانه ایرانی');

if (empty($seo_content)) {
    return;
}

// Preserve HTML formatting - use wp_kses_post to allow safe HTML
$full_content = wp_kses_post($seo_content);

// Get excerpt (first 3 lines) - Preserve HTML but limit to 3 lines
// First, get plain text version for line counting
$content_text = wp_strip_all_tags($seo_content);
$content_array = explode("\n", $content_text);
$excerpt_lines = array_slice($content_array, 0, 3);
$excerpt_plain = implode("\n", $excerpt_lines);

// For excerpt display, we'll use CSS to limit to 3 lines while preserving HTML
// Check if content has more than 3 lines or is longer
$has_more = count($content_array) > 3 || strlen($content_text) > strlen($excerpt_plain) + 50;
?>

<!-- SEO Content Section -->
<section class="ki-seo-content-section">
    <div class="container">
        <div class="ki-seo-content-wrapper">
            <?php if (!empty($seo_content_title)): ?>
                <h2 class="ki-seo-content-title"><?php echo esc_html($seo_content_title); ?></h2>
            <?php endif; ?>
            
            <div class="ki-seo-content-text">
                <div class="ki-seo-excerpt" id="seoExcerpt">
                    <?php echo wpautop($full_content); ?>
                </div>
                
                <?php if ($has_more): ?>
                    <div class="ki-seo-full-content" id="seoFullContent" style="display: none;">
                        <?php echo wpautop($full_content); ?>
                    </div>
                    
                    <button class="ki-seo-toggle-btn" id="seoToggleBtn">
                        <span class="ki-seo-show-more">نمایش بیشتر</span>
                        <span class="ki-seo-show-less" style="display: none;">نمایش کمتر</span>
                        <i class="fas fa-chevron-down ki-seo-chevron"></i>
                    </button>
                <?php else: ?>
                    <div class="ki-seo-full-content" id="seoFullContent" style="display: none;">
                        <?php echo wpautop($full_content); ?>
                    </div>
                    <button class="ki-seo-toggle-btn" id="seoToggleBtn" style="display: none;">
                        <span class="ki-seo-show-more">نمایش بیشتر</span>
                        <span class="ki-seo-show-less" style="display: none;">نمایش کمتر</span>
                        <i class="fas fa-chevron-down ki-seo-chevron"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$template_uri = get_template_directory_uri();
$eslimi_lady = esc_url($template_uri . '/images/image-home/' . rawurlencode('بانو.png'));
$eslimi_child = esc_url($template_uri . '/images/image-home/' . rawurlencode('کودک.png'));
?>
<style>
.ki-seo-content-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 30%, #ffffff 70%, #f8fafc 100%);
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-seo-content-section::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 20px;
    width: 250px;
    height: 250px;
    background-image: url('<?php echo $eslimi_lady; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top left;
    opacity: 0.1;
    pointer-events: none;
    z-index: 0;
}

.ki-seo-content-section::after {
    content: '';
    position: absolute;
    bottom: 20px;
    right: 20px;
    width: 220px;
    height: 220px;
    background-image: url('<?php echo $eslimi_child; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom right;
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
}

.ki-seo-content-section .container {
    position: relative;
    z-index: 1;
}

.ki-seo-content-wrapper {
    max-width: 1000px;
    margin: 0 auto;
}

.ki-seo-content-title {
    font-size: 32px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 40px 0;
    text-align: center;
    position: relative;
    padding-bottom: 20px;
}

.ki-seo-content-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796));
    border-radius: 2px;
}

.ki-seo-content-text {
    background: #ffffff;
    border-radius: 20px;
    padding: 45px 50px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(55, 195, 179, 0.1);
    line-height: 2;
    color: #475569;
    font-size: 16px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.ki-seo-content-text.expanded {
    overflow: visible;
}

/* Show only 3 lines by default - Desktop and Mobile */
.ki-seo-excerpt {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 2;
    max-height: calc(2em * 3);
    word-break: break-word;
}

.ki-seo-content-text.expanded .ki-seo-excerpt {
    display: none !important;
    max-height: 0;
    overflow: hidden;
}

.ki-seo-full-content {
    display: none;
    width: 100%;
}

.ki-seo-full-content.is-open {
    display: block !important;
    width: 100%;
}

.ki-seo-content-text::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--zs-teal-dark, #00A796), var(--zs-teal-medium, #37C3B3));
    border-radius: 20px 20px 0 0;
}

.ki-seo-content-text p {
    margin: 0 0 20px 0;
    font-size: 16px;
    line-height: 2;
    color: #475569;
}

.ki-seo-content-text p:last-child {
    margin-bottom: 0;
}

.ki-seo-content-text a {
    color: var(--zs-teal-medium, #37C3B3);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border-bottom: 2px solid transparent;
    padding-bottom: 2px;
}

.ki-seo-content-text a:hover {
    color: var(--zs-teal-dark, #00A796);
    border-bottom-color: var(--zs-teal-dark, #00A796);
}

.ki-seo-content-text h1,
.ki-seo-content-text h2,
.ki-seo-content-text h3,
.ki-seo-content-text h4 {
    color: var(--zs-teal-dark, #00A796);
    margin-top: 32px;
    margin-bottom: 20px;
    font-weight: 700;
    line-height: 1.4;
}

.ki-seo-content-text h2 {
    font-size: 24px;
    position: relative;
    padding-right: 20px;
}

.ki-seo-content-text h2::before {
    content: '▸';
    position: absolute;
    right: 0;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 20px;
}

.ki-seo-content-text ul,
.ki-seo-content-text ol {
    margin: 20px 0;
    padding-right: 30px;
}

.ki-seo-content-text li {
    margin-bottom: 12px;
    position: relative;
    padding-right: 10px;
    line-height: 1.8;
}

.ki-seo-content-text ul li::marker {
    color: var(--zs-teal-medium, #37C3B3);
    font-weight: 700;
}

.ki-seo-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796));
    color: #ffffff;
    border: none;
    padding: 14px 32px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    margin: 30px auto 0;
    transition: none;
    box-shadow: 0 4px 15px rgba(55, 195, 179, 0.3);
    position: relative;
    overflow: hidden;
    width: 100%;
    max-width: 300px;
    text-align: center;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 15px rgba(55, 195, 179, 0.3);
    }
    50% {
        box-shadow: 0 6px 25px rgba(55, 195, 179, 0.5);
    }
}

.ki-seo-toggle-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.ki-seo-toggle-btn:hover::before {
    width: 300px;
    height: 300px;
}

.ki-seo-toggle-btn:hover {
    transform: none;
    box-shadow: 0 4px 15px rgba(55, 195, 179, 0.3);
}

.ki-seo-toggle-btn:active {
    transform: none;
}

.ki-seo-toggle-btn.active .ki-seo-chevron {
    transform: rotate(180deg);
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.ki-seo-toggle-btn .ki-seo-show-more,
.ki-seo-toggle-btn .ki-seo-show-less {
    transition: none;
}

.ki-seo-toggle-btn:hover .ki-seo-chevron {
    transform: none;
}

.ki-seo-toggle-btn.active:hover .ki-seo-chevron {
    transform: rotate(180deg);
}

.ki-seo-chevron {
    transition: transform 0.3s ease;
    font-size: 14px;
}

.ki-seo-content-text:hover {
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .ki-seo-content-section {
        padding: 60px 0;
    }
    
    .ki-seo-content-section::before,
    .ki-seo-content-section::after {
        width: 150px;
        height: 150px;
        opacity: 0.06;
    }

    .ki-seo-content-title {
        font-size: 26px;
        margin-bottom: 30px;
        padding-bottom: 15px;
    }
    
    .ki-seo-content-title::after {
        width: 80px;
        height: 3px;
    }

    .ki-seo-content-text {
        padding: 30px 24px;
        font-size: 15px;
        border-radius: 16px;
        line-height: 1.9;
    }
    
    .ki-seo-content-text p {
        font-size: 15px;
        margin-bottom: 18px;
    }
    
    .ki-seo-content-text h2 {
        font-size: 20px;
        margin-top: 28px;
        margin-bottom: 18px;
    }

    .ki-seo-toggle-btn {
        width: 100%;
        padding: 14px 24px;
        font-size: 14px;
        margin-top: 25px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('seoToggleBtn');
    const excerptEl = document.getElementById('seoExcerpt');
    const fullContentEl = document.getElementById('seoFullContent');
    const contentTextEl = excerptEl?.closest('.ki-seo-content-text');
    const showMoreEl = toggleBtn?.querySelector('.ki-seo-show-more');
    const showLessEl = toggleBtn?.querySelector('.ki-seo-show-less');
    
    if (!toggleBtn || !excerptEl || !fullContentEl || !contentTextEl) return;
    
    // Check if button should be shown (if content is longer than 3 lines)
    function checkIfNeedsButton() {
        // Temporarily remove expanded class to measure
        const wasExpanded = contentTextEl.classList.contains('expanded');
        contentTextEl.classList.remove('expanded');
        
        // Force reflow
        void excerptEl.offsetHeight;
        
        const computedStyle = window.getComputedStyle(excerptEl);
        const fontSize = parseFloat(computedStyle.fontSize);
        const lineHeightValue = computedStyle.lineHeight;
        let lineHeight;
        
        if (lineHeightValue === 'normal') {
            lineHeight = fontSize * 2; // line-height: 2
        } else if (lineHeightValue.includes('px')) {
            lineHeight = parseFloat(lineHeightValue);
        } else {
            lineHeight = parseFloat(lineHeightValue) * fontSize;
        }
        
        const maxHeight = lineHeight * 3;
        const actualHeight = excerptEl.scrollHeight;
        
        // Restore expanded state
        if (wasExpanded) {
            contentTextEl.classList.add('expanded');
        }
        
        // Show button if content exceeds 3 lines
        if (actualHeight > maxHeight + 5) {
            toggleBtn.style.display = 'inline-flex';
        } else {
            toggleBtn.style.display = 'none';
            // If hidden, ensure default state
            contentTextEl.classList.remove('expanded');
            fullContentEl.classList.remove('is-open');
            fullContentEl.style.display = 'none';
            if (showMoreEl) showMoreEl.style.display = 'inline';
            if (showLessEl) showLessEl.style.display = 'none';
            toggleBtn.classList.remove('active');
        }
    }
    
    // Toggle expand/collapse
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const isExpanded = contentTextEl.classList.contains('expanded');
        
    if (isExpanded) {
        // Collapse
        contentTextEl.classList.remove('expanded');
        fullContentEl.classList.remove('is-open');
        fullContentEl.style.display = 'none';
        
        if (showMoreEl) {
            showMoreEl.style.display = 'inline';
        }
        if (showLessEl) {
            showLessEl.style.display = 'none';
        }
        toggleBtn.classList.remove('active');
    } else {
        // Expand - Show full content
        fullContentEl.style.display = 'block';
        fullContentEl.classList.add('is-open');
        contentTextEl.classList.add('expanded');
        
        if (showMoreEl) {
            showMoreEl.style.display = 'none';
        }
        if (showLessEl) {
            showLessEl.style.display = 'inline';
        }
        toggleBtn.classList.add('active');
    }
    });
    
    // Check on load and resize
    setTimeout(checkIfNeedsButton, 100);
    window.addEventListener('resize', function() {
        setTimeout(checkIfNeedsButton, 100);
    });
});
</script>

