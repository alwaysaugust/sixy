<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
var wpl_transaction_id;
var ajax_loader_element_renew = "#wpl_renew_membership_ajax_loader";
var ajax_loader_element_upgrade = "#wpl_upgrade_membership_ajax_loader";
var membership_activity_message_path = '.wpl-membership-activity-show-messages';

function wpl_renew_membership_transaction(user_id)
{
    wpl_remove_message(membership_activity_message_path);

    wplj(ajax_loader_element_renew).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    request_str = "wpl_format=f:addon_membership:ajax&wpl_function=renew_membership&user_id="+user_id+"&type=renew_membership&mode=2&title=<?php echo __('Renew Membership', 'real-estate-listing-realtyna-wpl'); ?>";

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
                if(response.data.free)
                {
                    wpl_show_messages("<?php echo __('Your membership renewed successfully.', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_green_msg');
                    wplj(ajax_loader_element_renew).html('');
                    window.location.reload();
                }
                else
                {
                    wpl_transaction_id = response.data.transaction_id;
                    wpl_renew_membership_get_checkout_form(wpl_transaction_id);
                }
            }
            else
            {
                wpl_show_messages("<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
                wplj(ajax_loader_element_renew).html('');
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element_renew).html('');
        }
    });
}

function wpl_renew_membership_get_checkout_form(wpl_transaction_id)
{
    wpl_remove_message(membership_activity_message_path);

    request_str = 'wpl_format=f:payments:raw&wplmethod=checkout&transaction_id='+wpl_transaction_id;

    /** run ajax query **/
    wplj.ajax(
    {
        type: 'POST',
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: request_str,
        success: function(html)
        {
            wplj("#wpl-membership-activity-wpluser<?php echo $this->activity_id; ?> #wpl-membership-renew-btn").hide();
            wplj('#wpl_membership_activity_page_container_renew').removeClass('wpl_hidden_element');
            wplj('#wpl_membership_activity_page_container_renew').html(html);
            wplj(ajax_loader_element_renew).html('');
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo __('Cannot generate the checkout page!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element_renew).html('');
        }
    });
}

function wpl_renew_membership_payment_success(response)
{
    wpl_show_messages(response.message, membership_activity_message_path, 'wpl_green_msg');
    wplj('#wpl_membership_activity_page_container_renew').html('');

    setTimeout(function(){window.location.reload()}, 1000);
}

function wpl_renew_membership_payment_error(response)
{
    if(response)
    {
        wpl_show_messages(response.message, membership_activity_message_path, 'wpl_red_msg');
    }
    else
    {
        wpl_show_messages("<?php echo __('Error Occurred!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
        wplj('#wpl_membership_activity_page_container_renew').html('');
    }
}

function wpl_upgrade_membership_list(user_id)
{
    wpl_remove_message(membership_activity_message_path);

    wplj(ajax_loader_element_upgrade).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    request_str = "wpl_format=f:addon_membership:raw&wplmethod=change_membership&user_id="+user_id+"&link_type=onclick";

    /** run ajax query **/
    wplj.ajax(
    {
        type: "POST",
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: request_str,
        dataType: 'HTML',
        success: function(html)
        {
            wplj("#wpl-membership-activity-wpluser<?php echo $this->activity_id; ?> #wpl-membership-change-membership-btn").hide();
            wplj('#wpl_membership_activity_page_container_upgrade').removeClass('wpl_hidden_element');
            wplj('#wpl_membership_activity_page_container_upgrade').html(html);
            wplj(ajax_loader_element_upgrade).html('');
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo __('Cannot create a membership list!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element_upgrade).html('');
        }
    });
}

function wpl_upgrade_membership_transaction(membership_id, user_id)
{
    wpl_remove_message(membership_activity_message_path);

    wplj('#wpl_membership_activity_page_container_upgrade').html('');

    wplj(ajax_loader_element_upgrade).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    request_str = "wpl_format=f:addon_membership:ajax&wpl_function=change_membership&user_id="+user_id+"&membership_id="+membership_id+"&type=change_membership&mode=2&title=<?php echo __('Change Membership', 'real-estate-listing-realtyna-wpl'); ?>";

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
                if(response.data.free)
                {
                    wpl_show_messages("<?php echo __('Your membership changed successfully.', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_green_msg');
                    wplj(ajax_loader_element_upgrade).html('');
                    window.location.reload();
                }
                else
                {
                    wpl_transaction_id = response.data.transaction_id;
                    wpl_upgrade_membership_get_checkout_form(wpl_transaction_id);
                }
            }
            else
            {
                wpl_show_messages("<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
                wplj(ajax_loader_element_upgrade).html('');
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo __('Unable to create transaction!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element_upgrade).html('');
        }
    });
}

function wpl_upgrade_membership_get_checkout_form(wpl_transaction_id)
{
    wpl_remove_message(membership_activity_message_path);

    request_str = 'wpl_format=f:payments:raw&wplmethod=checkout&transaction_id='+wpl_transaction_id;

    /** run ajax query **/
    wplj.ajax(
    {
        type: 'POST',
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: request_str,
        success: function(html)
        {
            wplj('#wpl_membership_activity_page_container_upgrade').removeClass('wpl_hidden_element');
            wplj('#wpl_membership_activity_page_container_upgrade').html(html);
            wplj(ajax_loader_element_upgrade).html('');
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo __('Cannot generate the checkout page!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
            wplj(ajax_loader_element_upgrade).html('');
        }
    });
}

function wpl_upgrade_membership_payment_success(response)
{
    wpl_show_messages(response.message, membership_activity_message_path, 'wpl_green_msg');
    wplj('#wpl_membership_activity_page_container_upgrade').html('');

    setTimeout(function(){window.location.reload()}, 1000);
}

function wpl_upgrade_membership_payment_error(response)
{
    if(response)
    {
        wpl_show_messages(response.message, membership_activity_message_path, 'wpl_red_msg');
    }
    else
    {
        wpl_show_messages("<?php echo __('Error Occurred!', 'real-estate-listing-realtyna-wpl'); ?>", membership_activity_message_path, 'wpl_red_msg');
        wplj('#wpl_membership_activity_page_container_upgrade').html('');
    }
}
</script>