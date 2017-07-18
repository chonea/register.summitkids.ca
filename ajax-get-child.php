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

if (isset($_REQUEST['childID'])) {
	if ($results = $db->select("child", "id = '".$_REQUEST['childID']."'")) {
		$child = $results[0];
		$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($child['birth_date'])));
		$datetime = $date->format('m/d/Y');
		$child['birth_date'] = $datetime;
		$returnJSON['child'] = $child;
	}
	$returnJSON['guardians'] = array();
	$relationships = $db->select("relationship", "child_id = '".$_REQUEST['childID']."'");
	foreach ($relationships as $relationship) {
		$guardians = $db->select("guardian", "id = '".$relationship['guardian_id']."'");
		$guardian = $guardians[0];
		$guardian['relationship'] = stripslashes($relationship['relationship']);
		$guardian['lives_with'] = $relationship['lives_with'];
		$returnJSON['guardians'][] = $guardian;
	}
	$returnJSON['contacts'] = $db->select("contact", "child_id = '".$_REQUEST['childID']."'");
}
echo json_encode($returnJSON);
exit();
?>