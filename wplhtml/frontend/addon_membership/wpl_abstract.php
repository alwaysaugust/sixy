<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.property');
_wpl_import('libraries.addon_membership');
_wpl_import('libraries.activities');

abstract class wpl_addon_membership_controller_abstract extends wpl_controller
{
	public $tpl_path = 'views.frontend.addon_membership.tmpl';
	public $tpl;
	public $model;

    /**
     * @var wpl_addon_membership
     */
    public $membership;

    /**
     * @var wpl_security
     */
    public $wpl_security;
    public $target;
    public $method;
    public $redirect_to;
    public $settings;
    public $wplraw;
    public $interim_login;
    public $message;
    public $user_id;
    public $user_data;
    public $key;
    public $membership_id;
    public $membership_data;
    public $user_type;
    public $user_type_data;
    public $form_type;
    public $selected;
    public $next_step;
    public $next_step_text;
    public $user_types;
    public $memberships;
    public $link_type;

    /**
     * @var wpl_addon_franchise
     */
    public $fs;
    public $signup_method;
    public $current_site;

	public function display($instance = array())
	{
        /** Set tpl to NULL **/
        if(wpl_request::getVar('tpl') == 'default') wpl_request::setVar('tpl', NULL);
        
        $this->membership = new wpl_addon_membership();
        $this->target = wpl_request::getVar('wpltarget', NULL);
        $this->method = wpl_request::getVar('wplmethod', 'login');
        $this->redirect_to = urldecode(wpl_request::getVar('redirect_to', NULL));
        $this->wpl_security = new wpl_security();
        
        /** global settings **/
		$this->settings = wpl_global::get_settings();
        
		/** membership model **/
		$this->model = new wpl_users();

        if($this->method == 'login') $output = $this->login();
        elseif($this->method == 'dashboard') $output = $this->dashboard();
        elseif($this->method == 'profile') $output = $this->profile();
        elseif($this->method == 'changepassword') $output = $this->changepassword();
        elseif($this->method == 'lostpassword') $output = $this->lostpassword();
        elseif($this->method == 'resetpass') $output = $this->resetpass();
        elseif($this->method == 'register') $output = $this->register();
        elseif($this->method == 'usertypes') $output = $this->usertypes();
        elseif($this->method == 'memberships') $output = $this->memberships();
        elseif($this->method == 'subscription') $output = $this->subscription();
        elseif($this->method == 'change_membership') $output = $this->change_membership();
        elseif($this->method == 'searches') $output = $this->searches();
        elseif($this->method == 'favorites') $output = $this->favorites();
        elseif($this->method == 'brokerage') $output = $this->brokerage();
        elseif($this->method == 'signup') $output = $this->signup();
        else $output = $this->login();
        
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
    
    private function login()
    {
        $this->tpl = wpl_request::getVar('tpl', 'login');
        
        /** Set to 1 when WordPress calls our login page in a modal **/
        $this->interim_login = wpl_request::getVar('interim-login', 0);

        // Set the Redirect Page if Auto Redirection is enabled
        if(isset($this->settings['mem_auto_redirect']) and $this->settings['mem_auto_redirect'] and !trim($this->redirect_to))
        {
            $url = wpl_global::remove_qs_var('c', wpl_global::get_full_url());
            $login_url = $this->membership->URL('login');

            if($url != $login_url and strpos($url, 'https://') === strpos($login_url, 'https://')) $this->redirect_to = $url;
        }
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function dashboard()
    {
        $this->tpl = wpl_request::getVar('tpl', 'dashboard');
        
        /** user is not logged in **/
        if(!wpl_users::check_user_login())
        {
            $login = $this->membership->URL('login');
            $login = wpl_global::add_qs_var('redirect_to', urlencode(wpl_global::get_full_url()), $login);
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->user_id = wpl_users::get_cur_user_id();
        $this->user_data = (array) wpl_users::get_wpl_data($this->user_id);
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function profile()
    {
        $this->tpl = wpl_request::getVar('tpl', 'profile');
        
        /** user is not logged in **/
        if(!wpl_users::check_user_login())
        {
            $login = $this->membership->URL('login');
            $login = wpl_global::add_qs_var('redirect_to', urlencode(wpl_global::get_full_url()), $login);
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->user_id = wpl_users::get_cur_user_id();
        $this->user_data = (array) wpl_users::get_wpl_data($this->user_id);
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function changepassword()
    {
        $this->tpl = wpl_request::getVar('tpl', 'changepassword');
        
        /** user is not logged in **/
        if(!wpl_users::check_user_login())
        {
            $login = $this->membership->URL('login');
            $login = wpl_global::add_qs_var('redirect_to', urlencode(wpl_global::get_full_url()), $login);
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->user_id = wpl_users::get_cur_user_id();
        $this->user_data = (array) wpl_users::get_wpl_data($this->user_id);
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }

    private function lostpassword()
    {
        $this->tpl = wpl_request::getVar('tpl', 'lostpassword');
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function resetpass()
    {
        $this->tpl = wpl_request::getVar('tpl', 'resetpass');
        $this->key = wpl_request::getVar('key', NULL);
        
        if(!wpl_users::validate_activation_key($this->key))
        {
            /** import message tpl **/
			$this->message = __("Reset key is not correct!", 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
        }
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function register()
    {
        $this->tpl = wpl_request::getVar('tpl', 'register');
        
        if(!wpl_global::get_wp_option('users_can_register'))
        {
            /** import message tpl **/
			$this->message = __("Registration disabled!", 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->membership_id = wpl_request::getVar('membership', NULL);
        $this->user_type = wpl_request::getVar('type', NULL);
        $this->form_type = wpl_request::getVar('register_form_type', (isset($this->settings['mem_register_form_type']) ? $this->settings['mem_register_form_type'] : 'normal'));
        
        if(!trim($this->membership_id) and !trim($this->user_type))
        {
            $this->membership_id = -1;
            $this->membership_data = wpl_users::get_membership($this->membership_id);
            
            $this->user_type = $this->membership_data->membership_type;
            $this->user_type_data = wpl_users::get_user_type($this->user_type);
        }
        elseif(trim($this->user_type))
        {
            $this->user_type_data = wpl_users::get_user_type($this->user_type);
            
            $this->membership_id = $this->user_type_data->default_membership_id;
            $this->membership_data = wpl_users::get_membership($this->membership_id);
        }
        elseif(trim($this->membership_id))
        {
            $this->membership_data = wpl_users::get_membership($this->membership_id);
            
            $this->user_type = $this->membership_data->membership_type;
            $this->user_type_data = wpl_users::get_user_type($this->user_type);
        }
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function usertypes()
    {
        $this->tpl = wpl_request::getVar('tpl', 'usertypes');
        $this->selected = wpl_request::getVar('selected', NULL);
        $this->next_step = wpl_request::getVar('next_step', 'register');
        $this->next_step_text = wpl_request::getVar('next_step_text', 'Register');
        $this->user_types = wpl_users::get_user_types(1);
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function memberships()
    {
        $this->tpl = wpl_request::getVar('tpl', 'memberships');
        $this->selected = wpl_request::getVar('selected', NULL);
        
        $this->user_type = wpl_request::getVar('type', NULL);
        if($this->user_type) $this->user_type_data = wpl_users::get_user_type($this->user_type);
        
        $condition = " AND `maccess_show_in_register`='1'";
        if($this->user_type) $condition .= " AND `membership_type`='".$this->user_type."'";
        
        $this->memberships = wpl_users::get_wpl_memberships($condition);
        
        if(!count($this->memberships))
        {
            /** import message tpl **/
			$this->message = __("No membership found!", 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
        }
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function subscription()
    {
        wpl_request::setVar('next_step', 'memberships');
        wpl_request::setVar('next_step_text', wpl_request::getVar('next_step_text', 'Memberships'));
        
        /** Show User Types Page **/
        return $this->usertypes();
    }
    
    private function change_membership()
    {
        $this->tpl = wpl_request::getVar('tpl', 'change_membership');
        $this->selected = wpl_request::getVar('selected', NULL);
        
        $this->user_id = wpl_request::getVar('user_id', NULL);
        if(!$this->user_id) $this->user_id = wpl_users::get_cur_user_id();
        
        if(!$this->user_id)
        {
            /** import message tpl **/
			$this->message = __("Please login to your account first.", 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->link_type = wpl_request::getVar('link_type', 'link');
        
        /** WPL user data **/
        $this->user_data = wpl_users::get_wpl_data($this->user_id);
        $condition = NULL;
        
        if(isset($this->user_data->maccess_upgradable) and !trim($this->user_data->maccess_upgradable))
        {
            /** import message tpl **/
			$this->message = __("Your membership is not upgradable!", 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
        }
        
        if(isset($this->user_data->maccess_upgradable_to) and trim($this->user_data->maccess_upgradable_to) and trim($this->user_data->maccess_upgradable_to) != 1) $condition = " AND `id` IN(".$this->user_data->maccess_upgradable_to.")";
        $this->memberships = wpl_users::get_wpl_memberships($condition);
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function searches()
    {
        $this->tpl = wpl_request::getVar('tpl', 'addon_save_searches');
        
        /** user is not logged in **/
        if(!wpl_users::check_user_login())
        {
            $login = $this->membership->URL('login');
            $login = wpl_global::add_qs_var('redirect_to', urlencode(wpl_global::get_full_url()), $login);
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->user_id = wpl_users::get_cur_user_id();
        $this->user_data = (array) wpl_users::get_wpl_data($this->user_id);
        
        /** import tpl **/
        return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function favorites()
    {
        $this->tpl = wpl_request::getVar('tpl', 'favorites');
        
        /** user is not logged in **/
        if(!wpl_users::check_user_login())
        {
            $login = $this->membership->URL('login');
            $login = wpl_global::add_qs_var('redirect_to', urlencode(wpl_global::get_full_url()), $login);
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->user_id = wpl_users::get_cur_user_id();
        $this->user_data = (array) wpl_users::get_wpl_data($this->user_id);
        
        /** import tpl **/
        return parent::render($this->tpl_path, $this->tpl, false, true);
    }

    private function brokerage()
    {
        $this->tpl = wpl_request::getVar('tpl', 'addon_brokerage');

        // User is not logged in
        if(!wpl_users::check_user_login())
        {
            $login = $this->membership->URL('login');
            $login = wpl_global::add_qs_var('redirect_to', urlencode(wpl_global::get_full_url()), $login);

            // Import Message TPL
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }

        // No Access
        if(!wpl_users::is_broker())
        {
            // Import Message TPL
            $this->message = __("You don't have access to brokerage menu!", 'real-estate-listing-realtyna-wpl');
            return parent::render($this->tpl_path, 'message', false, true);
        }

        $this->user_id = wpl_users::get_cur_user_id();
        $this->user_data = (array) wpl_users::get_wpl_data($this->user_id);

        /** import tpl **/
        return parent::render($this->tpl_path, $this->tpl, false, true);
    }
    
    private function signup()
    {
        $this->tpl = wpl_request::getVar('tpl', 'signup');
        
        if(!wpl_global::check_addon('franchise'))
        {
            /** import message tpl **/
			$this->message = __('The WPL Franchise Add-on must be installed for this feature!', 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->fs = new wpl_addon_franchise();
        $this->signup_method = $this->fs->get_signup_method();
        
        if(in_array($this->signup_method, array('none', 'user')))
        {
            $type = wpl_global::get_setting('membership_register_link_type');
            return $this->$type();
        }
        
        /** if only blog creation allowed so users should login first **/
        if($this->signup_method == 'blog' and !wpl_users::check_user_login())
        {
            $redirect_to = urlencode(wpl_global::get_full_url());
			$login = wpl_users::wp_login_url($redirect_to);
            
            /** import message tpl **/
            $this->message = sprintf(__('You should %s first.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$login.'">'.__('login', 'real-estate-listing-realtyna-wpl').'</a>');
            return parent::render($this->tpl_path, 'message', false, true);
        }
        
        $this->current_site = get_current_site();
        
        /** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
    }
}