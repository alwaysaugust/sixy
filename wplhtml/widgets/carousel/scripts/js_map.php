<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

// Load Google Maps API
wpl_global::include_google_maps();
?>
<script type="text/javascript">
var wpl_map<?php echo $this->widget_id; ?>;
var markers_array<?php echo $this->widget_id; ?> = new Array();
var loaded_markers<?php echo $this->widget_id; ?> = new Array();
var markers<?php echo $this->widget_id; ?>;
var bounds<?php echo $this->widget_id; ?>;
var infowindow<?php echo $this->widget_id; ?>;

function wpl_initialize<?php echo $this->widget_id; ?>()
{
	/** create empty LatLngBounds object **/
	bounds<?php echo $this->widget_id; ?> = new google.maps.LatLngBounds();
	var mapOptions = {
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
    
	/** init map **/
	wpl_map<?php echo $this->widget_id; ?> = new google.maps.Map(document.getElementById('wpl_carousel_map_canvas<?php echo $this->widget_id; ?>'), mapOptions);
	infowindow<?php echo $this->widget_id; ?> = new google.maps.InfoWindow();
	
	/** load markers **/
	wpl_load_markers<?php echo $this->widget_id; ?>(markers<?php echo $this->widget_id; ?>);
	
    var styles =
    [
        {
            "featureType": "water",
            "stylers": [
                {
                    "color": "#46bcec"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "landscape",
            "stylers": [
                {
                    "color": "#f2f2f2"
                }
            ]
        },
        {
            "featureType": "road",
            "stylers": [
                {
                    "saturation": -100
                },
                {
                    "lightness": 45
                }
            ]
        },
        {
            "featureType": "road.highway",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#444444"
                }
            ]
        },
        {
            "featureType": "poi",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        }
    ];

    var styledMap = new google.maps.StyledMapType(styles, {name: "WPL Map"});

    wpl_map<?php echo $this->widget_id; ?>.mapTypes.set('map_style', styledMap);
    wpl_map<?php echo $this->widget_id; ?>.setMapTypeId('map_style');
}

function wpl_marker<?php echo $this->widget_id; ?>(dataMarker)
{
	if(wplj.inArray(dataMarker.id, loaded_markers<?php echo $this->widget_id; ?>) != '-1') return true;
	
  	marker = new google.maps.Marker({
		position: new google.maps.LatLng(dataMarker.googlemap_lt, dataMarker.googlemap_ln),
		map: wpl_map<?php echo $this->widget_id; ?>,
		property_ids: dataMarker.pids,
		icon: '<?php echo wpl_global::get_wpl_url(); ?>assets/img/listing_types/gicon/'+dataMarker.gmap_icon,
		title: dataMarker.title,
	});
	
	/** extend the bounds to include each marker's position **/
  	bounds<?php echo $this->widget_id; ?>.extend(marker.position);

	loaded_markers<?php echo $this->widget_id; ?>.push(dataMarker.id);
  	markers_array<?php echo $this->widget_id; ?>.push(marker);
	
	google.maps.event.addListener(marker, "click", function(event)
	{
		if(this.html)
		{
			infowindow<?php echo $this->widget_id; ?>.close();
			infowindow<?php echo $this->widget_id; ?>.setContent(this.html);
			infowindow<?php echo $this->widget_id; ?>.open(wpl_map<?php echo $this->widget_id; ?>, this);
		}
		else
		{
			wplj("#wpl_carousel_map_canvas<?php echo $this->widget_id; ?>").append('<div class="map_search_ajax_loader"><img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader4.gif'); ?>" /></div>');
			
			infowindow_html = get_infowindow_html<?php echo $this->widget_id; ?>(this.property_ids);
			this.html = infowindow_html;
			infowindow<?php echo $this->widget_id; ?>.close();
			infowindow<?php echo $this->widget_id; ?>.setContent(infowindow_html);
			infowindow<?php echo $this->widget_id; ?>.open(wpl_map<?php echo $this->widget_id; ?>, this);
			
			wplj(".map_search_ajax_loader").remove();
		}
	});
}

function wpl_load_markers<?php echo $this->widget_id; ?>(markers, delete_markers)
{
	if(delete_markers) delete_markers<?php echo $this->widget_id; ?>();
	
	for(var i = 0; i < markers.length; i++)
	{
		wpl_marker<?php echo $this->widget_id; ?>(markers[i]);
	}
    
	wpl_map<?php echo $this->widget_id; ?>.setZoom(1);
    
	if(!markers.length)
	{
		wpl_map<?php echo $this->widget_id; ?>.setCenter(new google.maps.LatLng(default_lt<?php echo $this->widget_id; ?>, default_ln<?php echo $this->widget_id; ?>));
		wpl_map<?php echo $this->widget_id; ?>.setZoom(parseInt(default_zoom));
	}
	else
	{
		/** now fit the map to the newly inclusive bounds **/
		wpl_map<?php echo $this->widget_id; ?>.fitBounds(bounds<?php echo $this->widget_id; ?>);
	}
}

function get_infowindow_html<?php echo $this->widget_id; ?>(property_ids)
{
	var infowindow_html;
	
	wplj.ajax(
	{
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: 'wpl_format=c:functions:ajax&wpl_function=infowindow&property_ids='+property_ids+'&wpltarget=<?php echo wpl_request::getVar('wpltarget', 0); ?>',
		type: 'GET',
		async: false,
		cache: false,
		timeout: 30000,
		success: function(data)
		{
			infowindow_html = data;
		}
	});
	
	return infowindow_html;
}

function delete_markers<?php echo $this->widget_id; ?>()
{
	if(markers_array<?php echo $this->widget_id; ?>)
	{
		for(i=0; i < markers_array<?php echo $this->widget_id; ?>.length; i++) markers_array<?php echo $this->widget_id; ?>[i].setMap(null);
		markers_array<?php echo $this->widget_id; ?>.length = 0;
	}
	
	if(loaded_markers<?php echo $this->widget_id; ?>) loaded_markers<?php echo $this->widget_id; ?>.length = 0;
}
</script>
<script type="text/javascript">
var markers<?php echo $this->widget_id; ?> = <?php echo json_encode($this->markers); ?>;

/** default values in case of no marker to showing **/
var default_lt<?php echo $this->widget_id; ?> = '<?php echo $this->default_lt; ?>';
var default_ln<?php echo $this->widget_id; ?> = '<?php echo $this->default_ln; ?>';
var default_zoom<?php echo $this->widget_id; ?> = <?php echo $this->default_zoom; ?>;

jQuery(document).ready(function()
{
    wpl_add_googlemaps_callbacks(function()
    {
        wpl_initialize<?php echo $this->widget_id; ?>();
    
        if(markers<?php echo $this->widget_id; ?>.length === 1)
        {
            /** restore the zoom level after the map is done scaling **/
            var listener = google.maps.event.addListener(wpl_map<?php echo $this->widget_id; ?>, 'idle', function(event)
            {
                wpl_map<?php echo $this->widget_id; ?>.setZoom(default_zoom);
                google.maps.event.removeListener(listener);
            });
        }
    });
});
</script>