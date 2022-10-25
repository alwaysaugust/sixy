<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <div class="wpl_memberships_label"><?php echo __('Memberships', 'real-estate-listing-realtyna-wpl'); ?></div>
    <ul class="wpl_memberships_container">
        <?php
        foreach($this->memberships as $membership)
        {
            $link = wpl_global::add_qs_var('membership', $membership->id, $this->membership->URL('register', $this->target));
            ?>
            <li id="wpl_memberships<?php echo $membership->id; ?>" class="wpl_memberships <?php echo ($this->selected == $membership->id ? 'selected' : ''); ?>">
                <div class="wpl_memberships_wrap">
                    <h3 class="membership_headline" <?php echo (isset($membership->maccess_wpl_color) ? 'style="background:'.$membership->maccess_wpl_color.'"' : ''); ?>>
                        <span class="membership_name"><?php echo __($membership->membership_name, 'real-estate-listing-realtyna-wpl'); ?></span>
                        <span class="membership_price"><?php echo wpl_addon_membership::render_price($membership->maccess_price, $membership->maccess_price_unit); ?></span>
                    </h3>
                    <p class="membership_short_description"><?php echo __($membership->maccess_short_description, 'real-estate-listing-realtyna-wpl'); ?></p>
                    <ul class="membership_details">
                        <?php if($membership->maccess_renewable): ?>
                            <li class="wpl-membership-renew-price">
                                <span class="membership-label"><?php echo __('Renew Price', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                                <span class="membership-value"><?php echo $this->membership->render_price($membership->maccess_renewal_price, $membership->maccess_renewal_price_unit); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="wpl-membership-prp-count">
                            <span class="membership-label"><?php echo __('Property Count', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                            <span class="membership-value"><?php echo $this->membership->render_number($membership->maccess_num_prop); ?></span>
                        </li>
                        <li class="wpl-membership-prp-hot">
                            <span class="membership-label"><?php echo __('Hot Property', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                            <span class="membership-value"><?php echo $this->membership->render_number($membership->maccess_num_hot); ?></span>
                        </li>
                        <li class="wpl-membership-prp-featured">
                            <span class="membership-label"><?php echo __('Featured Property', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                            <span class="membership-value"><?php echo $this->membership->render_number($membership->maccess_num_feat); ?></span>
                        </li>
                        <li class="wpl-membership-period">
                            <span class="membership-label"><?php echo __('Period', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                            <span class="membership-value"><?php echo sprintf(__('%s Days', 'real-estate-listing-realtyna-wpl'), $this->membership->render_number($membership->maccess_period)); ?></span>
                        </li>
                        <li class="wpl-membership-listing-type">
                            <span class="membership-label"><?php echo __('Allowed Listings Types', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                            <span class="membership-value"><?php echo $this->membership->render_listings($membership->maccess_lrestrict, $membership->maccess_listings); ?></span>
                        </li>
                        <li class="wpl-membership-prp-type">
                            <span class="membership-label"><?php echo __('Allowed Property Types', 'real-estate-listing-realtyna-wpl'); ?>: </span>
                            <span class="membership-value"><?php echo $this->membership->render_property_types($membership->maccess_ptrestrict, $membership->maccess_property_types); ?></span>
                        </li>
                    </ul>
                    <p class="membership_long_description"><?php echo __($membership->maccess_long_description, 'real-estate-listing-realtyna-wpl'); ?></p>
                </div>
                <a class="membership_register_link" <?php echo (isset($membership->maccess_wpl_color) ? 'style="background:'.$membership->maccess_wpl_color.'"' : ''); ?>  href="<?php echo $link; ?>"><?php echo __('Register', 'real-estate-listing-realtyna-wpl'); ?></a>
            </li>
        <?php } ?>
    </ul>
</div>