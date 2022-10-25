<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.changepassword.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.changepassword.css', true, true);
?>
<div class="wpl_addon_membership_container wpl_view_container wpl_membership_wrap" id="wpl_addon_membership_container">
    <div class="wpl_dashboard_header">
        <?php echo __('Change password', 'real-estate-listing-realtyna-wpl'); ?>
        <?php if(wpl_users::is_administrator()): ?><a class="administrator_link" href="<?php echo wpl_global::get_wp_admin_url(); ?>"><?php echo __('Admin', 'real-estate-listing-realtyna-wpl'); ?></a><?php endif; ?>
    </div>
	<div class="wpl-row wpl-expanded">
		<?php if(count(wpl_activity::get_activities('dashboard_side', 1))): ?>
			<div class="wpl-large-3 wpl-medium-5 wpl-small-12 wpl-column">
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
		<div class="<?php if(count(wpl_activity::get_activities('dashboard_side', 1))): ?> wpl-large-9 wpl-medium-7 <?php endif; ?> wpl-small-12 wpl-column">
			<div class="wpl_dashboard_side1">
				<div id="wpl_dashboard_main_content">
					<form id="wpl_dashboard_change_password_form">
						<div id="wpl_dashboard_change_password_message"></div>
						<div class="wpl-form-row">
							<label for="wpl_change_password_new_password"><?php echo __('New Password', 'real-estate-listing-realtyna-wpl'); ?> : </label>
							<input type="password" name="new_password" id="wpl_change_password_new_password" />
						</div>
						<div class="wpl-form-row">
							<label for="wpl_change_password_new_password_repeat"><?php echo __('Repeat Password', 'real-estate-listing-realtyna-wpl'); ?> : </label>
							<input type="password" name="new_password_repeat" id="wpl_change_password_new_password_repeat" />
						</div>
						<div class="wpl-form-row">
							<input type="hidden" name="token" id="wpl_change_password_token" value="<?php echo $this->wpl_security->token(); ?>" />
							<input type="submit" id="wpl_change_password_submit" value="<?php echo __('Change', 'real-estate-listing-realtyna-wpl'); ?>" />
						</div>
						<span id="wpl_dashboard_change_password_ajax_loader"></span>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>