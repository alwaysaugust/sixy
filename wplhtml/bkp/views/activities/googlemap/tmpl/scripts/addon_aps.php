<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** importing library **/
_wpl_import('libraries.addon_aps');
$this->APS = new wpl_addon_aps();

if($this->map_property_preview)
{
    // Google recaptcha is enabled for details lightbox
    if(
        (isset($this->settings['gre_listing_contact_activity']) and $this->settings['gre_listing_contact_activity']) or
        (isset($this->settings['gre_report_listing']) and $this->settings['gre_report_listing']) or
        (isset($this->settings['gre_request_visit']) and $this->settings['gre_request_visit']) or
        (isset($this->settings['gre_send_to_friend']) and $this->settings['gre_send_to_friend']) or
        (isset($this->settings['gre_showing_scheduler']) and $this->settings['gre_showing_scheduler'])
    )
    {
        wpl_global::include_google_recaptcha_js();
    }

    // Load Google Maps infowindow libraries if the property preview feature has been enabled
    $scripts = array();
    $scripts[] = (object) array('param1'=>'lightslider.min.js', 'param2'=>'packages/light_slider/js/lightslider.min.js', 'param4'=>'1');
    foreach($scripts as $script) wpl_extensions::import_javascript($script);

    $css = array();
    $css[] = (object) array('param1'=>'lightslider.css', 'param2'=>'packages/light_slider/css/lightslider.min.css');
    foreach($css as $style) wpl_extensions::import_style($style);
}

$this->map_search_toggle = isset($this->params['map_search_toggle']) ? $this->params['map_search_toggle'] : 0;
$this->wpltarget = wpl_request::getVar('wpltarget', wpl_sef::get_current_post_id());
?>
<script type="text/javascript">
var wpl_aps_ajax_obj = null;
var wpl_aps_freeze = true;
var wpl_aps_search_timeout;
var wpl_aps_map_search_toggle = <?php echo $this->map_search_toggle; ?>;
var wpl_aps_drawing_manager;
var wpl_aps_draw_array = [];
var wpl_aps_init_time<?php echo $this->activity_id; ?>;

// Preview Property Feature
var wpl_google_infowindow_array<?php echo $this->activity_id; ?> = [];
var wpl_google_marker_array<?php echo $this->activity_id; ?> = [];
var wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?> = null;

function wpl_aps_init<?php echo $this->activity_id; ?>()
{
    wpl_aps_init_time<?php echo $this->activity_id; ?> = new Date().getTime();

	google.maps.event.addListener(wpl_map<?php echo $this->activity_id; ?>, 'idle', function()
	{
        if(wpl_aps_freeze || (new Date().getTime() - wpl_aps_init_time<?php echo $this->activity_id; ?> < 500)) wpl_aps_freeze = false;
        else
        {
            wpl_aps_trigger<?php echo $this->activity_id; ?>();
        }
	});

    wplj('#wpl_googlemap_container<?php echo $this->activity_id; ?>').addClass('wpl-aps-addon');
    wplj('#wpl_googlemap_container<?php echo $this->activity_id; ?> .wpl-map-add-ons').prepend('<div class="wpl_aps_container"></div>');

    if(wpl_aps_map_search_toggle)
    {
        wplj('.wpl_aps_container').prepend('<input id="wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>" autocomplete="off" type="checkbox" <?php echo ($this->map_search_status == '1' ? 'checked="checked"' : ''); ?> /><label for="wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>"><?php echo addslashes(__('Update my search as map is moved.', 'real-estate-listing-realtyna-wpl')); ?></label>');
        wplj("#wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>").on("change", function()
        {
            wpl_aps_trigger<?php echo $this->activity_id; ?>();
        });
    }

    var draw_searched_shape_listener = google.maps.event.addListener(wpl_map<?php echo $this->activity_id; ?>, 'idle', function()
	{
        wpl_aps_draw_searched_shape<?php echo $this->activity_id; ?>();
        
        /** Remove listener **/
        google.maps.event.removeListener(draw_searched_shape_listener);
	});
}

function wpl_aps_trigger<?php echo $this->activity_id; ?>()
{
    wpl_map_bounds_extend<?php echo $this->activity_id; ?> = false;
    wpl_map_set_default_geo_point<?php echo $this->activity_id; ?> = false;
    
    clearTimeout(wpl_aps_search_timeout);
    wpl_aps_search_timeout = setTimeout(function()
    {
        // Don't do the search when map search is disabled
        if(wpl_aps_map_search_toggle && !wplj("#wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>").is(":checked")) return false;

        wpl_aps_freeze = true;
        wpl_aps_search<?php echo $this->activity_id; ?>();
    }, 1000);
}

function wpl_aps_search<?php echo $this->activity_id; ?>()
{
	// calculating bounds
	var bounds = wpl_map<?php echo $this->activity_id; ?>.getBounds();
	var ne = bounds.getNorthEast();
	var sw = bounds.getSouthWest();
	
    var lat_max = ne.lat();
	var lat_min = sw.lat();
	var lng_min = sw.lng();
	var lng_max = ne.lng();
	
    /** Min/Max values for Longitude **/
	if(lng_min > lng_max)
	{
		lng_min = -180;
		lng_max = 180;
	}
	
    /** Min/Max values for Latitude **/
	if(lat_min > lat_max)
	{
		lat_max = 85;
		lat_min = -85;
	}
    
	var request_str = 'sf_tmin_googlemap_lt='+lat_min+'&sf_tmax_googlemap_lt='+lat_max+'&sf_tmin_googlemap_ln='+lng_min+'&sf_tmax_googlemap_ln='+lng_max+'&sf_locationtextsearch=&sf_multiplelocationtextsearch=&sf_advancedlocationtextsearch=';
    wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
}

function wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str)
{
    var now = new Date().getTime();
    if(wpl_listing_last_search_time && now-wpl_listing_last_search_time < 1500)
    {
        wpl_aps_freeze = false;
        return false;
    }
    
    if(typeof wpl_listing_request_str != 'undefined')
    {
        // Remove location search
        request_str = wpl_update_qs('sf_locationtextsearch', ' ', request_str);
        wpl_listing_request_str = wpl_update_qs('sf_locationtextsearch', ' ', wpl_listing_request_str);
        
        request_str = wpl_update_qs('sf_advancedlocationtextsearch', ' ', request_str);
        wpl_listing_request_str = wpl_update_qs('sf_advancedlocationtextsearch', ' ', wpl_listing_request_str);
        wpl_listing_request_str = wpl_update_qs('wplpage', '1', wpl_listing_request_str);
		
        wpl_listing_request_str = wpl_qs_apply(wpl_listing_request_str, request_str);
        request_str = wpl_qs_apply(request_str, wpl_listing_request_str);
    }
    
    //PROPERTIES CONTAINER (ONLOAD)
     
wplj(".wpl_property_listing_list_view_container div.wpl-column span,.wpl_property_listing_list_view_container div.wpl-column h3").css('background','linear-gradient(90deg, rgba(238,238,238,1) 0%, rgba(255,255,255,1) 50%, rgba(238,238,238,1) 100%)');

wplj(".wpl_property_listing_list_view_container h3,.wpl_property_listing_list_view_container span").css('color', '#f4f4f4');
     
wplj(".wpl_property_listing_list_view_container div.wpl-column span,.wpl_property_listing_list_view_container div.wpl-column h3").css('border-radius', '6px');
      
wplj(".wpl_property_listing_list_view_container div.wpl-column .wpl_prp_listing_icon_box").css('opacity', '0.1');
       
wplj(".wpl_property_listing_list_view_container div.wpl-column a").css('opacity', '0');
       
wplj(".wpl_gallery_container").css('background','linear-gradient(90deg, rgba(238,238,238,1) 0%, rgba(255,255,255,1) 50%, rgba(238,238,238,1) 100%) !important');
       
   // wplj(".wpl_property_listing_list_view_container div.wpl-column").fadeTo(300, 0.5);
    
    try
    {
        /** Create full url of search **/
        var search_str = '<?php echo wpl_property::get_property_listing_link($this->wpltarget); ?>';

        if(search_str.indexOf('?') >= 0) search_str = search_str+'&'+request_str;
        else search_str = search_str+'?'+request_str;
    
        history.pushState({search: 'WPL'}, "<?php echo addslashes(__('Search Results', 'real-estate-listing-realtyna-wpl')); ?>", search_str);
    }
    catch(err){}
    
    /** Load Markers **/
    if(typeof wpl_load_map_markers == 'function') wpl_load_map_markers(request_str, true);
    
    if(wpl_aps_ajax_obj !== null) wpl_aps_ajax_obj.abort();
	wpl_aps_ajax_obj = wplj.ajax(
	{
		type: 'GET',
		dataType: 'json',
		url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:property_listing:list&'+request_str,
		success: function(data)
		{
			wpl_listing_total_pages = data.total_pages;
            wpl_listing_current_page = data.current_page;
            
            wplj(".wpl_property_listing_list_view_container").html(data.html);
            wplj(".wpl_property_listing_list_view_container").fadeTo(300, 1);


			
			wpl_aps_ajax_obj = null;
            wpl_aps_freeze = false;
            
            wpl_listing_last_search_time = new Date().getTime();
            if(typeof wpl_fix_no_image_size == 'function') setTimeout(function(){wpl_fix_no_image_size();}, 50);
            wpl_listing_enable_view(wpl_current_property_css_class);

            /*Image lazy loading*/
            if(wplj('.wpl_property_listing_container').hasClass('wpl-property-listing-mapview')){
                wplj(".wpl-property-listing-mapview .wpl_property_listing_listings_container .lazyimg").Lazy({
                    appendScroll: wplj('.wpl-property-listing-mapview .wpl_property_listing_listings_container')
                });
            } else {
                wplj(".lazyimg").Lazy();
            }
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
		}
	});
}

function wpl_aps_init_draw<?php echo $this->activity_id; ?>()
{
	wpl_aps_drawing_manager = new google.maps.drawing.DrawingManager(
    {
		drawingControl: true,
		drawingControlOptions:
        {
			position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: [
                google.maps.drawing.OverlayType.POLYGON,
                google.maps.drawing.OverlayType.CIRCLE
			]
		},
		polygonOptions:
        {
            strokeColor: '#1e74c7',
            strokeOpacity: 0.6,
            strokeWeight: 1,
            editable: true,
            draggable: true,
            fillColor: '#1e90ff',
            fillOpacity: 0.3,
            clickable: false
        },
        circleOptions:
        {
            strokeColor: '#1e74c7',
            strokeOpacity: 0.6,
            strokeWeight: 1,
            editable: true,
            draggable: true,
            fillColor: '#1e90ff',
            fillOpacity: 0.3,
            clickable: false
        },
		map: wpl_map<?php echo $this->activity_id; ?>
	});
    
    google.maps.event.addListener(wpl_aps_drawing_manager, 'overlaycomplete', function(event)
	{
        wpl_aps_drawing_manager.setOptions({drawingMode: null});
        
        var overlay = event.overlay;
        wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, event.type);
        
        if(event.type === google.maps.drawing.OverlayType.POLYGON)
        {
            /** delete overlays **/
            for(var i = 0; i < wpl_aps_draw_array.length; i++) wpl_aps_draw_array[i].setMap(null);
            wpl_aps_draw_array = new Array();
            
            /** push to array **/
            wpl_aps_draw_array.push(overlay);
        }
        else if(event.type === google.maps.drawing.OverlayType.CIRCLE)
        {
            /** delete overlays **/
            for(var i = 0; i < wpl_aps_draw_array.length; i++) wpl_aps_draw_array[i].setMap(null);
            wpl_aps_draw_array = new Array();
            
            /** push to array **/
            wpl_aps_draw_array.push(overlay);
        }
        
        wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(event.type, overlay);
        wpl_aps_set_overlay_listeners<?php echo $this->activity_id; ?>(event.type, overlay);
	});
}

function wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(type, overlay)
{
    bounds<?php echo $this->activity_id; ?> = new google.maps.LatLngBounds();
    wpl_aps_freeze = true;
    
    if(type === google.maps.drawing.OverlayType.POLYGON)
    {
        overlay.getPaths().forEach(function(path, index)
        {
            var points = path.getArray();
            for(b in points) bounds<?php echo $this->activity_id; ?>.extend(points[b]);
        });
    }
    else if(type === google.maps.drawing.OverlayType.CIRCLE)
    {
        bounds<?php echo $this->activity_id; ?>.union(overlay.getBounds());
    }
    
    wpl_map<?php echo $this->activity_id; ?>.fitBounds(bounds<?php echo $this->activity_id; ?>);
    setTimeout(function(){wpl_aps_freeze = false;}, 1000);
}

function wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, type)
{
    if(type === google.maps.drawing.OverlayType.POLYGON)
    {
        var paths_strings = '[';
        
        overlay.getPaths().forEach(function(path, index)
        {
            var points = path.getArray();
            for(b in points)
            {
                paths_strings += points[b].lat()+','+points[b].lng()+';';
            }
        });
        
        paths_strings += ']';
        
        if(typeof wpl_listing_request_str != 'undefined')
        {
            wpl_listing_request_str = wpl_update_qs('sf_radiussearchunit', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_radiussearchradius', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_radiussearch_lat', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_radiussearch_lng', '', wpl_listing_request_str);
        }
        
        var request_str = 'sf_polygonsearch=1&sf_polygonsearchpoints='+paths_strings;
    }
    else if(type === google.maps.drawing.OverlayType.CIRCLE)
    {
        if(typeof wpl_listing_request_str != 'undefined')
        {
            wpl_listing_request_str = wpl_update_qs('sf_polygonsearch', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_polygonsearchpoints', '', wpl_listing_request_str);
        }
        
        var unit_id = 11; // meters
        var radius = overlay.getRadius();
        var center = overlay.getCenter();
        
        var latitude = center.lat();
        var longitude = center.lng();
        
        var request_str = 'sf_radiussearchunit='+unit_id+'&sf_radiussearchradius='+radius+'&sf_radiussearch_lat='+latitude+'&sf_radiussearch_lng='+longitude;
    }
    else if(type === null)
    {
        if(typeof wpl_listing_request_str != 'undefined')
        {
            wpl_listing_request_str = wpl_update_qs('sf_polygonsearch', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_polygonsearchpoints', '', wpl_listing_request_str);
            
            wpl_listing_request_str = wpl_update_qs('sf_radiussearchunit', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_radiussearchradius', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_radiussearch_lat', '', wpl_listing_request_str);
            wpl_listing_request_str = wpl_update_qs('sf_radiussearch_lng', '', wpl_listing_request_str);
        }
        
        var request_str = 'sf_polygonsearch=0';
    }
    
    wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
}

function wpl_aps_set_overlay_listeners<?php echo $this->activity_id; ?>(type, overlay)
{
    wpl_aps_toggle_remove_shapes_button<?php echo $this->activity_id; ?>('show');
    
    /** POLYGON **/
    if(type === google.maps.drawing.OverlayType.POLYGON)
    {
        overlay.getPaths().forEach(function(path, index)
        {
            google.maps.event.addListener(path, 'insert_at', function()
            {
                wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
                wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.POLYGON);
            });

            google.maps.event.addListener(path, 'remove_at', function()
            {
                wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
                wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.POLYGON);
            });

            var wpl_aps_polygon_set_at_listener = google.maps.event.addListener(path, 'set_at', function()
            {
                wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
                wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.POLYGON);
            });

            google.maps.event.addListener(overlay, 'dragstart', function()
            {
                /** Remove set_at event till dragend event **/
                google.maps.event.removeListener(wpl_aps_polygon_set_at_listener);
            });

            google.maps.event.addListener(overlay, 'dragend', function()
            {
                /** Add center_changed event again **/
                wpl_aps_polygon_set_at_listener = google.maps.event.addListener(path, 'set_at', function()
                {
                    wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
                    wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.POLYGON);
                });

                wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
                wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.POLYGON);
            });
        });
    }
    else if(type === google.maps.drawing.OverlayType.CIRCLE)
    {
        google.maps.event.addListener(overlay, 'radius_changed', function()
        {
            wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.CIRCLE, overlay);
            wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.CIRCLE);
        });

        var wpl_aps_circle_center_changed_listener = google.maps.event.addListener(overlay, 'center_changed', function()
        {
            wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.CIRCLE, overlay);
            wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.CIRCLE);
        });

        google.maps.event.addListener(overlay, 'dragstart', function()
        {
            /** Remove center_changed event till dragend event **/
            google.maps.event.removeListener(wpl_aps_circle_center_changed_listener);
        });

        google.maps.event.addListener(overlay, 'dragend', function()
        {
            /** Add center_changed event again **/
            wpl_aps_circle_center_changed_listener = google.maps.event.addListener(overlay, 'center_changed', function()
            {
                wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.CIRCLE, overlay);
                wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.CIRCLE);
            });
            
            wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.CIRCLE, overlay);
            wpl_aps_search_boundaries<?php echo $this->activity_id; ?>(overlay, google.maps.drawing.OverlayType.CIRCLE);
        });
    }
}

function wpl_aps_draw_searched_shape<?php echo $this->activity_id; ?>()
{
    var shape_type = <?php echo (wpl_request::getVar('sf_radiussearchunit', NULL) ? 'google.maps.drawing.OverlayType.CIRCLE' : 'google.maps.drawing.OverlayType.POLYGON'); ?>;
    
    if(shape_type === google.maps.drawing.OverlayType.CIRCLE)
    {
        var radius = <?php echo wpl_units::convert(wpl_request::getVar('sf_radiussearchradius', 0), wpl_request::getVar('sf_radiussearchunit', 11), 11); ?>;
        var center = new google.maps.LatLng(<?php echo wpl_request::getVar('sf_radiussearch_lat', 0); ?>, <?php echo wpl_request::getVar('sf_radiussearch_lng', 0); ?>);
        
        /** Draw Circle **/
        var overlay = new google.maps.Circle(
        {
            strokeColor: '#1e74c7',
            strokeOpacity: 0.6,
            strokeWeight: 1,
            editable: true,
            draggable: true,
            fillColor: '#1e90ff',
            fillOpacity: 0.3,
            map: wpl_map<?php echo $this->activity_id; ?>,
            center: center,
            radius: radius,
            clickable:false
        });
        
        wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.CIRCLE, overlay);
        wpl_aps_set_overlay_listeners<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.CIRCLE, overlay);
        
        /** push to array **/
        wpl_aps_draw_array.push(overlay);
    }
    else if(shape_type === google.maps.drawing.OverlayType.POLYGON)
    {
        <?php $polygons = $this->APS->toPolygons(wpl_request::getVar('sf_polygonsearchpoints', '[]')); ?>
        var coords = [
            <?php if(isset($polygons[0])) foreach($polygons[0] as $point): ?>
            new google.maps.LatLng(<?php echo $point[0]; ?>, <?php echo $point[1]; ?>),
            <?php endforeach; ?>
        ];
        
        if(coords.length)
        {
            /** Draw Polygon **/
            var overlay = new google.maps.Polygon(
            {
                strokeColor: '#1e74c7',
                strokeOpacity: 0.6,
                strokeWeight: 1,
                editable: true,
                draggable: true,
                fillColor: '#1e90ff',
                fillOpacity: 0.3,
                map: wpl_map<?php echo $this->activity_id; ?>,
                paths: coords,
                clickable:false
            });
            
            wpl_aps_extend_bounds<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
            wpl_aps_set_overlay_listeners<?php echo $this->activity_id; ?>(google.maps.drawing.OverlayType.POLYGON, overlay);
            
            /** push to array **/
            wpl_aps_draw_array.push(overlay);
        }
    }
}

function wpl_aps_remove_shapes<?php echo $this->activity_id; ?>()
{
    /** delete overlays **/
    for(var i = 0; i < wpl_aps_draw_array.length; i++) wpl_aps_draw_array[i].setMap(null);
    wpl_aps_draw_array = new Array();
    
    wpl_aps_toggle_remove_shapes_button<?php echo $this->activity_id; ?>('hide');
    wpl_aps_search_boundaries<?php echo $this->activity_id; ?>({}, null);
}

function wpl_aps_toggle_remove_shapes_button<?php echo $this->activity_id; ?>(method)
{
    if(typeof method == 'undefined') method = 'hide';
    
    if(method == 'hide')
    {
        wplj("#wpl_aps_remove_shapes_button<?php echo $this->activity_id; ?>").remove();
    }
    else if(method == 'show')
    {
        if(!wplj('.wpl_aps_container #wpl_aps_remove_shapes_button<?php echo $this->activity_id; ?>').length) wplj('.wpl_aps_container').append('<div id="wpl_aps_remove_shapes_button<?php echo $this->activity_id; ?>" class="wpl-aps-remove-shapes-btn"><button type="button" class="btn btn-primary" onclick="wpl_aps_remove_shapes<?php echo $this->activity_id; ?>();"><?php echo addslashes(__('Remove Shapes!', 'real-estate-listing-realtyna-wpl')); ?></button></div>');
    }
}

function wpl_preview_property_add_events<?php echo $this->activity_id; ?>(dataMarker)
{
    wpl_property_preview_hide_infowindow<?php echo $this->activity_id; ?>();

    count = dataMarker.pids.split(',');

    var multiple_properties = false;

    if(count.length > 1)
    {
        box_html = '<div class="wpl-map-marker-price-multiple"> <div>'+count.length+'</div></div>';
        multiple_properties = true;
    }
    else box_html = '<div class="wpl-map-marker-price" style="background-image: url(<?php echo wpl_global::get_wpl_url(); ?>assets/img/listing_types/gicon/map_marker-price-bkg.png);"> <div>'+dataMarker.title+'</div></div>';
////

    marker_options =
    {
        position: new google.maps.LatLng(dataMarker.googlemap_lt, dataMarker.googlemap_ln),
        map: <?php echo ($this->show_marker ? 'wpl_map'.$this->activity_id : 'null'); ?>,
        property_ids: dataMarker.pids,
        flat: true,
        borderRadius: 5,
        content: box_html
    };
    
//infowindow style
    infowindow_options =
    {
        disableAutoPan: false,
        maxWidth: 700,
        pixelOffset: new google.maps.Size(-172, -64),
        alignBottom: true,
        
        zIndex: null,
        boxStyle: {
            opacity: 1,
            height: '186px',
            width: '345px',
            borderRadius: '8px'
            //background: 'blue'
        },
        closeBoxURL: (multiple_properties ? "<?php echo wpl_global::get_wpl_asset_url('img/icon/icon-close.svg'); ?>" : ''),
        infoBoxClearance: new google.maps.Size(1, 1),
        isHidden: false,
        pane: "floatPane",
        enableEventPropagation: false
    };

    wpl_google_marker_array<?php echo $this->activity_id; ?>[dataMarker.id] = new RichMarker(marker_options);

    wpl_google_infowindow_array<?php echo $this->activity_id; ?>[dataMarker.id] = new InfoBox(infowindow_options);

    var infowindow = wpl_google_infowindow_array<?php echo $this->activity_id; ?>[dataMarker.id];
    var marker = wpl_google_marker_array<?php echo $this->activity_id; ?>[dataMarker.id];

    /** extend the bounds to include each marker's position **/
    if(wpl_map_bounds_extend<?php echo $this->activity_id; ?>) bounds<?php echo $this->activity_id; ?>.extend(marker.position);

    loaded_markers<?php echo $this->activity_id; ?>.push(dataMarker.id);
    markers_array<?php echo $this->activity_id; ?>.push(marker);

//MAP ROLLOVER PREVIEW

    google.maps.event.addListener(marker, 'mouseover', function(event)
    {
        infowindow.close();

        if(marker.html)
        {
            infowindow.setContent('<div class="" style="text-align:center">'+marker.html+'</div>');
            infowindow.open(wpl_map<?php echo $this->activity_id; ?>, marker);
        }
        else
        {
            infowindow.setContent('<div style="text-align:center"><img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader1.gif'); ?>" /></div>');
            infowindow.open(wpl_map<?php echo $this->activity_id; ?>, marker);

            var render_html = preview_get_infowindow_html<?php echo $this->activity_id; ?>(marker.property_ids);
            render_html.success(function(infowindow_html)
            {
                marker.html = infowindow_html;
                wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?> = null;

                //infowindow.setContent('any value');
                infowindow.setContent(marker.html);
            });
            
            wplj("#wpl_map_canvas<?php echo $this->activity_id; ?>").ajaxComplete(function()
            {
                infowindow.setContent(marker.html);
                infowindow.open(wpl_map<?php echo $this->activity_id; ?>, marker);
            });
        }
       // alert (marker.html);

        wpl_map<?php echo $this->activity_id; ?>.setOptions({draggableCursor:'pointer'});
    });

    /** mouseout event and show property show preview **/
    google.maps.event.addListener(marker, 'mouseout', function(event)
    {
        wplj("#wpl_map_canvas<?php echo $this->activity_id; ?>").unbind('ajaxComplete');

        if(!multiple_properties) infowindow.close();
        wpl_map<?php echo $this->activity_id; ?>.setOptions({draggableCursor:''});
    });

    /** Click event and show property show preview **/
    google.maps.event.addListener(marker, 'click', function()
    {
        if(!multiple_properties) wpl_property_preview_html(dataMarker.id);
    });
}

function wpl_property_preview_show_infowindow<?php echo $this->activity_id; ?>(id)
{
    if(!wplj('#wpl_property_listing_container').hasClass('wpl-property-listing-mapview')) return;
    if(wplj('.wpl_property_listing_list_view_container').css('opacity') != '1') return;

    wpl_property_preview_hide_infowindow<?php echo $this->activity_id; ?>();

    marker = wpl_google_marker_array<?php echo $this->activity_id; ?>[id];
    infowindow = wpl_google_infowindow_array<?php echo $this->activity_id; ?>[id];

    if(typeof marker != 'undefined' && marker.html)
    {
        infowindow.setContent(marker.html);
        infowindow.open(wpl_map<?php echo $this->activity_id; ?>, marker);
    }
    else
    {
        if(typeof infowindow != 'undefined')
        {
            infowindow.setContent('<div class="tst" style="text-align:center"><img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader1.gif'); ?>" /></div>');
            infowindow.open(wpl_map<?php echo $this->activity_id; ?>, marker);

            var render_html = preview_get_infowindow_html<?php echo $this->activity_id; ?>(marker.property_ids);
            render_html.success(function(infowindow_html)
            {
                marker.html = infowindow_html;
                wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?> = null;
            });

            wplj("#wpl_map_canvas<?php echo $this->activity_id; ?>").ajaxComplete(function()
            {
                infowindow.setContent(marker.html);
                infowindow.open(wpl_map<?php echo $this->activity_id; ?>, marker);
            });
        }
    }
}

function wpl_property_preview_hide_infowindow<?php echo $this->activity_id; ?>()
{
    //if(wplj('.wpl_property_listing_list_view_container').css('opacity') != '1') return;

    // disabling the ajaxComplete on wpl_map_canvas
    wplj("#wpl_map_canvas<?php echo $this->activity_id; ?>").unbind('ajaxComplete');

    if(!wplj('#wpl_property_listing_container').hasClass('wpl-property-listing-mapview')) return;

    for(var x in wpl_google_infowindow_array<?php echo $this->activity_id; ?>)
    {
        wpl_google_infowindow_array<?php echo $this->activity_id; ?>[x].setMap(null);
    }
}

function wpl_property_preview_html(id)
{
    wplj("#wpl_map_canvas<?php echo $this->activity_id; ?>").unbind('ajaxComplete');

    if(wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?> != null) wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?>.abort();

    // Show Loading
    html_ajLoader = Realtyna.ajaxLoader.show('.wpl_property_listing_container', 'normal', 'center', true, '#fff', 3);
    setTimeout(function()
    {
    //PROPERTY CALLOUT CLICK
        wplj.ajax(
        {
            url: '<?php echo wpl_global::get_full_url(); ?>',
            data: 'wpl_format=f:property_show:raw&pid='+id+'&tpl=internal_preview',
            type: 'GET',
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data)
            {
                wplj('#wpl_property_preview_html<?php echo $this->activity_id; ?>').html(data);
                wplj._realtyna.lightbox.open({'href':'#wpl_property_preview_html<?php echo $this->activity_id; ?>'},
                {
                    closeClass: 'wpl-property-preview-close-btn',
                    callbacks:
                    {
                        beforeOpen: function()
                        {
                            Realtyna.ajaxLoader.hide(html_ajLoader);
                            if(wplj('.wpl-gallery-metro').find('.gallery_no_image').length == 0)
                            {
                                wplj('.wpl-gallery-metro').lightGallery(
                                {
                                    selector: '.wpl-gallery-metro .lslide'
                                });
                            }
                        }
                    }
                });
            }
        });
    }, 100);
}

function wpl_create_multiple_circles(request)
{
    <?php $unit = wpl_units::get_unit(wpl_global::get_setting('multiple_radius_location_unit')); ?>

    if (request == '' || !request)
    {
        <?php
        $sf_multipleradiussearch = wpl_request::getVar('sf_multipleradiussearch', '');

        if($sf_multipleradiussearch != ''):

            $sf_multipleradiussearch = trim(urldecode($sf_multipleradiussearch), ',');
            $array_locations = explode('|', $sf_multipleradiussearch);
            $exp_location_data = array();

            foreach($array_locations as $location)
            {
                $exp_loc = explode(':', $location);
                $city = str_replace('city-', '', $exp_loc[0]);
                $radius = str_replace('radius-', '', $exp_loc[1]);
                $complete_address = str_replace('address-', '', $exp_loc[2]);

                $exp_location_data[$city] = array('location'=>str_replace('_', ' ', $city), 'radius'=>$radius, 'complete_address'=>$complete_address);
            }
        ?>
        raduis = '<?php echo json_encode($exp_location_data); ?>';
        <?php else: ?>
        raduis = '';
        <?php endif; ?>
    }
    else
    {
        raduis_json = '';
        request_array = request.split('&');
        str_query = 'sf_multipleradiussearch';

        for (var i = 0; i < request_array.length; i++)
        {
            var radius_search = request_array[i].split('=');
            if (radius_search[0] == str_query)
            {
                raduis_json = radius_search[1];

                if(raduis_json != '')
                {
                    raduis_json = decodeURIComponent(raduis_json);
                    raduis_json = raduis_json.split('|');

                    var array_location_data = [];
                    for(var i in raduis_json)
                    {
                        if(raduis_json[i] == '') continue;

                        var exp_data = raduis_json[i].split(':');

                        var city = exp_data[0].replace('city-', '');
                        var radius = exp_data[1].replace('radius-', '');
                        var complete_address = exp_data[2].replace('address-', '');

                        var data_location = {'location': city.replace('_', ' '), 'radius':radius, 'complete_address':complete_address};
                        array_location_data.push(data_location);
                    }
                    raduis_json = JSON.stringify(array_location_data);
                    break;
                }
                else break;
            }
        }
        raduis = raduis_json;
    }

    if(raduis != '')
    {
        var radius_multiple = JSON.parse(raduis);
        if (radius_multiple != '')
        {
            for (var i in radius_multiple)
            {
                var radius_unit = parseInt(radius_multiple[i].radius) * parseInt(<?php echo $unit['tosi']; ?>);
                wpl_create_circle_fromGeocode(radius_multiple[i].complete_address, radius_unit);
            }
        }
    }
    else
    {
        if(circles_<?php echo $this->activity_id; ?> != null)
        {
            for (var i in circles_<?php echo $this->activity_id; ?>) circles_<?php echo $this->activity_id; ?>[i].setMap(null)
        }
    }
}

var circles_<?php echo $this->activity_id; ?> = [];
function wpl_create_circle_fromGeocode(location, radius)
{
    if(circles_<?php echo $this->activity_id; ?> != null)
    {
        for (var i in circles_<?php echo $this->activity_id; ?>) circles_<?php echo $this->activity_id; ?>[i].setMap(null)
    }

    if(wplj.trim(location) == '') return;

    geocoder = new google.maps.Geocoder();
    geocoder.geocode(
    {
        'address': location
    },
    function(results, status)
    {
        if (status == google.maps.GeocoderStatus.OK)
        {
            var lat = results[0].geometry.location.lat();
            var lng = results[0].geometry.location.lng();
            var circle_center = new google.maps.LatLng(lat, lng);

            var circles = new google.maps.Circle({
                strokeColor: '#29a9df',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#29a9df',
                fillOpacity: 0.2,
                map: wpl_map<?php echo $this->activity_id; ?>,
                center: circle_center,
                radius: radius
            });

            circles_<?php echo $this->activity_id; ?>.push(circles);
        }
        else console.log("<?php echo __('Geocode was not successful for the following reason::', 'real-estate-listing-realtyna-wpl'); ?> " + status);
    });
}

function wpl_manage_events_for_loaded_markers<?php echo $this->activity_id; ?>()
{
    google.maps.event.addListener(wpl_map<?php echo $this->activity_id; ?>, 'idle', function(event)
    {
        wpl_property_preview_hide_infowindow<?php echo $this->activity_id; ?>();
    });

    google.maps.event.addListener(wpl_map<?php echo $this->activity_id; ?>, 'zoom_changed', function(event)
    {
        wpl_property_preview_hide_infowindow<?php echo $this->activity_id; ?>();
    });
}

function preview_get_infowindow_html<?php echo $this->activity_id; ?>(property_ids, callback)
{
    if(wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?> != null) wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?>.abort();

    wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?> = wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=c:functions:ajax&wpl_function=infowindow&property_ids='+property_ids+'&wpltarget=<?php echo wpl_request::getVar('wpltarget', 0); ?>&tpl=infowindow_preview',
        type: 'GET'
    });

    return wpl_aps_preview_tooltip_ajax<?php echo $this->activity_id; ?>;
}

jQuery(document).ready(function()
{
    wpl_add_googlemaps_callbacks(function()
    {
        wpl_aps_init<?php echo $this->activity_id; ?>();
        wpl_aps_init_draw<?php echo $this->activity_id; ?>();

        <?php
        $sf_multipleradiussearch = wpl_request::getVar('sf_multipleradiussearch', '');
        if(!empty($sf_multipleradiussearch)) echo 'wpl_create_multiple_circles()';
        ?>

        /** add the functions to the div each listing **/
        <?php if($this->map_property_preview): ?>
        wplj(document).ajaxComplete(function( event, xhr, settings )
        {
            wplj('.wpl_prp_cont').each(function()
            {
                var id = wplj(this).attr('id').replace('wpl_prp_cont', '');

                wplj(this).attr('onmouseover', 'wpl_property_preview_show_infowindow<?php echo $this->activity_id; ?>('+id+')');
                wplj(this).attr('onmouseout', 'wpl_property_preview_hide_infowindow<?php echo $this->activity_id; ?>()');
            });
        });

        var html_preview = '<div id="wpl_property_preview_html<?php echo $this->activity_id; ?>"></div>';
        wplj('body').append(html_preview);
        <?php endif; ?>
    });
});
</script>