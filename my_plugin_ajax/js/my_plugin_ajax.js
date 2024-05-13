jQuery(document).ready(function($) {
    $('#submit_custom_data').click(function(e) {
        e.preventDefault();

        var customData = $('#custom_data').val();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'save_custom_data_ajax',
                custom_data: customData
            },
            success: function(response) {
                $('#message').html('Data saved successfully!');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
});