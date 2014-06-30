<?php 
@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include("../check_session.php");
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php 
include('../php/js_css_common.php');
/*include("../modulemaster.php");

$id=option_admission_register_edit_registration;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>BRANCHES</title>
<link rel="shortcut icon" href="../favicon.ico">
<script type="text/javascript"   src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>

<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script language="javascript" type="text/javascript" src="../../js/date_time_currency_number_email.js"></script>
<script language="javascript">
function chkME()
{

if(document.frm.stu_fname.value==""){
	alert("Please Enter  First Name");
	document.frm.stu_fname.focus();
	return false;
}
if(document.frm.stu_lname.value==""){
	alert("Please Enter  Last Name");
	document.frm.stu_lname.focus();
	return false;
}
if(trim(document.frm.sex.value)=="0"){
	alert("Invalid Sex");
	document.frm.sex.focus();
	return false;
}
if(trim(document.frm.m_stat.value)=="--Select--"){
	alert("Invalid Sex");
	document.frm.sex.focus();
	return false;
}
month=document.frm.month.value;
day=document.frm.day.value;
year=document.frm.year.value;
var dob=year+"-"+month+"-"+day;
if(!isDate(dob))
    return false;



if(trim(document.frm.present_address.value)==""){
	alert("Invalid present address");
	document.frm.present_address.focus();
	return false;
}

if(trim(document.frm.pin.value)==""){
	alert("Invalid Pin code");
	document.frm.pin.focus();
	return false;
}

if(trim(document.frm.mob.value)==""){
	alert("Please Enter Mobile Number");
	document.frm.mob.focus();
	return false;
}
var mobilelen=document.frm.mob.value.length;
if(mobilelen<10){
	alert("Mobile number must be 10 digit");
	document.frm.mob.focus();
	return false;
}
	
return true;
}
</script>
<script language="javascript">

function validateEmail(email) 
{ 
	var emailReg = <?php echo REGEX_EMAIL;?>;
	  if( !emailReg.test( email ) ) {
	    return false;
	  } else {
	    return true; 
	  }
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
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">

<div id="middleWrap">
		<div class="head"><h2>ADMISSION</h2></div>



<?php
$department_id="";
$stu_fname="";
$stu_mname="";
$stu_lname="";
$br_id="";
$sex="";

$dob="";

$present_address="";

$pin="";$contacts="";
$email="";
$mob="";
$occupation="";
$height="";
$weight="";
$offtelno="";
$restelno="";
$physicalactivity="";
$presentmedications="";
$presentlypregnant="";
$physician="";
$presentphysicalactivity="";
$physicianname="";
$clinicno="";
$mobileclinic="";
$contactpersonname="";
$contactpersontelno="";
$msg="";

$action=@$_POST['action'];

	
if(@$action=="SAVE" || @$action=="UPDATE")
{	

	$stu_fname=makeSafe($_POST['stu_fname']);
	$stu_mname=makeSafe($_POST['stu_mname']);
	$stu_lname=makeSafe($_POST['stu_lname']);
	$sex=makeSafe($_POST['sex']);	
	

	$yy=makeSafe(@$_POST['year']);$mm=makeSafe(@$_POST['month']);$dd=makeSafe(@$_POST['day']);
	$dob=$yy."-".$mm."-".$dd;
	
	$email=makeSafe($_POST['email']);
	
	
	$present_address=makeSafe($_POST['present_address']);
	
	$pin=makeSafe($_POST['pin']);
	$mob=makeSafe($_POST['mob']);
	$occupation=makeSafe($_POST['occupation']);
	$height=makeSafe($_POST['height']);
	$weight=makeSafe($_POST['weight']);
	$offtelno=makeSafe($_POST['off_tel_no']);
	$restelno=makeSafe($_POST['res_tel_no']);
	$physicalactivity=makeSafe($_POST['physical_activity']);
	$presentmedications=makeSafe($_POST['present_medications']);
	$presentlypregnant=makeSafe($_POST['presently_pregnant']);
	$physician=makeSafe($_POST['physician']);
	$presentphysicalactivity=makeSafe($_POST['present_physical_activity']);
	$physicianname=makeSafe($_POST['physician_name']);
	$clinicno=makeSafe($_POST['clinic_no']);
	$mobileclinic=makeSafe($_POST['mobile_clinic']);
	$contactpersonname=makeSafe($_POST['contact_person_name']);
	$contactpersontelno=makeSafe($_POST['contact_person_telno']);
	
	$health=array();
	$health=$_POST['check'];
	$healthhistory=implode(",",$health);
	if(@$action=="SAVE")
		{	
			if (!is_numeric($pin))
				$msg= "<div class='error'>Please Enter Pin Number as Numbers Only</div>";
			else
				if (!is_numeric($mob))
					$msg= "<div class='error'>Please Enter Mobile Number as Numbers Only</div>";
			
			
			else
				if ($email!="" && !filter_var(trim($email),FILTER_VALIDATE_EMAIL))
				{
					$msg= "<div class='error'>This ($email) email Id is considered invalid.";
				
				}
			else
			{
				$sql="update mst_students set stu_fname='".$stu_fname."',stu_mname='".$stu_mname."',stu_lname='".$stu_lname."',sex='".$sex."',dob='".$dob."'";
				$sql =$sql .",email='".$email."'";
				$sql =$sql .",present_address='".$present_address."'";
				$sql =$sql .",mob='".$mob."',pin='".$pin."',city='".$city."',off_tel_no='".$offtelno."',res_tel_no='".$restelno."',occupation='".$occupation."',height='".$height."',weight='".$weight."',physical_activity='".$physicalactivity."',present_medications='".$presentmedications."',presently_pregnant='".$presentlypregnant."',physician='".$physician."',present_physical_activity='".$presentphysicalactivity."',physician_name='".$physicianname."',clinic_no='".$clinicno."',mobile_clinic='".$mobileclinic."',contact_person_name='".$contactpersonname."',contact_person_telno='".$contactpersontelno."',health_history_checklist='".$healthhistory."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where stu_id=".$_GET['studentID'];
				$res=mysql_query($sql,$link) or die(mysql_error($link));
				if(mysql_affected_rows($link)==0)
					$msg="<div class='success'>No Data Changed</div>";
				if(mysql_affected_rows($link)>0)
					$msg="<div class='success'>Record Updated successfully</div>";
				if(mysql_affected_rows($link)<0)
					$msg="<div class='error'>Record Not Updated successfully</div>";
			}
						
						
		}
	}
$sql="SELECT * FROM mst_students WHERE stu_id=".makeSafe($_GET['studentID']);
	$res=mysql_query($sql,$link) or die(mysql_error($link));
	if(mysql_affected_rows($link)>0)
	{
	$row=mysql_fetch_assoc($res);
	$stu_fname=$row['stu_fname'];$stu_mname=$row['stu_mname'];$stu_lname=$row['stu_lname'];$sex=$row['sex'];$dob=$row['dob'];$doa=$row['doa'];
	
	$email=$row['email'];$present_address=$row['present_address'];
	$mob=$row['mob'];
	$city=$row['city'];
	$pin=$row['pin'];
	$d=explode("-",$dob);
	$dd1=@$d[2];
	$mm1=@$d[1];
	$yy1=@$d[0];
	$d1=explode("-",$doa);
	$dd2=@$d1[2];
	$mm2=@$d1[1];
	$yy2=@$d1[0];
	
	$occupation=$row['occupation'];
	$height=$row['height'];
	$weight=$row['weight'];
	$offtelno=$row['off_tel_no'];
	$restelno=$row['res_tel_no'];
	$physicalactivity=$row['physical_activity'];
	$presentmedications=$row['present_medications'];
	$presentlypregnant=$row['presently_pregnant'];
	$physician=$row['physician'];
	$presentphysicalactivity=$row['present_physical_activity'];
	$physicianname=$row['physician_name'];
	$clinicno=$row['clinic_no'];
	$mobileclinic=$row['mobile_clinic'];
	$contactpersonname=$row['contact_person_name'];
	$contactpersontelno=$row['contact_person_telno'];
	$healthhistory=$row['health_history_checklist'];
	$health=array();
	$health=explode(",",$healthhistory);
	}
	
?>

	<span id="spErr"><?php echo @$msg;?></span>
	<form action="edit_student.php?studentID=<?php echo makeSafe($_GET['studentID']);?>" method="post" name="frm" onsubmit="return chkME();">

	<table border="0" cellspacing="0" cellpadding="0" align="center" class="adminlist" style="cursor: pointer;">

	
	  <tr><td colspan="4"><h3>Information</h3></td></tr>
	  <tr>
		<td align="right" ><font class="redstar"> </font>Name :</td>
		<td><input placeholder="FIRST NAME" type="text" name="stu_fname" id="stu_fname" value="<?php echo $stu_fname; ?>" size="30" maxlength="100"/></td>		
		<td><input placeholder="MIDDLE NAME" type="text" name="stu_mname" id="stu_mname" value="<?php echo $stu_mname; ?>" size="30" maxlength="100"/></td>
		<td><input placeholder="LAST NAME" type="text" name="stu_lname" id="stu_lname" value="<?php echo $stu_lname; ?>" size="30" maxlength="100"/></td>
		
	  </tr>
	  <tr>
		<td align="right" class="redstar">Sex :</td>
		<td><?php sex($sex); ?></td>
		<td  align="right" class="redstar">Birth Date :</td>
		<td><select name="month"> 
					<option value=0 selected>Month</option>
					<option value="01" <?php echo @$mm1=="01"?" selected":"";?> >Jan</option>
					<option value="02" <?php echo @$mm1=="02"?" selected":"";?>>Feb</option>
					<option value="03" <?php echo @$mm1=="03"?" selected":"";?>>Mar</option>
					<option value="04" <?php echo @$mm1=="04"?" selected":"";?>>Apr</option>
					<option value="05" <?php echo @$mm1=="05"?" selected":"";?>>May</option>
					<option value="06" <?php echo @$mm1=="06"?" selected":"";?>>Jun</option>
					<option value="07" <?php echo @$mm1=="07"?" selected":"";?>>Jul</option>
					<option value="08" <?php echo @$mm1=="08"?" selected":"";?>>Aug</option>
					<option value="09" <?php echo @$mm1=="09"?" selected":"";?>>Sep</option>
					<option value="10" <?php echo @$mm1=="10"?" selected":"";?>>Oct</option>
					<option value="11" <?php echo @$mm1=="11"?" selected":"";?>>Nov</option>
					<option value="12" <?php echo @$mm1=="12"?" selected":"";?>>Dec</option>
				</select>
					<select name="day">
					<option value=0>Day</option>
						<?php 
						  for($i=1;$i<=31;$i++)
							{
							 if($i<10)
								$day="0".$i;
								else
									$day=$i;
									
							
							echo"<option value=".$day;
							   if($i==@$dd1) echo " selected";
							   echo ">";
							  echo $day;
							   echo"</option>";
							   }
							   ?>
						  </select>
						  <select name="year"> 
							 <option value="0" selected>Year</option> 
							 <?php 
						  for($i=1970;$i<=2011;$i++)
							{echo"<option value='".$i."'";
							   if($i==@$yy1) echo " selected";
							   echo ">".$i."</option>";
							   }
							   ?>   </td>
			  </tr>
	  <tr>
		
							   </tr>
							    <tr>
		<td  align="right" class="redstar">Anniversary :</td>
		<td><select name="month"> 
					<option value=0 selected>Month</option>
					<option value="01" <?php echo @$mm2=="01"?" selected":"";?> >Jan</option>
					<option value="02" <?php echo @$mm2=="02"?" selected":"";?>>Feb</option>
					<option value="03" <?php echo @$mm2=="03"?" selected":"";?>>Mar</option>
					<option value="04" <?php echo @$mm2=="04"?" selected":"";?>>Apr</option>
					<option value="05" <?php echo @$mm2=="05"?" selected":"";?>>May</option>
					<option value="06" <?php echo @$mm2=="06"?" selected":"";?>>Jun</option>
					<option value="07" <?php echo @$mm2=="07"?" selected":"";?>>Jul</option>
					<option value="08" <?php echo @$mm2=="08"?" selected":"";?>>Aug</option>
					<option value="09" <?php echo @$mm2=="09"?" selected":"";?>>Sep</option>
					<option value="10" <?php echo @$mm2=="10"?" selected":"";?>>Oct</option>
					<option value="11" <?php echo @$mm2=="11"?" selected":"";?>>Nov</option>
					<option value="12" <?php echo @$mm2=="12"?" selected":"";?>>Dec</option>
				</select>
					<select name="day">
					<option value=0>Day</option>
						<?php 
						  for($i=1;$i<=31;$i++)
							{
							 if($i<10)
								$day="0".$i;
								else
									$day=$i;
									
							
							echo"<option value=".$day;
							   if($i==@$dd2) echo " selected";
							   echo ">";
							  echo $day;
							   echo"</option>";
							   }
							   ?>
						  </select>
						  <select name="year"> 
							 <option value="0" selected>Year</option> 
							 <?php 
						  for($i=1970;$i<=2011;$i++)
							{echo"<option value='".$i."'";
							   if($i==@$yy2) echo " selected";
							   echo ">".$i."</option>";
							   }
							   ?>   </td>
							   <td align="right" class="redstar">Pin Code :</td>
		<td><input type="text" name="pin" size="30" value="<?php echo $pin; ?>" /></td>
		
							   </tr>
			  <tr>
		<td align="right" class="redstar">Email :</td>
		<td><input name="email" size="30" value="<?php echo $email; ?>" /></td>
			<td align="right" class="redstar">Mobile No. :</td>
		<td><input name="mob" size="30" value="<?php echo $mob; ?>" /></td>
		  </tr>
	  <tr>
		<td align="right" class="redstar">Off Tel. No. :</td>
		<td><input name="offtelno" size="30" value="<?php echo $offtelno; ?>" /></td>
			<td align="right" class="redstar">Res. Tel. No. :</td>
		<td><input name="restelno" size="30" value="<?php echo $restelno; ?>" /></td>
		  </tr>
	  <tr>
		<td align="right" class="redstar">Present Address :</td>
		<td colspan="3"><input name="present_address" type="text" size="80" id="present_address" value="<?php echo @$present_address;?>" /></td>
	  </tr>
		   <tr> 
		<td align="right" class="redstar">City :</td>
		<td><input name="city" size="30" value="<?php echo $city; ?>" /></td>
		<td align="right" class="redstar">Occupation :</td>
		<td><input name="occupation" size="30" value="<?php echo $occupation; ?>" /></td>
		  </tr>
		  		   <tr> 
		<td align="right" class="redstar">Height :</td>
		<td><input name="height" size="30" value="<?php echo $height; ?>" /></td>
		<td align="right" class="redstar">Weight :</td>
		<td><input name="weight" size="30" value="<?php echo $weight; ?>" /></td>
		  </tr>
			  		   <tr> 
		<td align="right" class="redstar" colspan="3" style="text-align: left;">Are you currently involved in any physical activity?</td>
		<td><input type="radio" name="physicalactivity" value="YES" <?php if($physicalactivity=="YES") echo "Selected";?> />YES
	<input type="radio" name="physicalactivity" value="NO" <?php if($physicalactivity=="NO") echo "Selected";?>/>NO</td>
		  </tr>
		  		<tr><td colspan="4" style="text-align: left;"><h3>HEALTH FORM</h3></td></tr>	  		   <tr> 
		<td align="right" class="redstar" colspan="2" style="text-align: left;">
Are you presently taking any medications? If yes then please list
		</td>
		<td colspan="2"><input type="text" name="presentmedications" value="<?php echo $presentmedications;?>"></td>
		  </tr>
		    <tr> 
		<td align="right" class="redstar" colspan="2" style="text-align: left;">
Are you presently pregnant in last three months?
		</td>
		<td colspan="2"><input type="text" name="presentlypregnant" value="<?php echo $presentlypregnant;?>"></td>
		  </tr>
		   <tr> 
		<td align="right" class="redstar" colspan="3" style="text-align: left;">Does your physician know you are participating in exercise program?</td>
		<td><input type="radio" name="physician" value="YES" <?php if($physician=="YES") echo "Selected";?> />YES
	<input type="radio" name="physician" value="NO" <?php if($physician=="NO") echo "Selected";?>/>NO</td>
		  </tr>
		  	    <tr> 
		<td align="right" class="redstar" colspan="2" style="text-align: left;">
What physical activity are presently doing?		</td>
		<td colspan="2"><input type="text" name="presentphysicalactivity" value="<?php echo $presentphysicalactivity;?>"></td>
		  </tr>
		    	    <tr> 
		<td align="right" class="redstar" colspan="2" style="text-align: left;">
Physician Name	</td>
		<td colspan="2"><input type="text" name="physicianname" value="<?php echo $physicianname;?>"></td>
		  </tr>
		  	    	    <tr> 
		<td align="right" class="redstar"  style="text-align: left;">
Clinic No.	</td>
		<td ><input type="text" name="clinicno" value="<?php echo $clinicno;?>"></td>
		<td align="right" class="redstar" style="text-align: left;">
Clinic Mobile No.	</td>
		<td ><input type="text" name="mobileclinic" value="<?php echo $mobileclinic;?>"></td>
		  </tr>
		  		    	    <tr> 
		<td align="right" class="redstar" colspan="4" style="text-align: left;">
Person/s to contact in case of emergency?	</td>
		 </tr>
		 		    	    <tr> 
		<td align="right" class="redstar" style="text-align: left;">
Contact Person Name	</td>
		<td ><input type="text" name="contactpersonname" value="<?php echo $contactpersonname;?>"></td>
			<td align="right" class="redstar"  style="text-align: left;">
Contact Person No.	</td>
		<td><input type="text" name="contactpersontelno" value="<?php echo $contactpersontelno;?>"></td>	  </tr>
		<tr><td align="right" class="redstar" colspan="4" style="text-align: left;">Health History Checklist</td></tr>
		<tr>
		<td colspan="4"><input type="checkbox" name="check[]" value='Muscle'> Muscle joint or back disorder that could be aggravated by physical activity<br>
                                         	<input type="checkbox" name="check[]" value="History_of_heart" <?php if (in_array("History_of_heart", $health)) echo "checked"; ?>> History of heart problem in immediate family <br>
                                         	<input type="checkbox" name="check[]" value="Recent_surgery_history" <?php if (in_array("Recent_surgery_history", $health)) echo "checked"; ?>> Recent surgery in last three immediate family<br>
                                         	<input type="checkbox" name="check[]" value="Recent_surgery_last_three_month" <?php if (in_array("Recent_surgery_last_three_month", $health)) echo "checked"; ?>> Recent Surgery in last three months <br>
                                         	<input type="checkbox" name="check[]" value="High_cholesterol_level" <?php if (in_array("High_cholesterol_level", $health)) echo "checked"; ?>> High cholesterol level <br>
                                         	<input type="checkbox" name="check[]" value="High_trigyceride_level" <?php if (in_array("High_trigyceride_level", $health)) echo "checked"; ?>> High trigyceride level<br>
                                         	<input type="checkbox" name="check[]" value="Heart_Disease" <?php if (in_array("Heart_Disease", $health)) echo "checked"; ?>> Heart Disease <br>
                                         	<input type="checkbox" name="check[]" value="High_Blood_Pressure" <?php if (in_array("High_Blood_Pressure", $health)) echo "checked"; ?>> High Blood Pressure<br>
                                         	<input type="checkbox" name="check[]" value="Chest_Pain" <?php if (in_array("Chest_Pain", $health)) echo "checked"; ?>> Chest Pain <br>
                                         	<input type="checkbox" name="check[]" value="Stroke" <?php if (in_array("Stroke", $health)) echo "checked"; ?>> Stroke<br>
                                         	<input type="checkbox" name="check[]" value="Irregular_Heartbeats" <?php if (in_array("Irregular_Heartbeats", $health)) echo "checked"; ?>> Irregular Heartbeats<br>
                                         	<input type="checkbox" name="check[]" value="Shortness_of_Breath" <?php if (in_array("Shortness_of_Breath", $health)) echo "checked"; ?>> Shortness of Breath<br>
                                         	<input type="checkbox" name="check[]" value="Lung_Problem" <?php if (in_array("Lung_Problem", $health)) echo "checked"; ?>> Lung Problem <br>
                                         	<input type="checkbox" name="check[]" value="Asthma" <?php if (in_array("Asthma", $health)) echo "checked"; ?>> Asthma<br>
                                         	<input type="checkbox" name="check[]" value="Allergies_Depression" <?php if (in_array("Allergies_Depression", $health)) echo "checked"; ?>> Allergies Depression<br>
                                         	<input type="checkbox" name="check[]" value="Dizziness" <?php if (in_array("Dizziness", $health)) echo "checked"; ?>> Dizziness<br>
                                         	<input type="checkbox" name="check[]" value="Fainting_Spells" <?php if (in_array("Fainting_Spells", $health)) echo "checked"; ?>> Fainting Spells<br>
                                         	<input type="checkbox" name="check[]" value="Severe_headaches" <?php if (in_array("Severe_headaches", $health)) echo "checked"; ?>> Severe headaches<br>
                                         	<input type="checkbox" name="check[]" value="Seizures_or_Convulsion" <?php if (in_array("Seizures_or_Convulsion", $health)) echo "checked"; ?>> Seizures or Convulsion<br>
                                         	<input type="checkbox" name="check[]" value="Numbness_or_tingling" <?php if (in_array("Numbness_or_tingling", $health)) echo "checked"; ?>> Numbness or tingling<br>
                                         	<input type="checkbox" name="check[]" value="Anemia" <?php if (in_array("Anemia", $health)) echo "checked"; ?>> Anemia<br>
                                         	<input type="checkbox" name="check[]" value="Anxiety" <?php if (in_array("Anxiety", $health)) echo "checked"; ?>> Anxiety<br>
                                         	<input type="checkbox" name="check[]" value="Use_of_Laxatives_or_diuretics" <?php if (in_array("Use_of_Laxatives_or_diuretics", $health)) echo "checked"; ?>> Use of Laxatives or diuretics<br>
                                         	<input type="checkbox" name="check[]" value="Alcoholism_substance" <?php if (in_array("Alcoholism_substance", $health)) echo "checked"; ?>> Alcoholism substance<br></td></tr>
	<tr><td colspan="4" align="center" ><input class="btn save" type="submit" value='UPDATE' /> 
	<input type='hidden' name='action' value="SAVE" />
	<input type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
	</td></tr>
			</table>
			
	</form>
	<script language=javascript>
	document.getElementById("spErr").innerHTML= "<?php echo $msg; ?>";

   </script>
</div>
