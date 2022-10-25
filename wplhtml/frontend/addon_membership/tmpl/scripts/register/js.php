<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
});

function wpl_register()
{
    var message_path = '#wpl_register_form_show_messages';
    wpl_remove_message(message_path);
    
    if(wplj("#wpl_membership_username").val() == '' || wplj("#wpl_membership_email").val() == '')
    {
        wpl_show_messages("<?php echo addslashes(__('Please fill required data.', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        return false;
    }
    
    <?php if($this->settings['membership_agreement']): ?>
    if(!wplj("#wpl_membership_agreement").is(':checked'))
    {
        wpl_show_messages("<?php echo addslashes(__('Terms and Conditions should be agreed.', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
        return false;
    }
    <?php endif; ?>
    
    var request = wplj('#wpl_subscription_form').serialize();
    
    /** Make register button disabled **/
    wplj('#wpl_membership_register_button').attr('disabled', 'disabled');
    
    wpl_register_ajax = wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:addon_membership:ajax&wpl_function=register&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make register button enabled **/
            wplj('#wpl_membership_register_button').removeAttr('disabled');
            
            if(response.success)
            {
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                wplj('#wpl_membership_register_registration_container').slideUp();
                
                /** Generate Checkout Page **/
                wpl_subscription_generate_checkout(response.data.user_id);
                
                if(response.data.redirect_to) window.location = response.data.redirect_to;
            }
            else
            {
                if(response.data.token) wplj('#wpl_membership_register_token').val(response.data.token);
                
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                if(response.data.redirect_to) window.location = response.data.redirect_to;
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make register button enabled **/
            wplj('#wpl_membership_register_button').removeAttr('disabled');
        }
    });
}

var need_checkout = <?php echo ($this->membership_data->maccess_price ? 1 : 0); ?>;
var wpl_transaction_id = 0;

function wpl_subscription_generate_checkout(user_id)
{
    if(!need_checkout) return false;
    
    wplj("#wpl_membership_register_registration_container").slideUp();
    wplj("#wpl_membership_register_checkout_container").slideDown();
    wplj("#wpl_membership_register_checkout_container").addClass('wpl-checkout-loading');
    
    wplj("#wpl_subscription_steps_registration").removeClass('active');
    wplj("#wpl_subscription_steps_checkout").addClass('active');
    
    wpl_subscription_transaction(user_id);
}

function wpl_subscription_transaction(user_id)
{
    var message_path = '#wpl_register_form_show_messages';
    
	var membership_id = wplj('#wpl_membership_membership_id').val();
    var user_type = wplj('#wpl_membership_user_type').val();
    
	var request_str = "wpl_format=f:addon_membership:ajax&wpl_function=change_membership&user_id="+user_id+"&membership_id="+membership_id+"&user_type="+user_type+"&type=register_membership&title=<?php echo __('Register Membership', 'real-estate-listing-realtyna-wpl'); ?>&is_register=1";
    
	/** run ajax query **/
	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
        dataType: 'json',
		success: function(response)
		{
			if(response.success)
            {
                wpl_transaction_id = response.data.transaction_id;
                wpl_subscription_get_checkout_form(wpl_transaction_id);
            }
            else
            {
                wpl_show_messages('<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>', message_path, 'wpl_red_msg');
                wplj("#wpl_membership_register_checkout_container").removeClass('wpl-checkout-loading');
            }
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages('<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>', message_path, 'wpl_red_msg');
            wplj("#wpl_membership_register_checkout_container").removeClass('wpl-checkout-loading');
		}
	});
}

function wpl_subscription_get_checkout_form(wpl_transaction_id)
{
    var message_path = '#wpl_register_form_show_messages';
    
    request_str = 'wpl_format=f:payments:raw&wplmethod=checkout&transaction_id='+wpl_transaction_id+'&disabled_gateways=ewallet&isregister=1';
            
	/** run ajax query **/
	wplj.ajax(
	{
		type: 'POST',
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(html)
		{
            wplj("#wpl_membership_register_checkout_container").removeClass('wpl-checkout-loading');
            wplj("#wpl_membership_register_checkout_container").html(html);
            
            wpl_remove_message(message_path);
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages("<?php echo __('Cannot generate the checkout page!', 'real-estate-listing-realtyna-wpl'); ?>", message_path, 'wpl_red_msg');
            wplj("#wpl_membership_register_checkout_container").removeClass('wpl-checkout-loading');
		}
	});
}

function wpl_change_membership_payment_success(response)
{
    var message_path = '#wpl_register_form_show_messages';
    
    var gateway = 'unknown';
    if(response.data.gateway) gateway = response.data.gateway;
    
    var waiting = 0;
    if(response.data.waiting) waiting = response.data.waiting;
    
    if(gateway == 'bank' && waiting == 1) wpl_show_messages("<?php echo trim(sprintf(__('Bank receipt successfully submitted and your account will upgrade to <strong>%s</strong> after confirming the receipt.', 'real-estate-listing-realtyna-wpl'), $this->membership_data->membership_name)); ?>", message_path, 'wpl_green_msg');
    else wpl_show_messages("<?php echo sprintf(__('Payment successful.and your account upgraded to <strong>%s</strong>', 'real-estate-listing-realtyna-wpl'), $this->membership_data->membership_name); ?>", message_path, 'wpl_green_msg');
    
    wplj("#wpl_membership_register_checkout_container").slideUp();
}

function wpl_change_membership_payment_error(response)
{
    var message_path = '#wpl_register_form_show_messages';
    
    wpl_show_messages(response.message, message_path, 'wpl_red_msg');
    wplj(".wpl-util-form-messages").hide();
}
</script>