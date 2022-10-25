<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

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

/** Calculate how many vists per days **/
$days = 0;
if($add_date != '0000-00-00 00:00:00')
{
    $datetime1 = strtotime($add_date);
    $datetime2 = time();
    $interval = abs($datetime2 - $datetime1);
    $days = round($interval / 60 / 60 / 24);
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
?>
<div class="wpl_prp_show_layout2_container wpl_prp_show_container" id="wpl_prp_show_container">
    <div class="wpl_prp_container" id="wpl_prp_container<?php echo $this->pid; ?>" <?php echo $this->itemscope.' '.$this->itemtype_SingleFamilyResidence; ?>>

        <div class="wpl-row wpl-expanded wpl_prp_container_content">
            <div class="wpl-large-8 wpl-medium-7 wpl-small-12 wpl-column wpl_prp_container_content_left">

                <div class="wpl_prp_left_box wpl_prp_gallery">
                <?php if($pshow_gallery_activities): ?>
                        <?php /** load position gallery **/ wpl_activity::load_position('pshow_gallery', array('wpl_properties'=>$this->wpl_properties)); ?>
                <?php endif; ?>
                </div>

                <?php
                $description_column = 'field_308';
                if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, $this->kind)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);

                if(isset($this->wpl_properties['current']['data'][$description_column]) and $this->wpl_properties['current']['data'][$description_column]):
                ?>
                <div class="wpl_prp_show_detail_boxes wpl_category_description">
                    <div class="wpl_prp_show_detail_boxes_title"><span><?php echo __(wpl_flex::get_dbst_key('name', wpl_flex::get_dbst_id('field_308', $this->kind)), 'real-estate-listing-realtyna-wpl') ?></span></div>
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
                    /** skip for Basic details **/
                    if($values['self']['id']== 1) continue;

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
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows neighborhood"><label>' .__($value['name'],'real-estate-listing-realtyna-wpl') .(isset($value['distance']) ? '</label> <span class="'.$value['vehicle_type'].'">'. $value['distance'] .' '. __('Minutes','real-estate-listing-realtyna-wpl'). '</span>':''). '</div>';
                        }
                        elseif($value['type'] == 'feature')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows feature ';
                            if(!isset($value['values'][0])) echo ' single ';

                            echo '"><label>'.__($value['name'], 'real-estate-listing-realtyna-wpl').'</label>';

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

                                    echo '<div id="wpl-dbst-show'.$value['field_id'].'-'.$lk.'" class="wpl-column rows location '.$lk.'"><label>'.__($lk, 'real-estate-listing-realtyna-wpl').' : </label>';
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

                                    echo '<div id="wpl-dbst-show'.$value['field_id'].'-'.$lk.'" class="wpl-column rows location '.$lk.'"><label>'.__($lk, 'real-estate-listing-realtyna-wpl').' : </label>';
                                    echo '<span>'.$lvalue.'</span>';
                                    echo '</div>';
                                }
                            }
                        }
                        elseif($value['type'] == 'separator')
                        {
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows separator">' .__($value['name'], 'real-estate-listing-realtyna-wpl'). '</div>';
                        }
                        else
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows other"><label>' .__($value['name'], 'real-estate-listing-realtyna-wpl'). ' : </label><span>'. __((isset($value['value']) ? $value['value'] : ''), 'real-estate-listing-realtyna-wpl') .'</span></div>';
                    }

                    echo '</div></div>';
                    $i++;
                }
                ?>

                <?php if($pshow_video_activities): ?>
                    <div class="wpl_prp_show_detail_boxes">
                        <div class="wpl_prp_show_detail_boxes_title">
                            <span><?php echo __('Video', 'real-estate-listing-realtyna-wpl') ?></span>
                        </div>
                        <div class="wpl_prp_left_box">
                            <?php /** load position video **/ wpl_activity::load_position('pshow_video', array('wpl_properties'=>$this->wpl_properties)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="wpl_prp_show_tabs wpl_prp_show_tabs-maps wpl_prp_left_box">
                    <div class="tabs_box">
                        <ul class="tabs clearfix">
                            <?php if($pshow_googlemap_activities and $this->location_visibility === true): ?>
                                <li><a href="#tabs-1" data-for="tabs-1" data-init-googlemap="1"><?php echo __('Google Map', 'real-estate-listing-realtyna-wpl') ?></a></li>
                            <?php endif; ?>
                            <?php if($pshow_bingmap_activities and $this->location_visibility === true): ?>
                                <li><a href="#tabs-2" data-for="tabs-2" data-init-bingmap="1"><?php echo __('Birds Eye', 'real-estate-listing-realtyna-wpl') ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="tabs_container">
                        <?php if($pshow_googlemap_activities and $this->location_visibility === true): ?>
                        <div id="tabs-1" class="tabs_contents">
                            <?php /** load position googlemap **/ wpl_activity::load_position('pshow_googlemap', array('wpl_properties'=>$this->wpl_properties)); ?>
                        </div>
                        <?php endif; ?>
                        <?php if($pshow_bingmap_activities and $this->location_visibility === true): ?>
                        <div id="tabs-2" class="tabs_contents">
                            <?php /** load position bingmap **/ wpl_activity::load_position('pshow_bingmap', array('wpl_properties'=>$this->wpl_properties)); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
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
            <div class="wpl-large-4 wpl-medium-5 wpl-small-12 wpl-column wpl_prp_container_content_right">
                <div class="wpl_prp_right_boxes details">
                    <div class="wpl_prp_right_boxes_content wpl-prp-basic-info">
                        <div class="wpl_prp_right_boxe_details_top clearfix">
                            <div class="wpl_prp_show_title">
                                <?php
                                echo '<h1 class="title_text" '.$this->itemprop_name.'>'.$prp_title.'</h1>';
                                echo '<h2 class="location_build_up" '.$this->itemprop_address.' '.$this->itemscope.' '.$this->itemtype_PostalAddress.'><span '.$this->itemprop_addressLocality.'>'. $location_string .'</span></h2>';
                                ?>
                            </div>
                            <div class="wpl_prp_listing_icon_box">
                                <?php if(trim($bedroom) != ''): ?>
                                    <div <?php echo $this->itemprop_numberOfRooms.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue ?> class="bedroom"><span <?php echo $this->itemprop_value; ?>><?php echo $bedroom.'  </span> <span '.$this->itemprop_name.' class="name">'.__($this->wpl_properties['current']['materials']['bedrooms']['name'], 'real-estate-listing-realtyna-wpl').'</span>'; ?></div>
                                <?php endif; ?>
                                <?php if(trim($bathroom) != ''): ?>
                                    <div <?php echo $this->itemprop_numberOfRooms.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue ?> class="bathroom"><span <?php echo $this->itemprop_value; ?>><?php echo $bathroom.'  </span> <span '.$this->itemprop_name.' class="name">'.__($this->wpl_properties['current']['materials']['bathrooms']['name'], 'real-estate-listing-realtyna-wpl').'</span>'; ?></div>
                                <?php endif; ?>
                                <?php if(trim($build_up_area) != ''): ?>
                                    <div class="built_up_area" <?php echo $this->itemprop_floorSize.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue ?>><span <?php echo $this->itemprop_value; ?>><?php echo $build_up_area; ?></span></div>
                                <?php endif; ?>
                                
                                 <?php if(trim($build_up_area) != ''): ?>
                                    <div class="built_up_area" <?php echo $this->itemprop_floorSize.' '.$this->itemscope.' '.$this->itemtype_QuantitativeValue ?>><span <?php echo $this->itemprop_value; ?>><?php echo $build_up_area; ?></span></div>
                                <?php endif; ?>
                                <?php if(wpl_global::get_setting('show_plisting_visits')): ?>
                                    <div class="wpl-property-visit">
                                        <?php echo __('Visits', 'real-estate-listing-realtyna-wpl').' : <span class="value">'.$visits.($days ? ' '.sprintf(__('in %d days', 'real-estate-listing-realtyna-wpl'), $days) : '').'</span>'; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php echo '<div class="wpl_prp_right_boxe_details_bot" '.$this->itemscope.' '.$this->itemtype_offer.'><div class="price_box" '.$this->itemprop_price.'>'.$price.'</div></div>'; ?>
                            <?php if(trim($listing_id) != ''): ?><div class="wpl_prp_mls_id_box"><?php echo __($this->wpl_properties['current']['materials']['mls_id']['name'], 'real-estate-listing-realtyna-wpl').'  <span>#'.$listing_id.'</span>'; ?></div><?php endif; ?>
                        </div>
                        <?php

                        $values = $this->wpl_properties['current']['rendered'][1];
                        echo '<div class="wpl_prp_show_detail_boxes wpl_category_'.$values['self']['id'].'">
                            <div class="wpl_prp_show_detail_boxes_title"><span>'.__($values['self']['name'], 'real-estate-listing-realtyna-wpl').'</span></div>
                            <div class="wpl_prp_show_detail_boxes_cont">';

                        foreach($values['data'] as $key => $value)
                        {
                            if(!isset($value['type'])) continue;

                            elseif($value['type'] == 'neighborhood')
                            {
                                echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="rows neighborhood">' .__($value['name'],'real-estate-listing-realtyna-wpl') .(isset($value['distance']) ? ' <span class="'.$value['vehicle_type'].'">'. $value['distance'] .' '. __('Minutes','real-estate-listing-realtyna-wpl'). '</span>':''). '</div>';
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

                                        echo '<div id="wpl-dbst-show'.$value['field_id'].'-'.$lk.'" class="rows location '.$value['keywords'][$ii].'">'.__($lk, 'real-estate-listing-realtyna-wpl').' : ';
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

                                        echo '<div id="wpl-dbst-show'.$value['field_id'].'-'.$lk.'" class="rows location '.$lk.'">'.__($lk, 'real-estate-listing-realtyna-wpl').' : ';
                                        echo '<span>'.$lvalue.'</span>';
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
                        ?>
                    </div>

                    <div class="wpl_prp_show_position2">
                        <div class="wpl_prp_right_boxes listing_result clearfix">
                            <?php /** listing result **/ wpl_activity::load_position('pshow_listing_results', array('wpl_properties'=>$this->wpl_properties)); ?>
                        </div>
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
            <div class="wpl-large-12 wpl-medium-12 wpl-small-12 wpl-column wpl_prp_show_bottom">
                <?php if($pshow_walkscore_activities): ?>
                    <div class="wpl_prp_show_walkscore">
                        <?php /** load position walkscore **/ wpl_activity::load_position('pshow_walkscore', array('wpl_properties'=>$this->wpl_properties)); ?>
                    </div>
                <?php endif; ?>
                <?php if(is_active_sidebar('wpl-pshow-bottom')) dynamic_sidebar('wpl-pshow-bottom'); ?>
            </div>
        </div>
    </div>
    <?php /** Don't remove this element **/ ?>
    <div id="wpl_pshow_lightbox_content_container" class="wpl-util-hidden"></div>
    <?php if(wpl_global::check_addon('membership') and wpl_session::get('wpl_dpr_popup')): ?>
        <a id="wpl_dpr_lightbox" class="wpl-util-hidden" data-realtyna-href="#wpl_pshow_lightbox_content_container" data-realtyna-lightbox-opts="title:<?php echo __('Login to continue', 'real-estate-listing-realtyna-wpl'); ?>"></a>
    <?php endif; ?>
    <div class="wpl-powered-by-realtyna" style="display: none;">
        <a href="https://realtyna.com">
            <img src="<?php echo wpl_global::get_wpl_url().'assets/img/idx/powered-by-realtyna.png'; ?>" alt="Powered By Realtyna" width="120">
        </a>
    </div>
</div>