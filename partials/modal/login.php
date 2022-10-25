  <!--<div class="modal-body">-->
  <!--   <?php wp_login_form(); ?>-->
  <!--</div>-->
  
  <!-- Trigger/Open The Modal -->
<!--<button id="myBtn">Login</button>-->
<!--<a href="javasrcipt:;" class="menu-button-login" id="myBtn">Login</a>-->

<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
     <h3 class="login-heading" style="margin-top: 21px;">Login</h3>
    <form name="loginform" id="loginform" action="/tosell/wp-login.php" method="post">
			
		<p class="login-username">
			<label class="user-login-label" for="user_login">Username or Email</label>
			<input type="text" name="log" id="user_login" class="input" value="" size="20" placeholder="Your username or email">
		</p>
		<p class="login-password">
			<label class="user-login-label" for="user_pass">Password</label>
			<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" placeholder="Your password">
		</p>
		
		<p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>
		<a class="forgot-here" id="" href="javascript:;">Forgot password</a> 
		<p class="login-submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Login" style="cursor: pointer;">
			<input type="hidden" name="redirect_to" value="/tosell/">
		</p>
		
		<p class="regist-area">Don't have an account?&nbsp; <a class="register-here" href="/tosell/properties-3/v/members/?wplmethod=signup">Register here.</a></p>
		
	</form>
  </div>

</div>