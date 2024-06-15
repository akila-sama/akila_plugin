/**
 * Handles AJAX request to save custom data.
 * @since 1.0.0
 * @param {object} $ - jQuery object.
 */
jQuery(document).ready(function ($) {
	/**
	 * Handles click event on submit button.
	 *
	 * @param {object} e - Event object.
	 */
	$("#submit_custom_data").click(function (e) {
		e.preventDefault();
		var customData = $("#custom_data").val();
		$.ajax({
			type: "POST",
			url: ak_my_plugin.ajax_url,
			data: {
				action: "save_custom_data_ajax",
				custom_data: customData,
				security: ak_my_plugin.nonce, // Pass nonce
			},
			/**
			 * Handles success response from AJAX request.
			 *
			 * @param {string} response - Response from server.
			 */
			success: function (response) {
				if (response === "success") {
					$("#message").html("Data saved successfully!");
				} else {
					$("#message").html("Error saving data!");
				}
			},
			/**
			 * Handles error response from AJAX request.
			 *
			 * @param {object} xhr - XMLHttpRequest object.
			 * @param {string} status - Status of the request.
			 * @param {string} error - Error message.
			 */
			error: function (xhr, status, error) {
				console.error(error);
			},
		});
	});
});

/**
 * Handles AJAX requests to retrieve and delete portfolio posts.
 * @since 1.0.0
 * @param {object} $ - jQuery object.
 */
jQuery(document).ready(function ($) {
	/**
	 * Retrieves portfolio posts using AJAX.
	 */
	function getPortfolioPosts () {
		$.ajax({
			url: ak_my_plugin.rest_url + 'akila-portfolio/v1/portfolio-posts', // Use the global variable ajaxurl for AJAX requests
			method: "GET",
			/**
			 * Handles success response from AJAX request to retrieve portfolio posts.
			 *
			 * @param {string} response - Response from server.
			 */
			success: function (response) {
				if (response) {
					const $tmpl = wp.template('portfolio-post');
					$("#portfolio-posts-container").html(
						$tmpl(response)
					);
				} else {
					$("#portfolio-posts-container").html(
						"<p>No portfolio posts found.</p>"
					);
				}
			},
			/**
			 * Handles error response from AJAX request to retrieve portfolio posts.
			 *
			 * @param {object} xhr - XMLHttpRequest object.
			 * @param {string} status - Status of the request.
			 * @param {string} error - Error message.
			 */
			error: function (xhr, status, error) {
				console.error(error);
			},
		});
	}

	// Call function to retrieve portfolio posts when the page loads
	if ('plugin-details_page_ak_custom-submenu-slug' === pagenow) {
		getPortfolioPosts();
	}

	/**
	 * Handles click event on delete portfolio post button.
	 */
	$(document).off("click", ".delete-portfolio-post").on("click", ".delete-portfolio-post", function () {
		var postId = $(this).data("post-id");
		if (confirm("Are you sure you want to delete this portfolio post?")) {
			$.ajax({
				url: ak_my_plugin.ajax_url,
				method: "POST",
				data: {
					action: "delete_portfolio_post",
					post_id: postId,
					nonce: ak_my_plugin.nonce, // Pass nonce here
				},
				/**
				 * Handles success response from AJAX request to delete portfolio post.
				 *
				 * @param {string} response - Response from server.
				 */
				success: function (response) {
					if (response === "success") {
						$("#portfolio-posts-message")
							.text("Portfolio post deleted successfully.")
							.show();
						getPortfolioPosts(); // Refresh the list of portfolio posts after deletion
					} else {
						alert("Error deleting portfolio post.");
					}
				},
				/**
				 * Handles error response from AJAX request to delete portfolio post.
				 *
				 * @param {object} xhr - XMLHttpRequest object.
				 * @param {string} status - Status of the request.
				 * @param {string} error - Error message.
				 */
				error: function (xhr, status, error) {
					console.error(error);
					alert("Error deleting portfolio post.");
				},
			});
		}
	});
});

/**
 * Handles form submission for portfolio submissions.
 * Validates form inputs and sends data via AJAX.
 *  @since 1.0.0
 * @param {jQuery} $ - jQuery instance.
 */
jQuery(document).ready(function ($) {
	$("#submit_btn").on("click", function () {
		var name = $("#name").val();
		var company_name = $("#company_name").val();
		var company_url = $("#company_url").val();
		var email = $("#email").val();
		var phone = $("#phone").val();
		var address = $("#address").val();

		if (
			name.trim() === "" ||
			email.trim() === "" ||
			phone.trim() === "" ||
			address.trim() === ""
		) {
			$("#response_msg").html(
				'<div class="error">Please fill out all required fields.</div>'
			);
			return;
		}

		var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!emailPattern.test(email)) {
			$("#response_msg").html(
				'<div class="error">Please enter a valid email address.</div>'
			);
			return;
		}

		var phonePattern = /^\d{10}$/;
		if (!phonePattern.test(phone)) {
			$("#response_msg").html(
				'<div class="error">Please enter a valid 10-digit phone number.</div>'
			);
			return;
		}

		$("#response_msg").html('<div class="info">Please wait...</div>');

		var formData = $("#portfolio_submission_form").serializeArray();
		formData.push({ name: "company_url", value: company_url });
		$.ajax({
			type: "POST",
			url: ak_my_plugin.ajax_url,
			data: formData,
			success: function (response) {
				$("#response_msg").html(response);
				$("#portfolio_submission_form")[ 0 ].reset();

				setTimeout(function () {
					$("#response_msg").fadeOut("slow", function () {
						$(this).html("").show();
					});
				}, 5000);
			},
		});
	});
});


/**
 * Handles JavaScript functionalities for Akila Portfolio settings page.
 *
 * @since 1.0.0
 */

jQuery(document).ready(function ($) {
	/**
	 * Handles form submission for saving settings via AJAX.
	 *
	 * @since 1.0.0
	 *
	 * @param {Event} e - The submit event.
	 */
	$("#akila-settings-form").on("submit", function (e) {
		e.preventDefault();

		var data = {
			action: "save_settings",
			security: ak_my_plugin.nonce,
			email_notifications: $("#akila_email_notifications").is(":checked") ? 1 : 0,
			notification_frequency: $("#akila_notification_frequency").val()
		};

		$.ajax({
			type: "POST",
			url: ak_my_plugin.ajax_url,
			data: data,
			success: function (response) {
				if (response.success) {
					$("#akila-message").html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
				} else {
					$("#akila-message").html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
				}
			},
			error: function (xhr, status, error) {
				console.error(xhr.responseText);
			}
		});
	});
});
