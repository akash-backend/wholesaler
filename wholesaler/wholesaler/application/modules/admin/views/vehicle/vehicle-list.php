<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?php echo $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Company</th>
                    <th>Model</th> 
                    <th>Reg. No.</th> 
                    <th>Class</th> 
                    <th>Action</th>
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($vehicles)){                                                
                foreach ($vehicles as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                        <td><?= $value['company']; ?></td>
                        <td><?= $value['model']; ?></td>
                        <td><?= $value['vehicle_no']; ?></td>
                        <td><?= $value['class']; ?></td>
                        <td>
                            <a href="<?= base_url('admin/block/vehicle_detail/'.$value['id'].'/'.uri_string()); ?>" class="btn btn-<?php if($value['status'] == 0){ echo 'danger'; }else{ echo 'success'; } ?>" ><?php if($value['status'] == 0){ echo 'Block'; }else{ echo 'Unblock'; } ?></a>
                            <!-- <a href="<?= base_url('superadmin/editClass/'.$value['id']); ?>" class="btn btn-info"><i class="fa fa-edit"></i></a> -->
                        </td>                    
                    </tr>
                <?php }
            } ?>
            </tbody> 
        </table>
    </div>
</div>