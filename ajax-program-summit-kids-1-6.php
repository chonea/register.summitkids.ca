<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();

if (isset($_POST['form'])) {
	if ($_POST['registerID'] == '') {
		$insert = array(
			"created" => date("Y-m-d H:i:s"),
			"updated" => date("Y-m-d H:i:s"),
			"user_id" => $_POST["userID"],
			"program" => "summit-kids-1-6",
			"location" => $_POST["Location"],
			"grade" => $_POST["ChildGrade"],
			"type" => $_POST["MembershipType"],
			"page" => "program",
			"ip" => $_SERVER['REMOTE_ADDR']
		);
		$db->insert("register", $insert);
		$returnJSON['registerID'] = $db->lastInsertId();
	} else {
		$update = array(
			"program" => "summit-kids-1-6",
			"location" => $_POST["Location"],
			"grade" => $_POST["ChildGrade"],
			"type" => $_POST["MembershipType"],
			"page" => "program",
			"updated" => date("Y-m-d H:i:s")
		);
		$where = "id = '".$_POST['registerID']."'";
		$db->update("register", $update, $where);
		$returnJSON['registerID'] = $_POST['registerID'];
	}
}

echo json_encode($returnJSON);
exit();
?>