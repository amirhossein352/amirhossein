<?php
/**
 * Template Name: درباره ما
 * Template for About Us page
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */

get_header(); ?>

<main id="main" class="site-main">
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title"><?php echo esc_html(khane_irani_get_about_setting('about_title', 'درباره خانه ایرانی')); ?></h1>
            <p class="page-description"><?php echo esc_html(khane_irani_get_about_setting('about_description', 'فروشگاه اینترنتی خانه ایرانی، عرضه‌کننده محصولات فیزیکی با کیفیت و قیمت مناسب')); ?></p>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title"><?php echo esc_html(khane_irani_get_about_setting('values_title', 'ارزش‌های ما')); ?></h2>
            <div class="values-grid">
                <?php
                $values_items = khane_irani_get_about_setting('values_items', '🛍️|کیفیت برتر|فقط محصولات با کیفیت و اصل را در فروشگاه خود عرضه می‌کنیم.|💰|قیمت مناسب|بهترین قیمت‌ها را با حفظ کیفیت برای مشتریان عزیز فراهم کرده‌ایم.|🚚|ارسال سریع|ارسال سریع و مطمئن محصولات به سراسر کشور با بسته‌بندی مناسب.|❤️|رضایت مشتری|رضایت و اعتماد شما مهم‌ترین اولویت ماست.');


                // Process values_items - handle both array and string formats
                $processed_values = array();
                if (!empty($values_items)) {
                    if (is_array($values_items)) {
                        // New format: already an array
                        foreach ($values_items as $item) {
                            if (is_array($item) &&
                                isset($item['emoji']) &&
                                isset($item['title']) &&
                                isset($item['description'])) {
                                $processed_values[] = array(
                                    'emoji' => $item['emoji'],
                                    'title' => $item['title'],
                                    'description' => $item['description']
                                );
                            }
                        }
                    } else {
                        // Old format: pipe-separated string
                        $values_array = explode('|', $values_items);
                        $values_count = count($values_array);
                        for ($i = 0; $i < $values_count; $i += 3) {
                            if (isset($values_array[$i]) && isset($values_array[$i+1]) && isset($values_array[$i+2])) {
                                $processed_values[] = array(
                                    'emoji' => trim($values_array[$i]),
                                    'title' => trim($values_array[$i+1]),
                                    'description' => trim($values_array[$i+2])
                                );
                            }
                        }
                    }
                }


                // If no processed values, use defaults
                if (empty($processed_values)) {
                    $processed_values = array(
                        array('emoji' => '🛍️', 'title' => 'کیفیت برتر', 'description' => 'فقط محصولات با کیفیت و اصل را در فروشگاه خود عرضه می‌کنیم.'),
                        array('emoji' => '💰', 'title' => 'قیمت مناسب', 'description' => 'بهترین قیمت‌ها را با حفظ کیفیت برای مشتریان عزیز فراهم کرده‌ایم.'),
                        array('emoji' => '🚚', 'title' => 'ارسال سریع', 'description' => 'ارسال سریع و مطمئن محصولات به سراسر کشور با بسته‌بندی مناسب.'),
                        array('emoji' => '❤️', 'title' => 'رضایت مشتری', 'description' => 'رضایت و اعتماد شما مهم‌ترین اولویت ماست.')
                    );
                }

                // Display values
                foreach ($processed_values as $value) {
                    $emoji = isset($value['emoji']) ? trim($value['emoji']) : '';
                    $title = isset($value['title']) ? trim($value['title']) : '';
                    $description = isset($value['description']) ? trim($value['description']) : '';

                    if (!empty($title)) {
                        echo '<div class="value-card">';
                        if (!empty($emoji)) {
                            echo '<div class="value-emoji">' . esc_html($emoji) . '</div>';
                        }
                        echo '<h4>' . esc_html($title) . '</h4>';
                        if (!empty($description)) {
                            echo '<p>' . esc_html($description) . '</p>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-content">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2><?php echo esc_html(khane_irani_get_about_setting('about_content_title', 'خانه ایرانی، فروشگاه اینترنتی محصولات فیزیکی')); ?></h2>
                    <p><?php echo esc_html(khane_irani_get_about_setting('about_content_text', 'خانه ایرانی یک فروشگاه اینترنتی معتبر است که با هدف ارائه محصولات با کیفیت و قیمت مناسب برای خانواده‌های ایرانی راه‌اندازی شده است. ما در زمینه عرضه انواع محصولات فیزیکی از جمله نوشت‌افزار، لباس کودک و اسباب بازی فعالیت می‌کنیم.')); ?></p>
                    
                    <?php 
                    $features_title = khane_irani_get_about_setting('about_features_title', 'محصولات ما');
                    if (!empty($features_title)) {
                        echo '<h3>' . esc_html($features_title) . '</h3>';
                    }
                    ?>
                    <ul>
                        <?php 
                        $features = khane_irani_get_about_setting('about_features', 'نوشت‌افزار با کیفیت|لباس کودک و نوجوان|اسباب بازی و سرگرمی|محصولات آموزشی|لوازم تحریر مدرسه');
                        $features_array = explode('|', $features);
                        foreach ($features_array as $feature) {
                            if (!empty(trim($feature))) {
                                echo '<li>' . esc_html(trim($feature)) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                    
                    <?php 
                    $why_title = khane_irani_get_about_setting('about_why_title', 'چرا خانه ایرانی؟');
                    if (!empty($why_title)) {
                        echo '<h3>' . esc_html($why_title) . '</h3>';
                    }
                    ?>
                    <p><?php echo esc_html(khane_irani_get_about_setting('about_why_text', 'ما با سال‌ها تجربه در زمینه فروش محصولات فیزیکی، فقط کالاهای اصل و با کیفیت را عرضه می‌کنیم. تمام محصولات ما دارای گارانتی اصالت بوده و با بهترین قیمت‌ها و ارسال سریع در دسترس شما قرار می‌گیرند. هدف ما رضایت شماست.')); ?></p>
                </div>
                
                <div class="about-image">
                    <?php 
                    $about_image = khane_irani_get_about_setting('about_image');
                    if ($about_image) {
                        echo wp_get_attachment_image($about_image, 'large', false, array('alt' => 'درباره خانه ایرانی'));
                    } else {
                        echo '<img src="' . get_template_directory_uri() . '/images/banner-tarahi-site-.png" alt="درباره خانه ایرانی" />';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <?php 
    $stats_items = khane_irani_get_about_setting('stats_items', '1000+|مشتری راضی|500+|محصول متنوع|3+|دسته‌بندی اصلی|98%|رضایت مشتری');
    if (!empty($stats_items)) {
    ?>
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <?php 
                $stats_array = explode('|', $stats_items);
                $stats_count = count($stats_array);
                for ($i = 0; $i < $stats_count; $i += 2) {
                    if (isset($stats_array[$i]) && isset($stats_array[$i+1])) {
                        $number = trim($stats_array[$i]);
                        $label = trim($stats_array[$i+1]);
                        if (!empty($number) && !empty($label)) {
                            echo '<div class="stat-item">';
                            echo '<div class="stat-number">' . esc_html($number) . '</div>';
                            echo '<div class="stat-label">' . esc_html($label) . '</div>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- Contact CTA -->
    <?php 
    $cta_title = khane_irani_get_about_setting('about_cta_title', 'آماده خرید هستید؟');
    if (!empty($cta_title)) {
    ?>
    <section class="contact-cta">
        <div class="container">
            <div class="cta-content">
                <h2><?php echo esc_html($cta_title); ?></h2>
                <p><?php echo esc_html(khane_irani_get_about_setting('about_cta_text', 'برای خرید محصولات و یا دریافت مشاوره با ما تماس بگیرید')); ?></p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary"><?php echo esc_html(khane_irani_get_about_setting('about_cta_button_text', 'تماس با ما')); ?></a>
            </div>
        </div>
    </section>
    <?php } ?>

</main>

<?php get_footer(); ?>
