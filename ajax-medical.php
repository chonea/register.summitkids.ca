<?php
header("Content-type: application/json");
session_start();
//error_reporting(E_ALL);

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

$returnJSON = array();


include("config/register.config.php");

if (isset($_POST['form'])) {

	// Update registration
	if (isset($_POST['registerID']) && $_POST['registerID'] != '') {
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"page" => "medical",
		);
		$where = "id = '".$_POST['registerID']."'";
		$db->update("register", $update, $where);
	}

	if (isset($_POST['childID']) && $_POST['childID'] != '') {

		// Update child
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"alberta_health_care_number" => $_POST["AlbertaHealthCareNumber"],
			"doctor_name" => $_POST["DoctorName"],
			"doctor_phone" => $_POST["DoctorPhone"]
		);

		if ($_POST["MedicalImmunizations"] == "condition_immunizations_up_to_date") {
			$update["condition_immunizations_up_to_date"] = "yes";
			$update["condition_no_immunization"] = "no";
		} elseif ($_POST["MedicalImmunizations"] == "condition_no_immunization") {
			$update["condition_no_immunization"] = "yes";
			$update["condition_immunizations_up_to_date"] = "no";
		} else {
			$update["condition_no_immunization"] = "no";
			$update["condition_immunizations_up_to_date"] = "no";
		}

		$conditions = $CFG['medical_conditions'];
		unset($conditions['condition_immunizations_up_to_date']);
		unset($conditions['condition_no_immunization']);
		foreach ($conditions as $key => $value) {
			$update[$key] = "no";
		}
		if (isset($_POST["MedicalConditions"])) {
			foreach ($_POST["MedicalConditions"] as $value) {
				$update[$value] = "yes";
			}
		}

		if ($update["condition_allergy_life_threatening"] == "yes") {
			$update["condition_allergy_life_threatening_detail"] = $_POST["MedicalConditionAllergyLifeThreateningDetail"];
		} else {
			$update["condition_allergy_life_threatening_detail"] = NULL;
		}
		if ($update["condition_allergy_food"] == "yes") {
			$update["condition_allergy_food_detail"] = $_POST["MedicalConditionAllergyFoodDetail"];
		} else {
			$update["condition_allergy_food_detail"] = NULL;
		}
		if ($update["condition_allergy_environmental"] == "yes") {
			$update["condition_allergy_environmental_detail"] = $_POST["MedicalConditionAllergyEnvironmentalDetail"];
		} else {
			$update["condition_allergy_environmental_detail"] = NULL;
		}
		if ($update["condition_diabetes"] == "yes") {
			$update["condition_diabetes_detail"] = $_POST["MedicalConditionDiabetesDetail"];
		} else {
			$update["condition_diabetes_detail"] = NULL;
		}
		if ($update["condition_uti"] == "yes") {
			$update["condition_uti_detail"] = $_POST["MedicalConditionUTIDetail"];
		} else {
			$update["condition_uti_detail"] = NULL;
		}
		if ($update["condition_add_adhd"] == "yes") {
			$update["condition_add_adhd_detail"] = $_POST["MedicalConditionADDADHDDetail"];
		} else {
			$update["condition_add_adhd_detail"] = NULL;
		}
		if ($update["condition_epilepsy"] == "yes") {
			$update["condition_epilepsy_detail"] = $_POST["MedicalConditionEpilepsyDetail"];
		} else {
			$update["condition_epilepsy_detail"] = NULL;
		}
		if ($update["condition_heart_condition"] == "yes") {
			$update["condition_heart_condition_detail"] = $_POST["MedicalConditionHeartConditionDetail"];
		} else {
			$update["condition_heart_condition_detail"] = NULL;
		}
		if ($update["condition_autism_spectrum"] == "yes") {
			$update["condition_autism_spectrum_detail"] = $_POST["MedicalConditionAutismSpectrumDetail"];
		} else {
			$update["condition_autism_spectrum_detail"] = NULL;
		}
		if ($update["condition_medications"] == "yes") {
			$update["condition_medications_detail"] = $_POST["MedicalConditionMedicationsDetail"];
		} else {
			$update["condition_medications_detail"] = NULL;
		}

		$difficulties = $CFG['medical_difficulties'];
		foreach ($difficulties as $key => $value) {
			$update[$key] = "no";
		}
		if (isset($_POST["MedicalDifficulties"])) {
			foreach ($_POST["MedicalDifficulties"] as $key => $value) {
				$update[$value] = "yes";
			}
		}

		if ($update["difficulty_hearing"] == "yes") {
			$update["difficulty_hearing_detail"] = $_POST["MedicalDifficultyHearingDetail"];
		} else {
			$update["difficulty_hearing_detail"] = NULL;
		}
		if ($update["difficulty_speech"] == "yes") {
			$update["difficulty_speech_detail"] = $_POST["MedicalDifficultySpeechDetail"];
		} else {
			$update["difficulty_speech_detail"] = NULL;
		}
		if ($update["difficulty_eating"] == "yes") {
			$update["difficulty_eating_detail"] = $_POST["MedicalDifficultyEatingDetail"];
		} else {
			$update["difficulty_eating_detail"] = NULL;
		}
		if ($update["difficulty_vision"] == "yes") {
			$update["difficulty_vision_detail"] = $_POST["MedicalDifficultyVisionDetail"];
		} else {
			$update["difficulty_vision_detail"] = NULL;
		}
		if ($update["difficulty_bowels"] == "yes") {
			$update["difficulty_bowels_detail"] = $_POST["MedicalDifficultyBowelsDetail"];
		} else {
			$update["difficulty_bowels_detail"] = NULL;
		}
		if ($update["difficulty_urinary"] == "yes") {
			$update["difficulty_urinary_detail"] = $_POST["MedicalDifficultyUrinaryAccidentsDetail"];
		} else {
			$update["difficulty_urinary_detail"] = NULL;
		}
		if ($update["difficulty_social"] == "yes") {
			$update["difficulty_social_detail"] = $_POST["MedicalDifficultySocialDetail"];
		} else {
			$update["difficulty_social_detail"] = NULL;
		}
		if ($update["difficulty_other"] == "yes") {
			$update["difficulty_other_detail"] = $_POST["MedicalDifficultyOtherDetail"];
		} else {
			$update["difficulty_other_detail"] = NULL;
		}

		$update["behavior_favourite_activities"] = $_POST["ChildBehaviorFavouriteActivitiesDetail"];
		$update["behavior_fears"] = $_POST["ChildBehaviorFearsDetail"];
		$update["behavior_challenges"] = $_POST["ChildBehaviorBehavioralChallengesDetail"];
		$update["behavior_other"] = $_POST["ChildBehaviorOtherDetail"];

		$where = "id = '".$_POST['childID']."'";
		$db->update("child", $update, $where);
	}
}

echo json_encode($returnJSON);
exit();
?>