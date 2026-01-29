<?php
/**
 * About Settings Tab
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * About Settings Class
 */
class khane_irani_About_Settings extends khane_irani_Settings_Tab {
    
    public function __construct() {
        parent::__construct('about', 'درباره ما');
    }
    
    protected function add_settings_fields() { }
    
    public function render_tab_content() {
        ?>
        <form method="post" action="options.php" id="zarin-settings-form">
            <?php
            settings_fields('khane-irani-settings');
            ?>
            <h2 style="margin-top: 0;">سربرگ صفحه</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'about_title',
                    'label' => 'عنوان صفحه',
                    'default' => 'درباره خانه ایرانی',
                    'description' => 'عنوان اصلی صفحه درباره ما'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'about_description',
                    'label' => 'توضیحات صفحه',
                    'default' => 'فروشگاه اینترنتی خانه ایرانی، عرضه‌کننده محصولات فیزیکی با کیفیت و قیمت مناسب',
                    'description' => 'توضیحات زیر عنوان صفحه'
                ));
                ?>
            </table>

            <h2>بخش ارزش‌های ما</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'values_title',
                    'label' => 'عنوان بخش',
                    'default' => 'ارزش‌های ما',
                    'description' => 'عنوان بخش ارزش‌های ما'
                ));

                // Get current values
                $current_values = $this->get_option('values_items', '');
                $values_array = array();


                if (!empty($current_values)) {
                    // Check if values are stored as new array format or old string format
                    if (is_array($current_values)) {
                        // New format: already an array
                        foreach ($current_values as $item) {
                            if (is_array($item) &&
                                isset($item['emoji']) &&
                                isset($item['title']) &&
                                isset($item['description'])) {
                                $values_array[] = array(
                                    'emoji' => $item['emoji'],
                                    'title' => $item['title'],
                                    'description' => $item['description']
                                );
                            }
                        }
                    } else {
                        // Old format: pipe-separated string
                        $raw_items = explode('|', $current_values);
                        $items_count = count($raw_items);
                        for ($i = 0; $i < $items_count; $i += 3) {
                            if (isset($raw_items[$i + 2])) {
                                $values_array[] = array(
                                    'emoji' => trim($raw_items[$i]),
                                    'title' => trim($raw_items[$i + 1]),
                                    'description' => trim($raw_items[$i + 2])
                                );
                            }
                        }
                    }
                }

                // If no values, add default ones
                if (empty($values_array)) {
                    $values_array = array(
                        array('emoji' => '🛍️', 'title' => 'کیفیت برتر', 'description' => 'فقط محصولات با کیفیت و اصل را در فروشگاه خود عرضه می‌کنیم.'),
                        array('emoji' => '💰', 'title' => 'قیمت مناسب', 'description' => 'بهترین قیمت‌ها را با حفظ کیفیت برای مشتریان عزیز فراهم کرده‌ایم.'),
                        array('emoji' => '🚚', 'title' => 'ارسال سریع', 'description' => 'ارسال سریع و مطمئن محصولات به سراسر کشور با بسته‌بندی مناسب.'),
                        array('emoji' => '❤️', 'title' => 'رضایت مشتری', 'description' => 'رضایت و اعتماد شما مهم‌ترین اولویت ماست.')
                    );
                }
                ?>
                <tr>
                    <th scope="row">لیست ارزش‌ها</th>
                    <td>
                        <div id="values-items-container">
                            <?php foreach ($values_array as $index => $value): ?>
                            <div class="values-item" data-index="<?php echo $index; ?>">
                                <div class="values-item-fields">
                                    <input type="text"
                                           name="khane-irani-settings[values_items][<?php echo $index; ?>][emoji]"
                                           value="<?php echo esc_attr($value['emoji']); ?>"
                                           placeholder="ایموجی"
                                           class="values-emoji"
                                           maxlength="2" />
                                    <input type="text"
                                           name="khane-irani-settings[values_items][<?php echo $index; ?>][title]"
                                           value="<?php echo esc_attr($value['title']); ?>"
                                           placeholder="عنوان ارزش"
                                           class="values-title" />
                                    <textarea name="khane-irani-settings[values_items][<?php echo $index; ?>][description]"
                                              placeholder="توضیحات ارزش"
                                              class="values-description"
                                              rows="2"><?php echo esc_textarea($value['description']); ?></textarea>
                                </div>
                                <button type="button" class="button remove-values-item" title="حذف این ارزش">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="add-values-item" class="button button-primary">
                            <span class="dashicons dashicons-plus"></span>
                            افزودن ارزش جدید
                        </button>
                        <p class="description">برای هر ارزش، ایموجی، عنوان و توضیحات را وارد کنید. ایموجی‌ها در نمایش نهایی به آیکون‌های FontAwesome تبدیل می‌شوند.</p>
                    </td>
                </tr>
            </table>

            <h2>بخش محتوای اصلی</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'about_content_title',
                    'label' => 'عنوان محتوا',
                    'default' => 'خانه ایرانی، فروشگاه اینترنتی محصولات فیزیکی',
                    'description' => 'عنوان اصلی بخش محتوا'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'about_content_text',
                    'label' => 'متن محتوا',
                    'default' => 'خانه ایرانی یک فروشگاه اینترنتی معتبر است که با هدف ارائه محصولات با کیفیت و قیمت مناسب برای خانواده‌های ایرانی راه‌اندازی شده است. ما در زمینه عرضه انواع محصولات فیزیکی از جمله نوشت‌افزار، لباس کودک و اسباب بازی فعالیت می‌کنیم.',
                    'description' => 'متن اصلی درباره فروشگاه'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'about_features_title',
                    'label' => 'عنوان بخش محصولات',
                    'default' => 'محصولات ما',
                    'description' => 'عنوان بخش لیست محصولات'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'about_features',
                    'label' => 'لیست محصولات',
                    'default' => 'نوشت‌افزار با کیفیت|لباس کودک و نوجوان|اسباب بازی و سرگرمی|محصولات آموزشی|لوازم تحریر مدرسه',
                    'description' => 'لیست محصولات و دسته‌بندی‌ها (با | جدا کنید)'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'about_why_title',
                    'label' => 'عنوان بخش "چرا خانه ایرانی"',
                    'default' => 'چرا خانه ایرانی؟',
                    'description' => 'عنوان بخش آخر'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'about_why_text',
                    'label' => 'متن "چرا خانه ایرانی"',
                    'default' => 'ما با سال‌ها تجربه در زمینه فروش محصولات فیزیکی، فقط کالاهای اصل و با کیفیت را عرضه می‌کنیم. تمام محصولات ما دارای گارانتی اصالت بوده و با بهترین قیمت‌ها و ارسال سریع در دسترس شما قرار می‌گیرند. هدف ما رضایت شماست.',
                    'description' => 'متن توضیحی بخش آخر'
                ));
                
                $this->render_field(array(
                    'type' => 'image',
                    'id' => 'about_image',
                    'label' => 'تصویر بخش درباره ما',
                    'default' => '',
                    'description' => 'تصویر نمایش داده شده در بخش درباره ما'
                ));
                ?>
            </table>

            <h2>بخش آمار</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'stats_items',
                    'label' => 'لیست آمار',
                    'default' => '1000+|مشتری راضی|500+|محصول متنوع|3+|دسته‌بندی اصلی|98%|رضایت مشتری',
                    'description' => 'فرمت: عدد|برچسب (با | جدا کنید، هر 2 مورد یک آمار)'
                ));
                ?>
            </table>

            <h2>بخش تماس (CTA)</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'about_cta_title',
                    'label' => 'عنوان CTA',
                    'default' => 'آماده خرید هستید؟',
                    'description' => 'عنوان بخش CTA'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'about_cta_text',
                    'label' => 'متن CTA',
                    'default' => 'برای خرید محصولات و یا دریافت مشاوره با ما تماس بگیرید',
                    'description' => 'متن زیر عنوان CTA'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'about_cta_button_text',
                    'label' => 'متن دکمه',
                    'default' => 'تماس با ما',
                    'description' => 'متن دکمه CTA'
                ));
                ?>
            </table>
            
            <?php submit_button('ذخیره تنظیمات درباره ما'); ?>
            <input type="hidden" name="active_tab" value="about" />
        </form>
        <?php
    }
}
