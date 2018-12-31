

<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?= $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Name</th>
                     
                    <th>Action</th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($driver)){                                                
                foreach ($driver as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                        <td><?= $value['game_name']; ?></td>
                       
                        <td>
                            <a href="<?= base_url($link.$value['id']); ?>" class="btn btn-primary" ><i class="fa fa-eye"></i></a> &nbsp;
                       

                             <a href="<?php echo base_url('admin/editGame').'/'.$value['id']; ?>" class="btn btn-blue btn-sm btn-icon icon-left">Edit</a>&nbsp;


                            <a href="<?= base_url('admin/block/sport_game/'.$value['id'].'/'.uri_string()); ?>" class="btn btn-<?php if($value['status'] == 0){ echo 'danger'; }else{ echo 'success'; } ?>" ><?php if($value['status'] == 0){ echo 'Block'; }else{ echo 'Unblock'; } ?></a> &nbsp;
                        </td>                        
                    </tr>
                <?php }
            } ?>
            </tbody> 
        </table>
    </div>
</div>


