<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$save_child = array();
$save_emergency_contacts = array();
$save_authorized_contacts = array();
$save_restricted_contacts = array();
$user_id = $_SESSION['user_id'];

if (isset($_REQUEST["childID"])) {
	$query = "id = '".$_REQUEST["childID"]."'";
	if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
		$query .= " AND user_id = '".$_SESSION['user_id']."'";
	}
	if ($results = $db->select("child", "id = '".$_REQUEST["childID"]."'")) {
		$save_child = $results[0];
		$user_id = $save_child['user_id'];
		$save_emergency_contacts = $db->select("contact", "child_id = '".$save_child['id']."' AND type = 'emergency'");
		$save_authorized_contacts = $db->select("contact", "child_id = '".$save_child['id']."' AND type = 'authorized'");
		$save_restricted_contacts = $db->select("contact", "child_id = '".$save_child['id']."' AND type = 'restricted'");
	} else {
		$save_child = array();
	}
} elseif (($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager") && (isset($_REQUEST["userID"]) && $_REQUEST["userID"] != "")) {
	$user_id = $_REQUEST["userID"];
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
	<div id="child-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['childID'])) {
?>
			<h2>Add Child Profile</h2>
			<h3>General Information</h3>
<?php
	} else {
?>
			<h2>Edit Child Profile</h2>
			<h3>General Information for <?php echo loadSaveData("child", "first_name"); ?> <?php echo loadSaveData("child", "last_name"); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
		// General information form
		$form = new Form("child-form-general");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "child-form-general"));
		$form->addElement(new Element\Hidden("childID", loadSaveData("child", "id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
		$form->addElement(new Element\Hidden("guardianOneID", loadSaveData("guardians", "id", 0)));
		$form->addElement(new Element\Hidden("guardianTwoID",loadSaveData("guardians", "id", 1)));
		$form->addElement(new Element\Hidden("ChildPhoto", loadSaveData("child", "photo")));
		
		// Child's information
		$form->addElement(new Element\HTML('<fieldset id="child">'));
			$form->addElement(new Element\HTML('<legend>Profile</legend>'));
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
				$form->addElement(new Element\HTML('<div id="child-form-general-child-photo-imageFile">'));
				if (loadSaveData("child", "photo")) {
					$form->addElement(new Element\HTML('<img src="'.loadSaveData("child", "photo").'" style="height: 100px; margin: 0 20px 15px;" />'));
				}
				$form->addElement(new Element\HTML('</div>'));
				$form->addElement(new Element\HTML('<div class="controls">'));
					$form->addElement(new Element\HTML('<div id="child-form-general-child-photo"><noscript><p>Please enable JavaScript to use the file uploader.</p></noscript></div>'));
					$form->addElement(new Element\HTML('<div><span class="help-block">Types .jpg, .gif, or .png under 3MB only, please. <strong>We must have a recognizable photo of your child on file. If a photo is not provided here with this form, a physical photo must be provided.</strong></span></div>'));
				$form->addElement(new Element\HTML('</div>'));
			$form->addElement(new Element\HTML('</div>'));

		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = '/';"
		)));
		$form->addElement(new Element\Button("Save and Continue →"));
		$form->render();
		?>
	</div>
	
	<div id="child-page-contacts" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['childID'])) {
?>
			<h2>Add Child Profile</h2>
<?php
	} else {
?>
			<h2>Edit Child Profile</h2>
<?php
	}
?>
			<h3>Emergency Contacts for <?php echo loadSaveData("child", "first_name"); ?> <?php echo loadSaveData("child", "last_name"); ?></h3>
		</div>
		<div class="ajax-response"></div>
		
		<?php
		// Contact information form
		$form = new Form("child-form-contacts");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "child-form-contacts"));
		$form->addElement(new Element\Hidden("childID", loadSaveData("child", "id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
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
			"onclick" => 'showFormPage("child-page-general");'
		)));
		$form->addElement(new Element\Button("Save and Continue →"));
		$form->render();
		?>
	</div>
	
	<div id="child-page-medical" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['childID'])) {
?>
			<h2>Add Child Profile</h2>
<?php
	} else {
?>
			<h2>Edit Child Profile</h2>
<?php
	}
?>
			<h3>Medical Information for <?php echo loadSaveData("child", "first_name"); ?> <?php echo loadSaveData("child", "last_name"); ?></h3>
		</div>
		<div class="ajax-response"></div>
		
		<?php
		$form = new Form("child-form-medical");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "child-form-medical"));
		$form->addElement(new Element\Hidden("childID", loadSaveData("child", "id")));
		$form->addElement(new Element\Hidden("userID", $user_id));
		
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
		$form->addElement(new Element\HTML("<p>All personal and medical information collected by Summit Kids in the Registration Package becomes part of the child's record. It is considered to be confidential and is protected by our Confidentiality Policy. We will not share this information without written parent concent. Email addresses provided by parents will be added to our confidential email list and will be used to enhance communication with parents in our program."));
		
		$form->addElement(new Element\HTML('<div id="child-behavior-hidden-content">'));
		$form->addElement(new Element\HTML('</div>'));

		$form->addElement(new Element\Button("← Back", "button", array(
			"onclick" => 'showFormPage("child-page-contacts");'
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
