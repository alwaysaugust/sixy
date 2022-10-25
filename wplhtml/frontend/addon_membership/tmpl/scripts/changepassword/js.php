<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
wplj('#wpl_dashboard_change_password_form').on('submit', function(e)
{
    e.preventDefault();

    var request = wplj('#wpl_dashboard_change_password_form').serialize();
    var message_path = '#wpl_dashboard_change_password_message';
    
    /** Make change password button disabled **/
    wplj("#wpl_change_password_submit").attr('disabled', 'disabled');
    
    wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=change_password&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make change password button enabled **/
            wplj("#wpl_change_password_submit").removeAttr('disabled');
            
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
            }
            else
            {
                if(response.data.token) wplj("#wpl_change_password_token").val(response.data.token);
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        }
    });
});
</script>