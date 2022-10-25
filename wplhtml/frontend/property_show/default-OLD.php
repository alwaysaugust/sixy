<?php


/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

// Google Map is set to don't disply by default. A click by user on Googlemap widget is needed to show the map
if(isset($this->settings['googlemap_display_status']) and !$this->settings['googlemap_display_status'] and !wpl_request::getVar('wplfmap', 0)) return;

//var_dump($this->wpl_properties['current']['data']);die; 
$prp_type           = isset($this->wpl_properties['current']['materials']['property_type']['value']) ? $this->wpl_properties['current']['materials']['property_type']['value'] : '';
$prp_listings       = isset($this->wpl_properties['current']['materials']['listing']['value']) ? $this->wpl_properties['current']['materials']['listing']['value'] : '';
$build_up_area      = isset($this->wpl_properties['current']['materials']['living_area']['value']) ? $this->wpl_properties['current']['materials']['living_area']['value'] : (isset($this->wpl_properties['current']['materials']['lot_area']['value']) ? $this->wpl_properties['current']['materials']['lot_area']['value'] : '');
$build_up_area_name = isset($this->wpl_properties['current']['materials']['living_area']['value']) ? $this->wpl_properties['current']['materials']['living_area']['name'] : (isset($this->wpl_properties['current']['materials']['lot_area']['value']) ? $this->wpl_properties['current']['materials']['lot_area']['name'] : '');
$bedroom            = isset($this->wpl_properties['current']['materials']['bedrooms']['value']) ? $this->wpl_properties['current']['materials']['bedrooms']['value'] : '';
$bathroom           = isset($this->wpl_properties['current']['materials']['bathrooms']['value']) ? $this->wpl_properties['current']['materials']['bathrooms']['value'] : '';
$listing_id         = isset($this->wpl_properties['current']['materials']['mls_id']['value']) ? $this->wpl_properties['current']['materials']['mls_id']['value'] : '';
$price              = isset($this->wpl_properties['current']['materials']['price']['value']) ? $this->wpl_properties['current']['materials']['price']['value'] : '';
$price_type         = isset($this->wpl_properties['current']['materials']['price_period']['value']) ? $this->wpl_properties['current']['materials']['price_period']['value'] : '';
$location_string 	= (isset($this->wpl_properties['current']['location_text']) and $this->location_visibility === true) ? $this->wpl_properties['current']['location_text'] : $this->location_visibility;
$prp_title          = isset($this->wpl_properties['current']['property_title']) ? $this->wpl_properties['current']['property_title'] : '';
$visits             = wpl_property::get_property_stats_item($this->pid, 'visit_time');
$add_date           = isset($this->wpl_properties['current']['raw']['add_date']) ? $this->wpl_properties['current']['raw']['add_date'] : '0000-00-00 00:00:00';
$garage_spaces      = isset($this->wpl_properties['current']['materials']['field_3052']['value']) ? $this->wpl_properties['current']['materials']['field_3052']['value'] : (isset($this->wpl_properties['current']['materials']['field_3052']['value']) ? $this->wpl_properties['current']['materials']['field_3052']['value'] : '');
$community_id       = isset($this->wpl_properties['current']['materials']['field_3013']['value']) ? $this->wpl_properties['current']['materials']['field_3013']['value'] : '';

/** Calculate how many visits per days **/
$days = 0;
if($add_date != '0000-00-00 00:00:00')
{
    $datetime1 = strtotime($add_date);
    $datetime2 = time();
    $interval = abs($datetime2 - $datetime1);
    $days = round($interval / 60 / 60 / 24);
}

if(wpl_global::check_addon('MLS') && $this->show_agent_name || $this->show_office_name )
{
    $office_name = isset($this->wpl_properties['current']['raw']['field_2111']) ? '<div class="wpl-prp-office-name"><label>'.$this->label_office_name.'</label><span>'.$this->wpl_properties['current']['raw']['field_2111'].'</span></div>' : '';
    $agent_name = isset($this->wpl_properties['current']['raw']['field_2112']) ? '<div class="wpl-prp-agent-name"> <label>'.$this->label_agent_name.'</label><span>'.$this->wpl_properties['current']['raw']['field_2112'].'</span></div>' : '';
}

$office_id = $this->wpl_properties['current']['raw']['field_2111'];
$lot_sqft_name = $this->wpl_properties['current']['materials']['field_3041']['name'];
$garage_spaces_name = $this->wpl_properties['current']['materials']['field_3052']['name'];
$lot_sqft_full = $this->wpl_properties['current']['raw']['field_3041'];
$lot_sqft = preg_replace("/[^0-9]/", "", $lot_sqft_full);

if ($community_id!=''){
    $community = 'The property is located in the '.$community_id.' community.';
}

$pshow_gallery_activities = wpl_activity::get_activities('pshow_gallery', 1);
$pshow_googlemap_activities = wpl_activity::get_activities('pshow_googlemap', 1, '', 'loadObject');
$pshow_walkscore_activities = wpl_activity::get_activities('pshow_walkscore', 1);
$pshow_bingmap_activities = wpl_activity::get_activities('pshow_bingmap', 1, '', 'loadObject');

$this->pshow_googlemap_activity_id = isset($pshow_googlemap_activities->id) ? $pshow_googlemap_activities->id : NULL;
$this->pshow_bingmap_activity_id = isset($pshow_bingmap_activities->id) ? $pshow_bingmap_activities->id : NULL;

/** video tab for showing videos **/
$pshow_video_activities = count(wpl_activity::get_activities('pshow_video', 1));
if(!isset($this->wpl_properties['current']['items']['video']) or (isset($this->wpl_properties['current']['items']['video']) and !count($this->wpl_properties['current']['items']['video']))) $pshow_video_activities = 0;

/** Import JS file **/
$this->_wpl_import($this->tpl_path.'.scripts.js', true, true);


$city = $this->wpl_properties['current']['raw']['location4_name'];
global $wpdb;
$prop_sfamily = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 3 AND location4_name = '$city'" );
$prop_detached = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 15 AND location4_name = '$city'" );
$prop_retail = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 6 AND location4_name = '$city'" );
$prop_business = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 4 AND location4_name = '$city'" );
$prop_other = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 5 AND location4_name = '$city'" );
$prop_condoapt = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 17 AND location4_name = '$city'" );
$prop_twnhs = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 19 AND location4_name = '$city'" );
$prop_industrial = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 1 AND location4_name = '$city'" );

// $test = $wpdb->get_var ( "SELECT COUNT(*) FROM wp_wpl_properties WHERE property_type = 3 AND location4_name = '$city'" );
// echo $test;die;
?>
<style>
    
</style>
<div class="wpl_prp_show_container" id="wpl_prp_show_container">
    <div class="wpl_prp_container" id="wpl_prp_container<?php echo $this->pid; ?>" <?php echo $this->itemscope.' '.$this->itemtype_SingleFamilyResidence; ?>>
        <div class="wpl_prp_show_tabs">
            <div class="tabs_container">
            	<?php if($pshow_gallery_activities): ?>
                <div id="tabs-1" class="tabs_contents">
                    <?php /** load position gallery **/ wpl_activity::load_position('pshow_gallery', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_googlemap_activities and $this->location_visibility === true): ?>
                <div id="tabs-2" class="tabs_contents">
                    <?php /** load position googlemap **/ wpl_activity::load_position('pshow_googlemap', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_video_activities): ?>
                <div id="tabs-3" class="tabs_contents">
                    <?php /** load position video **/ wpl_activity::load_position('pshow_video', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_bingmap_activities and $this->location_visibility === true): ?>
                <div id="tabs-4" class="tabs_contents">
                    <?php /** load position bingmap **/ wpl_activity::load_position('pshow_bingmap', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="tabs_box">
                <ul class="tabs">
                	<?php if($pshow_gallery_activities): ?>
                    <li><a href="#tabs-1" data-for="tabs-1"><?php echo __('Pictures', 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_googlemap_activities and $this->location_visibility === true): ?>
                    <li><a href="#tabs-2" data-for="tabs-2" data-init-googlemap="1"><?php echo __('Google Map', 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_video_activities): ?>
                    <li><a href="#tabs-3" data-for="tabs-3"><?php echo __('Video', 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_bingmap_activities and $this->location_visibility === true): ?>
                    <li><a href="#tabs-4" data-for="tabs-4" data-init-bingmap="1"><?php echo __("Bird's eye", 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="wpl_prp_container_content">
            <div class="wpl-row wpl-expanded wpl_prp_container_content_title">
                <?php
               
                echo '<div class="wpl-large-10 wpl-medium-10 wpl-small-12 wpl-columns">';
                echo '<h1 class="title_text" '.$this->itemprop_name.'>'.$prp_title.'</h1>';
                echo '<h2 class="location_build_up" '.$this->itemprop_address.' '.$this->itemscope.' '.$this->itemtype_PostalAddress.'><span class="wpl-location" '.$this->itemprop_addressLocality.'>'. $location_string .'</span></h2>';
                echo '</div>
                
                <div class="wpl-large-2 wpl-medium-2 wpl-small-12 wpl-columns">';
                /** load QR Code **/ wpl_activity::load_position('pshow_qr_code', array('wpl_properties'=>$this->wpl_properties));
                echo '</div>';
                ?>
            </div>
            <div class="wpl_prp_container_content_top clearfix">
                <?php /** listing result **/ wpl_activity::load_position('pshow_listing_results', array('wpl_properties'=>$this->wpl_properties)); ?>
            </div>
            <div class="wpl-row wpl-expanded">
				<div class="wpl-large-8 wpl-medium-7 wpl-small-12 wpl_prp_container_content_left wpl-column">
				<?php
                    $description_column = 'field_308';
                    if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, $this->kind)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);
                    
                    if(isset($this->wpl_properties['current']['data'][$description_column]) and $this->wpl_properties['current']['data'][$description_column]):
                ?>
                <div class="wpl_prp_show_detail_boxes wpl_category_description">
                    <div class="wpl_prp_show_detail_boxes_title"><?php echo __(wpl_flex::get_dbst_key('name', wpl_flex::get_dbst_id('field_308', $this->kind)), 'real-estate-listing-realtyna-wpl') ?></div>
                    <div class="wpl_prp_show_detail_boxes_cont" <?php echo $this->itemprop_description; ?>>
                        <?php echo apply_filters('the_content', stripslashes($this->wpl_properties['current']['data'][$description_column])); ?>
                    </div>
                </div>
                
                <?php endif; ?>
                
                <?php
                $i = 0;
                $details_boxes_num = count($this->wpl_properties['current']['rendered']);
                
                foreach($this->wpl_properties['current']['rendered'] as $values)
				{
				    
				  
                    /** skip empty categories **/
					if(!count($values['data'])) continue;
                    
                    /** skip location if property address is hiden **/
					if($values['self']['prefix'] == 'ad' and $this->location_visibility !== true) continue;
                    
                    echo '<div class="wpl_prp_show_detail_boxes wpl_category_'.$values['self']['id'].'">
                            <div class="wpl_prp_show_detail_boxes_title"><span>'.__($values['self']['name'], 'real-estate-listing-realtyna-wpl').'</span></div>
                            <div class="wpl-small-up-1 wpl-medium-up-1 wpl-large-up-'.$this->fields_columns.' wpl_prp_show_detail_boxes_cont">';
                   
                    foreach($values['data'] as $key => $value)
					{
                        if(!isset($value['type'])) continue;
                        
                        elseif($value['type'] == 'neighborhood')
                        {
                            
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows neighborhood">' .__($value['name'],'real-estate-listing-realtyna-wpl') .(isset($value['distance']) ? ' <span class="'.$value['vehicle_type'].'">'. $value['distance'] .' '. __('Minutes','real-estate-listing-realtyna-wpl'). '</span>':''). '</div>';
                        }
                        elseif($value['type'] == 'feature')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows feature ';
                            if(!isset($value['values'][0])) echo ' single ';
							
                            echo '">'.__($value['name'], 'real-estate-listing-realtyna-wpl');
							
                            if(isset($value['values'][0]))
                            {
                                $html = '';
                                echo ' : <span>';
                                foreach($value['values'] as $val) $html .= __($val, 'real-estate-listing-realtyna-wpl').', ';
                                $html = rtrim($html, ', ');
                                echo $html;
                                echo '</span>';
                            }
							
                            echo '</div>';
                        }
                        elseif($value['type'] == 'locations' and isset($value['locations']) and is_array($value['locations']))
                        {
                            
                            echo '<a class="map-gl-btn inner-map-gl-btn" href="javascript:;">View in Google Maps</a>';
                            echo '<span class="wpl_googlemap_container wpl_googlemap_plisting" id="wpl_googlemap_container15" data-wpl-height="250">
                                <div class="wpl-map-add-ons"></div>
                                <div class="wpl_map_canvas" id="wpl_map_canvas15" style="height: 250px;"></div>
                            </span>';
                            echo '<br>';
                            if(isset($value['settings']) and is_array($value['settings']))
                            {
                                foreach($value['settings'] as $ii=>$lvalue)
                                {
                                    if(isset($lvalue['enabled']) and !$lvalue['enabled']) continue;

                                    $lk = isset($value['keywords'][$ii]) ? $value['keywords'][$ii] : '';
                                    if(trim($lk) == '') continue;
                                    echo '<div id="wpl-dbst-show'.$value['field_id'].'-'.$lk.'" class="wpl-column rows location '.$lk.'">'.__($lk, 'real-estate-listing-realtyna-wpl').' : ';
                                    echo '<span>'.$value['locations'][$ii].'</span>';
                                    echo '</div>';
                                }
                            }
                            else
                            {
                                foreach($value['locations'] as $ii=>$lvalue)
                                {
                                    $lk = isset($value['keywords'][$ii]) ? $value['keywords'][$ii] : '';
                                    if(trim($lk) == '') continue;
                                    
                                    echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows location '.$lk.'">'.__($lk, 'real-estate-listing-realtyna-wpl').' : ';
                                    echo '<span>'.$lvalue.'</span>';
                                    echo '</div>';
                                }
                            }
                        }
                        elseif($value['type'] == 'separator')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows separator">' .__($value['name'], 'real-estate-listing-realtyna-wpl'). '</div>';
                        }
                        else echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows other">' .__($value['name'], 'real-estate-listing-realtyna-wpl'). ' : <span>'. __((isset($value['value']) ? $value['value'] : ''), 'real-estate-listing-realtyna-wpl') .'</span></div>';
                    }
					
                    echo '</div></div>';
                	$i++;
                }
                ?>
                
                <div class="wpl_prp_show_position3">
                    <?php
                        $activities = wpl_activity::get_activities('pshow_position3');
                        
                        foreach($activities as $activity)
                        {
                            $content = wpl_activity::render_activity($activity, array('wpl_properties'=>$this->wpl_properties));
                            if(trim($content) == '') continue;
                            
                            $activity_title =  explode(':', $activity->activity);
                            ?>
                            <div class="wpl_prp_position3_boxes <?php echo $activity_title[0]; ?>">
                                <?php
                                if($activity->show_title and trim($activity->title) != '')
                                {
                                    $activity_box_title = NULL;
                                    $title_parts = explode(' ', __(stripslashes($activity->title), 'real-estate-listing-realtyna-wpl'));
                                    $i_part = 0;

                                    foreach($title_parts as $title_part)
                                    {
                                        if($i_part == 0) $activity_box_title .= '<span>'.$title_part.'</span> ';
                                        else $activity_box_title .= $title_part.' ';

                                        $i_part++;
                                    }

                                    echo '<div class="wpl_prp_position3_boxes_title">'.$activity_box_title.'</div>';
                                }
                                ?>
                                <div class="wpl_prp_position3_boxes_content clearfix">
                                    <?php echo $content; ?>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            
			    <div class="wpl-large-4 wpl-medium-5 wpl-small-12 wpl_prp_container_content_right wpl-column">		
                <div class="wpl_prp_right_boxes details">
                    <div class="wpl_prp_right_boxes_title">       
                        <?php echo '<span>'.$prp_type .'</span> '.$prp_listings; ?> 
                    </div>
                    <div class="wpl_prp_right_boxes_content">
                        <div class="wpl_prp_right_boxe_details_top clearfix">
                            <div class="wpl_prp_right_boxe_details_left">
                                <ul>
                                <?php echo '<li class="prop_type"><span class="value">'.$prp_type.'</span><br>Property Type</li>' ?>
                                    <?php if(trim($bedroom) != ''): ?>
                                        <li class="wpl-bedroom" <?php echo $this->itemprop_numberOfRooms.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue ?>>
                                            <?php echo '<span '.$this->itemprop_value.' class="value">'.$bedroom.'</span>'. $this->itemprop_name; ?><?php echo __($this->wpl_properties['current']['materials']['bedrooms']['name'], 'real-estate-listing-realtyna-wpl').' </span>'; ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(trim($bathroom) != ''): ?>
                                        <li class="wpl-bathroom" <?php echo $this->itemprop_numberOfRooms.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue ?>>
                                            <?php echo '<span '.$this->itemprop_value.' class="value">'.$bathroom.'</span>'.$this->itemprop_name; ?><?php echo __($this->wpl_properties['current']['materials']['bathrooms']['name'], 'real-estate-listing-realtyna-wpl').' </span>'; ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(trim($garage_spaces) != ''): ?>
                                        <li class="wpl-garage-spaces">
                                       <?php echo '<span '.$this->itemprop_value.' class="value">'.$garage_spaces.'</span>'.$this->itemprop_name; ?><?php echo __($this->wpl_properties['current']['materials']['field_3052']['name'], 'real-estate-listing-realtyna-wpl').' </span>'; ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(trim($lot_sqft) != ''): ?>
                                        <li class="wpl-build-up-area" <?php echo $lot_sqft_name; ?>>
                                       <?php echo '<span '.$this->itemprop_value.' class="value">'.$lot_sqft_full.'</span>'.$this->itemprop_name; ?><?php echo __($this->wpl_properties['current']['materials']['field_3041']['name'], 'real-estate-listing-realtyna-wpl').' </span>'; ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($price_type): ?>
                                        <li class="wpl-price">
                                            <?php echo __($this->wpl_properties['current']['materials']['price_period']['name'], 'real-estate-listing-realtyna-wpl').' <span class="value">'.$price_type.'</span>'; ?>
                                        </li>
                                       
                                    <?php endif; ?>
                                    <?php if(wpl_global::get_setting('show_plisting_visits')): ?>
                                    <li class="wpl-property-visit">
                                        <?php echo __('Visits', 'real-estate-listing-realtyna-wpl').' : <span class="value">'.$visits.($days ? ' '.sprintf(__('in %d days', 'real-estate-listing-realtyna-wpl'), $days) : '').'</span>'; ?>
                                    </li>
                                    <?php endif; ?>
                                    <?php if(wpl_global::check_addon('MLS') && $this->show_agent_name || $this->show_office_name): ?>
                                        <div class="wpl-mls-brokerage-info">
                                            <?php if($this->show_agent_name) echo '<li>'.$agent_name.'</li>'; ?>
                                            <?php if($this->show_office_name) echo '<li>'.$office_name.'</li>'; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php //print_r ($this->wpl_properties['current']['materials']); ?>
                                </ul>
                            </div>
                            <div class="wpl_prp_right_boxe_details_right">
                                <?php /** load wpl_pshow_link activity **/ wpl_activity::load_position('wpl_pshow_link', array('wpl_properties'=>$this->wpl_properties)); ?>
                            </div>
                        </div>
                        <div class="wpl_prp_right_boxe_details_bot" <?php echo $this->itemscope.' '.$this->itemtype_offer; ?>>
                            <?php echo '<div class="price_box" '.$this->itemprop_price.'>'.$price.'</div><div class="property_details_intro">'.$this->itemprop_addressLocality. $location_string .' is '.$prp_listings.' and has a listing price of '.$price.'. '.$community.' Its closest intersection is [intersection]. Its been on the market for [days on market] days.</div>'; ?></div>
            <?php if(trim($listing_id) != ''): ?>
             <div class="wpl-listinginfo-right"><?php echo ' <div class="mls-id"><span class="value"><span class="bold-semi">MLS ID:</span> '.$listing_id.'</span></div>
             <div class="office-id"><span class="value"><span class="bold-semi">Listing Courtesy of:</span> '.$office_id.'</span></div>'; ?></div><?php endif; ?>    
              </div>
                </div>
                
                <div class="wpl_prp_show_position2">
                    <?php
                        $activities = wpl_activity::get_activities('pshow_position2');
                        foreach($activities as $activity)
                        {
                            $content = wpl_activity::render_activity($activity, array('wpl_properties'=>$this->wpl_properties));
                            if(trim($content) == '') continue;
                            
                            $activity_title =  explode(':', $activity->activity);
                            ?>
                            <div class="wpl_prp_right_boxes <?php echo $activity_title[0]; ?>">
                                <?php
                                if($activity->show_title and trim($activity->title) != '')
                                {
                                    $activity_box_title = NULL;
                                    $title_parts = explode(' ', __(stripslashes($activity->title), 'real-estate-listing-realtyna-wpl'));
                                    $i_part = 0;

                                    foreach($title_parts as $title_part)
                                    {
                                        if($i_part == 0) $activity_box_title .= '<span>'.$title_part.'</span> ';
                                        else $activity_box_title .= $title_part.' ';

                                        $i_part++;
                                    }

                                    echo '<div class="wpl_prp_right_boxes_title">'.$activity_box_title.'</div>';
                                }
                                ?>
                                <div class="wpl_prp_right_boxes_content clearfix">
                                    <?php echo $content; ?>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            </div>
            
            <div class="wpl_prp_show_bottom">
                <?php if($pshow_walkscore_activities): ?>
                
                <div class="wpl_prp_show_walkscore">
                    <?php /** load position walkscore **/ wpl_activity::load_position('pshow_walkscore', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <div style="float:right">


</div>





                <!------- Widget Code ---->
                    <div id="Property_Type_Distribution">
                        <div id="chart-property-types"><h3>Property Type Distribution</h3></div>
                    </div>
                    <div id="chart"><h3>Price Per Square Foot</h3></div>
                    <div id="chart-community">
                          <h3>Community Stats</h3>
                          <div class="chart-data-head-wrapper">
                            <div>
                              <span class="data">$1,000,000</span>
                              <br>
                              <span class="title">Median Price</span>
                            </div>
                            <div class="divider">
                              <span class="data">9</span>
                              <br>
                              <span class="title">New Listings</span>
                            </div>
                            <div>
                              <span class="data">3</span>
                              <br>
                              <span class="title">Median Days on Market</span>
                            </div>
                          </div>
                          <div class="chart-data-tile-wrapper">
                            <div class="chart-data-tile">
                              <div class="chart-data-tile-title">1 Year Change</div>
                              <div class="chart-data-tile-value">+40.3%</div>
                            </div>
                            <div class="divider"></div>
                            <div class="chart-data-tile">
                              <div class="chart-data-tile-title">1 Year Change</div>
                              <div class="chart-data-tile-value">+40.3%</div>
                            </div>
                            <div class="divider"></div>
                            <div class="chart-data-tile">
                              <div class="chart-data-tile-title">1 Year Change</div>
                              <div class="chart-data-tile-value">+40.3%</div>
                            </div>
                          </div>
                        </div>	
                    <div id="chart-temperature">
                      <h3>Market Temperature</h3>
                      <div class="hchart-wrapper">
                        <div class="hchart-marker">
                          <img src="/wp-content/uploads/sites/49/2022/05/icon_triangle-market-temp-chart.png" />
                        </div>
                        <div class="hchart-label">Buyer's Market</div>
                        <div class="hchart-label">Balanced</div>
                        <div class="hchart-label">Seller's Market</div>
                      </div>
                    </div>
                        
                    <script> 
                        
                        //chart-property-types
                        var options = {
                          series: [<? echo $prop_sfamily; ?>, <? echo $prop_detached; ?>, <? echo $prop_retail; ?>,<? echo $prop_business; ?>, <? echo $prop_condoapt; ?>,<? echo $prop_twnhs; ?>,<? echo $prop_industrial; ?>, <? echo $prop_other; ?>],
                          chart: {
                          type: 'donut',
                           legend: {
                            position: 'top',
                            horizontalAlign: 'left',
                            floating: true,
                            offsetY: -25,
                            offsetX: -5
                           }, 
                        },
                         labels: ['Single Family', 'Detached', 'Retail', 'Business', 'Condo Apt', 'Att/Row/Twnhouse', 'Industrial', 'Other'],
                        colors:[ '#7292AB','#63717D', '#AB7272','#AB72A7','#727BAB','#72ABAA'],
                        responsive: [{
                          breakpoint: 480,
                          options: {
                            chart: {
                              width: 200
                            },
                          }
                        }]
                        };
                        
                        jQuery(".wpl_category_6").remove();
                        var chart = new ApexCharts(document.querySelector("#chart-property-types"), options);
                        chart.render();
                        //property neighbourhood trend
                        
                        
                        //Price Per Square Foot
                         var options = {
                             series: [{
                                 name: 'Current Listing',
                                 data: [400]
                             }, {
                                 name: 'Neighbourhood Avg',
                                 data: [260]
                             }],
                             chart: {
                                 height: 350,
                                 type: 'area'
                             },
                             dataLabels: {
                                 enabled: false
                             },
                             stroke: {
                                 curve: 'smooth'
                             },
                             colors: ['#63717d', '#000'],
                             fill: {
                                 type: "gradient",
                                 gradient: {
                                     shadeIntensity: 0.6,
                                     opacityFrom: 0.6,
                                     opacityTo: 0.6,
                                     stops: [100, 100]
                                 }
                             },
                             markers: {
                                 size: 0,
                                 colors: ['#fff'],
                                 strokeColors: ['#63717d', '#000'],
                                 strokeWidth: 3,
                                 strokeOpacity: 1,
                                 strokeDashArray: 0,
                                 fillOpacity: 1,
                                 discrete: [],
                                 shape: "circle",
                                 radius: 2,
                                 offsetX: 0,
                                 offsetY: 0,
                                 onClick: undefined,
                                 onDblClick: undefined,
                                 showNullDataPoints: true,
                                 hover: {
                                     size: 7,
                                     sizeOffset: 3
                                 }
                             },
                             xaxis: {
                                 type: 'year',
                                 categories: ["2021"]
                        
                             },
                             yaxis: {
                                 opposite: true,
                                 axisTicks: {
                                     show: false
                                 },
                                 axisBorder: {
                                     show: true,
                                     color: "#eee"
                                 },
                                 labels: {
                                     align: 'right',
                                     style: {
                                         colors: ['#191919']
                                     },
                                     formatter: function(value) {
                        
                                         var val = value.toLocaleString("en-US", {
                                             maximumFractionDigits: 0,
                                             minimumFractionDigits: 0
                                         });
                        
                                         val = '$' + val
                        
                                         return val
                                     }
                                 }
                             },
                             tooltip: {
                                 x: {
                                     format: '$'
                                 },
                             },
                         };
                        
                         var chart = new ApexCharts(document.querySelector("#chart"), options);
                         chart.render();
                         
                         //chart-community
                         var options = {
                            series: [{
                                name: 'Detached',
                                data: [500000, 820005, 760000]
                            }, {
                                name: 'All properties',
                                data: [260000, 350000, 570000]
                            }],
                            chart: {
                                height: 350,
                                type: 'area'
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            colors: ['#63717d', '#000'],
                            fill: {
                                type: "gradient",
                                gradient: {
                                    shadeIntensity: 0.6,
                                    opacityFrom: 0.6,
                                    opacityTo: 0.6,
                                    stops: [100, 100]
                                }
                            },
                            markers: {
                                size: 0,
                                colors: ['#fff'],
                                strokeColors: ['#63717d', '#000'],
                                strokeWidth: 3,
                                strokeOpacity: 1,
                                strokeDashArray: 0,
                                fillOpacity: 1,
                                discrete: [],
                                shape: "circle",
                                radius: 2,
                                offsetX: 0,
                                offsetY: 0,
                                onClick: undefined,
                                onDblClick: undefined,
                                showNullDataPoints: true,
                                hover: {
                                    size: 7,
                                    sizeOffset: 3
                                }
                            },
                            xaxis: {
                                type: 'year',
                                categories: ["2019", "2020", "2021", "2022"]
                        
                            },
                            yaxis: {
                                opposite: true,
                                axisTicks: {
                                    show: false
                                },
                                axisBorder: {
                                    show: true,
                                    color: "#eee"
                                },
                                labels: {
                                    align: 'right',
                                    style: {
                                        colors: ['#191919']
                                    },
                                    formatter: function(value) {
                        
                                        var val = value.toLocaleString("en-US", {
                                            maximumFractionDigits: 0,
                                            minimumFractionDigits: 0
                                        });
                        
                                        val = '$' + val
                        
                                        return val
                                    }
                                }
                            },
                            tooltip: {
                                x: {
                                    format: '$'
                                },
                            },
                        };
                        
                        var chart = new ApexCharts(document.querySelector("#chart-community"), options);
                        chart.render();
                    </script>
                <!----------- widget Code End --> 
                
                <?php 
                if(is_active_sidebar('wpl-pshow-bottom')) dynamic_sidebar('wpl-pshow-bottom'); 
                ?>



                
            </div>
        </div>
    </div>
    <?php /** Don't remove this element **/ ?>
    <div id="wpl_pshow_lightbox_content_container" class="wpl-util-hidden"></div>
    
    <?php if(wpl_global::check_addon('membership') and wpl_session::get('wpl_dpr_popup')): ?>
    <a id="wpl_dpr_lightbox" class="wpl-util-hidden" data-realtyna-href="#wpl_pshow_lightbox_content_container" data-realtyna-lightbox-opts="title:<?php echo __('Login to continue', 'real-estate-listing-realtyna-wpl'); ?>"></a>
    <?php endif; ?>
    <?php if($this->show_signature): ?>
    <div class="wpl-powered-by-realtyna">
        <a href="https://realtyna.com/wpl-platform/ref/<?php echo $this->affiliate_id; ?>/">
            <img src="<?php echo wpl_global::get_wpl_url().'assets/img/idx/powered-by-realtyna.png'; ?>" alt="Powered By Realtyna" width="120"/>
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
    .wpl_prp_show_detail_boxes.wpl_category_2 .wpl_googlemap_container:not(.wpl_googlemap_carousel) .wpl_map_canvas {
    	width: 106%;
    	right: 20px;
    }
    
    .map-gl-btn {
    	position: absolute;
    	border-radius: 5px;
    	border: 1px solid rgba(21, 22, 26, 1);
    	opacity: 1;
    	background-color: rgba(255, 255, 255, 1);
    	padding: 6px 15px;
    	top: 25px;
    	right: 38px;
    }
    
    .inner-map-gl-btn {
    	opacity: 1;
    	color: rgba(21, 22, 26, 1);
    	font-family: "Inter";
    	font-size: 14px;
    	font-weight: 600;
    	font-style: normal;
    	text-align: center;
    }
    
    .wpl_prp_show_detail_boxes.wpl_category_2 {
    	position: relative;
    }
    
    a.map-gl-btn.inner-map-gl-btn:hover {
    	background: #15161a;
    	color: white;
    }
    
    .col-12.col-md-3.nbh-counter-main {
    	border: 1px solid #EBEBEB;
    	border-top: 0;
    	border-right: 0;
    	padding: 18px 19px;
    	background: #F8F8F8;
    }
    
    .col-12.col-md-3.nbh-counter-main:last-child {
    	/*border-right: 1px solid #EBEBEB;*/
    	/*border-left: 0;*/
    	background: #F8F8F8;
    }
    
    .col-12.col-md-3.nbh-counter-main:first-child {
    	border-right: 1px solid #EBEBEB;
    	border-left: 0;
    	background: #F8F8F8;
    }
    
    .col-12.col-md-3.nbh-counter-main-sec {
    	border: 1px solid #EBEBEB;
    	border-right: 0;
    	border-top: 0;
    	border-bottom: 0;
    	padding: 18px 19px;
    	background: #F8F8F8;
    }
    
    .col-12.col-md-3.nbh-counter-main-sec:last-child {
    	/*border-right: 1px solid #EBEBEB;*/
    	/*border-left: 0;*/
    	background: #F8F8F8;
    }
    
    .col-12.col-md-3.nbh-counter-main-sec:first-child {
    	border-right: 1px solid #EBEBEB;
    	border-left: 0;
    	background: #F8F8F8;
    }
    
    span.count-descripts {
    	font-size: 12px !important;
    	font-weight: 600;
    	font-family: 'Inter';
    	line-height: 0.12;
    	color: #63717D;
    }
    
    span.count-descripts-span {
    	font-size: 11px !important;
    	/*font-weight: 600;*/
    	font-family: 'Inter' !important;
    	line-height: 0.12;
    	color: #63717D;
    }
    
    span.nbh-bold {
    	font-size: 18px !important;
    	font-family: 'Inter' !important;
    	font-weight: 600;
    	line-height: 1.768;
    	color: #191919;
    }
    
    .nb-hr {
    	position: relative;
    	border-top: 1px solid #EBEBEB;
    	width: 106.2%;
    	right: 21px;
    	height: 30px;
    }
    
    .loader {
    	border: 16px solid #f3f3f3;
    	border-radius: 50%;
    	border-top: 16px solid #3498db;
    	width: 120px;
    	height: 120px;
    	-webkit-animation: spin 2s linear infinite;
    	/* Safari */
    	animation: spin 2s linear infinite;
    }
    
    /* Safari */
    @-webkit-keyframes spin {
    	0% {
    		-webkit-transform: rotate(0deg);
    	}
    
    	100% {
    		-webkit-transform: rotate(360deg);
    	}
    }
    
    @keyframes spin {
    	0% {
    		transform: rotate(0deg);
    	}
    
    	100% {
    		transform: rotate(360deg);
    	}
    }
    
    #chart-property-types .apexcharts-legend-series {
    	border-bottom: 1px solid #ebebeb;
    }
    
    #chart-property-types .apexcharts-tooltip-title,
    #chart-property-types .apexcharts-tooltip-text-y-label {
    	color: #fff !important;
    }
    
    #chart-property-types .apexcharts-tooltip-text-y-label {
    	margin-left: 0px !important;
    }
    
    #chart-property-types .apexcharts-tooltip-text-y-value {
    	color: #ddd !important;
    }
    
    div.apexcharts-toolbar {
    	display: none !important;
    }
    
    span.apexcharts-legend-text {
    	color: #63717d !important;
    	font-weight: 600 !important;
    	font-size: 12px !important;
    	padding-top: 1px;
    	padding-left: 3px;
    }
    
    span.apexcharts-legend-marker {
    	height: 9px !important;
    	width: 9px !important;
    }
    
    .apexcharts-tooltip {
    	font-size: 12px !important;
    	font-weight: 600 !important;
    	border: 1px solid #ebebeb !important;
    }
    
    .apexcharts-tooltip-title {
    	background-color: #f8f8f8 !important;
    	border-bottom: 1px solid #ebebeb !important;
    }
    
    .apexcharts-tooltip-title,
    .apexcharts-tooltip-text-y-label {
    	color: #63717d !important;
    }
    
    .apexcharts-tooltip-text-y-label {
    	margin-left: -5px !important;
    }
    
    .apexcharts-tooltip-text-y-value {
    	color: #191919 !important;
    }
    
    .apexcharts-tooltip-marker {
    	height: 9px !important;
    	width: 9px !important;
    }
    
    div#chart g text,
    div#chart2 g text,
    div#chart3 g text,
    div#chart-community g text {
    	font-size: 13px !important;
    	color: #191919 !important;
    	font-weight: 400 !important;
    	font-family: 'Inter', sans-serif !important;
    	letter-spacing: -0.5px;
    }
    
    div.apexcharts-toolbar {
    	display: none !important;
    }
    
    span.apexcharts-legend-text {
    	color: #63717d !important;
    	font-weight: 600 !important;
    	font-size: 12px !important;
    	padding-top: 1px;
    	padding-left: 3px;
    	line-height: 3.2em;
    }
    
    span.apexcharts-legend-marker {
    	height: 9px !important;
    	width: 9px !important;
    }
    
    .apexcharts-tooltip {
    	font-size: 12px !important;
    	font-weight: 600 !important;
    	border: 1px solid #ebebeb !important;
    }
    
    .apexcharts-tooltip-title {
    	background-color: #f8f8f8 !important;
    	border-bottom: 1px solid #ebebeb !important;
    }
    
    .apexcharts-tooltip-title,
    .apexcharts-tooltip-text-y-label {
    	color: #63717d !important;
    }
    
    .apexcharts-tooltip-text-y-label {
    	margin-left: -5px !important;
    }
    
    .apexcharts-tooltip-text-y-value {
    	color: #191919 !important;
    }
    
    .apexcharts-tooltip-marker {
    	height: 9px !important;
    	width: 9px !important;
    }
    
    div#chart g text,
    div#chart2 g text,
    div#chart3 g text,
    div#chart-property-types g text {
    	font-size: 13px !important;
    	color: #191919 !important;
    	font-weight: 400 !important;
    	font-family: 'Inter', sans-serif !important;
    	box-shadow: none !important;
    }
    
    .hchart-wrapper {
    	width: 646px;
    	height: 45px;
    	margin-bottom: 16px;
    	border-radius: 22px;
    	background: rgb(114, 171, 170);
    	background: linear-gradient(90deg, rgba(114, 171, 170, 1) 0%, rgba(114, 146, 171, 1) 33%, rgba(114, 146, 171, 1) 66%, rgba(171, 114, 114, 1) 100%);
    }
    
    .hchart-label {
    	width: 33%;
    	color: #fff;
    	float: left;
    	text-align: center;
    	height: 45px;
    	line-height: 45px;
    	font-weight: 700;
    }
    
    .hchart-marker {
    	position: absolute;
    	width: 620px;
    }
    
    .hchart-marker img {
    	margin-left: 50%;
    	margin-top: 36px;
    	float: left;
    }
    
    .gmnoprint {
        visibility: hidden;
    }
    
</style>

