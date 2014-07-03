<?php

include('../conn.php');
include_once("../functions/common.php");

include("../functions/employee/dropdown.php");
include("../functions/functions.php");
include("../functions/comm_functions.php");
$message="";
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
	elseif ($mobileno=="" && !(ctype_digit($mobileno)))
	{
		redirect($self,"Invalid mobile number");
	
	}
	elseif ($offtelno=="" && !(ctype_digit($offtelno)))
	{
		redirect($self,"Invalid office telephone number");
	
	}
	elseif ($restelno=="" && !(ctype_digit($restelno)))
	{
		redirect($self,"Invalid residential telephone number");
	
	}
	elseif ($clinicno=="" && !(ctype_digit($clinicno)))
	{
		redirect($self,"Invalid residential telephone number");
	
	}
	elseif ($mobileclinic=="" && !(ctype_digit($mobileclinic)))
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
		/*	$_SESSION['sms_t_count']=0;
			$query2 = "SELECT SUM(spd.no_of_transactional_purchase) as sms_t_count, SUM(spd.no_of_promotional_purchase) as sms_p_count FROM schools sc LEFT JOIN school_sms_purchase_dtls spd ON sc.schoolid = spd.school_id WHERE sc.domain_identifier = '".$subdomainname."'";
			$res2 = mysql_query($query2, $scslink);
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
				/*	$schoolname = SCHOOL_NAME;
					$hashvalues = array($schoolname,$stu_fname,$newformid);
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
		
	}//$num_local1*/
	$message="Record Saved Successfully";
}
	
}

}

?>


<script language="javascript">

function chkME(frm)
{
if(trim(frm.firstname.value)==""){
	alert("Please Enter First Name");
	frm.firstname.focus();
	return false;
}
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
<div id="middleWrap">
		<div class="head">
    <h2>Register</h2></div>
<span id="spErr"><?php echo $message;?></span>

  <form class="sky-form" id="frm" action="apply_online.php" name="frm" onsubmit="return chkME(this);"  method="POST">
<table class="adminform" >
<tr>
   <td align="right"  class="redstar">Name :</td>
		<td><input placeholder="FIRST NAME" type="text" name="firstname" id="firstname" size="30" maxlength="100"/></td>		
		<td><input placeholder="MIDDLE NAME" type="text" name="middlename" id="middlename"  size="30" maxlength="100"/></td>
		<td><input placeholder="LAST NAME" type="text" name="lastname" id="lastname"  size="30" maxlength="100"/></td>
		
                                </tr>
                                <tr>
                                <td align="right"  class="redstar">Address :</td>
                                <td colspan="3">
                                       <textarea name="address" id="address"></textarea>
                                </td>
                                </tr>
                                <tr>
                                    <td align="right"  class="redstar">City :</td>
                                                <td><input type="text" name="city" id="city" ></td>
                                                <td align="right"  class="redstar">Pin :</td>
                                                <td><input type="text" name="pin" id="pin" ></td>
                                                </tr>
												
												<tr>
                                    <td align="right"  class="redstar">Office Tel.No. :</td>
                                                <td><input type="text" name="offtelno" id="offtelno"></td>
                                                <td align="right"  class="redstar">Res. Tel. No. :</td>
                                                <td> <input type="text" name="restelno" id="restelno"></td>
                                                </tr>
										<tr>
										<td align="right"  class="redstar">Mobile No. :</td>
										<td><input type="text" name="mobileno" id="mobileno"></td>
   									    <td align="right"  class="redstar">Sex :</td>
                                            
										<td><select name="sex" id="sex">
                                                <option value="MALE">MALE</option>
                                                <option value="FEMALE">FEMALE</option>
                                                </select></td></tr>
											<tr>
											<td align="right"  class="redstar">Email :</td>	
                                             <td><input type="text" name="email" id="email" /></td>
                                             <td align="right"  class="redstar">Date of Birth :</td>
                                             <td>  <select name="month1"> 
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
						   <select name="year1"> 
							 <option value="0" selected>Year</option> 
							 <?php 
						  for($i=1970;$i<=(date('Y')-2);$i++)
							{echo"<option value='".$i."'";
							   if($i==@$yy) echo " selected";
							   echo ">".$i."</option>";
							   }
							   ?>  </select></td>	
											</tr>
                                        <tr>
                                        <td align="right"  class="redstar">Anniversary :</td>	
                                    <td>
                                               
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
						 
						  <select name="year2"> 
							 <option value="0" selected>Year</option> 
							 <?php 
						  for($i=1970;$i<=(date('Y')-2);$i++)
							{echo"<option value='".$i."'";
							   if($i==@$yy) echo " selected";
							   echo ">".$i."</option>";
							   }
							   ?>  </select>
                                       </td>
                                          <td align="right"  class="redstar">Occupation :</td>	
                                          <td>
                                                <input type="text" name="occupation"></td></tr>
												<tr>
												      <td align="right"  class="redstar">Height :</td>	
                                             <td><input type="text" name="height" id="height"></td>
                                               <td align="right"  class="redstar">Weight :</td>	
												<td>                                         
                                                <input type="text" name="weight" id="weight"></td></tr>
												<tr><td colspan="3">
                                            Are you currently involved in any physical activity?</td><td><input type="radio" name="physicalactivity" value="YES">YES <input type="radio" name="physicalactivity" value="NO">NO</td></tr>
                                  
                                                 <tr>
                                    <td colspan="4">
                                        <label class="label col col-10" style="font-weight: bold;">HEALTH FORM</label>
                                    </td></tr>
										
                                    <tr><td colspan="4">
                                Are you presently taking any medications? If yes then please list<br><br>  <input type="text" name="presentmedications" size="80"></td>
                                    </tr> 		
                                       
                                    <tr><td colspan="4">
                                Are you presently pregnant in last three months?<br><br>                                           
                                                <input type="text" name="presentlypregnant" size="80">
                                               </td></tr>
                                               <tr><td colspan="3">
                                  Does your physician know you are participating in exercise program?</td>
                                        		  <td> <input type="radio" name="physician" value="YES">YES
                                       <input type="radio" name="physician" value="NO">NO</td>
                                    </tr>
                                       
                                        <tr><td colspan="4">
                                What physical activity are presently doing?<br><br>
                                        		                                 
                                                <input type="text" name="presentphysicalactivity" size="80" >
                                               </td></tr>
                                               <tr>
												  <td align="right"  class="redstar">Physician Name :</td>	
                                          <td>
                                                                             
                                                <input type="text" name="physicianname" id="physicianname"> </td>
                                                <td align="right"  class="redstar">Clinic No :</td>	
												<td>
                                                <input type="text" name="clinicno" id="clinicno">
                                               </td></tr>
                                               <tr> <td align="right"  class="redstar">Mobile No :</td>
                                        	   <td>                                    
                                              
                                                <input type="text" name="mobileclinic" id="mobileclinic">
                                               </td></tr>
											<tr><td colspan="4">
                                                  Person/s to contact in case of emergency?</td></tr>
                                    <tr>
                                    <td>1. Name</td>
                                         <td><input type="text" name="contactpersonname" id="contactpersonname">
                                             </td><td align="right">Tel. No. :</td>
                                        	 <td>
                                        	  <input type="text" name="contactpersontelno" id="contactpersontelno"></td>
                                               </tr>
                                <tr><td colspan="4"> Health History Checklist<br>
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
                                         	
                                         </td></tr>
                                         <tr><td colspan="4">
                                Rules and Regulations<br>
                                       1.We reserve the right to admission.<br>
                                        2.Membership fees are Non-refundable.<br>
                                          3.The rates for facility may changed at the sole discretion of the management without prior notice.<br>
                                          	4.The Management is not responsible for any loss ot theft of member's belongings.<br>
                                          	 5.We advice members not to carry valuables to the gym.<br>
                                          	 6.Smoking Chewing of Beetle Leaf is strictly prohibited in the gym promises.<br>
                                          	 7.Courteous member conduct is very important in order to provide everyone with pleasant workout experience poor conduct includes,but is not limited to use profanity,bad mouthing others and fighting If a members is found treating anyone disrespectfully, their membership  will be terminated without refund and that member waives their right to recourse.<br>
                                          	 8.Sports wear is compulsory in the gym.<br>
                                          	 9.Members must carry a separate pair of trainnig shoes to be changed into at the gym premises to help maintain hygiene.<br>
                                          	 10.The Management will maintain notice board for intimating the members of any future changes. The Management expects the members to read it on regular basis. We asssume the members read the notice. <br>
                                          	 11.We request members to use a deodorant spary.<br>
                                          	 12.Taxes as applicable.<br>
                                    </td></tr>
                                    <tr><td align="center" colspan="4">
                                <input class="btn save" type="submit" name="login_submit" value="Register">
                                	<input type=button class="btn close" value="Close" onClick="parent.emailwindow.close();">
                                </td>
                                </tr>
                                </table>
                                 </form>        
	
			</div>
		
	