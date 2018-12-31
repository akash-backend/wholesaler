
<aside class="sidebar-left">
  <nav class="navbar navbar-inverse">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".collapse" aria-expanded="false">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      </button>
      <h1><a class="navbar-brand" href="<?= base_url('admin/dashboard'); ?>"> 

        <img class="img-responsive" style="display: inline-block; width: 30px; vertical-align: sub; margin-top: 7px;" src="<?php echo base_url('/assets/sporto-logo.png'); ?>" height="20" width="20"> Sports App</a></h1>

        
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>

        <li class="treeview">
          <a href="<?= base_url('admin/dashboard'); ?>">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        

            <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span>User</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('admin/UserList'); ?>"><i class="fa fa-angle-right"></i> User List</a></li>
            
          </ul>
        </li>   



         <li class="treeview">
          <a href="#">
            <i class="fa fa-gamepad"></i>
            <span>Games</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('admin/gameList'); ?>"><i class="fa fa-angle-right"></i> Game List</a></li>
            <li><a href="<?= base_url('admin/addGame'); ?>"><i class="fa fa-angle-right"></i> Add Game</a></li>
          </ul>
        </li>  


         <li class="treeview">
          <a href="#">
            <i class="fa fa-calendar-o"></i>
            <span>Event</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('admin/eventList'); ?>"><i class="fa fa-angle-right"></i> Event List</a></li>
            <li><a href="<?= base_url('admin/addEvent'); ?>"><i class="fa fa-angle-right"></i> Add Event</a></li>
          </ul>
        </li>  

         <li class="treeview">
          <a href="#">
            <i class="fa fa-calendar"></i>
            <span>User Event</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('admin/user_eventList'); ?>"><i class="fa fa-angle-right"></i> Event List</a></li>
           
          </ul>
        </li>  



      </ul>
    </div>
      <!-- /.navbar-collapse -->
  </nav>
</aside>
  </div>

  </div>
<!--left-fixed -navigation-->

<!-- header-starts -->
<div class="sticky-header header-section ">
  <div class="header-left">
    <!--toggle button start-->
    <button id="showLeftPush"><i class="fa fa-bars"></i></button>
    <!--toggle button end-->
    <div class="profile_details_left"><!--notifications of menu start -->      
      <div class="clearfix"> </div>
    </div>
    <!--notification menu end -->
    <div class="clearfix"> </div>
  </div>
  <div class="header-right">
    
    
    
    
    <div class="profile_details">   
      <ul>
        <li class="dropdown profile_details_drop">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <div class="profile_img"> 
              <span class="prfil-img"><img src="<?= base_url('assets/images/2.jpg'); ?>" alt=""> </span> 
              <div class="user-name">
                <p>Admin </p>
                <span>Administrator</span>
              </div>
              <i class="fa fa-angle-down lnr"></i>
              <i class="fa fa-angle-up lnr"></i>
              <div class="clearfix"></div>  
            </div>  
          </a>
          <ul class="dropdown-menu drp-mnu">
            <!-- <li> <a href="#"><i class="fa fa-cog"></i> Settings</a> </li> 
            <li> <a href="#"><i class="fa fa-user"></i> My Account</a> </li> 
            <li> <a href="#"><i class="fa fa-suitcase"></i> Profile</a> </li>  -->
            <li> <a href="<?= base_url('admin/logout'); ?>"><i class="fa fa-sign-out"></i> Logout</a> </li>
          </ul>
        </li>
      </ul>
    </div>
    <div class="clearfix"> </div>       
  </div>
  <div class="clearfix"> </div> 
</div>

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/bootstap-validator/bootstrapValidator.min.js'); ?>">
<script type="text/javascript" src="<?= base_url('assets/bootstap-validator/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/bootstap-validator/companyValidation.js'); ?>"></script>

<div id="page-wrapper">
    <div class="main-page">