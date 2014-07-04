<?php

include('admin/conn.php');
include_once("admin/functions/common.php");

include("admin/functions/employee/dropdown.php");
include("admin/functions/functions.php");
include("admin/functions/comm_functions.php");
require 'admin/sms/SmsSystem.class.php';

include('email_settings.php');
$smssend = new SMS(); //create object instance of SMS class

$self=$_SERVER['PHP_SELF'];
if(isset($_POST['login_submit']))
{	
$br_id=1;
	$Errs="";
	$name=makeSafe(@$_POST['name']);
	$height=makeSafe($_POST['height']);
	$weight=makeSafe($_POST['weight']);
	$age=makeSafe($_POST['age']);
	$phoneno=makeSafe($_POST['phoneno']);
	$altphoneno=makeSafe($_POST['alternatephoneno']);
	$healthproblems=makeSafe($_POST['healthproblems']);
	$programrecommended=makeSafe($_POST['programrecommended']);
	$altprogramrecommended=makeSafe($_POST['alternateprogramrecommended']);
	$consultant=makeSafe($_POST['consultant']);
	$followno=makeSafe($_POST['followno']);
	$enquiryfor=makeSafe($_POST['enquiryfor']);
	$category=makeSafe($_POST['category']);
	
	if (trim($name)=="")
	{
		redirect($self,"Enter name");
	}
	elseif (trim($height)=="")
	{
		redirect($self,"Enter height");
	}
	elseif (trim($weight)=="")
	{
		redirect($self,"Enter weight");
	}
	elseif (trim($age)=="")
	{
		redirect($self,"Enter age");
	}
	elseif (trim($phoneno)=="")
	{
		redirect($self,"Enter phone no");
	}
	elseif ($phoneno!="" && !(ctype_digit($phoneno)))
	{
		redirect($self,"Invalid phone no");
		
	}
	elseif ($altphoneno!="" && !(ctype_digit($altphoneno)))
	{
		redirect($self,"Invalid alternate phone no");
	
	}
	else
	{
		
		$newformid=getNextMaxId("contact_us","enquiry_no")+1;
		$sql="INSERT INTO contact_us(enquiry_no,name,height,weight,age,health_problems,category,phone_no,alt_phone_no,program_recommended ,alt_program_recommended,consultant,follow_no,enquiry_for,enquiry_date) ";
		$sql =$sql ." values(".$newformid.",'".$name."','".$height."','".$weight."','".$age."','".$healthproblems."','".$category."','".$phoneno."','".$altphoneno."','".$programrecommended."','".$altprogramrecommended."','".$consultant."',";
		$sql =$sql ."'".$followno."','".$enquiryfor."','".date('Y-m-d h:i:s')."')";

		$res=mysql_query($sql,$link);
		if(mysql_affected_rows($link)>0)
		{
		
	$message="<div class='alert-success'>Record Saved Successfully</div>";
}
	
}

}

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

    	
    	<script type="text/javascript" src="js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="js/Ujquery-ui.min.js"></script>
<link rel="Stylesheet" type="text/css" href="css/jquery-ui.css" />
<script language="javascript">

function chkME(frm)
{
if(trim(frm.name.value)==""){
	alert("Please Enter Name");
	frm.name.focus();
	return false;
}/*
if(trim(document.frm.lastname.value)==""){
	alert("Please Enter Last Name");
	document.frm.lastname.focus();
	return false;
}
if(trim(document.frm.sex.value)=="0"){
	alert("Invalid Gender Selection");
	document.frm.sex.focus();
	return false;
}

month=document.frm.month1.value;
day=document.frm.day1.value;
year=document.frm.year1.value;
if(month!="0" || day!="0" || year!="0"){
var dob=year+"-"+month+"-"+day;
if(!isDate(dob)){
	return false;
}
}

if(trim(document.frm.pin.value)!=""){
var pinlen=document.frm.pin.value.length;
if(pinlen<6){
	alert("pincode must be 6 digits");
	document.frm.pin.focus();
	return false;
}
}
if(trim(document.frm.offtelno.value)!=""){

var offtelno=document.frm.offtelno.value.length;
if(offtelno<10 || offtelno>15){
	alert("office tel number must be 10-15 digits");
	document.frm.offtelno.focus();
	return false;
} }
if(trim(document.frm.restelno.value)!=""){

	var restelno=document.frm.restelno.value.length;
	if(restelno<10 || restelno>15){
		alert("residential telephone number must be 10-15 digits");
		document.frm.restelno.focus();
		return false;
	} }
if(trim(document.frm.mobileno.value)==""){
	alert("Invalid mobileno number");
	document.frm.mobileno.focus();
	return false;
}
var mobileno=document.frm.mobileno.value.length;
if(mobileno<10){
	alert("Mobile number must be 10 digit");
	document.frm.mobileno.focus();
	return false;
}

*/
return true;
}
$(document).ready(function(){
	$('#phoneno,#alternatephoneno,#age,#height,#weight').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9]/g))
		{
			this.value = this.value.replace(/[^0-9]/g,'');
		}
	});

	$('#name').keyup(function(){
		var value = $('#firstname').val();
		if(this.value.match(/[^a-zA-Z ]/g))
		{
			this.value = this.value.replace(/[^a-zA-Z ]/g,'');
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
		      <form class="sky-form" id="frm" action="enquiry_form.php" name="frm" onsubmit="return chkME(this);"  method="POST">
                            <header>Enquiry Form</header>
                            
                            <fieldset>          
								<?php echo @$message;?>							
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">Name</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="name" id="name">
												<b class="tooltip tooltip-bottom-right">Needed to enter name</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
								       <section>
                                    <div class="row">
                                        <label class="label col col-2">Height</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="height" id="height">
												<b class="tooltip tooltip-bottom-right">Needed to enter height</b>
                                            </label>
                                        </div>
										  <label class="label col col-2">Weight</label>
										   <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="weight" id="weight">
												<b class="tooltip tooltip-bottom-right">Needed to enter weight</b>
                                            </label>
                                        </div>
										 <label class="label col col-2">Age</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="age" id="age">
												<b class="tooltip tooltip-bottom-right">Needed to enter age</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
									       <section>
                                    <div class="row">
                                        <label class="label col col-2">Phone No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="phoneno" id="phoneno">
												<b class="tooltip tooltip-bottom-right">Needed to enter phone no.</b>
                                            </label>
                                        </div>
										  <label class="label col col-2">Alternate Phone No.</label>
										   <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="alternatephoneno" id="alternatephoneno">
												<b class="tooltip tooltip-bottom-right">Needed to enter alternate phone no.</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                	 <section>
                                    <div class="row">
                                        <label class="label col col-2">Category</label>
                                        <div class="col col-2">
                                            <label class="select">
                                                
                                            <select name="category" id="category">
                                            <?php $sql="select * from school_contact_category";
                                            $res=mysql_query($sql);
                                            while($row=mysql_fetch_array($res)){?>
                                            <option value='<?php echo $row['school_contact_cat_name']?>'><?php echo $row['school_contact_cat_name'];?></option>
                                            <?php }?>
												</select>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
                                
								 <section>
                                    <div class="row">
                                        <label class="label col col-2">Health Problems</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="healthproblems" id="healthproblems">
												<b class="tooltip tooltip-bottom-right">Needed to enter health problems</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
									 <section>
                                    <div class="row">
                                        <label class="label col col-2">Program Recommended</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="programrecommended" id="programrecommended">
												<b class="tooltip tooltip-bottom-right">Needed to enter program recommended</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
								 <section>
                                    <div class="row">
                                        <label class="label col col-2">Alternate Program Recommended</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="alternateprogramrecommended" id="alternateprogramrecommended">
												<b class="tooltip tooltip-bottom-right">Needed to enter alternate program recommended</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
									 <section>
                                    <div class="row">
                                        <label class="label col col-2">Consultant</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="consultant" id="consultant">
												<b class="tooltip tooltip-bottom-right">Needed to enter consultant</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
									 <section>
                                    <div class="row">
                                        <label class="label col col-2">Follow No</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="followno" id="followno">
												<b class="tooltip tooltip-bottom-right">Needed to enter follow no</b>
                                            </label>
                                        </div>
										
                                    </div>
                                </section>
									 <section>
                                    <div class="row">
                                        <label class="label col col-2">Enquiry For.</label>
                                        <div class="col col-10">
                                            <label class="input">
                                                
                                                <input type="text" name="enquiryfor" id="enquiryfor">
												<b class="tooltip tooltip-bottom-right">Needed to enter enquiry for</b>
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