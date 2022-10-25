<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$description_column = 'field_308';
if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, $this->kind)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);

foreach($this->wpl_properties as $key=>$property)
{
    if($key == 'current') continue;

    /** unset previous property **/
    unset($this->wpl_properties['current']);

    /** set current property **/
    $this->wpl_properties['current'] = $property;
    $listing_id = isset($property['materials']['mls_id']) ? '<label>'.__($property['materials']['mls_id']['name'], 'real-estate-listing-realtyna-wpl').'</label><span class="bedroom">'.$property['materials']['mls_id']['value'].'</span>' : '';
    
    $room       = isset($property['materials']['bedrooms']) ? '<label '.$this->itemprop_name.'>'.__($property['materials']['bedrooms']['name'], 'real-estate-listing-realtyna-wpl').'</label><span class="bedroom" '.$this->itemprop_value.'>'.$property['materials']['bedrooms']['value'].'</span>' : '';
    if((!isset($property['materials']['bedrooms']) or (isset($property['materials']['bedrooms']) and $property['materials']['bedrooms']['value'] == 0)) and (isset($property['materials']['rooms']) and $property['materials']['rooms']['value'] != 0)) $room = '<label '.$this->itemprop_name.'>'.__($property['materials']['rooms']['name'], 'real-estate-listing-realtyna-wpl').'</label><span class="room" '.$this->itemprop_value.'>'.$property['materials']['rooms']['value'].'</span>';
    
    $bathroom   = isset($property['materials']['bathrooms']) ? '<label '.$this->itemprop_name.'>'.__($property['materials']['bathrooms']['name'], 'real-estate-listing-realtyna-wpl').'</label><span class="bathroom" '.$this->itemprop_value.'>'.$property['materials']['bathrooms']['value'].'</span>' : '';
    $builtup_area   = isset($property['materials']['living_area']) ? '<label>'.__($property['materials']['living_area']['name'], 'real-estate-listing-realtyna-wpl').'</label><span class="builtup_area" '.$this->itemprop_floorSize.'>'.$property['materials']['living_area']['value'].'</span>' : '';
    $floor      = (isset($property['materials']['field_55']) and $property['materials']['field_55']['value']) ? '<label>'.__($property['materials']['field_55']['name'], 'real-estate-listing-realtyna-wpl').'</label><span class="floor_number">'.$property['materials']['field_55']['value'].'</span>' : '';
    $balcony    = isset($property['raw']['f_136']) ? '<label>'.__('Balcony', 'real-estate-listing-realtyna-wpl').'</label><span class="floor_number">'.($property['raw']['f_136'] ? __('Yes', 'real-estate-listing-realtyna-wpl') : __('No', 'real-estate-listing-realtyna-wpl')).'</span>' : '';
    $parking    = (isset($property['materials']['f_150']) and isset($property['materials']['f_150']['values'])) ? '<label>'.__('Parking', 'real-estate-listing-realtyna-wpl').'</label><span class="parking">'.implode(',', $property['materials']['f_150']['values']).'</span>' : '';
    $pic_count  = '<div class="pic_count">'.$property['raw']['pic_numb'].'</div>';

    // Set the View in Parent stat
    wpl_property::add_property_stats_item($property['data']['id'], 'view_in_parent_numb');

    $description = stripslashes(strip_tags($property['raw'][$description_column]));
    $cut_position = (trim($description) ? strrpos(substr($description, 0, 400), '.', -1) : 0);
    if(!$cut_position) $cut_position = 399;
    ?>
    <div class="wpl-complex-unit-cnt wpl-large-4 wpl-medium-6 wpl-small-6 left wpl-column  <?php echo ((isset($this->property_css_class) and in_array($this->property_css_class, array('row_box', 'grid_box'))) ? $this->property_css_class : ''); ?>" id="wpl_prp_cont<?php echo $property['data']['id']; ?>" <?php echo $this->itemscope.' '.$this->itemtype_Apartment ?>>
       <div class="wpl-complex-unit-wp clearfix">
           <div class="wpl-complex-unit-top">
               <div class="wpl-complex-unit-top-boxes front">
                   <?php wpl_activity::load_position('wpl_property_listing_image', array('wpl_properties'=>$this->wpl_properties)); ?>
               </div>
           </div>
           <div class="wpl-complex-unit-bot">
               <?php
               echo '<a id="prp_link_id_'.$property['data']['id'].'_view_detail" href="'.$property['property_link'].'" class="view_detail" title="'.$property['property_title'].'">
              <h5 class="wpl-complex-unit-title" '.$this->itemprop_name.'>'.$property['property_title'].'</h5></a>';
               ?>
               <div class="wpl-complex-unit-details">
                   <ul class="wpl-row">
                       <?php
                           if($listing_id) echo '<li class="wpl-large-6 wpl-column">'.$listing_id.'</li>';
                           if($room) echo '<li class="wpl-large-6 wpl-column" '.$this->itemprop_numberOfRooms.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue.'>'.$room.'</li>';
                           if($bathroom) echo '<li class="wpl-large-6 wpl-column" '.$this->itemprop_numberOfRooms.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue.'>'.$bathroom.'</li>';
                           if($builtup_area) echo '<li class="wpl-large-6 wpl-column" '.$this->itemtype_QuantitativeValue.'>'.$builtup_area.'</li>';
                           if($floor) echo '<li class="wpl-large-6 wpl-column">'.$floor.'</li>';
                           if($balcony) echo '<li class="wpl-large-6 wpl-column">'.$balcony.'</li>';
                           if($parking) echo '<li class="wpl-large-6 wpl-column">'.$parking.'</li>';
                       ?>
                   </ul>
                   <span class="wpl-complex-unit-price"></span>
                   <a <?php echo $this->itemprop_url; ?> id="prp_link_id_<?php echo $property['data']['id']; ?>" href="<?php echo $property['property_link']; ?>" class="wpl-complex-unit-view-detail"></a>
                   <?php if(isset($property['materials']['price'])): ?><div class="wpl-complex-unit-price-box"><span content="<?php echo $property['materials']['price']['value']; ?>"><?php echo $property['materials']['price']['value']; ?></span></div><?php endif; ?>
               </div>
           </div>
       </div>
    </div>
    <?php
}