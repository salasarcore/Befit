<?php 
/**
 * Following Code is kept for further reference.
 */
/*include('admin/conn.php');
include_once("admin/functions/common.php");
include_once("email_settings.php");
$msg="";
$self=$_SERVER['PHP_SELF'];
if(isset($_POST['login_submit']))
{
	makeSafe(extract($_POST));
	if ($txtmobile!="" && !(ctype_digit($txtmobile)))
	{
		redirect($self,"Invalid company mobile number");
	
	}
	elseif ($txtphoneno!="" && !(ctype_digit($txtphoneno)))
	{
		redirect($self,"Invalid company telephone number");
	
	}
	elseif ($txtphoneno1!="" && !(ctype_digit($txtphoneno1)))
	{
		redirect($self,"Invalid person telephone number");
	
	}
	elseif (strlen($txtmobile)<10)
	{
		redirect($self,"Company Mobile number must be 10 digit");
	
	}
	elseif (strlen($txtmobile1)<10)
	{
		redirect($self,"Mobile number must be 10 digit");
	
	}
	
	
	elseif ((trim($txtphoneno1)!="") && (strlen($txtphoneno1)<10 || strlen($txtphoneno1)>15) )
	{
		redirect($self,"telephone number should be in the range of 10-15 digits");
	}
	elseif ((trim($txtphoneno)!="") && (strlen($txtphoneno)<10 || strlen($txtphoneno)>15) )
	{
		redirect($self,"company telephone number should be in the range of 10-15 digits");
	}
	elseif ($txtemail!="" && !filter_var(trim($txtemail),FILTER_VALIDATE_EMAIL))
	{
		redirect($self,"Invalid company email address");
	
	}
	elseif ($txtemail1!="" && !filter_var(trim($txtemail1),FILTER_VALIDATE_EMAIL))
	{
		redirect($self,"Invalid  person email address");
	
	}
	else{
	$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_name = 'Placement Registration'";
	$sql_t = mysql_query($query_t) or die('Error. Query failed.');
	$nums = mysql_num_rows($sql_t);
	$gettemp = mysql_fetch_assoc($sql_t);
	$template = $gettemp['email_temp_format'];
	if($nums>0) {
		$subject="New Placement Registration";
		$path="logo.png";
		$email="";
	
		$hashvalue = array($txtcompanyname,$address,$txtphoneno,$txtmobile,$txtemail,$txtdetailscompany,$txtname,$txtaddress1,$txtphoneno1,$txtmobile1,$txtemail1,$txtdetails);
		$temp_value = getEmailMessage($template,$hashvalue);
		$sendEmail = Sending_EMail($temp_value,$email,$subject,$path);
	}
	}
}*/
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
    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    	
    	<script type="text/javascript" src="js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="js/Ujquery-ui.min.js"></script>
<link rel="Stylesheet" type="text/css" href="css/jquery-ui.css" />
<script language="javascript">
function chkME(frm)
{
if(document.frm.txtphoneno.value!=""){

var txtphoneno=document.frm.txtphoneno.value.length;
if(txtphoneno<10 || txtphoneno>15){
	alert("Complny phone number must be 10-15 digits");
	document.frm.offtelno.focus();
	return false;
} }
if(document.frm.txtphoneno1.value!=""){

	var txtphoneno1=document.frm.txtphoneno1.value.length;
	if(txtphoneno1<10 || txtphoneno1>15){
		alert("Phone number must be 10-15 digits");
		document.frm.restelno.focus();
		return false;
	} }
if(document.frm.txtmobile.value==""){
	alert("Invalid mobileno number");
	document.frm.txtmobile.focus();
	return false;
}
var txtmobile=document.frm.txtmobile.value.length;
if(txtmobile<10){
	alert("Mobile number must be 10 digit");
	document.frm.txtmobile.focus();
	return false;
}
if(document.frm.txtmobile1.value==""){
	alert("Invalid mobileno number");
	document.frm.txtmobile1.focus();
	return false;
}
var txtmobile1=document.frm.txtmobile1.value.length;
if(txtmobile1<10){
	alert("Mobile number must be 10 digit");
	document.frm.txtmobile1.focus();
	return false;
}

return true;
}
$(document).ready(function(){
	$('#txtmobile,#txtmobile1,#txtphoneno,#txtphoneno1').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9]/g))
		{
			this.value = this.value.replace(/[^0-9]/g,'');
		}
	});

});
</script>
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
				 <h2>Placements</h2>
				 <p><b>About us</b></p>
<p>To minimize your time span and speedy process in Recruiting personnel you may require an assistance of good placement consultants to fulfill all your requirements. We, therefore, wish to introduce ourselves as one of the leading professional placement service division based at Mumbai, which is promoted by a group of experienced professionals in fitness industry.</p>

<p>
BWA Placements has been catering to the spectrum of personnel needs of its clients from experienced professionals to enthusiastic fresher covering various disciplines and all possible job descriptions This has been made possible by the network built up over the years and through the Courses at BWA.caters as required is our USP</p>
<p> 
We assure you that we will render good professional service to your esteemed organization if given us a chance, we anticipate good business relations with you In future. Email to  befitonlyladiesgym@gmail.com
</p>
<p><b>SERVICES</b></p>
<p>BWA Placement Consultants offers recruitment as per your requirements at various levels such as senior; middle and junior level management positions. Both floor and off floor candidates We undertake to scrutinize and recommend the right candidates, which will minimize your time span and speedy processing and selection as per your requirements.
				 </p>
				</div>
				
				  <form  class="sky-form" action="placements.php"  id="frm" name="frm" method="post" onsubmit="return chkME(this);">
				  <header>ADD REGISTRATION FORM FROM INSTITUTE</header>
                            
                            <fieldset>          
											
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">Name of Company</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input name="txtcompanyname" id="txtcompanyname" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter company name</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">Address</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                               <textarea name="address" id="address"></textarea>
												<b class="tooltip tooltip-bottom-right">Needed to enter company address</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                   <section>
                                    <div class="row">
                                        <label class="label col col-2">Phone No</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                             <input name="txtphoneno" id="txtphoneno" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter Phone No.</b>
                                            </label>
                                        </div>
                                         <label class="label col col-2">Mobile No</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                            <input maxlength="10" name="txtmobile" id="txtmobile" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter Mobile No.</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                      <section>
                                    <div class="row">
                                        <label class="label col col-2">Email </label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                              <input name="txtemail" id="txtemail" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter company Email</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">For Details </label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                             <textarea name="txtdetailscompany" id="txtdetailscompany"></textarea>
												<b class="tooltip tooltip-bottom-right">Needed to enter company details</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                      <section>
                                    <div class="row">
                                        <label class="label col col-2">Name </label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                            <input name="txtname" id="txtname" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter name</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                          <section>
                                    <div class="row">
                                        <label class="label col col-2">Address </label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                            <textarea name="txtaddress1" id="txtaddress1"></textarea>
												<b class="tooltip tooltip-bottom-right">Needed to enter address</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                                <section>
                                    <div class="row">
                                        <label class="label col col-2">Phone No</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                             <input name="txtphoneno1" id="txtphoneno1" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter Phone No.</b>
                                            </label>
                                        </div>
                                         <label class="label col col-2">Mobile No</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                            <input maxlength="10" name="txtmobile1" id="txtmobile1" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter Mobile No.</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                  <section>
                                    <div class="row">
                                        <label class="label col col-2">Email </label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                              <input name="txtemail1" id="txtemail1" type="text" />
												<b class="tooltip tooltip-bottom-right">Needed to enter Email</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">For Details </label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                             <textarea name="txtdetails" id="txtdetails"></textarea>
												<b class="tooltip tooltip-bottom-right">Needed to enter details</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                </fieldset>
                                <footer>
                                <button class="btn btn-info pull-right" type="submit" name="login_submit" value="submit">Submit</button>
                                
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