<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.property');
_wpl_import('libraries.addon_save_searches');

class wpl_addon_save_searches_controller extends wpl_controller
{
    /**
     * @var wpl_addon_save_searches
     */
    public $save_searches;

    /**
     * @var wpl_security
     */
    public $wpl_security;
    public $token;

    public function display()
    {
        $function = wpl_request::getVar('wpl_function');
        
        $this->save_searches = new wpl_addon_save_searches();
        $this->wpl_security = new wpl_security();
        $this->token = wpl_request::getVar('token', NULL);
        
        if($function == 'login') $this->login();
        elseif($function == 'register') $this->register();
        elseif($function == 'save') $this->save();
        elseif($function == 'delete') $this->delete();
        elseif($function == 'alias') $this->alias();
        elseif($function == 'notify_mode') $this->notify_mode();
        elseif($function == 'edit_search_name') $this->edit_search_name();
    }
    
    private function login()
    {
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $vars = wpl_request::get('POST');
        
        $credentials = array();
        $credentials['user_login'] = isset($vars['username']) ? $vars['username'] : NULL;
        $credentials['user_password'] = isset($vars['password']) ? $vars['password'] : NULL;
        $credentials['remember'] = 0;
        
        $result = wpl_users::login_user($credentials);
        
        if(is_wp_error($result))
        {
            $success = 0;
            $code = $result->get_error_code();
            
            if($code == 'incorrect_password') $message = __('<strong>ERROR</strong>: The password you entered for the username is incorrect.', 'real-estate-listing-realtyna-wpl');
            elseif($code == 'invalid_username') $message = __('<strong>ERROR</strong>: Invalid username.', 'real-estate-listing-realtyna-wpl');
            else $message = $result->get_error_message();
            
            $data = array('token'=>$this->wpl_security->token());
        }
        else
        {
            $response = $this->save($vars, true);
            
            /** Update Save Search User **/
            $save_search_id = $response['data']['id'];
            $user_id = wpl_users::get_id_by_username($vars['username']);
            
            wpl_db::q("UPDATE `#__".$this->save_searches->table."` SET `user_id`='$user_id' WHERE `id`='$save_search_id'");
            
            $success = $response['success'];
            $message = $response['message'];
            $code = NULL;
            $data = $response['data'];
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function register()
    {
        $vars = wpl_request::get('POST');
        
        if(!wpl_global::get_wp_option('users_can_register')) $this->response(array('success'=>0, 'message'=>__('Registration disabled!', 'real-estate-listing-realtyna-wpl'), 'code'=>'registration_disabled', 'field_name'=>NULL, 'data'=>array()));
        
        $username = $vars['email'];
        $email = $vars['email'];
        $mobile = isset($vars['mobile']) ? $vars['mobile'] : '';
        $password = wpl_global::generate_password(8);
        
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array('token'=>$this->wpl_security->token())));
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->response(array('success'=>0, 'message'=>__('Invalid Email!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_email', 'field_name'=>'email', 'data'=>array('token'=>$this->wpl_security->token())));
        
        /** Checking existance of email **/
        if(wpl_users::email_exists($email)) $this->response(array('success'=>0, 'message'=>__('Email exists.', 'real-estate-listing-realtyna-wpl'), 'code'=>'email_exists', 'field_name'=>'email', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $result = wpl_users::insert_user(array('user_login'=>$username, 'user_email'=>$email, 'user_pass'=>$password));
        
        if(is_wp_error($result))
        {
            $success = 0;
            $code = $result->get_error_code();
            
            $message = $result->get_error_message();
            $data = array('token'=>$this->wpl_security->token());
        }
        else
        {
            $user_id = $result;
            
            // Trigger event for sending notification
            wpl_events::trigger('user_registered', array('password'=>$password, 'user_id'=>$user_id));
            
            // Change membership of user to default membership
            $default_membership_id = $this->save_searches->get_default_membership_id();
            wpl_users::change_membership($user_id, $default_membership_id);
            
            // Update User
            wpl_users::update('wpl_users', $user_id, 'mobile', $mobile);

            // Trigger event
            wpl_events::trigger('after_user_registered', array('id'=>$user_id));
            
            $response = $this->save($vars, true);
            
            // Update Save Search User
            $save_search_id = $response['data']['id'];
            wpl_db::q("UPDATE `#__".$this->save_searches->table."` SET `user_id`='$user_id' WHERE `id`='$save_search_id'");
            
            $success = $response['success'];
            $message = __('Your account and search saved successfully. Please check your email for password.', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            
            $data = $response['data'];
            $data['user_id'] = $user_id;
            $data['token'] = $this->wpl_security->token();
            $data['default_membership_id'] = $default_membership_id;
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function save($vars = NULL, $return = false)
    {
        /** $vars is not NULL when save function called in login or register functions **/
        if(is_null($vars))
        {
            if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
            $vars = wpl_request::get('GET');
        }
        
        $values = array();
        $values['name'] = isset($vars['wplname']) ? $vars['wplname'] : NULL;
        $values['alias'] = isset($vars['alias']) ? $vars['alias'] : NULL;
        $values['kind'] = isset($vars['kind']) ? $vars['kind'] : 0;
        $values['criteria'] = isset($vars['criteria']) ? json_decode(base64_decode($vars['criteria'])) : array();
        $values['url'] = isset($vars['url']) ? $vars['url'] : '';
        $values['user_id'] = wpl_users::get_cur_user_id();
        
        $id = $this->save_searches->save($values);
        
        $success = 1;
        $message = __('Your search saved successfully! We will notify you about new listings daily.', 'real-estate-listing-realtyna-wpl');
        $data = array('id'=>$id);
        
        $response = array('success'=>$success, 'message'=>$message, 'code'=>NULL, 'field_name'=>NULL, 'data'=>$data);
        
        if($return) return $response;
        else $this->response($response);

        return true;
    }
    
    private function delete()
    {
        $user_id = wpl_request::getVar('user_id', NULL);
        
        if(!is_null($user_id))
        {
            /** Normal user tries to remove another user saved searches **/
            if(!wpl_users::is_administrator() and $user_id != wpl_users::get_cur_user_id())
            {
                $message = __("You don't have access to this action!", 'real-estate-listing-realtyna-wpl');
                $this->response(array('success'=>0, 'message'=>$message, 'code'=>NULL, 'field_name'=>NULL, 'data'=>array()));
            }
            
            $this->save_searches->delete(NULL, $user_id);
            $message = __('All of your saved searches deleted successfully.', 'real-estate-listing-realtyna-wpl');
        }
        else
        {
            $id = wpl_request::getVar('id', NULL);
            
            $this->save_searches->delete($id);
            $message = __('Saved searches deleted successfully.', 'real-estate-listing-realtyna-wpl');
        }
        
        $success = 1;
        $data = array();
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>NULL, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function alias()
    {
        /** Normal user tries to save alias **/
        if(!wpl_users::is_administrator())
        {
            $message = __("You don't have access to this action!", 'real-estate-listing-realtyna-wpl');
            $this->response(array('success'=>0, 'message'=>$message, 'code'=>NULL, 'field_name'=>NULL, 'data'=>array()));
        }
            
        $id = wpl_request::getVar('id', NULL);
        $alias = wpl_global::url_encode(wpl_request::getVar('alias', NULL));
        
        $values = array();
        $values['alias'] = $alias;
        
        $this->save_searches->update($values, $id);
        
        $success = 1;
        $data = array('url'=>$this->save_searches->URL($id));
        $message = __('Alias updated successfully.', 'real-estate-listing-realtyna-wpl');
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>NULL, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function notify_mode()
    {
        $id = wpl_request::getVar('id');
        $notify_mode = wpl_request::getVar('mode');
		
        $this->save_searches->update(array('notify_mode'=>$notify_mode), $id);
    }

    private function edit_search_name()
    {
        $id = wpl_request::getVar('id');
        $name = wpl_request::getVar('search_name');

        $this->save_searches->update(array('name'=>$name), $id);
    }
}