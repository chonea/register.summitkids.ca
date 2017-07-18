// JavaScript Document


/*****************************************************************************************************
 * Setup
 *****************************************************************************************************/

// document ready
$(function() {

	// Fancybox setup
	$("#location-page .fancybox").fancybox({
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
	$('#location-page .form-page').hide();
	$('#location-page .form-page').first().show();

});


/*****************************************************************************************************
 * Form submit (AJAX)
 *****************************************************************************************************/

// document ready
$(function() {

	$('#location-page form#location-form').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-location.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			if (data.success === true) {
				// Success
				// Take us out of here, we're done
				location.href = 'locations.php';
			} else {
				// Validation failed
				$('#location-page #location-page-general .ajax-response').html('<div class="ajax-message">' + JSON.stringify(data) + '</div>');
			}
		}).fail(function(data) {
			// Error
			$('#location-page #location-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

});

/*****************************************************************************************************
 * Input specific events
 *****************************************************************************************************/

// document ready
$(function() {

});