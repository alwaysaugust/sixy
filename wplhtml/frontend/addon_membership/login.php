<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.login.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.login.css', true, true);

$fb_app_id = wpl_settings::get('fb_login_appid');
$fb_app_secret = wpl_settings::get('fb_login_secret');
$fb_login_enabled = ($fb_app_id and $fb_app_secret);
$fb_login_url = '';

if($fb_login_enabled)
{
    _wpl_import('libraries.vendors.facebook.autoload');

    $fb = new Facebook\Facebook(array(
      'app_id' => $fb_app_id,
      'app_secret' => $fb_app_secret,
      'default_graph_version' => 'v2.2',
    ));

    $fb_callback = wpl_addon_membership::URL('fblogin');
    $fb_helper = $fb->getRedirectLoginHelper();
    $fb_login_url = htmlspecialchars($fb_helper->getLoginUrl($fb_callback, ['email']));  
}

$twitter_api_key = wpl_settings::get('twitter_login_key');
$twitter_api_secret = wpl_settings::get('twitter_login_secret');
$twitter_login_enabled = ($twitter_api_key and $twitter_api_secret);
$twitter_login_url = '';

if($twitter_login_enabled)
{
    _wpl_import('libraries.vendors.twitter.autoload');

    $twitter_callback = wpl_addon_membership::URL('twitterlogin');
    $connection = new Abraham\TwitterOAuth\TwitterOAuth($twitter_api_key, $twitter_api_secret);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $twitter_callback));
    $twitter_login_url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

    session_start();
    wpl_session::set('oauth_token', $request_token['oauth_token']);
    wpl_session::set('oauth_token_secret', $request_token['oauth_token_secret']);
}

$instagram_app_key = wpl_settings::get('instagram_login_key');
$instagram_app_secret = wpl_settings::get('instagram_login_secret');
$instagram_login_enabled = ($instagram_app_key and $instagram_app_secret);
$instagram_login_url = '';

if($instagram_login_enabled)
{
    $instagram_callback = wpl_addon_membership::URL('instagramlogin');

    _wpl_import('libraries.vendors.instagram.vendor.autoload');

    $instagram = new Andreyco\Instagram\Client(array(
      'apiKey'      => $instagram_app_key,
      'apiSecret'   => $instagram_app_secret,
      'apiCallback' => $instagram_callback,
    ));

    $instagram_login_url = $instagram->getLoginUrl();
}

$linkedin_login_enabled = wpl_settings::get('linkedin_login_api');
?>
<style>
    form#wpl_login_form {
        margin: 0 !important;
        padding: 25px 40px !important;
        width: 133% !important;
        height: auto !important;
        border-radius: 5px !important;
        background: #ffffff;
    }
    .fusion-body .fusion-flex-container.fusion-builder-row-3 {
        padding-top: 50px !important;
        padding-bottom: 90px !important;
        padding-right: 0px !important;
    }
    button#wpl_login_submit {
        width: 42em !important;
        padding: 5px !important;
        text-transform: capitalize !important;
    }
    .wpl-login-form-row.custom-style {
        padding: 20px 0 !important;
    }
    .wpl_membership_addon_label {
        width: 116% !important;
        right: 41px !important;
        position: relative !important;
        padding: 0px 0px 18px 39px!important;
        margin: 0 0 27px 0px !important;
    }
    .fusion-flex-container .fusion-row .fusion-flex-column .fusion-column-wrapper:not(.fusion-flex-column-wrapper-legacy).fusion-content-layout-row {
        display: flex !important;
        justify-content: center !important;
    }
    .wpl-login-form-row.wpl-login-form-remember-wp {
        display: flex !important;
        justify-content: start !important;
        align-items: center !important;
    }
    /*.wpl-login-form-row.fg-password {*/
    /*    width: fit-content !important;*/
    /*    position: relative !important;*/
    /*    bottom: 60px !important;*/
    /*    left: 410px !important;*/
    /*    font-size: 14px !important;*/
    /*    text-decoration: underline !important;*/
    /*}*/
    label.remember-text {
        font-size: 14px !important;
        color: #15161a !important;
    }
    .wpl-login-form-row.wpl-login-form-remember-wp {
        width: fit-content !important;
        margin: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    #wpl_login_form_container input[type="checkbox"] {
        margin-right: 10px !important;
        order: 0;
        position: relative !important;
        bottom: 13px !important;
    }
    .wpl-login-form-row.goto-register-btn {
        font-size: 14px;
        display: flex;
        justify-content: center;
        color: #15161a;
        margin-top: 33px;
    }
    #wpl_login_form_container .wpl-login-form-row {
        margin-bottom: 5px;
        padding: 0px;
    }
    #wpl_login_form_container label {
        float: left;
        width: fit-content;
    }
    .wpl-login-form-row.fg-password {
        width: fit-content !important;
        position: relative !important;
        bottom: 53px !important;
        left: 400px !important;
        font-size: 14px !important;
        text-decoration: underline !important;
    }
    main#main {
        background: url(https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    .avada-page-titlebar-wrapper {
        display: none !important;
    }
    .fusion-layout-column.fusion_builder_column.fusion-builder-column-4.fusion_builder_column_1_1.\31 _1.fusion-flex-column {
        display: none !important;
    }
    input#wpl_login_username {
        background: white !important;
        border-radius: 5px !important;
        border-color: #e6e6e694 !important;
    }
    input#wpl_login_password {
        background: white !important;
        border-radius: 5px !important;
        border-color: #e6e6e694 !important;
    }
    .wpl_addon_membership_container #wpl_login_form_show_messages {
        margin: 10px !important;
        bottom: 21px !important;
        position: relative !important;
    }
    .logged-in form#wpl_logout_form {
    text-align: center;
    background-color: #ffffff70;
    width: 500px;
    padding: 50px 30px;
    border-radius: 12px;
    }
    .logged-in form#wpl_logout_form .wpl_membership_addon_label {
        font-size: 45px !important;
        font-weight: 700 !important;
        text-shadow: 4px 4px 4px #00000052;
        padding-left: 0 !important;
    }
    .logged-in form#wpl_logout_form button#wpl_logout_submit {
        width: 160px;
        text-align: center;
        padding: 8px 10px;
        font-size: 18px;
        border: 1px solid #191919;
    }
    .logged-in form#wpl_logout_form button#wpl_logout_submit:hover {
        color: #ffffff;
        background-color: #333 !important;
    }
</style>
<div class="wpl_addon_membership_container wpl_view_container <?php echo ($this->interim_login ? 'wpl-interim-login' : ''); ?>" id="wpl_addon_membership_container">
    <?php if(!wpl_users::check_user_login()): ?>
        <div id="wpl_login_form_container">
            <form id="wpl_login_form" class="wpl-login-form custom" method="POST" onsubmit="wpl_login(); return false;">
                <div class="wpl_membership_addon_label"><?php echo __('Existing user login', 'real-estate-listing-realtyna-wpl'); ?></div>
                <div class="wpl-login-form-row"><?php echo wpl_flash::get(); ?></div>
                <div class="wpl-login-form-row-wrap">
                    <div class="wpl-social-login-container">
                        <?php if($fb_login_enabled): ?>
                            <div class="wpl-login-form-row">
                                <div class="wpl_facebook_sign_in">
                                    <a href="<?php echo $fb_login_url; ?>" class="wpl_facebook_sign_in_btn">
                                        <span class="wpl_fb_sign_in_inner"><?php _e('Login with Facebook', 'real-estate-listing-realtyna-wpl') ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($twitter_login_enabled): ?>
                            <div class="wpl-login-form-row">
                                <div class="wpl_twitter_sign_in">
                                    <a href="<?php echo $twitter_login_url; ?>" class="wpl_twitter_sign_in_btn">
                                        <span class="wpl_twitter_sign_in_inner"><?php _e('Login with Twitter', 'real-estate-listing-realtyna-wpl') ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($linkedin_login_enabled): ?>
                            <div class="wpl-login-form-row">
                                <a class="wpl_linkedin_button" href="javascript:void(0)"><?php echo __('Login with LinkedIn', 'real-estate-listing-realtyna-wpl'); ?></a>
                            </div>
                        <?php endif; ?>

                        <?php if($instagram_login_enabled): ?>
                            <div class="wpl-login-form-row">
                                <div class="wpl_instagram_sign_in">
                                    <a href="<?php echo $instagram_login_url; ?>" class="wpl_instagram_sign_in_btn">
                                        <span class="wpl_instagram_sign_in_inner"><?php _e('Login with Instagram', 'real-estate-listing-realtyna-wpl') ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="wpl-default-login">
                        <div class="wpl-login-form-row custom-style">
                            <label for="wpl_login_username"><?php echo __('Username', 'real-estate-listing-realtyna-wpl'); ?> </label>
                            <input type="text" name="username" placeholder="Your username" id="wpl_login_username" size="20" />
                        </div>
                        <div class="wpl-login-form-row custom-style">
                            <label for="wpl_login_password"><?php echo __('Password', 'real-estate-listing-realtyna-wpl'); ?> </label>
                            <input type="password" name="password" placeholder="Your password" id="wpl_login_password" size="20" />
                        </div>
                        <div class="wpl-login-form-row">
                        <?php
                            /**
                             * Fires login_form action of WordPress for integrating third party plugins such as captcha plugins
                             */
                            do_action('login_form');
                        ?>
                        </div>
                        <div class="wpl-login-form-row wpl-login-form-remember-wp">
                            <input type="checkbox" name="remember" id="wpl_login_remember" value="1" />
                            <label for="wpl_login_remember" class="remember-text"><?php echo __('Remember me', 'real-estate-listing-realtyna-wpl'); ?></label>
                        </div>
                        <div class="wpl-login-form-row fg-password">
                            <a href="<?php echo $this->membership->URL('lostpassword'); ?>"><?php echo __('Forgot password', 'real-estate-listing-realtyna-wpl'); ?></a>
                        </div>
    					<div class="wpl-login-form-row wpl-recaptcha">
                            <label for="wpl-login-message"></label>
                            <?php echo wpl_global::include_google_recaptcha('gre_login'); ?>
                        </div>
                        <div class="wpl-login-form-row wpl-login-form-btns-wp">
                            <input type="hidden" name="wpltarget" value="<?php echo $this->target; ?>" />
                            <input type="hidden" name="token" id="wpl_membership_login_token" value="<?php echo $this->wpl_security->token(); ?>" />
                            <input type="hidden" name="redirect_to" id="wpl_membership_login_redirect_to" value="<?php echo urlencode($this->redirect_to); ?>" />
                            <button type="submit" class="button btn btn-primary" id="wpl_login_submit"><?php echo __('Login', 'real-estate-listing-realtyna-wpl'); ?></button>
                        </div>
                        <div class="wpl-login-form-row goto-register-btn">
                            <?php if(wpl_global::get_wp_option('users_can_register')): ?>
                            <a href="<?php echo wpl_users::wp_registration_url(); ?>">Don’t have an account? <span style="text-decoration: underline;"><?php echo __('Register', 'real-estate-listing-realtyna-wpl'); ?></span></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div id="wpl_login_form_show_messages"></div>
            </form>
        </div>
    <?php else: ?>
        <div id="wpl_logout_form_container">
            <div id="wpl_logout_form_show_messages"></div>
            <form id="wpl_logout_form" class="wpl-logout-form" method="POST" onsubmit="wpl_logout(); return false;">
                <div class="wpl_membership_addon_label"><?php echo __('Logout', 'real-estate-listing-realtyna-wpl'); ?></div>
                <div class="wpl-logout-form-row">
                    <input type="hidden" name="wpltarget" value="<?php echo $this->target; ?>" />
                    <input type="hidden" name="token" id="wpl_membership_logout_token" value="<?php echo $this->wpl_security->token(); ?>" />
                    <input type="hidden" name="redirect_to" id="wpl_membership_logout_redirect_to" value="<?php echo urlencode($this->redirect_to); ?>" />
                    <button type="submit" class="button btn btn-primary" id="wpl_logout_submit"><?php echo __('Logout', 'real-estate-listing-realtyna-wpl'); ?></button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php //var_dump($_SESSION); ?>