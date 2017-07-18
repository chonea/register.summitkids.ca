<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();
if (isset($_POST["guardianID"]) && $_POST['guardianID'] != '') {
	$delete_guardianID = $_POST['guardianID'];
// temporary disable until add admin
//	$results = $db->select("guardian", "id = '".$delete_guardianID."' AND user_id = '".$_SESSION['user_id']."'");
	$results = $db->select("guardian", "id = '".$delete_guardianID."'");
	if ($delete_guardian = $results[0]) {
		// Delete all child relationships for this guardian
		$db->delete("relationship", "guardian_id = '".$delete_guardianID."'");
		// Delete guardian
		$db->delete("guardian", "id = '".$delete_guardianID."'");

	} else {
		// Doesn't exist or it isn't this user's guardian
		die("Invalid guardian ID or you are not authorized.");
		exit;
	}
}

echo json_encode($returnJSON);
exit();
?>