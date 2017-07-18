<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$save_guardian = array();
$user_id = $_SESSION['user_id'];

if (isset($_REQUEST["guardianID"])) {
	$query = "id = '".$_REQUEST["guardianID"]."'";
	if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
		$query .= " AND user_id = '".$_SESSION['user_id']."'";
	}
	if ($result = $db->select("guardian", "id = '".$_REQUEST["guardianID"]."'")) {
		$save_guardian = $result[0];
		$user_id = $save_guardian['user_id'];
	} else {
		$save_guardian = '';
	}
} elseif (($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager") && (isset($_REQUEST["userID"]) && $_REQUEST["userID"] != "")) {
	$user_id = $_REQUEST["userID"];
}

if ($user_id == '') {
	// missing user id
	echo ('Invalid user ID or you are not authorized to view this information.');
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
	<div id="guardian-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['guardianID'])) {
?>
			<h2>Add Guardian Profile</h2>
			<h3>General Information</h3>
<?php
	} else {
?>
			<h2>Edit Guardian Profile</h2>
			<h3>General Information for <?php echo loadSaveData("guardian", "first_name", 0); ?> <?php echo loadSaveData("guardian", "last_name", 0); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("guardian-form");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "guardian-form"));
		$form->addElement(new Element\Hidden("guardianID", loadSaveData("guardian", "id", 0)));
		$form->addElement(new Element\Hidden("userID", $user_id));
		
		// Guardian's information
		$form->addElement(new Element\HTML('<fieldset id="guardian">'));
			$form->addElement(new Element\HTML('<legend>Guardian Profile</legend>'));

			$form->addElement(new Element\HTML('<fieldset id="guardian-profile">'));
				$form->addElement(new Element\Textbox("First Name", "GuardianFirstName", array(
					"value" => loadSaveData("guardian", "first_name", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "GuardianLastName", array(
					"value" => loadSaveData("guardian", "last_name", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Email("Email Address", "GuardianEmail", array(
					"value" => loadSaveData("guardian", "email", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Phone("Cell Phone", "GuardianCellPhone", array(
					"value" => loadSaveData("guardian", "cell_phone", 0)
					)
				));
				$form->addElement(new Element\Phone("Home Phone", "GuardianHomePhone", array(
					"value" => loadSaveData("guardian", "home_phone", 0)
					)
				));
				$form->addElement(new Element\Phone("Daytime Phone", "GuardianDaytimePhone", array(
					"value" => loadSaveData("guardian", "daytime_phone", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Address", "GuardianAddress", array(
					"value" => loadSaveData("guardian", "address", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("City", "GuardianCity", array(
					"value" => loadSaveData("guardian", "city", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Province("Province", "GuardianProvince", array(
					"value" => loadSaveData("guardian", "province", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Postal Code", "GuardianPostalCode", array(
					"value" => loadSaveData("guardian", "postal_code", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime Address", "GuardianDaytimeAddress", array(
					"value" => loadSaveData("guardian", "daytime_address", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime City", "GuardianDaytimeCity", array(
					"value" => loadSaveData("guardian", "daytime_city", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Province("Daytime Province", "GuardianDaytimeProvince", array(
					"value" => loadSaveData("guardian", "daytime_province", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime Postal Code", "GuardianDaytimePostalCode", array(
					"value" => loadSaveData("guardian", "daytime_postal_code", 0),
					"required" => 1
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = '/';"
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
