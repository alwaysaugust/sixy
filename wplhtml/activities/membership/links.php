<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$this->frontend_listing_manager = isset($params['frontend_listing_manager']) ? $params['frontend_listing_manager'] : 0;
$this->client = wpl_global::get_client() ? 'backend' : 'frontend';

$this->membership = new wpl_addon_membership();

$this->user_id = wpl_users::get_cur_user_id();
$this->user_data = wpl_users::get_user($this->user_id);
$this->wpl_kinds = wpl_flex::get_kinds('wpl_properties');

$this->wplmethod = wpl_request::getVar('wplmethod', NULL);

$listing_add_link = $this->frontend_listing_manager ? wpl_addon_pro::get_manager_link('wizard') : wpl_global::get_wpl_admin_menu('wpl_admin_add_listing');
$listing_manager_link = $this->frontend_listing_manager ? wpl_addon_pro::get_manager_link() : wpl_global::get_wpl_admin_menu('wpl_admin_listings');

/** importing js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.'.$this->client, true, true);
?>

<style>
    li.dashboard_link {
    display: none;
    }
</style>

<div class="wpl-membership-activity-wp links-tmpl <?php echo ($this->wplmethod == 'dashboard' ? 'wpl-dashboard-wp' : ''); ?>">
    <ul class="wpl_dashboard_links">
        <li class="dashboard_link <?php echo ($this->wplmethod == 'dashboard' ? 'active' : ''); ?>"><a href="<?php echo $this->membership->URL('dashboard'); ?>"><?php echo __('Dashboard', 'real-estate-listing-realtyna-wpl'); ?></a></li>
        <?php if(wpl_users::check_access('profilewizard')): ?><li class="profile_link <?php echo ($this->wplmethod == 'profile' ? 'active' : ''); ?>"><a href="<?php echo $this->membership->URL('profile'); ?>"><?php echo __('Profile', 'real-estate-listing-realtyna-wpl'); ?></a></li><?php endif; ?>
        <li class="changepassword_link <?php echo ($this->wplmethod == 'changepassword' ? 'active' : ''); ?>"><a href="<?php echo $this->membership->URL('changepassword'); ?>"><?php echo __('Change Password', 'real-estate-listing-realtyna-wpl'); ?></a></li>

        <?php if(wpl_global::check_addon('save_searches')): ?>
        <li class="wpl-addon-save-searches-link <?php echo ($this->wplmethod == 'searches' ? 'active' : ''); ?>">
            <a href="<?php echo $this->membership->URL('searches'); ?>"><?php echo __('Saved Searches', 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php endif; ?>

        <?php if(wpl_global::check_addon('pro') and count(wpl_activity::get_activities('dashboard_favorites', 1))): ?>
        <li class="wpl-addon-favorites-link <?php echo ($this->wplmethod == 'favorites' ? 'active' : ''); ?>">
            <a href="<?php echo $this->membership->URL('favorites'); ?>"><?php echo __('Favorites', 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php endif; ?>

        <?php if(wpl_global::check_addon('crm') and wpl_users::check_access('CRM')): _wpl_import('libraries.addon_crm'); $crm = new wpl_addon_crm(); ?>
        <li class="wpl-addon-crm-link">
            <a href="<?php echo $crm->URL('dashboard'); ?>"><?php echo __('CRM', 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php endif; ?>

        <?php if(wpl_users::is_broker()): ?>
        <li class="wpl-addon-brokerage-link <?php echo ($this->wplmethod == 'brokerage' ? 'active' : ''); ?>">
            <a href="<?php echo $this->membership->URL('brokerage'); ?>"><?php echo __('Brokerage', 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php endif; ?>
        
        <?php //if(wpl_users::check_access('propertywizard')): ?>
        <?php //foreach($this->wpl_kinds as $wpl_kind): ?>
            <?php //if(($wpl_kind['id'] == 1 and !wpl_users::check_access('complex_addon')) or ($wpl_kind['id'] == 4 and !wpl_users::check_access('neighborhoods'))) continue; ?>
            <!--<li class="properties_link" id="wpl_kind<?php echo $wpl_kind['id']; ?>">-->
            <!--    <span><?php echo __($wpl_kind['name'], 'real-estate-listing-realtyna-wpl'); ?></span>-->
            <!--    <ul>-->
            <!--        <li class="add_prp_link add"><a href="<?php echo wpl_global::add_qs_var('kind', $wpl_kind['id'], $listing_add_link); ?>"><?php echo __('Add', 'real-estate-listing-realtyna-wpl'); ?></a></li>-->
            <!--        <li class="manage_prp_link edit"><a href="<?php echo wpl_global::add_qs_var('kind', $wpl_kind['id'], $listing_manager_link); ?>"><?php echo __('Edit', 'real-estate-listing-realtyna-wpl'); ?></a></li>-->
            <!--    </ul>-->
            <!--</li>-->
        <?php //endforeach; ?>
        <?php //endif; ?>
        <li class="logout_link <?php echo ($this->wplmethod == 'logout' ? 'active' : ''); ?>"><a class="logout-btn-sec" href="<?php echo wp_logout_url(); ?>"><?php echo __('Logout', 'real-estate-listing-realtyna-wpl'); ?></a></li>
    </ul>
</div>