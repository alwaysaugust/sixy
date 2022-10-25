<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$wplview = wpl_request::getVar('wplview', '');
if($wplview == 'property_show')
{
    $pid = wpl_request::getVar('pid', 0);
    if(!$pid) return false;

    $mls_id = wpl_property::get_property_field('mls_server_id', $pid);
    if(!$mls_id) return false;
}
else
{
    $mls_id = wpl_request::getVar('sf_select_mls_server_id', 0);
    if(!$mls_id) $mls_id = wpl_db::select("SELECT `id` FROM `#__wpl_addon_mls` ORDER BY `id` ASC LIMIT 1", 'loadResult');

    if(!$mls_id) return false;
}
        
$mls_params = wpl_global::get_params('wpl_addon_mls', $mls_id);
?>
<div id="wpl_mls_disclaimer_widget_cnt<?php echo $this->widget_id; ?>" class="wpl-mls_disclaimer-widget <?php echo $this->css_class; ?>">
    <?php echo (trim($this->title) ? $args['before_title'].__($this->title, 'real-estate-listing-realtyna-wpl').$args['after_title'] : ''); ?>
    
    <div class="mls-disclaimer-left">
        <a <?php echo (isset($mls_params['link']) ? ' href="'.$mls_params['link'].'"' : ''); ?>>
            <?php if(isset($mls_params['logo']) and trim($mls_params['logo'])): ?><img src="<?php echo $mls_params['logo']; ?>" /><?php endif; ?>
            <span><?php echo (isset($mls_params['name']) ? $mls_params['name'] : ''); ?></span>
        </a>
    </div>
    <div class="mls-disclaimer-right">
        
        <?php if(isset($mls_params['description']) and trim($mls_params['description'])): ?>
        <div class="mls-disclaimer-content mls-disclaimer-collapse">
            <?php echo !preg_match('!!u', $mls_params['description']) ? utf8_decode($mls_params['description']) : $mls_params['description']; ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($instance['data']['copyright']) and $instance['data']['copyright'] == 'show'): ?>
        <div class="mls-copyright"><?php echo sprintf(__('Copyright %s %s, All Rights Reserved.', 'real-estate-listing-realtyna-wpl'), date('Y'), (isset($mls_params['name']) ? $mls_params['name'] : '')); ?></div>
        <?php endif; ?>
        
        <?php if(isset($instance['data']['last_update']) and $instance['data']['last_update'] == 'show'): $last_updated_date = wpl_db::select("SELECT `date` FROM `#__wpl_addon_mls_data` ORDER BY `date` DESC LIMIT 1", 'loadResult'); ?>
        <div class="mls-last-update"><?php echo sprintf(__('Data last updated: %s', 'real-estate-listing-realtyna-wpl'), '<span>'.(($last_updated_date and $last_updated_date != '0000-00-00') ? date('m/d/Y g:i', strtotime($last_updated_date)) : __('Unknown', 'real-estate-listing-realtyna-wpl')).'</span>'); ?></div>
        <?php endif; ?>
    </div>
</div>