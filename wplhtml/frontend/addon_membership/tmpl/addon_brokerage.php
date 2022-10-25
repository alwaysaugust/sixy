<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <div class="wpl_dashboard_header">
        <?php echo __('Brokerage', 'real-estate-listing-realtyna-wpl'); ?>
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
								<h3><?php echo __($activity->title, 'real-estate-listing-realtyna-wpl'); ?></h3>
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
				<div id="wpl_dashboard_main_content"><?php /** Loading WPL Brokerage Page **/ echo wpl_global::load('addon_brokerage', '', array('wplmethod'=>'brokerage', 'wpl_dashboard'=>1), NULL, true); ?></div>
			</div>
		</div>
	</div>
</div>