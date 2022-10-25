<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.addon_aps');

/** activity class **/
class wpl_activity_main_listing_results extends wpl_activity
{
    public $tpl_path = 'views.activities.listing_results.tmpl';
	
	public function start($layout, $params)
	{
        // Results Links
        $search_url = wpl_session::get('wpl_last_search_url');
        if(!trim($search_url)) return;
        
        $where = wpl_session::get('wpl_listing_criteria');
        $orderby = wpl_session::get('wpl_listing_orderby');
        $order = wpl_session::get('wpl_listing_order');
        $total = wpl_session::get('wpl_listing_total');

         /* Flare Rush */
         $wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();
   $property_id = (int) isset($wpl_properties['current']['data']['id']) ? $wpl_properties['current']['data']['id'] : NULL;

         $listings_positions = wpl_session::get('listings_positions');
         if(!$listings_positions) return;

         $listings_positions = json_decode($listings_positions);
         if(!$listings_positions) return;

         $pids = $listings_positions->pids;

         $position = array_search($property_id, $pids);
         if($position === false) return;

         $previous = 0;
         $next = 0;
         if(isset($pids[$position - 1])) $previous = $pids[$position - 1];
         if(isset($pids[$position + 1])) $next = $pids[$position + 1];

         $position += $listings_positions->offset + 1;
         /* end */
         
        
		// Include TPL Layout
		include _wpl_import($layout, true, true);
	}
}