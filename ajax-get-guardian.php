<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();
$returnJSON['success'] = true;
define("MAX_SIZE","400");

if (isset($_REQUEST['guardianID'])) {
	if ($results = $db->select("guardian", "id = '".$_REQUEST['guardianID']."'")) {
		$guardian = $results[0];
		if (isset($_REQUEST['childID'])) {
			if ($relation = $db->select("relationship", "guardian_id = '".$_REQUEST['guardianID']."' AND child_id = '".$_REQUEST['childID']."'")) {
				$guardian['relationship'] = stripslashes($relation[0]['relationship']);
				$guardian['lives_with'] = $relation[0]['lives_with'];
			}
		}
		$returnJSON['guardian'] = $guardian;
	}
}
echo json_encode($returnJSON);
exit();
?>