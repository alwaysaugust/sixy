<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$all_available_memberships = wpl_users::get_wpl_memberships("AND `membership_type`!='7'");
?>
<div class="page fixed" id="brokerage_container">
    <div class="odd">
        <span class="maccess_name"><?php echo __('Total number of agents', 'real-estate-listing-realtyna-wpl'); ?></span>
        <input type="text" name="maccess_brokerage_agent_limit" id="maccess_brokerage_agent_limit" value="<?php echo isset($this->data->maccess_brokerage_agent_limit) ? $this->data->maccess_brokerage_agent_limit : 10; ?>" />
    </div>
    <div>
        <span class="maccess_name"><?php echo __('Available memberships for agents', 'real-estate-listing-realtyna-wpl'); ?></span>
        <?php $available_memberships = explode(',', (isset($this->data->maccess_brokerage_memberships) ? $this->data->maccess_brokerage_memberships : '')); ?>
        <select name="maccess_brokerage_memberships" id="maccess_brokerage_memberships" multiple="multiple" data-chosen-opt="width:100%">
            <?php foreach($all_available_memberships as $membership): ?>
                <option value="<?php echo $membership->id; ?>" <?php echo (in_array($membership->id, $available_memberships) ? 'selected="selected"' : ''); ?>><?php echo __($membership->membership_name, 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>