<?php 
@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include("../functions/comm_functions.php");
include('../check_session.php');
require '../sms/SmsSystem.class.php';
include("../../email_settings.php");
$smssend = new SMS(); //create object instance of SMS class
?>
<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript"   src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<?php
include('../php/js_css_common.php');
/*include("../modulemaster.php");

$id=option_applied_student_list_accept_application;
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





<script language="javascript">

$(document).ready(function()
{
	$("#department").change(function()
	{
		$("#msgbox").removeClass().addClass('messagebox').text('Checking...').fadeIn("slow");
		$.post("populate_session.php",{ deptid:$(this).val() } ,function(data)
        {
		  
		  	$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
			{ 
			   $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
			});
		       });
 
	});
});
</script>
<script language="javascript">
function chkME()
{
	if(document.frm.adt.value==""){
		alert("Please Select Admission Date");
		document.frm.adt.value=="";
		document.frm.adt.focus();
		return false;
	}
	else if(document.frm.session.value=="0"){
	alert("Please Select Session");
	document.frm.session.focus();
	return false;
}
	else if(document.frm.reg_no.value==""){
		alert("Please Enter Registration Number");
		document.frm.reg_no.value=="";
		document.frm.reg_no.focus();
		return false;
	}
	

}
</script>
</head>
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">
<?php
$adm_form_no=makeSafe(@$_GET['fromID']);
$reg_no="";
$act="add";
$sql="SELECT admission_application.br_id,br_name,stu_fname,stu_mname,stu_lname,sex,pin,email,dob, admitted FROM admission_application,mst_branch WHERE admission_application.br_id=mst_branch.br_id and  adm_form_no=".makeSafe($_GET['fromID']);

$result  = mysql_query($sql) or die('Error,query failed');
if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		
		$branch_name=$row['br_name'];
		$br_id=$row['br_id'];
		$stu_fname=$row['stu_fname'];
		$stu_mname=$row['stu_mname'];
		$stu_lname=$row['stu_lname'];
		$sex=$row['sex'];	
		$admitted=$row['admitted'];	
		$dob=$row['dob'];	
			
		$email = $row['email'];
		
	}
$msg="";
$check="";
$action=makeSafe(@$_POST['action']);

if($admitted=="Y")	{$msg= "<div class='error'> Application already accepted</div>"; $check="Y";}
if(@$action=="SAVE" || @$action=="UPDATE")
{
	$reg_no=trim(makeSafe($_POST['reg_no']));
	$adt=makeSafe($_POST['adt']);		
	$session=makeSafe(@$_POST['session']);
	$department_id=getDetailsById("session_section","session_id",$session)['department_id'];
	if($admitted=="Y")
	{
		if($check!="Y")
		$msg= "<div class='error'> Application already accepted</div>";
	}
	else
	{
		//if($department_id!=0)
		//{
			if(@$action=="SAVE")
			{
				if($session=="0")
					$msg="<div class='error'>Invalid Session</div>";
				elseif($adm_form_no=="" || $adm_form_no=="0")
						$msg="<div class='error'>Invalid form no</div>";
					else
						{
							$sql="select  reg_no from mst_students where reg_no='".$reg_no."'";
							$res=mysql_query($sql) or die("Unable to connect to Server,We are sorry for inconvienent caused");
							if(mysql_affected_rows($link)>0)
								$msg="<div class='error'> Duplicate Registration no</div>";
							else
							{		
								$newstuid=getNextMaxId("mst_students","stu_id")+1;
								$sql="INSERT INTO mst_students(stu_id,reg_no,br_id,adm_form_no,stu_fname,stu_mname,stu_lname,sex,dob,doa,email,present_address,city,off_tel_no,res_tel_no,mob,pin,occupation,height,weight,physical_activity,present_medications,presently_pregnant,physician,present_physical_activity,physician_name,clinic_no,mobile_clinic,contact_person_name,contact_person_telno,health_history_checklist,admission_date,updated_by)";
							
								$sql =$sql ." SELECT ".$newstuid.",'".$reg_no."',br_id,adm_form_no,stu_fname,stu_mname,stu_lname,sex,dob,doa,email,present_address,city,off_tel_no,res_tel_no,mob,pin,occupation,height,weight,physical_activity,present_medications,presently_pregnant,physician,present_physical_activity,physician_name,clinic_no,mobile_clinic,contact_person_name,contact_person_telno,health_history_checklist,'".$adt."','".makeSafe($_SESSION['emp_name'])."' FROM admission_application where adm_form_no=".$adm_form_no;
								$res=mysql_query($sql,$link);
								if(mysql_affected_rows($link)>0)
									{
										$stu_id = mysql_insert_id();
										$sql="select  max(rollno) as roll_no from student_class where department_id='".$department_id."' and session_id=".$session;
									$res=mysql_query($sql) or die("Unable to connect to Server,We are sorry for inconvienent caused");
										if(mysql_affected_rows($link)>0)
										{
											$row     = mysql_fetch_array($res,MYSQL_ASSOC);
											$roll_no=$row['roll_no']+1;
										}
											else
											$roll_no=1;
										
									$sql="INSERT INTO student_class(stu_id,department_id,session_id,rollno,status,updated_by)";
									$sql=$sql ."VALUES (".$stu_id.",".$department_id.",'".$session."',".$roll_no.",'A','".makeSafe($_SESSION['emp_name'])."')";
									$res=mysql_query($sql) or die("Unable to connect to Server,We are sorry for inconvienent caused");
										
										
								/*	$sql="select * from customized_reg_data where data_id='".$adm_form_no."'";
									$sqlres=mysql_query($sql);
									if(mysql_num_rows($sqlres)>0){
										while($rowdata=mysql_fetch_assoc($sqlres)){
											$value=$rowdata['value'];
											$sqlval=mysql_query("select * from customized_reg_setting where field_id='".$rowdata['col_id']."'");
											$fielddet=mysql_fetch_assoc($sqlval);
											if($fielddet['field_type']=="FILE")
											{
												$value=base64_encode($adm_form_no)."_".$value;
											}
											$sqlinsert="INSERT INTO customized_reg_data_approved (data_id, col_id, value) VALUES (".$stu_id.",".$rowdata['col_id'].",'".$value."');";
											mysql_query($sqlinsert);
										}
									}*/
									
										$sql="update admission_application set admitted='Y' where adm_form_no=".$adm_form_no;
										$res=mysql_query($sql) or die("Unable to connect to Server,We are sorry for inconvienent caused");
										
										if(mysql_affected_rows($link)>0)
										{
											$msg="<div class='success'>Record Saved Successfully</div>";
										
											//email query 
										 	$query_local = "select * from notification_setting where module_id = ".APPLCATION_ACCEPTED." and  notification_type='E' and sending_type='A' " ; // module_id = '2' for Application Accepted
											$res_local = mysql_query($query_local,$link);
											$num_local = mysql_num_rows($res_local);
											
											if($num_local>0) {
												
											$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_name = 'Application Accepted'";
											$sql_t = mysql_query($query_t) or die('Error. Query failed.');
											$nums = mysql_num_rows($sql_t);
											$gettemp = mysql_fetch_assoc($sql_t);
											$template = $gettemp['email_temp_format'];

											$query="select distinct session_id,session,section,department_name  from session_section as ss, mst_departments as d where d.department_id=ss.department_id and ss.department_id=".$department_id." and freeze='N' and admission_open='Y' AND session_id  = ".$session." ";
											$result=mysql_query($query);
											$rows=mysql_fetch_array($result);
											$sess=$rows['session'];
											$sectn=$rows['section'];
											$department_name=$rows['department_name'];
											$reg=makeSafe($_POST['reg_no']);
											$subject = "Admission Accepted";
											$path = "../../logo.png";
											$stu_name = $stu_fname." ".$stu_lname;
											
											if(@$nums>0 && $email!="") {
										
													$hashvalue = array($stu_name,$branch_name,$sess,$department_name,$sectn,$reg);
													$temp_value = getEmailMessage($template,$hashvalue);
													$sendEmail = Sending_EMail($temp_value,$email,$subject,$path);
												}//nums
											
											}//$num_local
											
											//sms query
											
												$querysetting="select * from notification_setting where module_id=".APPLCATION_ACCEPTED." and notification_type='S' and sending_type='A'";
												$resquerysetting=mysql_query($querysetting,$link);
												$numrows1 = mysql_num_rows($resquerysetting);
												if($numrows1>0)
											{
											$query = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g WHERE e.module_id=g.module_id  AND  e.module_name='Application Accepted' and available_for_school='Y'";
											$sql = mysql_query($query,$scslink);
											$numrows = mysql_num_rows($sql);
											$getresult = mysql_fetch_assoc($sql);
											if($_SESSION['sms_t_count'] > 0 && $numrows > 0)
											{
												/**
												 * This if block indicates that the module has auto sms sending setting. Hence we will call the functions defined in the SmsSystem.class.php file.
												*/
											$session = makeSafe($_REQUEST['session']);
												$sessionquery = mysql_fetch_assoc(mysql_query("select section from session_section where department_id=".$department_id));
												$getmobilequery = mysql_fetch_assoc(mysql_query("select stu_fname, mob from admission_application where adm_form_no=".$adm_form_no));
												$hashvalues = array($stu_fname,$branch_name,$department_name,$sessionquery['section'],$reg_no);
												$message = $smssend->getSmsMessage($getresult['template_format'],$hashvalues);
												
												$send_to = 'MEMBER';
												$mobile = $getmobilequery['mob'];
												$sendMessage = $smssend->sendTransactionalSMS($getresult['template_id'],$mobile,$message,trim($sms_sender_id));
												$logInsert = $smssend->insertLog($sendMessage,$stu_fname,trim($message),$mobile,$send_to,'T');
											}
											}
										}
										else
											$msg="<div class='error'>Record Not Saved Successfully</div>";
									
									}
							}
						}
						
			//}
			
		}
		//else
		//  $msg="<div class='error'>Invalid department</div>";
//    }
}//END OF SAVD OR UPDATE 
}

?>

<SCRIPT>
function ClearField(frm){
	  frm.reg_no.value = "";
	  frm.adt.value = "";
	  frm.session.value = 0;
	}
</SCRIPT>

<div id="middleWrap">
		<div class="head"><h2>ADMISSION</h2></div>

<span id="spErr"><?php echo $msg;?>
<form action="admission.php?act=<?php echo $act; ?>&fromID=<?php echo makeSafe($_GET['fromID']);?>" method="post" name="frm" onSubmit="return chkME();">

<table border="0" cellspacing="0" cellpadding="0" align="center" class="adminform" style="font-size: 12px;">
<tr><td colspan="4"><h3>Admission Details</h3><br /></td></tr>
 <tr>
    <td align="right" class="redstar">Name :</td>
    <td><?php echo $stu_fname.' '.$stu_mname.' '.$stu_lname; ?>  </td>
	</tr>
	
    <td align="right" class="redstar">Birth Date :</td>
    <td align="left"><?php echo @$dob;?></td>
    </tr>
	<tr>
	<td align="right" >Admission Form No :</td><td align="left"><?php echo @$adm_form_no;?></td>
	
  </tr>

<tr>
	<td   align="right" class="redstar">Admission Date :</td>
    <td><input name="adt" type="text" class="date"	id="adt" value="<?php  if($act=="add") ""; elseif($act=="edit") echo @$adt;?>" size="11"/>
        <script type="text/javascript">
				  $(function() {
						$( "#adt" ).datepicker({
							numberOfMonths: [1,2],
							//showButtonPanel: true,
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
							//maxDate: new Date(2010,12,28)
						});
					});

				  </script>    </td>
  </tr>
<tr>
    <td  align="right" class="redstar">Session :</td>
    <td>
    
	<?php 
//echo $sql="select distinct session_id,session,section,department_name  from session_section,mst_departments where session_section.department_id=mst_departments.department_id and freeze='N' and admission_open='Y'";
echo "<select size=\"1\" name=\"session\" id=\"session\">";
echo "<option value=\"0\" selected>--Select session--</option>";
$sql="select distinct session_id,session,section,department_name  from session_section,mst_departments where session_section.department_id=mst_departments.department_id and freeze='N' and admission_open='Y'";

$res=mysql_query($sql) or die("Unable to connect to Server,We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
		echo"<option value='".$row1['session_id']."'>".$row1['session']."-Course:".$row1['department_name']."-Batch:".$row1['section']."</option>";
	
echo"</select>";?>
	
	
	</td>
    
  </tr>
  
 
<tr>
	<td align="right" class="redstar">Registration No :</td>
    <td><input type="text" name="reg_no" value="<?php if($act=="add") ""; elseif($act=="edit") echo @$reg_no; ?>" />    </td>
  </tr>
  
  
<tr>
<tr><td colspan="2" align="center"><input class="btn save" type="submit" value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
        <input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this.form)" />
      <input type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
        <input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>'>
        <input type='hidden' name='brID' value='<?php echo @$brID; ?>'>    </td></tr>
        </table>
</form>
</div>
</body>
</html>