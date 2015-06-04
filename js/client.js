$(document).ready(function() {
	attr_nb = 0;

	$('#add-attribute').click(function(e) {
		attr_nb++;
		$('#attr-count').attr("value", attr_nb);
		$('#ticket-attributes').append('<input name="attr-name-'+attr_nb+'" type="text"><input name="attr-value-'+attr_nb+'" type="text"><br>');
	});

	/* Settings page */
	function switchPaypalForm() {
		$("form#paypal_cred input").each(function(key, value) {
			if($(value).attr('disabled')) {
				$(value).prop('disabled', false);
			} else {
				$(value).prop('disabled', true);
			}			
		})		
	}

	if($('#use_dev').prop('checked')) {
		switchPaypalForm();
	}

	$("#use_dev").change(function() {
		switchPaypalForm();
	})
});