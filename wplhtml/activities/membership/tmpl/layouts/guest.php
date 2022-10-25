<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl-membership-activity-guest-wp">
    <?php if($this->show_login_link): ?>
    <div class="wpl-login-button-container">
        <a class="wpl-login-link" href="<?php echo $this->membership->URL('login'); ?>" target="_blank"><?php echo __('Login', 'real-estate-listing-realtyna-wpl'); ?></a>
    </div>
    <?php endif; ?>

    <?php if($this->show_register_link): ?>
    <div class="wpl-register-button-container">
        <a class="wpl-register-link" href="<?php echo $this->membership->URL('register'); ?>" target="_blank"><?php echo __('Register', 'real-estate-listing-realtyna-wpl'); ?></a>
    </div>
    <?php endif; ?>
</div>