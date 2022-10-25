<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$nlisting_title = wpl_request::getVar('ntitle', NULL);
$sort_options = wpl_sort_options::get_sort_options($this->kind, 1);

?>
<div class="wpl_sort_options_container">
    <h3><?php echo $nlisting_title ? $nlisting_title : __('All Communities', 'real-estate-listing-realtyna-wpl'); ?></h3>
    
    <?php if(count($sort_options)): ?>
        <div class="wpl_sort_options_container_title"><?php echo __('Sort Option', 'real-estate-listing-realtyna-wpl'); ?></div>
        <div class="wpl-sort-options-list <?php if ($this->wpl_listing_sort_type == 'list') echo 'active'; ?> <?php if ($this->wpl_listing_sort_type == 'dropdown') echo 'wpl-util-hidden'; ?>"><?php echo $this->model->generate_sorts(array('type'=>1, 'kind'=>$this->kind, 'sort_options'=>$sort_options)); ?></div>
        <span class="wpl-sort-options-selectbox <?php if ($this->wpl_listing_sort_type == 'dropdown') echo 'active'; ?> <?php if ($this->wpl_listing_sort_type == 'list') echo 'wpl-util-hidden'; ?>"><?php echo $this->model->generate_sorts(array('type'=>0, 'kind'=>$this->kind, 'sort_options'=>$sort_options)); ?></span>
    <?php endif; ?>

    <?php if($this->property_css_class_switcher): ?>
    <div class="wpl_list_grid_switcher <?php if($this->switcher_type == "icon+text") echo 'wpl-list-grid-switcher-icon-text'; ?>">
        <div id="grid_view" class="<?php echo ($this->switcher_type == "icon") ? 'wpl-tooltip-top ' : ''; ?>grid_view <?php if($this->property_css_class == 'grid_box') echo 'active'; ?>">
            <?php if($this->switcher_type == "icon+text") echo '<span>'.__('Grid', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
        </div>
        <?php if ($this->switcher_type == "icon"): ?>
            <div class="wpl-util-hidden"><?php _e('Grid', 'real-estate-listing-realtyna-wpl') ?></div>
        <?php endif; ?>
        <div id="list_view" class="<?php echo ($this->switcher_type == "icon") ? 'wpl-tooltip-top ' : ''; ?>list_view <?php if($this->property_css_class == 'row_box') echo 'active'; ?>">
            <?php if($this->switcher_type == "icon+text") echo '<span>'.__('List', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
        </div>
        <?php if ($this->switcher_type == "icon"): ?>
            <div class="wpl-util-hidden"><?php _e('List', 'real-estate-listing-realtyna-wpl') ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
</div>

<div class="wpl-row wpl-expanded <?php if($this->property_css_class == "grid_box") echo "wpl-small-up-1 wpl-medium-up-2 wpl-large-up-".$this->listing_columns; ?>  wpl_property_listing_listings_container clearfix">
	<?php echo $this->properties_str; ?>
</div>
<?php if($this->wplpagination != 'scroll'): ?>
<div class="wpl_pagination_container">
    <?php echo $this->pagination->show(); ?>
</div>
<?php endif; ?>