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

function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

if (isset($_POST['form'])) {

	if ($_POST['form'] == "child-form-general" || $_POST['form'] == "register-form-general") {

		$date = DateTime::createFromFormat("m/d/Y", $_POST["ChildBirthDate"]);
		$child_birth_date = $date->format("Y-m-d");

		if ($_POST['childID'] == '') {
	
			// Insert child
			$insert = array(
				"user_id" => $_POST["userID"],
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["ChildFirstName"],
				"last_name" => $_POST["ChildLastName"],
				"birth_date" => date($child_birth_date),
				"gender" => $_POST["ChildGender"],
				"photo" => $_POST["ChildPhoto"]
			);
			if (isset($filehash)) {
				$insert["image_hash"] = $filehash;
			}
			$db->insert("child", $insert);
			$child_id = $db->lastInsertId();
	
			// Update registration
			if (isset($_POST['registerID']) && $_POST['registerID'] != '') {
				$update = array(
					"updated" => date("Y-m-d H:i:s"),
					"child_id" => $child_id,
					"page" => "general",
				);
				$where = "id = '".$_POST['registerID']."'";
				$db->update("register", $update, $where);
			}
				
		} else {
	
			// Update child
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["ChildFirstName"],
				"last_name" => $_POST["ChildLastName"],
				"birth_date" => date($child_birth_date),
				"gender" => $_POST["ChildGender"],
				"photo" => $_POST["ChildPhoto"]
			);
			$where = "id = '".$_POST['childID']."'";
			$db->update("child", $update, $where);
			$child_id = $_POST['childID'];
	
			// Update registration
			if (isset($_POST['registerID']) && $_POST['registerID'] != '') {
				$update = array(
					"updated" => date("Y-m-d H:i:s"),
					"page" => "general",
				);
				$where = "id = '".$_POST['registerID']."'";
				$db->update("register", $update, $where);
			}
	
		}
		$returnJSON["childID"] = $child_id;
		if (isset($filehash)) {
			$returnJSON['imageFile'] = $filehash;
		} else {
			$returnJSON['imageFile'] = '';
		}
	}
}

echo json_encode($returnJSON);
exit();
?>