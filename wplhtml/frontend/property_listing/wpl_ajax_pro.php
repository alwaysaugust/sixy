<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.locations');
_wpl_import('libraries.addon_pro');

class wpl_property_listing_controller extends wpl_controller
{
    public function display()
    {
        $function = wpl_request::getVar('wpl_function');

        if($function == 'favorites_control') $this->favorites_control();
        elseif($function == 'favorites_load') $this->favorites_load();
        elseif($function == 'favorites_send') $this->favorites_send();
        elseif($function == 'get_locations_options') $this->get_locations_options();
    }

    private function favorites_control()
    {
        $property_id = wpl_request::getVar('pid');
        $mode = wpl_request::getVar('mode');
        $mode = $mode ? 'add' : 'remove';
            
        $results = wpl_addon_pro::favorite_add_remove($property_id, $mode);
        $pids = wpl_addon_pro::favorite_get_pids();
        
        $response = array('success'=>(int) $results, 'pids'=>$pids);
        echo json_encode($response);
        exit;
    }

    private function favorites_load()
    {
        $image_width = wpl_request::getVar('image_width', 32);
        $image_height = wpl_request::getVar('image_height', 32);
        $wpltarget = wpl_request::getVar('wpltarget', 0);
        
        $results = wpl_addon_pro::favorites_load('', $image_width, $image_height, $wpltarget);
        
        echo json_encode($results);
        exit;
    }

    private function favorites_send()
    {
        $fullname = wpl_request::getVar('fullname', '');
        $phone = wpl_request::getVar('phone', '');
        $email = wpl_request::getVar('email', '');
        $message = wpl_request::getVar('message', '');
        $gre = wpl_request::getVar('g-recaptcha-response', '');

        $parameters = array(
            'fullname' => $fullname,
            'phone' => $phone,
            'email' => $email,
            'message' => $message
        );

        // check recaptcha 
        $gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_widget_contact_activity');

        $returnData = array();
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your email is not a valid email!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_favorites_contact_form'))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif($gre_response['success'] === 0)
        {
            $returnData['success'] = 0;
            $returnData['message'] = $gre_response['message'];
        }
        else
        {
            if(wpl_addon_pro::favorites_send($parameters))
            {
                $returnData['success'] = 1;
                $returnData['message'] = __('Information sent to agents.', 'real-estate-listing-realtyna-wpl');
            }
            else
            {
                $returnData['success'] = 0;
                $returnData['message'] = __('Error sending!', 'real-estate-listing-realtyna-wpl');
            }
        }
        
        echo json_encode($returnData);
        exit;
    }

    private function get_locations_options()
    {
        $level = wpl_request::getVar('level', 0);
        $parent = wpl_request::getVar('parent', '');

        if(!trim($level))
        {
            $this->response(array('success'=>0));
            return;
        }

        $location_settings = wpl_global::get_settings('3'); # location settings

        $kind = wpl_request::getVar('kind', 0);
        $next_level = wpl_request::getVar('next_level', 0);

        $parent_column = "location".$level."_name";
        $target_column = "location".$next_level."_name";

        if(trim($parent))
        {
            $query = "SELECT `$target_column` FROM `#__wpl_properties` WHERE `kind`='".$kind."' AND `finalized`='1' AND `expired`='0' AND `confirmed`='1' AND `deleted`='0' AND `$target_column`!='' AND `$parent_column` IN ('".str_replace(',', "','", $parent)."') GROUP BY `$target_column` ORDER BY `$target_column` ASC";
            $locations = wpl_db::select($query, 'loadAssocList');
        }
        else $locations = array();

        $html = '<option value="">'.__($location_settings['location'.$next_level.'_keyword'], 'real-estate-listing-realtyna-wpl').'</option>';
        foreach($locations as $location) $html .= '<option value="'.$location[$target_column].'">'.$location[$target_column].'</option>';

        $this->response(array('success'=>1, 'html'=>$html));
    }
}