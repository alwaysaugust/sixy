<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.signup.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.signup.css', true, true);
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <form id="wpl_signup_form" class="wpl-signup-form-container" method="POST" onsubmit="wpl_signup(); return false;">
        <div id="wpl_membership_signup_container">
            <?php if($this->signup_method == 'all' and !wpl_users::check_user_login()): ?>
            <div id="wpl_membership_signup_user_container">
                <fieldset class="wpl_signup_form_account_info_container">
                    <legend><?php echo __('Account Info', 'real-estate-listing-realtyna-wpl'); ?></legend>
                    <div class="wpl_membership_field_row">
                        <label for="wpl_membership_username"><?php echo __('Username', 'real-estate-listing-realtyna-wpl'); ?>: <span class="required">*</span></label>
                        <input type="text" name="user_name" id="wpl_membership_username" autocomplete="off" />
                    </div>
                    <div class="wpl_membership_field_row">
                        <label for="wpl_membership_email"><?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?>: <span class="required">*</span></label>
                        <input type="text" name="user_email" id="wpl_membership_email" autocomplete="off" />
                    </div>
                </fieldset>
            </div>
            <div id="wpl_membership_signup_site_toggle_container">
                <input type="hidden" name="signup_for" value="user" />
                <input type="checkbox" name="signup_for" value="blog" id="wpl_membership_signup_for" checked="checked" onchange="wpl_site_toggle();" />
                <label for="wpl_membership_signup_for"><?php echo __('Give me a site!', 'real-estate-listing-realtyna-wpl'); ?></label>
            </div>
            <?php endif; ?>

            <?php if(in_array($this->signup_method, array('all', 'blog'))): ?>
            <div id="wpl_membership_signup_site_container">
                <fieldset class="wpl_signup_form_site_info_container">
                    <legend><?php echo __('Site Info', 'real-estate-listing-realtyna-wpl'); ?></legend>
                    <div class="wpl_membership_field_row">
                        <label for="wpl_membership_blogname"><?php echo __('Site Name', 'real-estate-listing-realtyna-wpl'); ?>: <span class="required">*</span></label>
                        <?php if(!is_subdomain_install()): ?>
                        <span class="prefix_address"><?php echo $this->current_site->domain . $this->current_site->path; ?></span>
                        <?php endif; ?>
                        <input type="text" name="blogname" id="wpl_membership_blogname" autocomplete="off" />
                        <?php if(is_subdomain_install()): ?>
                        <span class="suffix_address"><?php echo $site_domain = preg_replace('|^www\.|', '', $current_site->domain); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="wpl_membership_field_row">
                        <label for="wpl_membership_blog_title"><?php echo __('Site Title', 'real-estate-listing-realtyna-wpl'); ?>: <span class="required">*</span></label>
                        <input type="text" name="blog_title" id="wpl_membership_blog_title" autocomplete="off" />
                    </div>
                    <div class="wpl_membership_field_row">
                        <label for="wpl_membership_public_on"><?php echo __('Privacy', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                        <p><?php echo __('Allow search engines to index this site.', 'real-estate-listing-realtyna-wpl'); ?></p>
                        <label for="wpl_membership_public_on">
                            <input type="radio" name="blog_public" id="wpl_membership_public_on" value="1" autocomplete="off" checked="checked" />
                            <strong><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></strong>
                        </label>
                        <label for="wpl_membership_public_off">
                            <input type="radio" name="blog_public" id="wpl_membership_public_off" value="0" autocomplete="off" />
                            <strong><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></strong>
                        </label>
                    </div>
                    <div class="wpl_membership_field_row">
                        <?php
                        /**
                         * Fires after the site sign-up form.
                         *
                         * @since 3.0.0
                         *
                         * @param array $errors An array possibly containing 'blogname' or 'blog_title' errors.
                         */
                        do_action('signup_blogform', new stdClass());
                        ?>
                    </div>
                </fieldset>
            </div>
            <?php endif; ?>

            <div class="wpl_membership_field_row">
                <input type="hidden" name="token" id="wpl_membership_signup_token" value="<?php echo $this->wpl_security->token(); ?>" />
                <button type="submit" class="button btn btn-primary" id="wpl_membership_signup_button"><?php echo __('Create Site', 'real-estate-listing-realtyna-wpl'); ?></button>
            </div>
        </div>
        <div id="wpl_signup_form_show_messages"></div>
    </form>
</div>