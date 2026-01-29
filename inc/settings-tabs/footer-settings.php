<?php
/**
 * Footer Settings Tab
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Footer Settings Class
 */
class khane_irani_Footer_Settings extends khane_irani_Settings_Tab {
    
    public function __construct() {
        parent::__construct('footer', 'تنظیمات فوتر');
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
                    'type' => 'image',
                    'id' => 'footer_logo',
                    'label' => 'لوگوی فوتر',
                    'default' => '',
                    'description' => 'لوگوی نمایش داده شده در فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'image',
                    'id' => 'footer_bg_image',
                    'label' => 'تصویر بکگراند باکس آبی',
                    'default' => '',
                    'description' => 'تصویر بکگراند باکس آبی فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'footer_description',
                    'label' => 'توضیحات فوتر',
                    'default' => 'خانه ایرانی فروشگاه آنلاین محصولات با کیفیت بالا و قیمت مناسب',
                    'description' => 'توضیحات کوتاه در فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'footer_phone',
                    'label' => 'شماره تلفن فوتر',
                    'default' => '',
                    'description' => 'شماره تلفن نمایش داده شده در فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'footer_email',
                    'label' => 'ایمیل فوتر',
                    'default' => '',
                    'description' => 'ایمیل نمایش داده شده در فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'footer_address',
                    'label' => 'آدرس فوتر',
                    'default' => '',
                    'description' => 'آدرس نمایش داده شده در فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'footer_copyright',
                    'label' => 'متن کپی‌رایت',
                    'default' => 'تمامی حقوق محفوظ است © ' . date('Y'),
                    'description' => 'متن کپی‌رایت در پایین فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'checkbox',
                    'id' => 'show_social_links',
                    'label' => 'نمایش لینک‌های شبکه‌های اجتماعی',
                    'default' => '1',
                    'description' => 'نمایش آیکون‌های شبکه‌های اجتماعی در فوتر'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'facebook_url',
                    'label' => 'لینک فیسبوک',
                    'default' => '',
                    'description' => 'لینک صفحه فیسبوک'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'instagram_url',
                    'label' => 'لینک اینستاگرام',
                    'default' => '',
                    'description' => 'لینک صفحه اینستاگرام'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'telegram_url',
                    'label' => 'لینک تلگرام',
                    'default' => '',
                    'description' => 'لینک کانال تلگرام'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'whatsapp_url',
                    'label' => 'شماره واتساپ',
                    'default' => '',
                    'description' => 'شماره واتساپ (با کد کشور شروع شود)'
                ));
                
                ?>
            </table>
            
            <?php submit_button('ذخیره تنظیمات فوتر'); ?>
            <input type="hidden" name="active_tab" value="footer" />
        </form>
        <?php
    }
}
