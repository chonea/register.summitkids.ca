<?php include('login.header.php'); ?>
<div class="login-wrapper signup-wrapper">
<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
	if ($login->errors) {
		foreach ($login->errors as $error) {
			echo "<p>".$error."</p>";
		}
	}
	if ($login->messages) {
		foreach ($login->messages as $message) {
			echo "<p>".$message."</p>";
		}
	}
}
?>
<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
	if ($registration->errors) {
		foreach ($registration->errors as $error) {
			echo "<p>".$error."</p>";
		}
	}
	if ($registration->messages) {
		foreach ($registration->messages as $message) {
			echo "<p>".$error."</p>";
		}
	}
}
?>

<!-- show registration form, but only if we didn't submit already -->
<?php if (!$registration->registration_successful && !$registration->verification_successful) { ?>
<h2><?php echo WORDING_REGISTER_NEW_ACCOUNT; ?></h2>
<form method="post" action="signup.php" name="registerform" class="login">
<?php /* ?>
    <label for="user_name"><?php echo WORDING_REGISTRATION_USERNAME; ?></label>
    <input id="user_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
<?php */ ?>
<ul>
	<li>
    <label for="user_first_name"><?php echo WORDING_REGISTRATION_FIRST_NAME; ?></label>
    <input id="user_first_name" type="text" name="user_first_name" required placeholder="<?php echo WORDING_REGISTRATION_FIRST_NAME; ?>" />
	</li>
	<li>
    <label for="user_last_name"><?php echo WORDING_REGISTRATION_LAST_NAME; ?></label>
    <input id="user_last_name" type="text" name="user_last_name" required placeholder="<?php echo WORDING_REGISTRATION_LAST_NAME; ?>" />
	</li>
	<li>
    <label for="user_email"><?php echo WORDING_REGISTRATION_EMAIL; ?></label>
    <input id="user_email" type="email" name="user_email" required placeholder="<?php echo WORDING_REGISTRATION_EMAIL; ?>" />
	</li>
	<li>
    <label for="user_password_new"><?php echo WORDING_REGISTRATION_PASSWORD; ?></label>
    <input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" placeholder="<?php echo WORDING_REGISTRATION_PASSWORD; ?>" />
	</li>
	<li>
    <label for="user_password_repeat"><?php echo WORDING_REGISTRATION_PASSWORD_REPEAT; ?></label>
    <input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" placeholder="<?php echo WORDING_REGISTRATION_PASSWORD_REPEAT; ?>" />
	</li>
	<li>
    <img src="tools/showCaptcha.php" alt="captcha" /><br>

    <label><?php echo WORDING_REGISTRATION_CAPTCHA; ?></label>
    <input type="text" name="captcha" required placeholder="<?php echo WORDING_REGISTRATION_CAPTCHA; ?>" />
	</li>
	<li>
		<button type="submit" name="register" class="submit"><?php echo WORDING_REGISTER; ?></button>
	</li>
	<li>
		<button type="button" onclick="javascript:parent.window.location.href='/'"><?php echo WORDING_BACK_TO_LOGIN; ?></button>
	</li>
</ul>
</form>
<?php } elseif ($registration->registration_successful) { ?>
<form class="login">
<ul>
	<li>
	<p>Thank you for registering!</p><p>A verification email has been sent to your email address.  You will need to verify your email before logging into the system.</p>
	</li>
	<li>
		<button type="button" onclick="javascript:parent.window.location.href='/'; return false;"><?php echo WORDING_BACK_TO_LOGIN; ?></button>
	</li>
</ul>
</form>
<?php } elseif ($registration->verification_successful) { ?>
<form class="login">
<ul>
	<li>
	<p>Your email has been verified!</p><p>You may now log into the system.</p>
	</li>
	<li>
		<button type="button" onclick="javascript:parent.window.location.href='/'; return false;"><?php echo WORDING_BACK_TO_LOGIN; ?></button>
	</li>
</ul>
</form>
<?php } ?>
	<div class="page-footer">
		<p style="text-align: center;">Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.summitkids.ca" target="_blank">Summit Kids</a><?php /* | <a href="privacy.php">Privacy Policy</a><span id="dev-text"></span> */ ?></p>
	</div>
</div>
<?php include('login.footer.php'); ?>
