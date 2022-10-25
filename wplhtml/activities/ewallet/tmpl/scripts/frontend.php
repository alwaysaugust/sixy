<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
var wpl_transaction_id;
var ajax_loader_element = "#wpl_ewallet_charge_ajax_loader";
var ewallet_activity_message_path = '.wpl-ewallet-charge-show-messages';

// eWallet Charge Form Submit
wplj('#wpl_ewallet_charge_form').on('submit', function(e)
{
    e.preventDefault();

    wplj.when(wpl_ewallet_transaction())
    .fail(function()
    {
        wpl_show_messages('<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>', '.wpl-ewallet-charge-show-messages', 'wpl_red_msg');
        wplj(ajax_loader_element).html('');

    }).done(function(response)
    {
        if(response.success)
        {
            wpl_transaction_id = response.data.transaction_id;
            wpl_get_checkout_form(wpl_transaction_id);
        }
        else
        {
            wpl_show_messages('<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>', ewallet_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element).html('');
        }
    });
});

function wpl_ewallet_transaction()
{
    wpl_remove_message(ewallet_activity_message_path);
	var amount = wplj('#wpl_ewallet_charge_amount').val();
    
    if(parseInt(amount) < parseInt('<?php echo $this->minimum_charge; ?>'))
    {
        wpl_show_messages("<?php echo sprintf(__('You cannot charge less than %s', 'real-estate-listing-realtyna-wpl'), $this->minimum_charge_rendered); ?>", ewallet_activity_message_path, 'wpl_red_msg');
        wplj('#wpl_ewallet_charge_amount').val(<?php echo $this->minimum_charge; ?>);
        return false;
    }
    
    wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    
    var unit_id = wplj('#wpl_ewallet_charge_unit_id').val();
	request_str = "wpl_format=f:payments:ajax&wpl_function=ewallet_charge&amount="+amount+"&unit_id="+unit_id+"&type=ewallet&mode=1&title=<?php echo __('e-Wallet Charge', 'real-estate-listing-realtyna-wpl'); ?>&_wpnonce=<?php echo wpl_security::create_nonce('wpl_payments_ewallet_charge'); ?>";
    
	/** run ajax query **/
    return wplj.ajax(
    {
        type: "POST",
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: request_str,
        dataType: 'json'
    });
}

function wpl_get_checkout_form(wpl_transaction_id)
{
    wpl_remove_message(ewallet_activity_message_path);
    
    var request_str = 'wpl_format=f:payments:raw&wplmethod=checkout&transaction_id='+wpl_transaction_id+'&disabled_gateways=ewallet';
            
	/** run ajax query **/
	wplj.ajax(
	{
		type: 'POST',
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(html)
		{
            wplj('#wpl_ewallet_checkout_page_container').removeClass('wpl_hidden_element');
            wplj('#wpl_ewallet_checkout_page_container').html(html);
            wplj(ajax_loader_element).html('');
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages("<?php echo __('Cannot generate the checkout page!', 'real-estate-listing-realtyna-wpl'); ?>", ewallet_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_ewallet_payment_success(response)
{
    wpl_show_messages(response.message, ewallet_activity_message_path, 'wpl_green_msg');
    wplj('#wpl_ewallet_checkout_page_container').html('');
    
    setTimeout(function(){window.location.reload()}, 1000);
}

function wpl_ewallet_payment_error(response)
{
    if(response)
    {
        wpl_show_messages(response.message, ewallet_activity_message_path, 'wpl_red_msg');
    }
    else
    {
        wpl_show_messages("<?php echo __('Error Occurred!', 'real-estate-listing-realtyna-wpl'); ?>", ewallet_activity_message_path, 'wpl_red_msg');
        wplj('#wpl_ewallet_checkout_page_container').html('');
    }
}
</script>