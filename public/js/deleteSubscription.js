jQuery(document).ready(function () {
    jQuery('#btnDeleteSubscription').click(function () {
        var ajax_url = jQuery('meta[name=site_url ]').attr("content");

        jQuery.ajax({
            method: 'post',
            url: ajax_url + "/user/subscription/" + jQuery('#hidden_subscription_id').val(),
            data: {
                '_method': 'DELETE',
                '_token': jQuery('#modal_token').val()
            },
            success: function (data) {
                jsonData = JSON.parse(data);
                if (jsonData.status) {
                    $('#modal1').modal('toggle');
                    reloadSubscription.ajax.reload();
                    var notify = $.notify(jsonData.message, {
                        type: 'success',
                        allow_dismiss: true,
                        offset: {
                            x: 50,
                            y: 80
                        },
                        timer: 5000
                    });
                }
            }
        });
    });
});
