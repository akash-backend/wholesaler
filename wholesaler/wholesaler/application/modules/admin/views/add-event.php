
<div class="forms">
	<h2 class="title1"><?= $title; ?></h2>
	<?= $this->session->flashdata('msg'); ?>
	<div class="row">		
		<div class="form-three widget-shadow">
<form class="form-horizontal" id="addevent" method="post"  action="<?php if(!empty($event)){ echo site_url('admin/editEvent');}else{
        	echo  base_url('admin/addEvent');
        } ?>"  enctype="multipart/form-data" >
				<h3 class="text-center">Event Details</h3><br>


         <div class="form-group">
          <label class="col-sm-2 control-label">Title</label>
          <div class="col-sm-8">
            <input type="text" name="title"  class="form-control"  placeholder="title" 
            value="<?php if(!empty($event)){ echo $event['title']; } ?>"
            >
            <p><?php echo form_error('title', '<span class="error_msg">', '</span>'); ?></p>
          </div>
        </div>

			
				<div class="form-group">
					<label for="focusedinput" class="col-sm-2 control-label">Sport</label>
					<div class="col-sm-8">
						<select name="game_id"  class="form-control">
                    <option value=''> Select Game</option>
                    <?php
          
                    if (!empty($game)) {
		            foreach($game as $a2){
                  
		              	 	?>

		<option value="<?php echo $a2['id']; ?>" <?php if(!empty($event)) { if($a2['id'] == $event['game_id']) { echo "selected"; } } ?>><?php echo $a2['game_name'] ;?></option>
		       
               <?php
			  } }
			  ?>
                </select>
               <p> <?php echo form_error('game_id', '<span class="error_msg">', '</span>'); ?></p>
					</div>					
				</div>


        <div class="form-group">
          <label class="col-sm-2 control-label">Price</label>
          <div class="col-sm-8">
            <input type="text" name="price"  class="form-control"  placeholder="price" 
            value="<?php if(!empty($event)){ echo $event['price']; } ?>"
            >
            <p><?php echo form_error('price', '<span class="error_msg">', '</span>'); ?></p>
          </div>
        </div>





				<div class="form-group">
                <label for="dtp_input1" class="col-sm-2 control-label">DateTime</label>
                <div class="col-sm-8">
                <div class="input-group date form_datetime col-sm-8" data-date="1979-09-16T05:25:07Z" data-date-format="yyyy-mm-dd hh:ii" data-link-field="dtp_input1">
                    <input class="form-control" size="16" type="text" value="<?php if(!empty($event)){ echo $event['event_time']; } ?>" name="event_time" readonly required>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                </div>
              </div>
				<input type="hidden" id="dtp_input1" value="" />
<p><?php echo form_error('event_time', '<span class="error_msg">', '</span>'); ?></p>
        <br/>

            	</div>

				<div class="form-group">
					<label class="col-sm-2 control-label ">Duration</label>
					<div class="col-sm-8 slidecontainer">
            <div class="row">
              <div class="col-sm-9">
					<input type="range" min="2" max="50"  value="<?php if(!empty($event)){ echo $event['event_duration']; } ?>" class="slider" id="myRange" name="event_duration" required>
						</div>
            <div class="col-sm-3">
              <p>Hour: <span id="demo" ></span></p>
               
            </div>
            </div>
					</div>

				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label ">No of participants</label>
					<div class="col-sm-8 slidecontainer">
            <div class="row">
              <div class="col-sm-9">
					<input type="range" min="8" max="50"  class="slider" value="<?php if(!empty($event)){ echo $event['event_participant_no']; } ?>" id="myRange1" name="event_participant_no" required>
           </div>
           <div class="col-sm-3">
						<p>Participants: <span id="demo1"></span></p>
           
          </div>
        </div>
					</div>
				</div>

				<div class="form-group">
					<label for="inputPassword" class="col-sm-2 control-label">Description</label>
					<div class="col-sm-8">
						
						   <textarea rows="6" name="event_description" class="form-control" ><?php if(!empty($event)){ echo $event['event_description']; } ?> </textarea>
               <p><?php echo form_error('event_description', '<span class="error_msg">', '</span>'); ?><p>
					</div>
				</div>

        <div class="form-group">
          <label class="col-sm-2 control-label label-input-lg">Event Backround Image</label>
          <div class="col-sm-8">
            <input type="file" name="image" >


             <?php if(!empty($event)){ ?>
                     
                    
                    
                    <br/><br/>
                 <img class="img-responsive" src="<?php echo base_url('/assets/event/image/'.$event['event_image']); ?>" height="250px" width="200px">
                    <?php }?>



          </div>
        </div>
        

				<div class="form-group">
          <div class="col-md-12">
	                <label class="control-label">Address</label>
	        		<textarea name="event_address" id="event_address" class="form-control" data-validate="required" readonly><?php if(!empty($event)){ echo $event['event_address']; } ?> </textarea>
               <p><?php echo form_error('event_address', '<span class="error_msg">', '</span>'); ?><p>
	           </div>
              </div>
            


				<div class="col-sm-12">
            		<div class="form-group row">
	                	<label class="control-label">Map</label>
	                   	<div class="pac-card" id="pac-card">
		      				<div>
			       				<div id="type-selector" class="pac-controls">
			        			</div>
						    </div>
						    <div id="pac-container">
						        <input id="pac-input" type="text"
						            placeholder="Enter a location" class="form-control">
						    </div>
					    </div>
					    <div id="map" style="height: 250px"></div>
					    <div id="infowindow-content">
					      <span id="place-name"  class="title"></span><br>
					      <span id="place-address"><?php if(!empty($event)){ echo $event['event_address']; }else{echo "Woolloomooloo NSW 2011, Australia";} ?></span>
					    </div>
				    </div>
                </div>
         
           <input type="hidden" name="lat" id="lat" value="<?php if(!empty($event)){echo $event['latitude'];}?>">
           <input type="hidden" name="lng" id="lng" value="<?php if(!empty($event)){echo $event['longitude'];}?>">


				
			



					<?php if(!empty($event)){ ?>
  <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                <input type="submit" name="submit" value="Edit" class="btn btn-success">
                <?php } else { ?>
                <input type="submit" name="submit" value="Add" class="btn btn-success">
                <?php } ?>



			</form>
		</div>
	</div>
</div>	


<!-- date time picker start-->
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/event/datepicker/bootstrap-datetimepicker.min.css'); ?>">

<script type="text/javascript" src="<?= base_url('assets/event/datepicker/bootstrap-datetimepicker.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/event/datepicker/bootstrap-datetimepicker.fr.js'); ?>"></script>


<script type="text/javascript">
    $('.form_datetime').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1
    });
	$('.form_date').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('.form_time').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0
    });
</script>

<!-- date time picker end -->



<!-- range start -->
<style>
.error_msg {
    color: red;
}

.slidecontainer {
  /*  width: 40%; */
}

.slidecontainer p {
  margin-top: 10px;
}

.slider {
    -webkit-appearance: none;
    width: 100%;
    height: 10px;
    border-radius: 5px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
    margin-top: 15px;
}

.slider:hover {
    opacity: 1;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}
</style>

<script>
var slider1 = document.getElementById("myRange");
var output1  = document.getElementById("demo");
output1.innerHTML = slider1.value;
slider1.oninput = function() {
  console.log('value'+this.value);
  output1.innerHTML = this.value;
}
</script>


<script>
var slider = document.getElementById("myRange1");
var output  = document.getElementById("demo1");





output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>


<!-- rang end -->

<!-- start map  -->

<script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      

      function initMap() {
      var lat=$('#lat').val();
      var lng=$('#lng').val();
      var mylatlng={lat:parseFloat(lat),lng:parseFloat(lng)};
      var address=$("#event_address").val();
      //var lat = -33.8688;
      //var lng = 151.2195;
      if(lat == "" && lng == ""){
      lat = -33.8688;
      lng = 151.2195;
      mylatlng={lat:lat,lng:lng};
      }
      if(address==""){
      address="Woolloomooloo NSW 2011, Australia";
      }
      //alert(lat);
      // alert(lat+'bbb'+lng);
      
        var map = new google.maps.Map(document.getElementById('map'), {
          center: mylatlng,
          zoom: 13
        });
        var card = document.getElementById('pac-card');
        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');

        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        
        //console.log(infowindowContent);
        infowindow.setContent(infowindowContent);

        var marker = new google.maps.Marker({
          position: mylatlng,
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });
        infowindow.open(map,marker);

        //Add listener
    //google.maps.event.addListener(marker, "click", function(event) {
      //toggleBounce() 
      //showInfo(this.position);
    //});

    //
           geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng);
        });

        //var marker;
        function placeMarker(location) {
            if(marker){ //on vérifie si le marqueur existe
                marker.setPosition(location); //on change sa position
            }else{
                marker = new google.maps.Marker({ //on créé le marqueur
                    position: location, 
                    map: map
                });
            }
            
            getAddress(location);
        }

  function getAddress(latLng) {
    geocoder.geocode( {'latLng': latLng},
      function(results, status) {
        if(status == google.maps.GeocoderStatus.OK) {
          if(results[0]) {
            document.getElementById("event_address").value = results[0].formatted_address;
            document.getElementById('lat').value=results[0].geometry.location.lat();
            document.getElementById('lng').value=results[0].geometry.location.lng();

            //alert(results[0].geometry.location.lat()+'  '+results[0].geometry.location.lng());
            geocoder.geocode( { 'address': results[0].formatted_address}, function(results, status) {

  if (status == google.maps.GeocoderStatus.OK) {
    var latitude = results[0].geometry.location.lat();
    var longitude = results[0].geometry.location.lng();
    $("#lat").val(latitude);
    $("#lng").val(longitude);
    // alert(latitude);
  } 
}); 


          }
          else {
            document.getElementById("event_address").value = "No results";
           
          }
        }
        else {
          document.getElementById("event_address").value = status;
          
        }
      });
    }

    //
    


        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
           
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
             
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindowContent.children['place-icon'].src = place.icon;
          place_name=infowindowContent.children['place-name'].textContent = place.name;
          infowindowContent.children['place-address'].textContent = address;
          infowindow.open(map, marker);
          // alert(address);
          //   document.getElementById("place_name").value =address;
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById(id);
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes(types);

          });
        }

        function showInfo(latlng) {
      //alert('hello');
      geocoder.geocode({
        'latLng': latlng
      }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            alert(results[1].formatted_address);
            // here assign the data to asp lables
            //document.getElementById('<%=addressStandNo.ClientID %>').value = results[1].formatted_address;
          } else {
            alert('No results found');
          }
        } else {
          alert('Geocoder failed due to: ' + status);
        }
      });
    }

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);
            
        document.getElementById('use-strict-bounds')
            .addEventListener('click', function() {
              console.log('Checkbox clicked! New state=' + this.checked);
              autocomplete.setOptions({strictBounds: this.checked});
            });
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBz_5wUAu5_IYnw4RnwiK50qT1GUQiy1DE&libraries=places&callback=initMap"
        async defer></script>

        <!-- end map -->
