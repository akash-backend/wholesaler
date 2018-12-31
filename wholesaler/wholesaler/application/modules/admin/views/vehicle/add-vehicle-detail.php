<div class="forms">
	<h2 class="title1"><?= $title; ?></h2>
	<?= $this->session->flashdata('msg'); ?>
	<div class="row">		
		<div class="form-three widget-shadow">

			<form class="form-horizontal" method="post" action="<?= base_url('admin/addVehicleDetail') ?>" enctype="multipart/form-data">
				<input type="text" name="vehicle_company_id" value="<?= $vehicle_id; ?>">
				<input type="text" name="model_id" value="<?= $model_id; ?>">
				<input type="text" name="company_id" value="<?= $company_id; ?>">
				<div class="form-group">
					<label class="control-label col-md-3">Vehicle Number</label>
					<div class="col-md-8">
						<input type="text" name="vehicle_no" class="form-control" placeholder="Vehicle Number Plate">
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-md-3">Colour</label>
					<div class="col-md-8">
						<input type="text" name="colour" class="form-control" placeholder="Vehicle Colour">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">Year</label>
					<div class="col-md-8">
						<input type="text" name="year" class="form-control" placeholder="Vehicle year">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">Class</label>
					<div class="col-md-8">
						<select class="form-control" name="vehicle_class_id" required>
							<option selected disabled>Select Car Class</option>
							<?php if(!empty($carClass)){
								foreach ($carClass as $key => $value) { ?>
									<option value="<?= $value['id'] ?>"><?= $value['class']; ?></option>
								<?php }
							} ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">Insurance</label>
					<div class="col-md-8">
						<input type="file" name="insurance" class="form-control">
					</div>
				</div>
				
				<button type="submit" class="btn btn-default">Submit</button>
			</form> 
		</div>
	</div>
</div>