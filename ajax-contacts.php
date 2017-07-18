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

	// Update registration
	if (isset($_POST['registerID']) && $_POST['registerID'] != '') {
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"page" => "contacts",
		);
		$where = "id = '".$_POST['registerID']."'";
		$db->update("register", $update, $where);
	}
	$_POST['emergencyID1'] = '';
	$_POST['emergencyID2'] = '';
	$_POST['emergencyID3'] = '';
	$_POST['authorizedID1'] = '';
	$_POST['authorizedID2'] = '';
	$_POST['authorizedID3'] = '';
	$_POST['restrictedID1'] = '';
	$_POST['restrictedID2'] = '';
	$_POST['restrictedID3'] = '';

	// Delete all contacts (if they existed, they will be recreated)
	$where = "child_id = '".$_POST['childID']."'";
	$db->delete("contact", $where);

	if ($_POST["EmergencyContactOneFirstName"] != '' || $_POST["EmergencyContactOneLastName"] != '') {
		// Insert emergency contact 1
		if ($_POST['emergencyID1'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "emergency",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["EmergencyContactOneFirstName"],
				"last_name" => $_POST["EmergencyContactOneLastName"],
				"relationship" => $_POST["EmergencyContactOneRelationship"],
				"email" => $_POST["EmergencyContactOneEmail"],
				"phone_1" => $_POST["EmergencyContactOnePhoneOne"],
				"phone_2" => $_POST["EmergencyContactOnePhoneTwo"],
				"phone_3" => $_POST["EmergencyContactOnePhoneThree"],
				"daytime_address" => $_POST["EmergencyContactOneDaytimeAddress"],
				"daytime_city" => $_POST["EmergencyContactOneDaytimeCity"],
				"daytime_province" => $_POST["EmergencyContactOneDaytimeProvince"],
				"daytime_postal_code" => $_POST["EmergencyContactOneDaytimePostalCode"],
			);
			$db->insert("contact", $insert);
			$returnJSON["emergencyID1"] = $db->lastInsertId();
		} else {
		// Update emergency contact 1
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["EmergencyContactOneFirstName"],
				"last_name" => $_POST["EmergencyContactOneLastName"],
				"relationship" => $_POST["EmergencyContactOneRelationship"],
				"email" => $_POST["EmergencyContactOneEmail"],
				"phone_1" => $_POST["EmergencyContactOnePhoneOne"],
				"phone_2" => $_POST["EmergencyContactOnePhoneTwo"],
				"phone_3" => $_POST["EmergencyContactOnePhoneThree"],
				"daytime_address" => $_POST["EmergencyContactOneDaytimeAddress"],
				"daytime_city" => $_POST["EmergencyContactOneDaytimeCity"],
				"daytime_province" => $_POST["EmergencyContactOneDaytimeProvince"],
				"daytime_postal_code" => $_POST["EmergencyContactOneDaytimePostalCode"],
			);
			$where = "id = '".$_POST['emergencyID1']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["EmergencyContactTwoFirstName"] != '' || $_POST["EmergencyContactTwoLastName"] != '') {
		// Insert emergency contact 2
		if ($_POST['emergencyID2'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "emergency",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["EmergencyContactTwoFirstName"],
				"last_name" => $_POST["EmergencyContactTwoLastName"],
				"relationship" => $_POST["EmergencyContactTwoRelationship"],
				"email" => $_POST["EmergencyContactTwoEmail"],
				"phone_1" => $_POST["EmergencyContactTwoPhoneOne"],
				"phone_2" => $_POST["EmergencyContactTwoPhoneTwo"],
				"phone_3" => $_POST["EmergencyContactTwoPhoneThree"],
				"daytime_address" => $_POST["EmergencyContactTwoDaytimeAddress"],
				"daytime_city" => $_POST["EmergencyContactTwoDaytimeCity"],
				"daytime_province" => $_POST["EmergencyContactTwoDaytimeProvince"],
				"daytime_postal_code" => $_POST["EmergencyContactTwoDaytimePostalCode"],
			);
			$db->insert("contact", $insert);
			$returnJSON["emergencyID2"] = $db->lastInsertId();
		} else {
		// Update emergency contact 2
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["EmergencyContactTwoFirstName"],
				"last_name" => $_POST["EmergencyContactTwoLastName"],
				"relationship" => $_POST["EmergencyContactTwoRelationship"],
				"email" => $_POST["EmergencyContactTwoEmail"],
				"phone_1" => $_POST["EmergencyContactTwoPhoneOne"],
				"phone_2" => $_POST["EmergencyContactTwoPhoneTwo"],
				"phone_3" => $_POST["EmergencyContactTwoPhoneThree"],
				"daytime_address" => $_POST["EmergencyContactTwoDaytimeAddress"],
				"daytime_city" => $_POST["EmergencyContactTwoDaytimeCity"],
				"daytime_province" => $_POST["EmergencyContactTwoDaytimeProvince"],
				"daytime_postal_code" => $_POST["EmergencyContactTwoDaytimePostalCode"],
			);
			$where = "id = '".$_POST['emergencyID2']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["EmergencyContactThreeFirstName"] != '' || $_POST["EmergencyContactThreeLastName"] != '') {
		// Insert emergency contact 3
		if ($_POST['emergencyID3'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "emergency",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["EmergencyContactThreeFirstName"],
				"last_name" => $_POST["EmergencyContactThreeLastName"],
				"relationship" => $_POST["EmergencyContactThreeRelationship"],
				"email" => $_POST["EmergencyContactThreeEmail"],
				"phone_1" => $_POST["EmergencyContactThreePhoneOne"],
				"phone_2" => $_POST["EmergencyContactThreePhoneTwo"],
				"phone_3" => $_POST["EmergencyContactThreePhoneThree"],
				"daytime_address" => $_POST["EmergencyContactThreeDaytimeAddress"],
				"daytime_city" => $_POST["EmergencyContactThreeDaytimeCity"],
				"daytime_province" => $_POST["EmergencyContactThreeDaytimeProvince"],
				"daytime_postal_code" => $_POST["EmergencyContactThreeDaytimePostalCode"],
			);
			$db->insert("contact", $insert);
			$returnJSON["emergencyID3"] = $db->lastInsertId();
		} else {
		// Update emergency contact 3
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["EmergencyContactThreeFirstName"],
				"last_name" => $_POST["EmergencyContactThreeLastName"],
				"relationship" => $_POST["EmergencyContactThreeRelationship"],
				"email" => $_POST["EmergencyContactThreeEmail"],
				"phone_1" => $_POST["EmergencyContactThreePhoneOne"],
				"phone_2" => $_POST["EmergencyContactThreePhoneTwo"],
				"phone_3" => $_POST["EmergencyContactThreePhoneThree"],
				"daytime_address" => $_POST["EmergencyContactThreeDaytimeAddress"],
				"daytime_city" => $_POST["EmergencyContactThreeDaytimeCity"],
				"daytime_province" => $_POST["EmergencyContactThreeDaytimeProvince"],
				"daytime_postal_code" => $_POST["EmergencyContactThreeDaytimePostalCode"],
			);
			$where = "id = '".$_POST['emergencyID3']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["AuthorizedContactOneFirstName"] != '' || $_POST["AuthorizedContactOneLastName"] != '') {
		// Insert authorized contact 1
		if ($_POST['authorizedID1'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "authorized",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["AuthorizedContactOneFirstName"],
				"last_name" => $_POST["AuthorizedContactOneLastName"],
				"relationship" => $_POST["AuthorizedContactOneRelationship"],
				"email" => $_POST["AuthorizedContactOneEmail"],
				"phone_1" => $_POST["AuthorizedContactOnePhoneOne"],
				"phone_2" => $_POST["AuthorizedContactOnePhoneTwo"],
				"phone_3" => $_POST["AuthorizedContactOnePhoneThree"],
			);
			$db->insert("contact", $insert);
			$returnJSON["authorizedID1"] = $db->lastInsertId();
		} else {
		// Update authorized contact 1
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["AuthorizedContactOneFirstName"],
				"last_name" => $_POST["AuthorizedContactOneLastName"],
				"relationship" => $_POST["AuthorizedContactOneRelationship"],
				"email" => $_POST["AuthorizedContactOneEmail"],
				"phone_1" => $_POST["AuthorizedContactOnePhoneOne"],
				"phone_2" => $_POST["AuthorizedContactOnePhoneTwo"],
				"phone_3" => $_POST["AuthorizedContactOnePhoneThree"],
			);
			$where = "id = '".$_POST['authorizedID1']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["AuthorizedContactTwoFirstName"] != '' || $_POST["AuthorizedContactTwoLastName"] != '') {
		// Insert authorized contact 2
		if ($_POST['authorizedID2'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "authorized",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["AuthorizedContactTwoFirstName"],
				"last_name" => $_POST["AuthorizedContactTwoLastName"],
				"relationship" => $_POST["AuthorizedContactTwoRelationship"],
				"email" => $_POST["AuthorizedContactTwoEmail"],
				"phone_1" => $_POST["AuthorizedContactTwoPhoneOne"],
				"phone_2" => $_POST["AuthorizedContactTwoPhoneTwo"],
				"phone_3" => $_POST["AuthorizedContactTwoPhoneThree"],
			);
			$db->insert("contact", $insert);
			$returnJSON["authorizedID2"] = $db->lastInsertId();
		} else {
		// Update authorized contact 2
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["AuthorizedContactTwoFirstName"],
				"last_name" => $_POST["AuthorizedContactTwoLastName"],
				"relationship" => $_POST["AuthorizedContactTwoRelationship"],
				"email" => $_POST["AuthorizedContactTwoEmail"],
				"phone_1" => $_POST["AuthorizedContactTwoPhoneOne"],
				"phone_2" => $_POST["AuthorizedContactTwoPhoneTwo"],
				"phone_3" => $_POST["AuthorizedContactTwoPhoneThree"],
			);
			$where = "id = '".$_POST['authorizedID2']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["AuthorizedContactThreeFirstName"] != '' || $_POST["AuthorizedContactThreeLastName"] != '') {
		// Insert authorized contact 3
		if ($_POST['authorizedID3'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "authorized",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["AuthorizedContactThreeFirstName"],
				"last_name" => $_POST["AuthorizedContactThreeLastName"],
				"relationship" => $_POST["AuthorizedContactThreeRelationship"],
				"email" => $_POST["AuthorizedContactThreeEmail"],
				"phone_1" => $_POST["AuthorizedContactThreePhoneOne"],
				"phone_2" => $_POST["AuthorizedContactThreePhoneTwo"],
				"phone_3" => $_POST["AuthorizedContactThreePhoneThree"],
			);
			$db->insert("contact", $insert);
			$returnJSON["authorizedID3"] = $db->lastInsertId();
		} else {
		// Update authorized contact 3
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["AuthorizedContactThreeFirstName"],
				"last_name" => $_POST["AuthorizedContactThreeLastName"],
				"relationship" => $_POST["AuthorizedContactThreeRelationship"],
				"email" => $_POST["AuthorizedContactThreeEmail"],
				"phone_1" => $_POST["AuthorizedContactThreePhoneOne"],
				"phone_2" => $_POST["AuthorizedContactThreePhoneTwo"],
				"phone_3" => $_POST["AuthorizedContactThreePhoneThree"],
			);
			$where = "id = '".$_POST['authorizedID3']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["RestrictedContactOneFirstName"] != '' || $_POST["RestrictedContactOneLastName"] != '') {
		// Insert restricted contact 1
		if ($_POST['restrictedID1'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "restricted",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["RestrictedContactOneFirstName"],
				"last_name" => $_POST["RestrictedContactOneLastName"],
				"detail" => $_POST["RestrictedContactOneDetail"],
	//			"file" => $_POST["RestrictedContactOneFile"],
			);
			$db->insert("contact", $insert);
			$returnJSON["restrictedID1"] = $db->lastInsertId();
		} else {
		// Update restricted contact 1
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["RestrictedContactOneFirstName"],
				"last_name" => $_POST["RestrictedContactOneLastName"],
				"detail" => $_POST["RestrictedContactOneDetail"],
	//			"file" => $_POST["RestrictedContactOneFile"],
			);
			$where = "id = '".$_POST['restrictedID1']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["RestrictedContactTwoFirstName"] != '' || $_POST["RestrictedContactTwoLastName"] != '') {
		// Insert restricted contact 2
		if ($_POST['restrictedID2'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "restricted",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["RestrictedContactTwoFirstName"],
				"last_name" => $_POST["RestrictedContactTwoLastName"],
				"detail" => $_POST["RestrictedContactTwoDetail"],
	//			"file" => $_POST["RestrictedContactTwoFile"],
			);
			$db->insert("contact", $insert);
			$returnJSON["restrictedID2"] = $db->lastInsertId();
		} else {
		// Update restricted contact 2
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["RestrictedContactTwoFirstName"],
				"last_name" => $_POST["RestrictedContactTwoLastName"],
				"detail" => $_POST["RestrictedContactTwoDetail"],
	//			"file" => $_POST["RestrictedContactTwoFile"],
			);
			$where = "id = '".$_POST['restrictedID2']."'";
			$db->update("contact", $update, $where);
		}
	}

	if ($_POST["RestrictedContactThreeFirstName"] != '' || $_POST["RestrictedContactThreeLastName"] != '') {
		// Insert restricted contact 3
		if ($_POST['restrictedID3'] == '') {
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"type" => "restricted",
				"child_id" => $_POST['childID'],
				"first_name" => $_POST["RestrictedContactThreeFirstName"],
				"last_name" => $_POST["RestrictedContactThreeLastName"],
				"detail" => $_POST["RestrictedContactThreeDetail"],
	//			"file" => $_POST["RestrictedContactThreeFile"],
			);
			$db->insert("contact", $insert);
			$returnJSON["restrictedID3"] = $db->lastInsertId();
		} else {
		// Update restricted contact 3
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"first_name" => $_POST["RestrictedContactThreeFirstName"],
				"last_name" => $_POST["RestrictedContactThreeLastName"],
				"detail" => $_POST["RestrictedContactThreeDetail"],
	//			"file" => $_POST["RestrictedContactThreeFile"],
			);
			$where = "id = '".$_POST['restrictedID3']."'";
			$db->update("contact", $update, $where);
		}
	}
}

echo json_encode($returnJSON);
exit();
?>