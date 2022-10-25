<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$login_token = $this->wpl_security->token();
?>
<script type="text/javascript">
wplj(function()
{
    if(wplj('.wpl-interim-login').length)
    {
        wplj('html').addClass('wpl-interim-login');
        wplj('body').children().wrapAll('<div id="wpl-js-rest" style="display: none" />');
        wplj('#wpl_login_form').prependTo('body');
    }
});

function wpl_login()
{
    var request = wplj('#wpl_login_form').serialize();
    var message_path = '#wpl_login_form_show_messages';
    
    /** Make login button disabled **/
    wplj("#wpl_login_submit").attr('disabled', 'disabled');
    
    wpl_login_ajax = wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=login&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make login button enabled **/
            wplj("#wpl_login_submit").removeAttr('disabled');
                
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                
                <?php if($this->interim_login): ?>
                window.parent.wplj(".wp-auth-check-close").trigger('click');
                <?php else: ?>
                if(response.data.redirect_to) window.location = response.data.redirect_to;
                else window.location.reload();
                <?php endif; ?>
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                
                if(response.data.token) wplj("#wpl_membership_login_token").val(response.data.token);
                if(response.data.redirect_to) window.location = response.data.redirect_to;
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make login button enabled **/
            wplj("#wpl_login_submit").removeAttr('disabled');
        }
    });
}

function wpl_logout()
{
    var request = wplj('#wpl_logout_form').serialize();
    var message_path = '#wpl_logout_form_show_messages';
    
    /** Make logout button disabled **/
    wplj("#wpl_logout_submit").attr('disabled', 'disabled');
    
    wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=logout&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make logout button enabled **/
            wplj("#wpl_logout_submit").removeAttr('disabled');
            
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                
                if(response.data.redirect_to) window.location = response.data.redirect_to;
                else window.location.reload();
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                
                if(response.data.token) wplj("#wpl_membership_logout_token").val(response.data.token);
                if(response.data.redirect_to) window.location = response.data.redirect_to;
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make logout button enabled **/
            wplj("#wpl_logout_submit").removeAttr('disabled');
        }
    });
}

function wpl_login_linkedin()
{
    IN.Event.on(IN, "auth", function()
    {
        IN.API.Raw("/people/~").result(function(data)
        {
            var message_path = '#wpl_logout_form_show_messages';
            var token = '<?php echo $login_token; ?>';
            var name = (typeof data.firstName != 'undefined') ? data.firstName + ' ': '';
            name += (typeof data.lastName != 'undefined') ? data.lastName : '';
            var email = (typeof data.email != 'undefined') ? data.email : '';
            var request = "&linkedin_uid="+data.id+"&linkedin_name="+name+"&linkedin_email="+email+"&token="+token;
            
            /** Make login button disabled **/
            wplj("#wpl_login_submit").attr('disabled', 'disabled');

            wplj.ajax(
            {
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: 'wpl_format=f:addon_membership:ajax&wpl_function=login_linkedin&'+request,
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(response)
                {
                    /** Make login button enabled **/
                    wplj("#wpl_login_submit").removeAttr('disabled');
                        
                    if(response.success)
                    {
                        wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                        
                        <?php if($this->interim_login): ?>
                        window.parent.wplj(".wp-auth-check-close").trigger('click');
                        <?php else: ?>
                        if(response.data.redirect_to) window.location = response.data.redirect_to;
                        else window.location.reload();
                        <?php endif; ?>
                    }
                    else
                    {
                        wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                        
                        if(response.data.token) wplj("#wpl_membership_login_token").val(response.data.token);
                        if(response.data.redirect_to) window.location = response.data.redirect_to;
                    }
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
                    
                    /** Make login button enabled **/
                    wplj("#wpl_login_submit").removeAttr('disabled');
                }
            });
        }).error(function(error)
        {
            console.log(error);
        });
    });
}

jQuery(document).ready(function()
{
    jQuery(".wpl_linkedin_button").click(function()
    {
        window.IN.user.authorize();
        IN.Event.on(IN, "auth", wpl_login_linkedin);
    });
});
</script>