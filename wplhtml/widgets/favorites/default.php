<?php
defined('_WPLEXEC') or die('Restricted access');

$this->image_width = isset($this->data['image_width']) ? $this->data['image_width'] : 32;
$this->image_height = isset($this->data['image_height']) ? $this->data['image_height'] : 32;
$this->max_char_title = isset($this->data['max_char_title']) ? $this->data['max_char_title'] : 15;
$this->show_contact_form = isset($this->data['show_contact_form']) ? $this->data['show_contact_form'] : 1;
$this->show_compare = isset($this->data['show_compare']) ? $this->data['show_compare'] : 0;

/** import js codes **/
$this->_wpl_import('widgets.favorites.scripts.js', true, true);
?>
<script id="wpl_favorites_tmpl<?php echo $this->widget_id; ?>" type="text/x-handlebars-template">
    <![CDATA[
    <!-- blocker comment: first html node of handlebars script gets swallowed. -->
    <div class="wpl_favorite_widget_title">
        <?php echo $args['before_title']; ?>
            <?php echo $this->title; ?> <div id="wpl_favorites_count<?php echo $this->widget_id; ?>" class="badge">{{count}}</div>
        <?php echo $args['after_title']; ?>
    </div>
    <ul id="wpl_favorites_items<?php echo $this->widget_id; ?>" class="wpl_favorites_items">
        {{#each listings}}
        <li id="wpl_favorites_item<?php echo $this->widget_id; ?>_{{id}}">
            <a href="{{link}}" target="_blank">
                {{#if image}}
                <img class="wpl_favorite_item_image" src="{{image}}" width="<?php echo $this->image_width; ?>" height="<?php echo $this->image_height; ?>" />
                {{else}}
                <div class="no_image_box"></div>
                {{/if}}
                <span class="wpl_favorite_item_title">{{title}}</span>
            </a>
            <span class="wpl_favorite_item_remove" onclick="wpl_remove_favorite({{id}})">x</span>
        </li>
        {{/each}}
    </ul>
    <!-- ]]> -->
</script>
<div id="wpl_favorite_listings_cnt<?php echo $this->widget_id; ?>" class="wpl_favorite_listings <?php echo $this->css_class; ?>"></div>
<?php if($this->show_contact_form): ?>
    <div class="wpl_favorite_contact_form">
        <form method="post" action="#" id="wpl_favorite_form<?php echo $this->widget_id; ?>" onsubmit="return wpl_send_favorite(<?php echo $this->widget_id; ?>);">
            <div class="form-field">
                <input class="text-box" type="text" id="wpl_favorite_fullname" name="fullname" placeholder="<?php echo __('Full Name', 'real-estate-listing-realtyna-wpl'); ?>" />
            </div>

            <div class="form-field">
                <input class="text-box" type="text" id="wpl_favorite_phone" name="phone" placeholder="<?php echo __('Phone', 'real-estate-listing-realtyna-wpl'); ?>" />
            </div>

            <div class="form-field">
                <input class="text-box" type="text" id="wpl_favorite_email" name="email" placeholder="<?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?>" />
            </div>

            <div class="form-field">
                <textarea class="text-box" id="wpl_favorite_message" name="message" placeholder="<?php echo __('Message', 'real-estate-listing-realtyna-wpl'); ?>"></textarea>
            </div>

            <?php echo wpl_global::include_google_recaptcha('gre_widget_contact_activity'); ?>
            <?php wpl_security::nonce_field('wpl_favorites_contact_form'); ?>
            
            <div class="form-field">
                <input class="btn btn-primary" id="wpl-favorite-contact-agent" type="submit" value="<?php echo __('Contact Favorites', 'real-estate-listing-realtyna-wpl'); ?>" />
            </div>
        </form>
        <div id="wpl_favorite_ajax_loader_<?php echo $this->widget_id; ?>"></div>
        <div id="wpl_favorite_message_<?php echo $this->widget_id; ?>"></div>
    </div>
<?php endif; ?>
<?php if($this->show_compare): ?>
    <div class="form-field">
        <button style="margin: -5px 0px 0px 10px;border-radius: 0px;padding-left: 8px;padding-right: 8px;" onclick="location='<?php echo $this->compare_url ?>';" class="btn btn-primary" id="wpl-favorite-compare"><?php echo __('Compare favorites', 'real-estate-listing-realtyna-wpl'); ?></button>
    </div>
<?php endif; ?>

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
.wpl_addon_membership_container .wpl_dashboard_side1 {
    padding: 5px 30px 40px 30px;
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
h3.wpl_activity_title.fusion-responsive-typography-calculated {
    padding-left: 12px;
}
input#wpl_favorite_fullname, input#wpl_favorite_phone, input#wpl_favorite_email, textarea#wpl_favorite_message {
    font-size: 14px;
}
button#wpl-favorite-compare {
    background: #191919;
    font-size: 14px;
}
input#wpl-favorite-contact-agent {
    font-size: 14px;
    background: #f8f8f8 !important;
    color: black;
    border: 1px solid;
}
</style>