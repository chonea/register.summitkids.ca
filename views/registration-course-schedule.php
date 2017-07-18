<?php
//header("Content-type: application/json");
//session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$program_id = '';
$location_id = '';
$course_id = '';
$activity_id = '';
$save_programs = array();
$save_locations = array();
$save_courses = array();
$save_activities = array();

if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != "") {
	$program_id = $_REQUEST["programID"];
}

if (isset($_REQUEST["locationID"]) && $_REQUEST["locationID"] != "") {
	$location_id = $_REQUEST["locationID"];
}

if (isset($_REQUEST["courseID"]) && $_REQUEST["courseID"] != "") {
	$course_id = $_REQUEST["courseID"];
}

if (isset($_REQUEST["activityID"]) && $_REQUEST["activityID"] != "") {
	$activity_id = $_REQUEST["activityID"];
}

//set taxonomy
include("config/register.config.php");

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;

include("libraries/PFBC/Form.php");

$form = new Form("register-form-program");

$form->addElement(new Element\HTML('<div id="course-schedule">'));

$form->addElement(new Element\HTML('<div id="load-programs">'));
$query = "1 = 1";
$query .= " ORDER BY name ASC";
$program_options = array(
	"" => "- please select -"
);
$programs = $db->select("program", $query);
foreach ($programs as $program) {
	$program_options[$program['id']] = $program['name'];
}
$form->addElement(new Element\Select("Program", "ProgramID", $program_options, 
	array(
		"value" => $program_id,
		"required" => 1
	)
));
$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('<div id="load-locations">'));
$fields = "l.id, l.name, l.disabled, l.deleted";
$from = "location AS l";
$from .= ", location_program AS lp";
$where = "l.id = lp.location_id";
if ($program_id) {
	$where .= " AND lp.program_id = '".$program_id."'";
}
$where .= " AND (l.disabled IS NULL OR l.disabled = '0000-00-00 00:00:00')";
$where .= " AND (l.deleted IS NULL OR l.deleted = '0000-00-00 00:00:00')";
$order = " ORDER BY l.name ASC";
$locations = $db->select($from, $where." ".$order, "", $fields);
$location_options = array(
	"" => "- please select -"
);
foreach ($locations as $location) {
	$location_options[$location['id']] = $location['name'];
}
$form->addElement(new Element\Select("Location", "LocationID", $location_options, 
	array(
		"value" => $location_id,
		"required" => 1
	)
));
$form->addElement(new Element\HTML('</div>'));


$form->addElement(new Element\HTML('<div id="load-courses">'));
$fields = "c.*";
$from = "course AS c";
$where = "1 = 1";
if ($program_id) {
	$where .= " AND c.program_id = '".$program_id."'";
}
if ($location_id) {
	$where .= " AND c.location_id = '".$location_id."'";
}
$where .= " AND (c.disabled IS NULL OR c.disabled = '0000-00-00 00:00:00')";
$where .= " AND (c.deleted IS NULL OR c.deleted = '0000-00-00 00:00:00')";
$where .= " AND c.current_available > 0";
$where .= " AND NOW() BETWEEN c.registration_start_date AND c.registration_end_date";
$order = " ORDER BY c.name ASC";
$courses = $db->select($from, $where." ".$order, "", $fields);
$course_options = array(
	"" => "- please select -"
);
foreach ($courses as $course) {
	$course_options[$course['id']] = $course['name'];
}
$form->addElement(new Element\Select("Course", "CourseID", $course_options, 
	array(
		"value" => $course_id,
		"required" => 1
	)
));
$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('<div id="load-activities">'));
$activity_options = array(
	"" => "- please select -"
);
if ($course_id) {
	$fields = "ca.*, a.*";
	$from = "course_activity AS ca";
	$from .= ", activity AS a";
	$where = "1 = 1";
	$where .= " AND ca.activity_id = a.id";
	$where .= " AND ca.course_id = '".$course_id."'";
	$where .= " AND (ca.disabled IS NULL OR ca.disabled = '0000-00-00 00:00:00')";
	$where .= " AND (ca.deleted IS NULL OR ca.deleted = '0000-00-00 00:00:00')";
	$where .= " AND (ca.deleted IS NULL OR ca.deleted = '0000-00-00 00:00:00')";
	//$where .= " AND (la.disabled IS NULL OR la.disabled = '0000-00-00 00:00:00')";
	//$where .= " AND (la.deleted IS NULL OR la.deleted = '0000-00-00 00:00:00')";
	//$where .= " AND (a.deleted IS NULL OR a.deleted = '0000-00-00 00:00:00')";
	$order = " ORDER BY a.name ASC";
	//$order = " ORDER BY a.name ASC";
	$activities = $db->select($from, $where." ".$order, "", $fields);
	foreach ($activities as $activity) {
		$activity_options[$activity['id']] = $activity['name'];
	}
}
$form->addElement(new Element\Select("Activity", "ActivityID", $activity_options, 
	array(
		"value" => $activity_id,
		"required" => 1
	)
));
$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('</div>'));
$form->render();
?>