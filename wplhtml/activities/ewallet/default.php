<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$this->minimum_charge = isset($params['minimum_charge']) ? $params['minimum_charge'] : 1;
$this->default_amount = isset($params['default_amount']) ? $params['default_amount'] : 10;
$this->unit_id = isset($params['unit_id']) ? $params['unit_id'] : 260;
$this->show_charge_form = isset($params['show_charge_form']) ? $params['show_charge_form'] : 1;
$this->minimum_charge_rendered = wpl_render::render_price($this->minimum_charge, $this->unit_id);
$this->client = wpl_global::get_client() ? 'backend' : 'frontend';

$this->user_id = wpl_users::get_cur_user_id();
if($this->client == 'backend') $this->user_id = wpl_request::getVar('id', wpl_users::get_cur_user_id());

$this->payments = new wpl_payments();
$this->ewallet = $this->payments->get('ewallet');
$this->balance = $this->ewallet->balance($this->user_id, $this->unit_id);
$this->rendered = wpl_render::render_price($this->balance, $this->unit_id);

/** importing js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.'.$this->client, true, true);
?>
<style>
#main {
    background: #f8f8f8 !important;
}
.wpl-membership-activity-wp {
    padding: 25px 10px 0px 0px;
    height: 419px;
    border: 1px solid #EBEBEB;
    border-radius: 5px;
    background-color: #FFFFFF;
}
.wpl_addon_membership_container .wpl_dashboard_side1 {
    padding: 15px 0px;
    /*padding: 15px;*/
    /*width: 80%;*/
    background: white;
    border-radius: 5px;
    border: 1px solid #ebebeb;
}
.wpl-ewallet-charge input[type="submit"] {
    background: #ffffff;
    border: 1px solid;
    color: #191919;
    float: right;
    border-radius: 4px !important;
    font-size: 14px;
    padding: 10px 18px !important;
    text-transform: capitalize;
}
a.logout-btn {
    border: 1px solid;
    padding: 10px 24px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 14px;
}
.wpl_addon_membership_container .wpl_dashboard_links li {
    line-height: 40px;
    padding: 0 10px;
    font-weight: 700;
}
.post-content p {
    margin-top: 0;
    margin-bottom: 20px;
    font-weight: 600;
}
.block-property .wpl-row.wpl-expanded, .block-property .wpl_prp_show_bottom {
    background: #eef0f000;
}
.wpl_addon_membership_container .wpl_dashboard_side2 {
    background: #fff;
    border-bottom: 0px solid #c9c9c9;
    border-right: 0px solid #c9c9c9;
}
.wpl_dashboard {
    background: #f2f2f200;
    border: 0px solid #c9c9c9;
    margin-bottom: 15px;
}
.fusion-body .fusion-flex-container.fusion-builder-row-3 {
    padding-top: 20px !important;
    margin-top: 0px !important;
    padding-right: 30px !important;
    padding-bottom: 0px;
    margin-bottom: 20px !important;
    padding-left: 0px;
}
.wpl_dashboard_header {
    display: none;
}
li::before {
    visibility: hidden;
}
.wpl_addon_membership_container .wpl_dashboard_links li:hover a, .wpl_addon_membership_container .wpl_dashboard_links li.active a {
    color: #191919;
}
.wpl_addon_membership_container .wpl_dashboard_links li:hover, .wpl_addon_membership_container .wpl_dashboard_links li.active {
    border-right: 3px solid #716c6c;
    background: transparent;
}
.wpl_addon_membership_container .wpl_dashboard_links li:hover, .wpl_addon_membership_container .wpl_dashboard_links li.active:nth-last-child{
    border-right: 0px solid #716c6c;
    background: transparent;
}
li#wpl_kind0::before {
    display: none;
}
li#wpl_kind0::after {
    display: none;
}
li#wpl_kind0 {
    left: 21px;
    position: relative;
    width: 95%;
    font-size: 14px;
}
ul.wpl_dashboard_links li {
    height: 47px;
    width: 104%;
}
ul.wpl_dashboard_links li a {
    font-size: 14px;
}
a.logout-btn-sec{
    height: 41px;
    width: 91px;
    border: 1px solid #938c8c;
    border-radius: 5px;
    background-color: #FFFFFF;
    position: relative;
    left: 20px;
    text-transform: capitalize;
}
a.logout-btn-sec:hover {
    background: #191919;
    color: #f8f8f8 !important;
}
li.logout_link {
    /*position: relative;*/
    /*left: 21px;*/
    /*top: 44px;*/
    margin-top: 55px;
}
.avada-page-titlebar-wrapper {
    display: none;
}
.fusion-fullwidth.fullwidth-box.fusion-builder-row-2.fusion-flex-container.block-print.nonhundred-percent-fullwidth.non-hundred-percent-height-scrolling {
    display: none;
}
li.add_prp_link.add, .edit {
    padding: 0;
}
.wpl_addon_membership_container .wpl_dashboard_links li ul li {
    line-height: 15px;
    padding: 0 10px;
    font-weight: 700;
    border-radius: 5px;
    width: 150px;
    background: #e2e2e238;
}
</style>
<div class="wpl-ewallet-wp">
    <div class="wpl-ewallet-balance">
        <?php echo sprintf(__('Your current balance is <span>%s</span>', 'real-estate-listing-realtyna-wpl'), $this->rendered); ?>
    </div>
    <?php if($this->show_charge_form): ?>
    <div class="wpl-ewallet-charge">
        <form id="wpl_ewallet_charge_form">
            <input type="number" id="wpl_ewallet_charge_amount" name="wpl_ewallet_charge_amount" value="<?php echo $this->default_amount; ?>" autocomplete="off" />
            <input type="hidden" id="wpl_ewallet_charge_unit_id" name="wpl_ewallet_charge_unit_id" value="<?php echo $this->unit_id; ?>" autocomplete="off" />
            <input type="submit" class="wpl-button button-1" value="<?php echo __('Charge', 'real-estate-listing-realtyna-wpl'); ?>" />
            <span id="wpl_ewallet_charge_ajax_loader"></span>
        </form>
        <div class="wpl-ewallet-charge-show-messages"></div>
        <div id="wpl_ewallet_checkout_page_container" class="wpl_hidden_element"></div>
        <span data-realtyna-lightbox data-realtyna-lightbox-opts="reloadPage:true" id="wpl_ewallet_lightbox_handler" class="wpl_hidden_element" data-realtyna-href="#wpl_ewallet_checkout_page_container"></span>
    </div>
    <?php endif; ?>
</div>