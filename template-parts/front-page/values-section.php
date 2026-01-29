<?php
/**
 * Values Section - بخش ارزش‌های خرید از خانه ایرانی
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

// Get values from theme settings
$values_items_raw = khane_irani_get_setting('values_items', '');

// Process values_items - convert string to array if needed
$values_items = array();
if (!empty($values_items_raw)) {
    if (is_array($values_items_raw)) {
        // Already an array
        $values_items = $values_items_raw;
    } else {
        // Convert pipe-separated string to array
        $raw_items = explode('|', $values_items_raw);
        $items_count = count($raw_items);

        // Each value should have 3 parts: emoji|title|description
        for ($i = 0; $i < $items_count; $i += 3) {
            if (isset($raw_items[$i + 2])) {
                $values_items[] = array(
                    'icon' => 'fas fa-star', // Default icon since emoji isn't supported in FontAwesome
                    'title' => trim($raw_items[$i + 1]),
                    'description' => trim($raw_items[$i + 2]),
                    'is_active' => '1'
                );
            }
        }
    }
}

// Default values if not set
if (empty($values_items)) {
    $values_items = array(
        array(
            'icon' => 'fas fa-award',
            'title' => 'کیفیت برتر',
            'description' => 'انتخاب بهترین محصولات با کیفیت بالا و استانداردهای بین‌المللی',
            'is_active' => '1'
        ),
        array(
            'icon' => 'fas fa-shipping-fast',
            'title' => 'ارسال سریع',
            'description' => 'ارسال فوری سفارش‌ها به سراسر کشور با بسته‌بندی مناسب',
            'is_active' => '1'
        ),
        array(
            'icon' => 'fas fa-shield-alt',
            'title' => 'ضمانت اصالت',
            'description' => 'ضمانت اصالت کالا و کیفیت با امکان بازگشت محصول',
            'is_active' => '1'
        ),
        array(
            'icon' => 'fas fa-dollar-sign',
            'title' => 'قیمت مناسب',
            'description' => 'بهترین قیمت‌ها با تخفیف‌های ویژه و برنامه‌های وفاداری',
            'is_active' => '1'
        ),
        array(
            'icon' => 'fas fa-headset',
            'title' => 'پشتیبانی ۲۴/۷',
            'description' => 'پشتیبانی و مشاوره رایگان در تمام ساعات شبانه‌روز',
            'is_active' => '1'
        ),
        array(
            'icon' => 'fas fa-heart',
            'title' => 'رضایت مشتری',
            'description' => 'رضایت شما اولویت اول ماست. بیش از ۱۰۰ هزار مشتری راضی',
            'is_active' => '1'
        )
    );
}

// Filter active items
$values_items = array_filter($values_items, function($item) {
    return !isset($item['is_active']) || $item['is_active'] === '1';
});

// Limit to 4 items
$values_items = array_slice($values_items, 0, 4);
?>

<!-- Values Section -->
<section class="ki-values-section">
    <div class="container">
        <div class="ki-values-header">
            <h2 class="ki-values-title">ارزش‌های خرید از خانه ایرانی</h2>
        </div>

        <div class="ki-values-grid">
            <?php foreach ($values_items as $index => $value): 
                $icon = isset($value['icon']) ? esc_attr($value['icon']) : 'fas fa-star';
                $title = isset($value['title']) ? esc_html($value['title']) : '';
                $description = isset($value['description']) ? esc_html($value['description']) : '';
            ?>
                <div class="ki-value-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="ki-value-icon-wrapper">
                        <i class="<?php echo $icon; ?>"></i>
                    </div>
                    <div class="ki-value-content">
                        <h3 class="ki-value-title"><?php echo $title; ?></h3>
                        <p class="ki-value-description"><?php echo $description; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$template_uri = get_template_directory_uri();
$eslimi_house = esc_url($template_uri . '/images/image-home/' . rawurlencode('خانه ایرانی (6).png'));
$eslimi_teen = esc_url($template_uri . '/images/image-home/' . rawurlencode('نوجوان.png'));
?>
<style>
.ki-values-section {
    padding: 60px 0;
    background: #f8f9fa;
    position: relative;
    overflow: hidden;
}

/* Eslimi Pattern Background */
.ki-values-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 200px;
    height: 200px;
    background-image: url('<?php echo $eslimi_house; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: top left;
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
}

.ki-values-section::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 180px;
    height: 180px;
    background-image: url('<?php echo $eslimi_teen; ?>');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom right;
    opacity: 0.07;
    pointer-events: none;
    z-index: 0;
}

.ki-values-section .container {
    position: relative;
    z-index: 1;
}

.ki-values-header {
    text-align: center;
    margin-bottom: 35px;
}

.ki-values-subtitle {
    display: inline-block;
    color: var(--zs-teal-medium, #37C3B3);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

.ki-values-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--zs-teal-dark, #00A796);
    margin: 0 0 10px 0;
    line-height: 1.3;
}

.ki-values-description {
    font-size: 14px;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.ki-values-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.ki-value-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s ease;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.ki-value-card:hover {
    border-color: #dee2e6;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.ki-value-icon-wrapper {
    width: 56px;
    height: 56px;
    min-width: 56px;
    background: var(--zs-teal-medium, #37C3B3);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ki-value-icon-wrapper i {
    font-size: 24px;
    color: #ffffff;
}

.ki-value-content {
    flex: 1;
    min-width: 0;
}

.ki-value-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 4px 0;
    line-height: 1.4;
}

.ki-value-description {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

@media (max-width: 1024px) {
    .ki-values-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
}

@media (max-width: 768px) {
    .ki-values-section {
        padding: 30px 0;
        background: #ffffff;
    }

    .ki-values-header {
        margin-bottom: 18px;
    }

    .ki-values-title {
        font-size: 18px;
        margin-bottom: 0;
        font-weight: 600;
    }

    /* Hide subtitle and description on mobile */
    .ki-values-subtitle,
    .ki-values-description {
        display: none;
    }

    /* 2 columns on mobile */
    .ki-values-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        max-width: 100%;
    }

    .ki-value-card {
        padding: 12px;
        gap: 8px;
        flex-direction: column;
        text-align: center;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
    }

    .ki-value-icon-wrapper {
        width: 40px;
        height: 40px;
        min-width: 40px;
        margin: 0 auto;
        border-radius: 8px;
    }

    .ki-value-icon-wrapper i {
        font-size: 16px;
    }

    .ki-value-title {
        font-size: 12px;
        margin-bottom: 0;
        font-weight: 600;
        line-height: 1.4;
    }

    .ki-value-description {
        display: none;
    }
}
</style>

