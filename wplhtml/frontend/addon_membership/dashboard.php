<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<style>
    span.save-search-head {
    margin: 28px;
    height: 34px;
    width: 216px;
    color: #191919;
    font-family: Inter;
    font-size: 28px;
    font-weight: bold;
    letter-spacing: -0.28px;
    line-height: 72px;
}
#wpl_dashboard_bottom_container>div:first-child .wpl-button {
    top: 25px;
    right: 0px;
}
#wpl_dashboard_bottom_container>div:first-child .wpl-button:hover {
    background: #191919;
    color: #f8f8f8 !important;
}
</style>
<div class="wpl_addon_membership_container wpl_view_container wpl_dashboard" id="wpl_addon_membership_container">
    <div class="wpl_dashboard_header">
        <?php echo __('Dashboard', 'real-estate-listing-realtyna-wpl'); ?>
        <?php if(wpl_users::is_administrator()): ?><a class="administrator_link" href="<?php echo wpl_global::get_wp_admin_url(); ?>"><?php echo __('Admin', 'real-estate-listing-realtyna-wpl'); ?></a><?php endif; ?>
    </div>
    <div class="wpl-row wpl-expanded">
        <?php if(count(wpl_activity::get_activities('dashboard_side', 1))): ?>
            <div class="wpl-large-3 wpl-medium-12 wpl-small-12 wpl-column">
                <div class="wpl_dashboard_side2">
                    <div id="wpl_dashboard_side2_container">
                        <?php
                        $activities = wpl_activity::get_activities('dashboard_side', 1);
                        foreach($activities as $activity)
                        {
                            $content = wpl_activity::render_activity($activity, array('user_data'=>$this->user_data));
                            if(trim($content) == '') continue;
                            ?>
                            <div>
                                <?php if($activity->show_title and trim($activity->title) != ''): ?>
                                    <h3 class="wpl_activity_title"><?php echo __($activity->title, 'real-estate-listing-realtyna-wpl'); ?></h3>
                                <?php endif; ?>
                                <div><?php echo $content; ?></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="<?php if(count(wpl_activity::get_activities('dashboard_side', 1))): ?> wpl-large-9 wpl-medium-12 <?php endif; ?> wpl-small-12 wpl-column">
            <div class="wpl_dashboard_side1">
                <span class="save-search-head">Dashboard</span>
                <hr style="visibility: hidden;">
                <?php if(count(wpl_activity::get_activities('dashboard_top', 1))): ?>
                    <div id="wpl_dashboard_top_container">
                        <?php
                        $activities = wpl_activity::get_activities('dashboard_top', 1);
                        foreach($activities as $activity)
                        {
                            $content = wpl_activity::render_activity($activity, array('user_data'=>$this->user_data));
                            if(trim($content) == '') continue;
                            ?>
                            <div class="wpl_membership_activity_container">
                                <?php if($activity->show_title and trim($activity->title) != ''): ?>
                                    <h3 class="wpl_activity_title"><?php echo __($activity->title, 'real-estate-listing-realtyna-wpl'); ?></h3>
                                <?php endif; ?>
                                <div><?php echo $content; ?></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <div id="wpl_dashboard_main_content"></div>
                <?php if(count(wpl_activity::get_activities('dashboard_bottom', 1))): ?>
                    <div id="wpl_dashboard_bottom_container">
                        <?php
                        $activities = wpl_activity::get_activities('dashboard_bottom', 1);
                        foreach($activities as $activity)
                        {
                            $content = wpl_activity::render_activity($activity, array('user_data'=>$this->user_data));
                            if(trim($content) == '') continue;
                            ?>
                            <div>
                                <?php if($activity->show_title and trim($activity->title) != ''): ?>
                                    <h3 class="wpl_activity_title"><?php echo __($activity->title, 'real-estate-listing-realtyna-wpl'); ?></h3>
                                <?php endif; ?>
                                <div><?php echo $content; ?></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>