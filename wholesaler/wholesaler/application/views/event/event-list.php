
<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?= $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Title</th>
                    <th>Game Name</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Participant No</th>
                     
                    <th>Action</th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($event)){                                                
                foreach ($event as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                        <td><?= $value['title']; ?></td>
                        <td><?= $value['game_name']; ?></td>
                        <td><?= $value['event_time']; ?></td>
                        <td><?= $value['event_duration']; ?></td>
                        <td><?= $value['event_participant_no']; ?></td>

                       
                        <td>
                          
                       

                             <a href="<?php echo base_url('admin/editEvent').'/'.$value['id']; ?>" class="btn btn-blue btn-sm btn-icon icon-left">Edit</a>&nbsp;


                            <a href="<?= base_url('admin/block/sport_event/'.$value['id'].'/'.uri_string()); ?>" class="btn btn-<?php if($value['status'] == 0){ echo 'danger'; }else{ echo 'success'; } ?>" ><?php if($value['status'] == 0){ echo 'Block'; }else{ echo 'Unblock'; } ?></a> &nbsp;
                        </td>                        
                    </tr>
                <?php }
            } ?>
            </tbody> 
        </table>
    </div>
</div>


