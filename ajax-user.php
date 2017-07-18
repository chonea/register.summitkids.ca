<?php
header("Content-type: application/json");
session_start();
error_reporting(E_ALL);

include("config/db.config.php");
include("config/login.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

require_once('libraries/login/password_compatibility_library.php');
require_once('translations/en.php');
require_once('libraries/login/PHPMailer.php');

$returnJSON = array();
$returnJSON['success'] = true;

if (isset($_POST['form'])) {

	if ($_POST['form'] == "user-form") {

		$user_password = "";
		$user_password_confirm = "";
		$user_password_hash = "";
		$user_activation_hash = "";

		If ($_POST["UserActive"] == "send") {
			$user_active = 0;
			$user_activation_hash = sha1(uniqid(mt_rand(), true));
		} else {
			$user_active = $_POST["UserActive"];
		}

		if (isset($_POST["UserPassword"]) && $_POST["UserPassword"] != "") {
			$user_password = trim($_POST["UserPassword"]);
			$user_password_confirm = trim($_POST["UserPasswordConfirm"]);
			if ($user_password == $user_password_confirm) {
				// Taken from Registration.php
				$hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
				$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
			} else {
				$returnJSON['success'] = false;
				$returnJSON['message'] = "Passwords do not match.";
				echo json_encode($returnJSON);
				exit();
			}
		}

		// Insert User
		$user_email = trim($_POST["UserEmail"]);
		if ($_POST['userID'] == '') {
			if ($results = $db->select("users", "user_email = '".$user_email."'")) {
				$user = $results[0];
				if (isset($user['user_id'])) {
					$returnJSON["userID"] = "";
					$returnJSON['message'] = "Email already exists.";
					$returnJSON['success'] = false;
					echo json_encode($returnJSON);
					exit();
				}
			}

			$insert = array(
				"user_name" => $user_email,
				"user_first_name" => trim($_POST["UserFirstName"]),
				"user_last_name" => trim($_POST["UserLastName"]),
				"user_email" => $user_email,
				"user_active" => $user_active,
				"user_registration_datetime" => date("Y-m-d H:i:s"),
				"location_id" => $_POST["UserLocationID"],
			);
			if (isset($_POST["UserRole"])) {
				$insert["role"] = $_POST["UserRole"];
			}
			if ($user_password_hash != "") {
				$insert["user_password_hash"] = $user_password_hash;
			}
			if ($user_activation_hash != "") {
				$insert["user_activation_hash"] = $user_activation_hash;
			}
			if ($db->insert("users", $insert)) {
				$user_id = $db->lastInsertId();
				$returnJSON["userID"] = $user_id;
				
				// send a verification email
				if ($user_activation_hash != "") {
					if (!sendVerificationEmail($user_id, $user_email, $user_activation_hash, $user_password)) {
						// delete this users account immediately, as we could not send a verification email
						$where = "user_id = '".$user_id."'";
						$db->delete("users", $where);
						$returnJSON["userID"] = "";
						$returnJSON['message'] = "Error sending verification email.";
						$returnJSON['success'] = false;
						echo json_encode($returnJSON);
						exit();
					}
				}
			}
		// Update User
		} else {
			if ($results = $db->select("users", "user_id = '".$_REQUEST["userID"]."'")) {
				$user = $results[0];
				$update = array(
					"user_first_name" => trim($_POST["UserFirstName"]),
					"user_last_name" => trim($_POST["UserLastName"]),
					"user_active" => $user_active,
					"location_id" => $_POST["UserLocationID"],
				);
				if (isset($_POST["UserRole"])) {
					$update["role"] = $_POST["UserRole"];
				}
				if ($user_password_hash != "") {
					$update["user_password_hash"] = $user_password_hash;
				}
				if ($user_activation_hash != "") {
					$update["user_activation_hash"] = $user_activation_hash;
				}
				if ($user_email != $user['user_email']) {
					if ($results = $db->select("users", "user_email = '".$user_email."'")) {
						$returnJSON['success'] = false;
						$returnJSON['message'] = "Email already exists.";
					} else {
						$update["user_email"] = $user_email;
					}
				}
			}
				
			$where = "user_id = '".$_POST['userID']."'";
			if ($db->update("users", $update, $where)) {
				// send a verification email
				if ($user_activation_hash != "") {
					if (!sendVerificationEmail($_POST['userID'], $user_email, $user_activation_hash)) {
						// delete this users account immediately, as we could not send a verification email
						$returnJSON['message'] = "Error sending verification email.";
						$returnJSON['success'] = false;
						echo json_encode($returnJSON);
						exit();
					}
				}
			}
		}
	}
}
echo json_encode($returnJSON);
exit();


function sendVerificationEmail($user_id, $user_email, $user_activation_hash, $user_password = "")
{
		$mail = new PHPMailer;

		// please look into the config/config.php for much more info on how to use this!
		// use SMTP or use mail()
		if (EMAIL_USE_SMTP) {
				// Set mailer to use SMTP
				$mail->IsSMTP();
				//useful for debugging, shows full SMTP errors
				//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
				// Enable SMTP authentication
				$mail->SMTPAuth = EMAIL_SMTP_AUTH;
				// Enable encryption, usually SSL/TLS
				if (defined(EMAIL_SMTP_ENCRYPTION)) {
						$mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
				}
				// Specify host server
				$mail->Host = EMAIL_SMTP_HOST;
				$mail->Username = EMAIL_SMTP_USERNAME;
				$mail->Password = EMAIL_SMTP_PASSWORD;
				$mail->Port = EMAIL_SMTP_PORT;
		} else {
				$mail->IsMail();
		}

		$mail->From = EMAIL_VERIFICATION_FROM;
		$mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
		$mail->AddAddress($user_email);
		$mail->Subject = EMAIL_VERIFICATION_SUBJECT;

		$link = EMAIL_VERIFICATION_URL.'?id='.urlencode($user_id).'&verification_code='.urlencode($user_activation_hash);

		// the link to your register.php, please set this value in config/email_verification.php
		$message = "Welcome to the Summit Kids Online Registration System.\r\n";
		$message .= "\r\n";
		$message .= EMAIL_VERIFICATION_CONTENT.' '.$link.".\r\n";
		$message .= "\r\n";
		$message .= "Your login is: ".$user_email."\r\n";
		if ($user_password != "") {
			$message .= "\r\n";
			$message .= "Your password is: ".$user_password."\r\n";
			$message .= "\r\n";
			$message .= "Once activated, you may change your password in your user profile or via password reset. ";
			$message .= "Please safeguard your login information as your account contains sensitive personal data.\r\n";
		}
		$message .= "\r\n";
		$message .= "If you have questions regarding your account, please contact your local Summit Kids location (http://www.summitkids.ca/locations) or email corporate@summitkids.ca.\r\n";
		$mail->Body = $message;

		if(!$mail->Send()) {
				return $mail->ErrorInfo;
		} else {
				return true;
		}
}
?>