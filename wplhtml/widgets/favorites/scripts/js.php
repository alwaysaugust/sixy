<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$favorites_count = 0;
if(is_array($this->pids) and ($count = sizeof($this->pids))) $favorites_count = $count;
?>

<script type="text/javascript">
var wpl_favorites_count = <?php echo $favorites_count ?>;

function wpl_update_favorites(count)
{
	if(typeof(count) !== 'undefined') wpl_favorites_count = count;

	if(parseInt(wpl_favorites_count))
	{
		wplj('#wpl-favorite-contact-agent').removeAttr('disabled');
		wplj('#wpl-favorite-compare').removeAttr('disabled');
	}
	else
	{
		wplj('#wpl-favorite-contact-agent').attr('disabled', 'disabled');
		wplj('#wpl-favorite-compare').attr('disabled', 'disabled');
	}
}

jQuery(document).ready(function()
{
	wpl_load_favorites();
});

function wpl_load_favorites()
{
    var url = window.location.href.split('?')[0];
    request_str = 'wpl_format=f:property_listing:ajax_pro&wpl_function=favorites_load&image_width=<?php echo $this->image_width; ?>&image_height=<?php echo $this->image_height; ?>&wpltarget=<?php echo $this->wpltarget; ?>&_='+Math.random();

	

	ajax = wpl_run_ajax_query(url, request_str, false, 'JSON', 'GET');
	ajax.success(function(data)
	{
	   
        var source = wplj("#wpl_favorites_tmpl<?php echo $this->widget_id; ?>").html();
        var template = Handlebars.compile(source);
        var html = template(data);

        if(typeof(data.count) !== 'undefined') wpl_update_favorites(data.count);
        
        wplj("#wpl_favorite_listings_cnt<?php echo $this->widget_id; ?>").html(html);
	});
}

function wpl_remove_favorite(pid)
{
    const icon = wplj('#wpl_favorites_item<?php echo $this->widget_id; ?>_'+pid + ' .wpl_favorite_item_remove');
    icon.html('');
    icon.css('border', 'none');
    Realtyna.ajaxLoader.show(icon, 'tiny', 'center', true, '#000', 3, {
        backgroundColor: 'rgba(255,255,255,0)'
    });
    
    request_str = 'wpl_format=f:property_listing:ajax_pro&wpl_function=favorites_control&pid='+pid+'&mode=0';
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, false, 'JSON', 'GET');
	
	ajax.success(function(data)
	{
		wplj('#wpl_favorite_remove_'+pid).toggle().parent('li').toggleClass('added');;
		wplj('#wpl_favorite_add_'+pid).toggle();
        
		if(typeof wpl_load_favorites == 'function')
        {
            wpl_load_favorites();
        }
        
        if(typeof wpl_refresh_searchwidget_counter == 'function')
        {
            wpl_refresh_searchwidget_counter();
        }
	});
	
	return false;
}

function wpl_send_favorite(widget_id)
{
	ajax_loader_element = '#wpl_favorite_ajax_loader_'+widget_id;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    wpl_remove_message('#wpl_favorite_message_'+widget_id);
	
	request_str = 'wpl_format=f:property_listing:ajax_pro&wpl_function=favorites_send&'+wplj('#wpl_favorite_form'+widget_id).serialize();
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element, 'JSON', 'GET');
	
	ajax.success(function(data)
	{
		if(data.success === 1)
		{
			wpl_show_messages(data.message, '#wpl_favorite_message_'+widget_id, 'wpl_green_msg');
            wplj('#wpl_favorite_form' + widget_id).hide();
		}
		else if(data.success === 0)
		{
			wpl_show_messages(data.message, '#wpl_favorite_message_'+widget_id, 'wpl_red_msg');
		}
        
		wplj(ajax_loader_element).html('');
	});
	
	return false;
}
</script>