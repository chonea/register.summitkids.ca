<?php
//header("Content-type: application/json");
//session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$program_id = '';

if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != "") {
	$program_id = $_REQUEST["programID"];
}

$save_course = array();
$save_course_activity = array();

if (isset($_REQUEST["courseID"]) && $_REQUEST["courseID"] != "") {
	$query = "id = '".$_REQUEST["courseID"]."'";
	if ($result = $db->select("course", "id = '".$_REQUEST["courseID"]."'")) {
		$save_course = $result[0];
		if ($program_id == '') {
			$program_id = $save_course['program_id'];
		}
	}
}

if ($program_id != '') {
	$query = "1 = 1";
	$query .= " AND program_id = '".$program_id."'";
	$query .= " ORDER BY name ASC";
	$program_activities = $db->select("activity", $query);
	if (count($program_activities) == 0) {
		exit();
	}
} else {
	die('error');
	exit();
}

//add the loadSaveData function
include("loadsavedata.php");

//set taxonomy
include("config/register.config.php");

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;

include("libraries/PFBC/Form.php");

$form = new Form("course-activities-form");

$form->addElement(new Element\HTML('<ul id="course-activity-list" style="list-style-type: none; margin-left: 0;">'));

$selected_options = array();

foreach ($program_activities as $activity) {

	$selected_options[$activity['id']] = array(
		'id' => '',
		'cost' => '',
		'discount_cost' => '',
		'special_instructions' => '',
	);

	if ($result = $db->select("course_activity", "course_id = '".$_REQUEST["courseID"]."' AND activity_id = '".$activity['id']."' AND deleted IS NULL")) {
		$save_course_activity = $result[0];
		if ($save_course_activity['disabled'] != NULL) {
			$activity_id = '';
		} else {
			$activity_id = $save_course_activity['activity_id'];
		}
		$selected_options[$activity['id']] = array(
			id => $activity_id,
			cost => $save_course_activity['cost'],
			discount_cost => $save_course_activity['discount_cost'],
			special_instructions => $save_course_activity['special_instructions'],
		);
	}

	$form->addElement(new Element\HTML('<li id="course-activity-'.$activity['id'].'" class="course-activity" style="list-style-type: none;">'));

	$form->addElement(new Element\Checkbox("Activity", "CourseActivities", array($activity['id'] => $activity['name']), 
		array(
			"class" => "course-activity-select",
			"value" => $selected_options[$activity['id']]['id']
		)
	));

	$form->addElement(new Element\HTML('<div id="course-activity-detail-'.$activity['id'].'" class="course-activity-detail">'));
		$form->addElement(new Element\Textbox("Cost", "CourseActivityCost[]", array(
			"class" => "currency-cad",
			"value" => $selected_options[$activity['id']]['cost'],
			)
		));
		
		$form->addElement(new Element\Hidden("CourseActivityID[]", $activity['id']));

		$form->addElement(new Element\Textbox("Discount Cost", "CourseActivityDiscountCost[]", array(
			"class" => "currency-cad",
			"value" => $selected_options[$activity['id']]['discount_cost'],
			)
		));

		$form->addElement(new Element\Textarea("Special Instructions", "CourseActivitySpecialInstructions[]", array(
			"class" => "span12",
			"value" => $selected_options[$activity['id']]['special_instructions']
			)
		));

		$form->addElement(new Element\HTML('</div">'));
	$form->addElement(new Element\HTML('</li">'));
}
$form->addElement(new Element\HTML('</ul>'));
$form->render();
?>