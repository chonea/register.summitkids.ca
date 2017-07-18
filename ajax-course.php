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

function toInt($str) {
	return preg_replace("/([^0-9\\.])/i", "", $str);
}

if (isset($_POST['form'])) {

	if ($_POST['form'] == "course-form") {

		// Format dates
		$date = DateTime::createFromFormat("m/d/Y g:i a", $_POST["CourseStartDate"]);
		$course_start_date = $date->format("Y-m-d H:i:s");
		$date = DateTime::createFromFormat("m/d/Y g:i a", $_POST["CourseEndDate"]);
		$course_end_date = $date->format("Y-m-d H:i:s");
		$date = DateTime::createFromFormat("m/d/Y g:i a", $_POST["CourseRegistrationStartDate"]);
		$course_registration_start_date = $date->format("Y-m-d H:i:s");
		$date = DateTime::createFromFormat("m/d/Y g:i a", $_POST["CourseRegistrationEndDate"]);
		$course_registration_end_date = $date->format("Y-m-d H:i:s");
		
		// Insert Course
		if ($_POST['courseID'] == '') {
			$available = $_POST["CourseMaxAvailable"] - $_POST["CourseReserveAvailable"];
			$insert = array(
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
				"name" => $_POST["CourseName"],
				"program_id" => $_POST["CourseProgramID"],
				"location_id" => $_POST["CourseLocationID"],
				"grade" => $_POST["CourseGrade"],
				"start_date" => date($course_start_date),
				"end_date" => date($course_end_date),
				"registration_start_date" => date($course_registration_start_date),
				"registration_end_date" => date($course_registration_end_date),
				"max_available" => $_POST["CourseMaxAvailable"],
				"reserve_available" => $_POST["CourseReserveAvailable"],
				"current_available" => $available,
				"description" => $_POST["CourseDescription"],
				"special_instructions" => $_POST["CourseSpecialInstructions"],
			);
			/*
			if ($_POST["CourseDisabled"] == 'yes') {
				$insert['disabled'] = date("Y-m-d H:i:s");
			} else {
				$insert['disabled'] = '';
			}
			if ($_POST["CourseDeleted"] == 'yes') {
				$insert['deleted'] = date("Y-m-d H:i:s");
			} else {
				$insert['deleted'] = '';
			}
			*/
			$db->insert("course", $insert);
			$course_id = $db->lastInsertId();
			$returnJSON["courseID"] = $course_id;

		// Update Course
		} else {
			$course_id = $_POST["courseID"];
			$available = $_POST["CourseMaxAvailable"] - $_POST["CourseReserveAvailable"];
			$where = "course_id = '".$course_id."'";
			$where .= " AND (accepted IS NOT NULL AND accepted <> '0000-00-00 00:00:00')";
			$where .= " AND (deleted IS NULL OR deleted = '0000-00-00 00:00:00')";
			if ($result = $db->select("register", $where)) {
				$available -= count($result); 
			}
			$update = array(
				"updated" => date("Y-m-d H:i:s"),
				"name" => $_POST["CourseName"],
				"program_id" => $_POST["CourseProgramID"],
				"location_id" => $_POST["CourseLocationID"],
				"grade" => $_POST["CourseGrade"],
				"start_date" => date($course_start_date),
				"end_date" => date($course_end_date),
				"registration_start_date" => date($course_registration_start_date),
				"registration_end_date" => date($course_registration_end_date),
				"max_available" => $_POST["CourseMaxAvailable"],
				"reserve_available" => $_POST["CourseReserveAvailable"],
				"current_available" => $available,
				"description" => $_POST["CourseDescription"],
				"special_instructions" => $_POST["CourseSpecialInstructions"],
			);
			/*
			if ($_POST["CourseDisabled"] == 'yes') {
				$update['disabled'] = date("Y-m-d H:i:s");
			} else {
				$update['disabled'] = '';
			}
			if ($_POST["CourseDeleted"] == 'yes') {
				$update['deleted'] = date("Y-m-d H:i:s");
			} else {
				$update['deleted'] = '';
			}
			*/
			$where = "id = '".$course_id."'";
			$db->update("course", $update, $where);
		}
			
		// Disable all current course activity records
		$where = "course_id = '".$course_id."'";
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"deleted" => date("Y-m-d H:i:s"),
		);
		$db->update("course_activity", $update, $where);

		// Now update and re-enable any activities that have been included, if they exist
		if (isset($_POST["CourseActivities"])) {
			// Issue fixed -- now checking hidden ID field and checking to see if its checkbox came back with a value
			foreach ($_POST["CourseActivityID"] as $index => $activity_id) {
				if (in_array($activity_id,$_POST["CourseActivities"])) {
				// Try to update activity record
				$where = "course_id = '".$course_id."'";
				$where .= " AND activity_id = '".$activity_id."'";
				$update = array(
					"updated" => date("Y-m-d H:i:s"),
					"disabled" => NULL,
					"deleted" => NULL,
					"course_id" => $course_id,
					"activity_id" => $activity_id,
					"special_instructions" => $_POST["CourseActivitySpecialInstructions"][$index],
					"cost" => toInt($_POST["CourseActivityCost"][$index]),
					"discount_cost" => toInt($_POST["CourseActivityDiscountCost"][$index]),
				);
				if (!$db->update("course_activity", $update, $where)) {
					// if we couldn't update the activity record, we'll just create it
					$insert = array(
						"created" => date("Y-m-d H:i:s"),
						"updated" => date("Y-m-d H:i:s"),
						"disabled" => NULL,
						"deleted" => NULL,
						"course_id" => $course_id,
						"activity_id" => $activity_id,
						"special_instructions" => $_POST["CourseActivitySpecialInstructions"][$index],
						"cost" => toInt($_POST["CourseActivityCost"][$index]),
						"discount_cost" => toInt($_POST["CourseActivityDiscountCost"][$index]),
					);
					$db->insert("course_activity", $insert);
				} 
				}
			}
		}
	}
}

echo json_encode($returnJSON);
exit();
?>