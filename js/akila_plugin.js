jQuery(document).ready(function($) {
    $("#submit_custom_data").click(function(e) {
        e.preventDefault();

        var customData = $("#custom_data").val();

        $.ajax({
            type: "POST",
            url: my_ajax_object.ajaxurl,
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
            url: ajaxurl, // Use the global variable ajaxurl for AJAX requests
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
                url: ajaxurl,
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