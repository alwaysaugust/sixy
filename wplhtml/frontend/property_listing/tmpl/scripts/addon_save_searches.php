<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
});

function wpl_generate_save_search()
{
    /** Open lightbox **/
    wplj._realtyna.lightbox.open("#wpl_save_search_link_lightbox", {reloadPage: false});
    
    var ss = '';
    var searchurl = window.location.href;
    
    ss = wpl_update_qs('wplpage', '', wpl_listing_request_str);
    if(ss !== '') ss = wpl_update_qs('wplview', '', ss);
    if(ss !== '') ss = wpl_update_qs('wplpagination', '', ss);
    if(ss !== '') ss = wpl_update_qs('uid', '', ss);
    if(ss !== '') ss = wpl_update_qs('tpl', '', ss);
    
    var request_str = 'wpl_format=f:addon_save_searches:raw&wplmethod=form'+(ss !== '' ? '&'+ss : '')+'&searchurl='+encodeURIComponent(searchurl);
    var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_wp_url(); ?>', request_str, false, 'HTML', 'GET');
	
	ajax.success(function(html)
	{
        wplj("#wpl_plisting_lightbox_content_container").html(html);
	});
}
</script>