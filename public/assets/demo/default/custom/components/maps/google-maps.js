//== Class definition

var GoogleMapsDemo = function () {

    //== Private functions
    var demo8 = function () {

        var createMap = function (lat, lng) {
            map.removeMarkers();
            map.addMarker({
                lat: lat,
                lng: lng,
                draggable: true,
                dragend: function (event) {
                    lat = event.latLng.lat();
                    lng = event.latLng.lng();
                    jQuery('#userInputLat').val(lat);
                    jQuery('#userInputLong').val(lng);
                },
            });

            jQuery('#userInputLat').val(lat);
            jQuery('#userInputLong').val(lng);

        };

        let lat1 = jQuery('#userInputLat').val();
        let lng1 = jQuery('#userInputLong').val();

        var map = new GMaps({
            div: '#m_gmap_8',
            lat: lat1,
            lng: lng1,
            draggable: true,
            click: function (e) {
                createMap(e.latLng.lat(), e.latLng.lng());
                console.log(e);
            },

        });

        // createMap(27.6794303, 85.3398135);
        createMap(lat1, lng1);

        var handleAction = function () {
            var text = $.trim($('#m_gmap_8_address').val());
            GMaps.geocode({
                address: text,
                callback: function (results, status) {
                    if (status == 'OK') {
                        var latlng = results[0].geometry.location;
                        map.setCenter(latlng.lat(), latlng.lng());
                        map.removeMarkers();
                        createMap(latlng.lat(), latlng.lng());
                        mUtil.scrollTo('m_gmap_8');
                    }
                }
            });
        }

        $('#m_gmap_8_btn').click(function (e) {
            e.preventDefault();
            handleAction();
        });

        $("#m_gmap_8_address").keypress(function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode == '13') {
                e.preventDefault();
                handleAction();
            }
        });
    }

    return {
        // public functions
        init: function () {
            // default charts
            demo8();
        }
    };
}();


jQuery(document).ready(function () {
    GoogleMapsDemo.init();
});

