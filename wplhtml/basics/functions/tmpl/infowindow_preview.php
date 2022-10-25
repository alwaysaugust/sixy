<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.images');

$image_width = isset($image_width) ? $image_width : 180;
$image_height = isset($image_height) ? $image_height : 125;

$click_popup = 0;
if(count($this->wpl_properties) > 1) $click_popup = 1;

/*Agent and office name for mls compliance*/
$show_agent_name = wpl_global::get_setting('show_agent_name');
$show_office_name = wpl_global::get_setting('show_listing_brokerage');
    
foreach($this->wpl_properties as $key=>$property)
{
	$property_id = $property['data']['id'];
	$kind = $property['data']['kind'];
	$locations	 = $property['location_text'];

	// Get blog ID of property
	$blog_id = wpl_property::get_blog_id($property_id);

	$room = isset($property['materials']['bedrooms']) ? '<span class="wpl-infowindow-preview-bedroom">'.$property['materials']['bedrooms']['value'].'</span>' : '';
	if((!isset($property['materials']['bedrooms']) or (isset($property['materials']['bedrooms']) and $property['materials']['bedrooms']['value'] == 0)) and (isset($property['materials']['rooms']) and $property['materials']['rooms']['value'] != 0)) $room = '<span class="wpl-infowindow-preview-bedroom">'.$property['materials']['rooms']['value'].'</span>';

	$bathroom = isset($property['materials']['bathrooms']) ? '<span class="wpl-infowindow-preview-bathroom">'.$property['materials']['bathrooms']['value'].'</span>' : '';
    
    if($click_popup) $price = '<div class="wpl-infowindow-preview-price"><a href="javascript:void(0)" onclick="return wpl_property_preview_html('.$property_id.');">'.$property['materials']['price']['value'].'</a></div>';
    else $price = '<div class="wpl-infowindow-preview-price">'.$property['materials']['price']['value'].'</div>';
   
	$sqft = isset($property['rendered'][10]) ? '<div class="wpl-infowindow-preview-sqft">'.$property['rendered'][10]['value'].'</div>' : '';

	$office_name = $agent_name = '';
	if(wpl_global::check_addon('MLS') and ($show_agent_name or $show_office_name))
	{
		$office_name = isset($property['raw']['field_2111']) ? '<div class="wpl-prp-office-name">'.$property['raw']['field_2111'].'</div>' : '';
		$agent_name = isset($property['raw']['field_2112']) ? '<div class="wpl-prp-agent-name">'.$property['raw']['field_2112'].'</div>' : '';
	}

	?>
	<div id="main_infowindow_preview" class="clearfix">
			<div class="main_infowindow_l sub-div">
				<?php
				if(isset($property['items']['gallery'][0]))
				{
					$gallery = $property['items']['gallery'][0];
					$property_path = wpl_items::get_path($property_id, $kind, $blog_id);

					$params = array();
					$params['image_name'] = $gallery->item_name;
					$params['image_parentid'] = $gallery->parent_id;
					$params['image_parentkind'] = $gallery->parent_kind;
					$params['image_source'] = $property_path.$gallery->item_name;

					/** resize image if does not exist **/
					if(isset($gallery->item_cat) and $gallery->item_cat != 'external') $image_url = wpl_images::create_gallery_image($image_width, $image_height, $params);
					else $image_url = $gallery->item_extra3;

					echo '<img itemprop="image" id="wpl_gallery_image'.$property_id .'_0" src="'.$image_url.'" class="wpl_gallery_image" width="'.$image_width.'" height="'.$image_height.'" style="width: '.$image_width.'px; height: '.$image_height.'px;" />';
				}
				else echo '<div class="no_image_box"></div>';
				?>
			</div>
			<div class="main_infowindow_r sub-div">
				<?php echo $price; ?>
				<?php echo '<div class="wpl-infowindow-preview-icons">'.$room.$bathroom.'</div>'; ?>
				<?php echo $sqft; ?>
				<?php
				if($show_agent_name) echo $agent_name;
				if($show_office_name) echo $office_name;
				?>
			</div>
		</div>
<?php } ?>

