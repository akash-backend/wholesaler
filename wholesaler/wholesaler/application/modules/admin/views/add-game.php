

<div class="forms">
	<h2 class="title1"><?= $title; ?></h2>
	<?= $this->session->flashdata('msg'); ?>
	<div class="row">		
		<div class="form-three widget-shadow">
			<form class="form-horizontal" method="post" action="<?php if(!empty($game)){ echo site_url('admin/editGame');}else{
        	echo  base_url('admin/addGame');
        } ?>" enctype="multipart/form-data" >
				<h3 class="text-center">Company Details</h3><br>
			
				<div class="form-group">
					<label for="focusedinput" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-8">
						<input type="text" name="sport_game" class="form-control" placeholder="Game Name" required <?php if(!empty($game)) echo 'value="'.$game['game_name'].'"'; ?>>
					</div>					
				</div>
								
				
				
			
				
				<div class="form-group mb-n">
					<label class="col-sm-2 control-label label-input-lg">Game Logo Image</label>
					<div class="col-sm-8">
						<input type="file" name="image" >


						 <?php if(!empty($game)){ ?>
                     
                    
                    
                    <br/><br/>
                 <img class="img-responsive" src="<?php echo base_url('/assets/Game/gamelogo/'.$game['game_image']); ?>" height="250px" width="200px">
                    <?php }?>



					</div>
				</div>
				
				<div class="col-sm-offset-2">

					<?php if(!empty($game)){ ?>
  <input type="hidden" name="id" value="<?php echo $game['id']; ?>">
                <input type="submit" name="submit" value="Edit" class="btn btn-success">
                <?php } else { ?>
                <input type="submit" name="submit" value="Add" class="btn btn-success">
                <?php } ?>


					
				</div>
			</form>
		</div>
	</div>
</div>	



