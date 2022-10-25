<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_neighborhoods');
$this->neghborhoods_addon = new wpl_addon_neighborhoods();

$description_column = 'field_308';
if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, $this->kind)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);

foreach($this->wpl_properties as $key=>$property)
{
    if($key == 'current') continue;

    /** unset previous property **/
    unset($this->wpl_properties['current']);

    /** set current property **/
    $this->wpl_properties['current'] = $property;
    
    $total_properties = $this->neghborhoods_addon->get_property_count($property['data']['id'], $this->settings['neighborhood_listings_mode']);
    $total = ($total_properties) ? '<div class="total_listings" '.$this->itemprop_additionalProperty.' '.$this->itemscope.' '.$this->itemtype_PropertyValue.'><span class="value" '.$this->itemprop_value.'>'.$total_properties.'</span><span class="label" '.$this->itemprop_name.'>'.__("Properties",'real-estate-listing-realtyna-wpl').'</span></div>' : '';
	$pic_count  = (intval($property['raw']['pic_numb'])) ? '<div class="pic_count" '.$this->itemprop_additionalProperty.' '.$this->itemscope.' '.$this->itemtype_PropertyValue.'><span class="value" '.$this->itemprop_value.'>'.$property['raw']['pic_numb'].'</span><span class="label" '.$this->itemprop_name.'>'.__("Pictures",'real-estate-listing-realtyna-wpl').'</span></div>' : '';
    
    $description = stripslashes(strip_tags($property['raw'][$description_column]));
    $cut_position = (trim($description) ? strrpos(substr($description, 0, 400), '.', -1) : 0);
    if(!$cut_position) $cut_position = 399;
    ?>
	<div class="wpl-column">
		<div class="wpl_prp_cont <?php echo ((isset($this->property_css_class) and in_array($this->property_css_class, array('grid_box', 'row_box'))) ? $this->property_css_class : ''); ?>" id="wpl_prp_cont<?php echo $property['data']['id']; ?>" <?php echo $this->itemscope.' '.$this->itemtype_Place; ?>>
			<div class="wpl_prp_top">
				<div class="wpl_prp_top_boxes front">
					<?php wpl_activity::load_position('wpl_property_listing_image', array('wpl_properties'=>$this->wpl_properties)); ?>
				</div>
				<div class="wpl_prp_top_boxes back">
					<a id="prp_link_id_<?php echo $property['data']['id']; ?>" href="<?php echo $property['property_link']; ?>" class="view_detail"><?php echo __('More Details', 'real-estate-listing-realtyna-wpl'); ?></a>
				</div>
			</div>
			<div class="wpl_prp_bot">
				<?php echo '<a id="prp_link_id_'.$property['data']['id'].'_view_detail" href="'.$property['property_link'].'" class="view_detail" title="'.$property['property_title'].'"><span class="wpl_prp_title">'.$property['property_title'].'</span></a>';?>
				<div class="wpl_prp_listing_location" <?php echo $this->itemprop_address.''.$this->itemscope.' '.$this->itemtype_PostalAddress; ?>><a id="prp_link_id_<?php echo $property['data']['id']; ?>" href="<?php echo $property['property_link']; ?>" class="view_detail" <?php echo $this->itemprop_addressLocality;?>><?php echo $property['location_text']; ?></a></div>
				<div class="wpl_prp_desc" <?php echo $this->itemprop_description;?>><?php echo substr($description, 0, $cut_position + 1); ?></div>
				<div class="wpl_prp_listing_icon_box"><?php echo $total . $pic_count; ?></div>
			</div>
			<?php if(isset($property['materials']['price'])): ?><div class="price_box"><span content="<?php echo $property['materials']['price']['value']; ?>"><?php echo $property['materials']['price']['value']; ?></span></div><?php endif; ?>
		</div>
	</div>
    <?php
}