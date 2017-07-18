<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$save_activity = array();

if (isset($_REQUEST["activityID"])) {
	$query = "id = '".$_REQUEST["activityID"]."'";
	if (!$result = $db->select("activity", "id = '".$_REQUEST["activityID"]."'")) {
		$save_activity = '';
	} else {
		$save_activity = $result;
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
	<div id="activity-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['activityID'])) {
?>
			<h2>Add Activity</h2>
			<h3>General Information</h3>
<?php
	} else {
?>
			<h2>Edit Activity</h2>
			<h3>General Information for <?php echo loadSaveData("activity", "name", 0); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("activity-form");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "activity-form"));
		$form->addElement(new Element\Hidden("activityID", loadSaveData("activity", "id", 0)));
		
		// Activity information
		$form->addElement(new Element\HTML('<fieldset id="activity">'));
//			$form->addElement(new Element\HTML('<legend>Activity</legend>'));

			$form->addElement(new Element\HTML('<fieldset id="activity-profile">'));
				$form->addElement(new Element\Textbox("Activity Name", "ActivityName", array(
					"value" => loadSaveData("activity", "name", 0),
					"required" => 1
					)
				));
				$query = "1 = 1";
				$query .= " ORDER BY name ASC";
				$programs = $db->select("program", $query);
				$program_options = array(
					"" => "- please select -"
				);
				foreach ($programs as $program) {
					$program_options[$program['id']] = $program['name'];
				}
				$form->addElement(new Element\Select("Program", "ActivityProgramID", $program_options, 
					array(
						"value" => loadSaveData("activity", "program_id", 0),
						"required" => 1
					)
				));
				$form->addElement(new Element\Textarea("Description", "ActivityDescription", array(
					"class" => "span12",
					"basic" => 1,
					"value" => loadSaveData("activity", "description", 0)
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = 'activities.php';"
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
