jQuery(document).ready(function($) {
    $('#submit_custom_data').click(function(e) {
        e.preventDefault();

        var customData = $('#custom_data').val();

        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajaxurl,
            data: {
                action: 'save_custom_data_ajax',
                custom_data: customData,
                security: my_ajax_object.security // Pass nonce
            },
            success: function(response) {
                if (response === 'success') {
                    $('#message').html('Data saved successfully!');
                } else {
                    $('#message').html('Error saving data!');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
});


//rest api