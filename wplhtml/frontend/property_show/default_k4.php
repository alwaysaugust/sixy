<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$parent             = wpl_property::render_field($this->wpl_properties['current']['raw']['parent'], wpl_flex::get_dbst_id('parent', $this->wpl_properties['current']['raw']['kind']));
$listing_id         = isset($this->wpl_properties['current']['materials']['mls_id']['value']) ? $this->wpl_properties['current']['materials']['mls_id']['value'] : '';
$location_string 	= isset($this->wpl_properties['current']['location_text']) ? $this->wpl_properties['current']['location_text'] : '';
$prp_title          = isset($this->wpl_properties['current']['property_title']) ? $this->wpl_properties['current']['property_title'] : '';

$pshow_gallery_activities = wpl_activity::get_activities('pshow_gallery', 1);

/** Import JS file **/
$this->_wpl_import($this->tpl_path.'.scripts.js', true, true);
?>
<div class="wpl_prp_show_container wpl_prp_show_default_container wpl-neighborhood-addon" id="wpl_prp_show_container">
    <div class="wpl_prp_container" id="wpl_prp_container<?php echo $this->pid; ?>" <?php echo $this->itemscope.' '.$this->itemtype_Place ?>>
        <div class="wpl_prp_container_content">

            <div class="wpl-row wpl-expanded wpl_prp_container_content_title">
                <?php
                echo '<div class="wpl-large-12 wpl-medium-12 wpl-small-12 wpl-columns">';
                echo '<h1 class="title_text" '.$this->itemprop_name.'>'.$prp_title.'</h1>';
                echo '<h2 class="location_build_up" '.$this->itemprop_address.' '.$this->itemscope.' '.$this->itemtype_PostalAddress.'><span '.$this->itemprop_addressLocality.'>'. $location_string .'</span></h2>';
                ?>
                <?php if(isset($parent['value'])): ?>
                    <div class="wpl-pshow-nh-parent">
                        <?php echo isset($parent['html']) ? $parent['html'] : $parent['value']; ?>
                    </div>
                <?php endif; ?>
                </div>
                <div class="wpl-large-12 wpl-medium-12 wpl-small-12 wpl-columns">
                <?php /** load QR Code **/ wpl_activity::load_position('pshow_qr_code', array('wpl_properties'=>$this->wpl_properties)); ?>
                </div>

            </div>

            <div class="wpl_prp_show_bottom">
                <?php
                /** Show related properties or not **/
                if(isset($this->settings['neighborhood_show_related_listings']) and $this->settings['neighborhood_show_related_listings'])
                {
                    if(isset($this->settings['neighborhood_filter_method']) and $this->settings['neighborhood_filter_method'] == 2)
                    {
                        $demographic_objects = isset($this->wpl_properties['current']['items']['demographic']) ? $this->wpl_properties['current']['items']['demographic'] : array();

                        $polygon = NULL;
                        foreach($demographic_objects as $demographic_object) if($demographic_object->item_cat == 'polygon') $polygon = $demographic_object;

                        $points = isset($polygon->item_extra1) ? wpl_global::toBoundaries($polygon->item_extra1) : array();

                        $points_str = '';
                        foreach($points as $point) $points_str .= $point['lat'].','.$point['lng'].';';

                        wpl_request::setVar('sf_polygonsearch', '1');
                        wpl_request::setVar('sf_polygonsearchpoints', '['.$points_str.']');
                    }
                    else wpl_request::setVar('sf_textsearch_neighborhood_ids', '['.$this->pid.']');

                    /** Set listings mode (properties or complex) **/
                    wpl_request::setVar('kind', $this->settings['neighborhood_listings_mode']);

                    /** loading property listing **/ echo wpl_global::load('property_listing', '', array(), NULL, true);
                }
                ?>
            </div>

            <div class="wpl_prp_show_tabs">
                <div class="tabs_container">
                    <?php if($pshow_gallery_activities): ?>
                    <div id="tabs-1" class="tabs_contents">
                        <?php /** load position gallery **/ wpl_activity::load_position('pshow_gallery', array('wpl_properties'=>$this->wpl_properties)); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

			<div class="wpl-row wpl-expanded">
				<div class="wpl-large-12 wpl-medium-12 wpl-small-12 wpl_prp_container_content_left wpl-column">
				<?php
                    $description_column = 'field_308';
                    if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, $this->kind)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);
                    
                    if($this->wpl_properties['current']['data'][$description_column]):
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
                    /** skip empty categories **/
					if(!count($values['data'])) continue;
                    
                    /** skip location if property address is hiden **/
					if($values['self']['prefix'] == 'ad' and !$this->wpl_properties['current']['raw']['show_address']) continue;
                    
                    echo '<div class="wpl_prp_show_detail_boxes wpl_category_'.$values['self']['id'].'">
                            <div class="wpl_prp_show_detail_boxes_title"><span>'.__($values['self']['name'], 'real-estate-listing-realtyna-wpl').'</span></div>
                            <div class="wpl-small-up-1 wpl-medium-up-1 wpl-large-up-'.$this->fields_columns.' wpl_prp_show_detail_boxes_cont">';

                    foreach($values['data'] as $key => $value)
					{
                        if(!isset($value['type'])) continue;
                        
                        elseif($value['type'] == 'neighborhood')
                        {
                            $css_class = 'wpl-nh-' . str_replace(' ','-',strtolower($value['name']));
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows neighborhood wpl-nh-item '.$css_class.'">' .__($value['name'], 'real-estate-listing-realtyna-wpl') . (isset($value['distance']) ? '<span class="'.$value['vehicle_type'].'">'. $value['distance'] .' '. __('Minutes','real-estate-listing-realtyna-wpl'). '</span>':''). '</div>';
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
                                foreach ($value['values'] as $val) $html .= __($val, 'real-estate-listing-realtyna-wpl').', ';
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

                                    echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows location '.$lk.'">'.__($lk, 'real-estate-listing-realtyna-wpl').' : ';
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
                        else
                            echo '<div id="wpl-dbst-show'.$value['field_id'].'" class="wpl-column rows other">' .__($value['name'], 'real-estate-listing-realtyna-wpl'). ' : <span>'. __((isset($value['value']) ? $value['value'] : ''), 'real-estate-listing-realtyna-wpl') .'</span></div>';
                    }
					
                    echo '</div></div>';
                	$i++;
                }
                ?>
                
                <div class="wpl_prp_show_position3">
                    <?php
                        $activities = wpl_activity::get_activities('nshow_position3');
                        foreach($activities as $activity)
                        {
                            $content = wpl_activity::render_activity($activity, array('wpl_properties'=>$this->wpl_properties));
                            if(trim($content) == '') continue;
                            
                            $activity_title = explode(':', $activity->activity);
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
            </div>
        </div>
    </div>
    <?php /** Don't remove this element **/ ?>
    <div id="wpl_pshow_lightbox_content_container" class="wpl-util-hidden"></div>
    <div class="wpl-powered-by-realtyna" style="display: none;">
        <a href="https://realtyna.com">
            <img src="<?php echo wpl_global::get_wpl_url().'assets/img/idx/powered-by-realtyna.png'; ?>" alt="Powered By Realtyna" width="120" />
        </a>
    </div>
</div>