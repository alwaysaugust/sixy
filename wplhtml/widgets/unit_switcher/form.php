<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_unit_switcher_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_unit_switcher_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </div>
    
    <?php $layouts = wpl_folder::files(WPL_ABSPATH. 'views' .DS. 'activities' .DS. 'unit_switcher' .DS. 'tmpl', '.php', false, false); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="widefat">
            <?php foreach($layouts as $layout): $layout = str_replace('.php', '', $layout); ?>
            <option <?php if($layout == $instance['layout']) echo 'selected="selected"'; ?> value="<?php echo $layout; ?>"><?php echo $layout; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $unit_types = wpl_units::get_unit_types(); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_unit_type'); ?>"><?php echo __('Unit Type', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_unit_type'); ?>" name="<?php echo $this->get_field_name('data'); ?>[unit_type]">
            <?php foreach($unit_types as $unit_type): ?>
            <option <?php if(isset($instance['data']['unit_type']) and $instance['data']['unit_type'] == $unit_type['id']) echo 'selected="selected"'; ?> value="<?php echo $unit_type['id']; ?>"><?php echo __($unit_type['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
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