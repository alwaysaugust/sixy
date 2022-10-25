<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-row">
    <label for="wpl_o_show_membership_info"><?php echo __('Membership Info', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_membership_info]" id="wpl_o_show_membership_info">
        <option value="1" <?php if(isset($this->options->show_membership_info) and $this->options->show_membership_info) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->show_membership_info) and !$this->options->show_membership_info) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_show_renew_button"><?php echo __('Renew Button', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_renew_button]" id="wpl_o_show_renew_button">
        <option value="1" <?php if(isset($this->options->show_renew_button) and $this->options->show_renew_button) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->show_renew_button) and !$this->options->show_renew_button) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_show_upgrade_button"><?php echo __('Upgrade Button', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_upgrade_button]" id="wpl_o_show_upgrade_button">
        <option value="1" <?php if(isset($this->options->show_upgrade_button) and $this->options->show_upgrade_button) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->show_upgrade_button) and !$this->options->show_upgrade_button) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_show_login_link"><?php echo __('Login Link', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_login_link]" id="wpl_o_show_login_link">
        <option value="1" <?php if(isset($this->options->show_login_link) and $this->options->show_login_link) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->show_login_link) and !$this->options->show_login_link) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_show_register_link"><?php echo __('Register Link', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_register_link]" id="wpl_o_show_register_link">
        <option value="1" <?php if(isset($this->options->show_register_link) and $this->options->show_register_link) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->show_register_link) and !$this->options->show_register_link) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_frontend_listing_manager"><?php echo __('Frontend Listing Manager', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[frontend_listing_manager]" id="wpl_o_frontend_listing_manager">
        <option value="0" <?php if(isset($this->options->frontend_listing_manager) and !$this->options->frontend_listing_manager) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->frontend_listing_manager) and $this->options->frontend_listing_manager) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>