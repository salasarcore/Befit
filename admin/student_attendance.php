<?php
 include_once("functions/common.php");
 //include("../email_settings.php");
 //require 'sms/SmsSystem.class.php';

 
 //$smssend = new SMS(); //create object instance of SMS class
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_student_attendance,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin')&&($level!='Admin'))
	{
		if(!isAccessModule($id_admin, module_student_attendance))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
$Errs="";
$session_id = "";
$adt = "";
if(makeSafe(isset($_REQUEST['session'])) && makeSafe(isset($_REQUEST['adt'])))
{
	$session_id = makeSafe($_REQUEST['session']);
	$adt = makeSafe($_REQUEST['adt']);
}
?>
<script>
function selectID(objChk)
{
	document.getElementById("stu_id").value=objChk;
	$('#studentlist tr').on('click', function() {
		$('#studentlist tr').removeClass('selected');
		$(this).toggleClass('selected');
		$(this).find('input[name=rdoID]').attr('checked', 'checked');
	});
}
function ActionScript(act)
{
	if(document.getElementById("stu_id").value=="" && act =="EDIT")
	  	alert("Please select student attendance entry.");
	else
	{
		url="popups/student_attendance.php?act="+act+"&stu_id="+document.getElementById("stu_id").value+"&date="+document.getElementById("adt").value+"&session_id="+document.getElementById("session").value;
		open_modal(url,600,300,"STUDENT ATTENDANCE")
		return true;
	}
}

function  populateStudent(act)
{
    if(document.getElementById("adt").value!="")
	 {
		 if(document.getElementById("session").value!="")
		 {
			var url="ajax/session_student_attendance_list.php?session_id="+document.getElementById("session").value+"&date="+document.getElementById("adt").value+"&filter="+document.getElementById("txtfilter").value;
			makePOSTRequest(url,'','stuList');
		 }		
	 }
}

function checkfilter()
{
	var filter=document.studentattendance.txtfilter.value;
	var date=document.studentattendance.adt.value;
	var session=document.studentattendance.session.value;
	if(session!="" && date!="")
	{
		if(filter.trim()=="")
		{
			document.studentattendance.txtfilter.value="";
			alert('Enter search criteria to filter the records');
			return false;
		}
	}
	else
	{
		alert('Please select session and attendance date to filter the records');
		return false;
	}
	populateStudent('pop');
}
</script>
<script>
$(document).ready(function(){
	<?php if(json_encode($session_id) != "" && json_encode($adt) != ""){?>		
		populateStudent('');
	<?php }?>
});
</script>
<?php
@$source = makeSafe($_REQUEST['source']);
if(makeSafe(@$_GET['act']=="save"))
{	
	/*$smssetting = "SELECT * FROM notification_setting WHERE module_id=".STUDENT_ATTENDANCE." AND notification_type='S' AND sending_type='A'";
	$res = mysql_query($smssetting,$link) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$num = mysql_num_rows($res);
	if($num > 0)
	{
		/**
		 * Indicates that the sms sending setting for student attendance module is set to auto.
		 * Proceeding to fetch the template data from global database.
		 * Template data will be fetched on the basis of module id defined by the constant STUDENT_ATTENDANCE.
		 */
		/*$data = 'SELECT * FROM global_sms_templates WHERE module_id = '.STUDENT_ATTENDANCE.' AND template_type="T" AND approved="Y" AND available_for_school="Y"';
		$res2 = mysql_query($data,$scslink) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
		$numrows = mysql_num_rows($res2);
		$template = mysql_fetch_assoc($res2);
	}
	else
		$numrows = 0;
	//email template
	$query_local = "select * from notification_setting where module_id = ".STUDENT_ATTENDANCE." and  notification_type='E' and sending_type='A' ";
	$res_local = mysql_query($query_local,$link) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$num_local = mysql_num_rows($res_local);
	if($num_local>0)
	{
		$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_id = ".STUDENT_ATTENDANCE." ";
		$sql_t = mysql_query($query_t,$scslink) or die('Error. Query failed.');
		$nums_email = mysql_num_rows($sql_t);
		$gettemp = mysql_fetch_assoc($sql_t);
		$template_email = $gettemp['email_temp_format'];
	} else $nums_email = 0;

	*/
	$err="";
	$att_date=makeSafe($_POST['adt']);
	$ids=makeSafe($_POST['txtMax']);
	$session_id=makeSafe($_POST['session']);
	$count=0;
	$today=mysql_fetch_assoc(mysql_query("select curdate() as today"))['today'];
	$level=$_SESSION['access_level'];
	if($today!=$att_date)
		if(($level!='Super Admin')&&($level!='Admin'))
			$err="error";
	
	if($err=="")
	{
		$sql="SELECT stu_id,att_type FROM student_attendance_register where att_date='". $att_date ."' and session_id=".$session_id;
		$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$count=mysql_affected_rows($link);
		if($count>0)
			$Errs= "<div class='alert-danger'>Student attendance is already marked for ".date("d-m-Y", strtotime($att_date)).". To update entry, select student and update it.</div>";
		else{
		for($i=1;$i<=$ids;$i++)
			{
				$att_type=substr(makeSafe($_POST[$i]),0,1);
				$stu_id=substr(makeSafe($_POST[$i]),1);
				$sql="insert into student_attendance_register(stu_id,session_id,att_date,att_type,updated_by) values(".$stu_id.",".$session_id.",'".$att_date."','".$att_type."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			
				/*$getdata = getDetailsById('mst_students','stu_id',$stu_id);
				$br_data = getDetailsById('mst_branch','br_id',$_SESSION['br_id']);
				//email
				$to = $getdata['p_email'];
				if($nums_email>0 && $to!="" && $att_type == 'A') {
				
				
				if($getdata['father_name']=="")
					$name = $getdata['stu_lname'];
				else $name = $getdata['father_name'];
				$path = BRANCH_MANAGER;
				$subject = "Student Absent";
				$hashvalue = array($name,$getdata['stu_fname'],date('jS M,Y',strtotime($att_date)), $br_data['br_contact']);
				$temp_value = getEmailMessage($template_email,$hashvalue);
				$sendEmail = Sending_EMail($temp_value,$to,$subject,$path);
				
				}
				//sms
				if($_SESSION['sms_t_count'] > 0 && $numrows > 0 && $att_type == 'A')
				{
					/**
					 * Indicates that the user has transactional message credits remaining to his account, template data is available and the student is absent
					 */
					
					/*$hashvalues = array(SCHOOL_NAME,'Mr '.$getdata['father_name'],$getdata['stu_fname'],date('jS M,Y',strtotime($att_date)));
					$message = $smssend->getSmsMessage($template['template_format'],$hashvalues);
					$mobile = $getdata['mob'];
					$send_to = 'PARENT';					
					$sendMessage = $smssend->sendTransactionalSMS($template['template_id'],$mobile,$message,trim($sms_sender_id));
					$logInsert = $smssend->insertLog($sendMessage,$getdata['father_name'],trim($message),$mobile,$send_to,'T');
				}	*/
			}
			$Errs= "<div class='alert-success'>".($i-1)." Student Attendance Marked Successfully.</div>";			
		}
	}
	else 
		$Errs= "<div class='alert-danger'>You Are Only Authorised To Mark Today's Attendance. Please Contact Administrator.</div>";
}
?>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Member</a><span style="color: #000000;"> Member Attendance</span> </div>
<h2>Member Attendance <?php if(makeSafe(isset($_REQUEST['source']))){ ?><a href="<?php echo $source;?>#tabs-3" style="padding-left:10px;" class="btn close"> <span style="padding-left:7px;">BACK</span></a><?php }?></h2>
</div>
<br>
<span id="spErr"><?php echo $Errs;?> </span>
<form name="studentattendance" action="pages.php?src=student_attendance.php&act=save" method="post">
<table class="adminform1" width="100%"><tr><td>
<span class="redstar" style="margin-left: 10px;">Select Session, Course and Batch :</span>
<select name="session" id="session" onchange="populateStudent('pop')">
<option value="">--Select Session, Course and Batch--</option>
<?php
$sql="select d.department_name,s.session,s.section,s.session_id,s.freeze from mst_departments d, session_section s where d.department_id=s.department_id and s.freeze='N' and d.br_id=".$_SESSION['br_id']." and s.session='".makesafe($_SESSION['d_session'])."' order by department_name,session_id desc";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
while($row=mysql_fetch_array($res))
{
	echo "<option value='".$row['session_id']."'";
	if(@$session_id==$row['session_id']) echo " Selected"; echo ">".$row['session']."-".$row['department_name']."-".$row['section']."</option>";
}
  ?>
</select>
<span class="redstar">Date : </span><input name="adt" type="text" class="date" id="adt" value="<?php echo @$adt;?>" size="11"  onchange="populateStudent('pop')"/>
<script type="text/javascript">
  $(function() {
		$( "#adt" ).datepicker({
			numberOfMonths: [1,2],
			dateFormat: 'yy-mm-dd',
			maxDate: new Date()
		});
	});
</script> 
</td>
<td>
<div id="option_menu">
	<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('EDIT');">Edit</a>
</div>
</td>
</tr>
<tr>
<td><span class="redstar" style="margin-left: 10px;">Member Name/Registration no. : </span>
<input type="text" name="txtfilter" id="txtfilter"/>
<input type="button" name="search" id="search" value="Go" class="btn btn-info" onclick="return checkfilter();"/>
</td></tr></table>
<div id="stuList"></div>
<input type="hidden" name="stu_id" id="stu_id" value="" />
<?php if(makeSafe(isset($_REQUEST['source']))){?>
<input type="hidden" name="source" value="<?php echo $source;?>"/>
<?php }?>
</form>
<script language="javascript">
	document.getElementById("spErr").innerHTML= "<?php echo $Errs;?>";
</script>