<?php 

include('conn.php');
include_once("../functions/functions.php");
$_SESSION['br_id']=1;
//include('chk_session.php');


?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>fitness | Welcome...</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicon -->
   
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
	  <link href="../plugins/smartmenus/jquery.smartmenus.bootstrap.css" rel="stylesheet"> 
      <link rel="stylesheet" href="css/style.css">
	  	<link rel="stylesheet" href="../css/modal.css" type="text/css" />
	<link rel="stylesheet" href="../css/dhtmlwindow.css" type="text/css" />
</head> 
<body class="boxed container">
	<div class="wrapper">
		
       <?php include('php/header.php');?>
    
		<div class="container">
			<div class="row">
				<div class="col-md-12" style="min-height:400px">
				<?php
							
							
							$src=@$_GET['src'];
					
							if(isset($src))
							 {
								if(is_file($src))
									include($src);
								else
									include('404.htm'); 	
								}
							else
								include('init.php');
				?>
					
			
				</div>
			</div>
		</div>
	
			<?php include('php/copyright.php');?>
</div>

	<!-- JS Global Compulsory -->           
	<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="../js/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="../plugins/bootstrap/js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="../js/back-to-top.js"></script>
	<!-- JS Implementing Plugins -->           
	<!-- SmartMenus jQuery plugin -->
    <script type="text/javascript" src="../plugins/smartmenus/jquery.smartmenus.js"></script>

    <!-- SmartMenus jQuery Bootstrap Addon -->
    <script type="text/javascript" src="../plugins/smartmenus/jquery.smartmenus.bootstrap.js"></script><script type="text/javascript"></script>
<script type="text/javascript" src="../js/dhtmlwindow.js"></script>
<script type="text/javascript" src="../js/modal.js"></script>
	
	<!--[if lt IE 9]>
		<script src="plugins/respond.js"></script>
	<![endif]-->			
<body>
</html>