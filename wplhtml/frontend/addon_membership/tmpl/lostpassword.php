<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.lostpassword.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.lostpassword.css', true, true);
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <div class="wpl_dashboard_header"><?php echo __('Lost password', 'real-estate-listing-realtyna-wpl'); ?></div>
    <div id="wpl_lostpassword_form_container">
        <div id="wpl_lostpassword_form_show_messages"></div>
        <form id="wpl_lostpassword_form" class="wpl-forgot-password-form" method="POST" onsubmit="wpl_lostpassword(); return false;">
            <div class="wpl-forgot-password-form-row">
                <label for="wpl_lostpassword_usermail"><?php echo __('Username or Email', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="text" name="usermail" id="wpl_lostpassword_usermail" size="20" />
            </div>
			<div class="wpl-forgot-password-form-row wpl-recaptcha">
                <label for="wpl-forgot-password-message"></label>
                <?php echo wpl_global::include_google_recaptcha('gre_lostpassword'); ?>
            </div>
            <div class="wpl-forgot-password-form-row">
            <?php
            /**
             * Fires lostpassword_form action of WordPress for integrating third party plugins such as captcha plugins
             */
            do_action('lostpassword_form');
            ?>
            </div>
            <div class="wpl-forgot-password-form-row">
                <input type="hidden" name="wpltarget" value="<?php echo $this->target; ?>" />
                <input type="hidden" name="token" id="wpl_membership_lostpassword_token" value="<?php echo $this->wpl_security->token(); ?>" />
                <input type="hidden" name="redirect_to" id="wpl_membership_lostpassword_redirect_to" value="<?php echo urlencode($this->redirect_to); ?>" />
                <button type="submit" class="button" id="wpl_lostpassword_submit"><?php echo __('Send', 'real-estate-listing-realtyna-wpl'); ?></button>
            </div>
        </form>
    </div>
</div>