<div class="forms">
	<h2 class="title1"><?= $title; ?></h2>
	<?= $this->session->flashdata('msg'); ?>
	<div class="row">		
		<div class="form-three widget-shadow">

			<form class="form-horizontal" method="post" action="<?= base_url('admin/addVehicleDetail') ?>">
				<div class="form-group">
					<label class="control-label col-md-3">Select Vehicle Company</label>
					<div class="col-md-8">
						<select class="form-control" name="vehicle_id" id="vehicle_id">
							<option selected disabled>Select Vehicle Company</option>
							<option value="0">Add New Company</option>
							<?php if (!empty($company)) {
								foreach ($company as $key => $value) { ?>
									<option value="<?= $value['id']; ?>"><?= $value['company']; ?></option>
								<?php }
							} ?>
						</select>
					</div>
				</div>

				<div id="model">
					<div class="form-group">
						<label class="control-label col-md-3">Select Vehicle Model</label>
						<div class="col-md-8">
							<select class="form-control" name="model_id" id="model_id">
								<option selected disabled>Select Vehicle Model</option>
								<option value="0">Add New Vehicle Model</option>
							</select>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			</form> 
		</div>
	</div>
</div>	

<script type="text/javascript">
	$('#vehicle_id').change(function () {
		var vehicle_id = $(this).val(); 
		if(vehicle_id == 0){ 
			var companyName = prompt("Please enter Company Name", "Honda"); 
			if(companyName != null){
				$.ajax({
					type : 'post',
			        url: site_url+'admin/addVhicleCompanyName', 
			        data : { company : companyName },
			        success: function(result){
			          if(result == '1'){
			          	$.ajax({
							type : 'post',
					        url: site_url+'admin/getVehicleCompany', 					        
					        success: function(result){
					          $('#vehicle_id').html(result);
					        }
						});	
			          }
			        }
				});	
			}			
		}else{
			$.ajax({
				type : 'post',
		        url: site_url+'admin/getVehicleModel/'+vehicle_id, 
		        //data : { id : vehicle_id },
		        success: function(result){
		          $('#model_id').html(result);
		        }
			});	
		}
	});

	$('#model_id').change(function () {
		var model_id = $(this).val(); 
		var vehicle_id = $('#vehicle_id').val();
		if(model_id == 0){ 
			var modelName = prompt("Please enter Company Model", "WagonR"); 
			if(modelName != null){
				$.ajax({
					type : 'post',
			        url: site_url+'admin/addVehicleModel', 
			        data : { model : modelName , vehicle_id : vehicle_id },
			        success: function(result){ 			          
			          	 $('#model_id').html(result);		     
			          
			        }
				});	
			}			
		}
	});

</script>