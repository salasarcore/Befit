<?php 
@session_start();
include("../conn.php");
//include('../check_session.php');
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/functions.php");
include("../functions/common.php");
include("../classes/class.sample_image.php");
include('../../email_settings.php');
require '../sms/SmsSystem.class.php';
$smssend = new SMS(); //create object instance of SMS class
$act=makeSafe(@$_GET['act']);
$empID=makeSafe(@$_GET['empID']);
$action=makeSafe(@$_POST['action']);
?>
<script type="text/javascript"   src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<?php
include('../php/js_css_common.php');
function watermarkImage_passport ($SourceFile, $DestinationFile, $ext) {

	
	//$SourceFile is source of the image file to be watermarked
	//$WaterMarkText is the text of the watermark
	//$DestinationFile is the destination location where the watermarked images will be placed

	$WaterMarkText = 'SalasarEdu';
	//Delete if destinaton file already exists
	@unlink($DestinationFile);
	$image = new SimpleImage();
	$image->load($SourceFile);
	
	$width='130';
    $height='140';

	$image->resize($width,$height,'crop');
	$image->save($SourceFile);
	


	//This is the vertical center of the image
	 $top = getimagesize($SourceFile);
	
	$top = 128;
	$image_p = imagecreatetruecolor($width, $height);
	
	if($ext=='jpg' || $ext=='jpeg') $image = imagecreatefromjpeg($SourceFile);
	if($ext=='png' || $ext=='PNG') $image = imagecreatefromjpeg($SourceFile);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);

	//Path to the font file on the server. Do not miss to upload the font file
	$font = '../fonts/ARIAL.TTF';

	//Font sie
	$font_size = 11;

	//Give a white shadow
	$white = imagecolorallocate($image_p, 255, 255, 255);
	imagettftext($image_p, $font_size, 0, 30, $top, $white, $font, $WaterMarkText);

	//Print in black color
	$black = imagecolorallocate($image_p, 0, 0, 0);
	imagettftext($image_p, $font_size, 0, 28, $top-1, $black, $font, $WaterMarkText);

	if ($DestinationFile<>'') {

		imagejpeg ($image_p, $DestinationFile, 100);

	} else {

		header('Content-Type: image/jpeg');

		imagejpeg($image_p, null, 100);

	};

	imagedestroy($image);

	imagedestroy($image_p);

};
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>EMPLOYEE</title>
<script type="text/javascript" src="../../js/ajax.js"></script>
<script type="text/javascript" src="../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<link href="../css/classic.css" rel="stylesheet" type="text/css">

<script>
function chkMe()
{
if(trim(document.frm.full_name.value)==""){
	alert("Please Enter Your Full Name");
	$('#full_name').focus();
 return false;
 }
 
var fullname=document.frm.full_name.value.length;
if(fullname < 2){
	alert("Full Name Should Be Atleast 2 characters");
	document.frm.full_name.focus();
	return false;
} 

if(document.frm.designation.selectedIndex==0){
	alert("Please Select Designation");
	$('#designation').focus();
	 return false;
 }
 
if(document.frm.empdepartment.selectedIndex==0){
	alert("Please Select Department");
	$('#empdepartment').focus();
	 return false;
 }

if(trim(document.frm.emp_id.value)==""){
	alert("Please Enter Your Employee ID");
	$('#emp_id').focus();
 return false;
 }
 
 if(document.frm.sex.selectedIndex==0){
	alert("Please Select Gender");
	$('#sex').focus();
 return false;
 }
 
 if(document.frm.mstat.selectedIndex==0){
	alert("Please Select Marital Status");
	$('#mstat').focus();
 return false;
 }

 if( $('#doj').val()==""){
	 alert("Please Enter Date of Joining");
	 $('#doj').focus();
	  return false;
 }

if(document.frm.branches.selectedIndex==0){
	alert("Please Select Place Of Posting");
	$('#branches').focus();
 return false;
 }
if( $('#dob').val()==""){
	 alert("Please Enter Date of Birth");
	 $('#dob').focus();
	  return false;
 }	
	  
 if(trim(document.frm.hq.value)==""){
	alert("Please Enter Your Highest qualification");
	$('#hq').focus();
 return false;
 }
 
 if(trim(document.frm.permanent_address.value)==""){
	alert("Please Enter Your Permanent address");
	$('#permanent_address').focus();
 return false;
 }
 
 if(trim(document.frm.present_address.value)==""){
	alert("Please Enter Your Present address");
	$('#present_address').focus();
 return false;
 }
 
 if(trim(document.frm.ecn.value)==""){
	alert("Please Enter Emergency Contact Number");
	$('#ecn').focus();
 return false;
 }
 
 if(trim(document.frm.mob.value)==""){
	alert("Please Enter Mobile");
	$('#mob').focus();
 return false;
 }
 
 if(document.frm.pan.value!="")
{
 var pancard=document.frm.pan.value.length;
 if(pancard < 10)
{
 	alert("Pan Card number must be 10 digits.");
 	document.frm.pan.focus();
 	return false;
 } 
 }

 if(document.frm.father_no.value!=""){
 var  fathernumber=document.frm.father_no.value.length;
 if(fathernumber < 10){
 	alert("Father Mobile number must be 10 digits.");
 	document.frm.father_no.focus();
 	return false;
 } } 

 if(document.frm.husband_no.value!=""){
	 var husbandno=document.frm.husband_no.value.length;
	 if(husbandno < 10){
	 	alert("Spouse Mobile number must be 10 digits.");
	 	document.frm.husband_no.focus();
	 	return false;
	 } }


 if(document.frm.mob.value!=""){
	 var mobno=document.frm.mob.value.length;
	 if(mobno < 10){
	 	alert("Mobile number must be 10 digits.");
	 	document.frm.mob.focus();
	 	return false;
	 } }
 
 if(trim(document.frm.email.value)==""){
	alert("Please Enter Your Email ID");
	$('#email').focus();
 return false;
 }
 
 if(trim(document.frm.password.value)==""){
	alert("Please Enter Password");
	$('#password').focus();
 return false;
 }

 if(trim(document.frm.file.value)!="")
 {
	 return validateattachfile(document.frm.file.value);
 }
 if(trim(document.frm.payment_type.value)==""){
  alert("Please Select Payment Type");
  $('#payment_type').focus();
  return false;
  }
 return true;
}

</script>

<script>

function validateEmail(email) 
{ 
	var emailReg = <?php echo REGEX_EMAIL;?>;
	  if( !emailReg.test( email ) ) {
	    return false;
	  } else {
	    return true; 
	  }
}
function validateattachfile(filename)
{

	var validationStatus = true;
	var parts = filename.split('.');
	
	var exts = ['jpg', 'jpeg', 'png', 'gif'];
     if(exts.indexOf(parts[parts.length-1]) == -1) 
       {
    	 alert('Only JPG, JPEG, PNG and GIF files are allowed');
         validationStatus = false;
        }

     return validationStatus;

}

$(document).ready(function(){


	$('#full_name,#father_name,#mother_name,').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^a-zA-Z ]/g))
		{
			this.value = this.value.replace(/[^a-zA-Z ]/g,'');
		}
				
	});	
	$('#hq').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^a-zA-Z .]/g))
		{
			this.value = this.value.replace(/[^a-zA-Z .]/g,'');
		}
				
	});			
	$('#epic').blur( function() {
			    if ( $(this).val()!="" && ! $(this).val().match('^[a-zA-Z]{3}[0-9]{7}$'))
			    {
				    	alert(" Invalid  Voter Card Number");
				    	$('#epic').val('');
				    	$('#epic').focus();
			   }
	 });


	$('#emp_id,#password').keyup(function(){
		 if(this.value.match(/[\s]/g)){
	  	  		this.value=this.value.replace(/[\s]/g,'');  	  		
	  		}
	});

	$('#email').blur(function(){
		var email = $('#email').val();
		if(email!='')
		{
			if(!validateEmail(email))
			{
				alert('Please Enter Valid Email ID');
				$('#email').focus();
			}
		}
	});

	$('#home_phone,#ecn,#mob,#husband_no,#father_no').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9]/g))
		{
			this.value = this.value.replace(/[^0-9]/g,'');
		}
	});

	$('#sameadd').change(function()
			    {
		 if ($('#sameadd').is(':checked')) {
			
			 $("#permanent_address").val($("#present_address").val());
			   
			} else {
				$("#permanent_address").val('');
			} 
			     
			    });
	
});
</script>

</head>
<body>
<?php 
$msg="";
$br_id=0;
$designation_id=0;
$department_id=0;
if(@$action=="SAVE" || @$action=="UPDATE")
{
@include_once('../classes/class.sample_image.php');

$full_name=makeSafe($_POST['full_name']);
$designation_id=makeSafe($_POST['designation']);
$department_id=makeSafe($_POST['empdepartment']);
$emp_id=makeSafe($_POST['emp_id']);
$doj=makeSafe($_POST['doj']);
$br_id=makeSafe($_POST['branches']);
$hq=makeSafe($_POST['hq']);
$sex=makeSafe($_POST['sex']);
$present_address=makeSafe($_POST['present_address']);
$permanent_address=makeSafe($_POST['permanent_address']);
$ecn=makeSafe($_POST['ecn']);
$mob=makeSafe($_POST['mob']);
$epic=makeSafe($_POST['epic']);
$pan=makeSafe($_POST['pan']);
$email=makeSafe($_POST['email']);
$mstat=makeSafe($_POST['mstat']);
$password=makeSafe($_POST['password']);
$dob=makeSafe($_POST['dob']);
$adt=makeSafe($_POST['adt']);
$father_name=makeSafe($_POST['father_name']);
$mother_name=makeSafe($_POST['mother_name']);
$wife_husband=makeSafe($_POST['wife_husband']);
$husband_no=makeSafe($_POST['husband_no']);
$father_no=makeSafe($_POST['father_no']);
$home_phone=makeSafe($_POST['home_phone']);
$payment_type=makeSafe($_POST['payment_type']); 
@$super=makeSafe($_POST['superior']);
if($super=="S")  @$superior="S";
else  @$superior="E";
$check="";


if ($_FILES["file"]["error"] > 0)
{	$mime="";}
else
{
	$extn=explode('.',$_FILES["file"]["name"]);
	$mime=$extn[1];
	if($mime!='jpg' && $mime!='png' && $mime!='jpeg' && $mime!='gif')
	{
		$check="<div class='error'>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
	}
} 

/**
 * Below code checks validations.
 */
if($check==""){
if($full_name=='')
		$check= "<div class='error'>Please Enter Your Full Name</div>";
elseif($designation_id=='0')
		$check= "<div class='error'>Please Select Desgination</div>";
elseif($department_id=='0')
		$check= "<div class='error'>Please Select Department</div>";
elseif($emp_id=='')
		$check= "<div class='error'>Please Enter Your Employee ID</div>";
elseif($sex=='0')
		$check= "<div class='error'>Please Enter Your Gender</div>";
elseif($mstat=='')
		$check= "<div class='error'>Please select Martial Status</div>";
elseif($doj=='')
		$check= "<div class='error'>Please Enter Your Date Of Joining</div>";
elseif($br_id=='0')
		$check= "<div class='error'>Please Select Branch</div>";
elseif($dob=='')
		$check= "<div class='error'>Please Enter Your Date Of Birth</div>";
elseif($hq=='')
		$check= "<div class='error'>Please Select Place Of Posting</div>";
elseif($permanent_address=='')
		$check= "<div class='error'>Please Enter Your Permanent Address</div>";
elseif($present_address=='')
		$check= "<div class='error'>Please Enter Your Present Address</div>";
elseif($ecn=='')
		$check= "<div class='error'>Please Enter Your Emergency Contact No</div>";
elseif($mob=='')
		$check= "<div class='error'>Please Enter Your Mobile No</div>";

elseif($email=='')
		$check= "<div class='error'>Please Enter Your Email ID</div>";
elseif($password=='')
		$check= "<div class='error'>Please Enter Your Password</div>";
elseif($payment_type=='')
$check= "<div class='error'>Please Select Payment Type</div>";
elseif(!is_numeric($mob))
		$check= "<div class='error'>Please Enter Mobile Number as Numbers only </div>";
elseif(!is_numeric($ecn))
		$check= "<div class='error'>Please Enter Emergency Number as Numbers only </div>";

elseif($husband_no!="")
	{
		if(!is_numeric($husband_no))
		{
			$check= "<div class='error'>Please Enter Spouse Number as Numbers only </div>";
		}
		
		if(strlen($husband_no) < 10)
		{
			$check= "<div class='error'>Please Enter Spouse Number 10 digits only </div>";
		}
	}
elseif($father_no!="")
		{
			if(!is_numeric($father_no))
			{
				$check= "<div class='error'>Please Enter Father Number as Numbers only </div>";
			}
			if(strlen($father_no) < 10)
			{
				$check= "<div class='error'>Please Enter Father Number 10 digits only </div>";
			}
		}
else if($home_phone!="")
			{
				if(!is_numeric($home_phone))
					$check= "<div class='error'>Please Enter Home Number as Numbers only</div>";
				
				if(strlen($home_phone) < 10)
				{
					$check= "<div class='error'>Please Enter Home Number 10 digits only </div>";
				}
				
			}
elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$check= "<div class .</div>";
				}
				

 if($action=="SAVE")
	{
	    	$query   = "SELECT empid as mempid from employee where emp_id='".trim($emp_id)."'";
		    $result  = mysql_query($query) or die('Error, query failed');
		
		    $query_sup   = "SELECT department_id, superior from employee where superior = 'S' AND  department_id='".trim($department_id)."'";
		    $result_sup  = mysql_query($query_sup) or die('Error, query failed');
		     $n = mysql_num_rows($result_sup);
		    
		
		if(mysql_num_rows($result)>0)
		$msg="<DIV class='error'>Duplicate Employee ID</DIV>";
			
		else if(($superior=='S') AND ($n >= 1))
		$msg="<DIV class='error'>There can not be more than 1 HEAD IN the same department</DIV>";
		
		else
		{
			if($check!='')
			{
				$msg=$check;
			}
			  else 
			  {
			  	$mempid=getNextMaxId('employee','empid');
			  	$mempid=$mempid+1;
			  	
				$sql="insert into employee(empid,emp_id,department_id, br_id,designation_id,emp_name, sex, emp_qualification, emp_doj, emp_dob, emp_doa, emp_addr_pre, emp_addr_per, emp_ecn, emp_mob, emp_epic, emp_pan, mstat, email, password,  mime,activated, father_name,mother_name,wife_husband,husband_no,father_no,home_phone,payment_type,updated_by,superior)";
			 	$sql .=" values('".$mempid."','".trim($emp_id)."',".trim($department_id).",".trim($br_id).",".$designation_id.",'".trim($full_name)."','".trim($sex)."','".trim($hq)."','".trim($doj)."','".trim($dob)."','".trim($adt)."','".trim($present_address)."','".trim($permanent_address)."','".trim($ecn)."','".trim($mob)."','".trim($epic)."','".trim($pan)."','".trim($mstat)."','".trim($email)."','".trim($password)."','".trim($mime)."','N','".$father_name."','".$mother_name."','".$wife_husband."','".$husband_no."','".$father_no."','".$home_phone."','".$payment_type."', '".$superior."','".$_SESSION['emp_name']."')";
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					if(mysql_affected_rows($link)>0)
					{		
						if ($_FILES["file"]["error"] > 0)
						{}
						else
						{
							$extn=explode('.',$_FILES["file"]["name"]);
								
							$upath="../../site_img/emppic/".base64_encode($mempid).".".$extn[1];

							$SourceFile = $_FILES["file"]["tmp_name"];
							watermarkImage_passport ($SourceFile, $upath,$extn[1]); }
							// email query
							$query_local = "select * from notification_setting where module_id = ".EMPLOYEE_REG." and  notification_type='E' and sending_type='A' " ; // module_id = 16 for Employee Registration
							$res_local = mysql_query($query_local,$link);
							$num_local = mysql_num_rows($res_local);
							if($num_local>0) {
									
								$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_name = 'Employee Registration'";
								$sql_t = mysql_query($query_t) or die('Error. Query failed.');
								$nums = mysql_num_rows($sql_t);
								$gettemp = mysql_fetch_assoc($sql_t);
								$template = $gettemp['email_temp_format'];
								if($nums>0) {
									$schoolname = "Befit Ladies Gym";
									$url = "http://localhost/admin";
									$subject="Employee Registation";
									$path="../../logo.png";
										
									$hashvalue = array($full_name,$schoolname,$url,$emp_id,$password);
									$temp_value = getEmailMessage($template,$hashvalue);
									$sendEmail = Sending_EMail($temp_value,$email,$subject,$path); } }
									
									//sms query
									$querysetting="select * from notification_setting where module_id=".EMPLOYEE_REG." and notification_type='S' and sending_type='A'";
									$resquerysetting=mysql_query($querysetting,$link);
									$numrows1 = mysql_num_rows($resquerysetting);
									if($numrows1>0)
									{
										$query = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g  WHERE e.module_id=g.module_id  AND  e.module_name='Employee Registration' and available_for_school='Y' ";
										$sql = mysql_query($query);
										$numrows = mysql_num_rows($sql);
										$getresult = mysql_fetch_assoc($sql);
										if($_SESSION['sms_t_count'] > 0 && $numrows > 0)
										{
											$send_to='EMPLOYEE';
											$hashvalues = array($full_name,$emp_id,$password);
											$message = $smssend->getSmsMessage($getresult['template_format'],$hashvalues);
											$sendMessage = $smssend->sendTransactionalSMS($getresult['template_id'],$mob,$message,trim($sms_sender_id));
											$logInsert = $smssend->insertLog($sendMessage,$full_name,trim($message),$mob,$send_to,'T');
										}
									}
									
									$msg= "<div class='success'>Employee Registration completed</div>";
						
						}
						
						
						if($action=="SAVE")
							$act=="add";
							else
							$act=="edit";
				}
		  }

		}
	}
	
	if($action=="UPDATE")
	{		
		if($check!='')
		{
			$msg=$check;
		}
		else
		{
			$query   = "SELECT empid as mempid from employee where emp_id='".trim($emp_id)."' and empid!=".$empID;
			$result  = mysql_query($query) or die('Error, query failed');
			
			$query_sup = "SELECT department_id, superior from employee where superior = 'S'  AND department_id='".trim($department_id)."' and empid!=".$empID ;
			$result_sup  = mysql_query($query_sup) or die('Error, query failed');
			 $n = mysql_num_rows($result_sup);
					
		    if(mysql_num_rows($result)>0)
			$msg="<div class='error'>Duplicate employee id</DIV>";
			
			else if(($superior=='S') AND ($n >= 1))
				$msg="<DIV class='error'>There can not be more than 1 HEAD of in the same department</DIV>";
			else
			{
				$sql="update employee set emp_id='".trim($emp_id)."',department_id='".trim($department_id)."', br_id=".trim($br_id).",designation_id=".trim($designation_id).",emp_name='".trim($full_name)."',";
				$sql .="sex='".trim($sex)."', emp_qualification='".trim($hq)."', emp_doj='".trim($doj)."', emp_dob='".trim($dob)."', emp_doa='".trim($adt)."',"; 
				$sql .="emp_addr_pre='".trim($present_address)."', emp_addr_per='".trim($permanent_address)."', emp_ecn='".trim($ecn)."', emp_mob='".trim($mob)."', ";
				$sql .="emp_epic='".trim($epic)."', emp_pan='".trim($pan)."', mstat='".trim($mstat)."', email='".trim($email)."', password='".trim($password)."',  updated_by='".$_SESSION['emp_name']."'";
				$sql .=",father_name='".trim($father_name)."', mother_name='".trim($mother_name)."', wife_husband='".trim($wife_husband)."', husband_no='".trim($husband_no)."', father_no='".trim($father_no)."',  home_phone='".$home_phone."',  payment_type='".$payment_type."' , superior='".$superior."' ";
				$sql .=" where empid=".$empID;
				
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused ");
					if(mysql_affected_rows($link)==0)
						$msg="<div class='success'>No Data Changed</div>";
					if(mysql_affected_rows($link)>0)
						$msg="<div class='success'>Record Updated successfully</div>";
					if(mysql_affected_rows($link)<0)
						$msg="<div class='error'>Record Not Updated successfully</div>";
					if ($_FILES["file"]["error"] > 0)
						{}
						else
						{
							$extn=explode('.',$_FILES["file"]["name"]);
							$mime=$extn[1];
							$upath="../../site_img/emppic/".base64_encode($empID).".".$extn[1];
						    $SourceFile = $_FILES["file"]["tmp_name"];
						   
							watermarkImage_passport ($SourceFile, $upath,$mime);
							
							$sql="update employee set mime='".trim($mime)."' where empid=".$empID;
							$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
							$msg= "<div class='success'>Data updated successfully</div>";
						}
	
			}
		}
	
	
}//update
}//save-update
/*if(@$action=="DELETE")
{
	$query="select * from employee where empid=".$empID;
	$result  = mysql_query($query) or die('Error, query failed');
	$row=mysql_fetch_array($result);
	
	if($row['activated']=='Y')
	{
		$msg= "<div class='error'>Delete restricted, Deactivate the employee</div>";
	}
	else
	{
		$sql="delete from employee where empid=".$empID;
		$result  = mysql_query($sql) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
		{
			$msg= "<div class='success'>Employee Deleted Successfully</div>";
		}
		
	}
}*/
if(@$act=="edit" || @$act=="delete" )
{
     	$sql="select  emp_id,br_id,designation_id,department_id,father_name,father_no,mother_name,husband_no,wife_husband,home_phone,emp_name, sex, emp_qualification, emp_doj, emp_dob, emp_doa, emp_addr_pre, emp_addr_per, emp_ecn,emp_mob, emp_epic, emp_pan, mstat, email, password, payment_type,superior from employee where empid=".$empID;
		$result  = mysql_query($sql) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$br_id=@$row['br_id'];$full_name=@$row['emp_name'];
				$emp_id=@$row['emp_id'];
				$designation_id=@$row['designation_id'];
				$department_id=@$row['department_id'];
				$sex=@$row['sex'];
				$hq=@$row['emp_qualification'];
				$doj=@$row['emp_doj'];
				$dob=@$row['emp_dob'];
				$adt=@$row['emp_doa'];
				$present_address=@$row['emp_addr_pre'];
				$permanent_address=@$row['emp_addr_per'];
				$ecn=@$row['emp_ecn'];
				$mob=@$row['emp_mob'];
				$epic=@$row['emp_epic'];
				$pan=@$row['emp_pan'];
				$mstat=@$row['mstat'];
				$email=@$row['email'];
				$father_name=@$row['father_name'];
				$mother_name=@$row['mother_name'];
				$wife_husband=@$row['wife_husband'];
				$husband_no=@$row['husband_no'];
				$father_no=@$row['father_no'];
				$home_phone=@$row['home_phone'];
				$password=@$row['password'];
				$payment_type=@$row['payment_type'];
	$superior=@$row['superior'];			}
		else
			{
			echo "Invalid Selection";
			}	
}
?>

<SCRIPT>
function ClearField(frm){
	  frm.full_name.value = "";
	  frm.emp_id.value = "";
	  frm.adt.value = "";
	  frm.dob.value = "";
	  frm.mstat.value = "";
	  frm.doj.value = "";
	  frm.hq.value = "";
	  frm.permanent_address.value = "";
	  frm.present_address.value = "";
	  frm.father_name.value = "";
	  frm.father_no.value = "";
	  frm.mother_name.value = "";
	  frm.wife_husband.value = "";
	  frm.husband_no.value = "";
	  frm.ecn.value = "";
	  frm.mob.value = "";
	  frm.epic.value = "";
	  frm.pan.value = "";
	  frm.email.value = "";
	  frm.home_phone.value = "";
	  frm.password.value = "";
	  frm.file.value = "";
	  frm.empdepartment.value = "0";
	  frm.designation.value = "0";
	  frm.sex.value = "0";
	  frm.branches.value = "0";
	  
	}
</SCRIPT>

<div id="middleWrap">
		<div class="head"><h2>EMPLOYEE REGISTRATION</h2></div>
<span id="spErr"><?php echo $msg;?></span>

<form id="frm" name="frm" method="post" action="employee.php?empID=<?php echo makeSafe($empID);?>&act=<?php echo makeSafe($act); ?>"" onsubmit="return chkMe();" enctype="multipart/form-data">
<table class="adminform" >
<tr>
	<td align="right">Full Name</td><td class="redstar">:</td>
	<td><input name="full_name"	id="full_name"	type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$full_name;?>" maxlength="255" /></td>	<td></td><td></td><td></td>
</tr>

	<tr>
	<td  align="right">Designation</td><td class="redstar"> : </td>
	<td><?php  designation($designation_id);?></td>
	
	<td  align="right" ><label for="Department" >Department</label></td><td class="redstar" class="redstar"> : </td>
	<td>&nbsp;<?php  emp_department($department_id);?> </td>

	</tr>
	<tr>
	<td  align="right" >Employee ID</td><td class="redstar" class="redstar"> : </td>
	<td><input name="emp_id" id="emp_id" name="emp_id" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$emp_id;?>" autocomplete="off" maxlength="20" ></td>	
	<td  align="right">Gender</td><td c lass="redstar" class="redstar"> : </td>
	<td>&nbsp;<?php  sex(@$sex);?></td>
	  </tr>
	<tr>
	<td  align="right">Anniversary Date</td><td > : </td>
	<td>	
		<input name="adt"	id="adt" type="text" class="date" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$adt;?>"/>
		<script type="text/javascript">
				  $(function() {
						$( "#adt" ).datepicker({
							numberOfMonths: [1,2],
							//showButtonPanel: true,
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
							//maxDate: new Date(2010, 12,28)
						});
					});

				  </script></td>
	<td align="right">Marital Status</td><td class="redstar"> : </td>
	<td>&nbsp;<select name="mstat" id="mstat" size="1">
      <option value="" selected >---Select---</option>
      <option value="SINGLE"<?php if(@$mstat=="S") echo " selected";?> >SINGLE</option>
      <option value="MARRIED"<?php if(@$mstat=="M") echo " selected";?>>MARRIED</option>
      <option value="DIVORCE"<?php if(@$mstat=="D") echo " selected";?>>DIVORCE</option>
    </select>	</td>
	</tr>
	<tr>
	<td  align="right">Date Of Joining</td><td class="redstar"> : </td>
	<td>
		<input name="doj"	id="doj" type="text" class="date" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$doj;?>"/>
		<script type="text/javascript">
				  $(function() {
						$( "#doj" ).datepicker({
							numberOfMonths: [1,2],
							//showButtonPanel: true,
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
							//maxDate: new Date(2010, 12,28)
						});
					});

				  </script></td>
	<td align="right">Place of posting</td><td class="redstar">:</td>
	<td><?php branches($br_id);?>
			<div id="err_location"></div></td>
	</tr>
	<tr>
	<td  align="right">Date Of Birth</td><td class="redstar"> : </td>
	<td>
		<input name="dob"	id="dob" type="text" class="date" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$dob;?>"/>
		<script type="text/javascript">
				  $(function() {
						$( "#dob" ).datepicker({
							numberOfMonths: [1,2],
							//showButtonPanel: true,
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
							//maxDate: new Date(2010, 12,28)
						});
					});

				  </script></td>
	<td align="right">Superior(Head)</td><td> : </td>
			<td>
			
			<?php  if($act=="add") { ?> <input type="checkbox" name="superior" id="superior" value="S"> Select if employee is HEAD</td> <?php }
	            else { ?><select name="superior" id="superior" >
      <option value="" selected >---Select---</option>
      <option value="E"<?php if(@$superior=="E") echo " selected";?>>Employee</option>
      <option value="S"<?php if(@$superior=="S") echo " selected";?>>Superior(HEAD)</option>
    </select>	 <?php } ?></td>
	</tr>
	
	<tr>
	<td align="right">Highest Qualification</td><td class="redstar"> : </td>
	<td><input name="hq" id="hq" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$hq;?>"  maxlength="255" /></td>	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>
	<tr> 
<td align="right">Present Address</td><td class="redstar"> : </td>
	<td><textarea name="present_address" id="present_address" maxlength="500"><?php if($act=="add") ""; elseif($act=="edit") echo @$present_address;?></textarea></td>
		<td  align="right">Permanent Address</td><td class="redstar"> : </td>
	<td>
		<input type="checkbox" name="sameadd" id="sameadd"> Same as Present Address.<br>
		<textarea name="permanent_address" id="permanent_address" maxlength="500" ><?php if($act=="add") ""; elseif($act=="edit") echo @$permanent_address;?></textarea></td></tr>
	<tr>
	<td  align="right">Father Name</td><td> : </td>
	<td>
		<input name="father_name"	id="father_name" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$father_name;?>" maxlength="255"/></td>
			<td  style="white-space:nowrap;" align="right">Father Mobile No</td><td> : </td>
	<td>
		&nbsp;<input name="father_no" id="father_no" maxlength="10"  type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$father_no;?>" /></td>
	</tr>
    <tr>
		<td  align="right">Mother Name</td><td> : </td>
        <td>
            <input name="mother_name"	id="mother_name" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$mother_name;?>"  maxlength="255"/><div id="err_mother_name"></div>	</td>
        <td></td><td></td>
    </tr>
    <tr>    
        <td  align="right">Spouse Name</td><td> : </td>
        <td>
            <input name="wife_husband" id="wife_husband" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$wife_husband;?>"  maxlength="255" /><div id="err_wife_husband"></div>	
         </td>
         <td align="right" >Spouse Contact No</td><td> : </td>
	<td>
		&nbsp;<input name="husband_no" id="husband_no" maxlength="50"  type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$husband_no;?>" /><div id="err_husband_no"></div>	</td>
	</tr>
	<tr>
	<td  align="right" >Emergency Contact No </td><td class="redstar"> : </td>
	<td>
		<input name="ecn" id="ecn" maxlength="50"  type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$ecn;?>" /><div id="err_ecn"></div>	</td>
	<td  align="right">Mobile No</td><td class="redstar"> : </td>
	<td>
		&nbsp;<input name="mob" id="mob" maxlength="50"  type="text" value="<?php if($act=="add") ""; elseif($act=="edit")echo @$mob;?>" /><div id="err_mob"></div>	</td>
	</tr>
	<tr>
	<td  align="right">Voter Card No</td><td > : </td>
	<td><input name="epic" id="epic" type="text" maxlength="255"  value="<?php  if($act=="add") ""; elseif($act=="edit") echo @$epic;?>" /><div id="err_epic"></div></td>
		
	<td  align="right">PAN No</td><td > : </td>	<td>&nbsp;<input name="pan" id="pan" type="text" maxlength="255" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$pan;?>" /><div id="err_pan"></div></td>
	</tr>
	<tr>
  
	<tr>
	<td align="right">Email ID</td><td class="redstar"> : </td>
	<td>
		<input name="email" id="email" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$email;?>" maxlength="255" /><div id="err_email"></div>	</td>
		
	<td align="right">Home Phone</td><td> : </td>
	<td>&nbsp;<input name="home_phone" id="home_phone" maxlength="50" type="text" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$home_phone;?>" /><div id="err_home_phone"></div></td>
	</tr>
	<tr>
	<td  align="right">Password</td><td class="redstar"> : </td>
	<td>
		<input name="password" id="password" maxlength="20" type="password" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$password;?>"/>
		<div id="err_password"></div>	</td>
	<td  align="right">Payment Type</td><td class="redstar"> : </td>
	<td>
		&nbsp;	<select name="payment_type" id="payment_type">
		<option value="" selected>--Select Payment Type--</option>
			<option value="Cheque"<?php if($act=="edit") if($payment_type=="Cheque") echo "selected";?>>Cheque</option>
			<option value="Cash"<?php if($act=="edit") if($payment_type=="Cash") echo "selected";?>>Cash</option>
			<option value="Online"<?php if($act=="edit") if($payment_type=="Online") echo "selected";?>>Online</option>
		</select>
		<div id="err_payment_type"></div>
	</td>
	</tr>
	<tr>
	
	<td  align="right">Photo</td><td > : </td>
	<td>
		<input name="file" id="file" type="file" />
		<span class="hint">upload your photo<span class="hint-pointer">&nbsp;</span></span>
		<div id="err_pan"></div>	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>
	
       <td></td> <td align="center" colspan=4><input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
					
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
			<input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this.form)" />
			<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
			
          	<input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' /></td>
</table>

 </form>
 <script language=javascript>
	document.getElementById("spErr").innerHTML= "<?php echo $msg; ?>";

   </script>
  </div>
   