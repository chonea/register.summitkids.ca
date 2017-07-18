<?php
error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

if (!isset($_REQUEST["locationID"]) || $_REQUEST["locationID"] == "") {
//	echo 'Please select program.';
	exit();
}

$course = array();
$selected_programs = array();
$program_id = '';

if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != '') {
	$program_id = $_REQUEST["programID"];
} elseif (isset($_REQUEST["courseID"]) && $_REQUEST["courseID"] != '') {
	$query = "id = '".$_REQUEST["courseID"]."'";
	if ($result = $db->select("course", "id = '".$_REQUEST["courseID"]."'")) {
		$course = $result[0];
		$program_id = $course['program_id'];
	}
}

$query = "location_id = '".$_REQUEST["locationID"]."'";
if ($location_programs = $db->select("location_program", $query)) {
	foreach ($location_programs as $program) {
		$selected_programs[] = $program["program_id"];
	}
}
if (count($selected_programs) == 0) {
//	echo 'No programs have been selected for this location.';
	exit();
}

$query = "1 = 1";
$query .= " ORDER BY name ASC";
$programs = $db->select("program", $query);

//add the loadSaveData function
include("loadsavedata.php");

//set taxonomy
include("config/register.config.php");

echo '<option value="">- please select -</option>';

foreach ($programs as $program) {
	if (in_array($program['id'], $selected_programs)) {
		echo '<option value="'.$program['id'].'"';
		if ($program['id'] == $program_id) echo ' selected';
		echo '>'.$program['name'].'</option>';
	}
}
?>