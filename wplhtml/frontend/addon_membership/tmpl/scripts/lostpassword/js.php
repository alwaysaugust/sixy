<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
});

function wpl_lostpassword()
{
    var request = wplj('#wpl_lostpassword_form').serialize();
    var message_path = '#wpl_lostpassword_form_show_messages';
    
    /** Make logout button disabled **/
    wplj("#wpl_lostpassword_submit").attr('disabled', 'disabled');
    
    wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=forgot&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make logout button enabled **/
            wplj("#wpl_lostpassword_submit").removeAttr('disabled');
            
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
            }
            else
            {
                if(response.data.token) wplj("#wpl_membership_lostpassword_token").val(response.data.token);
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        }
    });
}
</script>