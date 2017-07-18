// JavaScript Document


/*****************************************************************************************************
 * Setup
 *****************************************************************************************************/

// document ready
$(function() {

	// Fancybox setup
	$("#child-page .fancybox").fancybox({
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
	     
	// FileUploader setup
	function createUploader(){            
		var uploader = new qq.FileUploader({
			element: document.getElementById('child-form-general-child-photo'),
			action: 'file-upload.php',
			// additional data to send, name-value pairs
			params: {},
			// validation    
			// ex. ['jpg', 'jpeg', 'png', 'gif'] or []
			//allowedExtensions: [],        
			// each file size limit in bytes
			// this option isn't supported in all browsers
			//sizeLimit: 0, // max size   
			//minSizeLimit: 0, // min size
			abortOnFailure: true, // Fail all files if one doesn't meet the criteria
			// set to true to output server response to console
			debug: false,
			// events         
			// you can return false to abort submit
			onSubmit: function(id, fileName){},
			onProgress: function(id, fileName, loaded, total){},
			onComplete: function(id, fileName, responseJSON){
				$('#child-page input[name="ChildPhoto"]').val(responseJSON.saveFile);
			},
			onCancel: function(id, fileName){},
			onError: function(id, fileName, xhr){},
			messages: {
				// error messages, see qq.FileUploaderBasic for content            
			},
			showMessage: function(message){ alert(message); }        
			});           
	}
	
	// in your app create uploader as soon as the DOM is ready
	// don't wait for the window to load  
	window.onload = createUploader;

	// Page setup
	$('#child-page .form-page').hide();
	$('#child-page .form-page').first().show();

});


/*****************************************************************************************************
 * Form submit (AJAX)
 *****************************************************************************************************/

// document ready
$(function() {

	$('#child-page form#child-form-general').submit(function(event) {
//		if (!$(this).find('input[name="ChildPhoto"]').val()) {
//			$('#child-page #child-page-general .ajax-response').html('<div class="ajax-message sessions-error">Child Photo is required.</div>');
//		} else {
			var form = $(this);
			$.ajax({
				type: 'POST',
				url: 'ajax-child.php',
				data: form.serialize(),
				dataType: 'json',
				cache: false
			}).done(function(data) {
				if (data.success === true) {
					// Success
					$('#child-page input[name="childID"]').val(data.childID);
					if (data.imageFile) $('#child-page #child-form-general-child-photo-imageFile').html('<img src="' + data.imageFile + '" style="width: 200px;" />');
					if (data.guardianOneID) $('#child-page input[name="guardianOneID"]').val(data.guardianOneID);
					if (data.guardianTwoID) $('#child-page input[name="guardianTwoID"]').val(data.guardianTwoID);
					showFormPage("child-page-contacts");
				} else {
					// Validation failed
					$('#child-page #child-page-general .ajax-response').html('<div class="ajax-message">' + JSON.stringify(data) + '</div>');
				}
			}).fail(function(data) {
				// Error
				$('#child-page #child-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			});
//		}
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	$('#child-page form#child-form-contacts').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-contacts.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			if (data.emergencyID1) $('#child-page input[name="emergencyID1"]').val(data.emergencyID1);
			if (data.emergencyID2) $('#child-page input[name="emergencyID2"]').val(data.emergencyID2);
			if (data.emergencyID3) $('#child-page input[name="emergencyID3"]').val(data.emergencyID3);
			if (data.authorizedID1) $('#child-page input[name="authorizedID1"]').val(data.authorizedID1);
			if (data.authorizedID2) $('#child-page input[name="authorizedID2"]').val(data.authorizedID2);
			if (data.authorizedID3) $('#child-page input[name="authorizedID3"]').val(data.authorizedID3);
			if (data.restrictedID1) $('#child-page input[name="restrictedID1"]').val(data.restrictedID1);
			if (data.restrictedID2) $('#child-page input[name="restrictedID2"]').val(data.restrictedID2);
			if (data.restrictedID3) $('#child-page input[name="restrictedID3"]').val(data.restrictedID3);
			showFormPage("child-page-medical");
		}).fail(function(data) {
			// Error
			$('#child-page #child-page-contacts .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	$('#child-page form#child-form-medical').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-medical.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			// Take us out of here, we're done
			location.href = '/';
		}).fail(function(data) {
			// Error
			$('#child-page #child-page-medical .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

});

/*****************************************************************************************************
 * Contact preload
 *****************************************************************************************************/

// document ready
$(function() {

	// Preload emergency contacts
	var showEmergencyContacts = 0;
	if ($('#child-page input[name="emergencyID1"]').val()) {
			showEmergencyContacts++;
	}
	if ($('#child-page input[name="emergencyID2"]').val()) {
			showEmergencyContacts++;
	}
	if ($('#child-page input[name="emergencyID3"]').val()) {
			showEmergencyContacts++;
	}
	if (showEmergencyContacts == 3) {
		$('#child-page button#add-emergency-contact').hide();
	}
	if (showEmergencyContacts < 2) showEmergencyContacts = 2;
	$('#child-page button#add-emergency-contact').click(function(){
		if (showEmergencyContacts < 3) {
			$(this).text("Add Another Emergency Contact");
			showEmergencyContacts++;
			$("#child-page #emergency-contact-" + showEmergencyContacts).show();
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
		$("#child-page #authorized-contact-" + showAuthorizedContacts).show();
		if (showAuthorizedContacts < 3) {
			$('#child-page button#add-authorized-contact').text("Add Another Authorized Contact");
		} else {
			$('#child-page button#add-authorized-contact').hide();
		}
	}
	if ($('#child-page input[name="authorizedID1"]').val()) {
		addAuthorizedContact();
	}
	if ($('#child-page input[name="authorizedID2"]').val()) {
		addAuthorizedContact();
	}
	if ($('#child-page input[name="authorizedID3"]').val()) {
		addAuthorizedContact();
	}
	$('#child-page button#add-authorized-contact').click(function(){
		if (showAuthorizedContacts < 3) {
			addAuthorizedContact();
		}
		return false; // Prevent the form from submitting via the browser.
	});

	// Preload emergency contacts
	var showRestrictedContacts = 0;
	function addRestrictedContact() {
		showRestrictedContacts++;
		$("#child-page #restricted-contact-" + showRestrictedContacts).show();
		if (showRestrictedContacts < 3) {
			$('#child-page button#add-restricted-contact').text("Add Another Restricted Contact");
		} else {
			$('#child-page button#add-restricted-contact').hide();
		}
	}
	if ($('#child-page input[name="restrictedID1"]').val()) {
		addRestrictedContact();
	}
	if ($('#child-page input[name="restrictedID2"]').val()) {
		addRestrictedContact();
	}
	if ($('#child-page input[name="restrictedID3"]').val()) {
		addRestrictedContact();
	}
	$('#child-page button#add-restricted-contact').click(function(){
		if (showRestrictedContacts < 3) {
			addRestrictedContact();
		}
		return false; // Prevent the form from submitting via the browser.
	});

});


/*****************************************************************************************************
 * Input specific events
 *****************************************************************************************************/

// document ready
$(function() {

	// force select from calendar
	$('#child-page input[name="childBirthDate"]').keydown(function() {
		$(this).blur();
	});

	$("#child-page input[name='MedicalConditions[]']").eq(2).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-allergy-life-threatening',
				'title' : 'Allergy - life threatening',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-allergy-life-threatening textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(2).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(4).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-allergy-food',
				'title' : 'Allergy - food',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-allergy-food textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(4).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(6).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-allergy-environmental',
				'title' : 'Allergy - environmental',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-allergy-environmental textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(6).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(8).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-diabetes',
				'title' : 'Diabetes',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-diabetes textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(8).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(9).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-uti',
				'title' : 'UTI',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-uti textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(9).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(12).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-add-adhd',
				'title' : 'ADD/ADHD',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-add-adhd textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(12).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(13).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-epilepsy',
				'title' : 'Epilepsy',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-epilepsy textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(13).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(14).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-heart-condition',
				'title' : 'Heart condition',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-heart-condition textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(14).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(15).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-autism-spectrum',
				'title' : 'Autism spectrum',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-autism-spectrum textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(15).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalConditions[]']").eq(16).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-conditions-medications',
				'title' : 'Medications',
				'beforeClose' : function() {
					if ($("#child-page #medical-conditions-medications textarea").val() == '') {
						$("#child-page input[name='MedicalConditions[]']").eq(16).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(0).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-hearing',
				'title' : 'Hearing',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-hearing textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(0).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(1).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-speech',
				'title' : 'Speech',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-speech textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(1).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(2).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-eating',
				'title' : 'Eating',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-eating textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(2).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(3).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-vision',
				'title' : 'Vision',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-vision textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(3).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(4).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-bowels',
				'title' : 'Bowels',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-bowels textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(4).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(5).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-urinary-accidents',
				'title' : 'Urinary accidents',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-urinary-accidents textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(5).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(6).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-social',
				'title' : 'Making friends',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-social textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(6).click();
					}
				}
			});
		}
	});

	$("#child-page input[name='MedicalDifficulties[]']").eq(7).click(function() {
		if (this.checked) {
			$.fancybox({
				'href' : '#medical-difficulties-other',
				'title' : 'Other',
				'beforeClose' : function() {
					if ($("#child-page #medical-difficulties-other textarea").val() == '') {
						$("#child-page input[name='MedicalDifficulties[]']").eq(7).click();
					}
				}
			});
		}
	});

});