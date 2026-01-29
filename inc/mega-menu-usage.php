<?php
/**
 * Mega Menu Usage Examples
 * 
 * این فایل مثال‌های استفاده از مگامنو را نشان می‌دهد
 * برای فعال کردن مگامنو، کدهای زیر را در functions.php یا یک فایل جدا اضافه کنید
 * 
 * @package khane_irani
 */

/**
 * مثال 1: مگامنو ساده با 3 ستون
 * 
 * برای استفاده: ID آیتم منو را پیدا کنید و در کد زیر قرار دهید
 */
function khane_irani_setup_mega_menu_example_1() {
    // ID آیتم منو را از Appearance > Menus پیدا کنید
    $menu_item_id = 123; // این عدد را با ID واقعی آیتم منو جایگزین کنید
    
    Khane_Irani_Mega_Menu::apply_to_item($menu_item_id, function() use ($menu_item_id) {
        return khane_irani_mega_menu($menu_item_id)
            ->set_columns(3) // 3 ستون
            ->add_column('محصولات', array(
                array(
                    'title' => 'مبلمان',
                    'url' => home_url('/products/furniture'),
                    'icon' => '🪑',
                ),
                array(
                    'title' => 'دکوراسیون',
                    'url' => home_url('/products/decoration'),
                    'icon' => '🎨',
                ),
                array(
                    'title' => 'نورپردازی',
                    'url' => home_url('/products/lighting'),
                    'icon' => '💡',
                ),
            ))
            ->add_column('خدمات', array(
                array(
                    'title' => 'طراحی داخلی',
                    'url' => home_url('/services/interior-design'),
                    'icon' => '🏠',
                ),
                array(
                    'title' => 'مشاوره رایگان',
                    'url' => home_url('/services/consultation'),
                    'icon' => '💬',
                ),
            ))
            ->add_column('درباره ما', array(
                array(
                    'title' => 'تیم ما',
                    'url' => home_url('/about/team'),
                    'icon' => '👥',
                ),
                array(
                    'title' => 'تماس با ما',
                    'url' => home_url('/contact'),
                    'icon' => '📞',
                ),
            ));
    });
}
// فعال کردن: کامنت زیر را بردارید
// add_action('init', 'khane_irani_setup_mega_menu_example_1');

/**
 * مثال 2: مگامنو با تصویر
 */
function khane_irani_setup_mega_menu_example_2() {
    $menu_item_id = 124; // ID آیتم منو
    
    Khane_Irani_Mega_Menu::apply_to_item($menu_item_id, function() use ($menu_item_id) {
        return khane_irani_mega_menu($menu_item_id)
            ->set_columns(2)
            ->add_image(
                get_template_directory_uri() . '/images/banner/mega-menu-image.jpg',
                'تصویر مگامنو'
            )
            ->add_column('دسته‌بندی 1', array(
                array('title' => 'آیتم 1', 'url' => '#', 'icon' => '🔗'),
                array('title' => 'آیتم 2', 'url' => '#', 'icon' => '🔗'),
            ))
            ->add_column('دسته‌بندی 2', array(
                array('title' => 'آیتم 3', 'url' => '#', 'icon' => '🔗'),
                array('title' => 'آیتم 4', 'url' => '#', 'icon' => '🔗'),
            ));
    });
}
// add_action('init', 'khane_irani_setup_mega_menu_example_2');

/**
 * مثال 3: مگامنو Full Width
 */
function khane_irani_setup_mega_menu_example_3() {
    $menu_item_id = 125;
    
    Khane_Irani_Mega_Menu::apply_to_item($menu_item_id, function() use ($menu_item_id) {
        return khane_irani_mega_menu($menu_item_id)
            ->set_full_width(true)
            ->set_columns(4)
            ->add_column('ستون 1', array(
                array('title' => 'آیتم 1', 'url' => '#'),
                array('title' => 'آیتم 2', 'url' => '#'),
            ))
            ->add_column('ستون 2', array(
                array('title' => 'آیتم 3', 'url' => '#'),
                array('title' => 'آیتم 4', 'url' => '#'),
            ))
            ->add_column('ستون 3', array(
                array('title' => 'آیتم 5', 'url' => '#'),
                array('title' => 'آیتم 6', 'url' => '#'),
            ))
            ->add_column('ستون 4', array(
                array('title' => 'آیتم 7', 'url' => '#'),
                array('title' => 'آیتم 8', 'url' => '#'),
            ));
    });
}
// add_action('init', 'khane_irani_setup_mega_menu_example_3');

/**
 * مثال 4: مگامنو با محتوای HTML
 */
function khane_irani_setup_mega_menu_example_4() {
    $menu_item_id = 126;
    
    Khane_Irani_Mega_Menu::apply_to_item($menu_item_id, function() use ($menu_item_id) {
        return khane_irani_mega_menu($menu_item_id)
            ->set_columns(2)
            ->add_column('محصولات', array(
                array('title' => 'مبلمان', 'url' => '#'),
                array('title' => 'دکوراسیون', 'url' => '#'),
            ))
            ->add_content(
                'پیشنهاد ویژه',
                '<p>خرید بالای 5 میلیون تومان، <strong>20% تخفیف</strong> دریافت کنید!</p><a href="#" class="btn">مشاهده</a>',
                'after'
            );
    });
}
// add_action('init', 'khane_irani_setup_mega_menu_example_4');

/**
 * راهنمای سریع:
 * 
 * 1. به Appearance > Menus بروید
 * 2. آیتم منویی که می‌خواهید مگامنو شود را پیدا کنید
 * 3. روی "Screen Options" کلیک کنید و "CSS Classes" را فعال کنید
 * 4. به آیتم منو کلاس "mega-menu" اضافه کنید
 * 5. یکی از مثال‌های بالا را کپی کنید و در functions.php قرار دهید
 * 6. ID آیتم منو را پیدا کنید و در کد جایگزین کنید
 * 7. کامنت add_action را بردارید تا فعال شود
 * 
 * یا از طریق کد:
 * 
 * $menu_item_id = 123; // ID آیتم منو
 * 
 * Khane_Irani_Mega_Menu::apply_to_item($menu_item_id, function() use ($menu_item_id) {
 *     return khane_irani_mega_menu($menu_item_id)
 *         ->set_columns(3)
 *         ->add_column('عنوان ستون', array(
 *             array('title' => 'آیتم 1', 'url' => '#', 'icon' => '🔗'),
 *             array('title' => 'آیتم 2', 'url' => '#', 'icon' => '🔗'),
 *         ));
 * });
 */

