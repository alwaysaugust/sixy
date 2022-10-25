<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
});

function wpl_signup()
{
    var message_path = '#wpl_signup_form_show_messages';
    wpl_remove_message(message_path);
    
    <?php if($this->signup_method == 'all' and !wpl_users::check_user_login()): ?>
    if(wplj("#wpl_membership_username").val() == '' || wplj("#wpl_membership_email").val() == '')
    {
        wpl_show_messages("<?php echo addslashes(__('Please fill required data.', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        return false;
    }
    <?php endif; ?>
    
    if(!wplj("#wpl_membership_signup_for").length && (wplj("#wpl_membership_blogname").val() == '' || wplj("#wpl_membership_blog_title").val() == ''))
    {
        wpl_show_messages("<?php echo addslashes(__('Please fill required data.', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        return false;
    }
    else if(wplj("#wpl_membership_signup_for").is(':checked') && (wplj("#wpl_membership_blogname").val() == '' || wplj("#wpl_membership_blog_title").val() == ''))
    {
        wpl_show_messages("<?php echo addslashes(__('Please fill required data.', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        return false;
    }
    
    var request = wplj('#wpl_signup_form').serialize();
    
    /** Make signup button disabled **/
    wplj('#wpl_membership_signup_button').attr('disabled', 'disabled');
    
    wpl_register_ajax = wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=signup&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make signup button enabled **/
            wplj('#wpl_membership_signup_button').removeAttr('disabled');
            
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                wplj('#wpl_membership_signup_container').slideUp();
            }
            else
            {
                if(response.data.token) wplj('#wpl_membership_signup_token').val(response.data.token);
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make signup button enabled **/
            wplj('#wpl_membership_signup_button').removeAttr('disabled');
        }
    });
}

function wpl_site_toggle()
{
    if(wplj("#wpl_membership_signup_for").is(':checked')) wplj("#wpl_membership_signup_site_container").slideDown();
    else wplj("#wpl_membership_signup_site_container").slideUp();
}
</script>