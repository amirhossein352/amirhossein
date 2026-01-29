<?php
/**
 * The template for displaying search forms
 *
 * @package khane_irani
 * @author Ali Ilkhani
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="search-field">
        <span class="screen-reader-text"><?php echo esc_html_x('Search for:', 'label', 'khane-irani'); ?></span>
    </label>
    <div class="search-input-group">
        <input type="search" id="search-field" class="search-field" 
               placeholder="<?php echo esc_attr_x('جستجو...', 'placeholder', 'khane-irani'); ?>" 
               value="<?php echo get_search_query(); ?>" 
               name="s" />
        <button type="submit" class="search-submit">
            <i class="fas fa-search"></i>
            <span class="screen-reader-text"><?php echo esc_html_x('Search', 'submit button', 'khane-irani'); ?></span>
        </button>
    </div>
</form>
