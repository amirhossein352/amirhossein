<?php
/**
 * Services Settings Tab
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Services Settings Class
 */
class khane_irani_Services_Settings extends khane_irani_Settings_Tab {
    
    public function __construct() {
        parent::__construct('services', 'محصولات');
    }
    
    protected function add_settings_fields() { }
    
    public function render_tab_content() {
        ?>
        <form method="post" action="options.php" id="zarin-settings-form">
            <?php
            settings_fields('khane-irani-settings');
            ?>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'services_title',
                    'label' => 'عنوان بخش محصولات',
                    'default' => 'محصولات ما',
                    'description' => 'عنوان اصلی بخش محصولات'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'services_subtitle',
                    'label' => 'زیرعنوان بخش محصولات',
                    'default' => 'محصولات با کیفیت بالا و قیمت مناسب',
                    'description' => 'زیرعنوان بخش محصولات'
                ));
                
                $this->render_field(array(
                    'type' => 'checkbox',
                    'id' => 'show_services_section',
                    'label' => 'نمایش بخش محصولات',
                    'default' => '1',
                    'description' => 'نمایش بخش محصولات در صفحه اصلی'
                ));

                // FAQ manager (repeater)
                $this->render_services_faq_field(array(
                    'id' => 'services_faq_items',
                    'label' => 'سوالات پرتکرار (FAQ)',
                    'description' => 'افزودن/ویرایش سوالات پرتکرار. تصویر اختیاری است. تعداد نامحدود.'
                ));
                ?>
            </table>
            
            <?php submit_button('ذخیره تنظیمات محصولات'); ?>
            <input type="hidden" name="active_tab" value="services" />
        </form>
        <?php
    }

    /**
     * FAQ repeater field for services tab
     */
    private function get_faq_items_default() {
        return array(
            array(
                'question' => 'چطور سفارش ثبت کنم؟',
                'answer' => 'از طریق دکمه افزودن به سبد خرید در کارت محصول اقدام کنید.',
                'author' => '',
                'image' => '',
                'is_featured' => '1',
                'is_active' => '1',
                'sort_order' => 0
            )
        );
    }

    public function render_services_faq_field($args) {
        $items = $this->get_option($args['id'], $this->get_faq_items_default());
        if (empty($items) || !is_array($items)) { $items = $this->get_faq_items_default(); }
        ?>
        <tr>
            <th scope="row"><label><?php echo esc_html($args['label']); ?></label></th>
            <td>
                <div class="zs-faq-manager">
                    <div class="faq-items">
                        <?php foreach ($items as $index => $item): ?>
                        <div class="faq-item" data-index="<?php echo intval($index); ?>">
                            <div class="faq-grid-row">
                                <p>
                                    <label>سوال:</label>
                                    <input type="text" class="regular-text" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][question]" value="<?php echo esc_attr($item['question'] ?? ''); ?>" />
                                </p>
                                <p>
                                    <label>نویسنده (اختیاری):</label>
                                    <input type="text" class="regular-text" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][author]" value="<?php echo esc_attr($item['author'] ?? ''); ?>" />
                                </p>
                            </div>
                            <p>
                                <label>پاسخ:</label>
                                <textarea class="large-text" rows="3" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][answer]"><?php echo esc_textarea($item['answer'] ?? ''); ?></textarea>
                            </p>
                            <div class="faq-grid-row">
                                <div class="image-upload-wrapper">
                                    <input type="hidden" class="faq-image-input" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][image]" value="<?php echo esc_attr($item['image'] ?? ''); ?>" />
                                    <div class="image-preview"><?php if (!empty($item['image'])) echo wp_get_attachment_image(intval($item['image']), 'thumbnail'); ?></div>
                                    <button type="button" class="button upload-image-button">انتخاب تصویر</button>
                                    <button type="button" class="button remove-image-button" style="display: <?php echo !empty($item['image']) ? 'inline-block' : 'none'; ?>">حذف تصویر</button>
                                </div>
                                <p>
                                    <label><input type="checkbox" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][is_featured]" value="1" <?php checked(($item['is_featured'] ?? '0'), '1'); ?> /> شاخص (کارت بزرگ)</label>
                                </p>
                                <p>
                                    <label><input type="checkbox" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][is_active]" value="1" <?php checked(($item['is_active'] ?? '1'), '1'); ?> /> فعال</label>
                                </p>
                                <p>
                                    <label>ترتیب:</label>
                                    <input type="number" class="small-text" name="khane-irani-settings[<?php echo esc_attr($args['id']); ?>][<?php echo intval($index); ?>][sort_order]" value="<?php echo esc_attr($item['sort_order'] ?? 0); ?>" />
                                </p>
                            </div>
                            <p><button type="button" class="button link-button remove-faq">حذف مورد</button></p>
                            <hr />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p><button type="button" class="button button-secondary add-faq">افزودن سوال جدید</button></p>
                </div>
                <p class="description"><?php echo esc_html($args['description']); ?></p>
            </td>
        </tr>
        <script>
        jQuery(function($){
            let idx = <?php echo count($items); ?>;
            const tpl = (i) => `
            <div class="faq-item" data-index="${i}">
              <div class="faq-grid-row">
                <p><label>سوال:</label><input type="text" class="regular-text" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][question]" value="" /></p>
                <p><label>نویسنده (اختیاری):</label><input type="text" class="regular-text" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][author]" value="" /></p>
              </div>
              <p><label>پاسخ:</label><textarea class="large-text" rows="3" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][answer]"></textarea></p>
              <div class="faq-grid-row">
                <div class="image-upload-wrapper">
                  <input type="hidden" class="faq-image-input" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][image]" value="" />
                  <div class="image-preview"></div>
                  <button type="button" class="button upload-image-button">انتخاب تصویر</button>
                  <button type="button" class="button remove-image-button" style="display:none;">حذف تصویر</button>
                </div>
                <p><label><input type="checkbox" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][is_featured]" value="1" /> شاخص (کارت بزرگ)</label></p>
                <p><label><input type="checkbox" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][is_active]" value="1" checked /> فعال</label></p>
                <p><label>ترتیب:</label><input type="number" class="small-text" name="khane-irani-settings[<?php echo esc_js($args['id']); ?>][${i}][sort_order]" value="0" /></p>
              </div>
              <p><button type="button" class="button link-button remove-faq">حذف مورد</button></p>
              <hr />
            </div>`;

            $('.zs-faq-manager').on('click','.add-faq',function(){
                $('.zs-faq-manager .faq-items').append(tpl(idx++));
            });
            $('.zs-faq-manager').on('click','.remove-faq',function(){
                $(this).closest('.faq-item').remove();
            });
            // media upload
            $('.zs-faq-manager').on('click','.upload-image-button',function(){
                const button = $(this);
                const input = button.siblings('.faq-image-input');
                const preview = button.siblings('.image-preview');
                const removeBtn = button.siblings('.remove-image-button');
                const frame = wp.media({title:'انتخاب تصویر',button:{text:'انتخاب'},multiple:false});
                frame.on('select',function(){
                    const at = frame.state().get('selection').first().toJSON();
                    input.val(at.id);
                    preview.html(`<img src="${at.sizes?.thumbnail?.url||at.url}" style="max-width:100%;height:auto;" />`);
                    removeBtn.show();
                });
                frame.open();
            });
            $('.zs-faq-manager').on('click','.remove-image-button',function(){
                $(this).siblings('.faq-image-input').val('');
                $(this).siblings('.image-preview').empty();
                $(this).hide();
            });
        });
        </script>
        <style>
        .zs-faq-manager .faq-grid-row{display:flex;gap:15px;flex-wrap:wrap}
        .zs-faq-manager .image-upload-wrapper{display:flex;align-items:center;gap:10px}
        .zs-faq-manager .image-preview{width:60px;height:60px;border:1px dashed #d1d5db;display:flex;align-items:center;justify-content:center;background:#f9fafb}
        </style>
        <?php
    }
}
