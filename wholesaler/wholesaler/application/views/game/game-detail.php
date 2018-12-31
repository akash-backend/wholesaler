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
                        if(!empty($user['game_image']))
                            {
                        ?>
                            <img class="img-responsive" src="<?php echo base_url('/assets/Game/gamelogo/'.$user['game_image']); ?>">
                        <?php
                        }
                        ?>
                        <br/><br/>
                    </div >
                </div>
                <div class="col-md-8">                    
                    <dl class="dl-horizontal">
                        <dt>Game ID </dt> <dd><?= $user['id']; ?></dd>
                        <dt>Name</dt> <dd><?= $user['game_name']; ?></dd>
                                           
                    </dl>
                </div>
                </div>
          
        </div>        
    </div>
</div>




