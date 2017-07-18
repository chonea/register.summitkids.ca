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

<div class="row-fluid">
	<div class="page-header">
		<h2>Privacy Policy</h2>
	</div>
	<p>All personal and medical information collected by Summit Kids in the Registration Package becomes part of the child's record. It is considered to be confidential and is protected by our Confidentiality Policy. We will not share this information without written parent concent. Email addresses provided by parents will be added to our confidential email list and will be used to enhance communication with parents in our program.</p>
</div>
<?php include('footer.php'); ?>