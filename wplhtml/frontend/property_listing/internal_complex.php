<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->properties_str = $this->_wpl_render($this->tpl_path.'.assets.internal_complex_listings', true);
if($this->wplraw == 1)
{
    echo $this->properties_str;
    exit;
}
?>
<div class="wpl-complex-unit">
    <?php if($this->property_css_class_switcher): ?>
    <div class="wpl_list_grid_switcher clearfix <?php if($this->switcher_type == "icon+text") echo 'wpl-list-grid-switcher-icon-text'; ?>">
        <div id="grid_view" class="grid_view <?php if(in_array($this->property_css_class, array('grid_box', 'map_box'))) echo 'active'; ?>">
            <?php if($this->switcher_type == "icon+text") echo '<span>'.__('Grid', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
        </div>
        <div id="list_view" class="list_view <?php if($this->property_css_class == 'row_box') echo 'active'; ?>">
            <?php if($this->switcher_type == "icon+text") echo '<span>'.__('List', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="wpl_property_listing_complexes_container clearfix">
        <?php
            if(trim($this->properties_str) != '')
            {
                echo '<div class="wpl-row">';
                echo $this->properties_str;
                echo '</div>';
            }
            else echo '<div class="wpl-complex-message">'.__('No units are listed.', 'real-estate-listing-realtyna-wpl').'</div>';
        ?>
    </div>

    <div class="wpl_pagination_container">
    	<?php echo $this->pagination->show(); ?>
    </div>
    <script>
        function wpl_pagesize_changed(page_size) {
            url = window.location.href;
            url = wpl_update_qs('limit', page_size, url);
            url = wpl_update_qs('wplpage', '1', url);
            window.location = url;
        }

        jQuery(document).ready(function () {
            jQuery.each(jQuery('.wpl-complex-tabs-wp ul li'),function(k1,v1){
                <?php if (isset($_GET['tab']) AND !empty($_GET['tab'])) { ?>
                    jQuery(this).attr('class','');
                    jQuery(this).find('a').attr('class','');
                    if (jQuery(this).find('a').attr('href')=='#<?=$_GET['tab']?>') {
                        jQuery(this).attr('class','wpl-gen-tab-active-parent');
                        jQuery(this).find('a').attr('class','wpl-gen-tab-active');
                        jQuery('.wpl-complex-tab-content').hide();
                        jQuery('#<?=$_GET['tab']?>').show();
                    }
                <?php } ?>
                let tab = jQuery(this).find('a').attr('href');
                jQuery.each(jQuery(tab+" .wpl_pagination_container .pagination a[href!='#']"),function(k2,v2){
                    let new_href = jQuery(this).attr('href')+'&tab='+tab.replace('#','');
                    if (!jQuery(this).attr('href').includes('tab=')) {
                        jQuery(this).attr('href',new_href);
                    }
                });
            });
        });
    </script>
</div>