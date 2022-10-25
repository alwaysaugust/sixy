<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->membership_id = $this->user_data->data->wpl_data->membership_id;
$this->membership_data = wpl_users::get_membership($this->membership_id);
?>
<div id="wpl-membership-activity-wpluser<?php echo $this->activity_id; ?>" class="wpl-membership-activity-wpluser-wp wpl-util-clearfix wpl-js-tab-system">
    <div class="wpl-gen-tab-wp wpl-membership-wpluser-head">
        <div class="wpl_activity_title"><?php echo __('Membership', 'real-estate-listing-realtyna-wpl'); ?></div>
        <ul class="wpl-membership-tab">
            <li class="wpl-membership-tab-title">
                <a href="#wpl-gen-tab-content1">
                    <?php echo __('Stats', 'real-estate-listing-realtyna-wpl'); ?>
                </a>
            </li>
            <?php if($this->show_renew_button and $this->user_data->data->wpl_data->maccess_renewable): ?>
            <li class="wpl-membership-tab-title">
                <a href="#wpl-gen-tab-content2">
                    <?php echo __('Renew', 'real-estate-listing-realtyna-wpl'); ?>
                </a>
            </li>
            <?php endif; ?>
            <?php if($this->show_upgrade_button and $this->user_data->data->wpl_data->maccess_upgradable): ?>
            <li class="wpl-membership-tab-title">
                <a href="#wpl-gen-tab-content3">
                    <?php echo __('Change Membership', 'real-estate-listing-realtyna-wpl'); ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="wpl-gen-tab-contents-wp wpl-membership-wpluser-tab-container">
        <div id="wpl-gen-tab-content1" class="wpl-gen-tab-content tab-content active">
            <ul class="wpl-membership-wpluser">
                <li class="wpl-membership-name">
                    <span class="membership-label"><?php echo __('Membership', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo sprintf(__('%s', 'real-estate-listing-realtyna-wpl'), isset($this->membership_data->membership_name) ? $this->membership_data->membership_name : __('None', 'real-estate-listing-realtyna-wpl')); ?></span>
                </li>
                <?php if($this->show_membership_info): ?>
                <li>
                    <span class="membership-label"><?php echo __('Property Count', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo $this->membership->render_number($this->user_data->data->wpl_data->maccess_num_prop); ?></span>
                </li>
                <li>
                    <span class="membership-label"><?php echo __('Hot Property', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo $this->membership->render_number($this->user_data->data->wpl_data->maccess_num_hot); ?></span>
                </li>
                <li>
                    <span class="membership-label"><?php echo __('Featured Property', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo $this->membership->render_number($this->user_data->data->wpl_data->maccess_num_feat); ?></span>
                </li>
                <li>
                    <span class="membership-label"><?php echo __('Period', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo sprintf(__('%s Days', 'real-estate-listing-realtyna-wpl'), $this->membership->render_number($this->user_data->data->wpl_data->maccess_period)); ?></span>
                </li>
                <li>
                    <span class="membership-label"><?php echo __('Allowed Listings Types', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo $this->membership->render_listings($this->user_data->data->wpl_data->maccess_lrestrict, $this->user_data->data->wpl_data->maccess_listings); ?></span>
                </li>
                <li>
                    <span class="membership-label"><?php echo __('Allowed Property Types', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo $this->membership->render_property_types($this->user_data->data->wpl_data->maccess_ptrestrict, $this->user_data->data->wpl_data->maccess_property_types); ?></span>
                </li>
                <?php endif; ?>
                <li class="wpl-expiry-date">
                    <span class="membership-label"><?php echo __('Expires at', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                    <span class="membership-value"><?php echo sprintf(__(' %s', 'real-estate-listing-realtyna-wpl'), $this->membership->render_date($this->user_data->data->wpl_data->expiry_date)); ?></span>
                </li>
            </ul>
            <?php if($this->membership->is_expired($this->user_id)): ?>
            <div class="wpl_red_msg"><?php echo __('Membership expired, your listings will no longer be displayed.', 'real-estate-listing-realtyna-wpl'); ?></div>
            <?php endif; ?>
            <?php if($profile_id = $this->membership->get_recurring_profile_id($this->user_id)): ?>
            <div class="wpl_gold_msg"><?php echo sprintf(__('A recurring subscription is active for you under %s ID. You can %s it whenever you like.', 'real-estate-listing-realtyna-wpl'), '<strong>'.$profile_id.'</strong>', '<strong><a href="'.$this->membership->get_cancel_subscription_url($profile_id).'" target="_blank">'.__('cancel', 'real-estate-listing-realtyna-wpl').'</a></strong>'); ?></div>
            <?php endif; ?>
        </div>
        <div id="wpl-gen-tab-content2" class="wpl-gen-tab-content tab-content">
            <?php if($this->show_renew_button and $this->user_data->data->wpl_data->maccess_renewable): ?>
            <div class="wpl-renew-membership">
                <h4><?php echo __('Renew your membership', 'real-estate-listing-realtyna-wpl'); ?></h4>
                <button id="wpl-membership-renew-btn" data-realtyna-href="#wpl_membership_activity_page_container_renew" class="wpl-button button-1" onclick="wpl_renew_membership_transaction(<?php echo $this->user_id; ?>); return false;"><?php echo __('Renew', 'real-estate-listing-realtyna-wpl'); ?></button>
                <span id="wpl_renew_membership_ajax_loader"></span>
            </div>
            <?php endif; ?>
            <div id="wpl_membership_activity_page_container_renew" class="wpl_hidden_element"></div>
        </div>
        <div id="wpl-gen-tab-content3" class="wpl-gen-tab-content tab-content">
            <?php if($this->show_upgrade_button and $this->user_data->data->wpl_data->maccess_upgradable): ?>
            <div class="wpl-upgrade-membership">
                <h4><?php echo __('Change your membership', 'real-estate-listing-realtyna-wpl'); ?></h4>
                <button id="wpl-membership-change-membership-btn" data-realtyna-href="#wpl_membership_activity_page_container_upgrade" class="wpl-button button-1" onclick="wpl_upgrade_membership_list(<?php echo $this->user_id; ?>); return false;"><?php echo __('Change Membership', 'real-estate-listing-realtyna-wpl'); ?></button>
                <span id="wpl_upgrade_membership_ajax_loader"></span>
            </div>
            <?php endif; ?>
            <div id="wpl_membership_activity_page_container_upgrade" class="fanc-box-wp wpl_lightbox wpl_hidden_element"></div>
        </div>
    </div>
</div>