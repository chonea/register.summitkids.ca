<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$user_id = $_SESSION['user_id'];
$save_registerID = '';
$save_registration = array();
$save_child = array();
$save_guardians = array();
$save_emergency_contacts = array();
$save_authorized_contacts = array();
$save_restricted_contacts = array();

if (isset($_REQUEST["registerID"]) && $_REQUEST["registerID"] != '') {
	$save_registerID = $_REQUEST["registerID"];
	$query = "id = '".$save_registerID."'";
	// block non-admins from accessing other registrations
	if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
		$query .= " AND user_id = '".$_SESSION['user_id']."'";
	}
	$results = $db->select("register", $query);
	if (!$save_registration = $results[0]) {
		// doesn't exist or it isn't this user's application
		echo ('Invalid registration ID or you are not authorized to view this information.');
		exit();
	}
	$user_id = $save_registration['user_id'];
	if ($results = $db->select("child", "id = '".$save_registration['child_id']."'")) {
		$save_child = $results[0];
		// find guardians in the relationship table
		$relationships = $db->select("relationship", "child_id = '".$save_child['id']."'");
		foreach ($relationships as $relationship) {
			// now go get the guardian record for that relationship
			$guardians = $db->select("guardian", "id = '".$relationship['guardian_id']."'");
			$guardian = $guardians[0];
			$guardian['relationship'] = stripslashes($relationship['relationship']);
			$guardian['lives_with'] = $relationship['lives_with'];
			$save_guardians[] = $guardian;
		}
		$save_emergency_contacts = $db->select("contact", "child_id = '".$save_child['id']."' AND type = 'emergency'");
		$save_authorized_contacts = $db->select("contact", "child_id = '".$save_child['id']."' AND type = 'authorized'");
		$save_restricted_contacts = $db->select("contact", "child_id = '".$save_child['id']."' AND type = 'restricted'");
	}
} elseif (($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager") && (isset($_REQUEST["userID"]) && $_REQUEST["userID"] != "")) {
	$user_id = $_REQUEST["userID"];
}
//die("user_id = ".$user_id);
if ($user_id != '') {
	$all_children = $db->select("child", "user_id = '".$user_id."'");
	$all_guardians = $db->select("guardian", "user_id = '".$user_id."'");
} else {
	$all_children = array();
	$all_guardians = array();
}

//add the loadSaveData function
include("loadsavedata.php");

//set taxonomy
include("config/register.config.php");

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;

include("libraries/PFBC/Form.php");

//die(getcwd());
/*
if(isset($_POST["form"])) {
	if(Form::isValid($_POST["form"])) {
		header("Content-type: application/json");
		echo file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . urlencode($_POST["Address"]) . "&sensor=false");
	}	else {
		Form::renderAjaxErrorResponse($_POST["form"]);
	}
	exit();
}
*/
/*
if (isset($_POST["form"])) {
	if (Form::isValid($_POST["form"])) {
		header("Content-type: application/json");
		echo file_get_contents("ajax-location.php");
	}	else {
		Form::renderAjaxErrorResponse($_POST["form"]);
	}
	exit();
}
*/

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

	<div id="registration-page-program" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['registerID'])) {
?>
			<h2>New Registration</h2>
<?php
	} else {
?>
			<h2>Edit Application</h2>
<?php
	}
?>
			<h3>Course Selection</h3>
		</div>
		<div class="ajax-response"></div>

<?php
		// Program form
		$form = new Form("register-form-program");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		));
		$form->addElement(new Element\Hidden("form", "register-form-program"));
		$form->addElement(new Element\Hidden("registerID", loadSaveData("registration", "id")));
		$form->addElement(new Element\Hidden("programID", loadSaveData("registration", "program_id")));
		$form->addElement(new Element\Hidden("locationID", loadSaveData("registration", "location_id")));
		$form->addElement(new Element\Hidden("courseID", loadSaveData("registration", "course_id")));
		$form->addElement(new Element\Hidden("activityID", loadSaveData("registration", "activity_id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
		$form->addElement(new Element\HTML('<div id="course-schedule">'));
		$form->addElement(new Element\HTML('</div>'));
			
		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = '/';"
		)));
		$form->addElement(new Element\Button("Save and Continue →"));
		$form->render();

?>
	</div>

	<div id="registration-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['registerID'])) {
?>
			<h2>New Registration</h2>
<?php
	} else {
?>
			<h2>Edit Application</h2>
<?php
	}
?>
			<h3>General Information</h3>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("register-form-general");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "register-form-general"));
		$form->addElement(new Element\Hidden("registerID", loadSaveData("registration", "id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
		$form->addElement(new Element\Hidden("childID", loadSaveData("child", "id")));
		$form->addElement(new Element\Hidden("guardianOneID", loadSaveData("guardians", "id", 0)));
		$form->addElement(new Element\Hidden("guardianTwoID",loadSaveData("guardians", "id", 1)));
		$form->addElement(new Element\Hidden("ChildPhoto", loadSaveData("child", "photo")));

		// Child's information
		$form->addElement(new Element\HTML('<fieldset id="child">'));
			$form->addElement(new Element\HTML('<legend>Child Info</legend>'));

			$children_array = array(
				"" => "- please select -",
				"add-child" => "Add New Child"
			);
			foreach ($all_children as $child) {
				$children_array[$child['id']] = $child['first_name']." ".$child['last_name'];
			}
			$form->addElement(new Element\Select("Select Child", "SelectChild", $children_array, array(
				"value" => loadSaveData("child", "id"),
				"required" => 1
				)
			));
	
			$form->addElement(new Element\HTML('<div id="child-information" class="hidden-content">'));
				$form->addElement(new Element\Textbox("First Name", "ChildFirstName", array(
					"value" => loadSaveData("child", "first_name"),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "ChildLastName", array(
					"value" => loadSaveData("child", "last_name"),
					"required" => 1
					)
				));

				if (loadSaveData("child", "birth_date") != '' && loadSaveData("child", "birth_date") != "0000-00-00 00:00:00") {
					$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime(loadSaveData("child", "birth_date"))));
					$datetime = $date->format('m/d/Y');
				} else {
					$datetime = "";
				}
				$form->addElement(new Element\Textbox("Birth Date", "ChildBirthDate", array(
					"value" => $datetime,
					"class" => "datepicker",
					"required" => 1
				)));

				$form->addElement(new Element\Radio("Gender", "ChildGender", array("male" => "Boy", "female" => "Girl"), array(
					"value" => loadSaveData("child", "gender"),
					"required" => 1
					)
				));
	
				// Child photo
	//			$form->addElement(new Element\File("Child Photo", "ChildPhoto", array("required" => 1)));
				$form->addElement(new Element\HTML('<div class="control-group">'));
					$form->addElement(new Element\HTML('<label class="control-label">'));
	//				$form->addElement(new Element\HTML('<span class="required">*</span> '));
					$form->addElement(new Element\HTML('Child Photo'));
					$form->addElement(new Element\HTML('</label>'));
					$form->addElement(new Element\HTML('<div id="register-form-general-child-photo-imageFile" class="controls">'));
					if (loadSaveData("child", "photo")) {
						$form->addElement(new Element\HTML('<img src="'.loadSaveData("child", "photo").'" />'));
					}
					$form->addElement(new Element\HTML('</div>'));
					$form->addElement(new Element\HTML('<div class="controls">'));
						$form->addElement(new Element\HTML('<div id="register-form-general-child-photo"><noscript><p>Please enable JavaScript to use the file uploader.</p></noscript></div>'));
						$form->addElement(new Element\HTML('<div><span class="help-block">Types .jpg, .gif, or .png under 3MB only, please. <strong>We must have a recognizable photo of your child on file. If a photo is not provided here with this form, a physical photo must be provided.</strong></span></div>'));
					$form->addElement(new Element\HTML('</div>'));
				$form->addElement(new Element\HTML('</div>'));
			$form->addElement(new Element\HTML('</div>'));

		$form->addElement(new Element\HTML('</fieldset>'));
		
		$form->addElement(new Element\HTML('<fieldset id="guardians">'));
			// Mom's information
			$form->addElement(new Element\HTML('<fieldset id="guardian-one">'));
				$form->addElement(new Element\HTML('<legend>Guardian One</legend>'));

				$guardian_array = array(
					"" => "- please select -",
					"add-guardian" => "Add New Guardian"
				);
				foreach ($all_guardians as $guardian) {
					$guardian_array[$guardian['id']] = $guardian['first_name']." ".$guardian['last_name'];
				}
				$form->addElement(new Element\Select("Select Guardian One", "SelectGuardian1", $guardian_array, array(
					"value" => loadSaveData("guardians", "id", 0),
					"required" => 1
					)
				));
		
				$form->addElement(new Element\HTML('<div id="guardian-1-information" class="hidden-content">'));
					$form->addElement(new Element\HTML('<fieldset id="guardian-one-applicable">'));
						$form->addElement(new Element\Textbox("First Name", "GuardianOneFirstName", array(
							"value" => loadSaveData("guardians", "first_name", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Last Name", "GuardianOneLastName", array(
							"value" => loadSaveData("guardians", "last_name", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Relation to Child", "GuardianOneRelationship", array(
							"value" => loadSaveData("guardians", "relationship", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Radio("Lives With?", "GuardianOneLivesWith", array("yes" => "Yes", "no" => "No"), array(
							"value" => loadSaveData("guardians", "lives_with", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Email("Email Address", "GuardianOneEmail", array(
							"value" => loadSaveData("guardians", "email", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Phone("Cell Phone", "GuardianOneCellPhone", array(
							"value" => loadSaveData("guardians", "cell_phone", 0)
							)
						));
						$form->addElement(new Element\Phone("Home Phone", "GuardianOneHomePhone", array(
							"value" => loadSaveData("guardians", "home_phone", 0)
							)
						));
						$form->addElement(new Element\Phone("Daytime Phone", "GuardianOneDaytimePhone", array(
							"value" => loadSaveData("guardians", "daytime_phone", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Address", "GuardianOneAddress", array(
							"value" => loadSaveData("guardians", "address", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("City", "GuardianOneCity", array(
							"value" => loadSaveData("guardians", "city", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Province("Province", "GuardianOneProvince", array(
							"value" => loadSaveData("guardians", "province", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Postal Code", "GuardianOnePostalCode", array(
							"value" => loadSaveData("guardians", "postal_code", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Daytime Address", "GuardianOneDaytimeAddress", array(
							"value" => loadSaveData("guardians", "daytime_address", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Daytime City", "GuardianOneDaytimeCity", array(
							"value" => loadSaveData("guardians", "daytime_city", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Province("Daytime Province", "GuardianOneDaytimeProvince", array(
							"value" => loadSaveData("guardians", "daytime_province", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Daytime Postal Code", "GuardianOneDaytimePostalCode", array(
							"value" => loadSaveData("guardians", "daytime_postal_code", 0),
							"required" => 1
							)
						));
						$form->addElement(new Element\Checkbox("Correct?", "GuardianOneCorrect", array("yes" => "I affirm that this information is correct."), 
							array(
								"value" => array(),
								"required" => 1,
								"longDesc" => "Please confirm the above information before continuing."
							)
						));
					$form->addElement(new Element\HTML('</fieldset>'));
				$form->addElement(new Element\HTML('</div>'));
			$form->addElement(new Element\HTML('</fieldset>'));

			// Dad's information
			$form->addElement(new Element\HTML('<fieldset id="guardian-two">'));
				$form->addElement(new Element\HTML('<legend>Guardian Two</legend>'));
				$guardian_array = array(
					"not-applicable" => "Not Applicable",
					"add-guardian" => "Add New Guardian"
				);
				foreach ($all_guardians as $guardian) {
					$guardian_array[$guardian['id']] = $guardian['first_name']." ".$guardian['last_name'];
				}
				$form->addElement(new Element\Select("Select Guardian Two", "SelectGuardian2", $guardian_array, array(
					"value" => loadSaveData("guardians", "id", 1),
					"required" => 1
					)
				));

				$form->addElement(new Element\HTML('<div id="guardian-2-information" class="hidden-content">'));
					$form->addElement(new Element\HTML('<fieldset id="guardian-two-applicable">'));
						$form->addElement(new Element\Textbox("First Name", "GuardianTwoFirstName", array(
							"value" => loadSaveData("guardians", "first_name", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Last Name", "GuardianTwoLastName", array(
							"value" => loadSaveData("guardians", "last_name", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Relation to Child", "GuardianTwoRelationship", array(
							"value" => loadSaveData("guardians", "relationship", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Radio("Lives With?", "GuardianTwoLivesWith", array("yes" => "Yes", "no" => "No"), array(
							"value" => loadSaveData("guardians", "lives_with", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Email("Email Address", "GuardianTwoEmail", array(
							"value" => loadSaveData("guardians", "email", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Phone("Cell Phone", "GuardianTwoCellPhone", array(
							"value" => loadSaveData("guardians", "cell_phone", 1)
							)
						));
						$form->addElement(new Element\Phone("Home Phone", "GuardianTwoHomePhone", array(
							"value" => loadSaveData("guardians", "home_phone", 1)
							)
						));
						$form->addElement(new Element\Phone("Daytime Phone", "GuardianTwoDaytimePhone", array(
							"value" => loadSaveData("guardians", "daytime_phone", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Address", "GuardianTwoAddress", array(
							"value" => loadSaveData("guardians", "address", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("City", "GuardianTwoCity", array(
							"value" => loadSaveData("guardians", "city", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Province("Province", "GuardianTwoProvince", array(
							"value" => loadSaveData("guardians", "province", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Postal Code", "GuardianTwoPostalCode", array(
							"value" => loadSaveData("guardians", "postal_code", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Daytime Address", "GuardianTwoDaytimeAddress", array(
							"value" => loadSaveData("guardians", "daytime_address", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Daytime City", "GuardianTwoDaytimeCity", array(
							"value" => loadSaveData("guardians", "daytime_city", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Province("Daytime Province", "GuardianTwoDaytimeProvince", array(
							"value" => loadSaveData("guardians", "daytime_province", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Textbox("Daytime Postal Code", "GuardianTwoDaytimePostalCode", array(
							"value" => loadSaveData("guardians", "daytime_postal_code", 1),
							"required" => 1
							)
						));
						$form->addElement(new Element\Checkbox("Correct?", "GuardianTwoCorrect", array("yes" => "I affirm that this information is correct."),
							array(
								"value" => array(),
								"required" => 1,
								"longDesc" => "Please confirm the above information before continuing."
							)
						));
					$form->addElement(new Element\HTML('</fieldset>'));
				$form->addElement(new Element\HTML('</div>'));
			$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('</fieldset>'));
		
		$form->addElement(new Element\Button("← Back", "button", array(
			"class" => "back-button"
		)));
		$form->addElement(new Element\Button("Save and Continue →"));
		$form->render();
		?>
	</div>
	
	<div id="registration-page-contacts" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['registerID'])) {
?>
			<h2>New Registration</h2>
<?php
	} else {
?>
			<h2>Edit Application</h2>
<?php
	}
?>
			<h3>Emergency Contacts</h3>
		</div>
		<div class="ajax-response"></div>
		
		<?php
		// Contact information form
		$form = new Form("register-form-contacts");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "register-form-contacts"));
		$form->addElement(new Element\Hidden("registerID", loadSaveData("registration", "id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
		$form->addElement(new Element\Hidden("childID", loadSaveData("child", "id")));
		$form->addElement(new Element\Hidden("emergencyID1", loadSaveData("emergency_contacts", "id", 0)));
		$form->addElement(new Element\Hidden("emergencyID2", loadSaveData("emergency_contacts", "id", 1)));
		$form->addElement(new Element\Hidden("emergencyID3", loadSaveData("emergency_contacts", "id", 2)));
		$form->addElement(new Element\Hidden("authorizedID1", loadSaveData("authorized_contacts", "id", 0)));
		$form->addElement(new Element\Hidden("authorizedID2", loadSaveData("authorized_contacts", "id", 1)));
		$form->addElement(new Element\Hidden("authorizedID3", loadSaveData("authorized_contacts", "id", 2)));
		$form->addElement(new Element\Hidden("restrictedID1", loadSaveData("restricted_contacts", "id", 0)));
		$form->addElement(new Element\Hidden("restrictedID2", loadSaveData("restricted_contacts", "id", 1)));
		$form->addElement(new Element\Hidden("restrictedID3", loadSaveData("restricted_contacts", "id", 2)));
		
		// Emergency contacts
		$form->addElement(new Element\HTML('<fieldset id="emergency-contacts">'));
			// Emergency contact one
			$form->addElement(new Element\HTML('<fieldset id="emergency-contact-1" class="emergency-contact">'));
				$form->addElement(new Element\HTML('<legend>Emergency Contact One</legend>'));
				$form->addElement(new Element\Textbox("First Name", "EmergencyContactOneFirstName", array(
					"value" => loadSaveData("emergency_contacts", "first_name", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "EmergencyContactOneLastName", array(
					"value" => loadSaveData("emergency_contacts", "last_name", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Relation to Child", "EmergencyContactOneRelationship", array(
					"value" => loadSaveData("emergency_contacts", "relationship", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Email("Email Address", "EmergencyContactOneEmail", array(
					"value" => loadSaveData("emergency_contacts", "email", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Phone("Phone One", "EmergencyContactOnePhoneOne", array(
					"value" => loadSaveData("emergency_contacts", "phone_1", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Phone("Phone Two", "EmergencyContactOnePhoneTwo", array(
					"value" => loadSaveData("emergency_contacts", "phone_2", 0)
					)
				));
				$form->addElement(new Element\Phone("Phone Three", "EmergencyContactOnePhoneThree", array(
					"value" => loadSaveData("emergency_contacts", "phone_3", 0)
					)
				));
				$form->addElement(new Element\Textbox("Daytime Address", "EmergencyContactOneDaytimeAddress", array(
					"value" => loadSaveData("emergency_contacts", "daytime_address", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime City", "EmergencyContactOneDaytimeCity", array(
					"value" => loadSaveData("emergency_contacts", "daytime_city", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Province("Daytime Province", "EmergencyContactOneDaytimeProvince", array(
					"value" => loadSaveData("emergency_contacts", "daytime_province", 0),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime Postal Code", "EmergencyContactOneDaytimePostalCode", array(
					"value" => loadSaveData("emergency_contacts", "daytime_postal_code", 0),
					"required" => 1
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));

			// Emergency contact two
			$form->addElement(new Element\HTML('<fieldset id="emergency-contact-2" class="emergency-contact">'));
				$form->addElement(new Element\HTML('<legend>Emergency Contact Two</legend>'));
				$form->addElement(new Element\Textbox("First Name", "EmergencyContactTwoFirstName", array(
					"value" => loadSaveData("emergency_contacts", "first_name", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "EmergencyContactTwoLastName", array(
					"value" => loadSaveData("emergency_contacts", "last_name", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Relation to Child", "EmergencyContactTwoRelationship", array(
					"value" => loadSaveData("emergency_contacts", "relationship", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Email("Email Address", "EmergencyContactTwoEmail", array(
					"value" => loadSaveData("emergency_contacts", "email", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Phone("Phone One", "EmergencyContactTwoPhoneOne", array(
					"value" => loadSaveData("emergency_contacts", "phone_1", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Phone("Phone Two", "EmergencyContactTwoPhoneTwo", array(
					"value" => loadSaveData("emergency_contacts", "phone_2", 1)
					)
				));
				$form->addElement(new Element\Phone("Phone Three", "EmergencyContactTwoPhoneThree", array(
					"value" => loadSaveData("emergency_contacts", "phone_3", 1)
					)
				));
				$form->addElement(new Element\Textbox("Daytime Address", "EmergencyContactTwoDaytimeAddress", array(
					"value" => loadSaveData("emergency_contacts", "daytime_address", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime City", "EmergencyContactTwoDaytimeCity", array(
					"value" => loadSaveData("emergency_contacts", "daytime_city", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Province("Daytime Province", "EmergencyContactTwoDaytimeProvince", array(
					"value" => loadSaveData("emergency_contacts", "daytime_province", 1),
					"required" => 1
					)
				));
				$form->addElement(new Element\Textbox("Daytime Postal Code", "EmergencyContactTwoDaytimePostalCode", array(
					"value" => loadSaveData("emergency_contacts", "daytime_postal_code", 1),
					"required" => 1
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));

			// Emergency contact three
			$form->addElement(new Element\HTML('<fieldset id="emergency-contact-3" class="emergency-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Emergency Contact Three</legend>'));
				$form->addElement(new Element\Textbox("First Name", "EmergencyContactThreeFirstName", array(
					"value" => loadSaveData("emergency_contacts", "first_name", 2)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "EmergencyContactThreeLastName", array(
					"value" => loadSaveData("emergency_contacts", "last_name", 2)
					)
				));
				$form->addElement(new Element\Textbox("Relation to Child", "EmergencyContactThreeRelationship", array(
					"value" => loadSaveData("emergency_contacts", "relationship", 2)
					)
				));
				$form->addElement(new Element\Email("Email Address", "EmergencyContactThreeEmail", array(
					"value" => loadSaveData("emergency_contacts", "email", 2)
					)
				));
				$form->addElement(new Element\Phone("Phone One", "EmergencyContactThreePhoneOne", array(
					"value" => loadSaveData("emergency_contacts", "phone_1", 2)
					)
				));
				$form->addElement(new Element\Phone("Phone Two", "EmergencyContactThreePhoneTwo", array(
					"value" => loadSaveData("emergency_contacts", "phone_2", 2)
					)
				));
				$form->addElement(new Element\Phone("Phone Three", "EmergencyContactThreePhoneThree", array(
					"value" => loadSaveData("emergency_contacts", "phone_3", 2)
					)
				));
				$form->addElement(new Element\Textbox("Daytime Address", "EmergencyContactThreeDaytimeAddress", array(
					"value" => loadSaveData("emergency_contacts", "daytime_address", 2)
					)
				));
				$form->addElement(new Element\Textbox("Daytime City", "EmergencyContactThreeDaytimeCity", array(
					"value" => loadSaveData("emergency_contacts", "daytime_city", 2)
					)
				));
				$form->addElement(new Element\Province("Daytime Province", "EmergencyContactThreeDaytimeProvince", array(
					"value" => loadSaveData("emergency_contacts", "daytime_province", 2)
					)
				));
				$form->addElement(new Element\Textbox("Daytime Postal Code", "EmergencyContactThreeDaytimePostalCode", array(
					"value" => loadSaveData("emergency_contacts", "daytime_postal_code", 2)
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\HTML('<button id="add-emergency-contact">Add Emergency Contact</button>'));
		
		$form->addElement(new Element\HTML('<h3>Additional People Authorized To Pick Up My Child (Optional)</h3>'));
		// Authorized contacts
		$form->addElement(new Element\HTML('<fieldset id="authorized-contacts">'));
			// Authorized contact one
			$form->addElement(new Element\HTML('<fieldset id="authorized-contact-1" class="authorized-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Authorized Contact</legend>'));
				$form->addElement(new Element\Textbox("First Name", "AuthorizedContactOneFirstName", array(
					"value" => loadSaveData("authorized_contacts", "first_name", 0)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "AuthorizedContactOneLastName", array(
					"value" => loadSaveData("authorized_contacts", "last_name", 0)
					)
				));
				$form->addElement(new Element\Textbox("Relation to Child", "AuthorizedContactOneRelationship", array(
					"value" => loadSaveData("authorized_contacts", "relationship", 0)
					)
				));
				$form->addElement(new Element\Email("Email Address", "AuthorizedContactOneEmail", array(
					"value" => loadSaveData("authorized_contacts", "email", 0)
					)
				));
				$form->addElement(new Element\Phone("Phone One", "AuthorizedContactOnePhoneOne", array(
					"value" => loadSaveData("authorized_contacts", "phone_1", 0)
					)
				));
				$form->addElement(new Element\Phone("Phone Two", "AuthorizedContactOnePhoneTwo", array(
					"value" => loadSaveData("authorized_contacts", "phone_2", 0)
					)
				));
				$form->addElement(new Element\Phone("Phone Three", "AuthorizedContactOnePhoneThree", array(
					"value" => loadSaveData("authorized_contacts", "phone_3", 0)
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
			// Authorized contact two
			$form->addElement(new Element\HTML('<fieldset id="authorized-contact-2" class="authorized-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Authorized Contact</legend>'));
				$form->addElement(new Element\Textbox("First Name", "AuthorizedContactTwoFirstName", array(
					"value" => loadSaveData("authorized_contacts", "first_name", 1)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "AuthorizedContactTwoLastName", array(
					"value" => loadSaveData("authorized_contacts", "last_name", 1)
					)
				));
				$form->addElement(new Element\Textbox("Relation to Child", "AuthorizedContactTwoRelationship", array(
					"value" => loadSaveData("authorized_contacts", "relationship", 1)
					)
				));
				$form->addElement(new Element\Email("Email Address", "AuthorizedContactTwoEmail", array(
					"value" => loadSaveData("authorized_contacts", "email", 1)
					)
				));
				$form->addElement(new Element\Phone("Phone One", "AuthorizedContactTwoPhoneOne", array(
					"value" => loadSaveData("authorized_contacts", "phone_1", 1)
					)
				));
				$form->addElement(new Element\Phone("Phone Two", "AuthorizedContactTwoPhoneTwo", array(
					"value" => loadSaveData("authorized_contacts", "phone_2", 1)
					)
				));
				$form->addElement(new Element\Phone("Phone Three", "AuthorizedContactTwoPhoneThree", array(
					"value" => loadSaveData("authorized_contacts", "phone_3", 1)
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
			// Authorized contact three
			$form->addElement(new Element\HTML('<fieldset id="authorized-contact-3" class="authorized-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Authorized Contact</legend>'));
				$form->addElement(new Element\Textbox("First Name", "AuthorizedContactThreeFirstName", array(
					"value" => loadSaveData("authorized_contacts", "first_name", 2)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "AuthorizedContactThreeLastName", array(
					"value" => loadSaveData("authorized_contacts", "last_name", 2)
					)
				));
				$form->addElement(new Element\Textbox("Relation to Child", "AuthorizedContactThreeRelationship", array(
					"value" => loadSaveData("authorized_contacts", "relationship", 2)
					)
				));
				$form->addElement(new Element\Email("Email Address", "AuthorizedContactThreeEmail", array(
					"value" => loadSaveData("authorized_contacts", "email", 2)
					)
				));
				$form->addElement(new Element\Phone("Phone One", "AuthorizedContactThreePhoneOne", array(
					"value" => loadSaveData("authorized_contacts", "phone_1", 2)
					)
				));
				$form->addElement(new Element\Phone("Phone Two", "AuthorizedContactThreePhoneTwo", array(
					"value" => loadSaveData("authorized_contacts", "phone_2", 2)
					)
				));
				$form->addElement(new Element\Phone("Phone Three", "AuthorizedContactThreePhoneThree", array(
					"value" => loadSaveData("authorized_contacts", "phone_3", 2)
					)
				));
			$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\HTML('<button id="add-authorized-contact">Add Authorized Contact</button>'));
		
		$form->addElement(new Element\HTML('<h3>Restrict Access To My Child (Optional)</h3>'));
		$form->addElement(new Element\HTML('<p>Please provide a copy of any relevant court orders regarding parental access. Are there specific individuals who are not allowed access to your child?</p>'));
		// Restricted contacts
		$form->addElement(new Element\HTML('<fieldset id="restricted-contacts">'));
			// Restricted contact one
			$form->addElement(new Element\HTML('<fieldset id="restricted-contact-1" class="restricted-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Restricted Contact</legend>'));
				$form->addElement(new Element\Textbox("First Name", "RestrictedContactOneFirstName", array(
					"value" => loadSaveData("restricted_contacts", "first_name", 0)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "RestrictedContactOneLastName", array(
					"value" => loadSaveData("restricted_contacts", "last_name", 0)
					)
				));
				$form->addElement(new Element\Textarea("Please Describe", "RestrictedContactOneDetail", array(
					"value" => loadSaveData("restricted_contacts", "detail", 0)
					)
				));
//				$form->addElement(new Element\File("Upload a File", "RestrictedContactOneFile"));
			$form->addElement(new Element\HTML('</fieldset>'));
			// Restricted contact two
			$form->addElement(new Element\HTML('<fieldset id="restricted-contact-2" class="restricted-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Restricted Contact</legend>'));
				$form->addElement(new Element\Textbox("First Name", "RestrictedContactTwoFirstName", array(
					"value" => loadSaveData("restricted_contacts", "first_name", 1)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "RestrictedContactTwoLastName", array(
					"value" => loadSaveData("restricted_contacts", "last_name", 1)
					)
				));
				$form->addElement(new Element\Textarea("Please Describe", "RestrictedContactTwoDetail", array(
					"value" => loadSaveData("restricted_contacts", "detail", 1)
					)
				));
//				$form->addElement(new Element\File("Upload a File", "RestrictedContactTwoFile"));
			$form->addElement(new Element\HTML('</fieldset>'));
			// Restricted contact three
			$form->addElement(new Element\HTML('<fieldset id="restricted-contact-3" class="restricted-contact hidden-content">'));
				$form->addElement(new Element\HTML('<legend>Restricted Contact</legend>'));
				$form->addElement(new Element\Textbox("First Name", "RestrictedContactThreeFirstName", array(
					"value" => loadSaveData("restricted_contacts", "first_name", 2)
					)
				));
				$form->addElement(new Element\Textbox("Last Name", "RestrictedContactThreeLastName", array(
					"value" => loadSaveData("restricted_contacts", "last_name", 2)
					)
				));
				$form->addElement(new Element\Textarea("Please Describe", "RestrictedContactThreeDetail", array(
					"value" => loadSaveData("restricted_contacts", "detail", 2)
					)
				));
//				$form->addElement(new Element\File("Upload a File", "RestrictedContactThreeFile"));
			$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\HTML('<button id="add-restricted-contact">Add Restricted Contact</button>'));

		$form->addElement(new Element\Button("← Back", "button", array(
			"onclick" => 'showFormPage("registration-page-general");'
		)));
		$form->addElement(new Element\Button("Save and Continue →"));
		$form->render();
		?>
	</div>
	
	<div id="registration-page-medical" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['registerID'])) {
?>
			<h2>New Registration</h2>
<?php
	} else {
?>
			<h2>Edit Application</h2>
<?php
	}
?>
			<h3>Medical Information</h3>
		</div>
		<div class="ajax-response"></div>
		
		<?php
		$form = new Form("register-form-medical");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "register-form-medical"));
		$form->addElement(new Element\Hidden("registerID", loadSaveData("registration", "id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
		$form->addElement(new Element\Hidden("childID", loadSaveData("child", "id")));
		
		$form->addElement(new Element\HTML('<fieldset id="medical-contact">'));
			$form->addElement(new Element\Textbox("Alberta Health Care Number", "AlbertaHealthCareNumber", array(
				"value" => loadSaveData("child", "alberta_health_care_number"),
				"required" => 1
				)
			));
			$form->addElement(new Element\Textbox("Doctor's Name/Clinic", "DoctorName", array(
				"value" => loadSaveData("child", "doctor_name"),
				"required" => 1
				)
			));
			$form->addElement(new Element\Phone("Phone Number", "DoctorPhone", array(
				"value" => loadSaveData("child", "doctor_phone")
				)
			));
		$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML('<fieldset id="medical-immunizations">'));
			// create the dropdown from two toggle options
			if (loadSaveData("child", "condition_immunizations_up_to_date") == "yes") {
				$value = "condition_immunizations_up_to_date";
			} elseif (loadSaveData("child", "condition_no_immunization") == "yes") {
				$value = "condition_no_immunization";
			} else {
				$value = "";
			}
			$form->addElement(new Element\Select("Immunizations", "MedicalImmunizations", array(
				"" => "- please select -",
				"condition_immunizations_up_to_date" => "Immunizations are up-to-date",
				"condition_no_immunization" => "Chose NOT to immunize"
				), array(
				"value" => $value,
				"required" => 1
				)
			));
		$form->addElement(new Element\HTML('</fieldset>'));

		// remove imunization from conditions list since they are handled separately
		$conditions = $CFG['medical_conditions'];
		unset($conditions['condition_immunizations_up_to_date']);
		unset($conditions['condition_no_immunization']);
		$save_conditions = array();
		foreach ($conditions as $key => $value) {
			if (loadSaveData("child", $key) == "yes") {
				$save_conditions[] = $key;
			}
		}
		$form->addElement(new Element\Checkbox("Illnesses, Allergies, and Other Medical Conditions (Please mark all items that apply to this child. If prompted, please provide additional details.)", "MedicalConditions", $conditions, array(
			"value" => $save_conditions
			)
		));
		
		$form->addElement(new Element\HTML('<div id="medical-conditions-allergy-life-threatening" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionAllergyLifeThreateningDetail", array(
				"value" => loadSaveData("child", "condition_allergy_life_threatening_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-allergy-food" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionAllergyFoodDetail", array(
				"value" => loadSaveData("child", "condition_allergy_food_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-allergy-environmental" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionAllergyEnvironmentalDetail", array(
				"value" => loadSaveData("child", "condition_allergy_environmental_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-diabetes" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionDiabetesDetail", array(
				"value" => loadSaveData("child", "condition_diabetes_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-uti" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionUTIDetail", array(
				"value" => loadSaveData("child", "condition_uti_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-add-adhd" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionADDADHDDetail", array(
				"value" => loadSaveData("child", "condition_add_adhd_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-epilepsy" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionEpilepsyDetail", array(
				"value" => loadSaveData("child", "condition_epilepsy_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-heart-condition" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionHeartConditionDetail", array(
				"value" => loadSaveData("child", "condition_heart_condition_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-autism-spectrum" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionAutismSpectrumDetail", array(
				"value" => loadSaveData("child", "condition_autism_spectrum_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		$form->addElement(new Element\HTML('<div id="medical-conditions-medications" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalConditionMedicationsDetail", array(
				"value" => loadSaveData("child", "condition_medications_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));

		$difficulties = $CFG['medical_difficulties'];
		$save_difficulties = array();
		foreach ($difficulties as $key => $value) {
			if (loadSaveData("child", $key) == "yes") {
				$save_difficulties[] = $key;
			}
		}
		$form->addElement(new Element\Checkbox("Has your child had any difficulty with the following? If so, please provide details when prompted.", "MedicalDifficulties", array(
			"difficulty_hearing" => "Hearing",
			"difficulty_speech" => "Speech",
			"difficulty_eating" => "Eating",
			"difficulty_vision" => "Vision",
			"difficulty_bowels" => "Bowels",
			"difficulty_urinary" => "Urinary accidents",
			"difficulty_social" => "Making friends",
			"difficulty_other" => "Other"
			), array(
			"value" => $save_difficulties
			)
		));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-hearing" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultyHearingDetail", array(
				"value" => loadSaveData("child", "difficulty_hearing_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-speech" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultySpeechDetail", array(
				"value" => loadSaveData("child", "difficulty_speech_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-eating" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultyEatingDetail", array(
				"value" => loadSaveData("child", "difficulty_eating_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-vision" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultyVisionDetail", array(
				"value" => loadSaveData("child", "difficulty_vision_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-bowels" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultyBowelsDetail", array(
				"value" => loadSaveData("child", "difficulty_bowels_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-urinary-accidents" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultyUrinaryAccidentsDetail", array(
				"value" => loadSaveData("child", "difficulty_urinary_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-social" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultySocialDetail", array(
				"value" => loadSaveData("child", "difficulty_social_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<div id="medical-difficulties-other" class="hidden-content">'));
			$form->addElement(new Element\Textarea("Please provide some details", "MedicalDifficultyOtherDetail", array(
				"value" => loadSaveData("child", "difficulty_other_detail")
				)
			));
		$form->addElement(new Element\HTML('</div>'));
		
		$form->addElement(new Element\HTML('<fieldset id="child-behavior">'));
			$form->addElement(new Element\HTML("<legend>Please tell us a few things that will help us understand your child's needs and personality.</legend>"));
			$form->addElement(new Element\Textarea("Favourite activities", "ChildBehaviorFavouriteActivitiesDetail", array(
				"value" => loadSaveData("child", "behavior_favourite_activities")
				)
			));
			$form->addElement(new Element\Textarea("Fears or challenges", "ChildBehaviorFearsDetail", array(
				"value" => loadSaveData("child", "behavior_fears")
				)
			));
			$form->addElement(new Element\Textarea("Behavioral challenges", "ChildBehaviorBehavioralChallengesDetail", array(
				"value" => loadSaveData("child", "behavior_challenges")
				)
			));
			$form->addElement(new Element\Textarea("Anything else", "ChildBehaviorOtherDetail", array(
				"value" => loadSaveData("child", "behavior_other")
				)
			));
		$form->addElement(new Element\HTML('</fieldset>'));
		$form->addElement(new Element\HTML("<p>All personal and medical information collected by Summit Kids in the Registration Package becomes part of the child's record. It is considered to be confidential and is protected by our Confidentiality Policy. We will not share this information without written parent concent. Email addresses provided by parents will be added to our confidential email list and will be used to enhance communication with parents in our program.</p>"));
		
		$form->addElement(new Element\HTML('<div id="child-behavior-hidden-content">'));
		$form->addElement(new Element\HTML('</div>'));

		$form->addElement(new Element\Button("← Back", "button", array(
			"onclick" => 'showFormPage("registration-page-contacts");'
		)));
		$form->addElement(new Element\Button("Save and Continue →"));
		$form->render();
		?>
	</div>

	<div id="registration-page-submit" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['registerID'])) {
?>
			<h2>New Registration</h2>
<?php
	} else {
?>
			<h2>Edit Application</h2>
<?php
	}
?>
			<h3>Final Steps</h3>
		</div>
		<div class="ajax-response"></div>
<?php /*
		<p>To finalize your registration, Summit Kids will need the following:</p>
		<ul>		
			<li>View your registration details by clicking "My Registration" below. From there you can View, Save, or Print the document. We will need a printed copy.</li>
			<li>Please view the Waivers and Payment Authorization by clicking the button(s) below. Print a copy and sign each document where appropriate. Please also remember to fill out your selected method of payment.</li>
			<li>Include a copy of all supporting documents for restricted contacts (court order, etc.) for our records.</li>
			<li>Bring all documents to your Summit Kids campus as soon as possible.</li>
		</ul>
*/ ?>
		<div class="span12">
			<h3>Registration Process – please complete <u>all</u> steps as listed below:</h3>
			<div class="span6 row-fluid">
				<span class="span2">Step 1</span><span class="span6">Print & Sign Registration Form</span>
				<span class="span4"><button id="button-my-registration" type="button">My Registration</button></span>
			</div>
			<div class="span6 row-fluid">
				<span class="span2">Step 2</span><span class="span6">Print & Sign Waiver</span>
				<span class="span4"><button id="button-waivers" type="button">Waivers</button></span>
			</div>
			<div class="span6 row-fluid">
				<span class="span2">Step 3</span><span class="span6">Print, Complete, & Sign Payment Form</span>
				<span class="span4"><button id="button-cc-authorization" type="button">CC Authorization</button>
				<button id="button-eft-authorization" type="button">EFT Authorization</button></span>
			</div>
			<div class="span6 row-fluid"><span class="span2">Step 4</span><span class="span10">Bring <u>all</u> forms to your nearest Summit Kids Campus</span></div>
			<div class="span6 row-fluid"><span class="span2">Step 5</span><span class="span10">Make sure you’ve completed all steps</span></div>
			<div class="span6 row-fluid"><span class="span2">Step 6</span><span class="span10">Bring your non-refundable registration fee or re-enrolment fee: <br>REGISTRATION $55 (individual), $80 (family); <br>RE-ENROLMENT $35 (individual), $50 (family)</span></div>
			<div class="span6 row-fluid"><span class="span2">Step 7</span><span class="span10">You’re done!…Have a fabulous day!</span></div>
		</div>
		<p>&nbsp;</p>
		<p><span class="label label-important">Remember</span> Your registration is NOT complete. A spot is not secured for your child until we have a signed (where appropriate) copy of these documents and a valid method of payment on record. Also, please note that if your uploaded and printed child photo is not recognizable or there was an error in the process, you will need to bring a physical copy for our records.</p>
		<?php
		// Submit application form
		$form = new Form("register-form-submit");
/*
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
			"action" => "print.php",
			"target" => "_blank"
		));
*/
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
			"action" => "/"
		));
		$form->addElement(new Element\Hidden("form", "register-form-submit"));
		$form->addElement(new Element\Hidden("registerID", ""));
		$form->addElement(new Element\Hidden("userID", $user_id));		
		
		$form->addElement(new Element\Button("← Back", "button", array(
			"class" => "back-button"
		)));
		$form->addElement(new Element\Button("My Registration", "button", array(
			"class" => "btn-success register-form-submit-btn-my-registration"
		)));
		$form->addElement(new Element\Button("CC Authorization", "button", array(
			"class" => "register-form-submit-btn-cc-authorization"
		)));
		$form->addElement(new Element\Button("EFT Authorization", "button", array(
			"class" => "register-form-submit-btn-eft-authorization"
		)));
		$form->addElement(new Element\Button("Waivers", "button", array(
			"class" => "register-form-submit-btn-waivers"
		)));
		$form->addElement(new Element\Button("Cancel", "button", array(
//			"onclick" => "window.location.reload(true);"
			"onclick" => "location.href = '/';"
		)));
//		$form->addElement(new Element\Button("Submit Application"));
		$form->addElement(new Element\Button("Submit Application", "button", array(
			"class" => "btn-primary register-form-submit-btn-submit"
		)));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
