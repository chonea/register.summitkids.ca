<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$save_program = array();
$save_activities = array();

if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != "") {
	$query = "id = '".$_REQUEST["programID"]."'";
	if ($result = $db->select("program", "id = '".$_REQUEST["programID"]."'")) {
		$save_program = $result;
		$query = "program_id = '".$_REQUEST["programID"]."'";
		if ($activities = $db->select("activity", $query)) {
			foreach ($activities as $activity) {
				$save_activities[] = $activity["id"];
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
	<div id="program-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['programID'])) {
?>
			<h2>Add Program</h2>
			<h3>General Information</h3>
<?php
	} else {
?>
			<h2>Edit Program</h2>
			<h3>General Information for <?php echo loadSaveData("program", "name", 0); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("program-form");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
			"enctype" => "multipart/form-data",
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "program-form"));
		$form->addElement(new Element\Hidden("programID", loadSaveData("program", "id", 0)));
		
		// Program information
		$form->addElement(new Element\HTML('<fieldset id="program">'));
//			$form->addElement(new Element\HTML('<legend>Program</legend>'));

			$form->addElement(new Element\HTML('<fieldset id="program-profile">'));
				$form->addElement(new Element\Textbox("Program Name", "ProgramName", array(
					"value" => loadSaveData("program", "name", 0),
					"required" => 1
					)
				));
/*
				$form->addElement(new Element\Textbox("3-Letter Abbreviation", "ProgramAbbreviation", array(
					"value" => loadSaveData("program", "abbreviation", 0),
					"maxlength" => "3",
					"required" => 1
					)
				));
*/
				$form->addElement(new Element\Url("Web URL", "ProgramURL", array(
					"value" => loadSaveData("program", "web_url", 0)
					)
				));
				$form->addElement(new Element\Textarea("Description", "ProgramDescription", array(
					"class" => "span12",
					"value" => loadSaveData("program", "description", 0)
					)
				));
//				$form->addElement(new Element\File("Image", "Image", array(
//					"value" => loadSaveData("program", "image", 0)
//					)
//				));
			$form->addElement(new Element\HTML('</fieldset>'));
/*
			$form->addElement(new Element\HTML('<fieldset id="program-activities">'));
				$query = "1 = 1";
				if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != "") {
					$query .= " AND program_id = ".$_REQUEST["programID"];
				}
				$query .= " ORDER BY name ASC";
				$activities = $db->select("activity", $query);
				$activity_options = array();
				foreach ($activities as $activity) {
					$activity_options[$activity['id']] = $activity['name'];
				}
				$form->addElement(new Element\Checkbox("Available Activities", "ProgramActivities", $activity_options, 
					array(
						"value" => $save_activities,
						"required" => 0
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
*/
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = 'programs.php';"
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
