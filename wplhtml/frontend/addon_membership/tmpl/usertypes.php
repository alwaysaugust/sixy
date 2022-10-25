<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <div class="wpl_dashboard_header"><?php echo __('User Types', 'real-estate-listing-realtyna-wpl'); ?></div>
    <ul class="wpl_usertypes_container">
        <?php
            foreach($this->user_types as $user_type)
            {
                // If next step is memberships and there is no enabled memberships then skip the user type
                if($this->next_step == 'memberships' and !count(wpl_users::get_wpl_memberships(" AND `maccess_show_in_register`='1' AND `membership_type`='".$user_type->id."'"))) continue;
                
                $link = wpl_global::add_qs_var('type', $user_type->id, $this->membership->URL($this->next_step, $this->target));
        ?>
        <li id="wpl_usertypes_type<?php echo $user_type->id; ?>" class="<?php echo strtolower($user_type->name).($this->selected == $user_type->id ? 'selected' : ''); ?>">
            <h3 class="usertype_name"><?php echo __($user_type->name, 'real-estate-listing-realtyna-wpl'); ?></h3>
            <p class="usertype_description"><?php echo __($user_type->description, 'real-estate-listing-realtyna-wpl'); ?></p>
            <a class="usertype_register_link" href="<?php echo $link; ?>"><?php echo __($this->next_step_text, 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php } ?>
    </ul>
</div>