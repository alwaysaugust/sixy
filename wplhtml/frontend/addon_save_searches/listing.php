<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.listing', true, ($this->wplraw ? false : true));
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
    color: #191919;
    float: right;
}
.wpl-ewallet-charge input[type="submit"] {
    background: #ffffff;
    border: 1px solid;
    color: #191919;
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
    background: #191919;
    color: #f8f8f8 !important;
}
li.logout_link {
    /*position: relative;*/
    /*left: 21px;*/
    /*top: 44px;*/
    /*margin-top: 55px;*/
}
li.logout_link {
    position: relative;
    top: 45px;
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
    text-align: left;
    padding-left: 30px !important;
    /*padding: 10px 24px 10px 10px;*/
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
    background: #ffff; 
    border-bottom: 1px solid #c9c9c9;
    border-top: 1px solid #c9c9c9;
    display: none;
}
li.add_prp_link.add, .edit {
    padding: 0;
}
/*.wpl_addon_membership_container .wpl_dashboard_links li ul li {*/
/*    line-height: 15px;*/
/*    padding: 0 10px;*/
/*    font-weight: 700;*/
/*    border-radius: 5px;*/
/*    width: 150px;*/
/*    background: #e2e2e238;*/
/*}*/
.wpl_addon_membership_container .wpl_dashboard_links li ul li {
    line-height: 2px;
    padding: 10px;
    font-weight: 700;
    border-radius: 5px;
    width: 150px;
    background: #e2e2e238;
}
li.manage_prp_link.edit {
    position: absolute;
    top: 65px;
}
.wpl-addon-save-search-notify-mode {
    width: max-content;
}
.custom-thead {
    font-size: 13px;
    position: relative;
    right: 16px;
}
tbody {
    font-size: 12px;
}
input.wpl_addon_save_searches_alias_custom {
    font-size: 12px;
}
div#wpl_save_search_name_content5 {
    width: max-content;
}
input[type='radio']:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    top: -2px;
    left: -1px;
    position: relative;
    background-color: #d1d3d1;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
}

input[type='radio']:checked:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    top: -2px;
    left: -1px;
    position: relative;
    background-color: #585252;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
}
div#wpl_save_search_name_content6, div#wpl_save_search_name_content4, div#wpl_save_search_name_content3, div#wpl_save_search_name_content2, div#wpl_save_search_name_content5 {
    display: flex;
    justify-content: space-between;
}
.fusion-body .fusion-builder-column-10 {
    margin-top: 0 !important;
}
.wpl-addon-save-search-grid input[type=text]:focus {
    box-shadow: inset 0 0 0 1px #191919;
    border-color: white;
}
.wpl-gen-grid-wp tr:hover td {
    background: rgb(100 100 93 / 10%);
}
</style>
<div class="wpl-save-search-addon wpl-addon-save-search-list-wp <?php echo wpl_request::getVar('wpl_dashboard', 0) ? '' : 'wpl_view_container'; ?>" id="wpl_addon_save_searches_container">
    <span class="save-search-head">Saved Searches</span>
    <?php if($this->users->is_administrator() and $this->user_id != $this->users->get_cur_user_id()): ?>
        <div class="wpl-addon-save-search-username"><?php echo sprintf(__('Saved Searches of %s', 'real-estate-listing-realtyna-wpl'), '<span>'.$this->user_data->data->user_login.'</span>'); ?></div>
    <?php endif; ?>

    <?php if($this->users->is_administrator() and wpl_sef::is_permalink_default()): ?>
        <div class="wpl_message_container"><?php echo __("To use the alias feature of the Saved Searches Add-on, your WordPress Permalink structure should not be set to default", 'real-estate-listing-realtyna-wpl'); ?></div>
    <?php endif; ?>

    <div class="wpl-save-search-msg" id="wpl_save_searches_list_show_messages"></div>

    <table class="wpl-gen-grid-wp wpl-gen-grid-center wpl-addon-save-search-grid" id="wpl_addon_save_searches_list_container">
        <thead class="custom-thead">
            <tr>
                <th><?php echo __('Search', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th><?php echo __('Created', 'real-estate-listing-realtyna-wpl'); ?></th>
                <?php if($this->users->is_administrator() and !wpl_sef::is_permalink_default()): ?>
                <th><?php echo __('Alias', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th><?php echo __('Link', 'real-estate-listing-realtyna-wpl'); ?></th>
                <?php endif; ?>
                <th><?php echo __('Notify mode', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th><?php echo __('Criteria', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th><span class="wpl-addon-save-search-remove-btn" id="wpl_addon_save_searches_delete_all" onclick="wpl_addon_save_searches_delete_all(<?php echo $this->user_id; ?>, 0);" title="<?php echo __('Delete All', 'real-estate-listing-realtyna-wpl'); ?>"></span></th>
            </tr>
        </thead>

        <tbody>

            <?php foreach($this->searches as $search): ?>
            <tr id="wpl_addon_save_search_item<?php echo $search['id']; ?>">

                <td><div id="wpl_save_search_name_content<?php echo $search['id']; ?>" class="wpl_save_search_name_content"><a target="_blank" id="wpl_save_search_name<?php echo $search['id']; ?>" href="<?php echo $search['url']; ?>"><?php echo $search['name']; ?></a>
		                <span id="wpl_save_search_edit_name<?php echo $search['id']; ?>" class="wpl-addon-save-search-edit-btn" onclick="wpl_addon_save_searches_edit_name(<?php echo $search['id']; ?>)"></span>
	                </div>
                </td>
                <td><?php echo $search['creation_date']; ?></td>

                <?php if($this->users->is_administrator() and !wpl_sef::is_permalink_default()): ?>
                    <td><input type="text" class="wpl_addon_save_searches_alias_custom" id="wpl_addon_save_searches_alias<?php echo $search['id']; ?>" placeholder="<?php echo __('Set an alias for link...', 'real-estate-listing-realtyna-wpl'); ?>" onchange="wpl_addon_save_searches_alias(<?php echo $search['id']; ?>);" value="<?php echo $search['alias']; ?>" /></td>

                    <td><a class="wpl-addon-save-search-show-link-btn" id="wpl_addon_save_searches_link<?php echo $search['id']; ?>" href="<?php echo $this->save_searches->URL($search['id']); ?>" target="_blank" title="<?php echo __('Open SEF link', 'real-estate-listing-realtyna-wpl'); ?>"></a></td>
                <?php endif; ?>
                <td>
                    <div class="wpl-addon-save-search-notify-mode">
                        <input type="radio" id="wpl_notify_once_a_day<?php echo $search['id']; ?>" name="wpl_notify_mode<?php echo $search['id']; ?>" <?php echo (($search['notify_mode'] == 'once_a_day') ? 'checked="checked"' :''); ?> onchange="wpl_addon_save_searches_notify_mode(<?php echo $search['id']; ?>, 'once_a_day');" />
                        <label for="wpl_notify_once_a_day<?php echo $search['id']; ?>"> <?php echo __('Once a day', 'real-estate-listing-realtyna-wpl'); ?> </label>
                    </div>
                    <div class="wpl-addon-save-search-notify-mode">
                        <input type="radio" id="wpl_notify_after_adding_property<?php echo $search['id']; ?>" name="wpl_notify_mode<?php echo $search['id']; ?>" <?php echo (($search['notify_mode'] == 'after_adding_property') ? 'checked="checked"' :''); ?> onchange="wpl_addon_save_searches_notify_mode(<?php echo $search['id']; ?>, 'after_adding_property');" />
                        <label for="wpl_notify_after_adding_property<?php echo $search['id']; ?>"> <?php echo __('After adding a property', 'real-estate-listing-realtyna-wpl'); ?></label>
                    </div>
                </td>
                <td>
	                <?php
	                $search_fields = !empty($search['criteria']) ? json_decode($search['criteria'], true) : array();
	                $readable_criteria = wpl_global::generate_readable_criteria($search_fields);
	                ?>
	                <a class="wpl-addon-save-search-detail-btn" id="wpl_save_search_see_criteria<?php echo $search['id']; ?>" title="<?php echo __('Criteria', 'real-estate-listing-realtyna-wpl'); ?>" data-realtyna-lightbox-opts="position:center|title:<?php echo __('Criteria', 'real-estate-listing-realtyna-wpl'); ?>|reloadPage:false|wrapperClass:wpl-frontend-lightbox-wp" data-realtyna-lightbox href="#wpl_save_search_see_criteria_box<?php echo $search['id']; ?>"></a>
	                <div class="wpl-saved-search-criteria-box" id="wpl_save_search_see_criteria_box<?php echo $search['id']; ?>" style="display:none">
		                <?php foreach($readable_criteria as $field_name=>$field_value)
		                {
			                if(!empty($field_value)) echo '<p>'.$field_name.' = '.$field_value.'</p>';
		                }
		                ?>
	                </div>
                </td>
                <td><span class="wpl-addon-save-search-remove-btn" id="wpl_addon_save_searches_delete<?php echo $search['id']; ?>" onclick="wpl_addon_save_searches_delete(<?php echo $search['id']; ?>, 0);" title="<?php echo __('Delete', 'real-estate-listing-realtyna-wpl'); ?>"></span></td>
            </tr>
            <?php endforeach; ?>

            <?php if(!count($this->searches)): ?>
                <tr>
                    <td class="wpl-gen-grid-no-result" colspan="1000">
                        <?php echo __('No saved search to show!', 'real-estate-listing-realtyna-wpl'); ?>
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
</div>
<style>
	.wpl-saved-search-criteria-box {display: none;}
	.wpl-saved-search-criteria-box p {margin: 10px;}
</style>