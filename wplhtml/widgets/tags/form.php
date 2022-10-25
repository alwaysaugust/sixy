<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_tags_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_tags_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="widefat">
            <?php echo $this->generate_layouts_selectbox('tags', $instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('wpltarget'); ?>"><?php echo __('Target page', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('wpltarget'); ?>" name="<?php echo $this->get_field_name('wpltarget'); ?>">
            <option value="">-----</option>
	        <?php echo $this->generate_pages_selectbox($instance); ?>
        </select>
    </div>
    
    <?php $kinds = wpl_flex::get_kinds('wpl_properties'); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_kind'); ?>"><?php echo __('Kind', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_kind'); ?>" name="<?php echo $this->get_field_name('data'); ?>[kind]">
            <?php foreach($kinds as $kind): ?>
            <option <?php if(isset($instance['data']['kind']) and $instance['data']['kind'] == $kind['id']) echo 'selected="selected"'; ?> value="<?php echo $kind['id']; ?>"><?php echo __($kind['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $tag_categories = json_decode(wpl_global::get_setting('tags_categories'), true); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_category'); ?>"><?php echo __('Category', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_category'); ?>" name="<?php echo $this->get_field_name('data'); ?>[category]">
            <option value="">----</option>
            <?php foreach($tag_categories as $tag_category): ?>
            <option value="<?php echo $tag_category; ?>" <?php echo ((isset($instance['data']['category']) and $instance['data']['category'] == $tag_category) ? 'selected="selected"' : ''); ?>><?php echo __($tag_category, 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_css_class'); ?>" name="<?php echo $this->get_field_name('data'); ?>[css_class]" value="<?php echo isset($instance['data']['css_class']) ? $instance['data']['css_class'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_show_count'); ?>"><?php echo __('Show Count', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_show_count'); ?>" name="<?php echo $this->get_field_name('data'); ?>[show_count]">
            <option <?php if(isset($instance['data']['show_count']) and $instance['data']['show_count'] == 1) echo 'selected="selected"'; ?> value="1"><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option <?php if(isset($instance['data']['show_count']) and $instance['data']['show_count'] == 0) echo 'selected="selected"'; ?> value="0"><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
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