<?php
/**
 * Base Settings Tab Class
 * 
 * @package Khane_Irani
 * @author Ali Ilkhani
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Base class for all settings tabs
 */
abstract class Khane_Irani_Settings_Tab {
    
    protected $tab_key;
    protected $tab_title;
    protected $settings_group;
    protected $settings_section;
    
    public function __construct($tab_key, $tab_title) {
        $this->tab_key = $tab_key;
        $this->tab_title = $tab_title;
        $this->settings_group = 'khane_irani_' . $tab_key . '_settings';
        $this->settings_section = 'khane_irani_' . $tab_key . '_section';
    }
    
    public function get_tab_title() {
        return $this->tab_title;
    }
    
    public function init_settings() {
        // Use unified settings group for all tabs - register only once
        static $registered = false;
        if (!$registered) {
            register_setting(
                'khane-irani-settings', 
                'khane-irani-settings',
                array(
                    'sanitize_callback' => array($this, 'sanitize_settings')
                )
            );
            $registered = true;
        }
        add_settings_section($this->settings_section, '', array($this, 'render_section_callback'), 'khane-irani-settings');
        $this->add_settings_fields();
    }
    
    /**
     * Sanitize settings before saving
     */
    public function sanitize_settings($input) {
        if (!is_array($input)) { return array(); }

        $sanitize_recursive = function ($value, $key = '') use (&$sanitize_recursive) {
            // Booleans / checkboxes
            $checkbox_like = preg_match('/^(is_|show_|enable|enabled|checked)/i', $key) || in_array($key, array('is_active','is_featured'), true);
            if ($checkbox_like) {
                return (isset($value) && ($value === '1' || $value === 1 || $value === true)) ? '1' : '0';
            }

            // Numeric common fields
            if (preg_match('/(width|height|size|count|number|quantity|sort|order|index|id)$/i', $key)) {
                return is_numeric($value) ? intval($value) : 0;
            }

            // Color
            if (strpos($key, 'color') !== false) {
                return is_string($value) ? sanitize_hex_color($value) : '';
            }

            // URL
            if (strpos($key, 'url') !== false || strpos($key, 'link') !== false) {
                return is_string($value) ? esc_url_raw($value) : '';
            }

            // Email
            if (strpos($key, 'email') !== false) {
                return is_string($value) ? sanitize_email($value) : '';
            }

            // Image / attachment id
            if (strpos($key, 'image') !== false || preg_match('/(_id|^id)$/i', $key)) {
                return is_numeric($value) ? absint($value) : 0;
            }

            // HTML-allowed fields (descriptions, answers, maps)
            if (preg_match('/(description|answer|content|map|html)/i', $key)) {
                return is_string($value) ? wp_kses_post($value) : '';
            }
            
            // Note: values_items is now handled as a regular array in the Arrays section below
            // No special conversion to pipe-separated string needed

            // Arrays: recurse (check this BEFORE the items/features/list check)
            // This ensures banners_items, faq_items, etc. are processed as arrays
            if (is_array($value)) {
                // Special arrays that should be preserved as arrays
                $array_keys = array('banners_items', 'faq_items', 'values_items', 'custom_product_carousels');
                if (in_array($key, $array_keys, true)) {
                    $out = array();
                    foreach ($value as $k => $v) {
                        $out[$k] = $sanitize_recursive($v, is_string($k) ? $k : '');
                    }
                    return $out;
                }
                
                // For other arrays, recurse normally
                $out = array();
                foreach ($value as $k => $v) {
                    $out[$k] = $sanitize_recursive($v, is_string($k) ? $k : '');
                }
                return $out;
            }

            // Textarea fields that contain pipe-separated values (convert arrays to string)
            // Only apply this to non-array values and exclude special array keys
            $array_keys = array('banners_items', 'faq_items', 'values_items', 'custom_product_carousels');
            if (!in_array($key, $array_keys, true) && preg_match('/(items|features|list)/i', $key)) {
                if (is_array($value)) {
                    // If it's an array, convert to empty string (shouldn't happen, but handle it)
                    return '';
                }
                // Use sanitize_text_field for pipe-separated values
                return is_string($value) ? sanitize_text_field($value) : '';
            }

            // Default string sanitization
            if (is_string($value)) {
                return sanitize_text_field($value);
            }

            return $value;
        };

        $sanitized = array();
        foreach ($input as $key => $value) {
            $sanitized[$key] = $sanitize_recursive($value, $key);
        }

        // Clean up banners_items - remove empty banners and re-index
        if (isset($sanitized['banners_items']) && is_array($sanitized['banners_items'])) {
            $banners = $sanitized['banners_items'];

            // Filter out empty banners (must have image to be valid)
            $banners = array_filter($banners, function($banner) {
                if (!is_array($banner)) {
                    return false;
                }
                // Banner must have a valid image ID to be kept
                $image_id = isset($banner['image']) ? intval($banner['image']) : 0;
                if ($image_id <= 0) {
                    return false;
                }
                // Verify attachment exists
                $attachment = get_post($image_id);
                if (!$attachment || $attachment->post_type !== 'attachment') {
                    return false;
                }
                return true;
            });

            // Re-index array to remove gaps
            $sanitized['banners_items'] = array_values($banners);
        }

        // Clean up faq_items - ensure it's a proper array
        if (isset($sanitized['faq_items']) && is_array($sanitized['faq_items'])) {
            $faq_items = $sanitized['faq_items'];
            
            // Filter out completely empty FAQ items
            $faq_items = array_filter($faq_items, function($item) {
                if (!is_array($item)) {
                    return false;
                }
                // Keep if it has at least question or answer
                $has_question = !empty($item['question']);
                $has_answer = !empty($item['answer']);
                return $has_question || $has_answer;
            });
            
            // Re-index array to remove gaps
            $sanitized['faq_items'] = array_values($faq_items);
        }

        return $sanitized;
    }
    
    abstract protected function add_settings_fields();
    abstract public function render_tab_content();
    
    public function render_section_callback() {
        // Override in child classes if needed
    }
    
    protected function get_option($key, $default = '') {
        $options = get_option('khane-irani-settings', array());
        return isset($options[$key]) ? $options[$key] : $default;
    }
    
    public function render_field($args) {
        $field_type = $args['type'];
        $field_name = 'khane-irani-settings[' . $args['id'] . ']';
        $field_value = $this->get_option($args['id'], $args['default']);
        $field_id = 'khane-irani-settings_' . $args['id'];
        
        // Convert array to string if needed
        if (is_array($field_value)) {
            $field_value = '';
        }
        
        // Ensure field_value is a string
        $field_value = (string) $field_value;
        
        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($field_id) . '">' . esc_html($args['label']) . '</label></th>';
        echo '<td>';
        
        switch ($field_type) {
            case 'text':
                echo '<input type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" class="regular-text" />';
                break;
                
            case 'textarea':
                echo '<textarea id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" rows="5" cols="50" class="large-text">' . esc_textarea($field_value) . '</textarea>';
                break;
                
            case 'checkbox':
                // For checkbox, use a hidden input with value 0, then checkbox with value 1
                echo '<input type="hidden" name="' . esc_attr($field_name) . '" value="0" />';
                echo '<input type="checkbox" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="1" ' . checked($field_value, '1', false) . ' />';
                echo '<label for="' . esc_attr($field_id) . '">' . esc_html($args['description']) . '</label>';
                break;
                
            case 'select':
                echo '<select id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '">';
                foreach ($args['options'] as $value => $label) {
                    echo '<option value="' . esc_attr($value) . '" ' . selected($field_value, $value, false) . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';
                break;
                
            case 'image':
                echo '<div class="image-upload-wrapper">';
                echo '<input type="hidden" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" />';
                echo '<div class="image-preview">';
                if ($field_value) {
                    echo wp_get_attachment_image($field_value, 'thumbnail');
                }
                echo '</div>';
                echo '<button type="button" class="button upload-image-button">انتخاب تصویر</button>';
                echo '<button type="button" class="button remove-image-button" style="display: ' . ($field_value ? 'inline-block' : 'none') . '">حذف تصویر</button>';
                echo '</div>';
                break;
                
            case 'color':
                echo '<input type="color" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" />';
                break;
        }
        
        if (isset($args['description']) && $field_type !== 'checkbox') {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
        
        echo '</td>';
        echo '</tr>';
    }
}
