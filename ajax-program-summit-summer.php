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
			"program" => "summit-summer",
			"location" => "",
			"grade" => $_POST["ChildGrade"],
			"cost" => 0,
			"type" => "sessions",
			"summit_id" => $_POST['SummitID'],
			"page" => "program",
			"ip" => $_SERVER['REMOTE_ADDR']
		);
		$db->insert("register", $insert);
		$registerID = $db->lastInsertId();
		$returnJSON['registerID'] = $registerID;
	} else {
		$update = array(
			"program" => "summit-summer",
			"location" => "",
			"grade" => $_POST["ChildGrade"],
			"cost" => 0,
			"type" => "sessions",
			"summit_id" => $_POST['SummitID'],
			"page" => "program",
			"updated" => date("Y-m-d H:i:s")
		);
		$where = "id = '".$_POST['registerID']."'";
		$db->update("register", $update, $where);
		$registerID = $_POST['registerID'];
		$returnJSON['registerID'] = $_POST['registerID'];
	}

	// remove any previous sessions for this registration, then add new from POST
	$db->delete("session","register_id = ".$registerID);
	$sessionArray = array();
	$cost = 0;
	// process regular sessions
	if (isset($_POST["SummerSessions"])) {
		foreach ($_POST["SummerSessions"] as $session_key) {
			$thisSession = explode('-', $session_key);
			$thisRow = $thisSession[2];
			if (!isset($_POST['SummerSession'.$thisRow.'Cost']) || $_POST['SummerSession'.$thisRow.'Cost'] == '') {
				$_POST['SummerSession'.$thisRow.'Cost'] = 0;
			} else {
				$cost += $_POST['SummerSession'.$thisRow.'Cost'];
			}
			if (!isset($_POST['SummerSession'.$thisRow.'AM'][0]) || $_POST['SummerSession'.$thisRow.'AM'][0] == '') {
				$_POST['SummerSession'.$thisRow.'AM'][0] = 0;
			} else {
				$cost += $_POST['SummerSession'.$thisRow.'AM'][0];
			}
			if (!isset($_POST['SummerSession'.$thisRow.'PM'][0]) || $_POST['SummerSession'.$thisRow.'PM'][0] == '') {
				$_POST['SummerSession'.$thisRow.'PM'][0] = 0;
			} else {
				$cost += $_POST['SummerSession'.$thisRow.'PM'][0];
			}
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"register_id" => $registerID,
				"session" => $session_key,
				"program" => "summit-summer",
				"activity" => $_POST['SummerSession'.$thisRow.'Activity'],
				"begin_date" => "",
				"end_date" => "",
				"extended" => "no",
				"base_cost" => $_POST['SummerSession'.$thisRow.'Cost'],
				"am_cost" => $_POST['SummerSession'.$thisRow.'AM'][0],
				"pm_cost" => $_POST['SummerSession'.$thisRow.'PM'][0]
			);
			$db->insert("session", $insert);
			$sessionArray[] = $db->lastInsertId();
		}
	}
	// process extended sessions
	if (isset($_POST["SummerExtendedSessions"])) {
		foreach ($_POST["SummerExtendedSessions"] as $session_key) {
			$thisSession = explode('-', $session_key);
			$thisRow = $thisSession[3];
			if (!isset($_POST['SummerExtendedSession'.$thisRow.'Cost']) || $_POST['SummerExtendedSession'.$thisRow.'Cost'] == '') {
				$_POST['SummerExtendedSession'.$thisRow.'Cost'] = 0;
			} else {
				$cost += $_POST['SummerExtendedSession'.$thisRow.'Cost'];
			}
			if (!isset($_POST['SummerExtendedSession'.$thisRow.'AM'][0]) || $_POST['SummerExtendedSession'.$thisRow.'AM'][0] == '') {
				$_POST['SummerExtendedSession'.$thisRow.'AM'][0] = 0;
			} else {
				$cost += $_POST['SummerExtendedSession'.$thisRow.'AM'][0];
			}
			if (!isset($_POST['SummerExtendedSession'.$thisRow.'PM'][0]) || $_POST['SummerExtendedSession'.$thisRow.'PM'][0] == '') {
				$_POST['SummerExtendedSession'.$thisRow.'PM'][0] = 0;
			} else {
				$cost += $_POST['SummerExtendedSession'.$thisRow.'PM'][0];
			}
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"register_id" => $registerID,
				"session" => $session_key,
				"program" => "summit-summer",
	//			"activity" => $_POST['SummerExtendedSession'.$thisRow.'Activity'],
				"activity" => "summit-citizens",
				"begin_date" => "",
				"end_date" => "",
				"extended" => "yes",
				"base_cost" => $_POST['SummerExtendedSession'.$thisRow.'Cost'],
				"am_cost" => $_POST['SummerExtendedSession'.$thisRow.'AM'][0],
				"pm_cost" => $_POST['SummerExtendedSession'.$thisRow.'PM'][0]
			);
			$db->insert("session", $insert);
			$sessionArray[] = $db->lastInsertId();
		}
	}

	// recalculate cost
	$update = array(
		"updated" => date("Y-m-d H:i:s"),
		"cost" => $cost
	);
	$where = "id = '".$registerID."'";
	$db->update("register", $update, $where);

	// send back the IDs of the new sessions
	$returnJSON['sessionID'] = $sessionArray; 
}

echo json_encode($returnJSON);
exit();
?>