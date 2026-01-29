<?php
/**
 * Header Settings Tab
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Header Settings Class
 */
class khane_irani_Header_Settings extends khane_irani_Settings_Tab {
    
    public function __construct() {
        parent::__construct('header', 'تنظیمات هدر');
    }
    
    protected function add_settings_fields() { }
    
    public function render_tab_content() {
        // Ensure media assets are available even if admin enqueue missed it
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        ?>
        <form method="post" action="options.php" id="zarin-settings-form">
            <?php
            settings_fields('khane-irani-settings');
            ?>
            <h2 style="margin-top: 0;">تنظیمات لوگو و رنگ‌بندی</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'image',
                    'id' => 'header_logo',
                    'label' => 'لوگو سایت',
                    'default' => '',
                    'description' => 'لوگوی اصلی سایت را انتخاب کنید'
                ));

                $this->render_field(array(
                    'type' => 'color',
                    'id' => 'header_bg_color',
                    'label' => 'رنگ پس‌زمینه هدر',
                    'default' => '#ffffff',
                    'description' => 'رنگ پس‌زمینه هدر'
                ));

                $this->render_field(array(
                    'type' => 'color',
                    'id' => 'header_text_color',
                    'label' => 'رنگ متن هدر',
                    'default' => '#333333',
                    'description' => 'رنگ متن و آیکون‌های هدر'
                ));
                ?>
            </table>

            <div class="hero-settings-info">
                <h3>تنظیمات پیش‌فرض</h3>
                <p>تنظیمات زیر به صورت خودکار اعمال می‌شوند:</p>
                <ul style="margin-bottom: 0; padding-right: 20px;">
                    <li>عرض لوگو: 150 پیکسل</li>
                    <li>ارتفاع لوگو: 60 پیکسل</li>
                    <li>جستجو: همیشه نمایش داده می‌شود</li>
                    <li>متن placeholder جستجو: "جستجو..."</li>
                </ul>
            </div>
            
            <?php 
            submit_button('ذخیره تنظیمات هدر', 'primary', 'submit', true, array('id' => 'zarin-submit-btn'));
            ?>
            <input type="hidden" name="active_tab" value="header" />
        </form>
        
        
        <?php
    }
}
