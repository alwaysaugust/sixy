<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
var wpl_transaction_id;
var ajax_loader_element = "#wpl_ewallet_charge_ajax_loader";

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
            wpl_show_messages('<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>', message_path, 'wpl_red_msg');
            wplj(ajax_loader_element).html('');
        }
    });
});

function wpl_ewallet_transaction()
{
    var message_path = '.wpl-ewallet-charge-show-messages';
    wpl_remove_message(message_path);
	var amount = wplj('#wpl_ewallet_charge_amount').val();
    
    if(parseInt(amount) < parseInt('<?php echo $this->minimum_charge; ?>'))
    {
        wpl_show_messages("<?php echo sprintf(__('You cannot charge less than %s', 'real-estate-listing-realtyna-wpl'), $this->minimum_charge_rendered); ?>", message_path, 'wpl_red_msg');
        wplj('#wpl_ewallet_charge_amount').val(<?php echo $this->minimum_charge; ?>);
        return false;
    }
    
    wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    
    var unit_id = wplj('#wpl_ewallet_charge_unit_id').val();
	request_str = "wpl_format=f:payments:ajax&wpl_function=ewallet_charge&amount="+amount+"&unit_id="+unit_id+"&type=ewallet&mode=1&user_id=<?php echo $this->user_id; ?>&title=<?php echo __('e-Wallet Charge', 'real-estate-listing-realtyna-wpl'); ?>&_wpnonce=<?php echo wpl_security::create_nonce('wpl_payments_ewallet_charge'); ?>";
    
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
    var message_path = '.wpl-ewallet-charge-show-messages';
    wpl_remove_message(message_path);
    
    request_str = 'wpl_format=f:payments:raw&wplmethod=checkout&transaction_id='+wpl_transaction_id+'&disabled_gateways=ewallet';
            
	/** run ajax query **/
	wplj.ajax(
	{
		type: 'POST',
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(html)
		{
            /** open lightbox **/
            wplj('#wpl_ewallet_checkout_page_container').html(html);
            wplj('#wpl_ewallet_lightbox_handler').trigger('click');
            wplj(ajax_loader_element).html('');
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages("<?php echo __('Cannot generate the checkout page!', 'real-estate-listing-realtyna-wpl'); ?>", message_path, 'wpl_red_msg');
            wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_ewallet_payment_success(response)
{
    setTimeout(function()
    {
        wplj._realtyna.lightbox.close();
        window.location.reload();
    }, 3000);
}

function wpl_ewallet_payment_error(response)
{
    setTimeout(function()
    {
        if(!response)
        {
            wplj._realtyna.lightbox.close();
            window.location.reload();
        }
    }, 3000);
}
</script>