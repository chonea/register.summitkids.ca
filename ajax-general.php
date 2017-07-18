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

/***************
 ** CHILD
 ***************/

	$date = DateTime::createFromFormat("m/d/Y", $_POST["ChildBirthDate"]);
	$child_birth_date = $date->format("Y-m-d");

//echo "[SelectChild: '".$_POST['SelectChild']."']";
//echo "[post: '";
//print_r $_POST."']";
//echo "[registerID: '".$_POST['registerID']."']";
	// If Add Child was selected
	if ($_POST['SelectChild'] == "add-child") {

		// If adding a child and no record has been created yet, create it.
		if ($_POST['childID'] == '') {
//echo "[add-child insert-child no-id]";
	
			// Insert new child
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
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"child_id" => $child_id,
				"page" => "general",
			);
			$where = "id = '".$_POST['registerID']."'";
			$db->update("register", $update, $where);
				
		// (else) We're still adding a child, but we've already created the record (clicking back) -- let's update the child.
		} else {
//echo "[add-child update-child with-id]";

			// Update new child
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
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"page" => "general",
			);
			$where = "id = '".$_POST['registerID']."'";
			$db->update("register", $update, $where);
	
		}

	// (else) We've selected a child from the list, or we are editing a previous application and the child was already selected.
	} else {
//echo "[selected-child update-child with-id]";

		// Update existing child
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"first_name" => $_POST["ChildFirstName"],
			"last_name" => $_POST["ChildLastName"],
			"birth_date" => date($child_birth_date),
			"gender" => $_POST["ChildGender"],
			"photo" => $_POST["ChildPhoto"]
		);
		$where = "id = '".$_POST['SelectChild']."'";
		$db->update("child", $update, $where);
		$child_id = $_POST['SelectChild'];

		// Update registration
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"child_id" => $child_id,
			"page" => "general",
		);
		$where = "id = '".$_POST['registerID']."'";
		$db->update("register", $update, $where);

	}

	$returnJSON["childID"] = $child_id;
	if (isset($filehash)) {
		$returnJSON['imageFile'] = $filehash;
	} else {
		$returnJSON['imageFile'] = '';
	}

/***************
 ** GUARDIANS
 ***************/

	// Delete all guardian relationships (if they existed, they will be recreated)
	$where = "child_id = '".$_POST['SelectChild']."'";
	$db->delete("relationship", $where);

	// Guardian One?
	// Insert Guardian One
	if ($_POST['SelectGuardian1'] == 'add-guardian' && $_POST['guardianOneID'] == '') {

		$insert = array(
			"user_id" => $_POST["userID"],
			"created" => date("Y-m-d H:i:s"),
			"updated" => date("Y-m-d H:i:s"),
			"first_name" => $_POST["GuardianOneFirstName"],
			"last_name" => $_POST["GuardianOneLastName"],
			"email" => $_POST["GuardianOneEmail"],
			"cell_phone" => $_POST["GuardianOneCellPhone"],
			"home_phone" => $_POST["GuardianOneHomePhone"],
			"daytime_phone" => $_POST["GuardianOneDaytimePhone"],
			"address" => $_POST["GuardianOneAddress"],
			"city" => $_POST["GuardianOneCity"],
			"province" => $_POST["GuardianOneProvince"],
			"postal_code" => $_POST["GuardianOnePostalCode"],
			"daytime_address" => $_POST["GuardianOneDaytimeAddress"],
			"daytime_city" => $_POST["GuardianOneDaytimeCity"],
			"daytime_province" => $_POST["GuardianOneDaytimeProvince"],
			"daytime_postal_code" => $_POST["GuardianOneDaytimePostalCode"],
		);
		$db->insert("guardian", $insert);
		$guardian_one_id = $db->lastInsertId();
		$returnJSON["guardianOneID"] = $guardian_one_id;
		// Create relationship
		$insert = array(
			"created" => date("Y-m-d H:i:s"),
			"guardian_id" => $guardian_one_id,
			"child_id" => $child_id,
			"relationship" => $_POST["GuardianOneRelationship"],
			"lives_with" => $_POST["GuardianOneLivesWith"],
		);
		$db->insert("relationship", $insert);

	// No Guardian One
	} elseif ($_POST['SelectGuardian1'] == 'not-applicable') {
		// Delete relationship already done, nothing else to do

	// Update Guardian One
	} elseif ($_POST['SelectGuardian1'] != '') {
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"first_name" => $_POST["GuardianOneFirstName"],
			"last_name" => $_POST["GuardianOneLastName"],
			"email" => $_POST["GuardianOneEmail"],
			"cell_phone" => $_POST["GuardianOneCellPhone"],
			"home_phone" => $_POST["GuardianOneHomePhone"],
			"daytime_phone" => $_POST["GuardianOneDaytimePhone"],
			"address" => $_POST["GuardianOneAddress"],
			"city" => $_POST["GuardianOneCity"],
			"province" => $_POST["GuardianOneProvince"],
			"postal_code" => $_POST["GuardianOnePostalCode"],
			"daytime_address" => $_POST["GuardianOneDaytimeAddress"],
			"daytime_city" => $_POST["GuardianOneDaytimeCity"],
			"daytime_province" => $_POST["GuardianOneDaytimeProvince"],
			"daytime_postal_code" => $_POST["GuardianOneDaytimePostalCode"],
		);
		$where = "id = '".$_POST['SelectGuardian1']."'";
		$db->update("guardian", $update, $where);

		// Recreate relationship
		$insert = array(
			"created" => date("Y-m-d H:i:s"),
			"guardian_id" => $_POST['SelectGuardian1'],
			"child_id" => $_POST['SelectChild'],
			"relationship" => $_POST["GuardianOneRelationship"],
			"lives_with" => $_POST["GuardianOneLivesWith"],
		);
		$db->insert("relationship", $insert);
	}

	// Guardian Two?
	// Insert Guardian Two
	if ($_POST['SelectGuardian2'] == 'add-guardian' && $_POST['guardianTwoID'] == '') {

		$insert = array(
			"user_id" => $_POST["userID"],
			"created" => date("Y-m-d H:i:s"),
			"updated" => date("Y-m-d H:i:s"),
			"first_name" => $_POST["GuardianTwoFirstName"],
			"last_name" => $_POST["GuardianTwoLastName"],
			"email" => $_POST["GuardianTwoEmail"],
			"cell_phone" => $_POST["GuardianTwoCellPhone"],
			"home_phone" => $_POST["GuardianTwoHomePhone"],
			"daytime_phone" => $_POST["GuardianTwoDaytimePhone"],
			"address" => $_POST["GuardianTwoAddress"],
			"city" => $_POST["GuardianTwoCity"],
			"province" => $_POST["GuardianTwoProvince"],
			"postal_code" => $_POST["GuardianTwoPostalCode"],
			"daytime_address" => $_POST["GuardianTwoDaytimeAddress"],
			"daytime_city" => $_POST["GuardianTwoDaytimeCity"],
			"daytime_province" => $_POST["GuardianTwoDaytimeProvince"],
			"daytime_postal_code" => $_POST["GuardianTwoDaytimePostalCode"],
		);
		$db->insert("guardian", $insert);
		$guardian_two_id = $db->lastInsertId();
		$returnJSON["guardianTwoID"] = $guardian_two_id;
		// Create relationship
		$insert = array(
			"created" => date("Y-m-d H:i:s"),
			"guardian_id" => $guardian_two_id,
			"child_id" => $child_id,
			"relationship" => $_POST["GuardianTwoRelationship"],
			"lives_with" => $_POST["GuardianTwoLivesWith"],
		);
		$db->insert("relationship", $insert);

	// No Guardian Two
	} elseif ($_POST['SelectGuardian2'] == 'not-applicable') {
		// Delete relationship already done, nothing else to do

	// Update Guardian Two
	} elseif ($_POST['SelectGuardian2'] != '') {
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"first_name" => $_POST["GuardianTwoFirstName"],
			"last_name" => $_POST["GuardianTwoLastName"],
			"email" => $_POST["GuardianTwoEmail"],
			"cell_phone" => $_POST["GuardianTwoCellPhone"],
			"home_phone" => $_POST["GuardianTwoHomePhone"],
			"daytime_phone" => $_POST["GuardianTwoDaytimePhone"],
			"address" => $_POST["GuardianTwoAddress"],
			"city" => $_POST["GuardianTwoCity"],
			"province" => $_POST["GuardianTwoProvince"],
			"postal_code" => $_POST["GuardianTwoPostalCode"],
			"daytime_address" => $_POST["GuardianTwoDaytimeAddress"],
			"daytime_city" => $_POST["GuardianTwoDaytimeCity"],
			"daytime_province" => $_POST["GuardianTwoDaytimeProvince"],
			"daytime_postal_code" => $_POST["GuardianTwoDaytimePostalCode"],
		);
		$where = "id = '".$_POST['SelectGuardian2']."'";
		$db->update("guardian", $update, $where);

		// Recreate relationship
		$insert = array(
			"created" => date("Y-m-d H:i:s"),
			"guardian_id" => $_POST['SelectGuardian2'],
			"child_id" => $_POST['SelectChild'],
			"relationship" => $_POST["GuardianTwoRelationship"],
			"lives_with" => $_POST["GuardianTwoLivesWith"],
		);
		$db->insert("relationship", $insert);
	}
} else {
	echo "ERROR: No form data.";
}
//echo print_r($_POST);

echo json_encode($returnJSON);
exit();

?>