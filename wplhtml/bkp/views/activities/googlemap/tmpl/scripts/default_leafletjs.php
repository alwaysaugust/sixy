<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>

<link rel="stylesheet" href="//leaflet.github.io/Leaflet.draw/src/leaflet.draw.css"/>
<link rel="stylesheet" href="//leaflet.github.io/Leaflet.draw/docs/examples/libs/leaflet.css"/>

<script type="text/javascript">
    var markers<?php echo $this->activity_id; ?> = <?php echo json_encode($this->markers); ?>;
    var wpl_map<?php echo $this->activity_id; ?>;
    var mymap;
    var mapMarkers = [];
    var do_map_move_actions = true;
    var map_move_work= false;
    var default_lt<?php echo $this->activity_id; ?> = '<?php echo $this->default_lt; ?>';
    var default_ln<?php echo $this->activity_id; ?> = '<?php echo $this->default_ln; ?>';
    var default_zoom<?php echo $this->activity_id; ?> = <?php echo $this->default_zoom; ?>;

    function wpl_remove_map_markers(){
        for(var i = 0; i < mapMarkers.length; i++){
            if(mapMarkers[i] !== null){
                mymap.removeLayer(mapMarkers[i]);
            }
            mapMarkers[i] = null;
        }
    }

    function wpl_add_map_markers(markers){
        for(var i = 0; i < markers.length; i++)
        {
            const dataMarker = markers[i];

            const marker_content = '<?php echo wpl_global::get_wpl_url(); ?>assets/img/listing_types/gicon/'+dataMarker.gmap_icon;

            var customIcon = L.icon({
                iconUrl: marker_content,
                iconSize:     [40, 40], // size of the icon
            });

            if(dataMarker.gmap_icon == 'price-icon1.png') {
                customIcon = L.divIcon({
                    html: '<div class="wpl-map-marker-price" style="font: 400 10px Roboto, Arial, sans-serif; background-image: url(<?php echo wpl_global::get_wpl_url(); ?>assets/img/listing_types/gicon/price-icon1.png); background-size: 42px 25px;background-repeat: no-repeat; color: #1b1e1f; width: 42px; height: 25px; line-height:16px; text-align:center"> <div>'+ dataMarker.title.replace('C$', '$') +'</div> </div>',
                    iconAnchor: [20, 10],
                });
            }

            var marker = new L.marker([dataMarker.googlemap_lt, dataMarker.googlemap_ln], {icon: customIcon});
            marker.bindPopup('loading...');

            marker.on('click', function(e) {
                do_map_move_actions = false;
                var popup = e.target.getPopup();
                ajax_layout = '&tpl=infowindow';

                wplj.ajax(
                    {
                        url: '<?php echo wpl_global::get_full_url(); ?>',
                        data: 'wpl_format=c:functions:ajax&wpl_function=infowindow&property_ids=' + dataMarker.id + '&wpltarget=<?php echo wpl_request::getVar('wpltarget', 0); ?>'+ajax_layout,
                        type: 'GET',
                        async: false,
                        cache: false,
                        timeout: 30000,
                        success: function(data)
                        {
                            popup.setContent(data);
                            popup.update();
                            setTimeout(function() {
                                do_map_move_actions = true;
                            }, 1000);
                        }
                    });
            });

            mapMarkers.push(marker);
            mymap.addLayer(marker);
        }
    }

    function wpl_load_map_markers(request_str, delete_markers, focus_center)
    {
        do_map_move_actions = false;
        if(typeof delete_markers == 'undefined') delete_markers = false;
        if(typeof focus_center == 'undefined') focus_center = true;

        wplj("#wpl_map_canvas<?php echo $this->activity_id; ?>").append('<div class="map_search_ajax_loader" style="z-index:999"><img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader4.gif'); ?>" /></div>');

        request_str = 'wpl_format=f:property_listing:raw&wplmethod=get_markers&'+request_str;
        var markers;

        wplj.ajax(
            {
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                type: 'GET',
                dataType: 'jSON',
                async: true,
                cache: false,
                timeout: 30000,
                success: function(data)
                {
                    /** AJAX loader **/
                    wplj(".map_search_ajax_loader").remove();

                    markers<?php echo $this->activity_id; ?> = data.markers;

                    wpl_remove_map_markers();

                    var markers = data.markers;
                    if(focus_center){
                        if(markers.length){
                            lt = markers[0].googlemap_lt;
                            ln = markers[0].googlemap_ln;

                            mymap.panTo(new L.LatLng(lt, ln));


                        }
                    }

                    wpl_add_map_markers(markers);

                    setTimeout(function(){ do_map_move_actions = true; }, 1000);
                }
            });

    }

    function map_move(){
        console.log('map is movingg');
        const bounds = mymap.getBounds();
        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();

        let lat_max = ne.lat;
        let lat_min = sw.lat;
        let lng_min = sw.lng;
        let lng_max = ne.lng;

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

        let request_str = 'sf_tmin_googlemap_lt='+lat_min+'&sf_tmax_googlemap_lt='+lat_max+'&sf_tmin_googlemap_ln='+lng_min+'&sf_tmax_googlemap_ln='+lng_max+'&sf_locationtextsearch=&sf_multiplelocationtextsearch=&sf_advancedlocationtextsearch=';

        /**
         * @david TODO: temporary comment
         */
        wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
    }

    function wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str)
    {
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

        try
        {
            let search_str = '<?php echo wpl_property::get_property_listing_link($this->wpltarget); ?>';

            if(search_str.indexOf('?') >= 0) search_str = search_str+'&'+request_str;
            else search_str = search_str+'?'+request_str;

            history.pushState({search: 'WPL'}, "<?php echo addslashes(__('Search Results', 'real-estate-listing-realtyna-wpl')); ?>", search_str);
        }
        catch(err){}

        if(typeof wpl_load_map_markers == 'function'){
            wpl_load_map_markers(request_str, false, false);
            do_map_move_actions = true;
        }

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



                    setTimeout(function(){

                        if(jQuery('#ajax_sort_properties').length<1){

                            var sort_options = '';

                            sort_options='<a role="option"  class="dropdown-item" aria-disabled="false" tabindex="0" aria-selected="false"><span class="text">Default Order</span></a>';
                            sort_options +='<a role="option"  class="dropdown-item" aria-disabled="false" tabindex="0" aria-selected="false"><span class="text">Price - Low to High</span></a>';
                            sort_options +='<a role="option"  class="dropdown-item" aria-disabled="false" tabindex="0" aria-selected="false"><span class="text">Price - High to Low</span></a>';
                            sort_options +='<a role="option"  class="dropdown-item" aria-disabled="false" tabindex="0" aria-selected="false"><span class="text">Featured Listings First</span></a>';
                            sort_options +='<a role="option"  class="dropdown-item" aria-disabled="false" tabindex="0" aria-selected="false"><span class="text">Date - Old to New</span></a>';
                            sort_options +='<a role="option"  class="dropdown-item" aria-disabled="false" tabindex="0" aria-selected="false"><span class="text">Date - New to Old</span></a>';


                            jQuery('.wpl_sort_options_container').after('<div class="page-title-wrap">            <div class="d-flex align-items-center" style="flex-direction: inherit;">                <div class="page-title flex-grow-1">                    </div>                <div class="sort-by">	<div class="d-flex align-items-center" style="flex-direction: inherit;">		<div class="sort-by-title">			Sort by:		</div><!-- sort-by-title -->  		<div class="dropdown bootstrap-select form-control"><select id="ajax_sort_properties" class="selectpicker form-control" title="Default Order" data-live-search="false" data-dropdown-align-right="auto" tabindex="-98">			<option value="">Default Order</option>			<option value="a_price">Price - Low to High</option>            <option value="d_price">Price - High to Low</option>                        <option value="featured_first">Featured Listings First</option>                        <option value="a_date">Date - Old to New</option>            <option value="d_date">Date - New to Old</option>		</select><button type="button" class="btn dropdown-toggle btn-light bs-placeholder" data-toggle="dropdown" role="button" data-id="ajax_sort_properties" title="Default Order" aria-expanded="false"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Default Order</div></div> </div></button><div class="dropdown-menu  dropdown-menu-right" role="combobox" style="max-height: 426px; overflow: hidden; min-height: 133px; min-width: 180px;"><div class="inner show" role="listbox" aria-expanded="false" tabindex="-1" style="max-height: 424px; overflow-y: auto; min-height: 131px;">'+sort_options+'</div></div></div><!-- selectpicker -->	</div><!-- d-flex --></div><!-- sort-by -->                               </div>          </div>');

                            jQuery('#ajax_sort_properties').selectpicker();

                            jQuery('.wpl_pagination_container li').addClass('page-item');
                            jQuery('.wpl_pagination_container li a').addClass('page-link');

                        }

                    },400);


                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                }
            });
    }

    function search_boundaries(event){
        if('polygon' === event.layerType){
            const bounds = event.layer._latlngs[0];
            let paths_strings = '[';
            for(index in bounds){
                paths_strings += bounds[index].lat + ',' + bounds[index].lng + ';';
            }
            paths_strings += ']';
            if(typeof wpl_listing_request_str != 'undefined')
            {
                wpl_listing_request_str = wpl_update_qs('sf_radiussearchunit', '', wpl_listing_request_str);
                wpl_listing_request_str = wpl_update_qs('sf_radiussearchradius', '', wpl_listing_request_str);
                wpl_listing_request_str = wpl_update_qs('sf_radiussearch_lat', '', wpl_listing_request_str);
                wpl_listing_request_str = wpl_update_qs('sf_radiussearch_lng', '', wpl_listing_request_str);
            }

            var request_str = 'sf_polygonsearch=1&sf_polygonsearchpoints='+paths_strings;

        }else if('circle' === event.layerType){
            const unit_id = 11; // meters
            const radius = event.layer._mRadius;
            const latitude = event.layer._latlng.lat;
            const longitude = event.layer._latlng.lng;

            if(typeof wpl_listing_request_str != 'undefined')
            {
                wpl_listing_request_str = wpl_update_qs('sf_polygonsearch', '', wpl_listing_request_str);
                wpl_listing_request_str = wpl_update_qs('sf_polygonsearchpoints', '', wpl_listing_request_str);
            }

            var request_str = 'sf_radiussearchunit='+unit_id+'&sf_radiussearchradius='+radius+'&sf_radiussearch_lat='+latitude+'&sf_radiussearch_lng='+longitude;
        }

        wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
    }

    function wpl_initialize<?php echo $this->activity_id; ?>(){
        const markers = markers<?php echo $this->activity_id; ?>;

        let lt = default_lt<?php echo $this->activity_id; ?>;
        let ln = default_ln<?php echo $this->activity_id; ?>;
        if(markers.length){
            lt = markers[0].googlemap_lt;
            ln = markers[0].googlemap_ln;
        }

        var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib });

        mymap = new L.Map('wpl_map_canvas<?php echo $this->activity_id; ?>');

        /*let loadedByResize = false;
         charlie comment it */
        mymap.on('load', function() {
            setTimeout(function(){
                console.log('resized');
                loadedByResize = true;
                mymap.invalidateSize();
            }, 1000)
        });

        mymap.setView(new L.LatLng(lt, ln), default_zoom<?php echo $this->activity_id; ?>);
        var drawnItems = L.featureGroup().addTo(mymap);

        L.control.layers(
            { 'osm': osm.addTo(mymap) },
            { 'drawlayer': drawnItems },
            { position: 'topleft', collapsed: false }
        ).addTo(mymap);

        mymap.addControl(new L.Control.Draw({
            edit: {
                featureGroup: drawnItems,
                poly: {
                    allowIntersection: true
                },
                edit: false,
            },
            draw: {
                polygon: {
                    allowIntersection: true,
                    showArea: true
                },
                marker: false,
                circlemarker: false,
                polyline: false,
                rectangle: false,
            }
        }));

        mymap.on(L.Draw.Event.CREATED, function (event) {
            drawnItems.eachLayer(function (removeLayer) {
                drawnItems.removeLayer(removeLayer);
            });

            var layer = event.layer;
            drawnItems.addLayer(layer);

            search_boundaries(event);
        });

        mymap.on(L.Draw.Event.DELETED, function (event) {
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

            wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);

        });

        var wpl_aps_search_timeout = '';


        mymap.on('moveend', function() {
            if(!loadedByResize && do_map_move_actions && map_move_work){
                clearTimeout(wpl_aps_search_timeout);
                wpl_aps_search_timeout = setTimeout(function(){ map_move() }, 1000);
            }
            if(loadedByResize) loadedByResize = false;
        });


        /*mymap.on('moveend', function() {

            marker_status= wplj("#wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>").is(':checked');
            console.log('marker_status');
            if(marker_status===true)
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

                wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
            }
        });
        */
        wplj('#wpl_googlemap_container<?php echo $this->activity_id; ?>').addClass('wpl-aps-addon');
        wplj('#wpl_googlemap_container<?php echo $this->activity_id; ?> .wpl-map-add-ons').css('z-index', '999');
        wplj('#wpl_googlemap_container<?php echo $this->activity_id; ?> .wpl-map-add-ons').prepend('<div class="wpl_aps_container"></div>');

        wplj('.wpl_aps_container').prepend('<input id="wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>" type="checkbox" /><label for="wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>"><?php echo addslashes(__('Update my search as map is moved.', 'real-estate-listing-realtyna-wpl')); ?></label>');
        wplj("#wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>").prop('checked',true);
        wplj("#wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>").on("change", function()
        {
            map_move_work = wplj(this).is(':checked');
            if(map_move_work === true){

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

                wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
            }

        });
        //wplj("#wpl_aps_map_search_toggle_checkbox<?php echo $this->activity_id; ?>").trigger('change');
        wpl_add_map_markers(markers);

        wplj(".leaflet-control-layers-selector").change( function () {
            if(this.checked === true){
                drawnItems.eachLayer(function (removeLayer) {
                    drawnItems.removeLayer(removeLayer);
                });

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

                wpl_aps_search_request<?php echo $this->activity_id; ?>(request_str);
            }
        });
    }
    function leaflet_map_is_ready(){
        wpl_initialize<?php echo $this->activity_id; ?>();
    }

    jQuery(document).ready(function() {
        /*mymap.setZoom(default_zoom<?php echo $this->activity_id; ?>-1);
        // wpl_initialize<?php //echo $this->activity_id; ?>();
        setTimeout(function(){
            mymap.setZoom(default_zoom<?php //echo $this->activity_id; ?>);
        }, 2000);*/
    });
</script>

<script src="<?php echo plugin_dir_url( __FILE__ ); ?>/leaflet_map.js"></script>