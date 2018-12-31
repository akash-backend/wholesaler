<div class="forms">
	<h2 class="title1"><?= $title; ?></h2>
	<?= $this->session->flashdata('msg'); ?>
	<div class="row">		
		<div class="form-three widget-shadow">
			<form class="form-horizontal" method="post" action="<?= base_url('admin/addDriver'); ?>" enctype="multipart/form-data" id="add-driver">
				<h3 class="text-center">Event Details</h3><br>
				<div class="form-group">
					<label for="focusedinput" class="col-sm-2 control-label">Company ID</label>
					<div class="col-sm-6">
						<input type="text" name="did" id="cid" class="form-control" placeholder="Company ID" required>
					</div>
					<div class="col-sm-2"><button type="button" class="btn btn-default" id="generate">Generate ID</button></div>				
				</div>
				<div class="form-group">
                <label for="dtp_input1" class="col-md-2 control-label">DateTime Picking</label>
                <div class="input-group date form_datetime col-md-5" data-date="1979-09-16T05:25:07Z" data-date-format="dd MM yyyy HH:ii p" data-link-field="dtp_input1">
                    <input class="form-control" size="16" type="text" value="" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                </div>
				<input type="hidden" id="dtp_input1" value="" /><br/>
            </div>
				<div class="form-group">
					<label for="focusedinput" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-8">
						<input type="text" name="name" class="form-control" placeholder="Driver Name" required>
					</div>					
				</div>
								
				<div class="form-group">
					<label for="inputPassword" class="col-sm-2 control-label">Email</label>
					<div class="col-sm-8">
						<input type="email" name="email" class="form-control"  placeholder="email" required>
					</div>
				</div>

				<div class="form-group">
					<label for="inputPassword" class="col-sm-2 control-label">Password</label>
					<div class="col-sm-8">
						<input type="password" name="password" class="form-control" placeholder="Password" required>
					</div>
				</div>
				
				<div class="form-group">
					<label for="inputPassword" class="col-sm-2 control-label">Mobile</label>
					<div class="col-sm-8">
						<input type="text" name="mobile" class="form-control" placeholder="Mobile" required>
					</div>
				</div>
				
				<div class="form-group mb-n">
					<label for="largeinput" class="col-sm-2 control-label">Date of Birth</label>
					<div class="col-sm-4">						
						<div class='input-group date' id='datetimepicker1'>
		                    <input type='text' class="form-control" name="dob" />
		                    <span class="input-group-addon">
		                        <span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
					</div>
				</div>
				<br>
				<h3 class="text-center">Documents</h3><br>
				<div class="form-group mb-n">
					<label class="col-sm-2 control-label label-input-lg">Driver Image</label>
					<div class="col-sm-8">
						<input type="file" name="image" required>
					</div>
				</div>
				<div class="form-group mb-n">
					<label class="col-sm-2 control-label label-input-lg">Driving Licence</label>
					<div class="col-sm-8">
						<input type="file" name="licence" required>
					</div>
				</div>
				<div class="form-group mb-n">
					<label for="largeinput" class="col-sm-2 control-label label-input-lg">Medical Certificate</label>
					<div class="col-sm-8">
						<input type="file" name="mcertificate" required>
					</div>
				</div>
				<div class="col-sm-offset-2">
					<button type="submit" class="btn btn-default">Submit</button>
				</div>
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