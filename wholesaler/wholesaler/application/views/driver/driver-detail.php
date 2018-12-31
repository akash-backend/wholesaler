<style type="text/css">
.iii img {
    width: 100%; max-height:300px;
 }
</style>
<div class="forms">
    <h2 class="title1"><?= $title; ?></h2>

    <div class="alert alert-dismissible fade" role="alert"><span id="msg"></span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

    <div class="row">
        <div class="form-three widget-shadow">                                  
            <div class="row">
                <div class="col-md-5">
                    <h3>Basic Detail</h3><br>
                    <dl class="dl-horizontal">
                        <dt>Driver ID </dt> <dd><?= $driver['did']; ?></dd>
                        <dt>Password </dt> <dd><?= $driver['password']; ?></dd>
                        <dt>Name </dt> <dd><?= $driver['name']; ?></dd>
                        <dt> Mobile </dt> <dd> <?= $driver['mobile']; ?></dd>                  
                        <dt>Date of Birth </dt> <dd> <?= date('dS M Y',strtotime($driver['dob'])); ?></dd>                  
                                          
                    </dl>
                </div>
                <?php if($this->session->userdata('admin')){ ?>
                    <div class="col-md-5">
                        <h3>Assign Car</h3>
                        <dl class="dl-horizontal">
                            <dt>Vehicle No. </dt> <dd id="vehicle_no"> <?= $driver['vehicle_id']; ?></dd>
                        </dl>
                        <button class="btn btn-primary assign-car">Assign Another Car</button>
                    </div>
                <?php } ?>
            </div>  
           
            <div class="row">
                <h3>Documents</h3>
                <div class="col-lg-4">
                    <?php if (file_exists('assets/userfile/driver/profile/'.$driver['image'])) {  ?>
                        <h4>Image</h4> 

                        <img width="100%" src="<?= base_url('assets/userfile/driver/profile/'.$driver['image']); ?>">
                    <?php } ?>
                </div> 
                <div class="col-lg-4">
                    <?php if (file_exists('assets/userfile/driver/documents/'.$driver['licence'])) {  ?>
                        <h4>License</h4>
                        <?php $pathinfo = pathinfo('assets/userfile/driver/profile/'.$driver['licence']);                         
                        if($pathinfo['extension'] == 'jpeg' || $pathinfo['extension'] == 'jpg' || $pathinfo['extension'] == 'png'){  ?>
                        <img width="100%" src="<?= base_url('assets/userfile/driver/documents/'.$driver['licence']); ?>">
                        <?php  }else{ ?>
                        <iframe style="width: 100%; max-height:300px" src="<?= base_url('assets/userfile/driver/documents/'.$driver['licence']); ?>"></iframe>
                        <?php   } 
                    } ?>                   
                </div>   
                <div class="col-lg-4">                    
                    <?php if (file_exists('assets/userfile/driver/documents/'.$driver['mcertificate'])) {  ?>
                        <h4>Medical Certificate</h4> 
                        <?php $pathinfo1 = pathinfo('assets/userfile/driver/profile/'.$driver['image']); 
                        if($pathinfo1['extension'] == 'jpeg' || $pathinfo1['extension'] == 'jpg' || $pathinfo1['extension'] == 'png'){ ?>
                            <img width="100%" src="<?= base_url('assets/userfile/driver/documents/'.$driver['mcertificate']); ?>">
                        <?php  }else{  ?>
                            <iframe style="width: 100%; max-height:300px" src="<?= base_url('assets/userfile/driver/documents/'.$driver['mcertificate']); ?>"></iframe>
                        <?php } 
                     } ?>                   
                </div>   
            </div>  
        </div>        
    </div>
</div>


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Assign Car</h4>
      </div>
      <div class="modal-body">        
            <div class="form-group">
                <select class="form-control" name="vehicle_class_id" id="vehicle_class_id">
                    <option disabled selected>Select Class</option>
                    <?php if(!empty($class)){
                        foreach ($class as $key => $value) { ?>
                            <option value="<?= $value['id']; ?>" <?php if($value['id'] == $driver['vehicle_class_id']){ echo 'selected'; } ?> ><?= $value['class']; ?></option>
                        <?php }
                    } ?>
                </select>   
            </div>
            <div class="form-group">
                 <select class="form-control" name="vehicle_id" id="vehicle_id">
                    <option disabled selected>Select Car</option>
                    <?php if(!empty($car)){
                        foreach ($car as $key => $value) { ?>
                            <option value="<?= $value['id']; ?>" <?php if($value['id'] == $driver['vehicle_id']){ echo 'selected'; } ?> ><?= $value['company'].' - '.$value['model']; ?></option>
                        <?php }
                    } ?>
                </select>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default assign">Submit</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
    $('.assign-car').click(function () { 
        $('#myModal').modal();
    });
    $('.assign').click(function(){
        var class_id = $('#vehicle_class_id').val();
        var vehicle_id = $('#vehicle_id').val();

        $.ajax({
            type : 'post',
            url : site_url+'admin/assignCar',
            data: { class_id : class_id, vehicle_id : vehicle_id, did : '<?= $driver['did']; ?>' },
            success : function(result){ 
                if(result != '0'){
                    $('#vehicle_no').html(result); 
                    $('.alert').removeClass('fade').addClass('alert-success');
                    $('#msg').html('Car assigned successfully');                    
                    $('#myModal').modal('toggle');
                }else{
                    $('.alert').removeClass('fade').addClass('alert-danger');
                    $('#msg').html('Some error occured. Please try again');
                    $('#myModal').modal('toggle');
                }
            }
        });
    });
</script>