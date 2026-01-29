<?php
/**
 * Template Name: تماس با ما
 * Template for Contact Us page
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title"><?php echo esc_html(khane_irani_get_contact_setting('contact_title', 'تماس با ما')); ?></h1>
            <p class="page-description"><?php echo esc_html(khane_irani_get_contact_setting('contact_subtitle', 'برای خرید محصولات و یا دریافت مشاوره با ما تماس بگیرید')); ?></p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-content">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>اطلاعات تماس</h2>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>آدرس</h3>
                            <p><?php echo esc_html(khane_irani_get_contact_setting('contact_address', 'تهران، ایران')); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h3>تلفن</h3>
                            <p><?php echo esc_html(khane_irani_get_contact_setting('contact_phone', '09903491529')); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>ایمیل</h3>
                            <p><?php echo esc_html(khane_irani_get_contact_setting('contact_email', 'info@khane-irani.com')); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h3>ساعات کاری</h3>
                            <p><?php echo esc_html(khane_irani_get_contact_setting('contact_working_hours', 'شنبه تا چهارشنبه: 9 صبح تا 6 عصر')); ?></p>
                        </div>
                    </div>
                    
                    <?php if (khane_irani_get_footer_setting('show_social_links', '1') == '1'): ?>
                    <div class="social-links">
                        <h3>شبکه‌های اجتماعی</h3>
                        <div class="social-icons">
                            <?php if (khane_irani_get_footer_setting('telegram_url')): ?>
                            <a href="<?php echo esc_url(khane_irani_get_footer_setting('telegram_url')); ?>" class="social-icon" target="_blank"><i class="fab fa-telegram"></i></a>
                            <?php endif; ?>
                            <?php if (khane_irani_get_footer_setting('instagram_url')): ?>
                            <a href="<?php echo esc_url(khane_irani_get_footer_setting('instagram_url')); ?>" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if (khane_irani_get_footer_setting('facebook_url')): ?>
                            <a href="<?php echo esc_url(khane_irani_get_footer_setting('facebook_url')); ?>" class="social-icon" target="_blank"><i class="fab fa-facebook"></i></a>
                            <?php endif; ?>
                            <?php if (khane_irani_get_footer_setting('whatsapp_url')): ?>
                            <a href="https://wa.me/<?php echo esc_attr(khane_irani_get_footer_setting('whatsapp_url')); ?>" class="social-icon" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="contact-form contact-form-sticky">
                    <h2>فرم تماس</h2>
                    <form class="contact-form" action="" method="post">
                        <div class="form-group">
                            <label for="name">نام و نام خانوادگی *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">ایمیل *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">شماره تماس</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">موضوع *</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">پیام *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">ارسال پیام</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <?php 
    $contact_map = khane_irani_get_contact_setting('contact_map');
    if ($contact_map): 
    ?>
    <section class="map-section">
        <div class="container">
            <h2 class="section-title"><?php echo esc_html(khane_irani_get_contact_setting('contact_map_title', 'موقعیت ما')); ?></h2>
            <div class="map-container">
                <?php echo wp_kses_post($contact_map); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FAQ Section -->
    <?php 
    $faq_items = khane_irani_get_contact_setting('contact_faq_items', 'چگونه می‌توانم محصولات شما را خریداری کنم؟|برای خرید محصولات ما، کافی است به بخش فروشگاه مراجعه کنید، محصول مورد نظر را انتخاب کرده و به سبد خرید اضافه کنید. سپس مراحل پرداخت را تکمیل کنید. ما انواع محصولات فیزیکی از جمله نوشت‌افزار، لباس کودک و اسباب بازی را عرضه می‌کنیم.|هزینه ارسال چقدر است؟|هزینه ارسال بسته به مکان مقصد و وزن محصول متفاوت است. برای اطلاع از هزینه دقیق ارسال، می‌توانید در صفحه محصول و یا هنگام تکمیل سفارش مشاهده کنید.|آیا محصولات شما اصل و با کیفیت هستند؟|بله، تمام محصولات عرضه شده در خانه ایرانی اصل و دارای گارانتی اصالت هستند. ما فقط کالاهای با کیفیت و اصل را به مشتریان عرضه می‌کنیم.|آیا امکان برگشت کالا وجود دارد؟|بله، در صورت وجود مشکل در محصول خریداری شده، می‌توانید ظرف مدت مشخص شده کالا را برگشت دهید. لطفاً برای جزئیات بیشتر با پشتیبانی تماس بگیرید.');
    if (!empty($faq_items)) {
    ?>
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title"><?php echo esc_html(khane_irani_get_contact_setting('contact_faq_title', 'سوالات متداول')); ?></h2>
            <div class="faq-list">
                <?php 
                $faq_array = explode('|', $faq_items);
                $faq_count = count($faq_array);
                for ($i = 0; $i < $faq_count; $i += 2) {
                    if (isset($faq_array[$i]) && isset($faq_array[$i+1])) {
                        $question = trim($faq_array[$i]);
                        $answer = trim($faq_array[$i+1]);
                        if (!empty($question) && !empty($answer)) {
                            echo '<div class="faq-item">';
                            echo '<h3 class="faq-question">' . esc_html($question) . '</h3>';
                            echo '<div class="faq-answer">';
                            echo '<p>' . esc_html($answer) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </section>
    <?php } ?>

</main>

<?php get_footer(); ?>
