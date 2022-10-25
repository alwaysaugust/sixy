<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.resetpass.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.resetpass.css', true, true);
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <div class="wpl_dashboard_header"><?php echo __('Reset password', 'real-estate-listing-realtyna-wpl'); ?></div>
    <div id="wpl_resetpass_form_container">
        <div id="wpl_resetpass_form_show_messages"></div>
        <form id="wpl_resetpass_form" class="wpl-resetpass-form" method="POST" onsubmit="wpl_resetpass(); return false;">
            <div class="wpl-resetpass-form-row">
                <label for="wpl_resetpass_username"><?php echo __('New Password', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="password" name="password" id="wpl_resetpass_password" size="20" autocomplete="off" />
            </div>
            <div class="wpl-resetpass-form-row">
                <label for="wpl_resetpass_repeat_password"><?php echo __('Repeat', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="password" name="repeat_password" id="wpl_resetpass_repeat_password" size="20" autocomplete="off" />
            </div>
            <div class="wpl-resetpass-form-row">
                <input type="hidden" name="wpltarget" value="<?php echo $this->target; ?>" />
                <input type="hidden" name="key" value="<?php echo $this->key; ?>" />
                <input type="hidden" name="token" id="wpl_membership_resetpass_token" value="<?php echo $this->wpl_security->token(); ?>" />
                <input type="hidden" name="redirect_to" id="wpl_membership_resetpass_redirect_to" value="<?php echo urlencode($this->redirect_to); ?>" />
                <button type="submit" class="button" id="wpl_resetpass_submit"><?php echo __('Reset', 'real-estate-listing-realtyna-wpl'); ?></button>
            </div>
        </form>
    </div>
</div>