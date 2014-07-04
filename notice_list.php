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
    			<link rel="stylesheet" href="css/modal.css" type="text/css" />
		<link rel="stylesheet" href="css/dhtmlwindow.css" type="text/css" />
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
			<h2 class="header notice">NOTICE LIST</h2>


<table class="table table-bordered">
	<thead>
		<tr>
			<th>DATE</th>
			<th>SUBJECT</th>
			<th>BODY</th>
			<th>ATTACHMENT</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$rowsPerPage = 15;
		$pageNum = 1;
		if(isset($_GET['pageNum']))    $pageNum = $_GET['pageNum'];
		$offset = ($pageNum - 1) * $rowsPerPage;
		$i=0;
		$sql="select nid , ntype ,nbody, ndate , subject,mime,published,updated_by  from notice_board where published='Y' order by date_updated desc LIMIT $offset, $rowsPerPage ";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error($link));

		while($row=mysql_fetch_array($res))
		{
			
			$i=$i+1;
			
			$date=$row['ndate'];
			$temp=new DateTime($date);
			$dates=$temp ->format('jS-M-Y, g:i A');

			
			echo "<tr class=";
			  if($i%2==0) echo "row0"; else echo "row1";
			  echo ">";
				
				echo "<td width='20%' align='center'>".$dates."</td>";
				?> 
				<td style='word-wrap: break-word;width:200px;'><a href="javascript:open_modal('read_notice.php?nid=<?php echo base64_encode($row['nid']);?>',600,400,'NOTICE');" class='hov'><?php echo $row['subject'];?></a></td> 
				<?php
				$str="";
				if(trim($row['nbody'])!="") 
				$str=strip_tags(substr($row['nbody'],0,100))."....";
					echo "<td>".$str."</td>";
				
				echo "<td align='center'>";
				if($row['mime']!="")
					echo "<a target='_blank' href='site_img/notice/".base64_encode($row['nid']).".".$row['mime']."' class='hov'><img src='images/message_attachment.png' height='25' width='25'/></a>";
				
				echo "</td>";
									

			echo "</tr></tbody>";
		}
		echo "<tfoot><tr><th colspan=5>";
					 

	// how many rows we have in database
	$query   = "SELECT COUNT(nid) AS numrows FROM notice_board where published='Y'  " ;
	$result  = mysql_query($query) or die('Error, query failed');
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
	$maxPage = ceil($numrows/$rowsPerPage);
	$offset++;
	if(($offset+15)<$numrows){$endpage=($offset+$rowsPerPage-1);}else{$endpage=$numrows;}
	
	if($numrows!=0)
	{
		echo "Showing : ".$offset." - ".$endpage." of ".$numrows." Record(s) : ";
	}
	else
	{
		echo "No Records Found";
	}
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
	   
		  $nav .= " <a href=\"notice_board.php?pageNum=$page\">$page</a> ";
	   } 
	}

	// creating previous and next link
	// plus the link to go straight to
	// the first and last page

	if ($pageNum > 1)
	{
	   $page  = $pageNum - 1;
	   $prev  = " <a href=\"notice_board.php?pageNum=$page\">[Prev]</a> ";

	   $first = " <a href=\"notice_board.php?pageNum=1\">[First Page]</a> ";
	} 
	else
	{
	   $prev  = '&nbsp;'; // we're on page one, don't print previous link
	   $first = '&nbsp;'; // nor the first page link
	}

	if ($pageNum < $maxPage)
	{
	   $page = $pageNum + 1;
	   $next = " <a href=\"notice_board.php?pageNum=$page\">[Next]</a> ";

	   $last = " <a href=\"notice_board.php?pageNum=$maxPage\">[Last Page]</a> ";
	} 
	else
	{
	   $next = '&nbsp;'; // we're on the last page, don't print next link
	   $last = '&nbsp;'; // nor the last page link
	}

	echo $first . $prev . $nav . $next . $last;

					 
	?>
			
			

	</th>
	</tr>
	</tfoot>
		</table>
		<style>
.hov:hover
{
text-decoration:underline;
}
</style>
		
</div>			</div>
		
		
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
<script type="text/javascript" src="js/dhtmlwindow.js"></script>
<script type="text/javascript" src="js/modal.js"></script>
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








<div id="content">
<div id="navigation"><a href="index.php">Home</a> <span> Notice List</span></div>
<h2 class="header notice">NOTICE LIST</h2>


<div class="clear"></div>
	<?php 
	
	if(@$n_n>0)  {?><div class="notify"> You Have  <?php echo $n_n; ?>  New  Notice(s) </div> <?php } ?>
<div class="clear"></div>
<table class="adminlist">
	<thead>
		<tr>
			<th>DATE</th>
			<th>SUBJECT</th>
			<th>BODY</th>
			<th>ATTACHMENT</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$rowsPerPage = 15;
		$pageNum = 1;
		if(isset($_GET['pageNum']))    $pageNum = $_GET['pageNum'];
		$offset = ($pageNum - 1) * $rowsPerPage;
		$i=0;
		$sql="select nid , ntype ,nbody, ndate , subject,mime,published,updated_by  from notice_board where published='Y' and br_id='".$_SESSION['stu_br_id']."' order by date_updated desc LIMIT $offset, $rowsPerPage ";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error($link));

		while($row=mysql_fetch_array($res))
		{
			
			$i=$i+1;
			
			$date=$row['ndate'];
			$temp=new DateTime($date);
			$dates=$temp ->format('jS-M-Y, g:i A');

			
			echo "<tr class=";
			  if($i%2==0) echo "row0"; else echo "row1";
			  echo ">";
				
				echo "<td width='20%' align='center'>".$dates."</td>";
				?> 
				<td style='word-wrap: break-word;width:200px;'><a href="javascript:open_modal('read_notice.php?nid=<?php echo base64_encode($row['nid']);?>',600,400,'NOTICE');" class='hov'><?php echo $row['subject'];?></a></td> 
				<?php
				$str="";
				if(trim($row['nbody'])!="") 
				$str=strip_tags(substr($row['nbody'],0,100))."....";
					echo "<td>".$str."</td>";
				
				echo "<td align='center'>";
				if($row['mime']!="")
					echo "<a target='_blank' href='site_img/notice/".DOMAIN_IDENTIFIER."_".base64_encode($row['nid']).".".$row['mime']."' class='hov'><img src='images/message_attachment.png' height='25' width='25'/></a>";
				
				echo "</td>";
									

			echo "</tr></tbody>";
		}
		echo "<tfoot><tr><th colspan=5>";
					 

	// how many rows we have in database
	$query   = "SELECT COUNT(nid) AS numrows FROM notice_board where published='Y' and br_id='".$_SESSION['stu_br_id']."' " ;
	$result  = mysql_query($query) or die('Error, query failed');
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
	$maxPage = ceil($numrows/$rowsPerPage);
	$offset++;
	if(($offset+15)<$numrows){$endpage=($offset+$rowsPerPage-1);}else{$endpage=$numrows;}
	
	if($numrows!=0)
	{
		echo "Showing : ".$offset." - ".$endpage." of ".$numrows." Record(s) : ";
	}
	else
	{
		echo "No Records Found";
	}
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
	   
		  $nav .= " <a href=\"notice_board.php?pageNum=$page\">$page</a> ";
	   } 
	}

	// creating previous and next link
	// plus the link to go straight to
	// the first and last page

	if ($pageNum > 1)
	{
	   $page  = $pageNum - 1;
	   $prev  = " <a href=\"notice_board.php?pageNum=$page\">[Prev]</a> ";

	   $first = " <a href=\"notice_board.php?pageNum=1\">[First Page]</a> ";
	} 
	else
	{
	   $prev  = '&nbsp;'; // we're on page one, don't print previous link
	   $first = '&nbsp;'; // nor the first page link
	}

	if ($pageNum < $maxPage)
	{
	   $page = $pageNum + 1;
	   $next = " <a href=\"notice_board.php?pageNum=$page\">[Next]</a> ";

	   $last = " <a href=\"notice_board.php?pageNum=$maxPage\">[Last Page]</a> ";
	} 
	else
	{
	   $next = '&nbsp;'; // we're on the last page, don't print next link
	   $last = '&nbsp;'; // nor the last page link
	}

	echo $first . $prev . $nav . $next . $last;

					 
	?>
			
			

	</th>
	</tr>
	</tfoot>
		</table>
</div>
<div class="clear"></div>

<style>
.hov:hover
{
text-decoration:underline;
}
</style>

