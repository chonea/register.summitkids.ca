<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();
if (isset($_POST["userID"]) && $_POST['userID'] != '') {
	$userID = $_POST['userID'];
	$query = "id = '".$userID."'";
	if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
		$query .= " AND user_id = '".$_SESSION['user_id']."'";
	}
	$results = $db->select("users", $query);
	$user = $results[0];

	/*
	Delete other stuff
	*/

	// Delete user
	$db->delete("users", "id = '".$userID."'");
}

echo json_encode($returnJSON);
exit();
?>