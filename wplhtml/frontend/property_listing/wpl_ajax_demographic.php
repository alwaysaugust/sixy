<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_demographic');

class wpl_property_listing_controller extends wpl_controller
{
    public $demographic;

    public function display()
    {
        $function = wpl_request::getVar('wpl_function');
        
        /** Demographic Object **/
        $this->demographic = new wpl_addon_demographic();
        
        if($function == 'load') $this->load_demographics();
    }

    private function load_demographics()
    {
        $ne_coords = wpl_request::getVar('ne_coords');
        $sw_coords = wpl_request::getVar('sw_coords');
        $limit = wpl_request::getVar('limit', 200);
        $type = wpl_request::getVar('type', 'shape');
        $category = wpl_request::getVar('category', '');
        $exclude = wpl_request::getVar('exclude');
        $unit_id = wpl_request::getVar('unit_id', 260);
        
        $tolerance = 0.1;
        $dquery = array();
        
        $dquery[] = "`type`='$type'";
        if(trim($category) != '') $dquery[] = "`category`='$category'";
        
        if(trim($ne_coords))
        {
            $ne_coords = explode(',', $ne_coords);
            $dquery[] = "`latitude`<='".($ne_coords[0] + $tolerance)."' AND `longitude`<='".($ne_coords[1] + $tolerance)."'";
        }

        if(trim($sw_coords))
        {
            $sw_coords = explode(',', $sw_coords);
            $dquery[] = "`latitude`>='".($sw_coords[0] - $tolerance)."' AND `longitude`>='".($sw_coords[1] - $tolerance)."'";
        }

        if(trim($exclude)) $dquery[] = "`id` NOT IN ($exclude)";

        $where = implode(' AND ', $dquery);
        
        $query = "SELECT * FROM `#__".$this->demographic->table."` WHERE 1 AND ".$where." LIMIT ".$limit;
        $demographics = wpl_db::select($query, 'loadAssocList');
        
        $objects = array();
        foreach($demographics as $demographic)
        {
            $demographic['category'] = wpl_global::human_readable($demographic['category']);
            $demographic['boundary'] = json_decode($demographic['boundary'], true);
            
            if($demographic['population']) $demographic['population_rendered'] = number_format($demographic['population'], 0, '.', ',');
            if($demographic['median_income']) $demographic['median_income_rendered'] = wpl_render::render_price($demographic['median_income'], $unit_id);
            if($demographic['average_home_value']) $demographic['average_home_value_rendered'] = wpl_render::render_price($demographic['average_home_value'], $unit_id);
            
            /** Remove unnecessary data **/
            unset($demographic['enabled']);
            unset($demographic['params']);
            
            $objects[$demographic['id']] = $demographic;
        }
        
        echo json_encode(array('count'=>count($objects), 'objects'=>array_values($objects)));
        exit;
    }
}