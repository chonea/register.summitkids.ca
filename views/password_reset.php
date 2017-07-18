<?php include('login.header.php'); ?>
<div class="login-wrapper reset-wrapper">
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
<?php if ($login->passwordResetLinkIsValid() == true) { ?>
<form method="post" action="password_reset.php" name="new_password_form" class="login">
    <input type='hidden' name='user_name' value='<?php echo $_GET['user_name']; ?>' />
    <input type='hidden' name='user_password_reset_hash' value='<?php echo $_GET['verification_code']; ?>' />
<ul>
	<li>
    <label for="user_password_new"><?php echo WORDING_NEW_PASSWORD; ?></label>
    <input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" placeholder="<?php echo WORDING_NEW_PASSWORD; ?>" />
	</li>
	<li>
    <label for="user_password_repeat"><?php echo WORDING_NEW_PASSWORD_REPEAT; ?></label>
    <input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" placeholder="<?php echo WORDING_NEW_PASSWORD_REPEAT; ?>" />
	</li>
	<li>
		<button type="submit" name="submit_new_password" class="submit"><?php echo WORDING_SUBMIT_NEW_PASSWORD; ?></button>
	</li>
	</li>
	<li>
		<button type="button" onclick="javascript:parent.window.location.href='/'; return false;"><?php echo WORDING_BACK_TO_LOGIN; ?></button>
	</li>
</ul>
</form>
<!-- no data from a password-reset-mail has been provided, so we simply show the request-a-password-reset form -->
<?php } else { ?>
<form method="post" action="password_reset.php" name="password_reset_form" class="login">
<ul>
	<li>
    <label for="user_name"><?php echo WORDING_REQUEST_PASSWORD_RESET; ?></label>
    <input id="user_name" type="text" name="user_name" required placeholder="<?php echo WORDING_REQUEST_PASSWORD_RESET; ?>" />
	</li>
	<li>
		<button type="submit" name="request_password_reset" class="submit"><?php echo WORDING_RESET_PASSWORD; ?></button>
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
