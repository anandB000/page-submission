(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 */

	$(document).on('submit', '#wpps-post-form', function (e) {
		e.preventDefault();
		var formData = new FormData();
		formData.append("wpps_post_featured_image", $('#wpps_post_featured_image')[0].files[0]);
		formData.append("action", "wpps_do_create_post");
		formData.append("wpps_nonce", _wpps_ajax_var.nonce);
		formData.append("wpps_post_title", $('#wpps_post_title').val());
		formData.append("wpps_post_type", $('#wpps_post_type').val());
		formData.append("wpps_post_content", $('#wpps_post_content').val());
		formData.append("wpps_post_excerpt", $('#wpps_post_excerpt').val());

		$.ajax({
			url: _wpps_ajax_var.ajaxurl,
			type: 'post',
			processData: false,
			contentType: false,
			data: formData,
			success(data) {
				if (data) {
					console.log(data);
					$('.wpps-res').html(data);
					$('#wpps-post-form')[0].reset();
				}
			},
		});
	});

})(jQuery);
