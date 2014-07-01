<?php include('admin/conn.php');
include_once("admin/functions/common.php");?>
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
			<div class="content">
				<script type="text/javascript" src="js/jquery.lightbox-0.5.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.lightbox-0.5.css" media="screen" />
	
	  <script type="text/javascript">
    $(function() {
        $('#img_gallery a').lightBox();
    });
    </script>
<?php
	$sql="SELECT  gallery_id, gallery_name, gallery_description, date_updated,updated_by from gallery where gallery_id=".makeSafe($_GET['gallery'])." order by date_updated desc";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	if(mysql_affected_rows()>0)
		{
		$rowGal     = mysql_fetch_array($res, MYSQL_ASSOC);
		$gname=$rowGal['gallery_name'];
		$gallery_description=$rowGal['gallery_description'];
		$date_updated=$rowGal['date_updated'];
		}
	else
	    {$gname='Not Found';
		$gallery_description="";
		$date_updated="";
		}
	?>
<div id="content">
<div id="navigation"><a href="index.php">Home</a><a href="gallery.php"> Gallery List</a> <span>Image List </span></div>
<h2 class="header icongallery">IMAGE GALLERY : <?php echo $gname; ?></h2>
	
	
	<div id="img_gallery">
	<?php
	$sqlImg="SELECT image_id,image_description, gallery_id, mime from  gallery_images where gallery_id='".makeSafe($_GET['gallery'])."' order by date_updated desc";
	$resImg=mysql_query($sqlImg) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	if(mysql_num_rows($resImg)>0)
	{
	while($rowImg=mysql_fetch_array($resImg))
	  {
		
	
		$imgPath="site_img/gallery/thumb/".base64_encode($rowImg['image_id']).".".$rowImg['mime'];
$imgPathBig="site_img/gallery/bigimg/".base64_encode($rowImg['image_id']).".".$rowImg['mime'];
if(is_file($imgPath))
$imgPath="site_img/gallery/thumb/".base64_encode($rowImg['image_id']).".".$rowImg['mime'];
		else
			$imgPath="site_img/gallery/thumb/no.jpg";
		
		echo "<a href='".$imgPathBig."' ><img src=\"$imgPath\" width=\"50px\" height=\"50px\" alt=".$gname." /></a>";
}
	}else {
echo "No Records Found";
}
	?>
		
	  </div>
	
	</div>
		
			</div>
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
