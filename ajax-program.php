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
//echo "<pre>";
//print_r($_FILES);
//echo "</pre>";
//die();
if (isset($_POST['form'])) {

	if ($_POST['form'] == "program-form") {

		// Insert Program
		if ($_POST['programID'] == '') {
			$insert = array(
				"name" => $_POST["ProgramName"],
//				"abbreviation" => $_POST["ProgramAbbreviation"],
				"web_url" => $_POST["ProgramURL"],
				"description" => $_POST["ProgramDescription"],
//				"image" => $_FILES["Image"],
			);
//			if (isset($filehash)) {
//				$insert["image_hash"] = $filehash;
//			}
			$db->insert("program", $insert);
			$program_id = $db->lastInsertId();
			$returnJSON["programID"] = $program_id;

		// Update Program
		} else {
			$update = array(
				"name" => $_POST["ProgramName"],
//				"abbreviation" => $_POST["ProgramAbbreviation"],
				"web_url" => $_POST["ProgramURL"],
				"description" => $_POST["ProgramDescription"],
//				"image" => $_FILES["image"],
			);
			$where = "id = '".$_POST['programID']."'";
			$db->update("program", $update, $where);
//			$returnJSON["programID"] = $program_id;
//			if (isset($filehash)) {
//				$returnJSON['imageFile'] = $filehash;
//			} else {
//				$returnJSON['imageFile'] = '';
//			}
		}
	}
}

echo json_encode($returnJSON);
exit();
?>