<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.property');
_wpl_import('libraries.addon_save_searches');
_wpl_import('libraries.activities');

abstract class wpl_addon_save_searches_controller_abstract extends wpl_controller
{
	public $tpl_path = 'views.frontend.addon_save_searches.tmpl';
	public $tpl;
	public $model;
	
	public function display($instance = array())
	{
        /** Set tpl to NULL **/
        if(wpl_request::getVar('tpl') == 'default') wpl_request::setVar('tpl', NULL);
        
        $this->save_searches = new wpl_addon_save_searches();
        $this->wplmethod = (isset($instance['wplmethod']) and trim($instance['wplmethod'])) ? $instance['wplmethod'] : wpl_request::getVar('wplmethod', 'listing');
        $this->wpl_security = new wpl_security();
        
        /** global settings **/
		$this->settings = wpl_global::get_settings();
        
		/** Users object **/
		$this->users = new wpl_users();
        
        if($this->wplmethod == 'listing') $output = $this->listing();
        elseif($this->wplmethod == 'form') $output = $this->form();
        elseif($this->wplmethod == 'search') $output = $this->search();
        elseif($this->wplmethod == 'unsubscribe') $output = $this->unsubscribe();
        else $output = $this->undefined();
        
        if($this->wplraw)
        {
            echo $output;
            exit;
        }
        else
        {
            /** Return **/
            return $output;
        }
	}
    
    private function listing()
    {
        $this->tpl = wpl_request::getVar('tpl', 'listing');
        
        /** Guest User **/
        if(!$this->users->check_user_login())
        {
            $login = wp_login_url(wpl_global::get_full_url());
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first to see your saved searches!', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->user_id = ($this->users->is_administrator() and wpl_request::getVar('uid', NULL)) ? wpl_request::getVar('uid') : $this->users->get_cur_user_id();
        $this->user_data = $this->users->get_user($this->user_id);
        $this->searches = $this->save_searches->get(NULL, $this->user_id);
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function form()
    {
        $tpl = wpl_request::getVar('tpl', 'form');
        
        $vars = array_merge(wpl_request::get('GET'), wpl_request::get('POST'));
        $this->criteria = array();
        
        foreach($vars as $key=>$value)
        {
            if(substr($key, 0, 3) != 'sf_' or trim($value) == '') continue;
            $this->criteria[$key] = $value;
        }
        
        /** No Criteria **/
        if(!count($this->criteria))
        {
            /** import message tpl **/
            $this->message = __("No Criteria! Please, perform a search.", 'real-estate-listing-realtyna-wpl');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->kind = wpl_request::getVar('kind', 0);
        $this->search_url = urldecode(wpl_request::getVar('searchurl', ''));
        
        if($this->users->check_user_login())
        {
            $this->tpl = $tpl.'_user';
            $this->user_id = $this->users->get_cur_user_id();
        }
        else
        {
            $this->tpl = $tpl.'_guest';
            $this->user_id = 0;
        }
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function search()
    {
        $this->tpl = wpl_request::getVar('tpl', 'search');
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function unsubscribe()
    {
        $key = wpl_request::getVar('key');
        
        $results = $this->save_searches->unsubscribe($key);
        
        // Message
        if($results) $this->message = __("You have been successfully unsubscribed!", 'real-estate-listing-realtyna-wpl');
        else $this->message = __("We couldn't recognize your saved search to unsubscribe!", 'real-estate-listing-realtyna-wpl');
        
        return parent::render($this->tpl_path, 'message', false, true);
    }
    
    private function undefined()
    {
        /** import message tpl **/
        $this->message = __("Undefined Action!", 'real-estate-listing-realtyna-wpl');
        return parent::render($this->tpl_path, 'message', false, true);
    }
}