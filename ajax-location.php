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

	if ($_POST['form'] == "location-form") {

		// Insert Location
		if ($_POST['locationID'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"name" => $_POST["LocationName"],
//				"abbreviation" => $_POST["LocationAbbreviation"],
				"address" => $_POST["LocationAddress"],
				"city" => $_POST["LocationCity"],
				"province" => $_POST["LocationProvince"],
				"postal_code" => $_POST["LocationPostalCode"],
				"email" => $_POST["LocationEmail"],
				"phone" => $_POST["LocationPhone"],
				"fax" => $_POST["LocationFax"],
				"web_url" => $_POST["LocationURL"],
				"description" => $_POST["LocationDescription"],
				"teaser" => $_POST["LocationTeaser"]
			);
			$db->insert("location", $insert);
			$location_id = $db->lastInsertId();
			$returnJSON["locationID"] = $location_id;
			// Associate programs
			if (isset($_POST["LocationPrograms"])) {
				foreach ($_POST["LocationPrograms"] as $program_id) {
					$program_insert = array(
						"location_id" => $location_id,
						"program_id" => $program_id
					);
					$db->insert("location_program", $program_insert);
				}
			}

		// Update Location
		} else {
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"name" => $_POST["LocationName"],
//				"abbreviation" => $_POST["LocationAbbreviation"],
				"address" => $_POST["LocationAddress"],
				"city" => $_POST["LocationCity"],
				"province" => $_POST["LocationProvince"],
				"postal_code" => $_POST["LocationPostalCode"],
				"email" => $_POST["LocationEmail"],
				"phone" => $_POST["LocationPhone"],
				"fax" => $_POST["LocationFax"],
				"web_url" => $_POST["LocationURL"],
				"description" => $_POST["LocationDescription"],
				"teaser" => $_POST["LocationTeaser"]
			);
			$where = "id = '".$_POST['locationID']."'";
			$db->update("location", $update, $where);
			// Associate programs
			$programs = $db->select("location_program", "location_id = '".$_POST['locationID']."'");
			$save_programs = array();
			foreach ($programs as $program) {
				$save_programs[] = $program["program_id"];
			}
			if (isset($_POST["LocationPrograms"])) {
				foreach ($save_programs as $program_id) {
					if (!in_array($program_id, $_POST["LocationPrograms"])) {
						$db->delete("location_program", "location_id = '".$_POST['locationID']."' AND program_id = '".$program_id."'");
					}
				}
				foreach ($_POST["LocationPrograms"] as $program_id) {
					if (!in_array($program_id, $save_programs)) {
						$program_insert = array(
							"location_id" => $_POST['locationID'],
							"program_id" => $program_id
						);
						$db->insert("location_program", $program_insert);
					}
				}
			} else {
				$db->delete("location_program", "location_id = '".$_POST['locationID']."'");
			}
		}
	}
}

echo json_encode($returnJSON);
exit();
?>