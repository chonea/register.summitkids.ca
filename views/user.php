<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$save_users = array();
$user_id = "";

if ($_SESSION['user_role'] == "admin") {
	if (isset($_REQUEST["userID"]) && $_REQUEST["userID"] != "") {
		$user_id = $_REQUEST["userID"];
		$query = "user_id = '".$_REQUEST["userID"]."'";
	} else {
		$query = "";
	}
} elseif (isset($_REQUEST["userID"]) && $_REQUEST["userID"] == $_SESSION['user_id']) {
	$user_id = $_REQUEST["userID"];
	$query = "user_id = '".$_REQUEST["userID"]."'";
} else {
	header("Location: ".$_SERVER["PHP_SELF"]."?userID=".$_SESSION['user_id']);
	exit();
}
if ($query != "") {
	if ($result = $db->select("users", $query)) {
		// this is a singular result
		$save_users = $result[0];
	} else {
		header("Location: /");
		exit();
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
	<div id="user-page-general" class="form-page">
		<div class="page-header">
<?php
	if (!isset($_REQUEST['userID']) || $_REQUEST['userID'] == "") {
?>
			<h2>Create Account</h2>
			<h3>New Account</h3>
<?php
	} else {
?>
			<h2>Edit Account</h2>
			<h3>User Information for <?php echo loadSaveData("users", "user_first_name"); ?> <?php echo loadSaveData("users", "user_last_name"); ?></h3>
<?php
	}
?>
		</div>
		<div class="ajax-response"></div>

		<?php
/*
echo "<pre>";
print_r($save_users);
echo "</pre>";
die();
*/
		// General information form
		$form = new Form("user-form");
		$form->configure(array(
			"prevent" => array("bootstrap", "jQuery"),
		//	"view" => new PFBC\View\Inline,
		//	"labelToPlaceholder" => 1
		));
		$form->addElement(new Element\Hidden("form", "user-form"));
		$form->addElement(new Element\Hidden("userID", loadSaveData("users", "user_id")));
		
		// User's information
		$form->addElement(new Element\HTML('<fieldset id="users">'));
			//$form->addElement(new Element\HTML('<legend>Account</legend>'));
			if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
				$form->addElement(new Element\Email("Email Address (login)", "UserEmail", array(
					"value" => loadSaveData("users", "user_email"),
					"required" => 1
					)
				));
			} else {
				$form->addElement(new Element\Email("Email Address (login)", "UserEmail", array(
					"value" => loadSaveData("users", "user_email"),
					"required" => 1
					)
				));
			}
			if (isset($_REQUEST['userID']) && $_REQUEST['userID'] != "") {
				$date = new DateTime(loadSaveData("users", "user_registration_datetime"));
				$form->addElement(new Element\Textbox("Creation Date", "UserRegistrationDateTime", array(
					"value" => $date->format('jS F Y \a\t g:i a'),
					"disabled" => 1
					)
				));
			}

			$options = array();
			$options["value"] = "";
			if (!isset($_REQUEST['userID']) || $_REQUEST['userID'] == "") {
				$options["required"] = 1;
			}
			$form->addElement(new Element\Password("Password", "UserPassword", $options));

			$options = array();
			$options["value"] = "";
			if (!isset($_REQUEST['userID']) || $_REQUEST['userID'] == "") {
				$options["required"] = 1;
			}
			$form->addElement(new Element\Password("Confirm Password", "UserPasswordConfirm", $options));

			if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager") {
				$form->addElement(new Element\Button("Generate Password", "button", array(
					"name" => "UserPasswordGenerate"
				)));
			}

			$form->addElement(new Element\Textbox("First Name", "UserFirstName", array(
				"value" => loadSaveData("users", "user_first_name"),
				"required" => 1
				)
			));

			$form->addElement(new Element\Textbox("Last Name", "UserLastName", array(
				"value" => loadSaveData("users", "user_last_name"),
				"required" => 1
				)
			));

			$query = "(disabled = '' OR ISNULL(disabled))";
			$query .= " AND (deleted = '' OR ISNULL(deleted))";
			$query .= " ORDER BY name ASC";
			$location_options = array(
				"" => "- please select -"
			);
			$locations = $db->select("location", $query);
			foreach ($locations as $location) {
				$location_options[$location['id']] = $location['name'];
			}
			$form->addElement(new Element\Select("Primary Location", "UserLocationID", $location_options, 
				array(
					"value" => loadSaveData("users", "location_id", 0)
				)
			));

			if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager") {
				$form->addElement(new Element\Select("Verified Email", "UserActive", array(
						"send" => "Send Verification Request",
						"0" => "Unverified",
						"1" => "Verified"
					), array(
						"value" => loadSaveData("users", "user_active"),
						"required" => 1
					)
				));

				$role_options = array(
					"" => "- please select -",
					"user" => "User"
				);
				$role_options["staff"] = "Staff";
				$role_options["manager"] = "Manager";
				if ($_SESSION['user_role'] == "admin") {
					$role_options["admin"] = "Administrator";
				}
				$form->addElement(new Element\Select("Role", "UserRole", $role_options, array(
					"value" => loadSaveData("users", "role"),
					"required" => 1
					)
				));
			}
		$form->addElement(new Element\HTML('</fieldset>'));

		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "location.href = '/users.php';"
		)));
		$form->addElement(new Element\Button("Save and Finish"));
		$form->render();
		?>
	</div>
</div>
<?php
include("footer.php");
