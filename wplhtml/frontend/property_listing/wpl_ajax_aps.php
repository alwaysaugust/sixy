<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.locations');
_wpl_import('libraries.addon_aps');

class wpl_property_listing_controller extends wpl_controller
{
    public function display()
    {
        $function = wpl_request::getVar('wpl_function');

        if($function == 'suggestion_search_autocomplete') $this->suggestion_search_autocomplete();
    }

    /**
     * Ajax request to getting the autocomlete of search fileds
     * @author Matthew N. <matthew@realtyna.com>
     * @return void
     */
    private function suggestion_search_autocomplete()
    {
        $term = wpl_request::getVar('term', '');
        $field_name = wpl_request::getVar('field_name', '');
        $kind = wpl_request::getVar('kind', '0');
        $limit = 10;

        if(stristr($field_name, 'location'))
        {
            $query = "SELECT `{$field_name}_name` AS name FROM `#__wpl_properties` WHERE `{$field_name}_name` LIKE '" . $term . "%' AND `kind`='$kind' GROUP BY `{$field_name}_name` LIMIT " . $limit;
        }
        elseif($field_name == 'zip_name')
        {
            $query = "SELECT `zip_name` AS name FROM `#__wpl_properties` WHERE `zip_name` LIKE '" . $term . "%' AND `kind`='$kind' GROUP BY `zip_name` LIMIT " . $limit;
        }
        elseif($field_name == 'feature')
        {
            $query = "SELECT `name`, `id` FROM `#__wpl_dbst` WHERE `name` LIKE '%" . $term . "%' AND `type`='feature' AND `kind`='$kind' LIMIT " . $limit;
            $results = wpl_db::select($query, 'loadAssocList');

            $output = array();
            foreach($results as $result)
            {
                $output[] = array('label'=>$result['name'], 'value'=>$result['name'], 'id'=>$result['id']);
            }

            echo json_encode($output);
            exit;
        }
        elseif($field_name == 'neighborhood')
        {
            $kind = 4;

            $parents = wpl_property::select_active_properties("AND (`mls_id` LIKE '%$term%' OR `field_312` LIKE '%$term%' OR `field_313` LIKE '%$term%') AND `kind`='$kind' ", '`id`, `mls_id`');
            $results = array();

            foreach($parents as $parent)
            {
                $label = wpl_property::update_property_title(NULL, $parent['id']);
                $results[] = array('id'=>$parent['id'], 'label'=>$label, 'value'=>$label);
            }

            echo json_encode($results);
            exit;
        }
        elseif($field_name == 'address') $query = "SELECT count(`id`) as count, `location_text` AS name FROM `#__wpl_properties` WHERE `location_text` LIKE '%" . trim($term) . "%' LIMIT " . $limit;
        else return;

        $results = wpl_db::select($query, 'loadAssocList');

        if(is_null($results)) exit;

        $output = array();
        foreach($results as $result)
        {
            $output[] = array('label'=>$result['name'], 'value'=>$result['name']);
        }

        echo json_encode($output);
        exit;
    }
}