<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function wpl_save_search()
{
    var request = wplj('#wpl_save_searches_form').serialize();
    var message_path = '#wpl_save_searches_form_show_messages';
    var wplmethod = wplj("#wpl_save_search_guest_method").val();
    
    /** Make button disabled **/
    wplj("#wpl_save_searches_"+wplmethod+"_save_submit").attr('disabled', 'disabled');
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make button enabled **/
            wplj("#wpl_save_searches_"+wplmethod+"_save_submit").removeAttr('disabled');
            
            if(response.success)
            {
                wplj("#wpl_save_searches_form").hide();
                wplj("#wpl_save_searches_toggle").hide();
                
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                 
                if(typeof wpl_refresh_searchwidget_counter == 'function')
                {
                    wpl_refresh_searchwidget_counter();
                }
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                if(response.data.token) wplj("#wpl_save_searches_token").val(response.data.token);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make button enabled **/
            wplj("#wpl_save_searches_"+wplmethod+"_save_submit").removeAttr('disabled');
        }
    });
}

function wpl_save_search_toggle(type)
{
    if(typeof type === undefined) type = 'register';
    
    if(type === 'login')
    {
        wplj("#wpl_save_searches_toggle_register").hide();
        wplj("#wpl_save_searches_toggle_login").show();
        
        wplj("#wpl_save_searches_form_register").hide();
        wplj("#wpl_save_searches_form_login").show();
        
        wplj("#wpl_save_searches_register_save_submit").hide();
        wplj("#wpl_save_searches_login_save_submit").show();
    }
    else
    {
        wplj("#wpl_save_searches_toggle_register").show();
        wplj("#wpl_save_searches_toggle_login").hide();
        
        wplj("#wpl_save_searches_form_register").show();
        wplj("#wpl_save_searches_form_login").hide();
        
        wplj("#wpl_save_searches_register_save_submit").show();
        wplj("#wpl_save_searches_login_save_submit").hide();
    }
    
    /** Set type to form values **/
    wplj("#wpl_save_search_guest_method").val(type);
}
</script>