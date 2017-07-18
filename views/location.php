<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$save_location = array();
$save_programs = array();
$save_activities = array();

if ($_SESSION['user_role'] != "admin") {
	echo "You are not authorized to complete this action.";
	exit;
}

if (isset($_REQUEST["locationID"]) && $_REQUEST["locationID"] != '') {
	$query = "id = '".$_REQUEST["locationID"]."'";
	$query .= " AND user_id = '".$_SESSION['user_id']."'";
	if ($result = $db->select("location", "id = '".$_REQUEST["locationID"]."'")) {
		$save_location = $result;
		$query = "location_id = '".$_REQUEST["locationID"]."'";
		if ($programs = $db->select("location_program", $query)) {
			foreach ($programs as $program) {
				$save_programs[] = $program["program_id"];
				$query = "location_id = '".$_REQUEST["locationID"]."'";
				$query .= " AND program_id = '".$program["program_id"]."'";
				if ($activities = $db->select("location_activity", $query)) {
					foreach ($activities as $activity) {
						$save_activities[] = $activity["activity_id"];
					}
				}
			}
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
	<div id="location-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['locationID'])) {
?>
			<h2>Add Location</h2>
			<h3>General Information</h3>
<?php
	} else {
?>
			<h2>Edit Location</h2>
			<h3>General Information for <?php echo loadSaveData("location", "name", 0); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("location-form");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "location-form"));
		$form->addElement(new Element\Hidden("locationID", loadSaveData("location", "id", 0)));
		
		// Location information
		$form->addElement(new Element\HTML('<fieldset id="location">'));
//			$form->addElement(new Element\HTML('<legend>Location</legend>'));

			$form->addElement(new Element\HTML('<fieldset id="location-general" class="span4" style="float: left;">'));
				$form->addElement(new Element\Textbox("Location Name", "LocationName", array(
					"value" => loadSaveData("location", "name", 0),
					"required" => 1
					)
				));
/*
				$form->addElement(new Element\Textbox("3-Letter Abbreviation", "LocationAbbreviation", array(
					"value" => loadSaveData("location", "abbreviation", 0),
					"maxlength" => "3",
					"required" => 1
					)
				));

*/				$form->addElement(new Element\Textbox("Address", "LocationAddress", array(
					"value" => loadSaveData("location", "address", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("City", "LocationCity", array(
					"value" => loadSaveData("location", "city", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Province("Province", "LocationProvince", array(
					"value" => loadSaveData("location", "province", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Postal Code", "LocationPostalCode", array(
					"value" => loadSaveData("location", "postal_code", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Email("Email Address", "LocationEmail", array(
					"value" => loadSaveData("location", "email", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Phone("Phone", "LocationPhone", array(
					"value" => loadSaveData("location", "phone", 0)
					)
				));
				$form->addElement(new Element\Phone("Fax", "LocationFax", array(
					"value" => loadSaveData("location", "fax", 0)
					)
				));
				$form->addElement(new Element\Url("Web URL", "LocationURL", array(
					"value" => loadSaveData("location", "web_url", 0)
					)
				));
				$form->addElement(new Element\Textarea("Description", "LocationDescription", array(
					"class" => "span12",
					"value" => loadSaveData("location", "description", 0)
					)
				));
				$form->addElement(new Element\Textarea("Teaser", "LocationTeaser", array(
					"class" => "span12",
					"value" => loadSaveData("location", "teaser", 0)
					)
				));
/*
			$form->addElement(new Element\HTML('</fieldset>'));

			$form->addElement(new Element\HTML('<fieldset id="location-programs">'));
				$form->addElement(new Element\HTML('<h3>Available Programs and Activities</h3>'));
				$query = "1 = 1";
				$query .= " ORDER BY name ASC";
				$programs = $db->select("program", $query);
				foreach ($programs as $program) {
					$query = "1 = 1";
					$query .= " AND program_id = '".$program['id']."'";
					$query .= " ORDER BY name ASC";
					if ($activities = $db->select("activity", $query)) {
						$form->addElement(new Element\HTML('<fieldset id="program-'.$program['id'].'" class="location-programs span5">'));
							$program_options = array();
							$program_options[$program['id']] = $program['name'];
							$form->addElement(new Element\Checkbox("Program", "LocationPrograms", $program_options, 
								array(
									"class" => "location-program",
									"value" => $save_programs,
									"required" => 0
								)
							));
							$form->addElement(new Element\HTML('</fieldset>'));
							$activity_options = array();
							foreach ($activities as $activity) {
								$activity_options[$activity['id']] = $activity['name'];
							}
							$form->addElement(new Element\HTML('<fieldset id="program-'.$program['id'].'-activities" class="location-activities span5">'));
								$form->addElement(new Element\Checkbox("Activities", "LocationActivities", $activity_options, 
									array(
										"class" => "location-activity",
										"value" => $save_activities,
										"required" => 0,
										"disabled" => "",
										"style" => "display: table-cell;"
									)
								));
						$form->addElement(new Element\HTML('</fieldset>'));
					}
				}
			$form->addElement(new Element\HTML('</fieldset>'));

			$form->addElement(new Element\HTML('<fieldset id="location-programs">'));
				$form->addElement(new Element\HTML('<h3>Available Programs and Activities</h3>'));
				$query = "1 = 1";
				$query .= " ORDER BY name ASC";
				$programs = $db->select("program", $query);
				foreach ($programs as $program) {
					$query = "1 = 1";
					$query .= " AND program_id = '".$program['id']."'";
					$query .= " ORDER BY name ASC";
					if ($activities = $db->select("activity", $query)) {
							$activity_options = array();
							foreach ($activities as $activity) {
								$activity_options[$activity['id']] = $program['name']." &rarr; ".$activity['name'];
							}
//							$form->addElement(new Element\HTML('<fieldset id="program-'.$program['id'].'-activities" class="location-activities">'));
								$form->addElement(new Element\Checkbox("", "LocationActivities", $activity_options, 
									array(
										"class" => "location-activity",
										"value" => $save_activities
									)
								));
//						$form->addElement(new Element\HTML('</fieldset>'));
					}
				}
			$form->addElement(new Element\HTML('</fieldset>'));
*/
			$form->addElement(new Element\HTML('</fieldset>'));

			$form->addElement(new Element\HTML('<fieldset id="location-activities" class="span8" style="float: left;">'));
				$query = "1 = 1";
				$query .= " ORDER BY name ASC";
				$programs = $db->select("program", $query);
				$program_options = array();
				foreach ($programs as $program) {
					$program_options[$program['id']] = $program['name'];
				}
				$form->addElement(new Element\Checkbox("Available Programs", "LocationPrograms", $program_options, 
					array(
						"class" => "location-program",
						"value" => $save_programs
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));

/*
			$form->addElement(new Element\HTML('<fieldset id="location-activities">'));
				foreach ($program_options as $id => $name) {
					$query = "1 = 1";
					$query .= " AND program_id = '".$key."'";
					$query .= " ORDER BY name ASC";
					$activities = $db->select("activity", $query);
					$activity_options = array();
					foreach ($activities as $activity) {
						$activity_options[$activity['id']] = $activity['name'];
					}
					$form->addElement(new Element\HTML('<fieldset id="location-activities-">'));
					$form->addElement(new Element\Checkbox("Available Activities", "LocationActivities", $activity_options, 
						array(
							"class" => "location-activity",
							"value" => $save_activities,
							"required" => 0
						)
					));
				}
			$form->addElement(new Element\HTML('</fieldset>'));
*/
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = 'locations.php';"
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
