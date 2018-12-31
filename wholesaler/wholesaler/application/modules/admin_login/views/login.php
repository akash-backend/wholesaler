<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
<title>Sport Admin Panel </title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Glance Design Dashboard Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
SmartPhone Compatible web template, free WebDesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

<!-- Bootstrap Core CSS -->
<link href="<?= base_url('assets/css/bootstrap.css'); ?>" rel='stylesheet' type='text/css' />

<!-- Custom CSS -->
<link href="<?= base_url('assets/css/style.css'); ?>" rel='stylesheet' type='text/css' />

<!-- font-awesome icons CSS-->
<link href="<?= base_url('assets/css/font-awesome.css'); ?>" rel="stylesheet"> 
<!-- //font-awesome icons CSS-->

 
 <!-- js-->
<script src="<?= base_url('assets/js/jquery-1.11.1.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/modernizr.custom.js'); ?>"></script>

<!--webfonts-->
<link href="//fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
<!--//webfonts-->

</head> 
<body class="cbp-spmenu-push">
    <div class="main-content">      
       
        <!-- main content start-->
        
            <div class="main-page login-page ">
                <h2 class="title1">Login</h2>
                <div class="widget-shadow">
                    <div class="login-body">
                        <?= $this->session->flashdata('msg'); ?>
                        <form  method="post" action="<?= base_url('admin_login/checkLogin'); ?>">
                            <input type="text" class="user" name="email" placeholder="Enter Login ID" required="">
                            <input type="password" name="password" class="lock" placeholder="Password" required="">
                            
                            <input type="submit" value="Sign In">
                           
                        </form>
                    </div>
                </div>
                
            </div>
       
        <!--footer-->
        <!-- <div class="footer">
           <p>&copy; 2018 All Rights Reserved  </p>  
        </div -->>
        <!--//footer-->
    </div>
    
    
    
    <!-- Bootstrap Core JavaScript -->
   <script src="<?= base_url('assets/js/bootstrap.js'); ?>"> </script>
    <!-- //Bootstrap Core JavaScript -->
   
</body>
</html> 