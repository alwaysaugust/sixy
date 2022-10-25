<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function wpl_save_search()
{
    var request = wplj('#wpl_save_searches_form').serialize();
    var message_path = '#wpl_save_searches_form_show_messages';
    
    /** Make button disabled **/
    wplj("#wpl_save_searches_submit").attr('disabled', 'disabled');
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:addon_save_searches:ajax&wpl_function=save&'+request,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make button enabled **/
            wplj("#wpl_save_searches_submit").removeAttr('disabled');
            
            if(response.success)
            {
                wplj("#wpl_save_searches_form").hide();
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
            wplj("#wpl_save_searches_submit").removeAttr('disabled');
        }
    });
}
</script>