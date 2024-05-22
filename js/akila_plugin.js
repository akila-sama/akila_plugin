//custom page form
jQuery(document).ready(function($) {
    $("#submit_custom_data").click(function(e) {
        e.preventDefault();

        var customData = $("#custom_data").val();

        $.ajax({
            type: "POST",
            url: my_plugin.ajax_url,
            data: {
                action: "save_custom_data_ajax",
                custom_data: customData,
                security: my_ajax_object.security, // Pass nonce
            },
            success: function(response) {
                if (response === "success") {
                    $("#message").html("Data saved successfully!");
                } else {
                    $("#message").html("Error saving data!");
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    });
});

//rest api
jQuery(document).ready(function($) {
    // Function to retrieve portfolio posts using AJAX
    function getPortfolioPosts() {
        $.ajax({
            url: my_plugin.ajax_url, // Use the global variable ajaxurl for AJAX requests
            method: "POST",
            data: {
                action: "get_portfolio_posts",
            },
            success: function(response) {
                if (response) {
                    $("#portfolio-posts-container").html(response);
                } else {
                    $("#portfolio-posts-container").html(
                        "<p>No portfolio posts found.</p>"
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    }

    // Call function to retrieve portfolio posts when the page loads
    getPortfolioPosts();

    // Function to handle portfolio post deletion
    $(document).on("click", ".delete-portfolio-post", function() {
        var postId = $(this).data("post-id");
        if (confirm("Are you sure you want to delete this portfolio post?")) {
            $.ajax({
                url: my_plugin.ajax_url,
                method: "POST",
                data: {
                    action: "delete_portfolio_post",
                    post_id: postId,
                    nonce: submenu_ajax_object.nonce, // Pass nonce here
                },
                success: function(response) {
                    if (response === "success") {
                        $("#portfolio-posts-message")
                            .text("Portfolio post deleted successfully.")
                            .show();
                        getPortfolioPosts(); // Refresh the list of portfolio posts after deletion
                    } else {
                        alert("Error deleting portfolio post.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("Error deleting portfolio post.");
                },
            });
        }
    });
});

//form
jQuery(document).ready(function($) {
    $('#submit_btn').on('click', function() {
        var name = $('#name').val();
        var company_name = $('#company_name').val();
        var company_url = $('#company_url').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var address = $('#address').val();

        // Basic form validation
        if (name.trim() === '' || email.trim() === '' || phone.trim() === '' || address.trim() === '') {
            $('#response_msg').html('<div class="error">Please fill out all required fields.</div>');
            return;
        }

        // Validate email format
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            $('#response_msg').html('<div class="error">Please enter a valid email address.</div>');
            return;
        }

        // Validate phone format (assuming US phone number format)
        var phonePattern = /^\d{10}$/;
        if (!phonePattern.test(phone)) {
            $('#response_msg').html('<div class="error">Please enter a valid 10-digit phone number.</div>');
            return;
        }

        var formData = $('#portfolio_submission_form').serializeArray();
        formData.push({ name: 'company_url', value: company_url });
        $.ajax({
            type: 'POST',
            url: my_plugin.ajax_url,
            data: formData,
            success: function(response) {
                $('#response_msg').html(response);
                $('#portfolio_submission_form')[0].reset(); // Reset the form

                // Hide success message after 5 seconds
                setTimeout(function() {
                    $('#response_msg').fadeOut('slow', function() {
                        $(this).html('').show(); // Clear the message and reset fade state
                    });
                }, 5000); // 5000 milliseconds = 5 seconds
            }
        });
    });
});