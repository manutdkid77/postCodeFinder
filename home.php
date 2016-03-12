<!DOCTYPE html>
<html>
  <head>
    <title>Postcode Finder</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <style>
      .bodyContainer{
        background:      
        linear-gradient(
          rgba(23, 22, 22, 0.79), 
          rgba(12, 12, 12, 0.08)
                    ),url("img/splash.jpg");
        background-size: cover;
      }
      .navbar{
        margin: 0;
        height: 78px;
        background: transparent;
        border: none;
        border-bottom: 2px solid rgba(128, 128, 128, 0.45);
        border-radius: 27px;
      }
      .navbar-brand{
        padding: 26px 15px;
        font-size: 1.4em;
      }
      div.row>div.col-md-6{
        margin-top: 3%;
        border: 2px solid rgba(128, 128, 128, 0.45);
        border-radius: 23px;
        margin-top: 3%;
      }
      div.row>div.col-md-6>p{
        margin-top: 4%;
        color: whitesmoke;
        text-align: center;
      }
      form{
        margin-top: 3%;
        margin-left: 9%;
      }
      form >div>input#address{
        background: transparent;
        border: 2px solid rgba(128, 128, 128, 0.45);
        border-radius: 15px;
        background-image: url("img/pin.png");
        background-repeat: no-repeat;
        background-position: right;
        height: 13%;
        color: whitesmoke;
      }
      form >div>input#submit{
        margin-top: 4%;
        width: 20%;
        border: 2px solid #00BCD4;
        margin-left: 39%;
        background: transparent;
        border-radius: 15px;
        color: whitesmoke;
      }
      #postAlert{
            margin-top: 27%;
            margin-left: 12%;
            width: 78%;
            background: transparent;
            border: 1px solid rgba(128, 128, 128, 0.45);
            display: none;
            color: whitesmoke;
      }
      .alert-success{
            box-shadow: -3px 3px 36px 0px whitesmoke;
      }
      .alert-danger{
            box-shadow: -3px 3px 36px 0px tomato;
      }
      #showMapBtn{
        margin-left: 39%;
        width: 23%;
        margin-top: 4%;
        margin-bottom: 8%;
        border: 2px solid #4CAF50;
        background: transparent;
        display: none;
        color: whitesmoke;
      }
      .modal-body{
        width: 100%;
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div class="bodyContainer container-fluid">
      <div class="row">
        <div class="navbar navbar-inverse">
          <div class="container-fluid">
            <div class="navbar-header">
              <a href="" class="navbar-brand">PostCode Finder</a>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <p class="lead">Enter any address to find the postcode</p>
          <form method="get" class="col-md-10" id="addressForm">
            <div class="form-group">
              <input type="text" class="form-control" name="address" id="address" placeholder="Enter address here">
            </div>
            <div class="form-group">
              <input type="submit" value="Search" name="submit" id="submit" class="btn btn-default form-control">
            </div>
          </form>
          <div class="alert" id="postAlert">
          </div>

            <button class="btn btn-info" id="showMapBtn" data-toggle="modal" data-target="#mapModal">Display in the map</button>
        </div>
      </div>

      <div class="modal" id="mapModal">
        <div class="modal-dialog">
          <div class="modal-content" style="background-color: transparent;border: 1px solid whitesmoke;border-radius: 17px;">
            <div class="modal-header">
              <button class="close btn" data-toggle="modal" data-target="#mapModal" style="color: rgb(240, 237, 229);">&times</button>
                <h4 class="modal-title" style="color: whitesmoke;">
                  Map based on your address
                </h4>
            </div>
            <div class="modal-body">
             <div id="map-canvas" style="width: 100%; height: 480px;">
             </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
    $(document).ready(function(){
        $(".bodyContainer").css('min-height',$(window).height());
      
      $("#addressForm").submit(function(e){
        e.preventDefault();
        submitFunction();
        });

        function submitFunction(){
          var addressVal=$("#address").val();

          $.ajax({
            type:"get",
            url:"https://maps.googleapis.com/maps/api/geocode/xml?address="+encodeURIComponent(addressVal)+"&key=AIzaSyBbsHrUi-vEPm1yL0TT-aTcu3uI3J6Y35U",
            dataType:"xml",
            success: xmlParser,
            error:error
          });

          function error(){
            $(".alert").toggle();
            $(".alert").removeClass("alert-success");
            $(".alert").addClass("alert-danger");
            $(".alert").html("Could not connect to server. Please try again").fadeIn();
          }

          function xmlParser(xml){
            $(xml).find("address_component").each(function(){
              if($(this).find("type").text()=="postal_code"){
  
                                $("#postAlert").toggle();
                                $("#postAlert").removeClass("alert-danger");
                                $("#postAlert").addClass("alert-success");
                                $("#postAlert").html("The postcode is "+$(this).find("long_name").text()).fadeIn();

                                $("#showMapBtn").toggle();

                                var latitude=parseFloat($(xml).find("location").children("lat").text());
                                var longitude=parseFloat($(xml).find("location").children("lng").text());

                                $("#mapModal").on('shown.bs.modal',function () {
                                  var pos = new google.maps.LatLng(latitude, longitude);
                                  var mapProp = {
                                      center: pos,
                                      zoom: 14,
                                      draggable: true,
                                      scrollwheel: true,
                                      mapTypeId: google.maps.MapTypeId.ROADMAP
                                  };
                                  var map = new google.maps.Map(document.getElementById("map-canvas"),
                                  mapProp);

                                  
                                  google.maps.event.trigger(map,"resize");
                                  var marker = new google.maps.Marker({
                                      position: pos,
                                      title:"Hello World!"
                                  });
                                  marker.setMap(map);
                              });
                                
                              }
                else{       
                            $("#showMapBtn").hide();
                            $("#postAlert").toggle();
                            $("#postAlert").removeClass("alert-success");
                            $("#postAlert").addClass("alert-danger");
                            $("#postAlert").html("Could not find postcode for that address. Please try again").fadeIn();
                }
            });
          }
        }
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js">
    </script>
  </body>
</html>