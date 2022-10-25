<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_mobile_app_widget_backend_form wpl_carousel_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_google_map_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="widefat">
            <option value="0" <?php echo (isset($instance['layout']) and $instance['layout'] == 0) ? 'selected="selected"' : ''; ?>><?php echo __('Default', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php echo (isset($instance['layout']) and $instance['layout'] == 1) ? 'selected="selected"' : ''; ?>><?php echo __('Above the header (Show Only in mobile Devices)', 'real-estate-listing-realtyna-wpl'); ?></option>
         </select>
    </div>
    <div class="wpl-widget-row">
        <input <?php if(isset($instance['data']['show_only_on_mobile']) and $instance['data']['show_only_on_mobile']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_show_only_on_mobile'); ?>" name="<?php echo $this->get_field_name('data'); ?>[show_only_on_mobile]" />
        <label for="<?php echo $this->get_field_id('data_show_only_on_mobile'); ?>" title="<?php echo __('Show Only in mobile Device above the header', 'real-estate-listing-realtyna-wpl'); ?>"><?php echo __('Show Only in mobile Device above the header', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('google_play_url'); ?>"><?php echo __('Google Play Store URL', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('google_play_url'); ?>" name="<?php echo $this->get_field_name('data'); ?>[google_play_url]" value="<?php echo isset($instance['data']['google_play_url']) ? $instance['data']['google_play_url'] : ''; ?>" />
    </div>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('app_store_url'); ?>"><?php echo __('Apple App Store URL', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('app_store_url'); ?>" name="<?php echo $this->get_field_name('data'); ?>[app_store_url]" value="<?php echo isset($instance['data']['app_store_url']) ? $instance['data']['app_store_url'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_css_class'); ?>" name="<?php echo $this->get_field_name('data'); ?>[css_class]" value="<?php echo isset($instance['data']['css_class']) ? $instance['data']['css_class'] : ''; ?>" />
    </div>
    
    <!-- Create a button to show Short-code of this widget -->
    <?php if(wpl_global::check_addon('pro') and !wpl_global::is_page_builder()): ?>
    <button id="<?php echo $this->get_field_id('btn-shortcode'); ?>"
            data-item-id="<?php echo $this->number; ?>"
            data-realtyna-lightbox-opts="clearContent:false"
            data-fancy-id="<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" class="wpl-open-lightbox-btn wpl-button button-1"
            href="#<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" data-realtyna-lightbox type="button"><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></button>
    
    <div id="<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" class="hidden">
        <div class="fanc-content size-width-1">
            <h2><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></h2>
            <div class="fanc-body fancy-search-body">
                <p class="wpl_widget_shortcode_preview"><?php echo '[wpl_widget_instance id="' . $this->id . '"]'; ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>