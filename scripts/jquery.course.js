// JavaScript Document


// document ready
$(document).ready(function() {
	
/*****************************************************************************************************
 * Setup
 *****************************************************************************************************/

	// Fancybox setup
	$("#course-page .fancybox").fancybox({
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

	// Page setup
	$('#course-page .form-page').hide();
	$('#course-page .form-page').first().show();

	// Convert currencies
	$('.currency-cad').autoNumeric('init', {
		// options
		aSign: '$ '
	});

	// Load program activities for selected program
	function loadPrograms() {
		var locationID = $('select[name="CourseLocationID"]').val();
		var programID = $('select[name="CourseProgramID"]').val();
		var courseID = $('input[name="courseID"]').val();
	//	alert('courseID: ' + courseID + 'locationID: ' + locationID);
		var listPrograms = $.post("course-program.php", { "locationID" : locationID, "programID" : programID, "courseID" : courseID });
	//	var listPrograms = $.post("course-program.php", { "locationID" : locationID, "programID" : programID });
		listPrograms.done(function(data) {
	//		$("#load-activities").empty().load("course-activity.php?programID=" + programID + "&courseID=" + courseID + " #course-activity-list");
			var content = $(data);
			$('select[name="CourseProgramID"]').empty().append(content);
	//		$('select[name="CourseProgramID"]').val('');
		});
		listPrograms.error(function (jqXHR, textStatus, errorThrown) {
			if (jqXHR.status == 500) {
				alert('Internal error: ' + jqXHR.responseText);
			} else {
				alert('Unexpected error. '+errorThrown);
			}
		});
	}
	
	// Load program activities for selected program
	function loadProgramActivities() {
		$("#load-activities").empty().html('<p>Please select program.</p>');
		var programID = $('select[name="CourseProgramID"]').val();
		if (programID != '') {
			var courseID = $('input[name="courseID"]').val();
//		alert('courseID: ' + courseID + 'programID: ' + programID);
			var listActivities = $.post("course-activity.php", { "programID": programID, "courseID": courseID });
			listActivities.done(function(data) {
		//		$("#load-activities").empty().load("course-activity.php?programID=" + programID + "&courseID=" + courseID + " #course-activity-list");
				var content = $(data).find("ul#course-activity-list");
//				if (!$.isEmptyObject(content)) {
					$("#load-activities").hide();
					$("#load-activities").empty().append(content);
					// Show activity detail when activity is selected
					$('input[name="CourseActivities[]"]').each(function() {
						if (this.checked) {
							$('div#course-activity-detail-' + $(this).val()).toggle();
							$('.currency-cad').autoNumeric('init', { aSign: '$ ' });
						}
					});
					$("#load-activities").slideDown(200);
//				}
			});
			listActivities.error(function (jqXHR, textStatus, errorThrown) {
				if (jqXHR.status == 500) {
					alert('Internal error: ' + jqXHR.responseText);
				} else {
					alert('Unexpected error. '+errorThrown);
				}
			});
		}
	}

	if ($('select[name="CourseLocationID"]').val() != '') {
		loadPrograms();
		$('select[name="CourseProgramID"]').removeAttr('disabled');
	} else {
		$('select[name="CourseProgramID"]').attr('disabled', true);
	}
	loadProgramActivities();

	$('.form-page').first().show();

/*****************************************************************************************************
 * Form submit (AJAX)
 *****************************************************************************************************/

	$('#course-page form#course-form').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-course.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			if (data.success === true) {
				// Success
				// Take us out of here, we're done
				location.href = 'courses.php';
			} else {
				// Validation failed
				$('#course-page #course-page-general .ajax-response').html('<div class="ajax-message">' + JSON.stringify(data) + '</div>');
			}
		}).fail(function(data) {
			// Error
			$('#course-page #course-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		}).error(function (jqXHR, textStatus, errorThrown) {
			if (jqXHR.status == 500) {
				alert('Internal error: ' + jqXHR.responseText);
			} else {
				alert('Unexpected error. '+errorThrown);
			}
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});


/*****************************************************************************************************
 * Input specific events
 *****************************************************************************************************/
/*
	$(document).on('change','input[name="CourseName"]', function() {
		var idStr = '';
		if ($('input[name="courseID"]').val() != '') {
			idStr = '-' + $('input[name="courseID"]').val();
		}
		$('input[name="CourseSlug"]').val(slug($('input[name="CourseName"]').val()) + idStr);
	});
*/
	// Load programs when location is changed
	$(document).on('change', 'select[name="CourseLocationID"]', function() {
		$('select[name="CourseProgramID"]').val('');
		$("#load-activities").empty().html('<p>Please select program.</p>');
		if ($('select[name="CourseLocationID"]').val() != '') {
			loadPrograms();
			loadProgramActivities();
			$('select[name="CourseProgramID"]').removeAttr('disabled');
		} else {
			$('select[name="CourseProgramID"]').attr('disabled', true);
		}
	});

	// Load program activities when program is changed
	$(document).on('change', 'select[name="CourseProgramID"]', function() {
		loadProgramActivities();
	});

	// Show activity detail when activity is clicked
	$(document).on('change', 'input[name="CourseActivities[]"]', function() {
		$('div#course-activity-detail-' + $(this).val()).toggle();
		$('.currency-cad').autoNumeric('init', { aSign: '$ ' });
	});

	$(document).on('change','.currency-cad', function() {
		// Convert currencies
		$(this).autoNumeric('update', { });
	});

	// Availability	
	$('input[name="CourseMaxAvailable"], input[name="CourseReserveAvailable"]').change(function() {

		var maxAvail = $('input[name="CourseMaxAvailable"]').val();
		maxAvail = maxAvail.match(/\d+$/)[0];
		$('input[name="CourseMaxAvailable"]').val(maxAvail);

		var resAvail = $('input[name="CourseReserveAvailable"]').val();
		resAvail = resAvail.match(/\d+$/)[0];
		$('input[name="CourseReserveAvailable"]').val(resAvail);

		var curReg = $('input[name="CourseCurrentRegistrations"]').val();

		var curAvail = parseInt(maxAvail, 10) - parseInt(resAvail, 10) - parseInt(curReg, 10);

		$('input[name="CourseCurrentAvailable"]').val(curAvail);
	});

});