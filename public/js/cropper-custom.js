// transform cropper dataURI output to a Blob which Dropzone accepts
var dataURItoBlob = function (dataURI) {
    var byteString = atob(dataURI.split(',')[1]);
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ab], {type: 'image/jpeg'});
};

Dropzone.autoDiscover = false;
var c = 0;
var app_url = $('meta[name=app_url]').attr("content");
var tempFile;

for (var i = 1; i <= 6; i++) {
    if (document.getElementById("my-dropzone-container-" + i)) {
        var myDropzone = new Dropzone("#my-dropzone-container-" + i, {
            addRemoveLinks: true,
            parallelUploads: 10,
            uploadMultiple: false,
            maxFiles: 1,
            init: function () {
                this.on('success', function (file) {
                    tempFile = file;
                    var $button = $('<a href="#" class="js-open-cropper-modal" data-file-name="' + file.name + '">Crop & Upload</a>');
                    $(file.previewElement).append($button);
                });
                this.on()
            },
        });
    }
}


$("[id^=my-dropzone-container]").on('click', '.js-open-cropper-modal', function (e) {
    // t = this;
    //  console.log(this);
    var image_number = $(this).parent().parent().parent().parent().find('input[name="image-number"]').val();

    e.preventDefault();
    var fileName = $(this).data('file-name');

    var modalTemplate =
        '<div class="modal fade" id="m_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">' +
        '<div class="modal-dialog modal-lg" role="document">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        ' <h5 class="modal-title" id="exampleModalLongTitle">Upload Image</h5>' +
        '<button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
        '<span aria-hidden="true">&times;</span>\n' +
        '</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="image-container">' +
        '<img id="img-' + ++c + '" class="dropzone-image" src="' + app_url + '/storage/uploads/tmpImages/' + fileName + '">' +
        '</div>' +
        '</div>' +
        '<div class="modal-footer">' +

        '<a href="#" class=" btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill drag" title="Drag Image">\n' +
        '<i class="fa fa-arrows-alt"></i>\n' +
        '</a>' +

        '<a href="#" class=" btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill rotate-left" title="Rotate left">\n' +
        '<i class="fa fa-redo"></i>\n' +
        '</a>' +

        '<a href="#" class=" btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill rotate-right" title="Rotate right">\n' +
        '<i class="fa fa-undo"></i>\n' +
        '</a>' +

        '<a href="#" class=" btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill scale-x " data-value="-1" title="Scale X">\n' +
        '<i class="fa       fa-arrows-alt-h"></i>\n' +
        '</a>' +

        '<a href="#" class=" btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill scale-y" data-value="-1" title="Scale Y">\n' +
        '<i class="fa       fa-arrows-alt-v"></i>\n' +
        '</a>' +

        '<a href="#" class=" btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill reset" title="Reset">\n' +
        '<i class="flaticon-refresh"></i>\n' +
        '</a>' +

        '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
        '<button type="button" class="btn btn-primary crop-upload">Crop & upload</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    var $cropperModal = $(modalTemplate);

    $cropperModal.modal('show').on("shown.bs.modal", function () {
        var cropper = new Cropper(document.getElementById('img-' + c), {
            aspectRatio: 1 / 1,
            autoCropArea: 1,
            movable: true,
            cropBoxResizable: true,
            rotatable: true
        });
        var $this = $(this);
        $this
            .on('click', '.crop-upload', function () {
                // Upload cropped image to server if the browser supports `HTMLCanvasElement.toBlob`
                cropper.getCroppedCanvas().toBlob((blob) => {
                    const formData = new FormData();

                    formData.append('croppedImage', blob);


                    var ajax_url = $('meta[name=site_url ]').attr("content");

                    var token = $('meta[name=csrf-token]').attr("content");
                    var user_id = $('input[name=user_id]').val();

                    formData.append('_token', token);
                    formData.append('number', image_number);
                    formData.append('tmpImageName', fileName);
                    // Use `$.ajax` method
                    $.ajax({
                        method: "POST",
                        url: ajax_url + "/user/" + user_id + "/edit/image",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            jsonData = JSON.parse(data);
                            if (jsonData.status) {
                                $('a.image-number_' + image_number).attr('href', jsonData.image_url);
                                $('img.image-number_' + image_number).attr('src', jsonData.image_url);
                                $('.btn-image-delete_' + image_number).removeClass("disabled").attr("data-target", "#modal1");
                                jQuery('.row').find('input[name="image-number"][value="' + image_number + '"]').siblings().find('.dz-remove')[0].click();

                                var notify = $.notify(jsonData.message, {
                                        type: 'success',
                                        allow_dismiss: true,
                                        offset: {
                                            x: 50,
                                            y: 80
                                        },
                                        timer: 5000
                                    }
                                );

                            } else {
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

                $this.modal('hide');
            })
            .on('click', '.drag', function () {
                cropper.setDragMode('move');
            })
            .on('click', '.rotate-right', function () {
                cropper.rotate(90);
            })
            .on('click', '.rotate-left', function () {
                cropper.rotate(-90);
            })
            .on('click', '.reset', function () {
                cropper.reset();
            })
            .on('click', '.scale-x', function () {
                var $this = $(this);
                cropper.scaleX($this.data('value'));
                $this.data('value', -$this.data('value'));
            })
            .on('click', '.scale-y', function () {
                var $this = $(this);
                cropper.scaleY($this.data('value'));
                $this.data('value', -$this.data('value'));
            });
    });
});