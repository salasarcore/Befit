<?php include('admin/conn.php');
include_once("admin/functions/common.php");

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
			<h2 class="header gal">IMAGE GALLERY</H2>
<?php
$rowsPerPage = 20;
$pageNum = 1;
if(isset($_GET['pageNum']))    $pageNum = makeSafe($_GET['pageNum']);
$offset = ($pageNum - 1) * $rowsPerPage;


	$sql="SELECT  gallery_id, gallery_name, gallery_description, date_updated,updated_by from gallery order by date_updated desc";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	$count=mysql_num_rows($res);
	if($count>0)
	{
	while($row=mysql_fetch_array($res))
	  {
		$sqlImg="SELECT image_id,image_description, gallery_id, mime from  gallery_images where gallery_id=".$row['gallery_id'];
		$resImg=mysql_query($sqlImg) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
		$rowImg     = mysql_fetch_array($resImg, MYSQL_ASSOC);
		$imgPath="site_img/gallery/thumb/".base64_encode($rowImg['image_id']).".".$rowImg['mime'];
if(is_file($imgPath))
$imgPath="site_img/gallery/thumb/".base64_encode($rowImg['image_id']).".".$rowImg['mime'];
		else
			$imgPath="site_img/gallery/thumb/no.jpg";
		
		echo "<div class='imageGallery'><a href='gallery_show.php?gallery=".$row["gallery_id"]."'><img src=\"$imgPath\" width=\"50px\" height=\"50px\"  /></a><p>".$row["gallery_name"]."</p></div>";
}

$query   = "SELECT COUNT(gallery_id) AS numrows FROM gallery";
$result  = mysql_query($query) or die('Error, query failed');
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$numrows = $row['numrows'];
$maxPage = ceil($numrows/$rowsPerPage);

// how many pages we have when using paging?
$maxPage = ceil($numrows/$rowsPerPage);

// print the link to access each page
$self = $_SERVER['PHP_SELF'];
$nav  = '';
for($page = 1; $page <= $maxPage; $page++)
{
if ($page == $pageNum)
{
$nav .= " $page "; // no need to create a link to current page
}
else
{

$nav .= " <a href=".$self."?pageNum=".$page.">$page</a> ";
}
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

if ($pageNum > 1)
{
$page  = $pageNum - 1;
$prev  = " <a href=".$self."?pageNum=$page>[Prev]</a> ";

$first = " <a href=".$self."?pageNum=1>[First Page]</a> ";
}
else
{
$prev  = '&nbsp;'; // we're on page one, don't print previous link
$first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
$page = $pageNum + 1;
$next = " <a href=".$self."?pageNum=$page>[Next]</a> ";

$last = " <a href=".$self."?pageNum=$maxPage>[Last Page]</a> ";
}
else
{
$next = '&nbsp;'; // we're on the last page, don't print next link
$last = '&nbsp;'; // nor the last page link
}
$offset++;
if(($offset+$rowsPerPage)>$numrows)
	$rowcount=$numrows;
else
	$rowcount=$offset+$rowsPerPage-1;
echo "<div class=clear style='float: right;'><b>Showing : ".$offset." - ".$rowcount." of ".$numrows." Record(s) ".$first . $prev . $nav . $next . $last."</b></div>";

	}
	else 
		 echo "<div class=clear style='float: left;'><b>NO RECORDS FOUND</b></div>";
		
?>
	
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


