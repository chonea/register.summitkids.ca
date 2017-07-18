<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();
//echo $_POST['registerID'];
if (isset($_POST['form']) && isset($_POST['registerID']) && $_POST['registerID'] != '') {
	if ($results = $db->select("register", "id = '".$_POST['registerID']."'")) {
		$registration = $results[0];

		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"submitted" => date("Y-m-d H:i:s"),
			"page" => "submit",
		);
		$where = "id = '".$_POST['registerID']."'";
		$db->update("register", $update, $where);

		if ($results = $db->select("course", "id = '".$registration['course_id']."'")) {
			$course = $results[0];

			$where = "course_id = '".$course['id']."'";
			$where .= " AND (accepted IS NOT NULL AND accepted <> '0000-00-00 00:00:00')";
			$where .= " AND (deleted IS NULL OR deleted = '0000-00-00 00:00:00')";
			if ($result = $db->select("register", $where)) {
				$current_registrations = count($result);
			} else {
				$current_registrations = 0;
			}
			$current_availability = $course['max_available'] - $course['reserve_available'] - $current_registrations;
	
			$update = array(
				"current_available" => $current_availability
			);
			$where = "id = '".$course['id']."'";
			$db->update("course", $update, $where);
		}
	}
}

echo json_encode($returnJSON);
exit();
?>