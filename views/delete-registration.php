<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

if (isset($_REQUEST["registerID"])) {
	$delete_registerID = $_REQUEST["registerID"];
	$results = $db->select("register", "id = '".$delete_registerID."' AND user_id = '".$_SESSION['user_id']."'");
	if (!$delete_registration = $results[0]) {
		// Doesn't exist or it isn't this user's application
		echo "<p>Invalid registration ID or you are not authorized to view this information.</p>";
		exit;
	}
	if ($results = $db->select("child", "id = '".$delete_registration['child_id']."'")) {
		$delete_child = $results[0];
		// Delete all contact information
		$db->delete("contact", "child_id = '".$delete_child['id']."'");
	}
	// Delete all program sessions
	$db->delete("session", "register_id = '".$delete_registerID."'");
	// Delete registration
	$db->delete("register", "id = '".$delete_registerID."'");
	echo "<p>Registration deleted.</p>";
}
?>