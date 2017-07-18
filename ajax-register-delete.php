<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();
if (isset($_POST["registerID"]) && $_POST['registerID'] != '') {
	$delete_registerID = $_POST['registerID'];
	$query = "id = '".$delete_registerID."'";
	if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
		$query .= " AND user_id = '".$_SESSION['user_id']."'";
	}
	$results = $db->select("register", $query);
	if (!$delete_registration = $results[0]) {
		// Doesn't exist or it isn't this user's application
		die("Invalid registration ID or you are not authorized to view this information.");
		exit;
	}
	/*
	if ($results = $db->select("child", "id = '".$delete_registration['child_id']."'")) {
		$delete_child = $results[0];
		// Delete all contact information
		$db->delete("contact", "child_id = '".$delete_child['id']."'");
	}
	*/
	// Delete registration
	$db->delete("register", "id = '".$delete_registerID."'");
}

echo json_encode($returnJSON);
exit();
?>