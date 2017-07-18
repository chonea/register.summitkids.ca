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

if (isset($_POST['form'])) {

	if ($_POST['form'] == "activity-form") {

		// Insert Activity
		if ($_POST['activityID'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"name" => $_POST["ActivityName"],
				"program_id" => $_POST["ActivityProgramID"],
				"description" => $_POST["ActivityDescription"],
			);
			$db->insert("activity", $insert);
			$activity_id = $db->lastInsertId();
			$returnJSON["activityID"] = $activity_id;

		// Update Activity
		} else {
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"name" => $_POST["ActivityName"],
				"program_id" => $_POST["ActivityProgramID"],
				"description" => $_POST["ActivityDescription"],
			);
			/*
			if ($_POST["ActivityDisabled"] == 'yes') {
				$update['disabled'] = date("Y-m-d H:i:s");
			} else {
				$update['disabled'] = '';
			}
			if ($_POST["ActivityDeleted"] == 'yes') {
				$update['deleted'] = date("Y-m-d H:i:s");
			} else {
				$update['deleted'] = '';
			}
			*/
			$where = "id = '".$_POST['activityID']."'";
			$db->update("activity", $update, $where);
		}
	}
}

echo json_encode($returnJSON);
exit();
?>