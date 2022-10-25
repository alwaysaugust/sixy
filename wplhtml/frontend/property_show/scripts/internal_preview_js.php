<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	wplj('.wpl_prp_show_tabs .tabs_container div.tabs_contents').hide();
	wplj('.wpl_prp_show_tabs .tabs_container div.tabs_contents:first').show();
	wplj('.wpl_prp_show_tabs ul.tabs li:first').addClass('active');
 
	wplj('.wpl_prp_show_tabs ul.tabs li a').click(function()
	{
		wplj('.wpl_prp_show_tabs ul.tabs li').removeClass('active');
		wplj(this).parent().addClass('active');
        
		var currentTab = wplj(this).attr('href');
		wplj('.wpl_prp_show_tabs .tabs_container div.tabs_contents').hide();
		wplj(currentTab).show();
		
        <?php if(isset($this->pshow_googlemap_activity_id)): ?>
                
        var init_google_map = wplj(this).attr('data-init-googlemap');
		if(init_google_map && typeof wpl_pshow_map_init<?php echo $this->pshow_googlemap_activity_id; ?> == 'function')
		{
			wpl_pshow_map_init<?php echo $this->pshow_googlemap_activity_id; ?>();
		}
		<?php endif; ?>
        
		return false;
	});

    wplj('.wpl_prp_show_container_preview_will .wpl_prp_show_detail_boxes_title').on('click',function(){
        wplj(this).siblings('.wpl_prp_show_detail_boxes_cont').slideToggle('slow');
        wplj(this).parent().toggleClass('op');
    });

    wplj('.wpl_prp_show_container_preview_will #contact_agent_link span').on('click',function(){
        wplj(".realtyna-lightbox-text-wrap").addClass('contact_info_visible');
        wplj(".wpl_contact_info_overlay").slideDown("normal");
    });
    wplj('.wpl_prp_show_container_preview_will .wpl_contact_close_popup,.wpl_contact_info_overlay,.realtyna-lightbox-title .wpl_share_close_popup').on('click',function(){
        wplj(".realtyna-lightbox-text-wrap").removeClass('contact_info_visible').removeClass('share_form_visible');
        wplj(".wpl_contact_info_overlay").slideUp("normal");
    });

	wplj('.wpl_prp_show_container_preview_will .send_to_friend_link a').on('click',function(){
        wplj(".realtyna-lightbox-text-wrap").addClass('share_form_visible');
        wplj(".wpl_contact_info_overlay").slideDown("fast");
    });

	wplj('.wpl_prp_show_container_preview_will .wpl-metro-next-handle').on('click',function(){
		var leftPos = wplj('.wpl_prp_show_container_preview_will .popup_preview_gallery').scrollLeft();
	  	wplj('.wpl_prp_show_container_preview_will .popup_preview_gallery').animate({scrollLeft: leftPos + 300}, 500);
	});

	wplj('.wpl_prp_show_container_preview_will .wpl-metro-prev-handle').on('click',function(){
		var leftPos = wplj('.wpl_prp_show_container_preview_will .popup_preview_gallery').scrollLeft();
	  	wplj('.wpl_prp_show_container_preview_will .popup_preview_gallery').animate({scrollLeft: leftPos - 300}, 500);
	});
    
    wplj('.realtyna-lightbox-title').hide("fast");
	wpl_listing_set_js_triggers()
});
 
/** Complex unit List/Grid View **/
function wpl_set_property_css_class(pcc)
{
	wpl_current_property_css_class = pcc;

	wplj.ajax(
		{
			url: '<?php echo wpl_global::get_full_url(); ?>',
			data: 'wpl_format=f:property_listing:ajax&wpl_function=set_pcc&pcc='+pcc,
			type: 'GET',
			dataType: 'json',
			cache: false,
			success: function(data)
			{
			}
		});
}

function wpl_listing_set_js_triggers()
{
	wplj('.list_view').on('click', function()
	{
		wplj('.grid_view,.map_view').removeClass('active');
		wplj('.list_view').addClass('active');

		wpl_set_property_css_class('row_box');

		wplj('.wpl-complex-unit-cnt').animate({opacity:0},function()
		{
			wplj(this).removeClass('grid_box').removeClass('map_box').addClass('row_box');
			wplj(this).stop().animate({opacity:1});
		});
	});

	wplj('.grid_view').on('click', function()
	{
		wplj('.list_view,.map_view').removeClass('active');
		wplj('.grid_view').addClass('active');

		wpl_set_property_css_class('grid_box');

		wplj('.wpl-complex-unit-cnt').animate({opacity:0},function()
		{
			wplj(this).removeClass('row_box').removeClass('map_box').addClass('grid_box');
			wplj(this).stop().animate({opacity:1});
		});
	});

	wplj('.map_view').on('click', function()
	{
		wplj('.list_view,.grid_view').removeClass('active');
		wplj('.map_view').addClass('active');

		wpl_set_property_css_class('map_box');

		wplj('.wpl-complex-unit-cnt').animate({opacity:0},function()
		{
			wplj(this).removeClass('row_box').removeClass('grid_box').addClass('map_box');
			wplj(this).stop().animate({opacity:1});
		});
	});
}
</script>