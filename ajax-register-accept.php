<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();


function incrementAlphaNumeric($anstr) {
	/*
	$numerics = "0123456789";
	$alphas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$numeric_array = str_split($numerics);
	$alpha_array = str_split($alphas);
	$anstr_array = str_split($anstr);
	foreach (array_reverse($anstr_array) as $index => $anchar) {
		if ($anchar < 9) {
			$anchar++;
			break;
		}
		if ($index == 0) {
			foreach (array_reverse($anstr_array) as $index => $anchar) {
			}
		}
	}
	*/
	
	$anstr++;
	return str_pad($anstr, 5, "0", STR_PAD_LEFT);
}

if (isset($_POST['registerID'])) {

	// mark registration as accepted
	$update = array(
		"updated" => date("Y-m-d H:i:s"),
		"accepted" => date("Y-m-d H:i:s"),
	);
	$where = "id = '".$_POST['registerID']."'";
	$db->update("register", $update, $where);
	$result = $db->select("register", $where);
	$register = $result[0];

	$where = "id = '".$register['course_id']."'";
	$result = $db->select("course", $where);
	$course = $result[0];

	// update course availability
	$registrations = $db->select("register", "course_id = '".$register['course_id']."' AND accepted IS NOT NULL AND accepted <> '0000-00-00 00:00:00'");
	$current_availability = $course['max_available'] - $course['reserve_available'] - count($registrations);
	$update = array(
		"current_available" => $current_availability
	);
	$where = "id = '".$course['id']."'";
	$db->update("course", $update, $where);

	$where = "id = '".$register['child_id']."'";
	$result = $db->select("child", $where);
	$child = $result[0];

	if ($child['summit_id'] == "" || $child['summit_id'] == NULL) {

		// get next summit id
		$result = $db->select("system", "id = '1'");
		$system = $result[0];
		$skID = $system['summit_id_last'];
		if ($skID == "" or $skID == NULL) {
			$skID = $system['summit_id_first'];
		}
		$skID = incrementAlphaNumeric($skID);

		// update system summit id last
		$update = array(
			"summit_id_last" => $skID
		);
		$where = "id = '1'";
		if (!$db->update("system", $update, $where)) {
			die('Error updating system summit ID.');
		}

		// update child summit id
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"summit_id" => $skID
		);
		$where = "id = '".$register['child_id']."'";
		if (!$db->update("child", $update, $where)) {
			die('Error updating child summit ID.');
		}

	} else {
		$skID = $child['summit_id'];
	}
	$returnJSON['summit_id'] = $skID;
}

echo json_encode($returnJSON);
exit();
?>