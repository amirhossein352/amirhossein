<?php
/**
 * Main plugin class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Modern_Account_Panel {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Hide header and footer on account pages
        add_action('template_redirect', array($this, 'hide_header_footer'));
        
        // Enqueue scripts and styles - use priority 999 to load after all themes and plugins
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 999);
        
        // Override WooCommerce account templates - use high priority to ensure our templates load first
        add_filter('woocommerce_locate_template', array($this, 'locate_template'), 99, 3);
        
        // Add custom body class
        add_filter('body_class', array($this, 'add_body_class'));
        
        // Override account navigation and content hooks
        add_action('template_redirect', array($this, 'override_account_hooks'), 5);
        
        
        // Debug: Log plugin initialization
        error_log('Modern Account Panel: Plugin initialized');
    }
    
    /**
     * Override WooCommerce account hooks - only add wrappers, don't remove anything
     */
    public function override_account_hooks() {
        if (!is_account_page()) {
            return;
        }
        
        // Close sidebar column and open main-content BEFORE navigation ends
        // This ensures main-content is outside of wd-my-account-sidebar
        add_action('woocommerce_after_account_navigation', array($this, 'close_sidebar_and_open_main'), 5);
        // Add header and page-content after navigation
        add_action('woocommerce_after_account_navigation', array($this, 'after_account_navigation'), 25);
        // Wrap content in woocommerce-MyAccount-content
        add_action('woocommerce_before_account_content', array($this, 'before_account_content'), 5);
        add_action('woocommerce_after_account_content', array($this, 'after_account_content'), 25);
    }
    
    /**
     * Run the plugin
     */
    public function run() {
        // Plugin is ready
    }
    
    /**
     * Hide header and footer on account pages
     */
    public function hide_header_footer() {
        if (!is_account_page()) {
            return;
        }
        
        // Hide header and footer with CSS (safer than removing actions)
        add_action('wp_head', array($this, 'hide_header_footer_css'), 999);
    }
    
    /**
     * CSS to hide header and footer and isolate from theme
     */
    public function hide_header_footer_css() {
        // Get theme font family from theme options
        $theme_font = 'inherit';
        
        // Try to get from theme mod (most themes use this)
        if (function_exists('get_theme_mod')) {
            $body_font = get_theme_mod('body_font_family', '');
            if (!empty($body_font)) {
                $theme_font = $body_font;
            }
        }
        
        // Try Woodmart specific font option
        if ($theme_font === 'inherit' && function_exists('woodmart_get_opt')) {
            $woodmart_font = woodmart_get_opt('primary-font');
            if (!empty($woodmart_font) && isset($woodmart_font['font-family'])) {
                $theme_font = $woodmart_font['font-family'];
            }
        }
        
        // Try to get from custom CSS
        if ($theme_font === 'inherit' && function_exists('wp_get_custom_css')) {
            $custom_css = wp_get_custom_css();
            if (preg_match('/body\s*\{[^}]*font-family:\s*([^;]+)/i', $custom_css, $matches)) {
                $theme_font = trim($matches[1]);
            }
        }
        
        // Final fallback: use CSS variable that JavaScript will set
        if ($theme_font === 'inherit') {
            $theme_font = 'var(--theme-font, inherit)';
        }
        
        ?>
        <style type="text/css">

        
        /* Hide theme header - comprehensive selectors */
        body.modern-account-panel-page > header,
        body.modern-account-panel-page header:not(.header),
        body.modern-account-panel-page .site-header,
        body.modern-account-panel-page .main-header,
        body.modern-account-panel-page .site-branding,
        body.modern-account-panel-page .main-navigation,
        body.modern-account-panel-page .site-navigation,
        body.modern-account-panel-page .navbar,
        body.modern-account-panel-page .top-bar,
        body.modern-account-panel-page .header-top,
        body.modern-account-panel-page .header-bottom,
        body.modern-account-panel-page .header-content,
        body.modern-account-panel-page .header-middle,
        body.modern-account-panel-page #header,
        body.modern-account-panel-page #masthead,
        body.modern-account-panel-page .masthead,
        body.modern-account-panel-page .wp-block-template-part[data-area="header"],
        body.modern-account-panel-page [role="banner"] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            z-index: -1 !important;
        }
        
        /* Prevent theme CSS from affecting account panel */
        body.modern-account-panel-page {
            padding-top: 0 !important;
            margin: 0 !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            height: 100vh !important;
        }
        
        html.modern-account-panel-page,
        html body.modern-account-panel-page {
            overflow-x: hidden !important;
            overflow-y: auto !important;
            height: 100% !important;
        }
        
        
        /* Hide theme footer - comprehensive selectors */
        body.modern-account-panel-page footer,
        body.modern-account-panel-page .site-footer,
        body.modern-account-panel-page .main-footer,
        body.modern-account-panel-page .footer:not(.taskbar),
        body.modern-account-panel-page #footer,
        body.modern-account-panel-page #colophon,
        body.modern-account-panel-page .site-info,
        body.modern-account-panel-page .wp-block-template-part[data-area="footer"],
        body.modern-account-panel-page [role="contentinfo"] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }
        
        /* Reset theme container styles */
        body.modern-account-panel-page #page,
        body.modern-account-panel-page .site,
        body.modern-account-panel-page .site-main,
        body.modern-account-panel-page .content-area,
        body.modern-account-panel-page .container,
        body.modern-account-panel-page .wrapper,
        body.modern-account-panel-page .woocommerce {
            margin: 0 !important;
            padding: 0 !important;
            min-height: auto !important;
            max-width: 100% !important;
            width: 100% !important;
        }
        
        /* Woodmart theme specific overrides - hide theme's account wrapper */
        body.modern-account-panel-page .wd-my-account-wrapper,
        body.modern-account-panel-page .wd-my-account-sidebar,
        body.modern-account-panel-page .wd-my-account-content,
        body.modern-account-panel-page .main-wrapper,
        body.modern-account-panel-page .content-wrapper,
        body.modern-account-panel-page .site-content,
        body.modern-account-panel-page .content,
        body.modern-account-panel-page .page-wrapper,
        body.modern-account-panel-page .wd-page-wrapper,
        body.modern-account-panel-page .wd-container,
        body.modern-account-panel-page .wd-row,
        body.modern-account-panel-page [class*="wd-col-"],
        body.modern-account-panel-page .wd-main-content {
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
        }
        
        /* Override Woodmart's grid system - break out of theme layout */
        body.modern-account-panel-page .wd-my-account-wrapper {
            position: relative !important;
            display: block !important;
            grid-template-columns: none !important;
            gap: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
        }
        
        /* Make theme's sidebar column contain our sidebar */
        body.modern-account-panel-page .wd-my-account-sidebar {
            position: relative !important;
            width: auto !important;
            max-width: none !important;
            flex: none !important;
            grid-column: auto !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Hide theme's title in sidebar */
        body.modern-account-panel-page .wd-my-account-sidebar > h3.woocommerce-MyAccount-title {
            display: none !important;
        }
        
        /* Sidebar should be fixed - only our account panel sidebar */
        body.modern-account-panel-page .wd-my-account-sidebar > .sidebar {
            position: fixed !important;
            right: 0 !important;
            top: 0 !important;
            z-index: 1000 !important;
        }
        
        /* Main content column should contain our main-content */
        body.modern-account-panel-page .wd-my-account-content {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
            grid-column: 1 / -1 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Ensure main-content is not constrained by theme - STRONG OVERRIDES */
        body.modern-account-panel-page .main-content {
            flex: 1 1 auto !important;
            width: calc(100% - var(--sidebar-width, 260px)) !important;
            max-width: calc(100% - var(--sidebar-width, 260px)) !important;
            min-width: 0 !important;
            margin-right: var(--sidebar-width, 260px) !important;
            margin-left: 0 !important;
            box-sizing: border-box !important;
            visibility: visible !important;
            display: flex !important;
            flex-direction: column !important;
            position: relative !important;
            overflow-x: hidden !important;
        }
        
        /* Prevent any theme container from expanding main-content */
        body.modern-account-panel-page .main-content * {
            max-width: 100% !important;
        }
        
        /* Ensure sidebar toggle button works */
        body.modern-account-panel-page .sidebar-toggle,
        body.modern-account-panel-page #sidebarToggle {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            z-index: 1001 !important;
            position: relative !important;
        }
        
        /* Prevent any theme styles from disabling the button */
        body.modern-account-panel-page .sidebar-toggle *,
        body.modern-account-panel-page #sidebarToggle * {
            pointer-events: none !important;
        }
        
        /* Strong override for main-content width - prevent theme interference */
        body.modern-account-panel-page .wd-my-account-content .main-content,
        body.modern-account-panel-page .wd-my-account-wrapper .main-content,
        body.modern-account-panel-page .main-content {
            width: calc(100% - var(--sidebar-width, 260px)) !important;
            max-width: calc(100% - var(--sidebar-width, 260px)) !important;
            min-width: 0 !important;
            margin-right: var(--sidebar-width, 260px) !important;
            margin-left: 0 !important;
            box-sizing: border-box !important;
        }
        
        body.modern-account-panel-page .wd-my-account-content .main-content.sidebar-collapsed,
        body.modern-account-panel-page .wd-my-account-wrapper .main-content.sidebar-collapsed,
        body.modern-account-panel-page .main-content.sidebar-collapsed {
            width: calc(100% - var(--sidebar-collapsed-width, 80px)) !important;
            max-width: calc(100% - var(--sidebar-collapsed-width, 80px)) !important;
            margin-right: var(--sidebar-collapsed-width, 80px) !important;
        }
        
        /* Ensure header is visible */
        body.modern-account-panel-page .main-content .header {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
        }
        
        /* Ensure page-content is visible */
        body.modern-account-panel-page .main-content .page-content {
            visibility: visible !important;
            display: block !important;
            opacity: 1 !important;
            flex: 1 1 auto !important;
            overflow: visible !important;
            overflow-y: visible !important;
            padding: 0 !important;
            min-height: 0 !important;
        }
        
        /* Add padding to page-content when it has content */
        body.modern-account-panel-page .main-content .page-content:not(:empty) {
            padding: 1.5rem !important;
            padding-bottom: 1.5rem !important;
            min-height: calc(100vh - var(--header-height, 70px)) !important;
            overflow: visible !important;
            overflow-y: visible !important;
        }
        
        
        /* Main content should not scroll - only body scrolls */
        body.modern-account-panel-page .main-content {
            overflow: visible !important;
            overflow-y: visible !important;
            overflow-x: hidden !important;
            height: auto !important;
            min-height: 100vh !important;
        }
        
        /* Header should not scroll - it's sticky */
        body.modern-account-panel-page .main-content .header {
            overflow: visible !important;
            overflow-y: visible !important;
            overflow-x: visible !important;
        }
        
        /* Responsive Styles for Mobile */
        @media (max-width: 768px) {
            /* Main content عرض کامل در موبایل */
            body.modern-account-panel-page .main-content,
            body.modern-account-panel-page .wd-my-account-content .main-content,
            body.modern-account-panel-page .wd-my-account-wrapper .main-content {
                margin-right: 0 !important;
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                visibility: visible !important;
                opacity: 1 !important;
                min-height: 100vh !important;
                background-color: var(--gray-50, #f9fafb) !important;
            }
            
            /* Ensure page-content is visible in mobile */
            body.modern-account-panel-page .main-content .page-content {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                padding: 1rem !important;
                overflow: visible !important;
                overflow-y: visible !important;
            }
            
            /* Main content should not scroll in mobile - only body scrolls */
            body.modern-account-panel-page .main-content {
                overflow: visible !important;
                overflow-y: visible !important;
                overflow-x: hidden !important;
            }
            
            body.modern-account-panel-page .main-content.sidebar-collapsed {
                margin-right: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            
            /* Page content padding مناسب موبایل */
            body.modern-account-panel-page .main-content .page-content:not(:empty),
            body.modern-account-panel-page .page-content,
            body.modern-account-panel-page .woocommerce-MyAccount-content {
                padding: 1rem !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            
            /* Tables responsive */
            body.modern-account-panel-page table,
            body.modern-account-panel-page .woocommerce table {
                width: 100% !important;
                max-width: 100% !important;
                font-size: 0.875rem !important;
                display: block !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
            
            /* Forms responsive */
            body.modern-account-panel-page form,
            body.modern-account-panel-page .woocommerce form {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            body.modern-account-panel-page input,
            body.modern-account-panel-page textarea,
            body.modern-account-panel-page select {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            
            /* Header responsive - ensure it's visible */
            body.modern-account-panel-page .main-content .header {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                padding: 1rem !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 100 !important;
                background-color: var(--header-bg, #ffffff) !important;
                width: 100% !important;
                height: auto !important;
                min-height: var(--header-height, 70px) !important;
            }
            
        }
        
        /* Small Mobile */
        @media (max-width: 480px) {
            body.modern-account-panel-page .header {
                padding: 0.75rem !important;
            }
            
            body.modern-account-panel-page .main-content .page-content:not(:empty),
            body.modern-account-panel-page .page-content,
            body.modern-account-panel-page .woocommerce-MyAccount-content {
                padding: 0.75rem !important;
            }
            
            /* Tables در موبایل کوچک */
            body.modern-account-panel-page table,
            body.modern-account-panel-page .woocommerce table {
                font-size: 0.75rem !important;
            }
            
            body.modern-account-panel-page table th,
            body.modern-account-panel-page table td {
                padding: 0.375rem !important;
            }
            
            /* Buttons full width در موبایل کوچک */
            body.modern-account-panel-page .btn,
            body.modern-account-panel-page .woocommerce .button {
                width: 100% !important;
                margin-bottom: 0.5rem !important;
            }
        }
        
        /* Ensure header is at top */
        body.modern-account-panel-page .main-content .header {
            flex-shrink: 0 !important;
        }
        
        /* When sidebar is collapsed */
        body.modern-account-panel-page .main-content.sidebar-collapsed {
            width: calc(100% - var(--sidebar-collapsed-width, 80px)) !important;
            max-width: calc(100% - var(--sidebar-collapsed-width, 80px)) !important;
            margin-right: var(--sidebar-collapsed-width, 80px) !important;
        }
        
        /* Ensure sidebar collapsed state works */
        body.modern-account-panel-page .sidebar.collapsed {
            width: var(--sidebar-collapsed-width, 80px) !important;
            min-width: var(--sidebar-collapsed-width, 80px) !important;
            max-width: var(--sidebar-collapsed-width, 80px) !important;
        }
        
        /* Override any theme flex/grid that might interfere */
        body.modern-account-panel-page .main-content .container,
        body.modern-account-panel-page .main-content .row,
        body.modern-account-panel-page .main-content [class*="col-"],
        body.modern-account-panel-page .main-content .wd-row,
        body.modern-account-panel-page .main-content [class*="wd-col-"] {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Hide ONLY theme's main sidebar (sidebar-1 / #secondary) - the one that appears in posts */
        body.modern-account-panel-page #secondary,
        body.modern-account-panel-page aside#secondary,
        body.modern-account-panel-page .content-sidebar-wrapper > #secondary,
        body.modern-account-panel-page .content-sidebar-wrapper > aside#secondary {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 0 !important;
            max-width: 0 !important;
        }
        
        /* Remove grid layout and make content-sidebar-wrapper single column to fill empty space */
        body.modern-account-panel-page .content-sidebar-wrapper {
            display: block !important;
            grid-template-columns: 1fr !important;
            gap: 0 !important;
        }
        
        /* Make content-area take full width when sidebar is hidden */
        body.modern-account-panel-page .content-area,
        body.modern-account-panel-page .content-area:has(+ #secondary),
        body.modern-account-panel-page .site-main:has(+ #secondary) {
            width: 100% !important;
            max-width: 100% !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        
        /* Remove any gap or spacing that creates empty space */
        body.modern-account-panel-page .content-sidebar-wrapper > .content-area {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        

        

        
 
        /* Override all links and buttons in account page with !important */
        body.modern-account-panel-page a,
        body.modern-account-panel-page button,
        body.modern-account-panel-page .btn,
        body.modern-account-panel-page .nav-link,
        body.modern-account-panel-page .btn-link,
        body.modern-account-panel-page .btn-primary,
        body.modern-account-panel-page .btn-secondary,
        body.modern-account-panel-page .btn-success,
        body.modern-account-panel-page .btn-danger,
        body.modern-account-panel-page .btn-icon,
        body.modern-account-panel-page .icon-btn,
        body.modern-account-panel-page .quick-action-item,
        body.modern-account-panel-page .dropdown-item,
        body.modern-account-panel-page .start-menu-item,
        body.modern-account-panel-page .start-menu-footer-item,
        body.modern-account-panel-page input[type="submit"],
        body.modern-account-panel-page input[type="button"],
        body.modern-account-panel-page .woocommerce-button,
        body.modern-account-panel-page .button,
        body.modern-account-panel-page .woocommerce-Button {
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        
        /* Links specific styles */
        body.modern-account-panel-page a {
            color: inherit !important;
            text-decoration: none !important;
        }
        
        body.modern-account-panel-page a:hover {
            text-decoration: none !important;
        }
        
        /* Buttons specific styles */
        body.modern-account-panel-page button,
        body.modern-account-panel-page .btn,
        body.modern-account-panel-page input[type="submit"],
        body.modern-account-panel-page input[type="button"],
        body.modern-account-panel-page .woocommerce-button,
        body.modern-account-panel-page .button {
            border: none !important;
            background: none !important;
            font-family: inherit !important;
            cursor: pointer !important;
        }
        
        /* Primary button */
        body.modern-account-panel-page .btn-primary,
        body.modern-account-panel-page .woocommerce-Button--primary {
            background-color: var(--primary-color, #6366f1) !important;
            color: white !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        body.modern-account-panel-page .btn-primary:hover,
        body.modern-account-panel-page .woocommerce-Button--primary:hover {
            background-color: var(--primary-dark, #4f46e5) !important;
            color: white !important;
        }
        
        /* Secondary button */
        body.modern-account-panel-page .btn-secondary {
            background-color: var(--gray-200, #e5e7eb) !important;
            color: var(--gray-700, #374151) !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.5rem !important;
        }
        
        body.modern-account-panel-page .btn-secondary:hover {
            background-color: var(--gray-300, #d1d5db) !important;
        }
        
        /* Navigation links */
        body.modern-account-panel-page .nav-link {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.875rem 1.25rem !important;
            color: var(--sidebar-text, #cbd5e1) !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
        }
        
        body.modern-account-panel-page .nav-link:hover {
            background-color: var(--sidebar-hover, #334155) !important;
            color: white !important;
            text-decoration: none !important;
        }
        
        body.modern-account-panel-page .nav-item.active .nav-link {
            background-color: var(--sidebar-active, #6366f1) !important;
            color: white !important;
        }
        
        /* Link buttons */
        body.modern-account-panel-page .btn-link {
            color: var(--primary-color, #6366f1) !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }
        
        body.modern-account-panel-page .btn-link:hover {
            color: var(--primary-dark, #4f46e5) !important;
            text-decoration: none !important;
        }
        
        /* Quick action items */
        body.modern-account-panel-page .quick-action-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            color: var(--gray-700, #374151) !important;
            text-decoration: none !important;
            border-radius: 0.5rem !important;
            transition: all 0.3s ease !important;
        }
        
        body.modern-account-panel-page .quick-action-item:hover {
            background-color: var(--gray-100, #f3f4f6) !important;
            color: var(--gray-900, #111827) !important;
            text-decoration: none !important;
        }
        
        /* Dropdown items */
        body.modern-account-panel-page .dropdown-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            color: var(--gray-700, #374151) !important;
            text-decoration: none !important;
        }
        
        body.modern-account-panel-page .dropdown-item:hover {
            background-color: var(--gray-50, #f9fafb) !important;
            color: var(--gray-900, #111827) !important;
            text-decoration: none !important;
        }
        
        /* Icon buttons */
        body.modern-account-panel-page .icon-btn,
        body.modern-account-panel-page .btn-icon {
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0.5rem !important;
            color: var(--gray-700, #374151) !important;
            text-decoration: none !important;
            border: none !important;
            background: none !important;
        }
        
        body.modern-account-panel-page .icon-btn:hover,
        body.modern-account-panel-page .btn-icon:hover {
            background-color: var(--gray-100, #f3f4f6) !important;
            color: var(--gray-900, #111827) !important;
        }
        
        /* WooCommerce specific buttons */
        body.modern-account-panel-page .woocommerce-button,
        body.modern-account-panel-page .button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            cursor: pointer !important;
        }
        
        /* Success and Danger buttons */
        body.modern-account-panel-page .btn-success {
            background-color: var(--success-color, #10b981) !important;
            color: white !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.5rem !important;
        }
        
        body.modern-account-panel-page .btn-success:hover {
            background-color: #059669 !important;
            color: white !important;
        }
        
        body.modern-account-panel-page .btn-danger {
            background-color: var(--danger-color, #ef4444) !important;
            color: white !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.5rem !important;
        }
        
        body.modern-account-panel-page .btn-danger:hover {
            background-color: #dc2626 !important;
            color: white !important;
        }
        
        /* Small buttons */
        body.modern-account-panel-page .btn-sm {
            padding: 0.5rem 1rem !important;
            font-size: 0.8125rem !important;
        }
        
        /* Taskbar buttons */
        body.modern-account-panel-page .taskbar-app,
        body.modern-account-panel-page .taskbar-start {
            border: none !important;
            background: none !important;
            cursor: pointer !important;
            color: inherit !important;
        }
        
        /* Start menu items */
        body.modern-account-panel-page .start-menu-item,
        body.modern-account-panel-page .start-menu-footer-item {
            text-decoration: none !important;
            color: inherit !important;
            display: flex !important;
            align-items: center !important;
        }
        
        body.modern-account-panel-page .start-menu-item:hover,
        body.modern-account-panel-page .start-menu-footer-item:hover {
            text-decoration: none !important;
        }
        
        /* Order links and action buttons */
        body.modern-account-panel-page .order-link,
        body.modern-account-panel-page .view-order,
        body.modern-account-panel-page .action-buttons .btn {
            text-decoration: none !important;
            cursor: pointer !important;
        }
        
        /* Form submit buttons */
        body.modern-account-panel-page form button[type="submit"],
        body.modern-account-panel-page form input[type="submit"] {
            background-color: var(--primary-color, #6366f1) !important;
            color: white !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.5rem !important;
            border: none !important;
            cursor: pointer !important;
        }
        
        body.modern-account-panel-page form button[type="submit"]:hover,
        body.modern-account-panel-page form input[type="submit"]:hover {
            background-color: var(--primary-dark, #4f46e5) !important;
        }
        
        /* Download links */
        body.modern-account-panel-page .download-link,
        body.modern-account-panel-page .download-meta a {
            color: var(--primary-color, #6366f1) !important;
            text-decoration: none !important;
        }
        
        body.modern-account-panel-page .download-link:hover,
        body.modern-account-panel-page .download-meta a:hover {
            color: var(--primary-dark, #4f46e5) !important;
            text-decoration: none !important;
        }
        
        /* Address action buttons */
        body.modern-account-panel-page .address-actions .btn,
        body.modern-account-panel-page .form-actions .btn {
            margin: 0.25rem !important;
        }
        
        /* Payment method actions */
        body.modern-account-panel-page .payment-method-actions .btn-icon {
            border: none !important;
            background: none !important;
            cursor: pointer !important;
        }
        
        </style>
        <?php
    }
    
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        if (!is_account_page()) {
            return;
        }
        
        // Debug: Log that we're on account page
        error_log('Modern Account Panel: Enqueuing scripts on account page');
        error_log('Modern Account Panel: Plugin path: ' . MODERN_ACCOUNT_PANEL_PATH);
        error_log('Modern Account Panel: Plugin URL: ' . MODERN_ACCOUNT_PANEL_URL);
        
        // Font Awesome - Load first so icons are available
        // Check if Font Awesome is already enqueued by theme or other plugins
        if (!wp_style_is('font-awesome', 'enqueued') && !wp_style_is('fontawesome', 'enqueued')) {
            // Try multiple CDN sources for better reliability
            $font_awesome_cdns = array(
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
                'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css',
                'https://use.fontawesome.com/releases/v6.4.0/css/all.css'
            );
            
            // Use the first CDN (cdnjs is usually most reliable)
            wp_enqueue_style(
                'modern-account-panel-font-awesome',
                $font_awesome_cdns[0],
                array(),
                '6.4.0',
                'all'
            );
            
            // Add preconnect for faster loading
            add_action('wp_head', array($this, 'add_font_awesome_preconnect'), 1);
            
            error_log('Modern Account Panel: Font Awesome enqueued');
        } else {
            error_log('Modern Account Panel: Font Awesome already enqueued by theme/plugin');
        }
        
        // Enqueue CSS files - check if files exist
        $main_css = MODERN_ACCOUNT_PANEL_PATH . 'assets/css/main.css';
        if (file_exists($main_css)) {
            wp_enqueue_style(
                'modern-account-panel-main',
                MODERN_ACCOUNT_PANEL_URL . 'assets/css/main.css',
                array(),
                MODERN_ACCOUNT_PANEL_VERSION . '-' . filemtime($main_css) // Add file modification time for cache busting
            );
            error_log('Modern Account Panel: Main CSS enqueued: ' . MODERN_ACCOUNT_PANEL_URL . 'assets/css/main.css');
        } else {
            error_log('Modern Account Panel: Main CSS file not found: ' . $main_css);
        }
        
        // Enqueue all CSS files
        $css_files = array(
            'modern-account-panel-sidebar' => 'assets/css/sidebar.css',
            'modern-account-panel-header' => 'assets/css/header.css',
            'modern-account-panel-dashboard' => 'assets/css/dashboard.css',
            'modern-account-panel-page-loader' => 'assets/css/page-loader.css',
            'modern-account-panel-responsive' => 'assets/css/responsive.css',
        );
        
        foreach ($css_files as $handle => $file) {
            $file_path = MODERN_ACCOUNT_PANEL_PATH . $file;
            if (file_exists($file_path)) {
                wp_enqueue_style(
                    $handle,
                    MODERN_ACCOUNT_PANEL_URL . $file,
                    array('modern-account-panel-main'),
                    MODERN_ACCOUNT_PANEL_VERSION . '-' . filemtime($file_path)
                );
            } else {
                error_log('Modern Account Panel: CSS file not found: ' . $file_path);
            }
        }
        
        // Page-specific CSS
        $endpoint = $this->get_current_endpoint();
        
        // Dashboard styles are already loaded in dashboard.css above
        // No need to load pages/dashboard.css separately
        
        // Load other page-specific CSS
        if ($endpoint && $endpoint !== 'dashboard' && file_exists(MODERN_ACCOUNT_PANEL_PATH . 'assets/css/pages/' . $endpoint . '.css')) {
            wp_enqueue_style(
                'modern-account-panel-' . $endpoint,
                MODERN_ACCOUNT_PANEL_URL . 'assets/css/pages/' . $endpoint . '.css',
                array('modern-account-panel-main'),
                MODERN_ACCOUNT_PANEL_VERSION . '-' . filemtime(MODERN_ACCOUNT_PANEL_PATH . 'assets/css/pages/' . $endpoint . '.css')
            );
        }
        
        // Enqueue JavaScript files - check if files exist
        $main_js = MODERN_ACCOUNT_PANEL_PATH . 'assets/js/main.js';
        if (file_exists($main_js)) {
            wp_enqueue_script(
                'modern-account-panel-main',
                MODERN_ACCOUNT_PANEL_URL . 'assets/js/main.js',
                array('jquery'),
                MODERN_ACCOUNT_PANEL_VERSION . '-' . filemtime($main_js), // Add file modification time for cache busting
                true
            );
            error_log('Modern Account Panel: Main JS enqueued: ' . MODERN_ACCOUNT_PANEL_URL . 'assets/js/main.js');
        } else {
            error_log('Modern Account Panel: Main JS file not found: ' . $main_js);
        }
        
        // Enqueue all JavaScript files
        // Note: page-loader.js is disabled to prevent white screen - using normal WordPress navigation
        $js_files = array(
            'modern-account-panel-sidebar' => array('file' => 'assets/js/sidebar.js', 'deps' => array('jquery', 'modern-account-panel-main')),
            'modern-account-panel-navigation' => array('file' => 'assets/js/navigation.js', 'deps' => array('jquery', 'modern-account-panel-main')),
            // 'modern-account-panel-page-loader' => array('file' => 'assets/js/page-loader.js', 'deps' => array('jquery', 'modern-account-panel-main')), // Disabled - causes white screen
            'modern-account-panel-quick-access' => array('file' => 'assets/js/quick-access.js', 'deps' => array('jquery', 'modern-account-panel-main')),
        );
        
        foreach ($js_files as $handle => $data) {
            $file_path = MODERN_ACCOUNT_PANEL_PATH . $data['file'];
            if (file_exists($file_path)) {
                wp_enqueue_script(
                    $handle,
                    MODERN_ACCOUNT_PANEL_URL . $data['file'],
                    $data['deps'],
                    MODERN_ACCOUNT_PANEL_VERSION . '-' . filemtime($file_path),
                    true
                );
            } else {
                error_log('Modern Account Panel: JS file not found: ' . $file_path);
            }
        }
        
        // Page-specific JS
        if ($endpoint && file_exists(MODERN_ACCOUNT_PANEL_PATH . 'assets/js/pages/' . $endpoint . '.js')) {
            wp_enqueue_script(
                'modern-account-panel-' . $endpoint,
                MODERN_ACCOUNT_PANEL_URL . 'assets/js/pages/' . $endpoint . '.js',
                array('jquery', 'modern-account-panel-main'),
                MODERN_ACCOUNT_PANEL_VERSION,
                true
            );
        }
        
        // Localize script - only if main.js was enqueued
        if (wp_script_is('modern-account-panel-main', 'enqueued')) {
            wp_localize_script('modern-account-panel-main', 'modernAccountPanel', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('modern_account_panel_nonce'),
                'accountUrl' => wc_get_page_permalink('myaccount'),
                'currentEndpoint' => $endpoint,
                'endpoints' => $this->get_account_endpoints(),
            ));
            error_log('Modern Account Panel: Scripts localized successfully');
        } else {
            error_log('Modern Account Panel: Main JS not enqueued, skipping localization');
        }
        
        // Debug: Log what was enqueued
        error_log('Modern Account Panel: CSS enqueued: ' . (wp_style_is('modern-account-panel-main', 'enqueued') ? 'yes' : 'no'));
        error_log('Modern Account Panel: JS enqueued: ' . (wp_script_is('modern-account-panel-main', 'enqueued') ? 'yes' : 'no'));
    }
    
    /**
     * Get current account endpoint
     */
    private function get_current_endpoint() {
        global $wp;
        $endpoint = isset($wp->query_vars) ? array_keys($wp->query_vars) : array();
        
        if (empty($endpoint) || !isset($endpoint[0])) {
            return 'dashboard';
        }
        
        $endpoint = $endpoint[0];
        
        // Map WooCommerce endpoints to our template names
        $endpoint_map = array(
            'orders' => 'orders',
            'downloads' => 'downloads',
            'edit-address' => 'addresses',
            'edit-account' => 'account-details',
            'payment-methods' => 'payment-methods',
        );
        
        return isset($endpoint_map[$endpoint]) ? $endpoint_map[$endpoint] : $endpoint;
    }
    
    /**
     * Get all account endpoints
     */
    private function get_account_endpoints() {
        $endpoints = wc_get_account_menu_items();
        $endpoint_urls = array();
        
        foreach ($endpoints as $endpoint => $label) {
            $endpoint_urls[$endpoint] = wc_get_account_endpoint_url($endpoint);
        }
        
        return $endpoint_urls;
    }
    
    /**
     * Locate custom templates
     */
    public function locate_template($template, $template_name, $template_path) {
        // Only override on account pages
        if (!is_account_page()) {
            return $template;
        }
        
        // Build plugin template path
        $plugin_template_path = MODERN_ACCOUNT_PANEL_PATH . 'templates/woocommerce/' . $template_name;
        
        // Check if our custom template exists
        if (file_exists($plugin_template_path)) {
            error_log('Modern Account Panel: Using custom template: ' . $template_name . ' from ' . $plugin_template_path);
            return $plugin_template_path;
        }
        
        // Log if template not found (for debugging)
        if (strpos($template_name, 'myaccount/') !== false) {
            error_log('Modern Account Panel: Template not found: ' . $template_name . ' at ' . $plugin_template_path);
        }
        
        return $template;
    }
    
    /**
     * Add custom body class
     */
    public function add_body_class($classes) {
        if (is_account_page()) {
            $classes[] = 'modern-account-panel-page';
            $classes[] = 'rtl';
        }
        return $classes;
    }
    
    /**
     * Add Font Awesome preconnect for faster loading
     */
    public function add_font_awesome_preconnect() {
        if (is_account_page()) {
            echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";
            echo '<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">' . "\n";
        }
    }
    
    /**
     * Close sidebar column and open main-content
     * This breaks out of theme's grid system
     */
    public function close_sidebar_and_open_main() {
        // Close theme's sidebar column and open main-content
        echo '</div>'; // Close wd-my-account-sidebar
        echo '<div class="wd-my-account-content wd-grid-col" style="--wd-col-lg:9;--wd-col-md:8;--wd-col-sm:12;">';
        echo '<div class="main-content" id="mainContent">';
    }
    
    /**
     * After account navigation - add header and page-content
     */
    public function after_account_navigation() {
        $header_path = MODERN_ACCOUNT_PANEL_PATH . 'templates/account/header.php';
        if (file_exists($header_path)) {
            include $header_path;
        } else {
            error_log('Modern Account Panel: Header template not found: ' . $header_path);
        }
        echo '<main class="modern-account-panel-page-content page-content" id="pageContent">';
        echo '<div class="woocommerce-MyAccount-content">';
    }
    
    /**
     * Before account content - woocommerce-MyAccount-content is already opened
     */
    public function before_account_content() {
        // woocommerce-MyAccount-content is already opened in after_account_navigation
    }
    
    /**
     * After account content wrapper
     */
    public function after_account_content() {
        error_log('Modern Account Panel: after_account_content hook executed');
        
        echo '</div>'; // Close woocommerce-MyAccount-content
        echo '</main>'; // Close page-content
        echo '</div>'; // Close main-content
        echo '</div>'; // Close wd-my-account-content
        
    }
    
}
