function showFormPage(showPage) {
	$('.form-page').hide();
	$('body,html').animate({
			scrollTop : 0
	}, 'fast', function() {
		$('#' + showPage).fadeIn('fast');
	});
	$('#' + showPage).find($('input[type="submit"]')).removeAttr("disabled");
}

function convertTFYN(value) {
	if (value == "yes") return true;
	else if (value == "no") return false;
	else if (value == true) return "yes";
	else if (value == false) return "no";
}

/**
 * Function generates a random string for use in unique IDs, etc
 *
 * @param <int> n - The length of the string
 */
function randString(n) {
	if (!n) {
		n = 5;
	}
	var text = '';
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
	for (var i=0; i < n; i++)	{
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return text;
}

// document ready
$(function() {

	// datetimepicker
	$('.datetimepicker').datetimepicker({
		format: 'm/d/Y h:i a',
		step: '30',
		allowBlank: true,
		formatTime: 'g:i a'
	});

	// datepicker
	$('.datepicker').datetimepicker({
		format: 'm/d/Y',
		allowBlank: true,
		timepicker: false
	});

	// add screen resolution to footer for dev testing
	$('#dev-text').text(" | " + $(window).width() + " X " + $(window).height() + " Screen");

	// add a location reference to the body tag
	var newTag = window.location.href;
	// strip path from location
	newTag = newTag.substring(newTag.lastIndexOf('/')+1);
	// strip file extension
	newTag = newTag.substring(0,newTag.lastIndexOf('.'));
	// add the location tag
//	$(document.body).addClass(newClass);
	$(document.body).attr('id',newTag + '-page');

	$('.scroll-top').click(function() {
		$('html, body').animate({
			//scrollTop: $("#elementtoScrollToID").offset().top
			scrollTop: 0
		}, 1000);
	});
	$('a.scroll-to').click(function(e) {
		e.preventDefault();
		var scrollPX = $($(this).attr("href")).offset().top - 40;
		$('html, body').animate({
			scrollTop: scrollPX
		}, 1000);
	});
});


/**
 * Function generates a slug string for use in URLs
 */
var slug = function(str) {
	str = str.replace(/^\s+|\s+$/g, ''); // trim
	str = str.toLowerCase();
	
	// remove accents, swap ñ for n, etc
	var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
	var to   = "aaaaaeeeeeiiiiooooouuuunc------";
	for (var i=0, l=from.length ; i<l ; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}
	
	str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
	.replace(/\s+/g, '-') // collapse whitespace and replace by -
	.replace(/-+/g, '-'); // collapse dashes
	
	return str;
};