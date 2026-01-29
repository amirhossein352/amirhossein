<?php
/**
 * Front Page Settings Tab
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Front Page Settings Class
 */
class khane_irani_Front_Page_Settings extends khane_irani_Settings_Tab {
    
    public function __construct() {
        parent::__construct('front-page', 'صفحه اصلی');
    }
    
    protected function add_settings_fields() { }
    
    public function render_tab_content() {
        ?>
        <form method="post" action="options.php" id="zarin-settings-form">
            <?php settings_fields('khane-irani-settings'); ?>
            
            <div class="zarin-settings-accordion">
                
                <!-- Hero Slider Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">اسلایدر هیرو (Hero Slider)</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'hero_slider_enabled',
                                'label' => 'فعال‌سازی اسلایدر',
                                'default' => '1',
                                'description' => 'نمایش اسلایدر هیرو در بالای صفحه اصلی'
                            ));
                            ?>
                        </table>
                        <?php $this->render_banners_repeater(); ?>
                    </div>
                </div>
                
                <!-- Values Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">بخش ارزش‌های خرید از خانه ایرانی</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'values_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '1',
                                'description' => 'نمایش بخش ارزش‌های خرید'
                            ));
                            ?>
                        </table>
                        <?php $this->render_values_repeater(); ?>
                    </div>
                </div>
                
                <!-- Latest Posts Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">بخش آخرین مطالب بلاگ</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'latest_posts_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '1',
                                'description' => 'نمایش بخش آخرین مطالب بلاگ'
                            ));
                            ?>
                        </table>
                    </div>
                </div>
                
                <!-- On Sale Products Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">کاروسل محصولات تخفیف‌دار</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'on_sale_products_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '1',
                                'description' => 'نمایش کاروسل محصولات تخفیف‌دار'
                            ));
                            
                            $this->render_field(array(
                                'type' => 'text',
                                'id' => 'sale_end_date',
                                'label' => 'تاریخ پایان تخفیف',
                                'default' => date('Y-m-d H:i:s', strtotime('+7 days')),
                                'description' => 'تاریخ و زمان پایان تخفیف (فرمت: Y-m-d H:i:s)'
                            ));
                            
                            $this->render_field(array(
                                'type' => 'text',
                                'id' => 'on_sale_products_count',
                                'label' => 'تعداد محصولات',
                                'default' => '12',
                                'description' => 'تعداد محصولات تخفیف‌دار نمایش داده شده'
                            ));
                            ?>
                        </table>
                    </div>
                </div>
                
                <!-- Latest Products Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">کاروسل جدیدترین محصولات</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'latest_products_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '1',
                                'description' => 'نمایش کاروسل جدیدترین محصولات'
                            ));
                            
                            $this->render_field(array(
                                'type' => 'text',
                                'id' => 'latest_products_count',
                                'label' => 'تعداد محصولات',
                                'default' => '12',
                                'description' => 'تعداد جدیدترین محصولات نمایش داده شده'
                            ));
                            ?>
                        </table>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">بخش سوالات متداول (FAQ)</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'faq_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '1',
                                'description' => 'نمایش بخش سوالات متداول در صفحه اصلی'
                            ));
                            ?>
                        </table>
                        <p class="description">برای مدیریت سوالات و پاسخ‌ها به تب "سوالات پرتکرار" مراجعه کنید.</p>
                    </div>
                </div>
                
                <!-- Custom Product Carousels Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">کاروسل‌های اختیاری محصولات (تا ۴ کاروسل)</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'custom_carousels_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '0',
                                'description' => 'نمایش کاروسل‌های اختیاری محصولات'
                            ));
                            ?>
                        </table>
                        <?php $this->render_custom_carousels_repeater(); ?>
                    </div>
                </div>
                
                <!-- SEO Content Section -->
                <div class="zarin-section-accordion">
                    <h3 class="zarin-section-header">بخش محتوای سئو (قبل از فوتر)</h3>
                    <div class="zarin-section-content">
                        <table class="form-table">
                            <?php
                            $this->render_field(array(
                                'type' => 'checkbox',
                                'id' => 'seo_content_enabled',
                                'label' => 'فعال‌سازی بخش',
                                'default' => '1',
                                'description' => 'نمایش بخش محتوای سئو قبل از فوتر'
                            ));
                            
                            $this->render_field(array(
                                'type' => 'text',
                                'id' => 'seo_content_title',
                                'label' => 'عنوان بخش',
                                'default' => 'درباره خانه ایرانی',
                                'description' => 'عنوان بخش محتوای سئو'
                            ));
                            
                            // Render WordPress Classic Editor for SEO Content
                            $seo_content_value = $this->get_option('seo_content', 'خانه ایرانی، مرجع اصلی خرید آنلاین محصولات ایرانی با کیفیت و قیمت مناسب است. ما با ارائه بهترین محصولات و خدمات، تجربه خرید لذت‌بخش برای شما فراهم می‌کنیم.');
                            ?>
                            <tr>
                                <th scope="row">
                                    <label for="khane-irani-settings_seo_content">محتوای سئو</label>
                                </th>
                                <td>
                                    <?php
                                    wp_editor(
                                        wp_kses_post($seo_content_value),
                                        'khane-irani-settings_seo_content',
                                        array(
                                            'textarea_name' => 'khane-irani-settings[seo_content]',
                                            'textarea_rows' => 10,
                                            'media_buttons' => false,
                                            'teeny' => false,
                                            'tinymce' => array(
                                                'toolbar1' => 'bold,italic,underline,strikethrough,|,bullist,numlist,|,link,unlink,|,undo,redo',
                                                'toolbar2' => '',
                                                'toolbar3' => '',
                                                'toolbar4' => '',
                                            ),
                                            'quicktags' => true,
                                        )
                                    );
                                    ?>
                                    <p class="description">محتوای سئو (قابلیت فرمت‌بندی و لینک‌دهی با ویرایشگر کلاسیک)</p>
                                </td>
                            </tr>
                            <?php
                            ?>
                        </table>
                        <p class="description">شما می‌توانید از HTML برای فرمت‌بندی و لینک‌دهی استفاده کنید. این محتوا با قابلیت "نمایش بیشتر" نمایش داده می‌شود.</p>
                    </div>
                </div>
                
            </div>
            
            <?php submit_button('ذخیره تنظیمات صفحه اصلی'); ?>
            <input type="hidden" name="active_tab" value="front-page" />
        </form>
        
        <style>
        .zarin-settings-accordion {
            margin-top: 20px;
        }
        
        .zarin-section-accordion {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            background: #fff;
        }
        
        .zarin-section-header {
            margin: 0;
            padding: 15px 20px;
            background: #f5f5f5;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            font-weight: 600;
            position: relative;
            user-select: none;
        }
        
        .zarin-section-header:hover {
            background: #f0f0f0;
        }
        
        .zarin-section-header::after {
            content: '▼';
            position: absolute;
            right: 20px;
            transition: transform 0.3s;
        }
        
        .zarin-section-accordion.active .zarin-section-header::after {
            transform: rotate(180deg);
        }
        
        .zarin-section-content {
            display: none;
            padding: 20px;
        }
        
        .zarin-section-accordion.active .zarin-section-content {
            display: block;
        }
        
        .zarin-section-content .form-table {
            margin-top: 0;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Accordion functionality for front page settings
            $('.zarin-settings-accordion .zarin-section-header').on('click', function() {
                $(this).parent().toggleClass('active');
            });
            
            // Open first accordion by default
            $('.zarin-settings-accordion .zarin-section-accordion').first().addClass('active');
        });
        </script>
        <?php
    }
    
    /**
     * Render Values Repeater
     */
    private function render_values_repeater() {
        $items = $this->get_option('values_items', $this->get_default_values());
        if (empty($items) || !is_array($items)) {
            $items = $this->get_default_values();
        }
        // Limit to 4 items
        $items = array_slice($items, 0, 4);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label>ارزش‌های خرید از خانه ایرانی</label></th>
                <td>
                    <div class="zs-values-manager">
                        <div class="values-items">
                            <?php foreach ($items as $index => $item): ?>
                            <div class="value-item" data-index="<?php echo intval($index); ?>">
                                <div class="value-header">
                                    <h4>ارزش <?php echo intval($index) + 1; ?></h4>
                                    <button type="button" class="button link-button remove-value">حذف</button>
                                </div>
                                <div class="value-fields">
                                    <p>
                                        <label>آیکون (کلاس FontAwesome):</label>
                                        <input type="text" class="regular-text" name="khane-irani-settings[values_items][<?php echo intval($index); ?>][icon]" value="<?php echo esc_attr($item['icon'] ?? 'fas fa-star'); ?>" placeholder="مثال: fas fa-award" />
                                        <span class="description">کلاس آیکون FontAwesome (مثال: fas fa-award)</span>
                                    </p>
                                    <p>
                                        <label>عنوان:</label>
                                        <input type="text" class="regular-text" name="khane-irani-settings[values_items][<?php echo intval($index); ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" />
                                    </p>
                                    <p>
                                        <label>توضیحات:</label>
                                        <textarea class="large-text" rows="2" name="khane-irani-settings[values_items][<?php echo intval($index); ?>][description]"><?php echo esc_textarea($item['description'] ?? ''); ?></textarea>
                                    </p>
                                    <p>
                                        <label><input type="checkbox" name="khane-irani-settings[values_items][<?php echo intval($index); ?>][is_active]" value="1" <?php checked(($item['is_active'] ?? '1'), '1'); ?> /> فعال</label>
                                    </p>
                                </div>
                                <hr />
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($items) < 4): ?>
                        <p><button type="button" class="button button-secondary add-value">افزودن ارزش جدید</button> (حداکثر ۴ مورد)</p>
                        <?php else: ?>
                        <p class="description">حداکثر ۴ ارزش می‌توانید اضافه کنید.</p>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
        <script>
        jQuery(function($){
            let valueIdx = <?php echo count($items); ?>;
            const valueTpl = (i) => `
            <div class="value-item" data-index="${i}">
              <div class="value-header">
                <h4>ارزش ${i + 1}</h4>
                <button type="button" class="button link-button remove-value">حذف</button>
              </div>
              <div class="value-fields">
                <p><label>آیکون (کلاس FontAwesome):</label><input type="text" class="regular-text" name="khane-irani-settings[values_items][${i}][icon]" value="fas fa-star" placeholder="مثال: fas fa-award" /><span class="description">کلاس آیکون FontAwesome (مثال: fas fa-award)</span></p>
                <p><label>عنوان:</label><input type="text" class="regular-text" name="khane-irani-settings[values_items][${i}][title]" value="" /></p>
                <p><label>توضیحات:</label><textarea class="large-text" rows="2" name="khane-irani-settings[values_items][${i}][description]"></textarea></p>
                <p><label><input type="checkbox" name="khane-irani-settings[values_items][${i}][is_active]" value="1" checked /> فعال</label></p>
              </div>
              <hr />
            </div>`;
            $('.zs-values-manager').on('click','.add-value',function(){
                if ($('.zs-values-manager .value-item').length < 4) {
                    $('.zs-values-manager .values-items').append(valueTpl(valueIdx++));
                } else {
                    alert('حداکثر ۴ ارزش می‌توانید اضافه کنید.');
                }
            });
            $('.zs-values-manager').on('click','.remove-value',function(){ $(this).closest('.value-item').remove(); });
        });
        </script>
        <style>
        .zs-values-manager .value-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        .zs-values-manager .value-header h4 { margin: 0; }
        .zs-values-manager .value-fields p { margin: 10px 0; }
        .zs-values-manager .value-item { background: #f9fafb; padding: 15px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 4px; }
        </style>
        <?php
    }
    
    /**
     * Get default values
     */
    private function get_default_values() {
        return array(
            array('icon' => 'fas fa-award', 'title' => 'کیفیت برتر', 'description' => 'انتخاب بهترین محصولات با کیفیت بالا و استانداردهای بین‌المللی', 'is_active' => '1'),
            array('icon' => 'fas fa-shipping-fast', 'title' => 'ارسال سریع', 'description' => 'ارسال فوری سفارش‌ها به سراسر کشور با بسته‌بندی مناسب', 'is_active' => '1'),
            array('icon' => 'fas fa-shield-alt', 'title' => 'ضمانت اصالت', 'description' => 'ضمانت اصالت کالا و کیفیت با امکان بازگشت محصول', 'is_active' => '1'),
            array('icon' => 'fas fa-dollar-sign', 'title' => 'قیمت مناسب', 'description' => 'بهترین قیمت‌ها با تخفیف‌های ویژه و برنامه‌های وفاداری', 'is_active' => '1'),
        );
    }
    
    /**
     * Render Banners Repeater
     */
    private function render_banners_repeater() {
        $items = $this->get_option('banners_items', array());
        if (empty($items) || !is_array($items)) {
            $items = array();
        }
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label>مدیریت بنرهای اسلایدر</label></th>
                <td>
                    <div class="zs-banners-manager">
                        <div class="banners-items">
                            <?php foreach ($items as $index => $item): ?>
                            <div class="banner-item" data-index="<?php echo intval($index); ?>">
                                <div class="banner-header">
                                    <h4>بنر <?php echo intval($index) + 1; ?></h4>
                                    <button type="button" class="button link-button remove-banner">حذف</button>
                                </div>
                                <div class="banner-fields">
                                    <p>
                                        <label>تصویر دسکتاپ:</label>
                                        <div class="image-upload-wrapper">
                                            <input type="hidden" class="banner-image-input" name="khane-irani-settings[banners_items][<?php echo intval($index); ?>][image]" value="<?php echo esc_attr($item['image'] ?? ''); ?>" />
                                            <div class="image-preview"><?php if (!empty($item['image'])) echo wp_get_attachment_image(intval($item['image']), 'thumbnail'); ?></div>
                                            <button type="button" class="button upload-image-button">انتخاب تصویر</button>
                                            <button type="button" class="button remove-image-button" style="display: <?php echo !empty($item['image']) ? 'inline-block' : 'none'; ?>">حذف تصویر</button>
                                        </div>
                                    </p>
                                    <p>
                                        <label>تصویر موبایل (اختیاری):</label>
                                        <div class="image-upload-wrapper">
                                            <input type="hidden" class="banner-image-mobile-input" name="khane-irani-settings[banners_items][<?php echo intval($index); ?>][mobile_image]" value="<?php echo esc_attr($item['mobile_image'] ?? ''); ?>" />
                                            <div class="image-preview"><?php if (!empty($item['mobile_image'])) echo wp_get_attachment_image(intval($item['mobile_image']), 'thumbnail'); ?></div>
                                            <button type="button" class="button upload-image-mobile-button">انتخاب تصویر موبایل</button>
                                            <button type="button" class="button remove-image-mobile-button" style="display: <?php echo !empty($item['mobile_image']) ? 'inline-block' : 'none'; ?>">حذف تصویر</button>
                                        </div>
                                        <span class="description">در صورت عدم انتخاب، تصویر دسکتاپ در موبایل هم نمایش داده می‌شود</span>
                                    </p>
                                    <p>
                                        <label>عنوان (اختیاری):</label>
                                        <input type="text" class="regular-text" name="khane-irani-settings[banners_items][<?php echo intval($index); ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" placeholder="عنوان بنر" />
                                    </p>
                                    <p>
                                        <label>لینک (URL):</label>
                                        <input type="url" class="regular-text" name="khane-irani-settings[banners_items][<?php echo intval($index); ?>][url]" value="<?php echo esc_attr($item['url'] ?? ''); ?>" placeholder="https://example.com" />
                                        <span class="description">لینک مقصد هنگام کلیک روی بنر (اختیاری)</span>
                                    </p>
                                    <p>
                                        <label><input type="checkbox" name="khane-irani-settings[banners_items][<?php echo intval($index); ?>][is_active]" value="1" <?php checked(($item['is_active'] ?? '1'), '1'); ?> /> فعال</label>
                                    </p>
                                    <p>
                                        <label>ترتیب:</label>
                                        <input type="number" class="small-text" name="khane-irani-settings[banners_items][<?php echo intval($index); ?>][sort_order]" value="<?php echo esc_attr($item['sort_order'] ?? $index); ?>" />
                                    </p>
                                </div>
                                <hr />
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <p><button type="button" class="button button-secondary add-banner">افزودن بنر جدید</button></p>
                    </div>
                </td>
            </tr>
        </table>
        <script>
        jQuery(function($){
            let bannerIdx = <?php echo count($items); ?>;
            const bannerTpl = (i) => `
            <div class="banner-item" data-index="${i}">
              <div class="banner-header">
                <h4>بنر ${i + 1}</h4>
                <button type="button" class="button link-button remove-banner">حذف</button>
              </div>
              <div class="banner-fields">
                <p><label>تصویر دسکتاپ:</label>
                  <div class="image-upload-wrapper">
                    <input type="hidden" class="banner-image-input" name="khane-irani-settings[banners_items][${i}][image]" value="" />
                    <div class="image-preview"></div>
                    <button type="button" class="button upload-image-button">انتخاب تصویر</button>
                    <button type="button" class="button remove-image-button" style="display:none;">حذف تصویر</button>
                  </div>
                </p>
                <p><label>تصویر موبایل (اختیاری):</label>
                  <div class="image-upload-wrapper">
                    <input type="hidden" class="banner-image-mobile-input" name="khane-irani-settings[banners_items][${i}][mobile_image]" value="" />
                    <div class="image-preview"></div>
                    <button type="button" class="button upload-image-mobile-button">انتخاب تصویر موبایل</button>
                    <button type="button" class="button remove-image-mobile-button" style="display:none;">حذف تصویر</button>
                  </div>
                  <span class="description">در صورت عدم انتخاب، تصویر دسکتاپ در موبایل نمایش داده می‌شود</span>
                </p>
                <p><label>عنوان (اختیاری):</label><input type="text" class="regular-text" name="khane-irani-settings[banners_items][${i}][title]" value="" placeholder="عنوان بنر" /></p>
                <p><label>لینک (URL):</label><input type="url" class="regular-text" name="khane-irani-settings[banners_items][${i}][url]" value="" placeholder="https://example.com" /><span class="description">لینک مقصد هنگام کلیک روی بنر (اختیاری)</span></p>
                <p><label><input type="checkbox" name="khane-irani-settings[banners_items][${i}][is_active]" value="1" checked /> فعال</label></p>
                <p><label>ترتیب:</label><input type="number" class="small-text" name="khane-irani-settings[banners_items][${i}][sort_order]" value="${i}" /></p>
              </div>
              <hr />
            </div>`;
            $('.zs-banners-manager').on('click','.add-banner',function(){ $('.zs-banners-manager .banners-items').append(bannerTpl(bannerIdx++)); });
            $('.zs-banners-manager').on('click','.remove-banner',function(){ 
                $(this).closest('.banner-item').remove();
                // Re-index remaining banners
                $('.zs-banners-manager .banner-item').each(function(index){
                    $(this).find('h4').text('بنر ' + (index + 1));
                    $(this).attr('data-index', index);
                    // Update all input names with new index
                    $(this).find('input, select, textarea').each(function(){
                        const name = $(this).attr('name');
                        if(name && name.includes('[banners_items]')){
                            const newName = name.replace(/\[banners_items\]\[\d+\]/, '[banners_items][' + index + ']');
                            $(this).attr('name', newName);
                        }
                    });
                });
            });
            $('.zs-banners-manager').on('click','.upload-image-button',function(){
                const button=$(this), input=button.siblings('.banner-image-input'), preview=button.siblings('.image-preview'), removeBtn=button.siblings('.remove-image-button');
                const frame=wp.media({title:'انتخاب تصویر بنر',button:{text:'انتخاب'},multiple:false});
                frame.on('select',function(){ const at=frame.state().get('selection').first().toJSON(); input.val(at.id); preview.html('<img src="'+(at.sizes?.thumbnail?.url||at.url)+'" style="max-width:100%;height:auto;" />'); removeBtn.show(); });
                frame.open();
            });
            $('.zs-banners-manager').on('click','.remove-image-button',function(){ $(this).siblings('.banner-image-input').val(''); $(this).siblings('.image-preview').empty(); $(this).hide(); });
            $('.zs-banners-manager').on('click','.upload-image-mobile-button',function(){
                const button=$(this), input=button.siblings('.banner-image-mobile-input'), preview=button.siblings('.image-preview'), removeBtn=button.siblings('.remove-image-mobile-button');
                const frame=wp.media({title:'انتخاب تصویر موبایل',button:{text:'انتخاب'},multiple:false});
                frame.on('select',function(){ const at=frame.state().get('selection').first().toJSON(); input.val(at.id); preview.html('<img src="'+(at.sizes?.thumbnail?.url||at.url)+'" style="max-width:100%;height:auto;" />'); removeBtn.show(); });
                frame.open();
            });
            $('.zs-banners-manager').on('click','.remove-image-mobile-button',function(){ $(this).siblings('.banner-image-mobile-input').val(''); $(this).siblings('.image-preview').empty(); $(this).hide(); });
        });
        </script>
        <style>
        .zs-banners-manager .banner-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        .zs-banners-manager .banner-header h4 { margin: 0; }
        .zs-banners-manager .banner-fields p { margin: 10px 0; }
        .zs-banners-manager .banner-item { background: #f9fafb; padding: 15px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 4px; }
        .zs-banners-manager .image-upload-wrapper { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .zs-banners-manager .image-preview { width: 80px; height: 80px; border: 1px dashed #d1d5db; display: flex; align-items: center; justify-content: center; background: #f9fafb; border-radius: 4px; }
        .zs-banners-manager .image-preview img { max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 4px; }
        </style>
        <?php
    }
    
    /**
     * Render Custom Product Carousels Repeater
     */
    private function render_custom_carousels_repeater() {
        $items = $this->get_option('custom_product_carousels', array());
        if (empty($items) || !is_array($items)) {
            $items = array();
        }
        // Limit to 4 carousels
        $items = array_slice($items, 0, 4);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label>کاروسل‌های اختیاری محصولات</label></th>
                <td>
                    <div class="zs-custom-carousels-manager">
                        <div class="custom-carousels-items">
                            <?php foreach ($items as $index => $item): ?>
                            <div class="custom-carousel-item" data-index="<?php echo intval($index); ?>">
                                <div class="carousel-header">
                                    <h4>کاروسل <?php echo intval($index) + 1; ?></h4>
                                    <button type="button" class="button link-button remove-carousel">حذف</button>
                                </div>
                                <div class="carousel-fields">
                                    <p>
                                        <label>عنوان کاروسل:</label>
                                        <input type="text" class="regular-text" name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" />
                                    </p>
                                    <p>
                                        <label>دسته‌بندی‌های محصولات:</label>
                                        <div class="category-selector">
                                            <?php
                                            $selected_cats = isset($item['category_ids']) && is_array($item['category_ids']) ? $item['category_ids'] : array();
                                            $categories = get_terms(array(
                                                'taxonomy' => 'product_cat',
                                                'hide_empty' => false,
                                                'parent' => 0
                                            ));
                                            if (!is_wp_error($categories) && !empty($categories)):
                                            ?>
                                                <select name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][category_ids][]" multiple class="regular-text" style="height: 120px;">
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected(in_array($cat->term_id, $selected_cats), true); ?>>
                                                            <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="description">برای انتخاب چند دسته‌بندی، Ctrl (یا Cmd در Mac) را نگه دارید</span>
                                            <?php endif; ?>
                                        </div>
                                    </p>
                                    <p>
                                        <label>تعداد محصولات:</label>
                                        <input type="number" class="small-text" name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][product_count]" value="<?php echo esc_attr($item['product_count'] ?? '12'); ?>" min="1" max="20" />
                                    </p>
                                    <p>
                                        <label>نمایش دکمه "مشاهده همه":</label>
                                        <select name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][show_link]">
                                            <option value="1" <?php selected(($item['show_link'] ?? '1'), '1'); ?>>بله</option>
                                            <option value="0" <?php selected(($item['show_link'] ?? '1'), '0'); ?>>خیر</option>
                                        </select>
                                    </p>
                                    <p>
                                        <label>لینک "مشاهده همه" (اختیاری):</label>
                                        <input type="url" class="regular-text" name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][link_url]" value="<?php echo esc_attr($item['link_url'] ?? home_url('/shop')); ?>" />
                                    </p>
                                    <p>
                                        <label>متن دکمه "مشاهده همه":</label>
                                        <input type="text" class="regular-text" name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][link_text]" value="<?php echo esc_attr($item['link_text'] ?? 'مشاهده همه'); ?>" />
                                    </p>
                                    <p>
                                        <label><input type="checkbox" name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][is_active]" value="1" <?php checked(($item['is_active'] ?? '1'), '1'); ?> /> فعال</label>
                                    </p>
                                    <p>
                                        <label>ترتیب:</label>
                                        <input type="number" class="small-text" name="khane-irani-settings[custom_product_carousels][<?php echo intval($index); ?>][sort_order]" value="<?php echo esc_attr($item['sort_order'] ?? $index); ?>" />
                                    </p>
                                </div>
                                <hr />
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($items) < 4): ?>
                        <p><button type="button" class="button button-secondary add-carousel">افزودن کاروسل جدید</button> (حداکثر ۴ کاروسل)</p>
                        <?php else: ?>
                        <p class="description">حداکثر ۴ کاروسل می‌توانید اضافه کنید.</p>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
        <script>
        jQuery(function($){
            let carouselIdx = <?php echo count($items); ?>;
            <?php
            // Get categories for JavaScript
            $all_categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'parent' => 0
            ));
            $cats_js = array();
            if (!is_wp_error($all_categories) && !empty($all_categories)) {
                foreach ($all_categories as $cat) {
                    $cats_js[] = array(
                        'id' => $cat->term_id,
                        'name' => $cat->name,
                        'count' => $cat->count
                    );
                }
            }
            ?>
            const categoriesList = <?php echo json_encode($cats_js); ?>;
            const carouselTpl = (i) => {
                let catsHtml = '';
                categoriesList.forEach(function(cat) {
                    catsHtml += '<option value="' + cat.id + '">' + cat.name + ' (' + cat.count + ')</option>';
                });
                return `
            <div class="custom-carousel-item" data-index="${i}">
              <div class="carousel-header">
                <h4>کاروسل ${i + 1}</h4>
                <button type="button" class="button link-button remove-carousel">حذف</button>
              </div>
              <div class="carousel-fields">
                <p><label>عنوان کاروسل:</label><input type="text" class="regular-text" name="khane-irani-settings[custom_product_carousels][${i}][title]" value="" /></p>
                <p><label>دسته‌بندی‌های محصولات:</label>
                  <div class="category-selector">
                    <select name="khane-irani-settings[custom_product_carousels][${i}][category_ids][]" multiple class="regular-text" style="height: 120px;">
                      ${catsHtml}
                    </select>
                    <span class="description">برای انتخاب چند دسته‌بندی، Ctrl (یا Cmd در Mac) را نگه دارید</span>
                  </div>
                </p>
                <p><label>تعداد محصولات:</label><input type="number" class="small-text" name="khane-irani-settings[custom_product_carousels][${i}][product_count]" value="12" min="1" max="20" /></p>
                <p><label>نمایش دکمه "مشاهده همه":</label>
                  <select name="khane-irani-settings[custom_product_carousels][${i}][show_link]">
                    <option value="1">بله</option>
                    <option value="0">خیر</option>
                  </select>
                </p>
                <p><label>لینک "مشاهده همه" (اختیاری):</label><input type="url" class="regular-text" name="khane-irani-settings[custom_product_carousels][${i}][link_url]" value="<?php echo esc_js(home_url('/shop')); ?>" /></p>
                <p><label>متن دکمه "مشاهده همه":</label><input type="text" class="regular-text" name="khane-irani-settings[custom_product_carousels][${i}][link_text]" value="مشاهده همه" /></p>
                <p><label><input type="checkbox" name="khane-irani-settings[custom_product_carousels][${i}][is_active]" value="1" checked /> فعال</label></p>
                <p><label>ترتیب:</label><input type="number" class="small-text" name="khane-irani-settings[custom_product_carousels][${i}][sort_order]" value="${i}" /></p>
              </div>
              <hr />
            </div>`;
            };
            $('.zs-custom-carousels-manager').on('click','.add-carousel',function(){
                if ($('.zs-custom-carousels-manager .custom-carousel-item').length < 4) {
                    $('.zs-custom-carousels-manager .custom-carousels-items').append(carouselTpl(carouselIdx++));
                } else {
                    alert('حداکثر ۴ کاروسل می‌توانید اضافه کنید.');
                }
            });
            $('.zs-custom-carousels-manager').on('click','.remove-carousel',function(){ $(this).closest('.custom-carousel-item').remove(); });
        });
        </script>
        <style>
        .zs-custom-carousels-manager .carousel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        .zs-custom-carousels-manager .carousel-header h4 { margin: 0; }
        .zs-custom-carousels-manager .carousel-fields p { margin: 10px 0; }
        .zs-custom-carousels-manager .custom-carousel-item { background: #f9fafb; padding: 15px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 4px; }
        .zs-custom-carousels-manager .category-selector select { width: 100%; }
        </style>
        <?php
    }
    
}

