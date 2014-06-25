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
   
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- CSS Implementing Plugins -->
    
    <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="plugins/basic_slider/bjqs.css">

    <!-- CSS Theme -->    
    <link rel="stylesheet" href="plugins/sky-forms/version-2.0.1/css/sky-forms.css">
    <link rel="stylesheet" href="plugins/sky-forms/version-2.0.1/css/custom-sky-forms.css">
    <!-- CSS Page Style -->    
   

    <!-- CSS Theme -->    
    <link rel="stylesheet" href="css/themes/default.css" id="style_color">
    <link rel="stylesheet" href="css/pages/service_event_posts.css" id="style_color">

    <!-- CSS Customization -->
    <link rel="stylesheet" href="css/custom.css">
</head> 
<body class="boxed container">
	<div class="wrapper">
		<?php include('php/header.php');?>
        <!-- Navigation -->
		
		<?php include('php/menu.php');?>
		
			
			
        <!-- End Navigation -->
    
    <div class="container">
		<div class="row">
			<div class="col-md-9">
		      <form class="sky-form" id="sky-form1" action="index.php" novalidate="novalidate" method="POST">
                            <header>Register</header>
                            
                            <fieldset>          
								<?php echo @$message;?>							
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">First Name</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="firstname">
												<b class="tooltip tooltip-bottom-right">Needed to enter firstname</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Middle Name</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="middlename">
												<b class="tooltip tooltip-bottom-right">Needed to enter middlename</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Last Name</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="lastname">
												<b class="tooltip tooltip-bottom-right">Needed to enter lastname</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                
                                <section>
                                    <div class="row">
                                        <label class="label col col-3">Address</label>
                                        <div class="col col-9">
                                            <label class="input">
                                              
                                                <input type="text" name="address">
												<b class="tooltip tooltip-bottom-right">Needed to enter address</b>
                                            </label>
                                            
                                        </div>
                                    </div>
                                </section> 
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">City</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="city">
												<b class="tooltip tooltip-bottom-right">Needed to enter city</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Pin</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="pin">
												<b class="tooltip tooltip-bottom-right">Needed to enter pin</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Office Tel. No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="offtelno">
												<b class="tooltip tooltip-bottom-right">Needed to enter Office Tel. No.</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
								<section>
                                    <div class="row">
                                        <label class="label col col-2">Res. Tel. No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="restelno">
												<b class="tooltip tooltip-bottom-right">Needed to enter Res. Tel. No.</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Mobile No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="mobileno">
												<b class="tooltip tooltip-bottom-right">Needed to enter mobile no.</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Office Tel. No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="offtelno">
												<b class="tooltip tooltip-bottom-right">Needed to enter Office Tel. No.</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                
                                <section>
                                    <div class="row">
                                        <div class="col col-4"></div>
                                        <div class="col col-8">
                                            <label class="checkbox"><input type="checkbox" checked="" name="remember"><i></i>Keep me logged in</label>
                                        </div>
                                    </div>
                                </section>
                            </fieldset>
                            <footer>
                                <button class="btn btn-info pull-right" type="submit" name="login_submit" value="submit">Register</button>
                               
                            </footer>
                        </form>        
	
			</div>
		
		
		<!--right content-->
		<?php include('php/right.php');?>
		<!--end right content-->
	 </div>
	 </div>
         <!--footer-->
		<?php include('php/footer.php')?>
		<!--/footer-->
		<!--copyright-->
			<?php include('php/copyright.php')?>
		  <!--/copyright-->
    
	</div>



	<!-- JS Global Compulsory -->           
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="plugins/bootstrap/js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="js/back-to-top.js"></script>
	<!-- JS Implementing Plugins -->           
	<script type="text/javascript" src="plugins/bxslider/jquery.bxslider.js"></script>
	<script type="text/javascript" src="plugins/basic_slider/bjqs-1.3.min.js"></script>

	<script class="secret-source">
        jQuery(document).ready(function($) {

          $('#banner-fade').bjqs({
		   animtype      : 'fade',
            height      : 450,
            width      : 1170,
			showcontrols    : false,
			showmarkers     : false,   
            
            responsive  : true
          });
		  $('.testimonials-slider').bxSlider({
				   slideWidth: 800,
				   minSlides: 1,
				   maxSlides: 1,
				   slideMargin: 32,
				   auto: true,
				   autoControls: true
				 });

        });
      </script>
	<!--[if lt IE 9]>
		<script src="plugins/respond.js"></script>
	<![endif]-->			
<body>
</html>