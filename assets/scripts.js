jQuery(document).ready(function ($) {
	$('#edd-tracking-info-notify-customer').click( function(e) {
		e.preventDefault();
		$(this).next('.spinner').css('visibility', 'visible');
		$(this).attr('disabled', 'disabled');

		var payment_id      = $(this).data('payment');
		var nonce           = $('#edd-ti-send-tracking').val();

		$('.edd-tracking-info-email-message').html('');

		var postData = {
			edd_action:   'send-tracking',
			payment_id: payment_id,
			nonce: nonce,
		};

		$.post(ajaxurl, postData, function (response) {
			$('.edd-tracking-info-email-message').html(response.message);
		});

		$(this).next('.spinner').css('visibility', 'hidden');
		$(this).attr('disabled', false);
	});
});
