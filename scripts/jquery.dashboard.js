// JavaScript Document


/*****************************************************************************************************
 * Setup
 *****************************************************************************************************/

// document ready
$(function() {

	// Fancybox setup
	$("#index-page .fancybox").fancybox({
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

});


/*****************************************************************************************************
 * Form submit (AJAX)
 *****************************************************************************************************/

// document ready
$(function() {

	$('form[name="dashboard-applications"] a.delete').click(function(event) {
		$('input[name="registerID"]').val($(this).data('deleteRegistrationId'));
		var deleteRow =	$(this).parent('.application-actions').parent('.application-row');
		$(deleteRow).find('.application-actions').hide();
		event.preventDefault(); // Prevent the link from firing via the browser.
//		$('form[name="dashboard-applications"] input[name="registerID"]').val($(this).data('deleteRegistrationId'));
		var form = $('form[name="dashboard-applications"]');
		$.ajax({
			type: 'POST',
			url: 'ajax-register-delete.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			$(deleteRow).fadeOut(500);
			$(deleteRow).remove();
			location.reload();
			//$('form[name="dashboard-applications"] .ajax-response').html('<div class="ajax-success">Application deleted.</div>');
			//$('form[name="dashboard-applications"] .ajax-response .ajax-success').delay(2000).slideUp(200);
		}).fail(function(data) {
			// Error
			$('form[name="dashboard-applications"] .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			$(deleteRow).find('.application-actions').show();
		});
	});

	$('form[name="dashboard-applications"] a.accept').click(function(event) {
		alert('accept');
		$('input[name="registerID"]').val($(this).data('acceptRegistrationId'));
		var acceptRow =	$(this).parent('.application-actions').parent('.application-row');
		$(acceptRow).find('.application-actions').hide();
		event.preventDefault(); // Prevent the link from firing via the browser.
//		$('form[name="dashboard-applications"] input[name="registerID"]').val($(this).data('deleteRegistrationId'));
		var form = $('form[name="dashboard-applications"]');
		$.ajax({
			type: 'POST',
			url: 'ajax-register-accept.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			$(acceptRow + ' .status-accepted').removeClass('status-disabled');
		}).fail(function(data) {
			// Error
			$('form[name="dashboard-applications"] .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			$(acceptRow).find('.application-actions').show();
		});
	});

	$('form[name="dashboard-guardian-profiles"] a.delete').click(function(event) {
		$('input[name="guardianID"]').val($(this).data('deleteGuardianId'));
		var deleteRow =	$(this).parent('.application-actions').parent('.application-row');
		$(deleteRow).find('.application-actions').hide();
		event.preventDefault(); // Prevent the link from firing via the browser.
//		$('form[name="dashboard-applications"] input[name="registerID"]').val($(this).data('deleteRegistrationId'));
		var form = $('form[name="dashboard-guardian-profiles"]');
		$.ajax({
			type: 'POST',
			url: 'ajax-guardian-delete.php',
			data: form.serialize(),
			dataType: 'json',
			cache: false
		}).done(function(data) {
			// Success
			$(deleteRow).fadeOut(500);
			$(deleteRow).remove();
			location.reload();
			//$('form[name="dashboard-applications"] .ajax-response').html('<div class="ajax-success">Application deleted.</div>');
			//$('form[name="dashboard-applications"] .ajax-response .ajax-success').delay(2000).slideUp(200);
		}).fail(function(data) {
			// Error
			$('form[name="dashboard-guardian-profiles"] .ajax-response').html('<div class="ajax-error">' + JSON.stringify(data) + '</div>');
			$(deleteRow).find('.application-actions').show();
		});
	});

});

/*****************************************************************************************************
 * Specific events
 *****************************************************************************************************/

// document ready
$(function() {
	
	$('img.child-avatar').click(function() {
		$.fancybox({
			'href' : $(this).attr('src'),
			'title' : $(this).attr('title'),
			'beforeClose' : function() {
			}
		});
	});

});