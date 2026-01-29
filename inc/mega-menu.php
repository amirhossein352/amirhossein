<?php
/**
 * Mega Menu Class
 * Easy-to-use class for creating mega menus
 * 
 * @package khane_irani
 */

if (!class_exists('Khane_Irani_Mega_Menu')) {
    class Khane_Irani_Mega_Menu {
        
        /**
         * Menu item ID
         */
        private $item_id;
        
        /**
         * Menu columns
         */
        private $columns = array();
        
        /**
         * Menu settings
         */
        private $settings = array(
            'columns_count' => 3,
            'full_width' => false,
            'has_image' => false,
            'image_url' => '',
            'image_alt' => '',
            'custom_class' => '',
        );
        
        /**
         * Constructor
         * 
         * @param int $item_id Menu item ID
         * @param array $settings Menu settings
         */
        public function __construct($item_id, $settings = array()) {
            $this->item_id = $item_id;
            $this->settings = wp_parse_args($settings, $this->settings);
        }
        
        /**
         * Set number of columns
         * 
         * @param int $count Number of columns (2, 3, or 4)
         * @return $this
         */
        public function set_columns($count) {
            $this->settings['columns_count'] = max(2, min(4, intval($count)));
            return $this;
        }
        
        /**
         * Enable full width
         * 
         * @param bool $full_width
         * @return $this
         */
        public function set_full_width($full_width = true) {
            $this->settings['full_width'] = (bool) $full_width;
            return $this;
        }
        
        /**
         * Add image to mega menu
         * 
         * @param string $image_url Image URL
         * @param string $image_alt Image alt text
         * @return $this
         */
        public function add_image($image_url, $image_alt = '') {
            $this->settings['has_image'] = true;
            $this->settings['image_url'] = esc_url($image_url);
            $this->settings['image_alt'] = esc_attr($image_alt);
            return $this;
        }
        
        /**
         * Add custom class
         * 
         * @param string $class_name
         * @return $this
         */
        public function add_class($class_name) {
            $this->settings['custom_class'] = esc_attr($class_name);
            return $this;
        }
        
        /**
         * Add a column to mega menu
         * 
         * @param string $title Column title
         * @param array $items Column items (array of arrays with 'title', 'url', 'icon')
         * @param array $options Column options
         * @return $this
         */
        public function add_column($title, $items = array(), $options = array()) {
            $this->columns[] = array(
                'title' => $title,
                'items' => $items,
                'options' => wp_parse_args($options, array(
                    'featured' => false,
                    'custom_class' => '',
                )),
            );
            return $this;
        }
        
        /**
         * Add content block to mega menu
         * 
         * @param string $title Content title
         * @param string $content Content HTML
         * @param string $position Position: 'before' or 'after'
         * @return $this
         */
        public function add_content($title, $content, $position = 'after') {
            if (!isset($this->settings['content_blocks'])) {
                $this->settings['content_blocks'] = array();
            }
            $this->settings['content_blocks'][] = array(
                'title' => $title,
                'content' => $content,
                'position' => $position,
            );
            return $this;
        }
        
        /**
         * Get CSS classes for menu item
         * 
         * @return string
         */
        public function get_item_classes() {
            $classes = array('nav-item', 'dropdown', 'mega-menu');
            
            // Add column count class
            if ($this->settings['columns_count'] >= 2 && $this->settings['columns_count'] <= 4) {
                $classes[] = 'mega-menu-' . $this->settings['columns_count'] . 'cols';
            }
            
            // Add full width class
            if ($this->settings['full_width']) {
                $classes[] = 'mega-menu-fullwidth';
            }
            
            // Add image class
            if ($this->settings['has_image']) {
                $classes[] = 'mega-menu-with-image';
            }
            
            // Add custom class
            if (!empty($this->settings['custom_class'])) {
                $classes[] = $this->settings['custom_class'];
            }
            
            return implode(' ', $classes);
        }
        
        /**
         * Render mega menu HTML
         * 
         * @return string
         */
        public function render() {
            $output = '';
            
            // Start dropdown menu
            $output .= '<ul class="dropdown-menu">';
            
            // Add content blocks before columns
            if (isset($this->settings['content_blocks'])) {
                foreach ($this->settings['content_blocks'] as $block) {
                    if ($block['position'] === 'before') {
                        $output .= $this->render_content_block($block);
                    }
                }
            }
            
            // Render columns
            if (!empty($this->columns)) {
                foreach ($this->columns as $column) {
                    $output .= $this->render_column($column);
                }
            }
            
            // Add image if exists
            if ($this->settings['has_image'] && !empty($this->settings['image_url'])) {
                $output .= $this->render_image();
            }
            
            // Add content blocks after columns
            if (isset($this->settings['content_blocks'])) {
                foreach ($this->settings['content_blocks'] as $block) {
                    if ($block['position'] === 'after') {
                        $output .= $this->render_content_block($block);
                    }
                }
            }
            
            $output .= '</ul>';
            
            return $output;
        }
        
        /**
         * Render a column
         * 
         * @param array $column Column data
         * @return string
         */
        private function render_column($column) {
            $column_class = 'mega-menu-column';
            if (!empty($column['options']['custom_class'])) {
                $column_class .= ' ' . esc_attr($column['options']['custom_class']);
            }
            if (!empty($column['options']['featured'])) {
                $column_class .= ' mega-menu-featured';
            }
            
            $output = '<li class="' . $column_class . '">';
            
            // Column title
            if (!empty($column['title'])) {
                $output .= '<h3 class="mega-menu-column-title">' . esc_html($column['title']) . '</h3>';
            }
            
            // Column items
            if (!empty($column['items'])) {
                $output .= '<ul>';
                foreach ($column['items'] as $item) {
                    $output .= $this->render_item($item);
                }
                $output .= '</ul>';
            }
            
            $output .= '</li>';
            
            return $output;
        }
        
        /**
         * Render a menu item
         * 
         * @param array $item Item data
         * @return string
         */
        private function render_item($item) {
            $item = wp_parse_args($item, array(
                'title' => '',
                'url' => '#',
                'icon' => '',
                'description' => '',
                'featured' => false,
                'custom_class' => '',
            ));
            
            $item_class = 'nav-item';
            if (!empty($item['custom_class'])) {
                $item_class .= ' ' . esc_attr($item['custom_class']);
            }
            if (!empty($item['featured'])) {
                $item_class .= ' featured-item';
            }
            
            $output = '<li class="' . $item_class . '">';
            $output .= '<a href="' . esc_url($item['url']) . '" class="nav-link">';
            
            // Icon
            if (!empty($item['icon'])) {
                $output .= '<span class="nav-icon">' . esc_html($item['icon']) . '</span>';
            }
            
            // Title
            $output .= '<span class="nav-text">' . esc_html($item['title']) . '</span>';
            
            // Description
            if (!empty($item['description'])) {
                $output .= '<span class="nav-description">' . esc_html($item['description']) . '</span>';
            }
            
            $output .= '</a>';
            $output .= '</li>';
            
            return $output;
        }
        
        /**
         * Render image
         * 
         * @return string
         */
        private function render_image() {
            $output = '<li class="mega-menu-image-wrapper">';
            $output .= '<img src="' . esc_url($this->settings['image_url']) . '"';
            $output .= ' alt="' . esc_attr($this->settings['image_alt']) . '"';
            $output .= ' class="mega-menu-image"';
            $output .= ' />';
            $output .= '</li>';
            
            return $output;
        }
        
        /**
         * Render content block
         * 
         * @param array $block Block data
         * @return string
         */
        private function render_content_block($block) {
            $output = '<li class="mega-menu-content">';
            if (!empty($block['title'])) {
                $output .= '<h3>' . esc_html($block['title']) . '</h3>';
            }
            $output .= '<div>' . wp_kses_post($block['content']) . '</div>';
            $output .= '</li>';
            
            return $output;
        }
        
        /**
         * Apply mega menu to WordPress menu item
         * 
         * @param int $item_id Menu item ID
         * @param callable $callback Callback function that returns Khane_Irani_Mega_Menu instance
         */
        public static function apply_to_item($item_id, $callback) {
            add_filter('nav_menu_css_class', function($classes, $item) use ($item_id, $callback) {
                if ($item->ID == $item_id) {
                    $mega_menu = call_user_func($callback);
                    if ($mega_menu instanceof Khane_Irani_Mega_Menu) {
                        $mega_classes = explode(' ', $mega_menu->get_item_classes());
                        $classes = array_merge($classes, $mega_classes);
                    }
                }
                return $classes;
            }, 10, 2);
            
            add_filter('walker_nav_menu_start_lvl', function($output, $depth, $args) use ($item_id, $callback) {
                if ($depth == 0) {
                    // Check if this is our mega menu item
                    $mega_menu = call_user_func($callback);
                    if ($mega_menu instanceof Khane_Irani_Mega_Menu) {
                        return $mega_menu->render();
                    }
                }
                return $output;
            }, 10, 3);
        }
    }
}

/**
 * Helper function to create mega menu easily
 * 
 * @param int $item_id Menu item ID
 * @param array $settings Menu settings
 * @return Khane_Irani_Mega_Menu
 */
function khane_irani_mega_menu($item_id, $settings = array()) {
    return new Khane_Irani_Mega_Menu($item_id, $settings);
}

