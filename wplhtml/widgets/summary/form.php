<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_summary_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_summary_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="widefat">
            <?php echo $this->generate_layouts_selectbox('summary', $instance); ?>
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
    
    <?php $kinds = wpl_flex::get_kinds('wpl_properties'); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('kind'); ?>"><?php echo __('Kind', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('kind'); ?>" name="<?php echo $this->get_field_name('data'); ?>[kind]">
            <?php foreach($kinds as $kind): ?>
            <option <?php if(isset($instance['data']['kind']) and $instance['data']['kind'] == $kind['id']) echo 'selected="selected"'; ?> value="<?php echo $kind['id']; ?>"><?php echo __($kind['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $listings = wpl_global::get_listings(); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('listing'); ?>"><?php echo __('Listing', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('listing'); ?>" name="<?php echo $this->get_field_name('data'); ?>[listing]">
            <?php foreach($listings as $listing): ?>
            <option <?php if(isset($instance['data']['listing']) and $instance['data']['listing'] == $listing['id']) echo 'selected="selected"'; ?> value="<?php echo $listing['id']; ?>"><?php echo __($listing['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $filters = wpl_flex::get_fields(NULL, 0, 0, '', '', "AND `enabled`>='1' AND `kind`='".$this->kind."' AND `type`='select'"); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('filter'); ?>"><?php echo __('Filter', 'real-estate-listing-realtyna-wpl'); ?></label>
        <?php if(count($filters)): ?>
        <select id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('data'); ?>[filter]">
            <?php foreach($filters as $filter): ?>
            <option <?php if(isset($instance['data']['filter']) and $instance['data']['filter'] == $filter->id) echo 'selected="selected"'; ?> value="<?php echo $filter->id; ?>"><?php echo __($filter->name, 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
        <?php else: ?>
        <div><?php echo __('No field found for filtering!', 'real-estate-listing-realtyna-wpl'); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('skip_zero'); ?>"><?php echo __('Skip Zero Results', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('skip_zero'); ?>" name="<?php echo $this->get_field_name('data'); ?>[skip_zero]">
            <option <?php if(isset($instance['data']['skip_zero']) and $instance['data']['skip_zero'] == 0) echo 'selected="selected"'; ?> value="0"><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option <?php if(isset($instance['data']['skip_zero']) and $instance['data']['skip_zero'] == 1) echo 'selected="selected"'; ?> value="1"><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <?php $units = wpl_units::get_units(4); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('unit_id'); ?>"><?php echo __('Currency Unit', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('unit_id'); ?>" name="<?php echo $this->get_field_name('data'); ?>[unit_id]">
            <?php foreach($units as $unit): ?>
            <option <?php if(isset($instance['data']['unit_id']) and $instance['data']['unit_id'] == $unit['id']) echo 'selected="selected"'; ?> value="<?php echo $unit['id']; ?>"><?php echo __($unit['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- Create a button to show Short-code of this widget -->
    <?php if(wpl_global::check_addon('pro') and !wpl_global::is_page_builder()): ?>
        <button id="<?php echo $this->get_field_id('btn-shortcode'); ?>"
                data-item-id="<?php echo $this->number; ?>"
                data-fancy-id="<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" class="wpl-open-lightbox-btn wpl-button button-1"
                href="#<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" type="button"><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></button>
    
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