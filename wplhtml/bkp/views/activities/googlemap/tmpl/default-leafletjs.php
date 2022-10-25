<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();

$this->params = $params;

$this->map_width = isset($params['map_width']) ? $params['map_width'] : 980;
$this->map_height = isset($params['map_height']) ? $params['map_height'] : 480;
$this->default_lt = isset($params['default_lt']) ? $params['default_lt'] : '38.685516';
$this->default_ln = isset($params['default_ln']) ? $params['default_ln'] : '-101.073324';
$this->default_zoom = isset($params['default_zoom']) ? $params['default_zoom'] : '4';
$this->infowindow_event = isset($params['infowindow_event']) ? $params['infowindow_event'] : 'click';

/** Preview Property feature **/
$this->map_property_preview = isset($params['map_property_preview']) ? $params['map_property_preview'] : 0;
$this->map_property_preview_show_marker_icon = isset($params['map_property_preview_show_marker_icon']) ? $params['map_property_preview_show_marker_icon'] : 'price';

$this->show_marker = 1;

unset($wpl_properties['current']);

$this->markers = wpl_property::render_markers($wpl_properties);

$this->_wpl_import($this->tpl_path.'.scripts.default_leafletjs', true, true);

?>
<style>
    #google_map_handle{
        z-index:999 !important;
    }
</style>
<div class="wpl_googlemap_container wpl_googlemap_plisting" id="wpl_googlemap_container<?php echo $this->activity_id; ?>" data-wpl-height="<?php echo $this->map_height; ?>">
    <div class="wpl-map-add-ons"></div>
    <div class="wpl_map_canvas" id="wpl_map_canvas<?php echo $this->activity_id; ?>" style="height: <?php echo $this->map_height ?>px;"></div>
</div>