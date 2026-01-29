<?php
/**
 * Contact Settings Tab
 * 
 * @package khane_irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact Settings Class
 */
class khane_irani_Contact_Settings extends khane_irani_Settings_Tab {
    
    public function __construct() {
        parent::__construct('contact', 'تماس با ما');
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
                    'id' => 'contact_title',
                    'label' => 'عنوان بخش تماس',
                    'default' => 'تماس با ما',
                    'description' => 'عنوان اصلی بخش تماس'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'contact_subtitle',
                    'label' => 'زیرعنوان بخش تماس',
                    'default' => 'برای خرید محصولات و یا دریافت مشاوره با ما تماس بگیرید',
                    'description' => 'زیرعنوان بخش تماس'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'contact_phone',
                    'label' => 'شماره تلفن تماس',
                    'default' => '09903491529',
                    'description' => 'شماره تلفن اصلی برای تماس'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'contact_email',
                    'label' => 'ایمیل تماس',
                    'default' => 'info@khane-irani.com',
                    'description' => 'ایمیل اصلی برای تماس'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'contact_address',
                    'label' => 'آدرس تماس',
                    'default' => 'تهران، ایران',
                    'description' => 'آدرس کامل فروشگاه'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'contact_working_hours',
                    'label' => 'ساعات کاری',
                    'default' => 'شنبه تا چهارشنبه: 9 صبح تا 6 عصر',
                    'description' => 'ساعات کاری فروشگاه'
                ));
                
                $this->render_field(array(
                    'type' => 'checkbox',
                    'id' => 'show_contact_form',
                    'label' => 'نمایش فرم تماس',
                    'default' => '1',
                    'description' => 'نمایش فرم تماس در بخش تماس'
                ));
                
                $this->render_field(array(
                    'type' => 'textarea',
                    'id' => 'contact_map',
                    'label' => 'کد نقشه گوگل',
                    'default' => '',
                    'description' => 'کد iframe نقشه گوگل'
                ));
                
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'contact_map_title',
                    'label' => 'عنوان بخش نقشه',
                    'default' => 'موقعیت ما',
                    'description' => 'عنوان بخش نقشه'
                ));
                ?>
            </table>

            <h2>بخش سوالات متداول (FAQ)</h2>
            <table class="form-table">
                <?php
                $this->render_field(array(
                    'type' => 'text',
                    'id' => 'contact_faq_title',
                    'label' => 'عنوان بخش FAQ',
                    'default' => 'سوالات متداول',
                    'description' => 'عنوان بخش سوالات متداول'
                ));
                ?>
            </table>
            
            <?php
            // Render FAQ Items with better UX
            $faq_items_raw = $this->get_option('contact_faq_items', 'چگونه می‌توانم محصولات شما را خریداری کنم؟|برای خرید محصولات ما، کافی است به بخش فروشگاه مراجعه کنید، محصول مورد نظر را انتخاب کرده و به سبد خرید اضافه کنید. سپس مراحل پرداخت را تکمیل کنید. ما انواع محصولات فیزیکی از جمله نوشت‌افزار، لباس کودک و اسباب بازی را عرضه می‌کنیم.|هزینه ارسال چقدر است؟|هزینه ارسال بسته به مکان مقصد و وزن محصول متفاوت است. برای اطلاع از هزینه دقیق ارسال، می‌توانید در صفحه محصول و یا هنگام تکمیل سفارش مشاهده کنید.|آیا محصولات شما اصل و با کیفیت هستند؟|بله، تمام محصولات عرضه شده در خانه ایرانی اصل و دارای گارانتی اصالت هستند. ما فقط کالاهای با کیفیت و اصل را به مشتریان عرضه می‌کنیم.|آیا امکان برگشت کالا وجود دارد؟|بله، در صورت وجود مشکل در محصول خریداری شده، می‌توانید ظرف مدت مشخص شده کالا را برگشت دهید. لطفاً برای جزئیات بیشتر با پشتیبانی تماس بگیرید.');
            
            // Parse FAQ items
            $faq_items = array();
            if (!empty($faq_items_raw)) {
                $faq_array = explode('|', $faq_items_raw);
                $faq_count = count($faq_array);
                for ($i = 0; $i < $faq_count; $i += 2) {
                    if (isset($faq_array[$i]) && isset($faq_array[$i+1])) {
                        $question = trim($faq_array[$i]);
                        $answer = trim($faq_array[$i+1]);
                        if (!empty($question) && !empty($answer)) {
                            $faq_items[] = array(
                                'question' => $question,
                                'answer' => $answer
                            );
                        }
                    }
                }
            }
            
            // If no items, add one empty item
            if (empty($faq_items)) {
                $faq_items[] = array('question' => '', 'answer' => '');
            }
            ?>
            
            <div class="faq-items-wrapper" style="margin: 20px 0;">
                <h3 style="margin-bottom: 10px;">مدیریت سوالات متداول</h3>
                <p class="description" style="margin-bottom: 15px; color: #666;">می‌توانید سوالات متداول را اضافه، ویرایش یا حذف کنید. هر سوال باید شامل یک سوال و پاسخ کامل باشد.</p>
                <div id="faq-items-container">
                    <?php foreach ($faq_items as $index => $item): ?>
                    <div class="faq-item-row" style="background: #f9f9f9; padding: 20px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong style="color: #333;">سوال <?php echo $index + 1; ?></strong>
                            <button type="button" class="button button-small remove-faq-item" style="background: #dc3232; color: white; border-color: #dc3232;" <?php echo count($faq_items) <= 1 ? 'disabled' : ''; ?>>حذف</button>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #555;">سوال:</label>
                            <input type="text" name="faq_questions[]" value="<?php echo esc_attr($item['question']); ?>" class="regular-text" placeholder="مثال: چگونه می‌توانم محصولات شما را خریداری کنم؟" style="width: 100%; padding: 8px;" />
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #555;">پاسخ:</label>
                            <textarea name="faq_answers[]" rows="4" class="large-text" placeholder="مثال: برای خرید محصولات ما، کافی است به بخش فروشگاه مراجعه کنید..." style="width: 100%; padding: 8px;"><?php echo esc_textarea($item['answer']); ?></textarea>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-faq-item" class="button" style="margin-top: 10px;">+ افزودن سوال جدید</button>
                <input type="hidden" id="contact_faq_items" name="khane-irani-settings[contact_faq_items]" value="<?php echo esc_attr($faq_items_raw); ?>" />
            </div>
            
            <style>
                .faq-item-row {
                    transition: all 0.3s ease;
                }
                .faq-item-row:hover {
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .remove-faq-item:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
            </style>
            
            <script>
            jQuery(document).ready(function($) {
                var faqItemIndex = <?php echo count($faq_items); ?>;
                
                // Add new FAQ item
                $('#add-faq-item').on('click', function() {
                    var itemHtml = '<div class="faq-item-row" style="background: #f9f9f9; padding: 20px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; position: relative;">' +
                        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">' +
                        '<strong style="color: #333;">سوال ' + (faqItemIndex + 1) + '</strong>' +
                        '<button type="button" class="button button-small remove-faq-item" style="background: #dc3232; color: white; border-color: #dc3232;">حذف</button>' +
                        '</div>' +
                        '<div style="margin-bottom: 15px;">' +
                        '<label style="display: block; font-weight: 600; margin-bottom: 5px; color: #555;">سوال:</label>' +
                        '<input type="text" name="faq_questions[]" value="" class="regular-text" placeholder="مثال: چگونه می‌توانم محصولات شما را خریداری کنم؟" style="width: 100%; padding: 8px;" />' +
                        '</div>' +
                        '<div>' +
                        '<label style="display: block; font-weight: 600; margin-bottom: 5px; color: #555;">پاسخ:</label>' +
                        '<textarea name="faq_answers[]" rows="4" class="large-text" placeholder="مثال: برای خرید محصولات ما، کافی است به بخش فروشگاه مراجعه کنید..." style="width: 100%; padding: 8px;"></textarea>' +
                        '</div>' +
                        '</div>';
                    $('#faq-items-container').append(itemHtml);
                    faqItemIndex++;
                    updateFAQNumbers();
                    updateRemoveButtons();
                });
                
                // Remove FAQ item
                $(document).on('click', '.remove-faq-item', function() {
                    if ($('.faq-item-row').length > 1) {
                        $(this).closest('.faq-item-row').fadeOut(300, function() {
                            $(this).remove();
                            updateFAQNumbers();
                            updateRemoveButtons();
                        });
                    }
                });
                
                // Update FAQ numbers
                function updateFAQNumbers() {
                    $('.faq-item-row').each(function(index) {
                        $(this).find('strong').text('سوال ' + (index + 1));
                    });
                }
                
                // Update remove buttons state
                function updateRemoveButtons() {
                    var itemCount = $('.faq-item-row').length;
                    $('.remove-faq-item').prop('disabled', itemCount <= 1);
                }
                
                // Convert FAQ items to pipe-separated format before submit
                $('#zarin-settings-form').on('submit', function() {
                    var questions = [];
                    $('.faq-item-row').each(function() {
                        var question = $(this).find('input[name="faq_questions[]"]').val().trim();
                        var answer = $(this).find('textarea[name="faq_answers[]"]').val().trim();
                        if (question && answer) {
                            questions.push(question);
                            questions.push(answer);
                        }
                    });
                    $('#contact_faq_items').val(questions.join('|'));
                });
            });
            </script>
            
            <?php submit_button('ذخیره تنظیمات تماس'); ?>
            <input type="hidden" name="active_tab" value="contact" />
        </form>
        <?php
    }
}
