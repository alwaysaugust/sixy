<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="page fixed" id="pricing_container">
	<div class="odd">
        <span class="maccess_short_name"><?php echo __('Subscription Price', 'real-estate-listing-realtyna-wpl'); ?></span>
		<input type="text" name="maccess_price" id="maccess_price" value="<?php echo $this->data->maccess_price; ?>" />
        <select name="maccess_price_unit" id="maccess_price_unit" class="short_selectbox" data-chosen-opt="width:100px">
            <?php foreach($this->units as $unit): ?>
            <option value="<?php echo $unit['id']; ?>" <?php echo ($this->data->maccess_price_unit == $unit['id'] ? 'selected="selected"' : ''); ?>><?php echo $unit['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <div class="tip"><?php echo __('0 means free!', 'real-estate-listing-realtyna-wpl'); ?></div>
	</div>
	<div>
        <span class="maccess_short_name"><?php echo __('Subscription Period', 'real-estate-listing-realtyna-wpl'); ?></span>
        <input type="text" name="maccess_period" id="maccess_period" value="<?php echo $this->data->maccess_period; ?>" />
        <span><?php echo __('Days', 'real-estate-listing-realtyna-wpl'); ?></span>
        <div class="tip"><?php echo __('-1 means unlimited.', 'real-estate-listing-realtyna-wpl'); ?></div>
	</div>
    <div class="odd">
        <span class="maccess_short_name"><?php echo __('Can Renew?', 'real-estate-listing-realtyna-wpl'); ?></span>
        <select name="maccess_renewable" id="maccess_renewable" onchange="wpl_membership_toggle('.wpl_can_renew_cnt');">
            <option value="0" <?php echo ($this->data->maccess_renewable == 0 ? 'selected="selected"' : ''); ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php echo ($this->data->maccess_renewable == 1 ? 'selected="selected"' : ''); ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
        <div class="wpl_can_renew_cnt" style="<?php echo ($this->data->maccess_renewable == 0 ? 'display: none;' : ''); ?>">
            <span class="maccess_short_name"><?php echo __('Renew Price', 'real-estate-listing-realtyna-wpl'); ?></span>
            <input type="text" name="maccess_renewal_price" id="maccess_renewal_price" value="<?php echo $this->data->maccess_renewal_price; ?>" />
            <select name="maccess_renewal_price_unit" id="maccess_renewal_price_unit" class="short_selectbox" data-chosen-opt="width:100px">
                <?php foreach($this->units as $unit): ?>
                <option value="<?php echo $unit['id']; ?>" <?php echo ($this->data->maccess_renewal_price_unit == $unit['id'] ? 'selected="selected"' : ''); ?>><?php echo $unit['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
	</div>
    <div>
        <span class="maccess_short_name"><?php echo __('Can Change Membership?', 'real-estate-listing-realtyna-wpl'); ?></span>
        <select name="maccess_upgradable" id="maccess_upgradable" onchange="wpl_membership_toggle('.wpl_can_upgrade_cnt');">
            <option value="0" <?php echo ($this->data->maccess_upgradable == 0 ? 'selected="selected"' : ''); ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php echo ($this->data->maccess_upgradable == 1 ? 'selected="selected"' : ''); ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
        <div class="wpl_can_upgrade_cnt" style="<?php echo ($this->data->maccess_upgradable == 0 ? 'display: none;' : ''); ?>">
            <span class="maccess_short_name"><?php echo __('Change Membership to', 'real-estate-listing-realtyna-wpl'); ?></span>
            <?php $upgradable_to = explode(',', $this->data->maccess_upgradable_to); ?>
            <select name="maccess_upgradable_to" id="maccess_upgradable_to" multiple="multiple" data-chosen-opt="width:100%">
                <option value="1" <?php if(trim($this->data->maccess_upgradable_to) == '' or in_array(1, $upgradable_to)) echo 'selected="selected"'; ?>><?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></option>
                <?php foreach($this->memberships as $membership): ?>
                <option value="<?php echo $membership->id; ?>" <?php echo (in_array($membership->id, $upgradable_to) ? 'selected="selected"' : ''); ?>><?php echo __($membership->membership_name, 'real-estate-listing-realtyna-wpl'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
	</div>
    <div class="odd">
        <span class="maccess_short_name"><?php echo __('Recurring Payment?', 'real-estate-listing-realtyna-wpl'); ?></span>
        <select name="maccess_recurring" id="maccess_recurring">
            <option value="0" <?php echo ((isset($this->data->maccess_recurring) and $this->data->maccess_recurring == 0) ? 'selected="selected"' : ''); ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php echo ((isset($this->data->maccess_recurring) and $this->data->maccess_recurring == 1) ? 'selected="selected"' : ''); ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
</div>