
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
            <?php if(!empty($brand_list)) echo 'Edit'; else echo 'Add'; ?> Brand
        </div>
    </div>
    <div class="panel-body">
        <form action="<?php if(!empty($brand_list)){ echo site_url('admin/edit_brand');}else{
          echo site_url('admin/add_brand');
        } ?>" role="form" id="form1" method="post" class="validate" enctype="multipart/form-data">
           
             <div class="form-group">
                <label class="control-label">Brand Name</label>
                <input type="text" name="brand_name"  class="form-control" data-validate="required"  <?php if(!empty($brand_list)) echo 'value="'.$brand_list[0]->brand_name.'"'; ?> />
            </div> 
        
            
            
              <div class="form-group">
                <label class="control-label">Brand Image</label>
                <!-- <input type="file" name="brand_image"> -->
                 <input id="file-4" type="file" class="file" data-upload-url="#" name="brand_image">
                   <?php if(!empty($brand_list)){ ?>
                    <?php 
                    
                    foreach($brand_list as $v)
                    {
                    $image=$v->brand_image;
                    
                    
                    
                            
                    ?>
                    <br/><br/>
                 <img src="<?php echo base_url('uploads/brands/'.$image); ?>" height="250px" width="200px">
                    <?php } }?>
            </div> 
            
          
            
            
  
        
            
          
            <div class="form-group">
                <?php if(!empty($brand_list)){ ?>
  <input type="hidden" name="brand_id" value="<?php echo $brand_list[0]->brand_id; ?>">
                <input type="submit" name="submit" value="Edit" class="btn btn-success">
                <?php } else { ?>
                <input type="submit" name="submit" value="Add" class="btn btn-success">
                <?php } ?>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/datepicker/bootstrap-datepicker.js"></script>