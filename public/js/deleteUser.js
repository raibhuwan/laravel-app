jQuery(document).ready(function () {
    jQuery('#btnDeleteUser').click(function () {
        var ajax_url = jQuery('meta[name=site_url ]').attr("content");
        var delType = jQuery('input[name="delete-option"]:checked').val();
        jQuery.ajax({
            method: 'post',
            url: ajax_url + '/user/' + jQuery('#hidden_user_id').val(),
            data: {
                '_method': 'DELETE',
                '_token': jQuery('#modal_token').val(),
                'delType': delType
            },
            success: function (data) {
                jsonData = JSON.parse(data);
                if (jsonData.status) {
                    $('#modal1').modal('toggle');
                    reloadUser.ajax.reload();

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