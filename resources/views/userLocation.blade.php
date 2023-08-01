<html>
    <head>
        <style>
            #map {
                height: 100%;
                width: 100%;
            }
        </style>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    </head>
    <body>
        <h3 align="center">User's Locations</h3>
        <div id="map"></div>
        <input type="hidden" value="{{csrf_token()}}" id="token">

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" id="btnModal" hidden>
        </button>

        <div class="modal" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <img id="modalUserImage" src="#" alt="Nothing" height="100px" width="100px">
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        <h4 class="modal-title" id="modalTitle">Modal Heading</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        DOB : <span id="modalBodyAge"></span><br>
                        Email : <span id="modalBodyEmail"></span><br>
                    </div>

                </div>
            </div>
        </div>

        <script>
            function initMap() {
                $(document).ready(function () {
                    $.ajax({
                        method:'GET',
                        url:'jsonDataOfAllUsers',
                        success:function(data) {
                            var map = new google.maps.Map(
                                document.getElementById('map'),
                                {
                                    zoom: 2,
                                    center: {lat:0,lng:0
                                    }
                                });
                            var locs =JSON.parse(data);

                            var images = {};

                            for(var i=0;i<locs.data.length;i++){
                                var position = {
                                    lat: Number(locs.data[i].latitude),
                                    lng: Number(locs.data[i].longitude)
                                };
                                var marker = new google.maps.Marker({
                                    position: position,
                                    map: map,
                                    title: locs.data[i].name,
                                    icon: {
                                        url:locs.data[i].uimage,
                                        scaledSize: new google.maps.Size(50, 50)
                                    }
                                });

                                //this is the way to add new property to marker
                                marker.userId = locs.data[i].userid;
                                marker.userImage =  locs.data[i].uimage;

                                images[locs.data[i].userid]=locs.data[i].uimage;

                                marker.addListener('click', function(mk) {

                                    $.ajax({
                                        url : 'getUserDataForMap',
                                        method: 'POST',
                                        data:{
                                            id:this.userId,
                                            _token:$('#token').val()
                                        },
                                        success:function (data) {
                                            user = JSON.parse(data);
                                            $('#modalTitle').text(user.name);
                                            $('#modalBodyAge').text(user.dob);
                                            $('#modalBodyEmail').text(user.email);
                                            $('#modalUserImage').attr("src",images[user.id]).attr("alt",user.name);
                                            $('#btnModal').click();

                                        },
                                        error:function (jqXHR,textStatus,errorThrown) {
                                            //this is for the error throwback
                                            //console.log(jqXHR+'\n'+textStatus+'\n'+errorThrown);
                                        }
                                    });
                                });
                            }
                        },
                        error:function (jqXHR,textStatus,errorThrown) {
                            //this is for the error throwback
                            //console.log(jqXHR+'\n'+textStatus+'\n'+errorThrown);
                        }
                    });
                });
            }
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8F7dboJbvkgxPLCFhAwEzBbS1i4LnMgg&callback=initMap">
        </script>
    </body>
</html>