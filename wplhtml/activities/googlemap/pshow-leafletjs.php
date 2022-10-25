<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();
$property_id = isset($wpl_properties['current']['data']['id']) ? $wpl_properties['current']['data']['id'] : NULL;

/** Kind **/
$this->kind = isset($wpl_properties['current']['data']['kind']) ? $wpl_properties['current']['data']['kind'] : 0;
$kind_data = wpl_flex::get_kind($this->kind);

/** Property Type **/
$ptype_data = array();
if(isset($wpl_properties['current']['data']['property_type'])) $ptype_data = wpl_global::get_property_types($wpl_properties['current']['data']['property_type']);

/** Parameters **/
$this->params = $params;

$this->map_width = isset($params['map_width']) ? $params['map_width'] : 360;
$this->map_height = isset($params['map_height']) ? $params['map_height'] : 385;
$this->default_lt = isset($params['default_lt']) ? $params['default_lt'] : '38.685516';
$this->default_ln = isset($params['default_ln']) ? $params['default_ln'] : '-101.073324';
$this->default_zoom = isset($params['default_zoom']) ? $params['default_zoom'] : '14';

$this->show_marker = (isset($kind_data['map']) and $kind_data['map'] != 'marker') ? 0 : ((isset($ptype_data->show_marker) and !$ptype_data->show_marker) ? 0 : 1);
if(isset($wpl_properties['current']['data']['show_marker']) and $wpl_properties['current']['data']['show_marker'] != 2) $this->show_marker = ($wpl_properties['current']['data']['show_marker'] ? $wpl_properties['current']['data']['show_marker'] : 0);

/** Preview Property feature **/
$this->map_property_preview = 0;
$this->map_property_preview_show_marker_icon = 'price';

/* Get Google Place Option */
$this->google_place = isset($params['google_place']) ? $params['google_place'] : 0;
$this->google_place_radius = isset($params['google_place_radius']) ? $params['google_place_radius'] : 1000;

$this->markers = wpl_property::render_markers($wpl_properties);

/** Map Search **/
$this->map_search_status = 0;

// Include JavaScript files in footer or not
$wplformat = wpl_request::getVar('wpl_format', NULL);
$inclusion = strpos($wplformat, ':raw') !== false ? false : true;

/** load js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.pshow_leafletjs', true, $inclusion);

?>
<div class="wpl_googlemap_container wpl_googlemap_pshow" id="wpl_googlemap_container<?php echo $this->activity_id; ?>">
    <div class="wpl-map-add-ons"></div>
    <div class="wpl_map_canvas" id="wpl_map_canvas<?php echo $this->activity_id; ?>" style="height: <?php echo $this->map_height ?>px; <?php echo $hide_map; ?>"></div>
</div>