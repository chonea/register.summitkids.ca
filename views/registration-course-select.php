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

if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != "") {
	$program_id = $_REQUEST["programID"];
}

if (isset($_REQUEST["locationID"]) && $_REQUEST["locationID"] != "" && $program_id != '') {
	$location_id = $_REQUEST["locationID"];
}

if (isset($_REQUEST["courseID"]) && $_REQUEST["courseID"] != "" && $program_id != '' && $location_id != '') {
	$course_id = $_REQUEST["courseID"];
}

if (isset($_REQUEST["activityID"]) && $_REQUEST["activityID"] != "" && $program_id != '' && $location_id != '' && $course_id != '') {
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
$program_options = array("" => "- please select -");
$query = "1 = 1";
$query .= " ORDER BY name ASC";
if ($programs = $db->select("program", $query)) {
	if (!empty($programs)) {
		foreach ($programs as $program) {
			// check to see if this program has any current courses offered
			$fields = "c.id";
			$from = "course AS c";
			$where = "c.program_id = '".$program['id']."'";
			$where .= " AND (c.disabled IS NULL OR c.disabled = '0000-00-00 00:00:00')";
			$where .= " AND (c.canceled IS NULL OR c.canceled = '0000-00-00 00:00:00')";
			$where .= " AND (c.deleted IS NULL OR c.deleted = '0000-00-00 00:00:00')";
			$where .= " AND c.current_available > 0";
			$where .= " AND NOW() BETWEEN c.registration_start_date AND c.registration_end_date";
			if ($result = $db->select($from, $where, "", $fields)) {
				if (!empty($result)) {
					$program_options[$program['id']] = $program['name'];
				}
			}
		}
	} else {
		$location_id = '';
	}
}
if ($program_id != '' && !isset($program_options[$program_id])) {
	$program_id = '';
}
if ($program_id != '') {
	$query = "id = '".$program_id."'  LIMIT 0,1";
	if ($results = $db->select("program", $query)) {
		$program = $results[0];
		$form->addElement(new Element\Hidden("ProgramForm_Waivers", $program['form_waivers']));
		$form->addElement(new Element\Hidden("ProgramForm_CC_Authorization", $program['form_cc_authorization']));
		$form->addElement(new Element\Hidden("ProgramForm_EFT_Authorization", $program['form_eft_authorization']));
	} else {
		$form->addElement(new Element\Hidden("ProgramForm_Waivers", ""));
		$form->addElement(new Element\Hidden("ProgramForm_CC_Authorization", ""));
		$form->addElement(new Element\Hidden("ProgramForm_EFT_Authorization", ""));
	}
} else {
	$form->addElement(new Element\Hidden("ProgramForm_Waivers", ""));
	$form->addElement(new Element\Hidden("ProgramForm_CC_Authorization", ""));
	$form->addElement(new Element\Hidden("ProgramForm_EFT_Authorization", ""));
}
$form->addElement(new Element\Select("Program", "RegisterProgramID", $program_options, 
	array(
		"value" => $program_id,
		"required" => 1
	)
));
$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('<div id="load-locations">'));
$location_options = array("" => "- please select -");
if ($program_id != '') {
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
	if ($locations = $db->select($from, $where." ".$order, "", $fields)) {
		if (!empty($locations)) {
			foreach ($locations as $location) {
				// check to see if this location has any current courses offered
				$fields = "c.id";
				$from = "course AS c";
				$where = "c.location_id = '".$location['id']."'";
				if ($program_id) {
					$where .= " AND c.program_id = '".$program_id."'";
				}
				$where .= " AND (c.disabled IS NULL OR c.disabled = '0000-00-00 00:00:00')";
				$where .= " AND (c.canceled IS NULL OR c.canceled = '0000-00-00 00:00:00')";
				$where .= " AND (c.deleted IS NULL OR c.deleted = '0000-00-00 00:00:00')";
				$where .= " AND c.current_available > 0";
				$where .= " AND NOW() BETWEEN c.registration_start_date AND c.registration_end_date";
				if ($result = $db->select($from, $where, "", $fields)) {
					if (!empty($result)) {
						$location_options[$location['id']] = $location['name'];
					}
				}
			}
		} else {
			$location_id = '';
		}
	} else {
		$location_id = '';
	}
} else {
	$location_id = '';
}
if ($location_id != '' && !isset($location_options[$location_id])) {
	$location_id = '';
}
$location_parameters = array(
	"value" => $location_id,
	"required" => 1
);
if ($program_id == '') {
	$location_parameters["disabled"] = 1;
}
$form->addElement(new Element\Select("Location", "RegisterLocationID", $location_options, $location_parameters));
$form->addElement(new Element\HTML('</div>'));


$form->addElement(new Element\HTML('<div id="load-courses">'));
$course_options = array("" => "- please select -");
if ($location_id != '' && $program_id != '') {
	$fields = "c.*";
	$from = "course AS c";
	$where = "1 = 1";
	$where .= " AND c.program_id = '".$program_id."'";
	$where .= " AND c.location_id = '".$location_id."'";
	$where .= " AND (c.disabled IS NULL OR c.disabled = '0000-00-00 00:00:00')";
	$where .= " AND (c.canceled IS NULL OR c.canceled = '0000-00-00 00:00:00')";
	$where .= " AND (c.deleted IS NULL OR c.deleted = '0000-00-00 00:00:00')";
	$where .= " AND c.current_available > 0";
	$where .= " AND NOW() BETWEEN c.registration_start_date AND c.registration_end_date";
	$order = " ORDER BY c.name ASC";
	if ($courses = $db->select($from, $where." ".$order, "", $fields)) {
		if (!empty($courses)) {
			foreach ($courses as $course) {
				$course_options[$course['id']] = $course['name']." &nbsp;(".$course['current_available']." available)";
			}
		} else {
			$course_id = '';
		}
	} else {
		$course_id = '';
	}
} else {
	$course_id = '';
}
if ($course_id != '' && !isset($course_options[$course_id])) {
	$course_id = '';
}
$course_parameters = array(
	"value" => $course_id,
	"required" => 1
);
if ($location_id == '') {
	$course_parameters["disabled"] = 1;
}
$form->addElement(new Element\Select("Course", "RegisterCourseID", $course_options, $course_parameters));
$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('<div id="load-activities">'));
$activity_options = array("" => "- please select -");
if ($course_id != '') {
	$fields = "ca.*, a.*";
	$from = "course_activity AS ca";
	$from .= ", activity AS a";
	$where = "1 = 1";
	$where .= " AND ca.activity_id = a.id";
	$where .= " AND ca.course_id = '".$course_id."'";
	$where .= " AND (ca.disabled IS NULL OR ca.disabled = '0000-00-00 00:00:00')";
	$where .= " AND (ca.deleted IS NULL OR ca.deleted = '0000-00-00 00:00:00')";
	$where .= " AND (ca.deleted IS NULL OR ca.deleted = '0000-00-00 00:00:00')";
	$order = " ORDER BY a.name ASC";
	if ($activities = $db->select($from, $where." ".$order, "", $fields)) {
		if (!empty($activities)) {
			foreach ($activities as $activity) {
				$activity_options[$activity['id']] = $activity['name'];
			}
		} else {
			$activity_id = '';
		}
	} else {
		$activity_id = '';
	}
} else {
	$activity_id = '';
}
if ($activity_id != '' && !isset($activity_options[$activity_id])) {
	$activity_id = '';
}
$activity_parameters = array(
	"value" => $activity_id,
	"required" => 1
);
if ($course_id == '') {
	$activity_parameters["disabled"] = 1;
}
$form->addElement(new Element\Select("Activity", "RegisterActivityID", $activity_options, $activity_parameters));
$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('</div>'));

$form->render();
?>