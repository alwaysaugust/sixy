
  <!-- Trigger/Open The Modal -->
<!--<button id="myBtn2">Register</button>-->

<!-- The Modal -->
<div id="myModal2" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close2">&times;</span>
     <h3 class="login-heading" style="margin-top: 21px;">Register</h3>
     <div id="wpl_membership_register_registration_container">
        <form type="post" action="" id="newUserForm">
    			
    		<p class="login-username">
    			<label class="user-login-label" for="wpl_membership_username">Username</label>
    			<input type="text" name="username" id="wpl_membership_username" class="input" value="" size="20" placeholder="Your username" required="">
    		</p>
    		<p class="login-password">
    			<label class="user-login-label" for="user_pass">Email</label>
    			<input type="text" name="email" id="wpl_membership_email" class="input" value="" size="20" placeholder="Your email address" required="">
    		</p>
    		<p class="login-password">
    			<label class="user-login-label" for="user_pass">Password</label>
    			<input type="password" name="password" id="wpl_membership_password" class="input" value="" size="20" placeholder="Your password" required="">
    		</p>
    		<p class="login-password">
    			<label class="user-login-label" for="wpl_membership_first_nam">First Name</label>
    			<input type="text" name="first_name" id="wpl_membership_first_nam" class="input" value="" size="20" placeholder="Your first name" required="">
    		</p>
    		<p class="login-password">
    			<label class="user-login-label" for="wpl_membership_last_name">Last Name</label>
    			<input type="text" name="last_name" id="wpl_membership_last_name" class="input" value="" size="20" placeholder="Your last name" required="">
    		</p>
    		
    		<p class="login-remember"><label><input name="" type="checkbox" id="" value="forever"> I agree with your</label></p>
    		<a class="terms-here" href="javascript:;">Terms & Conditions</a>
    		
    		<div class="wpl_membership_field_row">
                                                </div>
                        
            <div class="wpl-register-form-row wpl-recaptcha">
            <label for="wpl-register-message"></label>
                </div>
            <div class="wpl_membership_field_row">
             <input type="hidden" name="action" value="addUser"/>
            <button type="submit" class="button btn btn-primary" id="wpl_membership_register_buttons">Register</button> 
            </div>
    		
    		<p class="regist-area">Already have an account?&nbsp; <a class="register-here" href="/tosell/properties-3/v/members/?wplmethod=login">Login here.</a></p>
    		
    	</form>
    	</div>
    <div id="wpl_register_form_show_messages"></div>
    <div id="wpl_membership_register_checkout_container"></div>
  </div>
</div>
<style>
    button#wpl_membership_register_buttons {
        border-radius: 5px !important;
        background-color: #15161a !important;
        border-bottom: none;
    }
</style>
