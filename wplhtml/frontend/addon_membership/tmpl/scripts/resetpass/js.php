<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
});

function wpl_resetpass()
{
    var request = wplj('#wpl_resetpass_form').serialize();
    var message_path = '#wpl_resetpass_form_show_messages';
    
    var password = wplj("#wpl_resetpass_password").val();
    var repeat_password = wplj("#wpl_resetpass_repeat_password").val();
    
    if(password != repeat_password)
    {
        wpl_show_messages("<?php echo addslashes(__('Passwords do not match!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        return false;
    }
    
    /** Make resetpass button disabled **/
    wplj("#wpl_resetpass_submit").attr('disabled', 'disabled');
    
    wpl_resetpass_ajax = wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=resetpass&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make resetpass button enabled **/
            wplj("#wpl_resetpass_submit").removeAttr('disabled');
            
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                
                if(response.data.redirect_to) window.location = response.data.redirect_to;
                else window.location.reload();
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                
                if(response.data.token) wplj("#wpl_membership_resetpass_token").val(response.data.token);
                if(response.data.redirect_to) window.location = response.data.redirect_to;
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make resetpass button enabled **/
            wplj("#wpl_resetpass_submit").removeAttr('disabled');
        }
    });
}
</script>