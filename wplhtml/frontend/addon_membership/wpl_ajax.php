<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.property');
_wpl_import('libraries.addon_membership');

class wpl_addon_membership_controller extends wpl_controller
{
    /**
     * @var wpl_addon_membership
     */
    public $membership;

    /**
     * @var wpl_security
     */
    public $wpl_security;
    public $target;
    public $token;

    public function display()
    {
        $function = wpl_request::getVar('wpl_function');

        $this->membership = new wpl_addon_membership();
        $this->wpl_security = new wpl_security();
        $this->target = wpl_request::getVar('wpltarget', NULL);
        $this->token = wpl_request::getVar('token', NULL);
        
        if($function == 'login') $this->login();
        elseif($function == 'logout') $this->logout();
        elseif($function == 'forgot') $this->forgot();
        elseif($function == 'resetpass') $this->resetpass();
        elseif($function == 'register') $this->register();
        elseif($function == 'renew_membership') $this->renew_membership();
        elseif($function == 'change_membership') $this->change_membership();
        elseif($function == 'change_password') $this->change_password();
        elseif($function == 'signup') $this->signup();
        elseif($function == 'login_linkedin') $this->login_linkedin();
    }
    
    private function login()
    {
		$login_gre_enabled = wpl_global::get_setting('gre_login');
		if($login_gre_enabled == 1)
		{
			// Check for Google Recaptca
			$gre = wpl_request::getVar('g-recaptcha-response');
			$gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_login');

			if($gre_response['success'] === 0)
			{
                $success = 0;
                $message = $gre_response['message'];
                $code = NULL;
                $data = array('token'=>$this->wpl_security->token());

                $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
			}
		}
        
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $vars = wpl_request::get('POST');
        
        $credentials = array();
        $credentials['user_login'] = isset($vars['username']) ? $vars['username'] : NULL;
        $credentials['user_password'] = isset($vars['password']) ? $vars['password'] : NULL;
        $credentials['remember'] = isset($vars['remember']) ? $vars['remember'] : 0;
        
        $result = wpl_users::login_user($credentials);
        
        if(is_wp_error($result))
        {
            $success = 0;
            $code = $result->get_error_code();
            
            if($code == 'incorrect_password') $message = __('<strong>ERROR</strong>: The password you entered for the username is incorrect.', 'real-estate-listing-realtyna-wpl');
            elseif($code == 'invalid_username') $message = __('<strong>ERROR</strong>: Invalid username.', 'real-estate-listing-realtyna-wpl');
            else $message = $result->get_error_message();
            
            $data = array('redirect_to'=>NULL, 'token'=>$this->wpl_security->token());
        }
        else
        {
            $parameters = array('user_id'=>$result->data->ID);
            wpl_events::trigger('user_login', $parameters);
            
            $redirect_to = urldecode(wpl_request::getVar('redirect_to', NULL));
            if(!trim($redirect_to)) $redirect_to = $this->membership->URL('dashboard', $this->target);
            
            $success = 1;
            $message = __('You logged in successfully!', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            $data = array('user_id'=>$result->data->ID, 'redirect_to'=>wpl_global::add_qs_var('c', mt_rand(1000, 9999), $redirect_to));
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function logout()
    {
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        wpl_users::wp_logout();
        
        $redirect_to = urldecode(wpl_request::getVar('redirect_to', NULL));
        if(!trim($redirect_to)) $redirect_to = $this->membership->URL('login', $this->target);
        
        $success = 1;
        $message = __('You logged out successfully!', 'real-estate-listing-realtyna-wpl');
        $data = array('redirect_to'=>wpl_global::add_qs_var('c', mt_rand(1000, 9999), $redirect_to));
        
        $this->response(array('success'=>$success, 'message'=>$message, 'data'=>$data));
    }
    
    private function forgot()
    {
		 // Check for Google Recaptca
		$forgot_gre_enabled = wpl_global::get_setting('gre_lostpassword');
		if($forgot_gre_enabled == 1)
		{
			$gre = wpl_request::getVar('g-recaptcha-response');
			$gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_lostpassword');
			if($gre_response['success'] === 0)
			{
			  $success = 0;
			  $message = $gre_response['message'];
			  $code = NULL;
			  $data = array('token'=>$this->wpl_security->token());
			  $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
			}	
		}
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $usermail = trim(wpl_request::getVar('usermail', NULL));
        
        if(strpos($usermail, '@')) $user_data = wpl_users::get_user_by('email', $usermail);
        else $user_data = wpl_users::get_user_by('login', $usermail);
        
        if(!$user_data) $this->response(array('success'=>0, 'message'=>__('<strong>ERROR</strong>: Invalid username or email.', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_username_or_email', 'field_name'=>'usermail', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $errors = new WP_Error();
        
        /**
        * Fires before errors are returned from a password reset request.
        */
        do_action('lostpassword_post');
        
        if($errors->get_error_code()) $this->response(array('success'=>0, 'message'=>$errors->get_error_message(), 'code'=>$errors->get_error_code(), 'field_name'=>'', 'data'=>array('token'=>$this->wpl_security->token())));
    
        $user_login = $user_data->user_login;
        
        $allow = apply_filters('allow_password_reset', true, $user_data->ID);
        if(!$allow or ($allow and is_wp_error($allow))) $this->response(array('success'=>0, 'message'=>__('<strong>ERROR</strong>: Password reset is not allowed for this user.', 'real-estate-listing-realtyna-wpl'), 'code'=>'reset_password_not_allowed', 'field_name'=>NULL, 'data'=>array('token'=>$this->wpl_security->token())));
        
        $key = wpl_global::generate_password(20, false);
        $hashed = wpl_global::wpl_hasher(8, $key);
        
        /** Set hashed key in database **/
        $query = "UPDATE `#__users` SET `user_activation_key`='$hashed' WHERE `user_login`='$user_login'";
        $result = wpl_db::q($query, 'UPDATE');
        
        /** Trigger event for sending notification **/
        $parameters = array('user_activation_key'=>$hashed, 'user_id'=>$user_data->ID);
        wpl_events::trigger('user_reset_password_request', $parameters);
        
        if(!$result)
        {
            $success = 0;
            $message = __('<strong>ERROR</strong>: Error Occurred.', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            $data = array('token'=>$this->wpl_security->token());
        }
        else
        {
            $success = 1;
            $message = __('An email was sent to you to complete the password reset.', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            $data = array();
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function resetpass()
    {
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $key = wpl_request::getVar('key');
        $user_id = wpl_users::validate_activation_key($key);
        
        if(!$user_id) $this->response(array('success'=>0, 'message'=>__("Reset key is not correct!", 'real-estate-listing-realtyna-wpl'), 'code'=>NULL,  'field_name'=>'key', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $password = urldecode(wpl_request::getVar('password', ''));
        $repeat_password = urldecode(wpl_request::getVar('repeat_password', ''));
        
        /** Password is short **/
        if(strlen($password) < 6) $this->response(array('success'=>0, 'message'=>__('Password should be more than 6 characters!', 'real-estate-listing-realtyna-wpl'), 'code'=>'short_password', 'field_name'=>'password', 'data'=>array('token'=>$this->wpl_security->token())));
        
        if($password != $repeat_password) $this->response(array('success'=>0, 'message'=>__("Passwords do not match!", 'real-estate-listing-realtyna-wpl'), 'code'=>NULL, 'field_name'=>'password', 'data'=>array('token'=>$this->wpl_security->token())));
        
        wpl_users::set_password($user_id, $password);
        wpl_users::update_user_option($user_id, 'default_password_nag', false, true);
        
        /** Empty key in database **/
        $query = "UPDATE `#__users` SET `user_activation_key`='' WHERE `ID`='$user_id'";
        wpl_db::q($query, 'UPDATE');
        
        $user_data = wpl_users::get_user($user_id);
        
        $credentials = array();
        $credentials['user_login'] = isset($user_data->data) ? $user_data->data->user_login : NULL;
        $credentials['user_password'] = $password;
        $credentials['remember'] = 0;
        
        wpl_users::login_user($credentials);
        
        /** Trigger event for sending notification **/
        $parameters = array('user_id'=>$user_id, 'password'=>$password);
        wpl_events::trigger('user_password_reset', $parameters);
        
        $redirect_to = urldecode(wpl_request::getVar('redirect_to', NULL));
        if(!trim($redirect_to)) $redirect_to = $this->membership->URL('dashboard', $this->target);
        
        $success = 1;
        $message = __('Password reset successful. You have logged in automatically!', 'real-estate-listing-realtyna-wpl');
        $code = NULL;
        $data = array('user_id'=>$user_id, 'redirect_to'=>wpl_global::add_qs_var('c', mt_rand(1000, 9999), $redirect_to));
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function register()
    {
        $vars = wpl_request::get('POST');
        
        if(!wpl_global::get_wp_option('users_can_register')) $this->response(array('success'=>0, 'message'=>__('Registration disabled!', 'real-estate-listing-realtyna-wpl'), 'code'=>'registration_disabled', 'field_name'=>NULL, 'data'=>array()));

        $register_gre_enabled = wpl_global::get_setting('gre_register');
        if($register_gre_enabled == 1)
        {
            // Check for Google Recaptca
            $gre = wpl_request::getVar('g-recaptcha-response');
            $gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_register');

            if($gre_response['success'] === 0) $this->response(array('success'=>0, 'message'=>$gre_response['message'], 'code'=>NULL, 'field_name'=>NULL, 'data'=>array('token'=>$this->wpl_security->token())));
        }
        
        $username = $vars['username'];
        $email = $vars['email'];
        $membership_id = $vars['membership_id'];
        $user_type = $vars['user_type'];
        
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array('token'=>$this->wpl_security->token())));
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->response(array('success'=>0, 'message'=>__('Invalid Email!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_email', 'field_name'=>'email', 'data'=>array('token'=>$this->wpl_security->token())));
        if(!preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $username)) $this->response(array('success'=>0, 'message'=>__('Invalid Username!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_username', 'field_name'=>'username', 'data'=>array('token'=>$this->wpl_security->token())));
        
        if(wpl_users::username_exists($username)) $this->response(array('success'=>0, 'message'=>__('Username already exists.', 'real-estate-listing-realtyna-wpl'), 'code'=>'username_exists', 'field_name'=>'username', 'data'=>array('token'=>$this->wpl_security->token())));
        elseif(wpl_users::email_exists($email)) $this->response(array('success'=>0, 'message'=>__('Email exists.', 'real-estate-listing-realtyna-wpl'), 'code'=>'email_exists', 'field_name'=>'email', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $errors = new WP_Error();
        
        /**
        * Fires before errors are returned from a register request.
        */
        do_action('register_post', $username, $email, $errors);
        
        if($errors->get_error_code()) $this->response(array('success'=>0, 'message'=>$errors->get_error_message(), 'code'=>$errors->get_error_code(), 'field_name'=>'', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $membership_data = wpl_users::get_membership($membership_id);
        $user_type_data = wpl_users::get_user_type($user_type);
        
        $password = wpl_global::generate_password(8);
        $first_name = isset($vars['first_name']) ? $vars['first_name'] : '';
        $last_name = isset($vars['last_name']) ? $vars['last_name'] : '';
        $mobile = isset($vars['mobile']) ? $vars['mobile'] : '';
        
        $result = wpl_users::insert_user(array('user_login'=>$username, 'user_email'=>$email, 'user_pass'=>$password, 'first_name'=>$first_name, 'last_name'=>$last_name));
        
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
            wpl_events::trigger('user_registered', array('password'=>$password, 'user_id'=>$user_id, 'mobile'=>$mobile));
            
            // If default membership is paid don't assign user to that membership
            $default_membership_id = !trim($membership_data->maccess_price) ? $membership_id : $user_type_data->default_membership_id;
            
            $default_membership_data = wpl_users::get_membership($default_membership_id);
            if(trim($default_membership_data->maccess_price)) $default_membership_id = -1;
            
            // change membership of user to default membership
            wpl_users::change_membership($user_id, $default_membership_id);
            
            // Update User
            wpl_users::update('wpl_users', $user_id, 'mobile', $mobile);

            // Trigger event
            wpl_events::trigger('after_user_registered', array('id'=>$user_id));
            
            $success = 1;
            $message = __('User registered. Please check your email for password.', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            $data = array('user_id'=>$user_id, 'token'=>$this->wpl_security->token(), 'default_membership_id'=>$default_membership_id);
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function renew_membership()
    {
        $user_id = wpl_request::getVar('user_id', 0);
        $user_data = wpl_users::get_user($user_id);
        $user_wpl_data = $user_data->data->wpl_data;
        $membership_id = $user_data->data->wpl_data->membership_id;
        $membership_data = wpl_users::get_membership($membership_id);
        
        /** Not renewable **/
        if(!$user_wpl_data->maccess_renewable) $this->response(array('success'=>0, 'message'=>sprintf(__('%s membership is not renewable!', 'real-estate-listing-realtyna-wpl'), $membership_data->membership_name), 'code'=>'membership_not_renewable', 'field_name'=>NULL, 'data'=>array()));
        
        /** Free membership **/
        if(!$user_wpl_data->maccess_renewal_price)
        {
            $this->membership->renew($user_id);
            $this->response(array('success'=>1, 'message'=>__('Renewed successfully.', 'real-estate-listing-realtyna-wpl'), 'data'=>array('free'=>1)));
        }
        
        wpl_request::setVar('amount', $user_wpl_data->maccess_renewal_price);
        wpl_request::setVar('unit_id', $user_wpl_data->maccess_renewal_price_unit);
        wpl_request::setVar('title', sprintf(__('Renew %s Membership', 'real-estate-listing-realtyna-wpl'), $membership_data->membership_name));
        wpl_request::setVar('mode', 0);
        wpl_request::setVar('status', 2);
        
        $transaction_id = $this->insert_transaction(true);
        $transactions = new wpl_transactions($transaction_id);
        
        $system = array
        (
            'callbacks' => array
            (
                0 => array
                (
                    'class_path'=>'libraries.addon_membership',
                    'class_name'=>'wpl_addon_membership',
                    'function_name'=>'renew',
                    'function_params'=>array($user_id)
                )
            ),
            'javascript' => array
            (
                'success'=>array('wpl_renew_membership_payment_success(response);'),
                'error'=>array('wpl_renew_membership_payment_error(response);')
            )
        );
        
        $transactions->update(array('system'=>json_encode($system)));
        
        $res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = array('transaction_id'=>$transaction_id);
        
        $this->response(array('success'=>$res, 'message'=>$message, 'data'=>$data));
    }
    
    private function insert_transaction($return = false)
    {
        $transactions = new wpl_transactions(0);
        
        $values = array();
        $values['user_id'] = wpl_request::getVar('user_id', wpl_users::get_cur_user_id());
        $values['mode'] = wpl_request::getVar('mode', 0);
        $values['type'] = wpl_request::getVar('type', '');
        
        $amount = wpl_request::getVar('amount', 0);
        $unit_id = wpl_request::getVar('unit_id', 260);
        
        $values['amount'] = $amount;
        $values['unit_id'] = $unit_id;
        $values['amount_si'] = wpl_units::convert($amount, $unit_id, 260);
        
        $values['status'] = 2;
        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['title'] = wpl_request::getVar('title', NULL);
        $values['description'] = wpl_request::getVar('description', NULL);
        $values['params'] = wpl_request::getVar('params', array());
        $values['recurring'] = wpl_request::getVar('recurring', 0);
        $values['recurring_days'] = wpl_request::getVar('recurring_days', 0);
        
        $id = $transactions->create($values);
        
        /** return **/
        if($return) return $id;
        
        $res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = array('transaction_id'=>$id);
        
        $this->response(array('success'=>$res, 'message'=>$message, 'data'=>$data));
        return true;
    }
    
    private function change_membership()
    {
        $user_id = wpl_request::getVar('user_id', 0);
        $user_data = wpl_users::get_user($user_id);
        $user_wpl_data = $user_data->data->wpl_data;
        
        $membership_id = wpl_request::getVar('membership_id', 0);
        $membership_data = wpl_users::get_membership($membership_id);
        
        $is_register = wpl_request::getVar('is_register', 0);
        
        /** Not Upgradable **/
        if(!$is_register and !$user_wpl_data->maccess_upgradable) $this->response(array('success'=>0, 'message'=>__('Your membership is not upgradable!', 'real-estate-listing-realtyna-wpl'), 'code'=>'membership_not_upgradable', 'field_name'=>NULL, 'data'=>array()));
        
        /** Free Membership **/
        if(!$membership_data->maccess_price)
        {
            wpl_users::change_membership($user_id, $membership_id);
            $this->response(array('success'=>1, 'message'=>__('Upgraded successfully.', 'real-estate-listing-realtyna-wpl'), 'data'=>array('free'=>1)));
        }
        
        wpl_request::setVar('amount', $membership_data->maccess_price);
        wpl_request::setVar('unit_id', $membership_data->maccess_price_unit);
        wpl_request::setVar('title', sprintf(__('Change Membership to %s', 'real-estate-listing-realtyna-wpl'), $membership_data->membership_name));
        wpl_request::setVar('mode', 0);
        wpl_request::setVar('status', 2);
        wpl_request::setVar('recurring', $membership_data->maccess_recurring);
        wpl_request::setVar('recurring_days', $membership_data->maccess_period);
        
        $transaction_id = $this->insert_transaction(true);
        $transactions = new wpl_transactions($transaction_id);
        
        $system = array
        (
            'callbacks' => array
            (
                0 => array
                (
                    'class_path'=>'libraries.users',
                    'class_name'=>'wpl_users',
                    'function_name'=>'change_or_renew_membership',
                    'function_params'=>array($user_id, $membership_id)
                )
            ),
            'javascript' => array
            (
                'success'=>array('wpl_change_membership_payment_success(response);'),
                'error'=>array('wpl_change_membership_payment_error(response);')
            )
        );
        
        $transactions->update(array('system'=>json_encode($system)));
        
        $res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = array('transaction_id'=>$transaction_id);
        
        $this->response(array('success'=>$res, 'message'=>$message, 'data'=>$data));
    }
    
    private function change_password()
    {
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $new_password = wpl_request::getVar('new_password', '');
        $new_password_repeat = wpl_request::getVar('new_password_repeat', '');
        
        /** Password is hort **/
        if(strlen($new_password) < 6) $this->response(array('success'=>0, 'message'=>__('Password should be more than 6 characters!', 'real-estate-listing-realtyna-wpl'), 'code'=>'short_password', 'field_name'=>'new_password', 'data'=>array('token'=>$this->wpl_security->token())));
        
        /** password and password repeat are not matched **/
        if($new_password != $new_password_repeat) $this->response(array('success'=>0, 'message'=>__('Password and password repeat are not matched!', 'real-estate-listing-realtyna-wpl'), 'code'=>'not_matched_passwords', 'field_name'=>'new_password', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $user_id = wpl_users::get_cur_user_id();
        
        /** Invalid User **/
        if(!$user_id) $this->response(array('success'=>0, 'message'=>__('User is Invalid.', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_user', 'field_name'=>NULL, 'data'=>array('token'=>$this->wpl_security->token())));
        
        /** Set password **/
        wpl_users::wp_set_password($new_password, $user_id);
        
        $success = 1;
        $message = __('Password changed successfully!', 'real-estate-listing-realtyna-wpl');
        $data = array();
        
        $this->response(array('success'=>$success, 'message'=>$message, 'data'=>$data));
    }
    
    private function signup()
    {
        $vars = wpl_request::get('POST');
        
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $signup_for = isset($vars['signup_for']) ? $vars['signup_for'] : 'blog';
        
        $site_creation = true;
        if($signup_for == 'user') $site_creation = false;
        
        $user_id = wpl_users::get_cur_user_id();
        
        // User is logged in so create a blog only
        if($user_id and $site_creation)
        {
            $user = wp_get_current_user();
            $results = wpmu_validate_blog_signup($vars['blogname'], $vars['blog_title'], $user);
            
            // Extracted values
            $domain = $results['domain'];
            $path = $results['path'];
            $blog_title = $results['blog_title'];
            $errors = $results['errors'];

            if($errors->get_error_code()) $this->response(array('success'=>0, 'message'=>$errors->get_error_message(), 'code'=>'blog_not_validated', 'field_name'=>'', 'data'=>array('token'=>$this->wpl_security->token())));
            
            $public = (int) $vars['blog_public'];
            $blog_meta_defaults = array('lang_id'=>1, 'public'=>$public);
            
            $meta_defaults = apply_filters('signup_create_blog_meta', $blog_meta_defaults);
            $meta = apply_filters('add_signup_meta', $meta_defaults);
            
            $blog_id = wpmu_create_blog($domain, $path, $blog_title, $user_id, $meta, wpl_global::get_current_network_site_id());
            $blog_url = "http".(wpl_request::getVar('HTTPS', 'off', 'SERVER') == 'on' ? 's' : '')."://".$domain.$path;
            
            $success = 1;
            $message = sprintf(__('%s is your new site. %s as %s using your existing password.', 'real-estate-listing-realtyna-wpl'), '<a href="'.$blog_url.'">'.$blog_title.'</a>', '<a href="'.$blog_url.'wp-login.php">'.__('Login', 'real-estate-listing-realtyna-wpl').'</a>', $user->login_name);
            $code = 'done';
            $data = array('blog_id'=>$blog_id);
        }
        // User is not logged in so we should create both of blog and user
        else
        {
            $success = 0;
            $message = '';
            $code = 'done';
            $data = array();

            if($signup_for == 'user')
            {
                $results = wpmu_validate_user_signup($vars['user_name'], $vars['user_email']);
                
                $user_name = $results['user_name'];
                $user_email = $results['user_email'];
                $errors = $results['errors'];

                if($errors->get_error_code()) $this->response(array('success'=>0, 'message'=>$errors->get_error_message(), 'code'=>'user_not_validated', 'field_name'=>'', 'data'=>array('token'=>$this->wpl_security->token())));

                /** This filter is documented in wp-signup.php */
                wpmu_signup_user($user_name, $user_email, apply_filters('add_signup_meta', array()));
                
                $success = 1;
                $message = sprintf(__('<strong>%s</strong> is your username. But, before you can start using your new username, <strong>you must activate it</strong>. <p>Check your inbox at <strong>%s</strong> and click the link given.</p>', 'real-estate-listing-realtyna-wpl'), $user_name, $user_email);
                $code = 'done';
                $data = array();
            }
            elseif($signup_for == 'blog')
            {
                $results = wpmu_validate_user_signup($vars['user_name'], $vars['user_email']);
                
                $user_name = $results['user_name'];
                $user_email = $results['user_email'];
                $errors = $results['errors'];

                if($errors->get_error_code()) $this->response(array('success'=>0, 'message'=>$errors->get_error_message(), 'code'=>'user_not_validated', 'field_name'=>'', 'data'=>array('token'=>$this->wpl_security->token())));

                $results = wpmu_validate_blog_signup($vars['blogname'], $vars['blog_title']);
                
                $domain = $results['domain'];
                $path = $results['path'];
                $blog_title = $results['blog_title'];
                $errors = $results['errors'];

                if($errors->get_error_code()) $this->response(array('success'=>0, 'message'=>$errors->get_error_message(), 'code'=>'blog_not_validated', 'field_name'=>'', 'data'=>array('token'=>$this->wpl_security->token())));

                $public = (int) $vars['blog_public'];
                $signup_meta = array('lang_id'=>1, 'public'=>$public);

                /** This filter is documented in wp-signup.php */
                $meta = apply_filters('add_signup_meta', $signup_meta);
                wpmu_signup_blog($domain, $path, $blog_title, $user_name, $user_email, $meta);
                
                $blog_url = "http".(wpl_request::getVar('HTTPS', 'off', 'SERVER') == 'on' ? 's' : '')."://".$domain.$path;
                
                $success = 1;
                $message = sprintf(__('Congratulations! Your new site, %s, is almost ready. But, before you can start using your site, <strong>you must activate it</strong>. <p>Check your inbox at <strong>%s</strong> and click the link given. If you do not activate your site within two days, you will have to sign up again.</p>', 'real-estate-listing-realtyna-wpl'), '<a href="'.$blog_url.'">'.$blog_title.'</a>', $user_email);
                $code = 'done';
                $data = array();
            }
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }

    private function login_linkedin()
    {
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $vars = wpl_request::get('POST');
        $result = wpl_addon_membership::login_linkedin_user($vars['linkedin_uid'], $vars['linkedin_email'], $vars['linkedin_name']);

        $redirect_to = urldecode(wpl_request::getVar('redirect_to', NULL));
        if(!trim($redirect_to)) $redirect_to = $this->membership->URL('dashboard', $this->target);
        
        $success = $result['success'];
        $message = $success ? __('You logged in successfully!', 'real-estate-listing-realtyna-wpl') : __('An error occurred! Please try again later.', 'real-estate-listing-realtyna-wpl');
        $data = array('user_id'=>$result['id'], 'redirect_to'=>wpl_global::add_qs_var('c', mt_rand(1000, 9999), $redirect_to));
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>NULL, 'field_name'=>NULL, 'data'=>$data));
    }
}