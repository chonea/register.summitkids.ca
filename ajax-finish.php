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
	$update = array(
		"updated" => date("Y-m-d H:i:s"),
		"page" => "submit",
	);
	$where = "id = '".$_POST['registerID']."'";
	$db->update("register", $update, $where);
}

echo json_encode($returnJSON);
exit();
?>