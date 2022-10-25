<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access'); 

$prp_type = isset($this->wpl_properties['current']['materials']['property_type']['value']) ? $this->wpl_properties['current']['materials']['property_type']['value'] : '';
$prp_listings = isset($this->wpl_properties['current']['materials']['listing']['value']) ? $this->wpl_properties['current']['materials']['listing']['value'] : '';
$build_up_area = isset($this->wpl_properties['current']['materials']['living_area']['value']) ? $this->wpl_properties['current']['materials']['living_area']['value'] : (isset($this->wpl_properties['current']['materials']['lot_area']['value']) ? $this->wpl_properties['current']['materials']['lot_area']['value'] : '');
$build_up_area_name = isset($this->wpl_properties['current']['materials']['living_area']['value']) ? $this->wpl_properties['current']['materials']['living_area']['name'] : (isset($this->wpl_properties['current']['materials']['lot_area']['value']) ? $this->wpl_properties['current']['materials']['lot_area']['name'] : '');
$bedroom = isset($this->wpl_properties['current']['materials']['bedrooms']['value']) ? $this->wpl_properties['current']['materials']['bedrooms']['value'] : '';
$bathroom = isset($this->wpl_properties['current']['materials']['bathrooms']['value']) ? $this->wpl_properties['current']['materials']['bathrooms']['value'] : '';
$listing_id = isset($this->wpl_properties['current']['materials']['mls_id']['value']) ? $this->wpl_properties['current']['materials']['mls_id']['value'] : '';
$price = isset($this->wpl_properties['current']['materials']['price']['price_only']) ? $this->wpl_properties['current']['materials']['price']['price_only'] : '';
$price_type = isset($this->wpl_properties['current']['materials']['price_period']['value']) ? $this->wpl_properties['current']['materials']['price_period']['value'] : '';
$location_string = (isset($this->wpl_properties['current']['location_text']) and $this->location_visibility === true) ? $this->wpl_properties['current']['location_text'] : $this->location_visibility;
$prp_title = isset($this->wpl_properties['current']['property_title']) ? $this->wpl_properties['current']['property_title'] : '';

$pshow_gallery_activities = wpl_activity::get_activities('pshow_preview_gallery', 1);
$pshow_googlemap_activities = wpl_activity::get_activities('pshow_preview_googlemap', 1, '', 'loadObject');
$pshow_walkscore_activities = wpl_activity::get_activities('pshow_preview_walkscore', 1);
$pshow_bingmap_activities = wpl_activity::get_activities('pshow_preview_bingmap', 1, '', 'loadObject');

$this->pshow_googlemap_activity_id = $pshow_googlemap_activities->id;

/** video tab for showing videos **/
$pshow_video_activities = count(wpl_activity::get_activities('pshow_preview_video', 1));
if(!isset($this->wpl_properties['current']['items']['video']) or (isset($this->wpl_properties['current']['items']['video']) and !count($this->wpl_properties['current']['items']['video']))) $pshow_video_activities = 0;

/** Import JS file **/
echo $this->_wpl_import($this->tpl_path.'.scripts.internal_preview_js', true, false);
?>

<div class="wpl_prp_show_container wpl_prp_show_container_preview" id="wpl_prp_show_container">
    <div class="wpl_prp_container" id="wpl_prp_container<?php echo $this->pid; ?>" <?php echo $this->itemscope.' '.$this->itemtype_SingleFamilyResidence; ?>>
        <div class="show_header_preview">
            <div class="show_header_preview_left">
                <div class="show_header_preview_box c1">
                    <?php echo '<span>'.$prp_type .'</span> '.$prp_listings; ?>
                </div>
                <div class="show_header_preview_box c2">
                    <?php echo ' - ' . $location_string; ?>
                </div>
            </div>
            <div class="show_header_preview_expand">
                <a href="<?php echo $this->wpl_properties['current']['property_link']; ?>" > <?php echo __("Expand", 'real-estate-listing-realtyna-wpl'); ?></a>
            </div>
        </div>
        <div class="wpl_prp_show_tabs">
            <div class="tabs_container">
                <?php if($pshow_gallery_activities): ?>
                <div id="tabs-1" class="tabs_contents">
                    <?php /** load position gallery **/ wpl_activity::load_position('pshow_preview_gallery', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_googlemap_activities and $this->location_visibility === true): ?>
                <div id="tabs-2" class="tabs_contents">
                    <?php /** load position googlemap **/ wpl_activity::load_position('pshow_preview_googlemap', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_video_activities): ?>
                <div id="tabs-3" class="tabs_contents">
                    <?php /** load position video **/ wpl_activity::load_position('pshow_preview_video', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_bingmap_activities and $this->location_visibility === true): ?>
                <div id="tabs-4" class="tabs_contents">
                    <?php /** load position bingmap **/ wpl_activity::load_position('pshow_preview_bingmap', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
                <?php if($pshow_walkscore_activities and $this->location_visibility === true): ?>
                <div id="tabs-5"  class="tabs_contents">
                    <?php /** load position walkscore **/ wpl_activity::load_position('pshow_preview_walkscore', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="tabs_box">
                <ul class="tabs">
                    <?php if($pshow_gallery_activities): ?>
                    <li><a href="#tabs-1"><?php echo __('Pictures', 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_googlemap_activities and $this->location_visibility === true): ?>
                    <li><a href="#tabs-2" data-init-googlemap="1"><?php echo __('Google Map', 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_video_activities): ?>
                    <li><a href="#tabs-3"><?php echo __('Video', 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_bingmap_activities and $this->location_visibility === true): ?>
                    <li><a href="#tabs-4" data-init-bingmap="1"><?php echo __("Bird's eye", 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                    <?php if($pshow_walkscore_activities and $this->location_visibility === true): ?>
                    <li><a href="#tabs-5"><?php echo __("Walk score", 'real-estate-listing-realtyna-wpl') ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="wpl_prp_container_title">
            <div class="wpl_prp_container_title_left wpl-small-12 wpl-large-9 wpl-columns">
                <?php echo '<div class="r1">'.$location_string.'</div>' ; ?>
            </div>
            <div class="wpl_prp_container_title_right wpl-small-12 wpl-large-3 wpl-columns">
                <?php echo $price.' '.($price_type!=''?'<span>'.$price_type.'</span>':''); ?>
            </div>
        </div>
        <div class="wpl_prp_container_content">
            <div class="wpl_prp_container_content_left">
                <?php
                    $description_column = 'field_308';
                    if(wpl_global::check_multilingual_status()) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);
                    
                    if(isset($this->wpl_properties['current']['data'][$description_column]) and $this->wpl_properties['current']['data'][$description_column]):
                    ?>
                        <div class="wpl_prp_show_detail_boxes wpl_prp_show_detail_boxes_desc">
                            <div class="wpl_prp_show_detail_boxes_title"> <span><?php echo __("Description", 'real-estate-listing-realtyna-wpl')?></span></div>
                            <div class="wpl_prp_show_detail_boxes_cont" <?php echo $this->itemprop_description; ?>> <?php echo apply_filters('the_content', stripslashes($this->wpl_properties['current']['data'][$description_column])); ?>
                            </div>
                        </div>
                    <?php endif; 
                ?>
                <?php
                $i = 0;
                $details_boxes_num = count($this->wpl_properties['current']['rendered']);
                
                foreach($this->wpl_properties['current']['rendered'] as $values)
                {
                    /** skip empty categories **/
                    if(!count($values['data'])) continue;
                    
                    /** skip location if property address is hiden **/
                    if($values['self']['prefix'] == 'ad' and $this->location_visibility !== true) continue;

                    if( trim($values['self']['name']) != '')
                    {
                        $pshow_box_title = NULL;
                        $title_parts = explode(' ', __($values['self']['name'], 'real-estate-listing-realtyna-wpl'));
                        $i_part = 0;
                        foreach($title_parts as $title_part)
                        {
                            if($i_part == 0) $pshow_box_title .= '<span>'.$title_part.'</span> ';
                            else $pshow_box_title .= $title_part.' ';
                            $i_part++;
                        }
                    } 
                    echo '<div class="wpl_prp_show_detail_boxes cat_id_'.$values['self']['id'].'">
                            <div class="wpl_prp_show_detail_boxes_title">'.$pshow_box_title.'</div>
                            <div class="wpl_prp_show_detail_boxes_cont">';

                    foreach($values['data'] as $key => $value)
                    {
                        if(!isset($value['type'])) continue;
                        
                        elseif($value['type'] == 'neighborhood')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="rows neighborhood">' .__($value['name'], 'real-estate-listing-realtyna-wpl') .(isset($value['distance']) ? ' <span class="'.$value['vehicle_type'].'">'. $value['distance'] .' '. __('Minutes', 'real-estate-listing-realtyna-wpl'). '</span>':''). '</div>';
                        }
                        elseif($value['type'] == 'feature')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="rows feature ';
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
                            if(isset($value['settings']) and is_array($value['settings']))
                            {
                                foreach($value['settings'] as $ii=>$lvalue)
                                {
                                    if(isset($lvalue['enabled']) and !$lvalue['enabled']) continue;

                                    $lk = isset($value['keywords'][$ii]) ? $value['keywords'][$ii] : '';
                                    if(trim($lk) == '') continue;

                                    echo '<div id="wpl-dbst-show'.$value['field_id'].'-'.$lk.'" class="rows location '.$lk.'">'.__($lk, 'real-estate-listing-realtyna-wpl').' : ';
                                    echo '<span>'.$value['locations'][$ii].'</span>';
                                    echo '</div>';
                                }
                            }
                            else
                            {
                                foreach($value['locations'] as $ii => $lvalue)
                                {
                                    $lk = isset($value['keywords'][$ii]) ? $value['keywords'][$ii] : '';
                                    if(trim($lk) == '') continue;

                                    echo '<div id="wpl-dbst-show' . $value['field_id'] . '-'.$lk.'" class="rows location">' . __($lk, 'real-estate-listing-realtyna-wpl') . ' : ';
                                    echo '<span>' . $lvalue . '</span>';
                                    echo '</div>';
                                }
                            }
                        }
                        elseif($value['type'] == 'separator')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="rows separator">' .__($value['name'], 'real-estate-listing-realtyna-wpl'). '</div>';
                        }
                        else
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="rows other">' .__($value['name'], 'real-estate-listing-realtyna-wpl'). ' : <span>'. __((isset($value['value']) ? $value['value'] : ''), 'real-estate-listing-realtyna-wpl') .'</span></div>';
                    }
                    
                    echo '</div></div>';
                    $i++;
                }
                ?>
                <?php
                    $activities = wpl_activity::get_activities('pshow_preview_position3_preview');
                    foreach($activities as $activity)
                    {
                        $content = wpl_activity::render_activity($activity, array('wpl_properties'=>$this->wpl_properties));
                        if(trim($content) == '') continue;
                        
                        $activity_title =  explode(':', $activity->activity);
                        ?>
                        <div class="wpl_prp_show_detail_boxes <?php echo $activity_title[0]; ?>">
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

                                echo '<div class="wpl_prp_show_detail_boxes_title">'.$activity_box_title.'</div>';
                            }
                            ?>
                            <div class="wpl_prp_show_detail_boxes_cont clearfix">
                                <?php echo $content; ?>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
            <div class="wpl_prp_container_content_right">
                <div class="wpl_prp_right_boxes details">
                    <div class="wpl_prp_right_boxes_title">
                        <?php echo '<span>'.$prp_type .'</span> '.$prp_listings; ?>
                    </div>
                    <div class="wpl-row wpl-expanded">
                        <div class="wpl_prp_boxe_details_extra wpl-large-6 wpl-columns">
                            <ul>
                                <?php if(trim($listing_id) != ''): ?><li class="extra_mls"><?php echo '<span>'.__($this->wpl_properties['current']['materials']['mls_id']['name'], 'real-estate-listing-realtyna-wpl').':</span>'.' <span itemprop="productID">'.$listing_id.'</span>'; ?></li>
                                <?php endif; ?>
                                <?php if(trim($bedroom) != ''): ?><li class="extra_bedroom"><?php echo '<span>'.__($this->wpl_properties['current']['materials']['bedrooms']['name'], 'real-estate-listing-realtyna-wpl').':</span>'.$bedroom; ?></li><?php endif; ?>
                                <?php if(trim($bathroom) != ''): ?><li class="extra_bathroom"><?php echo '<span>'.__($this->wpl_properties['current']['materials']['bathrooms']['name'], 'real-estate-listing-realtyna-wpl').':</span>'.$bathroom; ?></li><?php endif; ?>
                                <?php if(trim($price_type) != ''): ?><li class="extra_price_type"><?php echo '<span>'.__("Price Type", 'real-estate-listing-realtyna-wpl').':</span>'.$price_type; ?></li><?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                    $activities = wpl_activity::get_activities('pshow_preview_position2');
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
    <?php /** Don't remove this element **/ ?>
    <div class="wpl-util-hidden-internal-preview">
        <div class="wpl_pshow_lightbox_title_container">
            <h2 class="realtyna-lightbox-title"><?php echo __('Share', 'real-estate-listing-realtyna-wpl'); ?><div class="wpl_share_close_popup"></div></h2>
        </div>
        <div id="wpl_pshow_lightbox_content_container" class="wpl-util-hidden">
            <div class="tmp-loading"></div>
        </div>
    </div>
</div>