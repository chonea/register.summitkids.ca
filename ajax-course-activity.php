<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();

$save_course = array();
$save_course_activity = array();

if (isset($_REQUEST["courseID"])) {
	$query = "id = '".$_REQUEST["courseID"]."'";
	if ($result = $db->select("course", "id = '".$_REQUEST["courseID"]."'")) {
		$save_course = $result;
		if ($result =$db->select("course_activity", "course_id = '".$_REQUEST["courseID"]."'")) {
			$save_course_activity = $result;
		}
	}
} else {
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

// General information form
$form = new Form("course-form");
$form->configure(array(
	"prevent" => array("bootstrap", "jQuery"),
//	"view" => new PFBC\View\Inline,
//	"labelToPlaceholder" => 1
));
$form->addElement(new Element\Hidden("form", "course-form"));
$form->addElement(new Element\Hidden("courseID", loadSaveData("course", "id", 0)));

$form->addElement(new Element\HTML('<ul id="course-activity-list" style="list-style-type: none;">'));
$save_activity_options = array();
foreach ($save_course_activity as $activity) {
	$save_activity_options[] = $activity['id'];
}
$query = "1 = 1";
$query .= " AND program_id = '".loadSaveData("course", "program_id", 0)."'";
$query .= " ORDER BY name ASC";
$activities = $db->select("activity", $query);
foreach ($activities as $activity) {
	$form->addElement(new Element\HTML('<li id="course-activity-'.$activity['id'].'" class="course-activity" style="list-style-type: none;">'));
	$activity_options = array();
	$activity_options[$activity['id']] = $activity['name'];
	$form->addElement(new Element\Checkbox("Activity", "CourseActivities", $activity_options, 
		array(
			"class" => "course-activity-select",
			"value" => $save_activity_options,
			"required" => 0
		)
	));
	$form->addElement(new Element\HTML('<div id="course-activity-detail-'.$activity['id'].'" class="course-activity-detail">'));
		$form->addElement(new Element\Textbox("Cost", "CourseActivityCost[]", array(
			"value" => ""
			)
		));
		$form->addElement(new Element\Textbox("Discount Cost", "CourseActivityDiscountCost[]", array(
			"value" => ""
			)
		));
		$form->addElement(new Element\HTML('</div">'));
	$form->addElement(new Element\HTML('</li">'));
}
$form->addElement(new Element\HTML('</ul>'));
$form->render();

echo json_encode($returnJSON);
exit();
?>