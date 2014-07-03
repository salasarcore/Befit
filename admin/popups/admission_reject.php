<?php

@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include('../check_session.php');
require '../sms/SmsSystem.class.php';
include("../../email_settings.php");
$smssend = new SMS(); //create object instance of SMS class
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
/*include("../modulemaster.php");

$id=option_applied_student_list_reject_application;
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

</head>
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">
<?php
$adm_form_no=makeSafe(@$_GET['fromID']);
$reg_no="";
$act="add";
$sql="SELECT rejected_cause,admission_application.br_id,br_name,concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name,sex,pin,email,dob, admitted FROM admission_application,mst_branch WHERE admission_application.br_id=mst_branch.br_id and  adm_form_no=".makeSafe($_GET['fromID']);
$result  = mysql_query($sql) or die('Error,query failed');
if(mysql_affected_rows($link)>0)
	{
		$row     = mysql_fetch_array($result,MYSQL_ASSOC);
		$branch_name=$row['br_name'];
		$br_id=$row['br_id'];
		$stu_name=$row['stu_name'];
		$sex=$row['sex'];	
		$dob=$row['dob'];
		$admitted=$row['admitted'];	

			
		$rejected_cause=$row['rejected_cause'];	
		$email = $row['email'];
	
	}
$msg="";
$chec="";

$action=makeSafe(@$_POST['action']);
$rejected_cause=makeSafe(@$_POST['rejected_cause']);

if($admitted=="Y")	{$msg= "<div class='error'> Application already accepted</div>"; $chec="Y";}
if(@$action=="SAVE" || @$action=="UPDATE")
{	
	if($admitted=="Y")
	{
		if($chec!="Y")
		$msg= "<div class='error'> Application already accepted</div>";
	}
	else
	{									
		$sql="update admission_application set admitted='R',rejected_cause='".$rejected_cause."' where adm_form_no=".$adm_form_no;
		$res=mysql_query($sql) or die("Unable to connect to Server,We are sorry for inconvienent caused");
		$msg="<div class='success'>Rejected Successfully</div>";
		//email query 
		$query_local = "select * from notification_setting where module_id = ".APPLCATION_REJECTED." and  notification_type='E' and sending_type='A' " ; // module_id = '3' for APPLICATION REJECTED
		$res_local = mysql_query($query_local,$link);
		$num_local = mysql_num_rows($res_local);
		
	    if($num_local>0) {
	 	$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y' AND  e.module_name = 'Application Rejected'";
	 	$sql_t = mysql_query($query_t) or die('Error. Query failed.');
	 	$nums = mysql_num_rows($sql_t);
	 	$gettemp = mysql_fetch_assoc($sql_t);
	 	$template = $gettemp['email_temp_format'];
		$subject = "Admission Rejected";
		$path="../../logo.png";
		if ($rejected_cause!="") {  $cause = "Reason : ".$rejected_cause; }  else {  $cause="";}
		
		if(@$nums>0 && $email!="") {
			
			$hashvalue = array($stu_name,$cause);
			$temp_value = getEmailMessage($template,$hashvalue);
			$sendEmail = Sending_EMail($temp_value,$email,$subject,$path);
		}//nums
		
		
		
	 }//$num_local
		//sms query
		
		$query = "SELECT * FROM sms_settings WHERE template_type='T' AND send_type='A' AND approved_status='Y' AND module_name ='Application Rejected'";
		$sql = mysql_query($query) or die('Error. Query failed.');

		 $querysetting="select * from notification_setting where module_id=".APPLCATION_REJECTED." and notification_type='S' and sending_type='A'";
		$resquerysetting=mysql_query($querysetting,$link);
		$numrows1 = mysql_num_rows($resquerysetting);
		if($numrows1>0)
		{
		 $query = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g WHERE e.module_id=g.module_id  AND  e.module_name='Application Rejected' and available_for_school='Y' ";
		$sql = mysql_query($query,$scslink) or die('Error. Query failed.');
		$numrows = mysql_num_rows($sql);
		$getresult = mysql_fetch_assoc($sql);
		if($_SESSION['sms_t_count'] > 0)
		{
			if($numrows>0)
			{
				
				$hashvalues = array($stu_name,$rejected_cause);
				$message = $smssend->getSmsMessage($getresult['template_format'],$hashvalues);
				$getmobilequery = mysql_fetch_assoc(mysql_query("select  mob from admission_application where adm_form_no=".$adm_form_no));
				
				$send_to = 'MEMBER';
					$mobile = $getmobilequery['mob'];
					$sendMessage = $smssend->sendTransactionalSMS($getresult['template_id'],$mobile,$message,trim($sms_sender_id));
				$logInsert = $smssend->insertLog($sendMessage,$stu_name,trim($message),$mobile,$send_to,'T');
			}
		}
		}
	}
	
		
}//END OF SAVD OR UPDATE 


?>

<div id="middleWrap">
		<div class="head"><h2>ADMISSION</h2></div>

<span id="spErr"><?php echo $msg;?>
<form action="admission_reject.php?act=<?php echo $act; ?>&fromID=<?php echo makeSafe($_GET['fromID']);?>" method="post" name="frm" onSubmit="return chkME();">

<table border="0" cellspacing="0" cellpadding="0" align="center" class="adminform" style="font-size: 12px;">
<tr><td colspan="4" style="text-align: left;"><h3>Admission Details</h3><br /></td></tr>
<tr><td align="right" class="redstar"> Name :</td><td align="left"><?php echo $stu_name; ?>  </td></tr>
<tr><td align="right" class="redstar">Birth Date :</td> <td align="left"><?php echo @$dob;?></td></tr>
<tr><td align="right">Admission Form No :</td><td align="left"><?php echo @$adm_form_no;?></td> </tr>
<tr><td align="right">Cause :</td><td><input type="text" name="rejected_cause" value="<?php   if($act=="add") ""; elseif($act=="edit")echo @$rejected_cause;?>" /></td></tr>
<tr><td colspan="2" align="center"><input class="btn save" type="submit" value='<?php 
	                if(@$act=="add") echo "REJECT";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
        <input type="reset" class="btn reset" value="RESET" name="B2">
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