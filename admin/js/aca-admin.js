jQuery(function($) {
    'use strict';

    // Test API Connection
    $('#aca-ai-content-agent-test-connection').on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $status = $('#aca-ai-content-agent-test-status');

        $button.prop('disabled', true);
        $status.text('Testing...').css('color', 'orange');

        $.ajax({
            url: aca_ai_content_agent_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_ai_content_agent_test_connection',
                nonce: aca_ai_content_agent_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data).css('color', 'green');
                } else {
                    $status.text('Error: ' + response.data).css('color', 'red');
                }
            },
            error: function() {
                $status.text('Error: An unknown AJAX error occurred.').css('color', 'red');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });

    // Buraya dashboard'daki "Generate Ideas", "Write Draft" gibi diğer butonlar için
    // AJAX kodları da eklenebilir. Şimdilik sadece test butonu eklendi.

});