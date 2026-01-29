<?php
/**
 * Asset helpers: versioning via file modification time
 * @package khane_irani
 */

if (!function_exists('khane_irani_asset_version')) {
    function khane_irani_asset_version($relative_path) {
        $file = get_template_directory() . $relative_path;
        
        // Force cache bust for specific files
        $cache_bust_files = array(
            '/css/theme-overrides.css' => '2.0.0',
            '/js/navigation.js' => '2.0.0',
        );
        
        if (isset($cache_bust_files[$relative_path])) {
            $base_version = $cache_bust_files[$relative_path];
            if (file_exists($file)) {
                return $base_version . '.' . filemtime($file);
            }
            return $base_version;
        }
        
        if (file_exists($file)) {
            return (string) filemtime($file);
        }
        return defined('_S_VERSION') ? _S_VERSION : '1.0.0';
    }
}


