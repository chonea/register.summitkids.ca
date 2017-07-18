<?php include('login.header.php'); ?>
<div class="login-wrapper">
<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
	if ($login->errors) {
		foreach ($login->errors as $error) {
			echo '<p class="login-error">'.$error.'</p>';
		}
	}
	if ($login->messages) {
		foreach ($login->messages as $message) {
			echo '<p class="login-message">'.$message.'</p>';
		}
	}
}
?>
<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
	if ($registration->errors) {
		foreach ($registration->errors as $error) {
			echo '<p class="login-error">'.$error.'</p>';
		}
	}
	if ($registration->messages) {
		foreach ($registration->messages as $message) {
			echo '<p class="login-message">'.$message.'</p>';
		}
	}
}
?>
<form method="post" action="index.php" name="loginform" class="login">
<ul>
	<li>
		<a href="signup.php" class="fancybox-disabled" data-fancybox-type="iframe" tabindex=4><?php echo WORDING_REGISTER_NEW_ACCOUNT; ?></a>
		<label for="user_name">Email Address</label>
		<input id="user_name" type="text" name="user_name" placeholder="Email Address" required tabindex=1>
	</li>
	<li>
		<a href="password_reset.php" class="fancybox-disabled" data-fancybox-type="iframe" tabindex=5><?php echo WORDING_FORGOT_MY_PASSWORD; ?></a>
		<label for="user_password">Password</label>
		<input id="user_password" type="password" name="user_password" placeholder="Password" autocomplete="off" required tabindex=2>
	</li>
	<li>
		<button type="submit" name="login" tabindex=3 class="submit">Login</button>
	</li>
</ul>
</form>
	<div class="page-footer">
		<p style="text-align: center;">Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.summitkids.ca" target="_blank">Summit Kids</a><?php /* | <a href="privacy.php">Privacy Policy</a><span id="dev-text"></span> */ ?></p>
	</div>
</div>
<?php include('login.footer.php'); ?>
