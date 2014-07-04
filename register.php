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
	$firstname=makeSafe(@$_POST['firstname']);
	$middlename=makeSafe(@$_POST['middlename']);
	$lastname=makeSafe(@$_POST['lastname']);
	$sex=makeSafe($_POST['sex']);	
	$health=array();
	$health=$_POST['check'];
	$health_his_check=implode(",",$health);
	
	$yy1=makeSafe(@$_POST['year1']);
	$mm1=makeSafe(@$_POST['month1']);
	$dd1=makeSafe(@$_POST['day1']);
	$dob=$yy1."-".$mm1."-".$dd1;
	$yy2=makeSafe(@$_POST['year2']);
	$mm2=makeSafe(@$_POST['month2']);
	$dd2=makeSafe(@$_POST['day2']);
	$doa=$yy2."-".$mm2."-".$dd2;
	
$address=makeSafe($_POST['address']);
$city=makeSafe($_POST['city']);
	$pin=makeSafe($_POST['pin']);
	$occupation=makeSafe($_POST['occupation']);
	$height=makeSafe($_POST['height']);
	$weight=makeSafe($_POST['weight']);
	$offtelno=makeSafe($_POST['offtelno']);
	$restelno=makeSafe($_POST['restelno']);
	$mobileno=makeSafe($_POST['mobileno']);
	$email=makeSafe($_POST['email']);
	$physicalactivity=makeSafe($_POST['physicalactivity']);
	$presentmedications=makeSafe($_POST['presentmedications']);
	$presentlypregnant=makeSafe($_POST['presentlypregnant']);
	$physician=makeSafe($_POST['physician']);
	$presentphysicalactivity=makeSafe($_POST['presentphysicalactivity']);
	$physicianname=makeSafe($_POST['physicianname']);
	$clinicno=makeSafe($_POST['clinicno']);
	$mobileclinic=makeSafe($_POST['mobileclinic']);
	$contactpersonname=makeSafe($_POST['contactpersonname']);
	$contactpersontelno=makeSafe($_POST['contactpersontelno']);
	if (trim($firstname)=="")
	{
		redirect($self,"Enter first name");
	}
	elseif (trim($lastname)=="")
	{
		redirect($self,"Enter last name");
	}
	elseif ($sex=="0")
	{
		redirect($self,"Invalid gender selection");
		
	}
	elseif (($mm1!="0" || $dd1!="0" || $yy1!="0") && (checkdate($mm1,$dd1,$yy1)==false))
	{
		redirect($self,"Invalid date of birth");
		
	}
	elseif ($address=="")
	{
		redirect($self,"Enter address");
		
	}
	elseif ($pin!="" && !(ctype_digit($pin)))
	{
		redirect($self,"Invalid pincode");
		
	}
	elseif ($mobileno!="" && !(ctype_digit($mobileno)))
	{
		redirect($self,"Invalid mobile number");
	
	}
	elseif ($offtelno!="" && !(ctype_digit($offtelno)))
	{
		redirect($self,"Invalid office telephone number");
	
	}
	elseif ($restelno!="" && !(ctype_digit($restelno)))
	{
		redirect($self,"Invalid residential telephone number");
	
	}
	elseif ($clinicno!="" && !(ctype_digit($clinicno)))
	{
		redirect($self,"Invalid residential telephone number");
	
	}
	elseif ($mobileclinic!="" && !(ctype_digit($mobileclinic)))
	{
		redirect($self,"Invalid residential telephone number");
	
	}
	elseif ($mobileclinic!="" && strlen($mobileclinic)<10)
	{
		redirect($self,"Clinic Mobile number must be 10 digit");
	
	}
	
	elseif (strlen($mobileno)<10)
	{
		redirect($self,"Mobile number must be 10 digit");
		
	}
	
	
    elseif ((trim($offtelno)!="") && (strlen($offtelno)<10 || strlen($offtelno)>15) )
		{
			redirect($self,"Office telephone number should be in the range of 10-15 digits");
		}
		elseif ((trim($clinicno)!="") && (strlen($clinicno)<10 || strlen($clinicno)>15) )
		{
			redirect($self,"Clinic telephone number should be in the range of 10-15 digits");
		}
		elseif ((trim($restelno)!="") && (strlen($restelno)<10 || strlen($restelno)>15) )
		{
			redirect($self,"Residential telephone number should be in the range of 10-15 digits");
		}
	elseif ($email!="" && !filter_var(trim($email),FILTER_VALIDATE_EMAIL))
	{
		redirect($self,"Invalid email address");
	
	}
 
	else
	{
		
		$newformid=getNextMaxId("admission_application","adm_form_no")+1;
		$sql="INSERT INTO admission_application(adm_form_no,br_id,stu_fname,stu_mname,stu_lname,sex,dob,doa,present_address ,city,off_tel_no,res_tel_no,mob,email,pin,occupation,height,weight,physical_activity,present_medications,presently_pregnant,physician,present_physical_activity,physician_name,clinic_no, mobile_clinic,contact_person_name,contact_person_telno,health_history_checklist,date_applied) ";
		$sql =$sql ." values(".$newformid.",".$br_id.",'".$firstname."','".$middlename."','".$lastname."','".$sex."','".$dob."','".$doa."','".$address."','".$city."','".$offtelno."',";
		$sql =$sql ." '".$restelno."','".$mobileno."','".$email."','".$pin."','".$occupation."','".$height."','".$weight."','".$physicalactivity."','".$presentmedications."','".$presentlypregnant."','".$physician."','".$presentphysicalactivity."','".$physicianname."','".$clinicno."','".$mobileclinic."','".$contactpersonname."','".$contactpersontelno."','".$health_his_check."','".date('Y-m-d h:i:s')."')";
	
		$res=mysql_query($sql,$link);
		if(mysql_affected_rows($link)>0)
		{
			$_SESSION['sms_t_count']=0;
			$query2 = "SELECT SUM(spd.no_of_transactional_purchase) as sms_t_count, SUM(spd.no_of_promotional_purchase) as sms_p_count FROM schools sc LEFT JOIN school_sms_purchase_dtls spd ON sc.schoolid = spd.school_id WHERE sc.domain_identifier = '".$subdomainname."'";
			$res2 = mysql_query($query2);
			if($res2)
			{
				$row2 = mysql_fetch_assoc($res2);
				$tquery = "select count(sms_type) as t_count from sms_transaction_log where sms_type='T'";
			
				$tres = mysql_query($tquery,$link);
			
				if($tres)
					$trow = mysql_fetch_assoc($tres);
			
				$_SESSION['sms_t_count']= $row2['sms_t_count'] - $trow['t_count'];
			}
				
			if($_SESSION['sms_t_count']>0){
				if($numrows>0)
				{
					/**
					 * This if block indicates that the module has auto sms sending setting. Hence we will call the functions defined in the SmsSystem.class.php file.
					 */
					
					$hashvalues = array($stu_fname,$newformid);
					$message = $smssend->getSmsMessage($getresult['template_format'],$hashvalues);
					$mobile = $stu_mob;
					$send_to = 'MEMBER';
					
						$mobile = $mob;
											
					$sendMessage = $smssend->sendTransactionalSMS($getresult['template_id'],$mobile,$message,trim($sms_sender_id));
					$logInsert = $smssend->insertLog($sendMessage,$stu_fname,trim($message),$mobile,$send_to,'T');
				}
			
			
	}
			$query_local1 = "select * from notification_setting where module_id = ".ONLINE_APPLICATION." and  notification_type='E' and sending_type='A' " ; // module_id = '1' is online application
			$res_local1 = mysql_query($query_local1,$link);
			$num_local1 = mysql_num_rows($res_local1);
			
			
	if($num_local1>0)
	{
		$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id  AND  e.module_name = 'Online Application' and available_for_school='Y' ";
		$sql_t = mysql_query($query_t) or die('Error, Query failed.');
		$nums = mysql_num_rows($sql_t);
		$gettemp = mysql_fetch_assoc($sql_t);
		$subject="Online Application";
		$path = "logo.png";
		$template = $gettemp['email_temp_format'];
			
		if(@$nums>0 && $email!="")
		{
			$hashvalue = array($firstname);
	
			$temp_value = getEmailMessage($template,$hashvalue);
			$sendEmail = Sending_EMail($temp_value,$email,$subject,$path);
		}
		
	}//$num_local1
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
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    	
    	<script type="text/javascript" src="js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="js/Ujquery-ui.min.js"></script>
<link rel="Stylesheet" type="text/css" href="css/jquery-ui.css" />
<script language="javascript">

function chkME(frm)
{
if(frm.firstname.value==""){
	alert("Please Enter First Name");
	frm.firstname.focus();
	return false;
}
if(document.frm.lastname.value==""){
	alert("Please Enter Last Name");
	document.frm.lastname.focus();
	return false;
}
if(document.frm.sex.value=="0"){
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

if(document.frm.pin.value!=""){
var pinlen=document.frm.pin.value.length;
if(pinlen<6){
	alert("pincode must be 6 digits");
	document.frm.pin.focus();
	return false;
}
}
if(document.frm.offtelno.value!=""){

var offtelno=document.frm.offtelno.value.length;
if(offtelno<10 || offtelno>15){
	alert("office tel number must be 10-15 digits");
	document.frm.offtelno.focus();
	return false;
} }
if(document.frm.restelno.value!=""){

	var restelno=document.frm.restelno.value.length;
	if(restelno<10 || restelno>15){
		alert("residential telephone number must be 10-15 digits");
		document.frm.restelno.focus();
		return false;
	} }
if(document.frm.mobileno.value==""){
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


return true;
}
$(document).ready(function(){
	$('#mobileno,#pin,#offtelno,#restelno,#height,#weight,#clinicno,#mobileclinic,#contactpersontelno').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9]/g))
		{
			this.value = this.value.replace(/[^0-9]/g,'');
		}
	});

	$('#firstname,#middlename,#lastname,#city,#physicianname,#contactpersonname').keyup(function(){
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
		      <form class="sky-form" id="frm" action="register.php" name="frm" onsubmit="return chkME(this);"  method="POST">
                            <header>Register</header>
                            
                            <fieldset>          
								<?php echo @$message;?>							
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">First Name</label>
                                        <div class="col col-2">
                                            <label class="input">
                                                
                                                <input type="text" name="firstname" id="firstname">
												<b class="tooltip tooltip-bottom-right">Needed to enter firstname</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Middle Name</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="middlename" id="middlename">
												<b class="tooltip tooltip-bottom-right">Needed to enter middlename</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Last Name</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="lastname" id="lastname">
												<b class="tooltip tooltip-bottom-right">Needed to enter lastname</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">Address</label>
                                        <div class="col col-10">
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
                                                
                                                <input type="text" name="city" id="city" >
												<b class="tooltip tooltip-bottom-right">Needed to enter city</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Pin</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="pin" id="pin">
												<b class="tooltip tooltip-bottom-right">Needed to enter pin</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Office Tel. No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="offtelno" id="offtelno">
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
                                                
                                                <input type="text" name="restelno" id="restelno">
												<b class="tooltip tooltip-bottom-right">Needed to enter Res. Tel. No.</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Mobile No.</label>
                                        <div class="col col-2">
                                            <label class="input">
                                               
                                                <input type="text" name="mobileno" id="mobileno">
												<b class="tooltip tooltip-bottom-right">Needed to enter mobile no.</b>
                                            </label>
                                        </div>
										<label class="label col col-2">Sex</label>
                                        <div class="col col-2">
                                            <label class="select">
                                               
                                                <select name="sex" id="sex">
                                                <option value="MALE">MALE</option>
                                                <option value="FEMALE">FEMALE</option>
                                                </select>
												<b class="tooltip tooltip-bottom-right">Needed to select sex</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                        <label class="label col col-2">Email</label>
                                        <div class="col col-6">
                                            <label class="input">
                                                
                                                <input type="text" name="email" id="email" />
												<b class="tooltip tooltip-bottom-right">Needed to enter email</b>
                                            </label>
                                        </div>
                                        </div>
                                        </section>
                                        <section>
                                    <div class="row">
										<label class="label col col-2">Date of Birth</label>
                                        <div class="col col-2">
                                            <label class="select">
                                               
                                            <select name="month1"> 
					<option value=0 selected>Month</option>
					<option value="01" <?php echo @$mm=="01"?" selected":"";?> >Jan</option>
					<option value="02" <?php echo @$mm=="02"?" selected":"";?>>Feb</option>
					<option value="03" <?php echo @$mm=="03"?" selected":"";?>>Mar</option>
					<option value="04" <?php echo @$mm=="04"?" selected":"";?>>Apr</option>
					<option value="05" <?php echo @$mm=="05"?" selected":"";?>>May</option>
					<option value="06" <?php echo @$mm=="06"?" selected":"";?>>Jun</option>
					<option value="07" <?php echo @$mm=="07"?" selected":"";?>>Jul</option>
					<option value="08" <?php echo @$mm=="08"?" selected":"";?>>Aug</option>
					<option value="09" <?php echo @$mm=="09"?" selected":"";?>>Sep</option>
					<option value="10" <?php echo @$mm=="10"?" selected":"";?>>Oct</option>
					<option value="11" <?php echo @$mm=="11"?" selected":"";?>>Nov</option>
					<option value="12" <?php echo @$mm=="12"?" selected":"";?>>Dec</option>
				</select>
				</label>
				</div>
				  <div class="col col-2">
                                            <label class="select">
					<select name="day1">
					<option value=0>Day</option>
						<?php 
						  for($i=1;$i<=31;$i++)
							{
							 if($i<10)
								$day="0".$i;
								else
									$day=$i;
									
							
							echo"<option value=".$day;
							   if($i==@$dd) echo " selected";
							   echo ">";
							  echo $day;
							   echo"</option>";
							   }
							   ?>
						  </select>
						  </label>
						  </div>
						    <div class="col col-2">
                                            <label class="select">
						  
						  <select name="year1"> 
							 <option value="0" selected>Year</option> 
							 <?php 
						  for($i=1970;$i<=(date('Y')-2);$i++)
							{echo"<option value='".$i."'";
							   if($i==@$yy) echo " selected";
							   echo ">".$i."</option>";
							   }
							   ?>  </select>
								</label>
                                        </div>
										
                                       
                                    </div>
                                </section>
                                    <section>
                                    <div class="row">
                                        <label class="label col col-2">Anniversary</label>
                                         <div class="col col-2">
                                            <label class="select">
                                               
                                            <select name="month2"> 
					<option value=0 selected>Month</option>
					<option value="01" <?php echo @$mm=="01"?" selected":"";?> >Jan</option>
					<option value="02" <?php echo @$mm=="02"?" selected":"";?>>Feb</option>
					<option value="03" <?php echo @$mm=="03"?" selected":"";?>>Mar</option>
					<option value="04" <?php echo @$mm=="04"?" selected":"";?>>Apr</option>
					<option value="05" <?php echo @$mm=="05"?" selected":"";?>>May</option>
					<option value="06" <?php echo @$mm=="06"?" selected":"";?>>Jun</option>
					<option value="07" <?php echo @$mm=="07"?" selected":"";?>>Jul</option>
					<option value="08" <?php echo @$mm=="08"?" selected":"";?>>Aug</option>
					<option value="09" <?php echo @$mm=="09"?" selected":"";?>>Sep</option>
					<option value="10" <?php echo @$mm=="10"?" selected":"";?>>Oct</option>
					<option value="11" <?php echo @$mm=="11"?" selected":"";?>>Nov</option>
					<option value="12" <?php echo @$mm=="12"?" selected":"";?>>Dec</option>
				</select>
				</label>
				</div>
				  <div class="col col-2">
                                            <label class="select">
					<select name="day2">
					<option value=0>Day</option>
						<?php 
						  for($i=1;$i<=31;$i++)
							{
							 if($i<10)
								$day="0".$i;
								else
									$day=$i;
									
							
							echo"<option value=".$day;
							   if($i==@$dd) echo " selected";
							   echo ">";
							  echo $day;
							   echo"</option>";
							   }
							   ?>
						  </select>
						  </label>
						  </div>
						    <div class="col col-2">
                                            <label class="select">
						  
						  <select name="year2"> 
							 <option value="0" selected>Year</option> 
							 <?php 
						  for($i=1970;$i<=(date('Y')-2);$i++)
							{echo"<option value='".$i."'";
							   if($i==@$yy) echo " selected";
							   echo ">".$i."</option>";
							   }
							   ?>  </select>
                                        </div>
                                        </section>
                                        <section>
                                        <div class="row">
										<label class="label col col-2">Occupation</label>
                                        <div class="col col-6">
                                            <label class="input">
                                               
                                                <input type="text" name="occupation">
												<b class="tooltip tooltip-bottom-right">Needed to enter Occupation</b>
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
										
                                       
                                    </div>
                                </section>
                                             <section>
                                    <div class="row">
                                        <label class="label col col-7">Are you currently involved in any physical activity?</label><input type="radio" name="physicalactivity" value="YES">YES <input type="radio" name="physicalactivity" value="NO">NO
                                                               
                                    </div>
                                </section>
                                                 <section>
                                    <div class="row">
                                        <label class="label col col-10" style="font-weight: bold;">HEALTH FORM</label>
                                    
										
                                       
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                        <label class="label col col-10">Are you presently taking any medications? If yes then please list</label>
                                        		
                                       
                                    </div>
                                </section>
                          
                                     <section>
                                    <div class="row">
                                         <div class="col col-11">
                                            <label class="input">
                                                
                                                <input type="text" name="presentmedications">
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter present medications</b>
                                            </label>
                                        		
                                       </div>
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                        <label class="label col col-10">Are you presently pregnant in last three months?</label>
                                        		
                                       
                                    </div>
                                </section>
                               
								 <section>
                                    <div class="row">
                                         <div class="col col-11">
                                            <label class="input">
                                                
                                                <input type="text" name="presentlypregnant">
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter presently pregnant </b>
                                            </label>
                                        		
                                       </div>
                                    </div>
                                </section>
                                   <section>
                                    <div class="row">
                                        <label class="label col col-7">Does your physician know you are participating in exercise program?</label>
                                        		   <input type="radio" name="physician" value="YES">YES
                                       <input type="radio" name="physician" value="NO">NO
                                    </div>
                                </section>
                                       <section>
                                    <div class="row">
                                         <div class="col col-2">
                                            <label class="input">
                                                
                                             
                                                
                                               
										
                                            </label>
                                        	</div>	
                                         <div class="col col-2">
                                            <label class="input">
                                                
                                              
                                                
                                               
											
                                            </label>
                                        	</div>	
                                    </div>
                                </section>
                                  <section>
                                    <div class="row">
                                        <label class="label col col-10">What physical activity are presently doing?</label>
                                        		
                                       
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                     <div class="col col-11">
                                            <label class="input">
                                                
                                              
                                                <input type="text" name="presentphysicalactivity" >
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter physical activity </b>
                                            </label>
                                        	</div>	
                                        		
                                       
                                    </div>
                                </section>
                                      <section>
                                    <div class="row">
                                          <label class="label col col-2">Physician Name</label>
                                         <div class="col col-9">
                                            <label class="input">
                                                
                                              
                                                <input type="text" name="physicianname" id="physicianname"> 
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter physician name </b>
                                            </label>
                                        	</div>	
                                    </div>
                                </section>
                                                <section>
                                    <div class="row">
                                          <label class="label col col-2">Clinic No</label>
                                         <div class="col col-2">
                                            <label class="input">
                                                
                                              
                                                <input type="text" name="clinicno" id="clinicno">
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter clinic no. </b>
                                            </label>
                                        	</div>	
                                        	   <label class="label col col-2">Mobile No</label>
                                        	 <div class="col col-2">
                                            <label class="input">
                                                
                                              
                                                <input type="text" name="mobileclinic" id="mobileclinic">
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter clinic mobile no. </b>
                                            </label>
                                        	</div>	
                                    </div>
                                </section>
                                                  <section>
                                    <div class="row">
                                          <label class="label col col-10">Person/s to contact in case of emergency?</label>
                                         
                                    </div>
                                </section>
                                                <section>
                                    <div class="row">
                                          <label class="label col col-2">1. Name</label>
                                         <div class="col col-2">
                                            <label class="input">
                                                
                                              
                                                <input type="text" name="contactpersonname" id="contactpersonname">
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter contact person name  </b>
                                            </label>
                                        	</div>	
                                        	   <label class="label col col-2">Tel. No.</label>
                                        	 <div class="col col-2">
                                            <label class="input">
                                                
                                              
                                                <input type="text" name="contactpersontelno" id="contactpersontelno">
                                               
												<b class="tooltip tooltip-bottom-right">Needed to enter clinic contact person tel. no. </b>
                                            </label>
                                        	</div>	
                                    </div>
                                </section>
                                <section>
                                    <div class="row">
                                          <label class="label col col-10">Health History Checklist</label>
                                           </div></section>
                                         <section>
                                           <div class="row">
                                         	<input type="checkbox" name="check[]" value='Muscle'> Muscle joint or back disorder that could be aggravated by physical activity<br>
                                         	<input type="checkbox" name="check[]" value="History_of_heart"> History of heart problem in immediate family <br>
                                         	<input type="checkbox" name="check[]" value="Recent_surgery_history"> Recent surgery in last three immediate family<br>
                                         	<input type="checkbox" name="check[]" value="Recent_surgery_last_three_month"> Recent Surgery in last three months <br>
                                         	<input type="checkbox" name="check[]" value="High_cholesterol_level"> High cholesterol level <br>
                                         	<input type="checkbox" name="check[]" value="High_trigyceride_level"> High trigyceride level<br>
                                         	<input type="checkbox" name="check[]" value="Heart_Disease"> Heart Disease <br>
                                         	<input type="checkbox" name="check[]" value="High_Blood_Pressure"> High Blood Pressure<br>
                                         	<input type="checkbox" name="check[]" value="Chest_Pain"> Chest Pain <br>
                                         	<input type="checkbox" name="check[]" value="Stroke"> Stroke<br>
                                         	<input type="checkbox" name="check[]" value="Irregular_Heartbeats"> Irregular Heartbeats<br>
                                         	<input type="checkbox" name="check[]" value="Shortness_of_Breath"> Shortness of Breath<br>
                                         	<input type="checkbox" name="check[]" value="Lung_Problem"> Lung Problem <br>
                                         	<input type="checkbox" name="check[]" value="Asthma"> Asthma<br>
                                         	<input type="checkbox" name="check[]" value="Allergies_Depression"> Allergies Depression<br>
                                         	<input type="checkbox" name="check[]" value="Dizziness"> Dizziness<br>
                                         	<input type="checkbox" name="check[]" value="Fainting_Spells"> Fainting Spells<br>
                                         	<input type="checkbox" name="check[]" value="Severe_headaches"> Severe headaches<br>
                                         	<input type="checkbox" name="check[]" value="Seizures_or_Convulsion"> Seizures or Convulsion<br>
                                         	<input type="checkbox" name="check[]" value="Numbness_or_tingling"> Numbness or tingling<br>
                                         	<input type="checkbox" name="check[]" value="Anemia"> Anemia<br>
                                         	<input type="checkbox" name="check[]" value="Anxiety"> Anxiety<br>
                                         	<input type="checkbox" name="check[]" value="Use_of_Laxatives_or_diuretics"> Use of Laxatives or diuretics<br>
                                         	<input type="checkbox" name="check[]" value="Alcoholism_substance"> Alcoholism substance<br>
                                         	
                                         	
                                       
                                    </div>
                                </section>
                                               
                                
                                                  <section>
                                    <div class="row">
                                          <label class="label col col-10">Rules and Regulations</label>
                                          	
                                    </div>
                                    <div class="row">
                                          <label class="label col col-11">We reserve the right to admission.</label>
                                          	 <label class="label col col-11">Membership fees are Non-refundable.</label>
                                          	 <label class="label col col-11">The rates for facility may changed at the sole discretion of the management without prior notice.</label>
                                          	 <label class="label col col-11">The Management is not responsible for any loss ot theft of member's belongings.</label>
                                          	 <label class="label col col-11">We advice members not to carry valuables to the gym.</label>
                                          	 <label class="label col col-11">Smoking Chewing of Beetle Leaf is strictly prohibited in the gym promises.</label>
                                          	 <label class="label col col-11">Courteous member conduct is very important in order to provide everyone with pleasant workout experience poor conduct includes,but is not limited to use profanity,bad mouthing others and fighting If a members is found treating anyone disrespectfully, their membership  will be terminated without refund and that member waives their right to recourse.</label>
                                          	 <label class="label col col-11">Sports wear is compulsory in the gym.</label>
                                          	 <label class="label col col-11">Members must carry a separate pair of trainnig shoes to be changed into at the gym premises to help maintain hygiene.</label>
                                          	 <label class="label col col-11">The Management will maintain notice board for intimating the members of any future changes. The Management expects the members to read it on regular basis. We asssume the members read the notice. </abel>
                                          	 <label class="label col col-11">We request members to use a deodorant spary.</label>
                                          	 <label class="label col col-11">Taxes as applicable.</label>
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