<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$this->show_membership_info = isset($params['show_membership_info']) ? $params['show_membership_info'] : 1;
$this->show_renew_button = isset($params['show_renew_button']) ? $params['show_renew_button'] : 1;
$this->show_upgrade_button = isset($params['show_upgrade_button']) ? $params['show_upgrade_button'] : 1;
$this->show_login_link = isset($params['show_login_link']) ? $params['show_login_link'] : 1;
$this->show_register_link = isset($params['show_register_link']) ? $params['show_register_link'] : 1;
$this->client = wpl_global::get_client() ? 'backend' : 'frontend';

$this->payments = new wpl_payments();
$this->membership = new wpl_addon_membership();

$this->user_id = wpl_users::get_cur_user_id();
if($this->client == 'backend') $this->user_id = wpl_request::getVar('id', wpl_users::get_cur_user_id());

$this->user_data = wpl_users::get_user($this->user_id);
$this->guest = ($this->user_id ? false : true);

if($this->guest) $layout = 'guest';
elseif(is_object($this->user_data->wpl_data)) $layout = 'wpluser';
else $layout = 'wpuser';

$this->layout_path = _wpl_import($this->tpl_path.'.layouts.'.$layout, true, true);

/** importing js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.'.$this->client, true, true);
?>
<div class="wpl-membership-activity-wp">
    <?php include $this->layout_path; ?>
    <div class="wpl-membership-activity-show-messages"></div>
    <div id="wpl_membership_activity_page_container" class="fanc-box-wp wpl_lightbox wpl_hidden_element"></div>
    <span id="wpl_membership_activity_fancybox_handler" class="wpl_hidden_element" href="#wpl_membership_activity_page_container"></span>
</div>