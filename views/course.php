<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

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
}

//add the loadSaveData function
include("loadsavedata.php");

//set taxonomy
include("config/register.config.php");

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;

include("libraries/PFBC/Form.php");

if(isset($_POST["form"])) {
	Form::isValid($_POST["form"]);
	header("Location: " . $_SERVER["PHP_SELF"]);
	exit();	
}

include("header.php");
//include("sidebar.php");
$version = file_get_contents("version");

?>

<div class="span12">
	<div id="course-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['courseID'])) {
?>
			<h2>Add Course</h2>
			<h3>Course Information</h3>
<?php
	} else {
?>
			<h2>Edit Course</h2>
			<h3>Course Information for <?php echo loadSaveData("course", "name", 0); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("course-form");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "course-form"));
		$form->addElement(new Element\Hidden("courseID", loadSaveData("course", "id", 0)));
		
		// Course information
		$form->addElement(new Element\HTML('<fieldset id="course">'));
//			$form->addElement(new Element\HTML('<legend>Course</legend>'));

			$form->addElement(new Element\HTML('<div class="row-fluid">'));
			$form->addElement(new Element\HTML('<fieldset id="course-general" class="span4" style="float: left;">'));
				$form->addElement(new Element\HTML('<legend>General</legend>'));
				$form->addElement(new Element\Textbox("Course Name", "CourseName", array(
					"value" => loadSaveData("course", "name", 0),
					"required" => 1
					)
				));

				$query = "(disabled = '' OR ISNULL(disabled))";
				$query .= " AND (deleted = '' OR ISNULL(deleted))";
//				$query = "1 = 1";
				$query .= " ORDER BY name ASC";
				$location_options = array(
					"" => "- please select -"
				);
				$locations = $db->select("location", $query);
				foreach ($locations as $location) {
					$location_options[$location['id']] = $location['name'];
				}
				$form->addElement(new Element\Select("Location", "CourseLocationID", $location_options, 
					array(
						"value" => loadSaveData("course", "location_id", 0),
						"required" => 1
					)
				));

				$grade_options = array(
					"" => "- please select -",
					"any" => "Any"
				);
				foreach ($CFG['grades'] as $key => $value) {
					$grade_options[$key] = $value;
				}
				$form->addElement(new Element\Select("Grade", "CourseGrade", $grade_options,
					array(
						"value" => loadSaveData("course", "grade", 0),
						"required" => 1
					)
				));

				$form->addElement(new Element\HTML('<div id="load-programs">'));
				$query = "1 = 1";
				$query .= " ORDER BY name ASC";
				$program_options = array(
//					"" => "- please select -"
				);
//				$programs = $db->select("program", $query);
//				foreach ($programs as $program) {
//					$program_options[$program['id']] = $program['name'];
//				}
				$form->addElement(new Element\Select("Program", "CourseProgramID", $program_options, 
					array(
//						"value" => loadSaveData("course", "program_id", 0),
						"required" => 1
					)
				));
				$form->addElement(new Element\HTML('</div>'));
			$form->addElement(new Element\HTML('</fieldset>'));

			$form->addElement(new Element\HTML('<fieldset id="course-description" class="span8" style="float: left;">'));
				$form->addElement(new Element\HTML('<legend>Description</legend>'));
				$form->addElement(new Element\Textarea("Description", "CourseDescription", array(
					"class" => "span12",
					"basic" => 1,
					"value" => loadSaveData("course", "description", 0)
					)
				));

				$form->addElement(new Element\Textarea("Special Instructions", "CourseSpecialInstructions", array(
					"class" => "span12",
					"basic" => 1,
					"value" => loadSaveData("course", "special_instructions", 0)
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
			$form->addElement(new Element\HTML('</div>'));

			$form->addElement(new Element\HTML('<div class="row-fluid">'));
			$form->addElement(new Element\HTML('<fieldset id="course-stats" class="span4" style="float: left;">'));
				$form->addElement(new Element\HTML('<legend>Schedule</legend>'));

				if (loadSaveData("course", "start_date", 0) != '' && loadSaveData("course", "start_date", 0) != "0000-00-00 00:00:00") {
					//$datetime = strftime('%Y-%m-%dT%H:%M:%S', strtotime(loadSaveData("course", "start_date", 0)));
					$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime(loadSaveData("course", "start_date", 0))));
					$datetime = $date->format('m/d/Y g:i a');
				} else {
					$datetime = "";
				}
				$form->addElement(new Element\Textbox("Start Date", "CourseStartDate", array(
					"value" => $datetime,
					"class" => "datetimepicker",
					"required" => 1
				)));
				
				if (loadSaveData("course", "end_date", 0) != '' && loadSaveData("course", "end_date", 0) != "0000-00-00 00:00:00") {
					//$datetime = strftime('%Y-%m-%dT%H:%M:%S', strtotime(loadSaveData("course", "end_date", 0)));
					$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime(loadSaveData("course", "end_date", 0))));
					$datetime = $date->format('m/d/Y g:i a');
				} else {
					$datetime = "";
				}
				$form->addElement(new Element\Textbox("End Date", "CourseEndDate", array(
					"value" => $datetime,
					"class" => "datetimepicker",
					"required" => 1
				)));
				
				if (loadSaveData("course", "registration_start_date", 0) != '' && loadSaveData("course", "registration_start_date", 0) != "0000-00-00 00:00:00") {
					//$datetime = strftime('%Y-%m-%dT%H:%M:%S', strtotime(loadSaveData("course", "registration_start_date", 0)));
					$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime(loadSaveData("course", "registration_start_date", 0))));
					$datetime = $date->format('m/d/Y g:i a');
				} else {
					$datetime = "";
				}
				$form->addElement(new Element\Textbox("Registration Start Date", "CourseRegistrationStartDate", array(
					"value" => $datetime,
					"class" => "datetimepicker",
					"required" => 1
				)));
				
				if (loadSaveData("course", "registration_end_date", 0) != '' && loadSaveData("course", "registration_end_date", 0) != "0000-00-00 00:00:00") {
					//$datetime = strftime('%Y-%m-%dT%H:%M:%S', strtotime(loadSaveData("course", "registration_end_date", 0)));
					$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime(loadSaveData("course", "registration_end_date", 0))));
					$datetime = $date->format('m/d/Y g:i a');
				} else {
					$datetime = "";
				}
				$form->addElement(new Element\Textbox("Registration End Date", "CourseRegistrationEndDate", array(
					"value" => $datetime,
					"class" => "datetimepicker",
					"required" => 1
				)));

				$max_available = loadSaveData("course", "max_available", 0);
				$form->addElement(new Element\Number("Maximum Availability", "CourseMaxAvailable", array(
					"value" => $max_available,
					"required" => 1
					)
				));
				
				$reserve_available = loadSaveData("course", "reserve_available", 0);
				$form->addElement(new Element\Number("Reserve Availability", "CourseReserveAvailable", array(
					"value" => $reserve_available,
					"required" => 1
					)
				));

				if (loadSaveData("course", "id", 0) != "") {
					$where = "course_id = '".loadSaveData("course", "id", 0)."'";
					$where .= " AND (accepted IS NOT NULL AND accepted <> '0000-00-00 00:00:00')";
					$where .= " AND (deleted IS NULL OR deleted = '0000-00-00 00:00:00')";
					if ($result = $db->select("register", $where)) {
						$current_registrations = count($result);
					} else {
						$current_registrations = 0;
					}
				} else {
					$current_registrations = 0;
				}
				$form->addElement(new Element\Textbox("Current Registrations", "CourseCurrentRegistrations", array(
					"value" => $current_registrations,
					"disabled" => 1
					)
				));

				$current_available = $max_available - $reserve_available - $current_registrations;
				$form->addElement(new Element\Textbox("Current Availability", "CourseCurrentAvailable", array(
					"value" => $current_available,
					"disabled" => 1
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));

			$form->addElement(new Element\HTML('<fieldset id="course-activities" class="span8" style="float: left;">'));
				$form->addElement(new Element\HTML('<legend>Available Activities</legend>'));
				$form->addElement(new Element\HTML('<div id="load-activities"></div>'));
			$form->addElement(new Element\HTML('</fieldset>'));
			$form->addElement(new Element\HTML('</div">'));
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = 'courses.php';"
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
