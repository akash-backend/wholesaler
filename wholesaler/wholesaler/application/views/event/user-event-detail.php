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
                <div class="col-md-12">
                    <h3>Basic Detail</h3><br>
                </div>
                <div class="col-md-4">
                     <div>
                        <?php
                        if(!empty($event['event_image']))
                            {
                        ?>
                            <img class="img-responsive" src="<?php echo base_url('/assets/event/image/'.$event['event_image']); ?>">
                        <?php
                        }
                        ?>
                        <br/><br/>
                    </div >
                </div>
                <div class="col-md-8">                    
                    <dl class="dl-horizontal">
                        <dt>Event ID </dt> <dd><?= $event['id']; ?></dd>
                        <dt>Title </dt> <dd><?= $event['title']; ?></dd>
                        <dt>Sport name</dt> <dd><?= $event['game_name']; ?></dd>
                        <dt>Time </dt> <dd><?= $event['event_time']; ?></dd>
                        <dt>Duration</dt> <dd><?= $event['event_duration'].'&nbsp; Hour'; ?></dd>
                        <dt>Participant </dt> <dd><?= $event['event_participant_no']; ?></dd>
                        <dt>Description</dt> <dd><?= $event['event_description']; ?></dd>
                        <dt>User Email</dt> <dd><?= $event['user_email']; ?></dd>
                        <dt>User Name</dt> <dd><?= $event['user_name']; ?></dd>
                                           
                    </dl>
                </div>
                </div>
          
        </div>        
    </div>
</div>




