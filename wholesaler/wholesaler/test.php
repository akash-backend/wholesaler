
<link href="<?php echo base_url();?>assets/css/imgareaselect-default.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery.awesome-cropper.css">
<style type="text/css"> 
 #pet-multi-img {border:1px solid #eee;display:inline-block;float:left;height:95px !important;margin-bottom:10px;margin-right:10px;width:114px;}
 
div#show-pet-image {width:100%;}

.fileUpload {color:#979898;font-size:16px;height:46px;line-height:46px;text-align:center;width:100%;}

.fileUpload {border:1px solid #ddd;height:46px;overflow:hidden;position:relative;}

.fileUpload input.upload {cursor:pointer;font-size:20px;height:46px;left:0;margin:0;opacity:0;padding:0;position:absolute;top:0;width:100%;}

.img-row {margin-top:15px;}

</style>

<?php if($this->session->flashdata('error')){ ?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong><?php echo $this->session->flashdata('error'); ?></strong>
        </div>
    </div>
</div>
<?php } ?>
<?php if($this->session->flashdata('success')){ ?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong><?php echo $this->session->flashdata('success'); ?></strong>
        </div>
    </div>
</div>
<?php } ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <?php if(!empty($car_list)) echo 'Edit'; else echo 'Add'; ?> Car
        </div>
    </div>
    <div class="panel-body">
        <form action="<?php if(!empty($car_list)){ echo site_url('admin/edit_car');}else{
        	echo site_url('admin/add_car');
        } ?>" role="form" id="form1" method="post" class="validate" enctype="multipart/form-data">
           
             <div class="form-group row">
               <div class="col-sm-6">
                <label class="control-label">Car Name</label>
                <input type="text" name="car_name"  class="form-control" data-validate="required"  <?php if(!empty($car_list)) echo 'value="'.$car_list[0]->car_name.'"'; ?> />
                </div>
                <div class="col-sm-6">
                <label class="control-label">Car Full Name</label>
                <input type="text" name="car_fullname"  class="form-control" data-validate="required"  <?php if(!empty($car_list)) echo 'value="'.$car_list[0]->car_fullname.'"'; ?> />
              </div>
            </div> 
            
           
            
            
             <div class="form-group row">
                 <div class="col-sm-4">
                <label class="control-label">State</label>
              <select name="car_state_id" id="state_ids" class="form-control" required>
                    <option value=''> Select State</option>
                    <?php
          
                     if (!empty($state)) {
            foreach($state as $a2){
              	 	?>
<option value="<?php echo $a2->state_id; ?>" <?php if(!empty($car_list)) { if($a2->state_id ==$car_list[0]->car_state_id) { echo "selected"; } } ?>><?php echo $a2->state_name ;?></option>
       
               <?php
			  } }
			  ?>
                </select>
            </div>  
            
            
                
             <div class="col-sm-4">
                 
                <label class="control-label">City</label>
                  <?php if (!empty($city)) {
                    
             foreach($city as $c){
                  
              	 	?>
                   <select name="car_city_id" id="city_id" class="form-control" required>
                    <option value="<?php echo $c->city_id; ?>"><?php echo $c->city_name ;?></option>
                </select>
                
                  <?php }
                  
             } 
            else{
            ?>
                
               <select name="car_city_id" id="city_id" class="form-control" required>
                    <option value="">select city</option>
                </select>
            <?php } ?>     
            </div>  
           
            
              
           <!--  <div class="col-sm-4">
               

                 <label class="control-label">Zip Code</label>

                <input type="text" name="car_zip_code" class="form-control" data-validate="required"  <?php// if(!empty($car_list)) echo 'value="'.$car_list[0]->car_zip_code.'"'; ?> />
            </div>   -->
            
           </div> 
            <div class="form-group">
                <label class="control-label">Address</label>
        <textarea name="car_address" id="car_address" class="form-control" data-validate="required" readonly><?php if(!empty($car_list)){ echo $car_list[0]->car_address; } ?> </textarea>
               </div>
            
            
            
             <div class="form-group row">
                <div class="col-sm-4">
                <label class="control-label">Price</label>

                <input type="text" name="car_price" id="price" class="form-control" data-validate="required"  <?php if(!empty($car_list)) echo 'value="'.$car_list[0]->car_price.'"'; ?> />
                <span id="sprice"></span>
            </div> 
             
            
              <div class="col-sm-4">
                <label class="control-label">Reseller Price</label>

                <input type="text" name="car_Reseller_price" id="reseller_price" class="form-control" data-validate="required"  <?php if(!empty($car_list)) echo 'value="'.$car_list[0]->car_Reseller_price.'"'; ?> />
                <span id="sreseller_price"></span>
            </div> 
             
             
              <div class="col-sm-4">
                <label class="control-label">Car featured</label>
                
                 
                
              <select name="car_featured"  class="form-control" required>
                 
                    <option value='' >select featured</option>
                    <option value='1'<?php if(!empty($car_list)) if($car_list[0]->car_featured==1){ echo "selected"; }  ?>>yes</option>
                      <option value='2' <?php if(!empty($car_list)) if($car_list[0]->car_featured==2){ echo "selected"; }  ?> >no</option>
               </select>
                </div> 
               
            </div>
                
           <!--  <div class="form-group">
                <label class="control-label">Latitude</label>

                <input type="text" name="car_latitude" id="car_latitude" class="form-control" data-validate="required"  <?php //if(!empty($car_list)) echo 'value="'.$car_list[0]->car_latitude.'"'; ?> />
                 </div> 
            
            
           
            
            
            <div class="form-group">
                <label class="control-label">Longitude</label>

                <input type="text" name="car_longitude" id="car_longitude" class="form-control" data-validate="required"  <?php //if(!empty($car_list)) echo 'value="'.$car_list[0]->car_longitude.'"'; ?> />
               </div>  -->
            
                        
            <div class="col-sm-12">
            <div class="form-group row">
                <label class="control-label">Map</label>
                   <div class="pac-card" id="pac-card">
      <div>
        <!-- <div id="title">
          Autocomplete search
        </div> -->
        <div id="type-selector" class="pac-controls">
          <!-- <input type="radio" name="type" id="changetype-all" checked="checked">
          <label for="changetype-all">All</label>

          <input type="radio" name="type" id="changetype-establishment">
          <label for="changetype-establishment">Establishments</label>

          <input type="radio" name="type" id="changetype-address">
          <label for="changetype-address">Addresses</label>

          <input type="radio" name="type" id="changetype-geocode">
          <label for="changetype-geocode">Geocodes</label>
        </div>
        <div id="strict-bounds-selector" class="pac-controls">
          <input type="checkbox" id="use-strict-bounds" value="">
          <label for="use-strict-bounds">Strict Bounds</label> -->
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
      <span id="place-address"><?php if(!empty($car_list)){ echo $car_list[0]->car_address; }else{echo "Woolloomooloo NSW 2011, Australia";} ?></span>
    </div>
                </div>
                </div>
         
           <input type="hidden" name="lat" id="lat" value="<?php if(!empty($car_list)){echo $car_list[0]->car_latitude;}?>">
            <input type="hidden" name="lng" id="lng" value="<?php if(!empty($car_list)){echo $car_list[0]->car_longitude;}?>">

         
            
             <div class="form-group row">
                <div class="col-sm-4">
                <label class="control-label">Door</label>

                <input type="text" name="car_door" id="car_latitude" class="form-control" data-validate="required"  <?php if(!empty($car_list)) echo 'value="'.$car_list[0]->car_door.'"'; ?> />
                </div> 
            
      		
      		
      		
      		<div class="col-sm-4">
               <label class="control-label">Options</label>
            	
               	 <input type="text" name="car_option" id="car_option4" class="form-control"   <?php if(!empty($car_list)) echo 'value="'.$car_list[0]->car_option.'"'; ?> />
               	
                </div> 
                
                
                
                
                
                
              
            
            
              <div class="col-sm-4">
                <label class="control-label">Car Type</label>
              <select name="car_category_id" type_ids" class="form-control" required>
                    <option value=''> Select Car Type</option>
                    <?php
          
                     if (!empty($type)) {
            foreach($type as $a2){
              	 	?>
<option value="<?php echo $a2->carType_id; ?>" <?php if(!empty($car_list)) { if($a2->carType_id==$car_list[0]->car_category_id) { echo "selected"; } } ?>><?php echo $a2->carType_name; ?></option>
       
               <?php
			  } }
			  ?>
                </select>
            </div> 

            </div> 
            
                   
            
            
            <div class="form-group row">
              <div class="col-sm-4">
                <label class="control-label">Fuel</label>
              <select name="car_Fuel"  class="form-control" required>
                    <option value=''>Select Fuel</option>
                    <?php
                    foreach($fuel as $u){
              	 	?>
<option value="<?php echo $u->carFuel_id; ?>" <?php if(!empty($car_list)) { if($u->carFuel_id ==$car_list[0]->car_Fuel) { echo "selected"; } } ?>><?php echo $u->carFuel_type;?></option>
       
               <?php
                    }
			  ?>
                </select>
            </div>  
            
            
                       
                       <div class="col-sm-4">
                <label class="control-label">Color</label>
              <select name="car_color[]"  class="selectpicker form-control" multiple title="Select Color" required>
                    
                    <?php
                    foreach($color as $u){
                  ?>
<option value="<?php echo $u->carColor_id; ?>" <?php if(!empty($car_list)) {  if (in_array($u->carColor_id,$color_id)) { echo "selected"; } } ?>><?php echo $u->carColor_name;?></option>
       
               <?php
                    }
        ?>
                </select>
            </div>  
            
            
                  
            <div class="col-sm-4">
                <label class="control-label">Brand Name</label>
              <select name="car_Brand" id="brand_ids" class="form-control" required>
                    <option value=''> Select Brand</option>
                    <?php
          
                     if (!empty($brand)) {
            foreach($brand as $a2){
              	 	?>
<option value="<?php echo $a2->brand_id; ?>" <?php if(!empty($car_list)) { if($a2->brand_id==$car_list[0]->car_Brand) { echo "selected"; } } ?>><?php echo $a2->brand_name ;?></option>
       
               <?php
			  } }
			  ?>
                </select>
            </div>  
          </div>
            
            
            
            <div class="form-group row">
                   <div class="col-sm-4">
                <label class="control-label">Model</label>
                  <?php if (!empty($city)) {
                    
             foreach($model as $c){
                  
              	 	?>
                
                   <select name="car_model" id="model_id" class="form-control" required>
                    <option value="<?php echo $c->carModel_id; ?>"><?php echo $c->carModel_name ;?></option>
                </select>
                
                  <?php }
                  
             } 
            else{
            ?>
                
               <select name="car_model" id="model_id" class="form-control" required>
                    <option value=""> select Model</option>
                </select>
            <?php } ?>     
            </div>  
            
                           <!-- <div class="form-group">
                <label class="control-label">Model</label> 	
              <select name="car_model"  class="form-control" required>
                    <option value=''>Select Model</option>
                    <?php
                    foreach($model as $u){
              	 	?>
<option value="<?php echo $u->carModel_id; ?>" <?php if(!empty($car_list)) { if($u->carModel_id ==$car_list[0]->car_model) { echo "selected"; } } ?>><?php echo $u->carModel_name;?></option>
       
               <?php
                    }
			  ?>
                </select>
            </div>  
             -->


              <div class="col-sm-4">
                <label class="control-label">Version</label>
              <select name="car_Version"  class="form-control" required>
                    <option value=''>Select version</option>
                    <?php
                    foreach($version as $u){
                  ?>
<option value="<?php echo $u->carVersion_id; ?>" <?php if(!empty($car_list)) { if($u->carVersion_id ==$car_list[0]->car_Version) { echo "selected"; } } ?>><?php echo $u->carVersion_name;?></option>
       
               <?php
                    }
        ?>
                </select>
            </div>  
                        <div class="col-sm-4">
                <label class="control-label">Blindagem Type</label>
              <select name="car_blindagem"  class="form-control" required>
                    <option value=''>Select blindagem type</option>
                    <?php
                    foreach($blindagem as $u){
              	 	?>
<option value="<?php echo $u->blindagem_id; ?>" <?php if(!empty($car_list)) { if($u->blindagem_id ==$car_list[0]->car_blindagem) { echo "selected"; } } ?>><?php echo $u->blindagem_type;?></option>
       
               <?php
                    }
			  ?>
                </select>
            </div>  
            
             
          </div>
            
         
            
            
                  <div class="form-group row  ">
                  <div class="col-sm-4">  <label class="control-label">Year</label>
              <select name="car_Year"  class="form-control" required>
                    <option value=''>Select Year</option>
                    <?php
                    foreach($year as $u){
              	 	?>
<option value="<?php echo $u->carYear_id; ?>" <?php if(!empty($car_list)) { if($u->carYear_id ==$car_list[0]->car_Year) { echo "selected"; } } ?>><?php echo $u->carYear_name;?></option>
       
               <?php
                    }
			  ?>
                </select>
            </div>  
            
            
            
            
                    <div class="col-sm-4"> 
                <label class="control-label">Gear Type</label>
              <select name="car_Gear"  class="form-control" required>
                    <option value=''>Select Gear Type</option>
                    <?php
                    foreach($gear as $u){
              	 	?>
<option value="<?php echo $u->carGear_id; ?>" <?php if(!empty($car_list)) { if($u->carGear_id ==$car_list[0]->car_Gear) { echo "selected"; } } ?>><?php echo $u->carGear_type;?></option>
       
               <?php
                    }
			  ?>
                </select>
            </div>  

            <div class="col-sm-4"> 
                <label class="control-label">User Name</label>
              <select name="car_user_id"  class="form-control" required>
                    <option value=''>Select User</option>
                    <?php
                    foreach($user as $u){
                  ?>
<option value="<?php echo $u->user_id; ?>" <?php if(!empty($car_list)) { if($u->user_id ==$car_list[0]->car_user_id) { echo "selected"; } } ?>><?php echo $u->user_name ;?></option>
       
               <?php
                    }
        ?>
                </select>
            </div> 

            
             </div>  
            
             
              <!-- <div class="form-group">

                                                    <label for="details">Car Profile Image: </label>
                                                    
                                                    <input id="profile_image" type="hidden" name="car_profile_image">

                                                    <form role="form">
                                                     <div class="upload-btn-wrapper">
                                                         <button class="upload_btn" type="button">Upload banner image</button>
                                                    <input id="sample_input" type="hidden" name="test[image]">
                                                    </div>
                                                  </form>
                                                    
                                                </div>  -->
            
            
            
            
             <div class="form-group row">
            <div class="col-sm-12 padding-right-0">
                           
                                <div class="fileUpload">
                                    <span> <i class="fa fa-plus"> </i> Upload Product Images </span>
                                    <!-- <input type="hidden" name="media_for" value="1">
 -->
                                    <input type="file" name="file[]" accept="image/*"
                                    multiple accept="image/*" class="upload pet_images"
                                    id="upload-pet-img" />

                                   
                                </div> 
                               
                            </div>

               <div class="col-sm-12">
                          <div class="img-row clearfix">
                            <div class="pad-left col-sm-12">
                                <div class="inset-img clearfix" id="show-pet-image">
                                </div>
                                 <div class="inset-img clearfix image_preview" id="image_preview"></div>
                               
                            </div>
                            

                        </div>
                        </div>

                          <?php if(!empty($car_list)){ ?>
                    <?php 
                    $car=$car_list[0]->car_images;
                     if(!empty($car))
                    {
                    $car=explode(",",$car);
                           foreach($car as $c)
                    {
      
                    
                    ?>
                    <div class="filediv">
           <a href="<?php echo site_url('admin/delete_image') ?>/<?php echo $car_list[0]->car_id; ?>/<?php echo base64_encode($c) ?>" class="btn btn-success btn-sm   delet-car-button"><i class="fa fa-trash-o" aria-hidden="true"></i>
</a>
                                    
                 <img class="car-images-uploaded" src="<?php echo base_url('uploads/cars/'.$c); ?>"  >
                 </div>
                   <?php }}}?>
                        </div>
            
            
            
  
        
            
          
            <div class="form-group">
                <?php if(!empty($car_list)){ ?>
  <input type="hidden" name="car_id" value="<?php echo $car_list[0]->car_id; ?>">
                <input type="submit" name="submit" onclick="return dataValidation()" value="Edit" class="btn btn-success">
                <?php } else { ?>
                <input type="submit" name="submit" onclick="return dataValidation()" value="Add" class="btn btn-success">
                <?php } ?>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/datepicker/bootstrap-datepicker.js"></script>



<script>

var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
$('#add_more').click(function() {
  //alert($('#form1 input[type=file]').get(0).files.length);
var count_file=0;
$("#filediv .file_type").each(function() {
    //alert($(this).val());
    if($(this).val()==""){
    count_file=1;
    }

});
if( document.getElementById("file").files.length != 0 && count_file !=1){
$(this).before($("<div/>", {
id: 'filediv'
}).fadeIn('slow').append($("<input/>", {
name: 'file[]',
type: 'file',
id: 'file',
class: 'file_type'
}), $("<br/><br/>")));
}
});
// Following function will executes on change event of file input to select different file.
$('body').on('change', '#file', function() {
if (this.files && this.files[0]) {
abc += 1; // Incrementing global variable by 1.
var z = abc - 1;
var x = $(this).parent().find('#previewimg' + z).remove();
$(this).before("<div id='abcd" + abc + "' class='abcd'><img id='previewimg" + abc + "' src=''/></div>");
var reader = new FileReader();
reader.onload = imageIsLoaded;
reader.readAsDataURL(this.files[0]);
$(this).hide();
$("#abcd" + abc).append($("<i>", {
id: 'img',
class: 'fa fa-trash-o',
alt: 'delete'

}).click(function() {
$(this).parent().parent().remove();
}));
}
});
// To Preview Image
function imageIsLoaded(e) {
$('#previewimg' + abc).attr('src', e.target.result);
};
$('#upload').click(function(e) {
var name = $(":file").val();
if (!name) {
alert("First Image Must Be Selected");
e.preventDefault();
}
});
});

</script>

<style>
    @import "http://fonts.googleapis.com/css?family=Droid+Sans";
form{
background-color:#fff
}
#maindiv{
width:960px;
margin:10px auto;
padding:10px;
font-family:'Droid Sans',sans-serif
}
#formdiv{
width:500px;
float:left;
text-align:center
}
form{
padding:40px 20px;
box-shadow:0 0 10px;
border-radius:2px
}
h2{
margin-left:30px
}
.upload {
  background-color: #ff6600;
  border: 1px solid #ff6600;
  border-radius: 5px;
  box-shadow: none;
  color: #fff;
  display: inline-block;
  /*float: right;*/
  padding: 10px;
  text-shadow: none;
}
 
#file{
color:green;
padding:5px;
border:1px dashed #123456;
background-color:#f9ffe5;
margin-bottom: 10px;
}
#upload{
margin-left:45px
}
#noerror{
color:green;
text-align:left
}
#error{
color:red;
text-align:left
}
 
.abcd{
text-align:center;
position: relative;
 
}

/*.abcd #img {
  background-color: #2b2b2b;
  border: 7px none;
  color: #fff;
  font-weight: bold;
  height: 30px;
  margin-bottom: 12px !important;
  margin-left: 0;
  margin-right: 0;
  margin-top: -31px;
  text-transform: capitalize;
  top: 0;
  width: 55px;
  z-index: 2147483647;
}*/

.abcd #img {
  background-color: red;
  border: 7px none;
  color: #fff;
  font-weight: bold;
  z-index: 2147483647;
  padding: 10px;
  position: absolute;
  left: 0;
}
.abcd img{
  border: 1px solid #e8debd;
    height: 250px;
    padding: 5px;
    width: 100%;
}

#filediv {
  /*display: inline-block;*/
  float: left;
  margin:6px;
  width: 300px;
}
b{
color:red
}

.full-width {
    width: 100%;
}

</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 

<script src="<?php echo base_url(); ?>assets/js/jquery.imgareaselect.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/jquery.awesome-cropper.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/DT_bootstrap.js"></script> 
<script>
    $(document).ready(function () {
        $('#sample_input').awesomeCropper(
        { width: 1024, height: 400, debug: true }
        );
    });


</script>



<script>

function preview_images_new1()
{

 

 var total_file=document.getElementById("images").files.length;
 for(var i=0;i<total_file;i++)
 {

 //$('#image_preview').append("<div class='col-md-3'><img class='img-responsive' src='"+URL.createObjectURL(event.target.files[i])+"'></div>");


  //$(this).parent().closest('.image_preview').append("<div class='col-md-3'><img class='img-responsive' src='"+URL.createObjectURL(event.target.files[i])+"'></div>");


  //$(this).parent().closest(".image_preview").append("<div class='col-md-3'><img class='img-responsive' src='"+URL.createObjectURL(event.target.files[i])+"'></div>");
 }
}
 $("body").on('change',".imagesnew", function() {

//$( ".imagesnew" ).change(function() {

  console.log($(this).attr('imgattr'));

var total_file=document.getElementById("images").files.length;

 for(var i=0;i<total_file;i++)
 {

 //$('#image_preview').append("<div class='col-md-3'><img class='img-responsive' src='"+URL.createObjectURL(event.target.files[i])+"'></div>");

 $(this).parent().prev('.image_preview').append("<div class='col-md-3'><img class='img-responsive' src='"+URL.createObjectURL(event.target.files[i])+"'></div>");
}
});


$(".btn-addproduct").click(function(){
    //$("p").append("<b>Appended text</b>");

    $(".addmoreclone").clone()
           .removeAttr("class")
           .addClass( "col-sm-10 uploadbox")
           .append( $('<a class="delete_clone" href="JavaScript:void(0);">Remove</a>') )
            .appendTo("#additionalselects");

   // $(".uploadbox").clone().insertAfter("div.uploadbox:last");

  //  $("ol").append("<li>Appended item</li>");
});
    $("body").on('click',".delete_clone", function() {
        $(this).closest(".uploadbox").remove();
    });

 function previewImages() {
$("#show-pet-image img").remove();
  var preview = document.querySelector('#show-pet-image');
 
  if (this.files) {
    [].forEach.call(this.files, readAndPreview);
  }

  function readAndPreview(file) {

    // Make sure `file.name` matches our extensions criteria
    if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
      return alert(file.name + " is not an image");
    } // else...
   
    var reader = new FileReader();
   
    reader.addEventListener("load", function() {
      var image = new Image();
      image.height = 100;
      image.title  = file.name;
      image.src    = this.result;
      image.id = "pet-multi-img";
      preview.appendChild(image);
    }, false);
   
    reader.readAsDataURL(file);
   
  }

}
  document.querySelector('#upload-pet-img').addEventListener("change", previewImages, false); 
  
  
   function previewImages1() {

  var preview = document.querySelector('#show-pro-image');
 
  if (this.files) {
    [].forEach.call(this.files, readAndPreview1);
  }

  function readAndPreview1(file) {

    // Make sure `file.name` matches our extensions criteria
    if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
      return alert(file.name + " is not an image");
    } // else...
   
    var reader = new FileReader();
   
    reader.addEventListener("load", function() {
      var image = new Image();
      image.height = 100;
      image.title  = file.name;
      image.src    = this.result;
      image.id = "pro-multi-img";
      preview.appendChild(image);
    }, false);
   
    reader.readAsDataURL(file);
   
  }

}
</script>

<script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      

      function initMap() {
      var lat=$('#lat').val();
      var lng=$('#lng').val();
      var mylatlng={lat:parseFloat(lat),lng:parseFloat(lng)};
      var address=$("#car_address").val();
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
            document.getElementById("car_address").value = results[0].formatted_address;
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
            document.getElementById("car_address").value = "No results";
           
          }
        }
        else {
          document.getElementById("car_address").value = status;
          
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDbBaUHyhC1zApCeg3H0rfhi1bfSk27ryM&libraries=places&callback=initMap"
        async defer></script>