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
<div class="wpl_addon_membership_container wpl_view_container <?php echo ($this->interim_login ? 'wpl-interim-login' : ''); ?>" id="wpl_addon_membership_container">
    <?php if(!wpl_users::check_user_login()): ?>
        <div id="wpl_login_form_container">
            <form id="wpl_login_form" class="wpl-login-form" method="POST" onsubmit="wpl_login(); return false;">
                <div class="wpl_membership_addon_label"><?php echo __('Login', 'real-estate-listing-realtyna-wpl'); ?></div>
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
                        <div class="wpl-login-form-row">
                            <label for="wpl_login_username"><?php echo __('Username', 'real-estate-listing-realtyna-wpl'); ?> : </label>
                            <input type="text" name="username" id="wpl_login_username" size="20" />
                        </div>
                        <div class="wpl-login-form-row">
                            <label for="wpl_login_password"><?php echo __('Password', 'real-estate-listing-realtyna-wpl'); ?> : </label>
                            <input type="password" name="password" id="wpl_login_password" size="20" />
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
                            <label for="wpl_login_remember"><?php echo __('Remember me', 'real-estate-listing-realtyna-wpl'); ?></label>
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
                        <div class="wpl-login-form-row">
                            <a href="<?php echo $this->membership->URL('lostpassword'); ?>"><?php echo __('Lost Your Password?', 'real-estate-listing-realtyna-wpl'); ?></a>
                        </div>
                        <div class="wpl-login-form-row">
                            <?php if(wpl_global::get_wp_option('users_can_register')): ?>
                            <a href="<?php echo wpl_users::wp_registration_url(); ?>"><?php echo __('Register', 'real-estate-listing-realtyna-wpl'); ?></a>
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