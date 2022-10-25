<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

// mark markers with id and convert any svg icon to str [fix for taking snapshot]
$params = $this->params;
$mms = $this->markers;
foreach($mms as $key => $mm){
	if(isset($mm['advanced_marker']))
	{
		$mm['advanced_marker'] = str_replace('<img ','<img id="marker-icon" ',$mm['advanced_marker']);  
		$mms[$key] = $mm;
	}
}

$this->markers = $mms;
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>

<script type="text/javascript">
    var markers<?php echo $this->activity_id; ?> = <?php echo json_encode($this->markers); ?>;
    
    var default_lt<?php echo $this->activity_id; ?> = '<?php echo $this->default_lt; ?>';
    var default_ln<?php echo $this->activity_id; ?> = '<?php echo $this->default_ln; ?>';
    var default_zoom<?php echo $this->activity_id; ?> = <?php echo $this->default_zoom; ?>;

    function wpl_initialize<?php echo $this->activity_id; ?>(){
        const markers = markers<?php echo $this->activity_id; ?>;
        
        let lt = default_lt<?php echo $this->activity_id; ?>;
        let ln = default_ln<?php echo $this->activity_id; ?>;
        if(markers.length){
            lt = markers[0].googlemap_lt;
            ln = markers[0].googlemap_ln;
        }
        
        var mymap = L.map('wpl_map_canvas<?php echo $this->activity_id; ?>').setView([lt, ln], default_zoom<?php echo $this->activity_id; ?>);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);
        
        for(var i = 0; i < markers.length; i++)
        {
            dataMarker = markers[i];
            
            //console.log(dataMarker.id);
            
            const marker_content = '<?php echo wpl_global::get_wpl_url(); ?>assets/img/listing_types/gicon/'+dataMarker.gmap_icon;
            
            var customIcon = L.icon({
                iconUrl: marker_content,
                iconSize:     [18, 18], // size of the icon
            });
            
            var marker = new L.marker([dataMarker.googlemap_lt, dataMarker.googlemap_ln], {icon: customIcon});
            mymap.addLayer(marker);    
        }
        
        
    }
    
    wpl_initialize<?php echo $this->activity_id; ?>();   
</script>