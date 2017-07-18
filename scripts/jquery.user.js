// JavaScript Document


/*****************************************************************************************************
 * Setup
 *****************************************************************************************************/

// document ready
$(function() {

	// Fancybox setup
	$("#user-page .fancybox").fancybox({
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
//	$('#user-page-general .form-page').hide();
	$('.form-page').show();

//	$('input[name="UserEmail"]').attr('readonly','readonly');

/*****************************************************************************************************
 * Form submit (AJAX)
 *****************************************************************************************************/

	$('form#user-form').submit(function(event) {
		var form = $(this);
		$.ajax({
			type: 'POST',
			url: 'ajax-user.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			if (data.success === true) {
				// Success
				// Take us out of here, we're done
				location.href = '/users.php';
			} else {
				// Validation failed
				$('#user-page #user-page-general .ajax-response').html('<div class="ajax-error">' + data.message + '</div>');
			}
		}).fail(function(data) {
			// Error
			$('#user-page #user-page-general .ajax-response').html('<div class="ajax-error">' + data.message + '</div>');
		});
		event.preventDefault(); // Prevent the form from submitting via the browser.
	});

	$('form#user-form input[name="UserPasswordGenerate"]').click(function() {
		var generatedPassword = randString(8);
		$('form#user-form input[name="UserPassword"]').val(generatedPassword);
		$('form#user-form input[name="UserPasswordConfirm"]').val(generatedPassword);
	});


/*****************************************************************************************************
 * Input specific events
 *****************************************************************************************************/

	$('form#user-form input, form#user-form select').change(function(){
		$('form#user-form .btn-primary').removeAttr('disabled');
	});

});