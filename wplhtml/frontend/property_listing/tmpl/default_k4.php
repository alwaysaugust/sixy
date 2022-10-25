<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->properties_str = $this->_wpl_render($this->tpl_path.'.assets.default_neighborhoods', true);
if($this->wplraw == 1)
{
    echo $this->properties_str;
    exit;
}

$this->listview_str = $this->_wpl_render($this->tpl_path.'.assets.default_neighborhoods_listview', true);
if($this->wplraw == 2)
{
    echo json_encode(array('total_pages'=>$this->total_pages, 'current_page'=>$this->page_number, 'html'=>$this->listview_str));
    exit;
}

$this->_wpl_import($this->tpl_path.'.scripts.js', true, true);
if($this->wplpagination == 'scroll' and $this->property_listview and wpl_global::check_addon('pro')) $this->_wpl_import($this->tpl_path.'.scripts.js_scroll', true, true);

/** Save Search Add-on **/
if(wpl_global::check_addon('save_searches')) $this->_wpl_import($this->tpl_path.'.scripts.addon_save_searches', true, true);

/** import print library if is enabled **/
if(wpl_global::check_addon('PRO') and wpl_global::get_setting('pdf_results_page_method') == 'print')
{
	$js[] = (object) array('param1'=>'jquery.print.js', 'param2'=>'js/libraries/wpl.jquery.print.min.js','param4'=>'1');
	foreach($js as $javascript) wpl_extensions::import_javascript($javascript);
}
?>
<div class="wpl_property_listing_container wpl-neighborhood-addon <?php if(!$this->listing_picture_mouseover) echo "wpl-prp-disable-image-hover"; ?>" id="wpl_property_listing_container">
	<?php /** load position1 **/ wpl_activity::load_position('nlisting_position1', array('wpl_properties'=>$this->wpl_properties)); ?>
    
    <?php if(is_active_sidebar('wpl-nlisting-top') and $this->kind == 4): ?>
    <div class="wpl_plisting_top_sidebar_container wpl_nlisting_top_sidebar_container">
        <?php dynamic_sidebar('wpl-nlisting-top'); ?>
    </div>
    <?php endif; ?>
    
    <?php if($this->property_listview): ?>
    <div class="wpl_property_listing_list_view_container">
        <?php echo $this->listview_str; ?>
    </div>
    <?php endif; ?>
    
    <?php /** Don't remove this element **/ ?>
    <div id="wpl_plisting_lightbox_content_container" class="wpl-util-hidden"></div>

    <?php /** Favorite Login / Register Popup **/ ?>
    <a id="wpl_favorites_lightbox" class="wpl-util-hidden" data-realtyna-href="#wpl_plisting_lightbox_content_container" data-realtyna-lightbox-opts="title:<?php echo __('Login to continue', 'real-estate-listing-realtyna-wpl'); ?>"></a>
    
</div>
<div class="wpl-powered-by-realtyna" style="display: none;">
    <a href="https://realtyna.com">
        <img src="<?php echo wpl_global::get_wpl_url().'assets/img/idx/powered-by-realtyna.png'; ?>" alt="Powered By Realtyna" width="120" />
    </a>
</div>