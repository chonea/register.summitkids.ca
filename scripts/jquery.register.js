// JavaScript Document
var selectedProgram = '';

// document ready
$(function() {

	// Load course select
	function loadCourseSelect() {
		var programID = $('input[name="programID"]').val();
		var locationID = $('input[name="locationID"]').val();
		var courseID = $('input[name="courseID"]').val();
		var activityID = $('input[name="activityID"]').val();
		var listPrograms = $.post("registration-course-select.php", { "locationID" : locationID, "programID" : programID, "courseID" : courseID, "activityID" : activityID });
	//	var listPrograms = $.post("course-program.php", { "locationID" : locationID, "programID" : programID });
		listPrograms.done(function(data) {
	//		$("#load-activities").empty().load("course-activity.php?programID=" + programID + "&courseID=" + courseID + " #course-activity-list");
			var content = $(data).find('#course-schedule');
			$('#course-schedule').empty().replaceWith(content);
			// load the proper forms for the submit page
			if ($('form#register-form-program input[name="ProgramForm_Waivers"]').val() != '') {
				$('form#register-form-submit button#button-waivers').show();
			} else {
				$('form#register-form-submit button#button-waivers').hide();
			}
			if ($('form#register-form-program input[name="ProgramForm_CC_Authorization"]').val() != '') {
				$('form#register-form-submit button#button-cc-authorization').show();
			} else {
				$('form#register-form-submit button#button-cc-authorization').hide();
			}
			if ($('form#register-form-program input[name="ProgramForm_EFT_Authorization"]').val() != '') {
				$('form#register-form-submit button#button-eft-authorization').show();
			} else {
				$('form#register-form-submit button#button-eft-authorization').hide();
			}
		});
		listPrograms.error(function (jqXHR, textStatus, errorThrown) {
			if (jqXHR.status == 500) {
				alert('Internal error: ' + jqXHR.responseText);
			} else {
				alert('Unexpected error. '+errorThrown);
			}
		});
	}
	loadCourseSelect();

	// Load program activities when program is changed
	$(document).on('change', 'select[name="RegisterProgramID"], select[name="RegisterLocationID"], select[name="RegisterCourseID"], select[name="RegisterActivityID"]', function() {

		$('input[name="programID"]').val('');
		$('input[name="locationID"]').val('');
		$('input[name="courseID"]').val('');
		$('input[name="activityID"]').val('');
		if ($('select[name="RegisterProgramID"]').val() != "") {
			$('input[name="programID"]').val($('select[name="RegisterProgramID"]').val());
			if ($('select[name="RegisterLocationID"]').val() != "") {
				$('input[name="locationID"]').val($('select[name="RegisterLocationID"]').val());
				if ($('select[name="RegisterCourseID"]').val() != "") {
					$('input[name="courseID"]').val($('select[name="RegisterCourseID"]').val());
					if ($('select[name="RegisterActivityID"]').val() != "") {
						$('input[name="activityID"]').val($('select[name="RegisterActivityID"]').val());
					}
				}
			}
		}

		loadCourseSelect();
	});

	function clearChildGuardian1() {
		// clear guardian 1
		$('input[name="guardianOneID"]').val("");
		$('input[name="GuardianOneFirstName"]').val("");
		$('input[name="GuardianOneLastName"]').val("");
		$('input[name="GuardianOneRelationship"]').val("");
		$('input[name="GuardianOneLivesWith"]').prop("checked",false);
		$('input[name="GuardianOneEmail"]').val("");
		$('input[name="GuardianOneCellPhone"]').val("");
		$('input[name="GuardianOneHomePhone"]').val("");
		$('input[name="GuardianOneDaytimePhone"]').val("");
		$('input[name="GuardianOneAddress"]').val("");
		$('input[name="GuardianOneCity"]').val("");
		$('select[name="GuardianOneProvince"]').val("");
		$('input[name="GuardianOnePostalCode"]').val("");
		$('input[name="GuardianOneDaytimeAddress"]').val("");
		$('input[name="GuardianOneDaytimeCity"]').val("");
		$('select[name="GuardianOneDaytimeProvince"]').val("");
		$('input[name="GuardianOneDaytimePostalCode"]').val("");
		$('input[name="GuardianOneCorrect"]').prop("checked",false);
		$('#guardian-1-information').slideUp();
	}
	function clearChildGuardian2() {
		// clear guardian 2
		$('input[name="guardianTwoID"]').val("");
		$('input[name="GuardianTwoFirstName"]').val("");
		$('input[name="GuardianTwoLastName"]').val("");
		$('input[name="GuardianTwoRelationship"]').val("");
		$('input[name="GuardianTwoLivesWith"]').prop("checked",false);
		$('input[name="GuardianTwoEmail"]').val("");
		$('input[name="GuardianTwoCellPhone"]').val("");
		$('input[name="GuardianTwoHomePhone"]').val("");
		$('input[name="GuardianTwoDaytimePhone"]').val("");
		$('input[name="GuardianTwoAddress"]').val("");
		$('input[name="GuardianTwoCity"]').val("");
		$('select[name="GuardianTwoProvince"]').val("");
		$('input[name="GuardianTwoPostalCode"]').val("");
		$('input[name="GuardianTwoDaytimeAddress"]').val("");
		$('input[name="GuardianTwoDaytimeCity"]').val("");
		$('select[name="GuardianTwoDaytimeProvince"]').val("");
		$('input[name="GuardianTwoDaytimePostalCode"]').val("");
		$('input[name="GuardianTwoCorrect"]').prop("checked",false);
		$('#guardian-2-information').slideUp();
	}

	//$('select[name="SelectGuardian1"] option[value=""]').prop("selected",true);
	//$('select[name="SelectGuardian2"] option[value="not-applicable"]').prop("selected",true);
		
	if ($('form#register-form-general input[name="childID"]').val()) {
		$('form#register-form-general #child-information').show();
	}

	$('form#register-form-general select[name="SelectChild"]').change(function(){
		// if none selected or add child, wipe possible existing form fields
		if ($(this).val() == "" || $(this).val() == "add-child") {
			$('form#register-form-general input[name="childID"]').val("");
			$('form#register-form-general input[name="ChildFirstName"]').val("");
			$('form#register-form-general input[name="ChildLastName"]').val("");
			$('form#register-form-general input[name="ChildBirthDate"]').val("");
			$('form#register-form-general input[name="ChildGender"]').prop("checked",false);
			// wipe child photo
			$('form#register-form-general input[name="ChildPhoto"]').val("");
			// wipe all contacts
			$('#emergency-contacts input[type="text"], #emergency-contacts select, #emergency-contacts textarea').val("");
			$('#authorized-contacts input[type="text"], #authorized-contacts select, #authorized-contacts textarea').val("");
			$('#restricted-contacts input[type="text"], #restricted-contacts select, #restricted-contacts textarea').val("");
			// wipe all medical
			// default all checkboxes to false
			$('input[name="MedicalConditions"]').each(function(){
				$(this).prop("checked",false);
			});
			$('input[name="MedicalDifficulties"]').each(function(){
				$(this).prop("checked",false);
			});
			$('#register-form-medical input[type="text"], #register-form-medical select, #register-form-medical textarea').val("");

		// we've selected a child		
		} else {
			// update the id
			$('form#register-form-general input[name="childID"]').val($(this).val());
			var form = $('#register-form-general');
			$.ajax({
				type: 'POST',
				url: 'ajax-get-child.php',
				data: form.serialize(),
				dataType: 'json',
				cache: false
			}).done(function(data) {
				// Success
				// preload general
				$('input[name="childID"]').val(data.child['id']);
				$('input[name="ChildFirstName"]').val(data.child['first_name']);
				$('input[name="ChildLastName"]').val(data.child['last_name']);
				$('input[name="ChildBirthDate"]').val(data.child['birth_date']);
				if (data.child['gender'] == "male") {
					$('input[name="ChildGender"]:eq(0)').prop("checked",true);
				} else {
					$('input[name="ChildGender"]:eq(1)').prop("checked",true);
				}
				$('input[name="ChildPhoto"]').val(data.child['photo']);
				if (data.child['photo']) {
					$('#register-form-general-child-photo-imageFile').html('<img src="' + data.child['photo'] + '" />');
				} else {
					$('#register-form-general-child-photo-imageFile').html('');
				}
				
				// preload guardians
				// clear guardian 1
				clearChildGuardian1();
				// clear guardian 2
				clearChildGuardian2();

				// load guardians for new relationships
				// Preload guardian 1
				if (typeof(data.guardians[0]) == "object") {
					$('select[name="SelectGuardian1"]').val(data.guardians[0]['id']);
					if (data.guardians[0]['id'] != '') {
						$('select[name="SelectGuardian1"] option[value="'+data.guardians[0]['id']+'"]').prop("selected",true);
					} else {
						$('select[name="SelectGuardian1"] option[value="not-applicable"]').prop("selected",true);
					}
					$('input[name="guardianOneID"]').val(data.guardians[0]['id']);
					$('input[name="GuardianOneFirstName"]').val(data.guardians[0]['first_name']);
					$('input[name="GuardianOneLastName"]').val(data.guardians[0]['last_name']);
					$('input[name="GuardianOneRelationship"]').val(data.guardians[0]['relationship']);
					if (data.guardians[0]['lives_with'] == "yes") {
						$('input[name="GuardianOneLivesWith"]:eq(0)').prop("checked",true);
					}
					if (data.guardians[0]['lives_with'] == "no") {
						$('input[name="GuardianOneLivesWith"]:eq(1)').prop("checked",true);
					}
					$('input[name="GuardianOneEmail"]').val(data.guardians[0]['email']);
					$('input[name="GuardianOneCellPhone"]').val(data.guardians[0]['cell_phone']);
					$('input[name="GuardianOneHomePhone"]').val(data.guardians[0]['home_phone']);
					$('input[name="GuardianOneDaytimePhone"]').val(data.guardians[0]['daytime_phone']);
					$('input[name="GuardianOneAddress"]').val(data.guardians[0]['address']);
					$('input[name="GuardianOneCity"]').val(data.guardians[0]['city']);
					$('select[name="GuardianOneProvince"]').val(data.guardians[0]['province']);
					$('input[name="GuardianOnePostalCode"]').val(data.guardians[0]['postal_code']);
					$('input[name="GuardianOneDaytimeAddress"]').val(data.guardians[0]['daytime_address']);
					$('input[name="GuardianOneDaytimeCity"]').val(data.guardians[0]['daytime_city']);
					$('select[name="GuardianOneDaytimeProvince"]').val(data.guardians[0]['daytime_province']);
					$('input[name="GuardianOneDaytimePostalCode"]').val(data.guardians[0]['daytime_postal_code']);
					$('input[name="GuardianOneCorrect"]').prop("checked",false);
					$('#guardian-1-information').slideDown();
				}
				// Preload guardian 2
				if (typeof(data.guardians[1]) == "object") {
					$('select[name="SelectGuardian2"]').val(data.guardians[1]['id']);
					$('input[name="guardianTwoID"]').val(data.guardians[1]['id']);
					$('input[name="GuardianTwoFirstName"]').val(data.guardians[1]['first_name']);
					$('input[name="GuardianTwoLastName"]').val(data.guardians[1]['last_name']);
					$('input[name="GuardianTwoRelationship"]').val(data.guardians[1]['relationship']);
					if (data.guardians[1]['lives_with'] == "yes") {
						$('input[name="GuardianTwoLivesWith"]:eq(0)').prop("checked",true);
					}
					if (data.guardians[1]['lives_with'] == "no") {
						$('input[name="GuardianTwoLivesWith"]:eq(1)').prop("checked",true);
					}

					$('input[name="GuardianTwoEmail"]').val(data.guardians[1]['email']);
					$('input[name="GuardianTwoCellPhone"]').val(data.guardians[1]['cell_phone']);
					$('input[name="GuardianTwoHomePhone"]').val(data.guardians[1]['home_phone']);
					$('input[name="GuardianTwoDaytimePhone"]').val(data.guardians[1]['daytime_phone']);
					$('input[name="GuardianTwoAddress"]').val(data.guardians[1]['address']);
					$('input[name="GuardianTwoCity"]').val(data.guardians[1]['city']);
					$('select[name="GuardianTwoProvince"]').val(data.guardians[1]['province']);
					$('input[name="GuardianTwoPostalCode"]').val(data.guardians[1]['postal_code']);
					$('input[name="GuardianTwoDaytimeAddress"]').val(data.guardians[1]['daytime_address']);
					$('input[name="GuardianTwoDaytimeCity"]').val(data.guardians[1]['daytime_city']);
					$('select[name="GuardianTwoDaytimeProvince"]').val(data.guardians[1]['daytime_province']);
					$('input[name="GuardianTwoDaytimePostalCode"]').val(data.guardians[1]['daytime_postal_code']);
					$('input[name="GuardianTwoCorrect"]').prop("checked",false);
					$('#guardian-2-information').slideDown();
				}
				
				// preload contacts
				// are we supposed to do this?
				while (data.child['contacts']) {
					
				}


				// preload medical
				$('input[name="AlbertaHealthCareNumber"]').val(data.child['alberta_health_care_number']);
				$('input[name="DoctorName"]').val(data.child['doctor_name']);
				$('input[name="DoctorPhone"]').val(data.child['doctor_phone']);
				$('input[name="MedicalImmunizations"]').val(data.child['doctor_phone']);
				if (data.child['condition_immunizations_up_to_date'] == "yes") {
					$('select#MedicalImmunizations').val('condition_immunizations_up_to_date');
				} else {
					$('select#MedicalImmunizations').val('condition_no_immunization');
				}
				// default all checkboxes to false
				$('input[name="MedicalConditions"]').each(function(){
					$(this).prop("checked",false);
				});
				$('input[name="MedicalDifficulties"]').each(function(){
					$(this).prop("checked",false);
				});
				$('input[name="MedicalConditions"]:eq(0)').prop("checked",convertTFYN(data.child['condition_ear_infections']));
				$('input[name="MedicalConditions"]:eq(1)').prop("checked",convertTFYN(data.child['condition_tubes_in_ears']));
				$('input[name="MedicalConditions"]:eq(2)').prop("checked",convertTFYN(data.child['condition_allergy_life_threatening']));
				$('input[name="MedicalConditions"]:eq(3)').prop("checked",convertTFYN(data.child['condition_headaches']));
				$('input[name="MedicalConditions"]:eq(4)').prop("checked",convertTFYN(data.child['condition_allergy_food']));
				$('input[name="MedicalConditions"]:eq(5)').prop("checked",convertTFYN(data.child['condition_sore_throats']));
				$('input[name="MedicalConditions"]:eq(6)').prop("checked",convertTFYN(data.child['condition_allergy_engironmental']));
				$('input[name="MedicalConditions"]:eq(7)').prop("checked",convertTFYN(data.child['condition_colds']));
				$('input[name="MedicalConditions"]:eq(8)').prop("checked",convertTFYN(data.child['condition_diabetes']));
				$('input[name="MedicalConditions"]:eq(9)').prop("checked",convertTFYN(data.child['condition_uti']));
				$('input[name="MedicalConditions"]:eq(10)').prop("checked",convertTFYN(data.child['condition_asthma_with_puffer']));
				$('input[name="MedicalConditions"]:eq(11)').prop("checked",convertTFYN(data.child['condition_stomach_upset']));
				$('input[name="MedicalConditions"]:eq(12)').prop("checked",convertTFYN(data.child['condition_add_adhd']));
				$('input[name="MedicalConditions"]:eq(13)').prop("checked",convertTFYN(data.child['condition_epilepsy']));
				$('input[name="MedicalConditions"]:eq(14)').prop("checked",convertTFYN(data.child['condition_heart_condition']));
				$('input[name="MedicalConditions"]:eq(15)').prop("checked",convertTFYN(data.child['condition_autism_spectrum_condition']));
				$('input[name="MedicalConditions"]:eq(16)').prop("checked",convertTFYN(data.child['condition_medications']));

				$('textarea[name="MedicalConditionAllergyLifeThreateningDetail"]').val(data.child['condition_allergy_life_threatening_detail']);
				$('textarea[name="MedicalConditionAllergyFoodDetail"]').val(data.child['condition_allergy_food_detail']);
				$('textarea[name="MedicalConditionAllergyEnvironmentalDetail"]').val(data.child['condition_allergy_environmental_detail']);
				$('textarea[name="MedicalConditionDiabetesDetail"]').val(data.child['condition_diabetes_detail']);
				$('textarea[name="MedicalConditionADDADHDDetail"]').val(data.child['condition_add_adhd_detail']);
				$('textarea[name="MedicalConditionEpilepsyDetail"]').val(data.child['condition_epilepsy_detail']);
				$('textarea[name="MedicalConditionHeartConditionDetail"]').val(data.child['condition_heart_condition_detail']);
				$('textarea[name="MedicalConditionAutismSpectrumDetail"]').val(data.child['condition_autism_spectrum_detail']);
				$('textarea[name="MedicalConditionMedicationsDetail"]').val(data.child['condition_medications_detail']);

				$('input[name="MedicalDifficulties"]:eq(0)').prop("checked",convertTFYN(data.child['difficulty_hearing']));
				$('input[name="MedicalDifficulties"]:eq(1)').prop("checked",convertTFYN(data.child['difficulty_speech']));
				$('input[name="MedicalDifficulties"]:eq(2)').prop("checked",convertTFYN(data.child['difficulty_eating']));
				$('input[name="MedicalDifficulties"]:eq(3)').prop("checked",convertTFYN(data.child['difficulty_vision']));
				$('input[name="MedicalDifficulties"]:eq(4)').prop("checked",convertTFYN(data.child['difficulty_bowels']));
				$('input[name="MedicalDifficulties"]:eq(5)').prop("checked",convertTFYN(data.child['difficulty_urinary']));
				$('input[name="MedicalDifficulties"]:eq(6)').prop("checked",convertTFYN(data.child['difficulty_social']));
				$('input[name="MedicalDifficulties"]:eq(7)').prop("checked",convertTFYN(data.child['difficulty_other']));

				$('textarea[name="MedicalDifficultyHearingDetail"').val(data.child['difficulty_hearing_detail']);
				$('textarea[name="MedicalDifficultySpeechDetail"').val(data.child['difficulty_speech_detail']);
				$('textarea[name="MedicalDifficultyEatingDetail"').val(data.child['difficulty_eating_detail']);
				$('textarea[name="MedicalDifficultyVisionDetail"').val(data.child['difficulty_vision_detail']);
				$('textarea[name="MedicalDifficultyBowelsDetail"').val(data.child['difficulty_bowels_detail']);
				$('textarea[name="MedicalDifficultyUrinaryDetail"').val(data.child['difficulty_urinary_detail']);
				$('textarea[name="MedicalDifficultySocialDetail"').val(data.child['difficulty_social_detail']);
				$('textarea[name="MedicalDifficultyOtherDetail"').val(data.child['difficulty_other_detail']);

				$('textarea[name="ChildBehaviorFavouriteActivitiesDetail"').val(data.child['behavior_favourite_activities']);
				$('textarea[name="ChildBehaviorFearsDetail"').val(data.child['behavior_fears']);
				$('textarea[name="ChildBehaviorBehavioralChallengesDetail"').val(data.child['behavior_challenges']);
				$('textarea[name="ChildBehaviorOtherDetail"').val(data.child['behavior_other']);

			}).fail(function(data) {
				// Error
				$('#registration-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			});
		
		}
		if ($(this).val() == "") {
			$('#child-information').slideUp();
		} else {
			$('#child-information').slideDown();
		}
	});
	
	// Guardian 1
	if ($('input[name="guardianOneID"]').val()) {
		$('#guardian-1-information').show();
	}
	$('select[name="SelectGuardian1"]').change(function(){
		if ($(this).val() == "" || $(this).val() == "add-guardian") {
			// clear all fields
			clearChildGuardian1();
			$('#guardian-1-information').slideDown();
		} else {
			$('input[name="guardianOneID"]').val($('select[name="SelectGuardian1"]').val());

			var form = $('#register-form-general');
			$.ajax({
				type: 'POST',
				url: 'ajax-get-guardian.php?guardianID=' + $('input[name="guardianOneID"]').val(),
				data: form.serialize(),
				dataType: 'json',
				cache: false
			}).done(function(data) {
				// Success
				// Preload guardian 1
				if (data.guardian['id'] != '') {
					$('select[name="SelectGuardian1"] option[value="'+data.guardian['id']+'"]').prop("selected",true);
				} else {
					$('select[name="SelectGuardian1"] option[value="not-applicable"]').prop("selected",true);
				}
				$('input[name="guardianOneID"]').val(data.guardian['id']);
				$('input[name="GuardianOneFirstName"]').val(data.guardian['first_name']);
				$('input[name="GuardianOneLastName"]').val(data.guardian['last_name']);
				$('input[name="GuardianOneRelationship"]').val(data.guardian['relationship']);
				if (data.guardian['lives_with'] == "yes") {
					$('input[name="GuardianOneLivesWith"]:eq(0)').prop("checked",true);
				}
				if (data.guardian['lives_with'] == "no") {
					$('input[name="GuardianOneLivesWith"]:eq(1)').prop("checked",true);
				}
				$('input[name="GuardianOneEmail"]').val(data.guardian['email']);
				$('input[name="GuardianOneCellPhone"]').val(data.guardian['cell_phone']);
				$('input[name="GuardianOneHomePhone"]').val(data.guardian['home_phone']);
				$('input[name="GuardianOneDaytimePhone"]').val(data.guardian['daytime_phone']);
				$('input[name="GuardianOneAddress"]').val(data.guardian['address']);
				$('input[name="GuardianOneCity"]').val(data.guardian['city']);
				$('select[name="GuardianOneProvince"]').val(data.guardian['province']);
				$('input[name="GuardianOnePostalCode"]').val(data.guardian['postal_code']);
				$('input[name="GuardianOneDaytimeAddress"]').val(data.guardian['daytime_address']);
				$('input[name="GuardianOneDaytimeCity"]').val(data.guardian['daytime_city']);
				$('select[name="GuardianOneDaytimeProvince"]').val(data.guardian['daytime_province']);
				$('input[name="GuardianOneDaytimePostalCode"]').val(data.guardian['daytime_postal_code']);
			}).fail(function(data) {
				// Error
				$('#registration-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			});
		
		}
		if ($(this).val() == "") {
			$('#guardian-1-information').slideUp();
		} else {
			$('#guardian-1-information').slideDown();
		}
	});
	
	// Guardian 2
	if ($('input[name="guardianTwoID"]').val() != '') {
		$('#guardian-2-information').show();
		$('input[name="GuardianTwoFirstName"]').attr("required", "required");
		$('input[name="GuardianTwoLastName"]').attr("required", "required");
		$('input[name="GuardianTwoRelationship"]').attr("required", "required");
		$('input[name="GuardianTwoLivesWith"]').attr("required", "required");
		$('input[name="GuardianTwoEmail"]').attr("required", "required");
		$('input[name="GuardianTwoDaytimePhone"]').attr("required", "required");
		$('input[name="GuardianTwoAddress"]').attr("required", "required");
		$('select[name="GuardianTwoProvince"]').attr("required", "required");
		$('input[name="GuardianTwoPostalCode"]').attr("required", "required");
		$('input[name="GuardianTwoCorrect"]').attr("required", "required");
	} else {
		$('#guardian-2-information input, #guardian-2-information select').removeAttr("required");
	}
	$('select[name="SelectGuardian2"]').change(function(){
		if ($(this).val() == "" || $(this).val() == "add-guardian") {
			// clear all fields
			clearChildGuardian2();
			$('#guardian-2-information').slideDown();
		} else {
			$('input[name="guardianTwoID"]').val($('select[name="SelectGuardian2"]').val());

			var form = $('#register-form-general');
			$.ajax({
				type: 'POST',
				url: 'ajax-get-guardian.php?guardianID=' + $('input[name="guardianTwoID"]').val(),
				data: form.serialize(),
				dataType: 'json',
				cache: false
			}).done(function(data) {
				// Success
				// Preload guardian 2
				if (data.guardian['id'] != '') {
					$('select[name="SelectGuardian2"] option[value="'+data.guardian['id']+'"]').prop("selected",true);
				} else {
					$('select[name="SelectGuardian2"] option[value="not-applicable"]').prop("selected",true);
				}
				$('input[name="guardianTwoID"]').val(data.guardian['id']);
				$('input[name="GuardianTwoFirstName"]').val(data.guardian['first_name']);
				$('input[name="GuardianTwoLastName"]').val(data.guardian['last_name']);
				$('input[name="GuardianTwoRelationship"]').val(data.guardian['relationship']);
				if (data.guardian['lives_with'] == "yes") {
					$('input[name="GuardianTwoLivesWith"]:eq(0)').prop("checked",true);
				}
				if (data.guardian['lives_with'] == "no") {
					$('input[name="GuardianTwoLivesWith"]:eq(1)').prop("checked",true);
				}
				$('input[name="GuardianTwoEmail"]').val(data.guardian['email']);
				$('input[name="GuardianTwoCellPhone"]').val(data.guardian['cell_phone']);
				$('input[name="GuardianTwoHomePhone"]').val(data.guardian['home_phone']);
				$('input[name="GuardianTwoDaytimePhone"]').val(data.guardian['daytime_phone']);
				$('input[name="GuardianTwoAddress"]').val(data.guardian['address']);
				$('input[name="GuardianTwoCity"]').val(data.guardian['city']);
				$('select[name="GuardianTwoProvince"]').val(data.guardian['province']);
				$('input[name="GuardianTwoPostalCode"]').val(data.guardian['postal_code']);
				$('input[name="GuardianTwoDaytimeAddress"]').val(data.guardian['daytime_address']);
				$('input[name="GuardianTwoDaytimeCity"]').val(data.guardian['daytime_city']);
				$('select[name="GuardianTwoDaytimeProvince"]').val(data.guardian['daytime_province']);
				$('input[name="GuardianTwoDaytimePostalCode"]').val(data.guardian['daytime_postal_code']);
			}).fail(function(data) {
				// Error
				$('#registration-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			});
		
		}
		if ($(this).val() == "not-applicable") {
			$('#guardian-2-information input, #guardian-2-information select').removeAttr("required");
			$('#guardian-2-information').slideUp();
		} else {
			$('#guardian-2-information').slideDown();
			$('input[name="GuardianTwoFirstName"]').attr("required", "required");
			$('input[name="GuardianTwoLastName"]').attr("required", "required");
			$('input[name="GuardianTwoRelationship"]').attr("required", "required");
			$('input[name="GuardianTwoLivesWith"]').attr("required", "required");
			$('input[name="GuardianTwoEmail"]').attr("required", "required");
			$('input[name="GuardianTwoDaytimePhone"]').attr("required", "required");
			$('input[name="GuardianTwoAddress"]').attr("required", "required");
			$('select[name="GuardianTwoProvince"]').attr("required", "required");
			$('input[name="GuardianTwoPostalCode"]').attr("required", "required");
		}
	});

	$("input[name='MedicalConditions[]']").eq(2).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-allergy-life-threatening',
				'title' : 'Allergy - life threatening',
				'beforeClose' : function() {
					if ($("#medical-conditions-allergy-life-threatening textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(2).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(4).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-allergy-food',
				'title' : 'Allergy - food',
				'beforeClose' : function() {
					if ($("#medical-conditions-allergy-food textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(4).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(6).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-allergy-environmental',
				'title' : 'Allergy - environmental',
				'beforeClose' : function() {
					if ($("#medical-conditions-allergy-environmental textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(6).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(8).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-diabetes',
				'title' : 'Diabetes',
				'beforeClose' : function() {
					if ($("#medical-conditions-diabetes textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(8).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(9).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-uti',
				'title' : 'UTI',
				'beforeClose' : function() {
					if ($("#medical-conditions-uti textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(9).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(12).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-add-adhd',
				'title' : 'ADD/ADHD',
				'beforeClose' : function() {
					if ($("#medical-conditions-add-adhd textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(12).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(13).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-epilepsy',
				'title' : 'Epilepsy',
				'beforeClose' : function() {
					if ($("#medical-conditions-epilepsy textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(13).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(14).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-heart-condition',
				'title' : 'Heart condition',
				'beforeClose' : function() {
					if ($("#medical-conditions-heart-condition textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(14).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(15).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-autism-spectrum',
				'title' : 'Autism spectrum',
				'beforeClose' : function() {
					if ($("#medical-conditions-autism-spectrum textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(15).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalConditions[]']").eq(16).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-medications',
				'title' : 'Medications',
				'beforeClose' : function() {
					if ($("#medical-conditions-medications textarea").val() == '') {
						$("input[name='MedicalConditions[]']").eq(16).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(0).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-hearing',
				'title' : 'Hearing',
				'beforeClose' : function() {
					if ($("#medical-difficulties-hearing textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(0).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(1).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-speech',
				'title' : 'Speech',
				'beforeClose' : function() {
					if ($("#medical-difficulties-speech textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(1).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(2).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-eating',
				'title' : 'Eating',
				'beforeClose' : function() {
					if ($("#medical-difficulties-eating textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(2).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(3).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-vision',
				'title' : 'Vision',
				'beforeClose' : function() {
					if ($("#medical-difficulties-vision textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(3).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(4).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-bowels',
				'title' : 'Bowels',
				'beforeClose' : function() {
					if ($("#medical-difficulties-bowels textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(4).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(5).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-urinary-accidents',
				'title' : 'Urinary accidents',
				'beforeClose' : function() {
					if ($("#medical-difficulties-urinary-accidents textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(5).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(6).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-social',
				'title' : 'Making friends',
				'beforeClose' : function() {
					if ($("#medical-difficulties-social textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(6).click();
					}
				}
			});
		}
	});

	$("input[name='MedicalDifficulties[]']").eq(7).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-other',
				'title' : 'Other',
				'beforeClose' : function() {
					if ($("#medical-difficulties-other textarea").val() == '') {
						$("input[name='MedicalDifficulties[]']").eq(7).click();
					}
				}
			});
		}
	});

	$(".fancybox").fancybox({
		'autoScale': true,
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'speedIn': 500,
		'speedOut': 300,
		'autoDimensions': false,
		'width': 560,
		'height': 340,
		'centerOnScroll': true
	});

/******************************************************************************************************/

	$('.form-page').hide();
	$('#registration-page-program').show();
	
	$('#registration-page-general .back-button').click(function() {
		showFormPage("registration-page-program");
	});
	
	$('#registration-page-medical .back-button').click(function() {
		showFormPage("registration-page-general");
	});
	
	$('#registration-page-submit .back-button').click(function() {
		showFormPage("registration-page-medical");
	});

	// Submit course selection
	$('form#register-form-program').submit(function(event) {

		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-register-course-select.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			$('input[name="registerID"]').val(data.registerID);
			showFormPage("registration-page-general");
		}).fail(function(data) {
			// Error
			$('#registration-page-program .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});
/*
	$('input[name="childBirthDate"]').keydown(function() {
		$(this).blur();
	});
*/
	$('form#register-form-general').submit(function(event) {
//		if (!$(this).find('input[name="ChildPhoto"]').val()) {
//			$('#registration-page-general .ajax-response').html('<div class="ajax-message sessions-error">Child Photo is required.</div>');
//		} else {
			var form = $(this);
			$.ajax({
				type: 'POST',
				url: 'ajax-general.php',
				data: form.serialize(),
				dataType: 'json',
				cache: false
			}).done(function(data) {
				if (data.success === true) {
					// Success
					$('input[name="childID"]').val(data.childID);
					if (data.imageFile) $('#register-form-general-child-photo-imageFile').html('<img src="' + data.imageFile + '" style="width: 200px;" />');
					if (data.guardianOneID) $('input[name="guardianOneID"]').val(data.guardianOneID);
					if (data.guardianTwoID) $('input[name="guardianTwoID"]').val(data.guardianTwoID);
					showFormPage("registration-page-contacts");
				} else {
					// Validation failed
					$('#registration-page-general .ajax-response').html('<div class="ajax-message">' + JSON.stringify(data) + '</div>');
				}
			}).fail(function(data) {
				// Error
				$('#registration-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			});
//		}
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	$('form#register-form-contacts').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-contacts.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			if (data.emergencyID1) $('input[name="emergencyID1"]').val(data.emergencyID1);
			if (data.emergencyID2) $('input[name="emergencyID2"]').val(data.emergencyID2);
			if (data.emergencyID3) $('input[name="emergencyID3"]').val(data.emergencyID3);
			if (data.authorizedID1) $('input[name="authorizedID1"]').val(data.authorizedID1);
			if (data.authorizedID2) $('input[name="authorizedID2"]').val(data.authorizedID2);
			if (data.authorizedID3) $('input[name="authorizedID3"]').val(data.authorizedID3);
			if (data.restrictedID1) $('input[name="restrictedID1"]').val(data.restrictedID1);
			if (data.restrictedID2) $('input[name="restrictedID2"]').val(data.restrictedID2);
			if (data.restrictedID3) $('input[name="restrictedID3"]').val(data.restrictedID3);
			showFormPage("registration-page-medical");
		}).fail(function(data) {
			// Error
			$('#registration-page-contacts .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	$('form#register-form-medical').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-medical.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			showFormPage("registration-page-submit");
		}).fail(function(data) {
			// Error
			$('#registration-page-medical .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

/******************************************************************************************************/

	// If Back button is clicked after program info is entered...
	/*
	$('input#register-form-general-element-58').click(function(){
		if ($('input[name="selectedProgram"]').val() == "summit-spring") {
			showFormPage('registration-page-program-summit-spring');
		} else if ($('input[name="selectedProgram"]').val() == "summit-summer") {
			showFormPage('registration-page-program-summit-summer');
		} else {
			showFormPage('registration-page-program-summit-kids-k');
		}
		return false; // Prevent the form from submitting via the browser.
	});
*/
	// Preload emergency contacts
	var showEmergencyContacts = 0;
	if ($('input[name="emergencyID1"]').val()) {
			showEmergencyContacts++;
	}
	if ($('input[name="emergencyID2"]').val()) {
			showEmergencyContacts++;
	}
	if ($('input[name="emergencyID3"]').val()) {
			showEmergencyContacts++;
			$('#emergency-contact-3').removeClass('hidden-content');
	}
	if (showEmergencyContacts == 3) {
		$('button#add-emergency-contact').hide();
	}
	if (showEmergencyContacts < 2) showEmergencyContacts = 2;
	$('button#add-emergency-contact').click(function(){
		if (showEmergencyContacts < 3) {
			$(this).text("Add Another Emergency Contact");
			showEmergencyContacts++;
			$("#emergency-contact-" + showEmergencyContacts).show();
			if (showEmergencyContacts == 3) {
				$(this).hide();
			}
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// Preload authorized contacts
	var showAuthorizedContacts = 0;
	function addAuthorizedContact() {
		showAuthorizedContacts++;
		$("#authorized-contact-" + showAuthorizedContacts).show();
		if (showAuthorizedContacts < 3) {
			$('button#add-authorized-contact').text("Add Another Authorized Contact");
		} else {
			$('button#add-authorized-contact').hide();
		}
	}
	if ($('input[name="authorizedID1"]').val()) {
		addAuthorizedContact();
	}
	if ($('input[name="authorizedID2"]').val()) {
		addAuthorizedContact();
	}
	if ($('input[name="authorizedID3"]').val()) {
		addAuthorizedContact();
	}
	$('button#add-authorized-contact').click(function(){
		if (showAuthorizedContacts < 3) {
			addAuthorizedContact();
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// Preload emergency contacts
	var showRestrictedContacts = 0;
	function addRestrictedContact() {
		showRestrictedContacts++;
		$("#restricted-contact-" + showRestrictedContacts).show();
		if (showRestrictedContacts < 3) {
			$('button#add-restricted-contact').text("Add Another Restricted Contact");
		} else {
			$('button#add-restricted-contact').hide();
		}
	}
	if ($('input[name="restrictedID1"]').val()) {
		addRestrictedContact();
	}
	if ($('input[name="restrictedID2"]').val()) {
		addRestrictedContact();
	}
	if ($('input[name="restrictedID3"]').val()) {
		addRestrictedContact();
	}
	$('button#add-restricted-contact').click(function(){
		if (showRestrictedContacts < 3) {
			addRestrictedContact();
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// Summit Summer
	var costType = "nonmember";
	
	var summerSessions = new Array();
	summerSessions[1] = {member: 176, nonmember: 193};
	summerSessions[2] = {member: 220, nonmember: 242};
	summerSessions[3] = {member: 220, nonmember: 242};
	summerSessions[4] = {member: 220, nonmember: 242};
	summerSessions[5] = {member: 220, nonmember: 242};
	summerSessions[6] = {member: 176, nonmember: 193};
	summerSessions[7] = {member: 220, nonmember: 242};
	summerSessions[8] = {member: 220, nonmember: 242};
	summerSessions[9] = {member: 220, nonmember: 242};

	$('#summit-summer-sessions .control-label').hide();
	$('#summit-summer-sessions .controls').css('margin-left','0');
	$('#summit-summer-sessions .controls').prepend('<legend id="summit-summer-sessions-header" class="row-fluid"></legend>');
	$('#summit-summer-sessions-header').append('<div class="span3" style="float: left;">Date</div>');
	$('#summit-summer-sessions-header').append('<div class="span3" style="float: left;">Activity</div>');
	$('#summit-summer-sessions-header').append('<div class="span2" style="float: left;">Cost</div>');
	$('#summit-summer-sessions-header').append('<div class="span2" style="float: left;">Pre Care (7-8 AM)</div>');
	$('#summit-summer-sessions-header').append('<div class="span2" style="float: left;">After Care (5-6 PM)</div>');
	var sessionCount = 0;
	$('#summit-summer-sessions .controls label.checkbox').each(function(){
		$(this).find('input').addClass('row-toggle');
		sessionCount++;
		$(this).wrapAll('<div id="summit-summer-session-' + sessionCount + '" class="row-fluid accounting-table session-row" />');
		$(this).wrapAll('<div id="summit-summer-session-' + sessionCount + '-checkbox" class="span3 accounting-cell" />');
		$('#summit-summer-session-' + sessionCount).append('<div id="summit-summer-session-' + sessionCount + '-activity" class="span3 accounting-cell"></div>');
		$('#summit-summer-session-' + sessionCount + '-activity').append($('select[name="SummerSession' + sessionCount + 'Activity"]'));
		$('#summit-summer-session-' + sessionCount).append('<div id="summit-summer-session-' + sessionCount + '-cost" class="span2 accounting-cell"></div>');
		$('#summit-summer-session-' + sessionCount + '-cost').append($('input[name="SummerSession' + sessionCount + 'Cost"]'));
		$('input[name="SummerSession' + sessionCount + 'Cost"]').addClass('cost-update');
		$('input[name="SummerSession' + sessionCount + 'Cost"]').val(summerSessions[sessionCount][costType]);
		$('#summit-summer-session-' + sessionCount + '-cost').append($('input[name="SummerSession' + sessionCount + 'CostFormatted"]'));
		$('input[name="SummerSession' + sessionCount + 'CostFormatted"]').addClass('cost-format');
		$('input[name="SummerSession' + sessionCount + 'CostFormatted"]').val(accounting.formatMoney(summerSessions[sessionCount][costType]));
		$('#summit-summer-session-' + sessionCount).append('<div id="summit-summer-session-' + sessionCount + '-am" class="span2 accounting-cell"></div>');
		$('#summit-summer-session-' + sessionCount + '-am').append($('input[name="SummerSession' + sessionCount + 'AM[]"]').parent('label'));
		$('input[name="SummerSession' + sessionCount + 'AM[]"]').addClass('cost-toggle');
		$('#summit-summer-session-' + sessionCount).append('<div id="summit-summer-session-' + sessionCount + '-pm" class="span2 accounting-cell"></div>');
		$('#summit-summer-session-' + sessionCount + '-pm').append($('input[name="SummerSession' + sessionCount + 'PM[]"]').parent('label'));
		$('input[name="SummerSession' + sessionCount + 'PM[]"]').addClass('cost-toggle');
	});

	var summerExtendedSessions = new Array();
	summerExtendedSessions[1] = {member: 440, nonmember: 484};
	summerExtendedSessions[2] = {member: 440, nonmember: 484};

	$('#summit-summer-extended-sessions .control-label').hide();
	$('#summit-summer-extended-sessions .controls').css('margin-left','0');
	$('#summit-summer-extended-sessions .controls').prepend('<legend id="summit-summer-extended-sessions-header" class="row-fluid"></legend>');
	$('#summit-summer-extended-sessions-header').append('<div class="span3" style="float: left;">Date</div>');
	$('#summit-summer-extended-sessions-header').append('<div class="span3" style="float: left;">Activity</div>');
	$('#summit-summer-extended-sessions-header').append('<div class="span2" style="float: left;">Cost</div>');
	$('#summit-summer-extended-sessions-header').append('<div class="span2" style="float: left;">Pre Care (7-8 AM)</div>');
	$('#summit-summer-extended-sessions-header').append('<div class="span2" style="float: left;">After Care (5-6 PM)</div>');
	var sessionCount = 0;
	$('#summit-summer-extended-sessions .controls label.checkbox').each(function(){
		$(this).find('input').addClass('row-toggle');
		sessionCount++;
		$(this).wrapAll('<div id="summit-summer-extended-session-' + sessionCount + '" class="row-fluid accounting-table session-row" />');
		$(this).wrapAll('<div id="summit-summer-extended-session-' + sessionCount + '-checkbox" class="span3 accounting-cell" />');
		$('#summit-summer-extended-session-' + sessionCount).append('<div id="summit-summer-extended-session-' + sessionCount + '-activity" class="span3 accounting-cell"></div>');
		$('#summit-summer-extended-session-' + sessionCount + '-activity').append($('select[name="SummerExtendedSession' + sessionCount + 'Activity"]'));
		$('#summit-summer-extended-session-' + sessionCount).append('<div id="summit-summer-extended-session-' + sessionCount + '-cost" class="span2 accounting-cell"></div>');
		$('#summit-summer-extended-session-' + sessionCount + '-cost').append($('input[name="SummerExtendedSession' + sessionCount + 'Cost"]'));
		$('input[name="SummerExtendedSession' + sessionCount + 'Cost"]').addClass('cost-update');
		$('input[name="SummerExtendedSession' + sessionCount + 'Cost"]').val(summerExtendedSessions[sessionCount][costType]);
		$('#summit-summer-extended-session-' + sessionCount + '-cost').append($('input[name="SummerExtendedSession' + sessionCount + 'CostFormatted"]'));
		$('input[name="SummerExtendedSession' + sessionCount + 'CostFormatted"]').addClass('cost-format');
		$('input[name="SummerExtendedSession' + sessionCount + 'CostFormatted"]').val(accounting.formatMoney(summerExtendedSessions[sessionCount][costType]));
		$('#summit-summer-extended-session-' + sessionCount).append('<div id="summit-summer-extended-session-' + sessionCount + '-am" class="span2 accounting-cell"></div>');
		$('#summit-summer-extended-session-' + sessionCount + '-am').append($('input[name="SummerExtendedSession' + sessionCount + 'AM[]"]').parent('label'));
		$('input[name="SummerExtendedSession' + sessionCount + 'AM[]"]').addClass('cost-toggle');
		$('#summit-summer-extended-session-' + sessionCount).append('<div id="summit-summer-extended-session-' + sessionCount + '-pm" class="span2 accounting-cell"></div>');
		$('#summit-summer-extended-session-' + sessionCount + '-pm').append($('input[name="SummerExtendedSession' + sessionCount + 'PM[]"]').parent('label'));
		$('input[name="SummerExtendedSession' + sessionCount + 'PM[]"]').addClass('cost-toggle');
	});

	$('#summit-summer-extended-sessions .controls').append('<legend id="summit-summer-extended-sessions-footer" class="row-fluid" style="margin-top: 40px;"></legend>');
	$('#summit-summer-extended-sessions .controls').append('<div class="row-fluid"><div class="span9" style="float: left; text-align: right;"><span class="info-message">Discounts will be applied at time of submission.</span></div><h4 id="summit-summer-total-cost" class="span3" style="float: left;">{Total}</h4></div>');

	function totalSessionCost() {
		var totalCost = 0;
		// Add session cost
		var sessionCount = 0;
		$('#summit-summer-sessions .session-row').each(function() {
			sessionCount++;
			var thisSession = $(this);
			if (thisSession.find('.row-toggle').is(':checked')) {
				totalCost += parseFloat(thisSession.find('.cost-update').val());
				$(thisSession).find('.cost-toggle').each(function() {
					if ($(this).is(':checked')) {
						totalCost += parseFloat($(this).val());
					}
				});
			}
		});
		// Add extended session cost
		var sessionCount = 0;
		$('#summit-summer-extended-sessions .session-row').each(function() {
			sessionCount++;
			var thisSession = $(this);
			if (thisSession.find('.row-toggle').is(':checked')) {
				totalCost += parseFloat(thisSession.find('.cost-update').val());
				$(thisSession).find('.cost-toggle').each(function() {
					if ($(this).is(':checked')) {
						totalCost += parseFloat($(this).val());
					}
				});
			}
		});

		$('#summit-summer-total-cost').html('Total Cost: ' + accounting.formatMoney(accounting.toFixed(totalCost, 2)));
	}
	totalSessionCost();

	$('input[name="SummerSessions[]"], input[name="SummerExtendedSessions[]"], .cost-toggle').change(function() {
		totalSessionCost();
		if ($(this).is(':checked')) {
			$(this).closest('.session-row').find('.session-activity').attr("required", "required");
//			$(this).closest('.session-row').find('.session-activity').after('<span class="required"> * </span>');
			$('#register-form-program-summit-summer-element-78').removeAttr('disabled'); // if we add more fields, the ID of the submit button will change
			$('#registration-page-program-summit-summer .sessions-error').remove();
		} else {
			$(this).closest('.session-row').find('.session-activity').removeAttr("required");
//			$(this).closest('.session-row').find('.session-activity').next('span.required').remove();
		}
	});

	// Extended sessions
	$('#register-form-program-summit-summer-element-61-0').change(function() {
		if ($(this).is(':checked')) {
			$('#register-form-program-summit-summer-element-8-1').attr('checked',false);
			$('#register-form-program-summit-summer-element-8-2').attr('checked',false);
		}
		totalSessionCost();
	});
	$('#register-form-program-summit-summer-element-61-1').change(function() {
		if ($(this).is(':checked')) {
			$('#register-form-program-summit-summer-element-8-3').attr('checked',false);
			$('#register-form-program-summit-summer-element-8-4').attr('checked',false);
		}
		totalSessionCost();
	});
	$('#register-form-program-summit-summer-element-8-1, #register-form-program-summit-summer-element-8-2').change(function() {
		if ($(this).is(':checked')) {
			$('#register-form-program-summit-summer-element-61-0').attr('checked',false);
		}
		totalSessionCost();
	});
	$('#register-form-program-summit-summer-element-8-3, #register-form-program-summit-summer-element-8-4').change(function() {
		if ($(this).is(':checked')) {
			$('#register-form-program-summit-summer-element-61-1').attr('checked',false);
		}
		totalSessionCost();
	});

	$('input[name="SummitID"]').blur(function() {
		if ($(this).val() != '') {
			costType = "member"
		} else {
			costType = "nonmember"
		}
		// Update session cost
		sessionCount = 0;
		$('#summit-summer-sessions input.cost-update').each(function() {
			sessionCount++;
			$(this).val(summerSessions[sessionCount][costType]);
			$('input[name="' + $(this).attr('name') + 'Formatted"]').val(accounting.formatMoney(summerSessions[sessionCount][costType]));
		});
		// Update extended session cost
		sessionCount = 0;
		$('#summit-summer-extended-sessions input.cost-update').each(function() {
			sessionCount++;
			$(this).val(summerExtendedSessions[sessionCount][costType]);
			$('input[name="' + $(this).attr('name') + 'Formatted"]').val(accounting.formatMoney(summerExtendedSessions[sessionCount][costType]));
		});
		totalSessionCost();
	});
	
	// Show correct payment authorization buttons
//	$('input[name="selectedProgram"]').change(function(){
/*
	$('input#register-form-program-element-2').change(function(){
		if ($(this).val() == "summit-spring" || $(this).val() == "summit-summer") {
			$('input#register-form-submit-element-4').show();  // Show CC Authorization button
			$('input#register-form-submit-element-5').hide();  // Hide EFT Authorization button
		} else {
			$('input#register-form-submit-element-4').show();  // Show CC Authorization button
			$('input#register-form-submit-element-5').show();  // Show EFT Authorization button
		}
	});
*/
	// Child photo
/*
	$('input[name="ChildPhoto"]').change(function() {
		$('#register-form-general-element-67').removeAttr('disabled'); // if we add more fields, the ID of the submit button will change
	});
*/
	$('#register-form-general-child-photo').click(function() {
		$('#register-form-general-element-67').removeAttr('disabled'); // if we add more fields, the ID of the submit button will change
		$('#registration-page-general .sessions-error').remove();
	});


/************************
 ** SUBMIT PAGE
 ************************/

	// If My Registration is clicked...
	$('input.register-form-submit-btn-my-registration').click(function(event){
		var form = $('form#register-form-submit');
		$.ajax({
			type: 'POST',
			url: 'ajax-print.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			window.open("print.php?registerID=" + $('#register-form-submit input[name="registerID"]').val());
		}).fail(function(data) {
			// Error
			$('#registration-page-submit .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	// If CC Authorization is clicked...
	$('input.register-form-submit-btn-cc-authorization').click(function(){
		if ($('form#register-form-program input[name="ProgramForm_CC_Authorization"]').val() != '') {
			window.open($('form#register-form-program input[name="ProgramForm_CC_Authorization"]').val());
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// If EFT Authorization is clicked...
	$('input.register-form-submit-btn-eft-authorization').click(function(){
		if ($('form#register-form-program input[name="ProgramForm_EFT_Authorization"]').val() != '') {
			window.open($('form#register-form-program input[name="ProgramForm_EFT_Authorization"]').val());
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// If Waivers is clicked...
	$('input.register-form-submit-btn-waivers').click(function(){
		if ($('form#register-form-program input[name="ProgramForm_Waivers"]').val() != '') {
			window.open($('form#register-form-program input[name="ProgramForm_Waivers"]').val());
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// If Submit Application is clicked...
	$('input.register-form-submit-btn-submit').click(function(event){
		var form = $('form#register-form-submit');
		$.ajax({
			type: 'POST',
			url: 'ajax-register-submit.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			location.href = '/';
		}).fail(function(data) {
			// Error
			$('#registration-page-submit .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
//		form.submit();
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	// hide all the old buttons
	$('#register-form-submit-element-4').hide();
	$('#register-form-submit-element-5').hide();
	$('#register-form-submit-element-6').hide();
	$('#register-form-submit-element-7').hide();

	// for the new buttons, trigger old buttons
	$('#button-my-registration').click(function() {
		$('input.register-form-submit-btn-my-registration').trigger('click');
	});
	$('#button-waivers').click(function() {
		$('input.register-form-submit-btn-waivers').trigger('click');
	});
	$('#button-cc-authorization').click(function() {
		$('input.register-form-submit-btn-cc-authorization').trigger('click');
	});
	$('#button-eft-authorization').click(function() {
		$('input.register-form-submit-btn-eft-authorization').trigger('click');
	});

});