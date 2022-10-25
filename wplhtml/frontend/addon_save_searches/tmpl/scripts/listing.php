<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>

<script id="wpl-js-confirm-message" type="text/x-handlebars-template">
    <div>{{message}}</div>
    <div class="wpl-addon-save-search-msg-btns">
        <span class="wpl-addon-save-search-yes-btn" {{onclick yes.func yes.param}}>{{yes.text}}</span>
        <span class="wpl-addon-save-search-no-btn" {{onclick no.func no.param}}>{{no.text}}</span>
    </div>
</script>

<script type="text/javascript">
Handlebars.registerHelper('onclick', function(funcName, funcParam)
{
    funcName = Handlebars.Utils.escapeExpression(funcName);
    funcParam =  '(' + funcParam.join(',') + ')';

    var result = 'onclick="' + funcName + funcParam +  '"';

    return new Handlebars.SafeString(result);
});

function wpl_addon_save_searches_delete_all(user_id, confirmed)
{
    var message_path = '#wpl_save_searches_list_show_messages';

    if(!confirmed)
	{
        var message = Handlebars.compile(wplj("#wpl-js-confirm-message").html())({
            message : '<?php echo __('Are you sure you want to remove all items?', 'real-estate-listing-realtyna-wpl'); ?>',
            yes     : {
                text    : '<?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?>',
                func    : 'wpl_addon_save_searches_delete_all',
                param   : [user_id, 1]
            },
            no      : {
                text    :'<?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?>',
                func    : 'wpl_remove_message',
                param   : ['\''+ message_path+ '\'']
            }
        });

		wpl_show_messages(message, message_path);
		return false;
	}
	else if(confirmed) wpl_remove_message(message_path);
    
    /** Show AJAX loader **/
    var wpl_ajax_loader = Realtyna.ajaxLoader.show("#wpl_addon_save_searches_delete_all", 'tiny', 'rightOut');
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&wpl_function=delete&user_id='+user_id,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            if(response.success)
            {
                wplj("#wpl_addon_save_searches_list_container").remove();
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
            }
            
            /** Remove AJAX loader **/
            Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Remove AJAX loader **/
            Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        }
    });
}

function wpl_addon_save_searches_delete(id, confirmed)
{
    var message_path = '#wpl_save_searches_list_show_messages';
    
    if(!confirmed)
	{
        var message = Handlebars.compile(wplj("#wpl-js-confirm-message").html())({
            message : '<?php echo __('Are you sure you want to remove this item?', 'real-estate-listing-realtyna-wpl'); ?>',
            yes     : {
                text    : '<?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?>',
                func    : 'wpl_addon_save_searches_delete',
                param   : [id, 1]
            },
            no      : {
                text    :'<?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?>',
                func    : 'wpl_remove_message',
                param   : ['\''+ message_path+ '\'']
            }
        });

		wpl_show_messages(message, message_path);
		return false;
	}
	else if(confirmed) wpl_remove_message(message_path);
    
    /** Show AJAX loader **/
    var wpl_ajax_loader = Realtyna.ajaxLoader.show("#wpl_addon_save_searches_delete"+id, 'tiny', 'rightOut');
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&wpl_function=delete&id='+id,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            if(response.success)
            {
                wplj("#wpl_addon_save_search_item"+id).remove();
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
            }
            
            /** Remove AJAX loader **/
            Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Remove AJAX loader **/
            Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        }
    });
}

function wpl_addon_save_searches_alias(id)
{
    var message_path = '#wpl_save_searches_list_show_messages';
    
    /** Show AJAX loader **/
    var wpl_ajax_loader = Realtyna.ajaxLoader.show("#wpl_addon_save_searches_alias"+id, 'tiny', 'rightIn');
    var alias = wplj("#wpl_addon_save_searches_alias"+id).val();
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&wpl_function=alias&id='+id+'&alias='+alias,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                if(response.data.url) wplj("#wpl_addon_save_searches_link"+id).attr('href', response.data.url);
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
            }
            
            /** Remove AJAX loader **/
            Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Remove AJAX loader **/
            Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        }
    });
}

function wpl_addon_save_searches_notify_mode(id, mode)
{
    if(wplj.trim(mode) == '') return;
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&wpl_function=notify_mode&id='+id+'&mode='+mode,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response) { },
        error: function(jqXHR, textStatus, errorThrown) { }
    });
}

function wpl_addon_save_searches_edit_name(id)
{
    var search_name = wplj('#wpl_save_search_name'+id).text();
    var search_href = wplj('#wpl_save_search_name'+id).attr('href');

    input_html = '<input type="text" id="wpl_save_search_edited_name'+id+'" name="wpl_save_search_edited_name'+id+'" value="'+search_name+'"> <span class="wpl-save-search-edited-name wpl-addon-save-search-edit-btn" onclick="wpl_addon_save_searches_edited_name('+id+', \''+search_href+'\')"></span>';

    wplj('#wpl_save_search_name_content'+id).html(input_html);
}

function wpl_addon_save_searches_edited_name(id, href)
{
    if(wplj.trim(id) == '') return;

    var name = wplj('#wpl_save_search_edited_name'+id).val();

    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&wpl_function=edit_search_name&id='+id+'&search_name='+name,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response) {
            input_html = '<a target="_blank" id="wpl_save_search_name'+id+'" href="">'+name+'</a> <span id="wpl_save_search_edit_name'+id+'" class="wpl-save-search-edit-name wpl-addon-save-search-edit-btn" onclick="wpl_addon_save_searches_edit_name('+id+')"></span>';

            wplj('#wpl_save_search_name_content'+id).html(input_html);
            wplj('#wpl_save_search_name'+id).attr('href', href);
        },
        error: function(jqXHR, textStatus, errorThrown) { }
    });
}
</script>