<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
var freeze = false;

<?php if(wpl_global::check_addon('aps') and (isset($this->property_css_class) and $this->property_css_class == 'map_box')): ?>
var wpl_sp_selector_div = '.wpl_property_listing_listings_container';
var wpl_sp_append_div = '.wpl_property_listing_listings_container';
<?php else: ?>
var wpl_sp_selector_div = window;
var wpl_sp_append_div = '#wpl_property_listing_container';
<?php endif; ?>

/** Listen to scroll event **/
var wpl_scroll_pagination_listener = wplj(wpl_sp_selector_div).on('scroll', function()
{
    wpl_scroll_pagination();
});

function wpl_scroll_pagination()
{
    if(freeze) return;
    
    // get full height of div (from top and actual div height).
	var elem = wplj(wpl_sp_append_div);
	var divend = elem[0].scrollHeight + elem.offset().top;
    var selector_elem = wplj(wpl_sp_selector_div);

	if(wplj(wpl_sp_selector_div).scrollTop() >= divend - wplj(wpl_sp_selector_div).innerHeight() || wplj(wpl_sp_selector_div).scrollTop() + wplj(wpl_sp_selector_div).innerHeight() >= selector_elem[0].scrollHeight)
	{
        freeze = true;
		wpl_listing_current_page += 1;
        
		if(wpl_listing_current_page > wpl_listing_total_pages)
        {
            freeze = false;
            return;
        }
        
        var ajax_loader = '<div id="scroll_pagination_loader" class="clear" title="<?php echo __('Loading ...', 'real-estate-listing-realtyna-wpl'); ?>"><ul class="bokeh"><li></li><li></li><li></li><li></li></ul></div>';
		wplj(wpl_sp_append_div).append(ajax_loader);
		
        wpl_listing_request_str = wpl_update_qs('wplpage', wpl_listing_current_page, wpl_listing_request_str);
        wpl_listing_request_str = wpl_update_qs('wplpcc', wpl_current_property_css_class, wpl_listing_request_str);
        
		var request_str = 'wpl_format=f:property_listing:raw&'+wpl_listing_request_str;
        
        try
        {
            var current_js_url = window.location.href;
            current_js_url = wpl_update_qs('wplpage', wpl_listing_current_page, current_js_url);
            history.pushState({wplpage: wpl_listing_current_page}, "page "+wpl_listing_current_page, current_js_url);
        }
        catch(err){}
        
        /** Load Markers **/
        if(typeof wpl_load_map_markers == 'function') wpl_load_map_markers(wpl_listing_request_str);
                
		wplj.ajax(
		{
			url: '<?php echo wpl_global::get_full_url(); ?>',
			data: request_str,
			type: 'GET',
			//async: true,
			cache: false,
			timeout: 30000,
			success: function(data)
			{
				if(data !== '' && (wpl_listing_total_pages - wpl_listing_current_page) == 0)
					wpl_listing_total_pages++;
				
                wplj('#scroll_pagination_loader').remove();
				wplj('.wpl_property_listing_listings_container').append(data);
				
                if(typeof wpl_fix_no_image_size == 'function') setTimeout(function(){wpl_fix_no_image_size();}, 50);
				freeze = false;

                /*Image lazy loading*/
				if(wplj('.wpl_property_listing_container').hasClass('wpl-property-listing-mapview')){
					wplj(".wpl-property-listing-mapview .wpl_property_listing_listings_container .lazyimg").Lazy({
						appendScroll: wplj('.wpl-property-listing-mapview .wpl_property_listing_listings_container')
					});
				} else {
					wplj(".lazyimg").Lazy();
				}
			}
		});
	}
}
</script>