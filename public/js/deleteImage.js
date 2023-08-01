jQuery(document).ready(function () {

    jQuery('button[name="image-delete-button"]').click(function () {
        // t = this;
        // console.log(t);
        var image_number = jQuery(this).parent().parent().find('input[name="image-number"]').val();
        $("#hidden_image_id").val(image_number);
        $(".image-number-modal").text(image_number);
    });

    jQuery('#btnDeleteImage').click(function () {

        var ajax_url = jQuery('meta[name=site_url ]').attr("content");
        var app_url = jQuery('meta[name=app_url ]').attr("content");
        var user_id = jQuery('input[name=user_id]').val();

        jQuery.ajax({
            method: 'post',
            url: ajax_url + "/user/" + user_id + "/delete/image",
            data: {
                '_method': 'DELETE',
                '_token': jQuery('#modal_token').val(),
                'number': jQuery('#hidden_image_id').val(),
            },
            success: function (data) {
                jsonData = JSON.parse(data);
                if (jsonData.status) {
                    $('#modal1').modal('toggle');

                    $('a.image-number_' + jsonData.image_number).attr('href', app_url + '/images/profile/portrait_placeholder.png');
                    $('img.image-number_' + jsonData.image_number).attr('src', app_url + '/images/profile/portrait_placeholder.png');

                    $('.btn-image-delete_' + jsonData.image_number).addClass("disabled").attr("data-target", "");

                    var notify = $.notify(jsonData.message, {
                        type: 'success',
                        allow_dismiss: true,
                        offset: {
                            x: 50,
                            y: 80
                        },
                        timer: 5000
                    });
                } else {
                    $('#modal1').modal('toggle');
                    var notify = $.notify(jsonData.message, {
                        type: 'danger',
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