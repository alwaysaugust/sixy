<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.register.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.register.css', true, true);

// Check if social login is enabled
$fb_app_id = wpl_settings::get('fb_login_appid');
$fb_app_secret = wpl_settings::get('fb_login_secret');
$fb_login_enabled = ($fb_app_id and $fb_app_secret);

$twitter_api_key = wpl_settings::get('twitter_login_key');
$twitter_api_secret = wpl_settings::get('twitter_login_secret');
$twitter_login_enabled = ($twitter_api_key and $twitter_api_secret);

$instagram_app_key = wpl_settings::get('instagram_login_key');
$instagram_app_secret = wpl_settings::get('instagram_login_secret');
$instagram_login_enabled = ($instagram_app_key and $instagram_app_secret);

$google_login_enabled = wpl_settings::get('google_login_api');
$linkedin_login_enabled = wpl_settings::get('linkedin_login_api');

$login_link = wpl_addon_membership::URL('login');
$social_login_enabled = ($fb_login_enabled or $twitter_login_enabled or $instagram_login_enabled or $google_login_enabled or $linkedin_login_enabled);

$membership_color = '';
if(isset($this->membership_data->maccess_wpl_color)) $membership_color = 'style="color:'.$this->membership_data->maccess_wpl_color.'"';
?>
<style>
    .register-form-box {
        position: relative !important;
        left: 260px !important;
    }
    .wpl_membership_field_row {
        display: block !important;
        justify-content: center !important;
        padding-bottom: 15px;
    }
    #wpl_subscription_form input {
        font-size: 14px !important;
        width: 350% !important;
    }
    .wpl_addon_membership_container .wpl_membership_field_row label {
        float: left;
        /*width: -webkit-fill-available !important;*/
    }
    .wpl_addon_membership_container .wpl_memberships_label, .wpl_addon_membership_container .wpl_membership_addon_label {
        border-bottom: 1px solid #e6e6e6;
        padding: 10px;
        width: fit-content !important;
    }
    .wpl_addon_membership_container.wpl_membership_wrap {
        border: 1px solid #E2E2E2 !important;
        width: 152% !important;
        padding: 25px !important;
    }
    fieldset.wpl_subscription_form_account_info_container {
        width: min-content !important;
    }
    button#wpl_membership_register_button {
        padding: 6px 247px !important;
        font-size: 14px;
        text-transform: capitalize !important;
    }
    .wpl_addon_membership_container .wpl_memberships_label, .wpl_addon_membership_container .wpl_membership_addon_label {
        border-bottom: 0px solid #e6e6e6 !important;
        padding: 0px 15px !important;
        width: fit-content !important;
    }
    #wpl_subscription_form input {
        font-size: 14px !important;
        width: -webkit-fill-available !important;
    }
    form#wpl_subscription_form {
        width: 315px !important;
    }
    p#wpl_register_info {
        border-bottom: 1px solid #e2e2e2;
        width: 114.4% !important;
        right: 41px !important;
        position: relative !important;
        padding: 0 42px 24px 42px !important;
    }
    .wpl_dashboard_registeration {
        padding: 0 15px 15px 15px;
    }
    main#main {
        background: url(https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    .wpl_membership_wrap {
        border-radius: 6px;
        background: #ffffff;
    }
    .avada-page-titlebar-wrapper {
        display: none !important;
    }
    .fusion-layout-column.fusion_builder_column.fusion-builder-column-4.fusion_builder_column_1_1.\31 _1.fusion-flex-column {
        display: none !important;
    }
    #wpl_subscription_form input {
        background: white !important;
        border-radius: 5px !important;
        border-color: #e2e2e287 !important;
    }
    .wpl_membership_field_row label {
        color: #63717d;
        font-size: 11px !important;
        padding-bottom: 5px !important;
    }
</style>
<div class="register-form-box">
    <div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    
        <?php if(isset($this->membership_data->maccess_private) and $this->membership_data->maccess_private == '1'): ?>
            <p><?php echo __("Unfortunately you're not permitted to register to this membership.", 'real-estate-listing-realtyna-wpl'); ?></p>
        <?php else: ?>
        <div class="wpl_memberships_label"><?php echo __('New user registration', 'real-estate-listing-realtyna-wpl'); ?></div>
        <div class="wpl_dashboard_registeration">
            <p id="wpl_register_info"><?php echo sprintf(__("You're registering as %s in %s membership.", 'real-estate-listing-realtyna-wpl'), $this->user_type_data->name, $membership_color, $this->membership_data->membership_name); ?></p>
            <?php if($this->membership_data->maccess_price): ?>
                <ul class="wpl-subscription-steps">
                    <li id="wpl_subscription_steps_registration" class="active"><span><?php echo __('Registration', 'real-estate-listing-realtyna-wpl'); ?></span></li>
                    <li id="wpl_subscription_steps_checkout"><span><?php echo __('Checkout', 'real-estate-listing-realtyna-wpl'); ?></span></li>
                </ul>
            <?php endif; ?>
            <div id="wpl_membership_register_registration_container">
                <form id="wpl_subscription_form" class="wpl-subscription-form-container" method="POST" onsubmit="wpl_register(); return false;">
                    <fieldset class="wpl_subscription_form_account_info_container">
                        <legend><?php echo __('Account Info', 'real-estate-listing-realtyna-wpl'); ?></legend>
                        <div class="wpl_membership_field_row">
                            <label for="wpl_membership_username"><?php echo __('Username', 'real-estate-listing-realtyna-wpl'); ?> <span class="required"></span></label>
                            <input type="text" name="username" placeholder="Your username" id="wpl_membership_username" autocomplete="off" />
                        </div>
                        <div class="wpl_membership_field_row">
                            <label for="wpl_membership_email"><?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?> <span class="required">*</span></label>
                            <input type="text" name="email" placeholder="Your email address" id="wpl_membership_email" autocomplete="off" />
                        </div>
                        <!--<div class="wpl_membership_field_row">-->
                        <!--    <label for="wpl_membership_email"><?php echo __('Password', 'real-estate-listing-realtyna-wpl'); ?> <span class="required"></span></label>-->
                        <!--    <input type="password" name="password" placeholder="Your password" id="wpl_membership_email" autocomplete="off" />-->
                        <!--</div>-->
                        
                        <?php if(in_array($this->form_type, array('normal', 'full'))): ?>
                        <div class="wpl_membership_field_row">
                            <label for="wpl_membership_first_name"><?php echo __('First Name', 'real-estate-listing-realtyna-wpl'); ?> </label>
                            <input type="text" name="first_name" placeholder="Your first name" id="wpl_membership_first_name" autocomplete="off" />
                        </div>
                        <div class="wpl_membership_field_row">
                            <label for="wpl_membership_last_name"><?php echo __('Last Name', 'real-estate-listing-realtyna-wpl'); ?> </label>
                            <input type="text" name="last_name" placeholder="Your last name" id="wpl_membership_last_name" autocomplete="off" />
                        </div>
                        <?php endif; ?>
                        
                        <?php if(in_array($this->form_type, array('full'))): ?>
                        <div class="wpl_membership_field_row">
                            <label for="wpl_membership_mobile"><?php echo __('Mobile', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                            <input type="text" name="mobile" id="wpl_membership_mobile" autocomplete="off" />
                        </div>
                        <?php endif; ?>
                        
                        <div class="wpl_membership_field_row">
                            <?php
                                /**
                                 * Fires register_form action of WordPress for integrating third party plugins such as captcha plugins
                                 */
                                do_action('register_form');
                            ?>
                        </div>
                        
                        <?php if($this->settings['membership_agreement'] and $this->settings['membership_agreement_type'] == 2): #HTML ?>
                        <div class="wpl_membership_field_row">
                            <input type="checkbox" name="agreement" id="wpl_membership_agreement" autocomplete="off" />
                            <label for="wpl_membership_agreement"><?php echo __($this->settings['membership_agreement_html'], 'real-estate-listing-realtyna-wpl'); ?></label>
                        </div>
                        <?php elseif($this->settings['membership_agreement'] and $this->settings['membership_agreement_type'] == 3): #WordPress Page ?>
                        <div class="wpl_membership_field_row">
                            <input type="checkbox" name="agreement" id="wpl_membership_agreement" autocomplete="off" />
                            <label for="wpl_membership_agreement"><?php echo sprintf(__('I agree with %s', 'real-estate-listing-realtyna-wpl'), '<a href="'.wpl_sef::get_page_link($this->settings['membership_agreement_page']).'" target="_blank">'.wpl_sef::get_post_name($this->settings['membership_agreement_page']).'</a>'); ?></label>
                        </div>
                        <?php elseif($this->settings['membership_agreement'] and $this->settings['membership_agreement_type'] == 1): #Text ?>
                        <div class="wpl_membership_field_row">
                            <label for="wpl_membership_agreement_textarea"><?php echo __('Agreement', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                            <textarea id="wpl_membership_agreement_textarea" readonly="readonly"><?php echo __($this->settings['membership_agreement_text'], 'real-estate-listing-realtyna-wpl'); ?></textarea>
                        </div>
                        <div class="wpl_membership_field_row">
                            <input type="checkbox" name="agreement" id="wpl_membership_agreement" autocomplete="off" />
                            <label for="wpl_membership_agreement"><?php echo __('I agree', 'real-estate-listing-realtyna-wpl'); ?></label>
                        </div>
                        <?php endif; ?>
    					<div class="wpl-register-form-row wpl-recaptcha">
                            <label for="wpl-register-message"></label>
                            <?php echo wpl_global::include_google_recaptcha('gre_register'); ?>
                        </div>
                        <div class="wpl_membership_field_row">
                            <input type="hidden" name="wpltarget" value="<?php echo $this->target; ?>" />
                            <input type="hidden" name="membership_id" id="wpl_membership_membership_id" value="<?php echo $this->membership_id; ?>" />
                            <input type="hidden" name="user_type" id="wpl_membership_user_type" value="<?php echo $this->user_type; ?>" />
                            <input type="hidden" name="token" id="wpl_membership_register_token" value="<?php echo $this->wpl_security->token(); ?>" />
                            <input type="hidden" name="redirect_to" id="wpl_membership_register_redirect_to" value="<?php echo urlencode($this->redirect_to); ?>" />
                            <button type="submit" class="button btn btn-primary" id="wpl_membership_register_button"><?php echo __('Register', 'real-estate-listing-realtyna-wpl'); ?></button>
                        </div>
                    </fieldset>
                </form>
    
                <?php if($social_login_enabled): ?>
                    <div class="wpl_facebook_sign_up">
                        <h2><?php _e('Have a social network account?', 'real-estate-listing-realtyna-wpl'); ?></h2>
                        <a href="<?php echo $login_link; ?>" class="wpl_facebook_sign_up_btn">
                            <span class="wpl_fb_sign_up_inner"><?php _e('Login with a Social Network account', 'real-estate-listing-realtyna-wpl') ?></span>
                        </a>
                    </div>
                <?php endif; ?>
    
            </div>
            <div id="wpl_register_form_show_messages"></div>
            <div id="wpl_membership_register_checkout_container"></div>
        </div>
        <?php endif; ?>
    </div>
</div>