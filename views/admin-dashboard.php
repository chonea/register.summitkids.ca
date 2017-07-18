<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

//set taxonomy
include("config/register.config.php");

// boot the non-admins
if ($_SESSION['user_role'] != "admin") {
	header("Location: /");
	exit();
}

include('header.php');
//include("sidebar.php");
?>
<?php
/*
// if you need the user's information, just put them into the $_SESSION variable and output them here
echo '<h2>' . WORDING_WELCOME . ', ' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']  . ".</h2>";
echo WORDING_YOU_ARE_LOGGED_IN_AS . $_SESSION['user_name'] . "<br />";
//echo WORDING_PROFILE_PICTURE . '<br/><img src="' . $login->user_gravatar_image_url . '" />;
echo WORDING_PROFILE_PICTURE . '<br/>' . $login->user_gravatar_image_tag;
?>

<div>
    <a href="index.php?logout"><?php echo WORDING_LOGOUT; ?></a>
    <a href="edit.php"><?php echo WORDING_EDIT_USER_DATA; ?></a>
    <a href="register.php"><?php echo WORDING_ENROLL; ?></a>
</div>
<? */ ?>

<div class="row-fluid" style="margin: 0 auto 30px;">
	<div class="page-header">
		<h2>Dashboard</h2>
		<h3><?php echo date('l, jS \of F, Y, h:i A'); ?></h3>
	</div>
</div>

<?php include('footer.php'); ?>
