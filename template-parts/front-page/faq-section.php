<?php
/**
 * FAQ Section Template (dynamic from theme settings)
 */

$options = get_option('khane-irani-settings', array());
$faq_items = isset($options['faq_items']) && is_array($options['faq_items']) ? $options['faq_items'] : array();

// دریافت تصویر ستون اول
$faq_section_image_id = isset($options['faq_section_image']) ? absint($options['faq_section_image']) : 0;
$faq_section_image_url = '';
if ($faq_section_image_id > 0) {
    $faq_section_image_url = wp_get_attachment_image_url($faq_section_image_id, 'full');
    if (!$faq_section_image_url) {
        $faq_section_image_url = wp_get_attachment_url($faq_section_image_id);
    }
}

// Normalize and filter active
$normalized = array();
foreach ($faq_items as $item) {
    $question = isset($item['question']) ? wp_kses_post($item['question']) : '';
    $answer   = isset($item['answer']) ? wp_kses_post($item['answer']) : '';
    if ($question === '' || $answer === '') continue;
    $normalized[] = array(
        'question'   => $question,
        'answer'     => $answer,
        'author'     => isset($item['author']) ? sanitize_text_field($item['author']) : '',
        'image'      => isset($item['image']) ? absint($item['image']) : 0,
        'is_featured'=> isset($item['is_featured']) && $item['is_featured'] === '1',
        'is_active'  => !isset($item['is_active']) || $item['is_active'] === '1',
        'sort_order' => isset($item['sort_order']) ? intval($item['sort_order']) : 0,
    );
}

// Only active
$normalized = array_values(array_filter($normalized, function($i){ return $i['is_active']; }));

// Sort by sort_order
usort($normalized, function($a,$b){ return $a['sort_order'] <=> $b['sort_order']; });

// Pick featured for large card; fallback to first item
$featured = null;
foreach ($normalized as $i) { if ($i['is_featured']) { $featured = $i; break; } }
if (!$featured && !empty($normalized)) { $featured = $normalized[0]; }

// Small cards: take first 3 items excluding featured if present twice
$smalls = array();
foreach ($normalized as $i) {
    if ($featured && $i['question'] === $featured['question'] && $i['answer'] === $featured['answer']) { continue; }
    $smalls[] = $i;
}
$smalls = array_slice($smalls, 0, 3);
?>

<section class="faq-section">
    <div class="container">
        <h2 class="faq-section-title">سوالات متداول</h2>
        <div class="faq-two-columns">
            <!-- ستون اول: عکس -->
            <div class="faq-column faq-image-column">
                <?php if ($faq_section_image_url): ?>
                    <div class="faq-image-wrapper">
                        <img src="<?php echo esc_url($faq_section_image_url); ?>" alt="سوالات متداول" class="faq-section-img" />
                    </div>
                <?php else: ?>
                    <div class="faq-image-placeholder">
                        <i class="fas fa-image"></i>
                        <p>تصویر ستون اول را در تنظیمات قالب آپلود کنید</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- ستون دوم: سوالات -->
            <div class="faq-column faq-questions-column">
                <div class="faq-accordion">
                    <?php foreach ($normalized as $index => $item): ?>
                        <div class="faq-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="faq-question-header">
                                <h3 class="faq-question-title"><?php echo $item['question']; ?></h3>
                                <span class="faq-toggle-icon">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </div>
                            <div class="faq-answer-content">
                                <div class="faq-answer-text">
                                    <?php echo $item['answer']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$template_uri = get_template_directory_uri();
$eslimi_lady = esc_url($template_uri . '/images/image-home/' . rawurlencode('بانو.png'));
$eslimi_teen = esc_url($template_uri . '/images/image-home/' . rawurlencode('نوجوان.png'));
?>
<style>
.faq-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 50%, #ffffff 100%);
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.faq-section::before {
    content: '';
    position: absolute;
    top: 30px;
    left: 30px;
    width: 200px;
    height: 200px;
    background-image: url('<?php echo $eslimi_lady; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top left;
    opacity: 0.07;
    pointer-events: none;
    z-index: 0;
}

.faq-section::after {
    content: '';
    position: absolute;
    bottom: 30px;
    right: 30px;
    width: 220px;
    height: 220px;
    background-image: url('<?php echo $eslimi_teen; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom right;
    opacity: 0.06;
    pointer-events: none;
    z-index: 0;
}

.faq-section .container {
    position: relative;
    z-index: 1;
}

/* Hide decorative images on mobile */
@media (max-width: 768px) {
    .faq-section::before,
    .faq-section::after {
        display: none !important;
        background-image: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
    }
}

.faq-section-title {
    text-align: center;
    font-size: 32px;
    font-weight: 700;
    color: var(--zs-teal-dark, #00A796);
    margin-bottom: 50px;
    position: relative;
    padding-bottom: 20px;
}

.faq-section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, var(--zs-teal-medium, #37C3B3), var(--zs-teal-dark, #00A796));
    border-radius: 2px;
}

/* دو ستونه */
.faq-two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: start;
}

.faq-column {
    position: relative;
}

/* ستون عکس */
.faq-image-column {
    position: sticky;
    top: 100px;
}

.faq-image-wrapper {
    width: 100%;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    background: #ffffff;
    padding: 20px;
}

.faq-section-img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px;
    object-fit: cover;
}

.faq-image-placeholder {
    width: 100%;
    min-height: 400px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px dashed rgba(55, 195, 179, 0.3);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--zs-teal-medium, #37C3B3);
    text-align: center;
    padding: 40px;
}

.faq-image-placeholder i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.faq-image-placeholder p {
    font-size: 16px;
    color: #6b7280;
    margin: 0;
}

/* ستون سوالات */
.faq-questions-column {
    flex: 1;
}

.faq-accordion {
    width: 100%;
}

.faq-item {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item:last-child {
    margin-bottom: 0;
}

.faq-item.active {
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.faq-question-header {
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    user-select: none;
    background: #ffffff;
    transition: background 0.2s ease;
}

.faq-item:hover .faq-question-header {
    background: #f9fafb;
}

.faq-item.active .faq-question-header {
    background: #f9fafb;
}

.faq-question-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
    padding-left: 12px;
    line-height: 1.5;
}

.faq-toggle-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    background: #f3f4f6;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.faq-toggle-icon i {
    font-size: 14px;
    color: #6b7280;
    transition: transform 0.3s ease;
}

.faq-item.active .faq-toggle-icon {
    background: #e5e7eb;
}

.faq-item.active .faq-toggle-icon i {
    transform: rotate(180deg);
    color: #374151;
}

.faq-answer-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-item.active .faq-answer-content {
    max-height: 1000px;
}

.faq-answer-text {
    padding: 0 20px 20px 20px;
    color: #4b5563;
    font-size: 14px;
    line-height: 1.7;
}

.faq-answer-text p {
    margin: 0 0 12px 0;
}

.faq-answer-text p:last-child {
    margin-bottom: 0;
}

@media (max-width: 992px) {
    .faq-two-columns {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .faq-image-column {
        position: relative;
        top: 0;
    }
    
    .faq-image-wrapper {
        max-width: 500px;
        margin: 0 auto;
    }
}

@media (max-width: 768px) {
    .faq-section {
        padding: 50px 0;
    }

    .faq-section-title {
        font-size: 26px;
        margin-bottom: 35px;
    }
    
    .faq-two-columns {
        gap: 30px;
    }

    .faq-question-header {
        padding: 16px;
    }

    .faq-question-title {
        font-size: 15px;
        padding-left: 8px;
    }

    .faq-answer-text {
        padding: 0 16px 16px 16px;
        font-size: 13px;
    }
    
    .faq-image-placeholder {
        min-height: 300px;
        padding: 30px 20px;
    }
    
    .faq-image-placeholder i {
        font-size: 48px;
        margin-bottom: 15px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.faq-question-header').on('click', function() {
        const $item = $(this).closest('.faq-item');
        const $accordion = $item.closest('.faq-accordion');
        
        // Close other items if clicking a different one
        if (!$item.hasClass('active')) {
            $accordion.find('.faq-item').removeClass('active');
        }
        
        // Toggle current item
        $item.toggleClass('active');
    });
});
</script>
