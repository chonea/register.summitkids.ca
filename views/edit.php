<?php include('header.php'); ?>

<!-- clean separation of HTML and PHP -->
<h3><?php echo WORDING_EDIT_YOUR_CREDENTIALS; ?></h3>
<?php /* ?>
<!-- edit form for username / this form uses HTML5 attributes, like "required" and type="email" -->
<form method="post" action="edit.php" name="user_edit_form_name">
    <label for="user_name"><?php echo WORDING_NEW_USERNAME; ?></label>
    <input id="user_name" type="text" name="user_name" pattern="[a-zA-Z0-9]{2,64}" required /> (<?php echo WORDING_CURRENTLY; ?>: <?php echo $_SESSION['user_name']; ?>)
    <input type="submit" name="user_edit_submit_name" value="<?php echo WORDING_CHANGE_USERNAME; ?>" />
</form><hr/>
<?php */ ?>

<!-- edit form for user first name and last name / this form uses HTML5 attributes, like "required" -->
<form method="post" action="edit.php" name="user_edit_form_firstlast_name">
    <label for="user_first_name"><?php echo WORDING_NEW_FIRST_NAME; ?></label>
    <input id="user_first_name" type="text" name="user_first_name" value="<?php echo $_SESSION['user_first_name']; ?>" required />
    <label for="user_last_name"><?php echo WORDING_NEW_LAST_NAME; ?></label>
    <input id="user_last_name" type="text" name="user_last_name" value="<?php echo $_SESSION['user_last_name']; ?>" required />
    <input type="submit" name="user_edit_submit_firstlast_name" value="<?php echo WORDING_CHANGE_FIRSTLAST_NAME; ?>" />
</form><hr/>

<!-- edit form for user email / this form uses HTML5 attributes, like "required" and type="email" -->
<form method="post" action="edit.php" name="user_edit_form_email">
    <label for="user_email"><?php echo WORDING_NEW_EMAIL; ?></label>
    <input id="user_email" type="email" name="user_email" value="<?php echo $_SESSION['user_email']; ?>" required />
    <input type="submit" name="user_edit_submit_email" value="<?php echo WORDING_CHANGE_EMAIL; ?>" />
</form><hr/>

<!-- edit form for user's password / this form uses the HTML5 attribute "required" -->
<form method="post" action="edit.php" name="user_edit_form_password">
    <label for="user_password_old"><?php echo WORDING_OLD_PASSWORD; ?></label>
    <input id="user_password_old" type="password" name="user_password_old" autocomplete="off" />

    <label for="user_password_new"><?php echo WORDING_NEW_PASSWORD; ?></label>
    <input id="user_password_new" type="password" name="user_password_new" autocomplete="off" />

    <label for="user_password_repeat"><?php echo WORDING_NEW_PASSWORD_REPEAT; ?></label>
    <input id="user_password_repeat" type="password" name="user_password_repeat" autocomplete="off" />

    <input type="submit" name="user_edit_submit_password" value="<?php echo WORDING_CHANGE_PASSWORD; ?>" />
</form><hr/>

<!-- backlink -->
<button onclick="window.location.href='/'"><?php echo WORDING_BACK_TO_LOGIN; ?></button>

<?php include('footer.php'); ?>
