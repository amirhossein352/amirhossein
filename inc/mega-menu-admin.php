<?php
/**
 * Mega Menu Admin Interface
 * رابط کاربری آسان برای فعال‌سازی مگامنو
 * 
 * @package khane_irani
 */

if (!class_exists('Khane_Irani_Mega_Menu_Admin')) {
    class Khane_Irani_Mega_Menu_Admin {
        
        /**
         * Initialize
         */
        public function __construct() {
            add_action('wp_update_nav_menu_item', array($this, 'save_mega_menu_fields'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_action('admin_footer-nav-menus.php', array($this, 'add_mega_menu_fields_js'));
        }
        
        /**
         * Save mega menu fields
         */
        public function save_mega_menu_fields($menu_id, $menu_item_db_id) {
            // Save mega menu enabled
            if (isset($_POST['menu-item-mega-menu'][$menu_item_db_id])) {
                update_post_meta($menu_item_db_id, '_menu_item_mega_menu', '1');
                // Also add CSS class automatically
                $classes = get_post_meta($menu_item_db_id, '_menu_item_classes', true);
                if (empty($classes)) {
                    $classes = array();
                } else {
                    $classes = maybe_unserialize($classes);
                    if (!is_array($classes)) {
                        $classes = array();
                    }
                }
                if (!in_array('mega-menu', $classes)) {
                    $classes[] = 'mega-menu';
                }
                update_post_meta($menu_item_db_id, '_menu_item_classes', $classes);
            } else {
                delete_post_meta($menu_item_db_id, '_menu_item_mega_menu');
                // Remove CSS class
                $classes = get_post_meta($menu_item_db_id, '_menu_item_classes', true);
                if (!empty($classes)) {
                    $classes = maybe_unserialize($classes);
                    if (is_array($classes)) {
                        $classes = array_diff($classes, array('mega-menu'));
                        update_post_meta($menu_item_db_id, '_menu_item_classes', $classes);
                    }
                }
            }
            
            // Save columns
            if (isset($_POST['menu-item-mega-columns'][$menu_item_db_id])) {
                $columns = sanitize_text_field($_POST['menu-item-mega-columns'][$menu_item_db_id]);
                if (in_array($columns, array('2', '3', '4'))) {
                    update_post_meta($menu_item_db_id, '_menu_item_mega_columns', $columns);
                }
            } else {
                delete_post_meta($menu_item_db_id, '_menu_item_mega_columns');
            }
            
            // Save fullwidth
            if (isset($_POST['menu-item-mega-fullwidth'][$menu_item_db_id])) {
                update_post_meta($menu_item_db_id, '_menu_item_mega_fullwidth', '1');
            } else {
                delete_post_meta($menu_item_db_id, '_menu_item_mega_fullwidth');
            }
            
            // Save image
            if (isset($_POST['menu-item-mega-image'][$menu_item_db_id])) {
                $image_url = esc_url_raw($_POST['menu-item-mega-image'][$menu_item_db_id]);
                if (!empty($image_url)) {
                    update_post_meta($menu_item_db_id, '_menu_item_mega_image', $image_url);
                } else {
                    delete_post_meta($menu_item_db_id, '_menu_item_mega_image');
                }
            } else {
                delete_post_meta($menu_item_db_id, '_menu_item_mega_image');
            }
        }
        
        /**
         * Enqueue admin scripts
         */
        public function enqueue_admin_scripts($hook) {
            if ($hook !== 'nav-menus.php') {
                return;
            }
            wp_enqueue_script('jquery');
        }
        
        /**
         * Add mega menu fields using JavaScript
         */
        public function add_mega_menu_fields_js() {
            // Get all menu items with mega menu data
            $menu_items = wp_get_nav_menu_items(get_user_option('nav_menu_recently_edited'));
            $mega_menu_data = array();
            
            if ($menu_items) {
                foreach ($menu_items as $item) {
                    $mega_menu_data[$item->ID] = array(
                        'enabled' => get_post_meta($item->ID, '_menu_item_mega_menu', true) === '1',
                        'columns' => get_post_meta($item->ID, '_menu_item_mega_columns', true) ?: '3',
                        'fullwidth' => get_post_meta($item->ID, '_menu_item_mega_fullwidth', true) === '1',
                        'image' => get_post_meta($item->ID, '_menu_item_mega_image', true),
                    );
                }
            }
            ?>
            <style>
            .mega-menu-settings {
                margin: 15px 0 !important;
                padding: 15px !important;
                background: #f9f9f9 !important;
                border: 1px solid #ddd !important;
                border-radius: 5px !important;
                clear: both !important;
                animation: fadeIn 0.3s ease-in;
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .mega-menu-options {
                border-top: 1px solid #ddd;
                padding-top: 15px;
                margin-top: 15px;
            }
            .mega-menu-settings p {
                margin: 10px 0 !important;
            }
            .mega-menu-settings label {
                font-weight: normal !important;
            }
            .mega-menu-settings strong {
                color: #00A796 !important;
            }
            </style>
            
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                var megaMenuData = <?php echo json_encode($mega_menu_data); ?>;
                
                // Function to add mega menu fields to a menu item
                function addMegaMenuFields(itemId) {
                    var $item = $('#menu-item-' + itemId);
                    if ($item.length === 0 || $item.find('.mega-menu-settings').length > 0) {
                        return;
                    }
                    
                    var data = megaMenuData[itemId] || {
                        enabled: false,
                        columns: '3',
                        fullwidth: false,
                        image: ''
                    };
                    
                    var html = '<div class="mega-menu-settings">' +
                        '<p class="description" style="margin-bottom: 10px;">' +
                        '<strong style="color: #00A796; font-size: 14px;">🎯 تنظیمات مگامنو</strong>' +
                        '</p>' +
                        '<p>' +
                        '<label>' +
                        '<input type="checkbox" ' +
                        'name="menu-item-mega-menu[' + itemId + ']" ' +
                        'value="1" ' +
                        (data.enabled ? 'checked="checked" ' : '') +
                        'class="mega-menu-toggle" /> ' +
                        '<strong>فعال‌سازی مگامنو برای این آیتم</strong>' +
                        '</label>' +
                        '</p>' +
                        '<div class="mega-menu-options" style="' + (data.enabled ? '' : 'display: none;') + '">' +
                        '<p>' +
                        '<label>' +
                        '<strong>تعداد ستون‌ها:</strong><br>' +
                        '<select name="menu-item-mega-columns[' + itemId + ']" style="width: 100%; margin-top: 5px;">' +
                        '<option value="2" ' + (data.columns === '2' ? 'selected' : '') + '>2 ستون</option>' +
                        '<option value="3" ' + (data.columns === '3' ? 'selected' : '') + '>3 ستون (پیش‌فرض)</option>' +
                        '<option value="4" ' + (data.columns === '4' ? 'selected' : '') + '>4 ستون</option>' +
                        '</select>' +
                        '</label>' +
                        '</p>' +
                        '<p>' +
                        '<label>' +
                        '<input type="checkbox" ' +
                        'name="menu-item-mega-fullwidth[' + itemId + ']" ' +
                        'value="1" ' +
                        (data.fullwidth ? 'checked="checked" ' : '') +
                        '/> ' +
                        'نمایش Full Width (تمام عرض صفحه)' +
                        '</label>' +
                        '</p>' +
                        '<p>' +
                        '<label>' +
                        '<strong>تصویر مگامنو (اختیاری):</strong><br>' +
                        '<input type="url" ' +
                        'name="menu-item-mega-image[' + itemId + ']" ' +
                        'value="' + (data.image || '') + '" ' +
                        'placeholder="https://example.com/image.jpg" ' +
                        'style="width: 100%; margin-top: 5px;" />' +
                        '<span class="description">URL تصویری که در مگامنو نمایش داده می‌شود</span>' +
                        '</label>' +
                        '</p>' +
                        (data.image ? '<p><img src="' + data.image + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 3px; margin-top: 5px;" onerror="this.style.display=\'none\';" /></p>' : '') +
                        '<p class="description" style="margin-top: 10px; padding: 10px; background: #fff; border-right: 3px solid #00A796; font-size: 12px;">' +
                        '<strong>💡 نکته:</strong> برای اینکه مگامنو کار کند، این آیتم باید زیرمنو داشته باشد. ' +
                        'زیرمنوها به صورت خودکار در ستون‌های مگامنو نمایش داده می‌شوند.' +
                        '</p>' +
                        '</div>' +
                        '</div>';
                    
                    // Insert before the field-move paragraph
                    var $fieldMove = $item.find('p.field-move');
                    if ($fieldMove.length > 0) {
                        $fieldMove.before(html);
                    } else {
                        $item.find('.menu-item-settings').append(html);
                    }
                }
                
                // Add fields to existing items
                $('.menu-item').each(function() {
                    var itemId = $(this).attr('id').replace('menu-item-', '');
                    if (itemId) {
                        addMegaMenuFields(itemId);
                    }
                });
                
                // Add fields when new items are added (multiple methods for compatibility)
                $(document).on('menu-item-added', function(e, item) {
                    var itemId = $(item).attr('id').replace('menu-item-', '');
                    if (itemId) {
                        setTimeout(function() {
                            addMegaMenuFields(itemId);
                        }, 100);
                    }
                });
                
                // Also watch for AJAX completion
                $(document).ajaxComplete(function(event, xhr, settings) {
                    if (settings.data && settings.data.indexOf('action=add-menu-item') !== -1) {
                        setTimeout(function() {
                            $('.menu-item').each(function() {
                                var itemId = $(this).attr('id');
                                if (itemId && $(this).find('.mega-menu-settings').length === 0) {
                                    itemId = itemId.replace('menu-item-', '');
                                    addMegaMenuFields(itemId);
                                }
                            });
                        }, 300);
                    }
                });
                
                // Watch for item expansion
                $('#nav-menu-theme-locations, #nav-menu-theme-locations + .tabs-panel-active').on('click', '.item-edit', function() {
                    var $item = $(this).closest('.menu-item');
                    var itemId = $item.attr('id').replace('menu-item-', '');
                    if (itemId && $item.find('.mega-menu-settings').length === 0) {
                        setTimeout(function() {
                            addMegaMenuFields(itemId);
                        }, 100);
                    }
                });
                
                // Toggle options
                $(document).on('change', '.mega-menu-toggle', function() {
                    var $options = $(this).closest('.mega-menu-settings').find('.mega-menu-options');
                    if ($(this).is(':checked')) {
                        $options.slideDown(200);
                    } else {
                        $options.slideUp(200);
                    }
                });
                
                // Update image preview
                $(document).on('input', 'input[name^="menu-item-mega-image"]', function() {
                    var $img = $(this).closest('.mega-menu-settings').find('img');
                    var url = $(this).val();
                    if (url && $img.length === 0) {
                        $(this).closest('p').after('<p><img src="' + url + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 3px; margin-top: 5px;" onerror="this.style.display=\'none\';" /></p>');
                    } else if ($img.length > 0) {
                        $img.attr('src', url);
                    }
                });
            });
            </script>
            <?php
        }
    }
    
    // Initialize
    new Khane_Irani_Mega_Menu_Admin();
}

