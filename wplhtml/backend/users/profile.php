<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path . '.scripts.js');
$this->_wpl_import($this->tpl_path . '.scripts.css');

$my_profile_top_activities = count(wpl_activity::get_activities('my_profile_top', 1));
$my_profile_bottom_activities = count(wpl_activity::get_activities('my_profile_bottom', 1));
$this->finds = array();
?>
<style>
#main {
    background: #f8f8f8 !important;
}
.wpl-membership-activity-wp {
    padding: 25px 10px 0px 0px;
    height: 440px;
    border: 1px solid #EBEBEB;
    border-radius: 5px;
    background-color: #FFFFFF;
}
.wpl_addon_membership_container .wpl_dashboard_side1 {
    padding: 15px;
    width: 80%;
    background: white;
    border-radius: 5px;
    border: 1px solid #ebebeb;
}
.wpl-ewallet-charge input[type="submit"] {
    background: #ffffff;
    border: 1px solid;
    color: #000;
    float: right;
}
.wpl-ewallet-charge input[type="submit"] {
    background: #ffffff;
    border: 1px solid;
    color: #000;
    float: right;
    border-radius: 4px !important;
    font-size: 14px;
    padding: 10px 18px !important;
}
a.logout-btn {
    border: 1px solid;
    padding: 10px 24px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 14px;
}
.wpl_addon_membership_container .wpl_dashboard_links li {
    line-height: 42px;
    padding: 0px 10px;
    font-weight: 700;
    margin: 10px 0px;
    height: 40px;
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
    /*padding-top: 20px !important;*/
    /*margin-top: 0px !important;*/
    /*padding-right: 30px !important;*/
    /*padding-bottom: 0px;*/
    /*margin-bottom: 20px !important;*/
    /*padding-left: 0px;*/
}
.wpl_dashboard_header {
    display: none;
}
li::before {
    visibility: hidden;
}
.wpl_addon_membership_container .wpl_dashboard_links li:hover a, .wpl_addon_membership_container .wpl_dashboard_links li.active a {
    color: #000;
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
}
a.logout-btn-sec:hover {
    background: #070606;
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
.wpl-gen-grid-wp th:first-child {
    border-left: 0px solid #d4d4d4;
}
.wpl-gen-grid-wp th {
    color: #585252;
    background: #f5f5f500;
    text-align: center;
    /*padding: 10px 0;*/
    padding: 10px 56px 10px 2px;
    border-right: 0px solid #d4d4d4;
    border-top: 1px solid #e6e6e6;
    border-bottom: 1px solid #d4d4d4;
}
span.save-search-head {
    margin: 28px;
    height: 34px;
    width: 216px;
    color: #191919;
    font-family: Inter;
    font-size: 28px;
    font-weight: bold;
    letter-spacing: -0.28px;
    line-height: 34px;
}
.wpl_addon_membership_container .wpl_dashboard_side1 {
    padding: 33px 0px 0px 0px;
    background: white;
}
.wpl_addon_membership_container .wpl_dashboard_links li ul {
    background: rgb(255 255 255);
    border-bottom: 0px solid #c9c9c9;
    border-top: 0px solid #c9c9c9;
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
.wpl_addon_membership_container .wpl_dashboard_side1 {
    padding: 0px;
    background: white;
}
label {
    padding: 10px 0px;
}
.wpl-wp .wpl-button.button-1 {
    border: 1px solid #938c8c !important;
    border-radius: 5px !important;
    background-color: #FFFFFF !important;
    color: #141617 !important;
    text-transform: capitalize;
}
.wpl-wp .wpl-button.button-1:hover {
    background: #141617 !important;
    color: #fff !important;
}
#wpl_dashboard_main_content .profile-wp .panel-wp h3:before {
    content: "\e648";
    visibility: hidden;
}
h3.fusion-responsive-typography-calculated {
    width: 216px;
    color: #191919 !important;
    font-family: Inter;
    font-size: 28px;
    font-weight: bold;
    letter-spacing: -0.28px;
}
#wpl_dashboard_main_content .profile-wp .panel-wp h3 {
    background: #ffffff;
    border-bottom: 1px solid #e6e6e6;
    font: 11pt"Lato",Arial,Helvetica,sans-serif;
    margin-bottom: 0;
    padding: 25px 0px;
    width: 100%;
}
button#wpl_profile_finalize_button {
    border: 1px solid #938c8c !important;
    border-radius: 5px !important;
    background-color: #FFFFFF !important;
    color: #141617 !important;
    padding: 8px 22px !important;
    font-size: 14px !important;
    text-transform: capitalize !important;
    font-family: 'Inter' !important;
    font-weight: 500 !important;
}
button#wpl_profile_finalize_button:hover {
    background: #141617 !important;
    color: #fff !important;
}
input {
    font-size: 14px !important;
}
.wpl-wp.profile-wp .panel-wp .panel-body .wpl_listing_field_container .upload-preview-wp {
    margin-top: 5px;
    border: 2px solid #938c8c47;
    padding: 8px;
    border-radius: 5px;
}
.wpl-wp.profile-wp .panel-wp {
    border: 0px solid #ccc;
}
.chosen-container {
    position: absolute;
    margin-top: 10px !important;
}
div#wpl_listing_location_level_container911_3 {
    display: flex !important;
    align-items: center !important;
}
div#wpl_listing_location_level_container911_4, div#wpl_listing_location_level_container911_5, div#wpl_listing_location_level_container911_zips {
    display: flex !important;
    align-items: center !important;
}
div#wpl_c_906_chosen {
    margin-left: 5px !important;
}
input#wpl_c_900, input#wpl_c_901, input#wpl_c_902, input#wpl_c_903, input#wpl_c_904, input#wpl_c_905, input#wpl_c_907, input#wpl_c_908, input#wpl_c_909, input#wpl_c_914, textarea#wpl_c_918 {
    font-size: 13px !important;
}
input#wpl_listing_location5_select, input#wpl_listing_locationzips_select, input#wpl_listing_location4_select, input#wpl_listing_location3_select {
    font-size: 13px !important;
}
.chosen-container-single .chosen-single {
    padding: 0 0 0 14px !important;
}
.wpl_listing_field_container textarea {
    margin-left: 0px !important;
}
#wpl_dashboard_main_content .profile-wp .panel-wp .finilize-btn {
    margin-right: 80px;
    text-align: right;
}
li.logout_link {
    position: relative;
    top: 45px;
}
</style>
<div class="wrap wpl-wp profile-wp <?php echo wpl_request::getVar('wpl_dashboard', 0) ? '' : 'wpl_view_container'; ?>">
    <header>
        <div id="icon-profile" class="icon48"></div>
        <h2><?php echo __('Profile', 'real-estate-listing-realtyna-wpl'); ?></h2>
    </header>
    <div class="wpl_user_profile"><div class="wpl_show_message"></div></div>
    
    <?php if($my_profile_top_activities): ?>
    <div id="my_profile_top_container">
        <?php
            $activities = wpl_activity::get_activities('my_profile_top', 1);
            foreach($activities as $activity)
            {
                $content = wpl_activity::render_activity($activity, array('user_data'=>$this->user_data));
                if(trim($content) == '') continue;
                ?>
                <div class="panel-wp margin-top-1p">
                    <?php if($activity->show_title and trim($activity->title) != ''): ?>
                    <h3><?php echo __($activity->title, 'real-estate-listing-realtyna-wpl'); ?></h3>
                    <?php endif; ?>
                    <div class="panel-body"><?php echo $content; ?></div>
                </div>
                <?php
            }
        ?>
    </div>
    <?php endif; ?>
    
    <div class="panel-wp margin-top-1p">
        <h3><?php echo __('Profile', 'real-estate-listing-realtyna-wpl'); ?></h3>
        <div class="panel-body">
            <div class="pwizard-panel">
                <div class="pwizard-section">
                    <?php
                        $wpl_flex = new wpl_flex();
                        $wpl_flex->kind = $this->kind;
                        $wpl_flex->generate_wizard_form($this->user_fields, $this->user_data, $this->user_data['id'], $this->finds, $this->nonce);
                    ?>
                </div>
                <div class="text-left finilize-btn">
                    <button class="wpl-button button-1" onclick="wpl_profile_finalize(<?php echo $this->user_data['id']; ?>);" id="wpl_profile_finalize_button" type="button" class="button button-primary"><?php echo __('Finalize', 'real-estate-listing-realtyna-wpl'); ?></button>
                    <span id="wpl_profile_wizard_ajax_loader"></span>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($my_profile_bottom_activities): ?>
    <div id="my_profile_bottom_container">
        <?php
            $activities = wpl_activity::get_activities('my_profile_bottom', 1);
            foreach($activities as $activity)
            {
                $content = wpl_activity::render_activity($activity, array('user_data'=>$this->user_data));
                if(trim($content) == '') continue;
                ?>
                <div class="panel-wp margin-top-1p">
                    <?php if($activity->show_title and trim($activity->title) != ''): ?>
                    <h3><?php echo __($activity->title, 'real-estate-listing-realtyna-wpl'); ?></h3>
                    <?php endif; ?>
                    <div class="panel-body"><?php echo $content; ?></div>
                </div>
                <?php
            }
        ?>
    </div>
    <?php endif; ?>
    
    <footer>
        <div class="logo"></div>
    </footer>
</div>

<script type="text/javascript">
function wpl_profile_finalize(item_id)
{
	/** validate form **/
	if (!wpl_validation_check()) return;
	
	var ajax_loader_element = '#wpl_profile_wizard_ajax_loader';
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	wplj("#wpl_profile_finalize_button").attr("disabled", "disabled");
	
	var request_str = 'wpl_format=b:users:ajax&wpl_function=finalize&item_id=' + item_id + '&_wpnonce=<?php echo $this->nonce; ?>';

	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
	ajax.success(function(data)
	{
		wplj("#wpl_profile_finalize_button").removeAttr("disabled");
		wplj(ajax_loader_element).html('');
		
		if(data.success === 1)
		{
		    <?php /* Force Profile Completion */ if(isset($this->user_data['maccess_fpc']) and $this->user_data['maccess_fpc']): ?>
            window.location.replace("<?php echo wpl_addon_membership::URL('dashboard'); ?>");
            <?php endif; ?>
		}
		else if(data.success !== 1)
		{
		}
	});
}

function wpl_validation_check()
{
    var go_to_error = false;

	<?php
	foreach (wpl_flex::$wizard_js_validation as $js_validation) {
		if (trim($js_validation) == '')
			continue;
	
		echo $js_validation;
	}
	?>
	
	return true;
}
</script>