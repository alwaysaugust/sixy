<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$logged_in = wpl_users::get_cur_user_id();
$watched = wpl_db::exists($this->property_id, 'wpl_addon_watch_changes', 'pid');
?>
<div class="wpl-links-watch-changes-wp" id="wpl_form_watch_changes_container">
    <form class="wpl-gen-form-wp" id="wpl_watch_changes_form" onsubmit="wpl_watch_changes_submit(); return false;" novalidate="novalidate">
        <div class="wpl-gen-form-row">
            <input type="checkbox" name="wplfdata[enabled]" id="wpl-links-watch-changes-enabled" <?php echo $watched ? 'checked' : ''; ?>>
            <span for="wpl-links-watch-changes-enabled"><?php echo __('Inform me when property is updated', 'real-estate-listing-realtyna-wpl'); ?></span>
        </div>
        
        <?php if(!$logged_in): ?>
            <div id="wpl_watch_changes_form_register">
                <div class="wpl-gen-form-row">
                    <hr />
                    <p><span class="wpl-util-icon-key"></span><?php echo __('Create an account to watch changes of properties (it only takes a minute).', 'real-estate-listing-realtyna-wpl'); ?></p>
                </div>

                <div class="wpl-gen-form-row">
                    <label for="wpl-links-watch-changes-email"><?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                    <input type="text" name="wplfdata[email]" id="wpl-links-watch-changes-email">
                </div>

                <div class="wpl-gen-form-row">
                    <label for="wpl-links-watch-changes-phone"><?php echo __('Phone', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                    <input type="text" name="wplfdata[phone]" id="wpl-links-watch-changes-phone">
                </div>
            </div>

            <div id="wpl_watch_changes_form_login" style="display: none;">
                <div class="wpl-gen-form-row">
                    <hr />
                    <p><span class="wpl-util-icon-key"></span><?php echo __('Login to watch changes of properties.', 'real-estate-listing-realtyna-wpl'); ?></p>
                </div>

                <div class="wpl-gen-form-row">
                    <label for="wpl-links-watch-changes-username"><?php echo __('Username', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                    <input type="text" name="wplfdata[username]" id="wpl-links-watch-changes-username">
                </div>

                <div class="wpl-gen-form-row">
                    <label for="wpl-links-watch-changes-password"><?php echo __('Password', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                    <input type="password" name="wplfdata[password]" id="wpl-links-watch-changes-password">
                </div>
            </div>

            <input type="hidden" name="wplfdata[guest_method]" id="wpl_watch_changes_guest_method" value="register" />
        <?php endif; ?>

        <input type="hidden" name="wplfdata[property_id]" value="<?php echo $this->property_id; ?>" />
        <?php wpl_security::nonce_field('wpl_watch_changes_form'); ?>
        
        <div class="wpl-gen-form-row wpl-util-right">
            <input class="wpl-gen-btn-1" type="submit" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>

        <div class="wpl_show_message"></div>
    </form>

    <?php if(!$logged_in): ?>
        <div id="wpl_watch_changes_toggle" class="wpl-gen-form-row wpl-addon-ss-toggle-btns">
            <p id="wpl_watch_changes_toggle_register">
                <?php echo sprintf(__('Already a member? %s.', 'real-estate-listing-realtyna-wpl'), '<a href="#" onclick="wpl_watch_changes_toggle(\'login\');return false;">'.__('Login', 'real-estate-listing-realtyna-wpl').'</a>'); ?>
            </p>
            <p class="wpl-util-hidden" id="wpl_watch_changes_toggle_login">
                <?php echo sprintf(__('Not a member? %s.', 'real-estate-listing-realtyna-wpl'), '<a href="#" onclick="wpl_watch_changes_toggle(\'register\');return false;">'.__('Register', 'real-estate-listing-realtyna-wpl').'</a>'); ?>
            </p>
        </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
function wpl_watch_changes_toggle(type)
{
    if(typeof type === undefined) type = 'register';
    
    if(type === 'login')
    {
        wplj("#wpl_watch_changes_toggle_register").hide();
        wplj("#wpl_watch_changes_toggle_login").show();
        
        wplj("#wpl_watch_changes_form_register").hide();
        wplj("#wpl_watch_changes_form_login").show();
    }
    else
    {
        wplj("#wpl_watch_changes_toggle_register").show();
        wplj("#wpl_watch_changes_toggle_login").hide();
        
        wplj("#wpl_watch_changes_form_register").show();
        wplj("#wpl_watch_changes_form_login").hide();
    }
    
    /** Set type to form values **/
    wplj("#wpl_watch_changes_guest_method").val(type);
}
</script>