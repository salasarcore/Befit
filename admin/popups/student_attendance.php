<?php 
@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include('../check_session.php');
include('../functions/comm_functions.php');
//include("../email_settings.php");
//require '../sms/SmsSystem.class.php';

//$smssend = new SMS(); //create object instance of SMS class

$msg="";
makeSafe(extract($_REQUEST));
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
/*include("../modulemaster.php");

	$id=option_student_attendance_edit;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$level=$_SESSION['access_level'];

$oldvalue = mysql_fetch_assoc(mysql_query("select * from student_attendance_register where stu_id=$stu_id AND att_date='$date'"));
if($oldvalue['att_type'] == 'A') $oldValue = 'Absent';
else if($oldvalue['att_type'] == 'P') $oldValue = 'Present';
else if($oldvalue['att_type'] == 'L') $oldValue = 'Leave';
else if($oldvalue['att_type'] == 'H') $oldValue = 'Holiday';



if(@$action=="UPDATE")
{
	if($att_type == 'P') $newvalue = 'Present';
	else if($att_type == 'A') $newvalue = 'Absent';
	else if($att_type == 'L') $newvalue = 'Leave';
	else if($att_type == 'H') $newvalue = 'Holiday';
	
	/*$smssetting = "SELECT * FROM notification_setting WHERE module_id=".STUDENT_ATTENDANCE_EDIT." AND notification_type='S' AND sending_type='A'";
	$res = mysql_query($smssetting,$link) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$num = mysql_num_rows($res);
	if($num > 0)
	{
		/**
		 * Indicates that the sms sending setting for student attendance edit module is set to auto.
		 * Proceeding to fetch the template data from global database.
		 * Template data will be fetched on the basis of module id defined by the constant STUDENT_ATTENDANCE_EDIT.
		 */
		/*$data = 'SELECT * FROM global_sms_templates WHERE module_id = '.STUDENT_ATTENDANCE_EDIT.' AND template_type="T" AND approved="Y" AND available_for_school="Y"';
		$res2 = mysql_query($data,$scslink) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
		$numrows = mysql_num_rows($res2);
		$template = mysql_fetch_assoc($res2);
	}
	else
		$numrows = 0;
    //email template
    
	$query_local = "select * from notification_setting where module_id = ".STUDENT_ATTENDANCE_EDIT." and  notification_type='E' and sending_type='A' ";
	$res_local = mysql_query($query_local,$link) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$num_local = mysql_num_rows($res_local);
	if($num_local>0)
	{
		$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_id = ".STUDENT_ATTENDANCE_EDIT." ";
		$sql_t = mysql_query($query_t,$scslink) or die('Error. Query failed.');
		$nums_email = mysql_num_rows($sql_t);
		$gettemp = mysql_fetch_assoc($sql_t);
		$template_email = $gettemp['email_temp_format'];
	} else $nums_email = 0;
	*/
	$err="";
	$today=mysql_fetch_assoc(mysql_query("select curdate() as today"))['today'];
	if($today!=$date)
	if(($level!='Super Admin')&&($level!='Admin'))
		$err="error";
	
	if($err==""){
		$sql="update student_attendance_register set att_type='".$att_type."' where att_date='".$date."' and stu_id=".$stu_id;
		$res=mysql_query($sql,$link);
		if(mysql_affected_rows($link)>=0)
			$msg= "<div class='success'>Attendance Record Updated Successfully.</div>";
		else
			$msg="<div class='error'>Attendance Record Not Updated Successfully.</div>";
		/*$getdata = getDetailsById('mst_students','stu_id',$stu_id);
		$br_data = getDetailsById('mst_branch','br_id',$_SESSION['br_id']);
		//email
		$to = $getdata['p_email'];
		if($nums_email>0 && $to!="" && ($newvalue!=$oldValue)) {
			
			if($oldValue=='Present') $oldval= "<font style='color:#1DC12D;'>$oldValue</font>";
			if($oldValue=='Absent') $oldval= "<font style='color:#FF0800;'>$oldValue</font>";
			if($oldValue=='Leave') $oldval= "<font style='color:#0B8DB3;'>$oldValue</font>";
			if($oldValue=='Holiday') $oldval= "<font style='color:#e5861c;'>$oldValue</font>";

			if($newvalue=='Present') $newval= "<font style='color:#1DC12D;'>$newvalue</font>";
			if($newvalue=='Absent') $newval= "<font style='color:#FF0800;'>$newvalue</font>";
			if($newvalue=='Leave') $newval= "<font style='color:#0B8DB3;'>$newvalue</font>";
			if($newvalue=='Holiday') $newval= "<font style='color:#e5861c;'>$newvalue</font>";
			
		
			if($getdata['father_name']=="")
				$name = $getdata['stu_lname'];
			else $name = $getdata['father_name'];
			$path = POPUP;
			$subject = "Student Attandance Status Changed";
			$hashvalue = array($name,$getdata['stu_fname'],strtoupper($oldval),strtoupper($newval),date('jS M,Y',strtotime($date)),$br_data['br_contact'] );
			$temp_value = getEmailMessage($template_email,$hashvalue);
			$sendEmail = Sending_EMail($temp_value,$to,$subject,$path);
		
		}
		if($_SESSION['sms_t_count'] > 0 && $numrows > 0 && ($newvalue!=$oldValue))
		{
			/**
			 * Indicates that the user has transactional message credits remaining to his account, template data is available and the student is absent
			 */
			
		/*	$hashvalues = array(SCHOOL_NAME,'Mr '.$getdata['father_name'],$getdata['stu_fname'],$oldValue,$newvalue,date('jS M,Y',strtotime($date)));
			$message = $smssend->getSmsMessage($template['template_format'],$hashvalues);
			$mobile = $getdata['mob'];
			$send_to = 'PARENT';
			$sendMessage = $smssend->sendTransactionalSMS($template['template_id'],$mobile,$message,trim($sms_sender_id));
			$logInsert = $smssend->insertLog($sendMessage,$getdata['father_name'],trim($message),$mobile,$send_to,'T');
		}*/
		}

else
	$msg= "<div class='error'>You Are Only Authorised To Mark Today's Attendance. Please Contact Administrator.</div>";
}

if(@$act=="EDIT")
{

	$query   = "select concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name,department_name,section,reg_no
				from mst_students as ms, student_class as sc, mst_departments as d, session_section as ss
				where ms.stu_id=sc.stu_id and sc.department_id=d.department_id and ss.session_id=sc.session_id and ms.stu_id=".$stu_id;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			
		$stu_name=@$row['stu_name'];
		$dept_name=@$row['department_name'];
		$section=@$row['section'];
		$reg_no=@$row['reg_no'];
		
	/*	$leave_approved="";
		$cause="";
		$l=false;
		$att_type="";
		$holiday="";
		$sql="SELECT leave_approved,cause FROM student_leave_applications where stu_id=".$stu_id."  and '". $date ."' between date_from and date_to";
		$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		if(mysql_affected_rows($link)>0)
		{
			$rowL = mysql_fetch_array($resC,MYSQL_ASSOC);
			$leave_approved=$rowL['leave_approved'];
			$cause=$rowL['cause'];
			$l=true;
		}
		$sql="SELECT event_type FROM event where '". $date ."' between date(event_start_date) and date(event_end_date) and session_id=".$session_id;
		$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		if(mysql_affected_rows($link)>0)
		{
			$rowL = mysql_fetch_array($resC,MYSQL_ASSOC);
			$holiday=$rowL['event_type'];
				
		}*/
		
		$sql="SELECT att_type FROM student_attendance_register where stu_id=".$stu_id."  and att_date='". $date ."'";
		$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		if(mysql_affected_rows($link)>0)
		{
			$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
			$att_type=$rowL['att_type'];
		}
		else
			$msg="<div class='error'>Attendance For This Date is Not Yet Marked.</div>";
	}
	
}
?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>Employee Attendance</title>
<link rel="shortcut icon" href="../favicon.ico">

<script language="javascript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
</head>
<body>
	<div id="Middlewrap" style="height: 70%;">
		<div class="head"><h2>EDIT STUDENT ATTENDANCE</h2></div>
	
<span id="spErr"><?php echo $msg;?> </span>
		<form method="post" name="frmManu"
			action="student_attendance.php?stu_id=<?php echo makeSafe($stu_id);?>&act=<?php echo makeSafe($act); ?>"  onsubmit="return validate(this);">
			<input type="hidden" name="date" id="date" value="<?php echo $date;?>" /><input type="hidden" name="session_id" id="session_id" value="<?php echo $session_id;?>" />
			<table class="adminform" width="90%" align="center">

				<tr>
					<td width="25%" align="right" colspan="1"
						style="font-weight: bold;">Name :</td>
					<td width="70%" colspan="4"><?php echo $stu_name; ?></td>
				</tr>
				<tr>
					<td width="25%" align="right" colspan="1"
						style="font-weight: bold;">Course Name :</td>
					<td width="70%" colspan="4"><?php echo $dept_name; ?></td>
				</tr>
				<tr>
					<td width="25%" align="right" colspan="1"
						style="font-weight: bold;">Batch Name :</td>
					<td width="70%" colspan="4"><?php echo $section; ?></td>
				</tr>
				<tr>
					<td width="25%" align="right" colspan="1"
						style="font-weight: bold;">Reg No :</td>
					<td width="70%" colspan="4"><?php echo $reg_no; ?></td>
				</tr>
				<tr>
					<td align="right" style="font-weight: bold;">Attendance :</td>
					<td><input type='radio' id='P' name='att_type' value="P"
					<?php if(@$att_type=="P") echo "checked";?> />Present</td>
					<td><input type='radio' id='A' name='att_type' value="A"
					<?php
					if(@$att_type=="A")
						echo "checked";
					?> />Absent</td>
					<td><input type='radio' id='L' name='att_type' value="L"
					<?php
					if(@$att_type=="L")
					echo "checked";
					?> />Leave</td>
					<td><input type='radio' id='H' name='att_type' value="H"
					<?php
					if(@$att_type=="H")
					echo "checked";
					?> />Holiday</td>

				</tr>
				
				<tr>
					<td align="center" colspan=5><input type="submit" class="btn save" value="UPDATE"
						name="B1"> <input type=button value="CLOSE" class="btn close"
						onClick="parent.emailwindow.close()"> <input type='hidden'
						name='action' value="UPDATE" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $msg; ?>";
</script>
</html>
