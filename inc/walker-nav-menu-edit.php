<?php
/**
 * Custom Walker for Nav Menu Edit
 * Extends the default walker to add custom fields
 * 
 * @package khane_irani
 */

if (!class_exists('Walker_Nav_Menu_Edit_Custom')) {
    require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
    
    class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu_Edit {
        
        /**
         * Start the element output.
         */
        function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            $item_output = '';
            parent::start_el($item_output, $item, $depth, $args, $id);
            
            // Add our custom fields before the closing </div>
            $custom_fields = $this->get_custom_fields($item);
            $item_output = preg_replace(
                '/(?=<p[^>]+class="[^"]*field-move[^"]*"[^>]*>)/',
                $custom_fields,
                $item_output
            );
            
            $output .= $item_output;
        }
        
        /**
         * Get custom fields HTML
         */
        private function get_custom_fields($item) {
            $item_id = esc_attr($item->ID);
            $is_mega_menu = get_post_meta($item->ID, '_menu_item_mega_menu', true) === '1';
            $mega_columns = get_post_meta($item->ID, '_menu_item_mega_columns', true) ?: '3';
            $mega_fullwidth = get_post_meta($item->ID, '_menu_item_mega_fullwidth', true) === '1';
            $mega_image = get_post_meta($item->ID, '_menu_item_mega_image', true);
            
            ob_start();
            ?>
            <div class="mega-menu-settings" style="margin: 10px 0; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; clear: both;">
                <p class="description" style="margin-bottom: 10px; font-weight: bold; color: #00A796;">
                    🎯 تنظیمات مگامنو
                </p>
                
                <p>
                    <label>
                        <input type="checkbox" 
                               name="menu-item-mega-menu[<?php echo $item_id; ?>]" 
                               value="1" 
                               <?php checked($is_mega_menu, true); ?>
                               class="mega-menu-toggle" />
                        <strong>فعال‌سازی مگامنو برای این آیتم</strong>
                    </label>
                </p>
                
                <div class="mega-menu-options" style="margin-top: 15px; <?php echo $is_mega_menu ? '' : 'display: none;'; ?>">
                    <p>
                        <label>
                            <strong>تعداد ستون‌ها:</strong><br>
                            <select name="menu-item-mega-columns[<?php echo $item_id; ?>]" style="width: 100%; margin-top: 5px;">
                                <option value="2" <?php selected($mega_columns, '2'); ?>>2 ستون</option>
                                <option value="3" <?php selected($mega_columns, '3'); ?>>3 ستون (پیش‌فرض)</option>
                                <option value="4" <?php selected($mega_columns, '4'); ?>>4 ستون</option>
                            </select>
                        </label>
                    </p>
                    
                    <p>
                        <label>
                            <input type="checkbox" 
                                   name="menu-item-mega-fullwidth[<?php echo $item_id; ?>]" 
                                   value="1" 
                                   <?php checked($mega_fullwidth, true); ?> />
                            نمایش Full Width (تمام عرض صفحه)
                        </label>
                    </p>
                    
                    <p>
                        <label>
                            <strong>تصویر مگامنو (اختیاری):</strong><br>
                            <input type="url" 
                                   name="menu-item-mega-image[<?php echo $item_id; ?>]" 
                                   value="<?php echo esc_attr($mega_image); ?>" 
                                   placeholder="https://example.com/image.jpg"
                                   style="width: 100%; margin-top: 5px;" />
                            <span class="description">URL تصویری که در مگامنو نمایش داده می‌شود</span>
                        </label>
                    </p>
                    
                    <?php if (!empty($mega_image)) : ?>
                        <p>
                            <img src="<?php echo esc_url($mega_image); ?>" 
                                 style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 3px; margin-top: 5px;" 
                                 onerror="this.style.display='none';" />
                        </p>
                    <?php endif; ?>
                    
                    <p class="description" style="margin-top: 10px; padding: 10px; background: #fff; border-right: 3px solid #00A796; font-size: 12px;">
                        <strong>💡 نکته:</strong> برای اینکه مگامنو کار کند، این آیتم باید زیرمنو داشته باشد. 
                        زیرمنوها به صورت خودکار در ستون‌های مگامنو نمایش داده می‌شوند.
                    </p>
                </div>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $('.mega-menu-toggle').on('change', function() {
                    var options = $(this).closest('.mega-menu-settings').find('.mega-menu-options');
                    if ($(this).is(':checked')) {
                        options.slideDown(200);
                    } else {
                        options.slideUp(200);
                    }
                });
            });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}

