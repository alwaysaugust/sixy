<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

include _wpl_import('widgets.favorites.scripts.css_backend', true, true);
?>
<div class="wpl_favorites_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_favorites_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="widefat">
            <?php echo $this->generate_layouts_selectbox('favorites', $instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('wpltarget'); ?>"><?php echo __('Target page', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('wpltarget'); ?>" name="<?php echo $this->get_field_name('wpltarget'); ?>">
            <option value="">-----</option>
	        <?php echo $this->generate_pages_selectbox($instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_css_class'); ?>" name="<?php echo $this->get_field_name('data'); ?>[css_class]" value="<?php echo isset($instance['data']['css_class']) ? $instance['data']['css_class'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('max_char_title'); ?>"><?php echo __('Maximum title character', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('max_char_title'); ?>" name="<?php echo $this->get_field_name('data'); ?>[max_char_title]" value="<?php echo $instance['data']['max_char_title']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('image_width'); ?>"><?php echo __('Image width', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('image_width'); ?>" name="<?php echo $this->get_field_name('data'); ?>[image_width]" value="<?php echo $instance['data']['image_width']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('image_height'); ?>"><?php echo __('Image height', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('image_height'); ?>" name="<?php echo $this->get_field_name('data'); ?>[image_height]" value="<?php echo $instance['data']['image_height']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('show_contact_form'); ?>"><?php echo __('Show contact', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('show_contact_form'); ?>" name="<?php echo $this->get_field_name('data'); ?>[show_contact_form]" class="widefat">
            <option value="1" <?php if($instance['data']['show_contact_form'] == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="0" <?php if($instance['data']['show_contact_form'] == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('show_compare'); ?>"><?php echo __('Show compare', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('show_compare'); ?>" name="<?php echo $this->get_field_name('data'); ?>[show_compare]" class="widefat">
            <option value="1" <?php if($instance['data']['show_compare'] == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="0" <?php if($instance['data']['show_compare'] == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <!-- Create a button to show Short-code of this widget -->
    <?php if(wpl_global::check_addon('pro') and !wpl_global::is_page_builder()): ?>
        <button id="btn-shortcode-<?php echo $this->number ?>"
                data-is-init="false"
                data-item-id="<?php echo $this->number ?>"
                data-fancy-id="wpl_view_shortcode_<?php echo $this->number; ?>" class="wpl-open-lightbox-btn wpl-button button-1"
                href="#wpl_view_shortcode_<?php echo $this->number ?>" type="button"><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></button>

        <div id="wpl_view_shortcode_<?php echo $this->number ?>" class="hidden">
            <div class="fanc-content size-width-1" id="wpl_flex_modify_container">
                <h2><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></h2>
                <div class="fanc-body fancy-search-body">
                    <p class="wpl_widget_shortcode_preview"><?php echo '[wpl_widget_instance id="' . $this->id . '"]'; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>