<?php
/** No direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type='text/javascript'>
var ws_wsid = '<?php echo $this->walkscore_license_key; ?>';
var ws_address = '<?php echo $this->property_address; ?>';
var ws_lat = '<?php echo $this->walkscore_googlemap_lt; ?>';
var ws_lon = '<?php echo $this->walkscore_googlemap_ln; ?>';
var ws_width = '<?php echo $this->walkscore_width; ?>';
var ws_height = '<?php echo $this->walkscore_height; ?>';
var ws_layout = '<?php echo $this->walkscore_layout; ?>';
var ws_commute = 'true';
var ws_transit_score = 'true';
var ws_map_modules = 'all';
</script>
<script type="text/javascript" src="//www.walkscore.com/tile/show-walkscore-tile.php"></script>