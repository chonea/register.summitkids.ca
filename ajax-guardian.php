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

	if ($_POST['form'] == "guardian-form") {

		// Insert Guardian
		if ($_POST['guardianID'] == '') {
			$insert = array(
				"user_id" => $_POST["userID"],
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["GuardianFirstName"],
				"last_name" => $_POST["GuardianLastName"],
				"email" => $_POST["GuardianEmail"],
				"cell_phone" => $_POST["GuardianCellPhone"],
				"home_phone" => $_POST["GuardianHomePhone"],
				"daytime_phone" => $_POST["GuardianDaytimePhone"],
				"address" => $_POST["GuardianAddress"],
				"city" => $_POST["GuardianCity"],
				"province" => $_POST["GuardianProvince"],
				"postal_code" => $_POST["GuardianPostalCode"],
				"daytime_address" => $_POST["GuardianDaytimeAddress"],
				"daytime_city" => $_POST["GuardianDaytimeCity"],
				"daytime_province" => $_POST["GuardianDaytimeProvince"],
				"daytime_postal_code" => $_POST["GuardianDaytimePostalCode"],
			);
			$db->insert("guardian", $insert);
			$guardian_id = $db->lastInsertId();
			$returnJSON["guardianID"] = $guardian_id;

		// Update Guardian
		} else {
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["GuardianFirstName"],
				"last_name" => $_POST["GuardianLastName"],
				"email" => $_POST["GuardianEmail"],
				"cell_phone" => $_POST["GuardianCellPhone"],
				"home_phone" => $_POST["GuardianHomePhone"],
				"daytime_phone" => $_POST["GuardianDaytimePhone"],
				"address" => $_POST["GuardianAddress"],
				"city" => $_POST["GuardianCity"],
				"province" => $_POST["GuardianProvince"],
				"postal_code" => $_POST["GuardianPostalCode"],
				"daytime_address" => $_POST["GuardianDaytimeAddress"],
				"daytime_city" => $_POST["GuardianDaytimeCity"],
				"daytime_province" => $_POST["GuardianDaytimeProvince"],
				"daytime_postal_code" => $_POST["GuardianDaytimePostalCode"],
			);
			$where = "id = '".$_POST['guardianID']."'";
			$db->update("guardian", $update, $where);
		}
	}
}

echo json_encode($returnJSON);
exit();
?>