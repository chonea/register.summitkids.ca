// JavaScript Document


/*****************************************************************************************************
 * Setup
 *****************************************************************************************************/

// document ready
$(function() {

	// Fancybox setup
	$("#guardian-page .fancybox").fancybox({
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
	$('#guardian-page .form-page').hide();
	$('#guardian-page .form-page').first().show();

});


/*****************************************************************************************************
 * Form submit (AJAX)
 *****************************************************************************************************/

// document ready
$(function() {

	$('#guardian-page form#guardian-form').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-guardian.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			if (data.success === true) {
				// Success
				// Take us out of here, we're done
				location.href = '/';
			} else {
				// Validation failed
				$('#guardian-page #guardian-page-general .ajax-response').html('<div class="ajax-message">' + JSON.stringify(data) + '</div>');
			}
		}).fail(function(data) {
			// Error
			$('#guardian-page #guardian-page-general .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
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