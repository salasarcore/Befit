<?php
@session_start ();
include ("../conn.php");
include ('../check_session.php');
include ("../functions/common.php");
include("../functions/comm_functions.php");
require '../sms/SmsSystem.class.php';

$smssend = new SMS(); //create object instance of SMS class
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
/*include '../modulemaster.php';
$id=option_academic_calender_eventattendance;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$Errs = "";
$event_id = makeSafe ( $_GET ['event_id'] );
$routine="select r.*,sub.subject_name, e.* from routine_class r left join class_session_subjects sub on r.subject_id=sub.class_session_subject_id left join event e on r.event_id=e.event_id where r.event_id=". $event_id;
$res=mysql_query($routine) or die('unable to connect to server. We are sorry for the inconvenience caused.');
$rowroutine = mysql_fetch_array ( $res );
$act = makeSafe ( $_GET ['act'] );


$smssetting = "SELECT * FROM notification_setting WHERE module_id=".LECTURE_ATTENDANCE." AND notification_type='S' AND sending_type='A'";
$res = mysql_query($smssetting,$link) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
$num = mysql_num_rows($res);
if($num > 0)
{
	/**
	 * Indicates that the sms sending setting for lecture attendance module is set to auto.
	 * Proceeding to fetch the template data from global database.
	 * Template data will be fetched on the basis of module id defined by the constant LECTURE_ATTENDANCE.
	 */
	$data = 'SELECT * FROM global_sms_templates WHERE module_id = '.LECTURE_ATTENDANCE.' AND template_type="T" AND approved="Y" AND available_for_school="Y"';
	$res2 = mysql_query($data,$scslink) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$numrows = mysql_num_rows($res2);
	$template = mysql_fetch_assoc($res2);
}
else
	$numrows = 0;

$smssetting2 = "SELECT * FROM notification_setting WHERE module_id=".LECTURE_ATTENDANCE_EDIT." AND notification_type='S' AND sending_type='A'";
$res2 = mysql_query($smssetting2,$link) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
$num2 = mysql_num_rows($res2);
if($num2 > 0)
{
	/**
	 * Indicates that the sms sending setting for lecture attendance edit module is set to auto.
	 * Proceeding to fetch the template data from global database.
	 * Template data will be fetched on the basis of module id defined by the constant LECTURE_ATTENDANCE_EDIT.
	 */
	$data2 = 'SELECT * FROM global_sms_templates WHERE module_id = '.LECTURE_ATTENDANCE_EDIT.' AND template_type="T" AND approved="Y" AND available_for_school="Y"';
	$res3 = mysql_query($data2,$scslink) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$numrows2 = mysql_num_rows($res3);
	$template2 = mysql_fetch_assoc($res3);
}
else
	 $numrows2 = 0;


if (makeSafe ( @$_POST ['action'] ) == "SAVE") {


	$from_time="";
	$time_to="";
	$ids = makeSafe ( $_POST ['txtMax'] );
	for($i = 1; $i < $ids; $i ++) {
		$value = makeSafe ( $_POST [$i] );
		
		if(makeSafe(isset($_REQUEST['time_from'.$i])))
			$from_time=makeSafe($_REQUEST['time_from'.$i]);
		else
			$from_time=date('H:i:s',strtotime($rowroutine['time_from']));
		
		if(makeSafe(isset($_REQUEST['time_to'.$i])))
			$time_to=makeSafe($_REQUEST['time_to'.$i]);
		else
			$time_to=date('H:i:s',strtotime($rowroutine['time_to']));
		

		$values = preg_split ( "/(?=\d)/", $value, 2 );
		$att_type = $values [0];
		$person_id = substr ( $values [1], 0, strlen ( $values [1] ) - 1 );
		$person_type = substr ( $values [1], strlen ( $values [1] ) - 1 );
		$actual_intime="";
		$actual_outtime="";
		
		
		$oldvalues = mysql_fetch_assoc(mysql_query('select * from event_person_invites where person_id='.$person_id));		
		if($person_type=="S")
		{
			$getdata = getDetailsById('mst_students','stu_id',$person_id);
			$sql = "update event_person_invites set actual_intime='".$from_time."' , actual_outtime='".$time_to."', show_up_status='" . $att_type . "' where person_id=" . $person_id . " and person_type='" . $person_type . "' and event_id=".$event_id;
			if($oldvalues['show_up_status'] == "" || $oldvalues['show_up_status'] == null)
			{
				/**
				 * This block denotes that the attendance is being marked for the first time for the students.
				 * Attendance marked for the first time will use a different template
				 */
				if($_SESSION['sms_t_count'] > 0 && $numrows > 0 && $rowroutine['event_type'] == 'LECTURE' && $att_type == 'A')
				{
					/**
					 * Indicates that the user has transactional message credits remaining to his account, template data is available and the student is absent
					 */					
					$hashvalues = array(SCHOOL_NAME,'Mr '.$getdata['father_name'],$getdata['stu_fname'],$rowroutine['subject_name'],date('jS M,Y',strtotime($rowroutine['event_start_date'])).'('.date('g:i a',strtotime($from_time)).' to '.date('g:i a',strtotime($time_to)).')');
					$message = $smssend->getSmsMessage($template['template_format'],$hashvalues);
					$mobile = $getdata['mob'];
					$send_to = 'PARENT';
					$sendMessage = $smssend->sendTransactionalSMS($template['template_id'],$mobile,$message,trim($sms_sender_id));
					$logInsert = $smssend->insertLog($sendMessage,$getdata['father_name'],trim($message),$mobile,$send_to,'T');
				}
			}
			else
			{
				/**
				 * This block denotes that the student attendance has already been marked from event once and it is being edited.
				 * Attendance editing will use a different template.
				 */
				if($_SESSION['sms_t_count'] > 0 && $numrows2 > 0 && $rowroutine['event_type'] == 'LECTURE')
				{
					if($oldvalues['show_up_status'] == 'P') $oldValue = 'Present';
					if($oldvalues['show_up_status'] == 'A') $oldValue = 'Absent';
					if($oldvalues['show_up_status'] == 'L') $oldValue = 'Leave';
					if($oldvalues['show_up_status'] == 'H') $oldValue = 'Holiday';
					if($att_type == 'A') $newvalue = 'Absent';
					if($att_type == 'P') $newvalue = 'Present';
					if($att_type == 'L') $newvalue = 'Leave';
					if($att_type == 'H') $newvalue = 'Holiday';
					
					if($oldValue != $newvalue)
					{
						$hashvalues2 = array(SCHOOL_NAME,'Mr '.$getdata['father_name'],$getdata['stu_fname'],$rowroutine['subject_name'],date('jS M,Y',strtotime($rowroutine['event_start_date'])).'('.date('g:i a',strtotime($from_time)).' to '.date('g:i a',strtotime($time_to)).')',$oldValue,$newvalue);
						$message2 = $smssend->getSmsMessage($template2['template_format'],$hashvalues2);
						$mobile2 = $getdata['mob'];
						$send_to = 'PARENT';
						$sendMessage2 = $smssend->sendTransactionalSMS($template2['template_id'],$mobile2,$message2,trim($sms_sender_id));
						$logInsert2 = $smssend->insertLog($sendMessage2,$getdata['father_name'],trim($message2),$mobile2,$send_to,'T');
					}
				}
			}
		}
		if($person_type=="E")
		$sql = "update event_person_invites set actual_intime='".$from_time."' , actual_outtime='".$time_to."', show_up_status='" . $att_type . "' where person_id=" . $person_id . " and person_type='" . $person_type . "'and event_id=".$event_id;
		$res = mysql_query ( $sql ) or die ( "Unable to connect to Server, We are sorry for inconvienent caused" );
		if($person_type=="E")
		{
			
			$sql="SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(ep.actual_outtime,ep.actual_intime)))),'%k.%i')  as total_work_hours from event as e,event_person_invites as ep where e.event_id=ep.event_id and e.event_type='LECTURE' and ep.show_up_status!=' ' and ep.person_type='E' and ep.person_id=".$person_id."  and date(invited_on)= '". $rowroutine['date'] ."'";
			$resE=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$rowE= mysql_fetch_assoc($resE);
			$work_hours=$rowE['total_work_hours'];
			
			$sql="SELECT eatt.* FROM emp_attendance_register as eatt
	join employee as e on eatt.empid=e.empid  where eatt.empid=".$person_id." and att_date='". $rowroutine['date'] ."'";
			$resE=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$count=0;
			$count=mysql_num_rows($resE);
			if($count==0)
			{	
		$sql="insert into emp_attendance_register(empid,att_date,att_type,updated_by,work_hours) values(".$person_id.",'".$rowroutine['date']."','".$att_type."','".makeSafe($_SESSION['emp_name'])."','".$work_hours."')";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			}
			else 
			{
				$rowE= mysql_fetch_assoc($resE);
				
				$sql="update emp_attendance_register set work_hours=".$work_hours." where empid=".$rowE['empid']." and att_date='".$rowroutine['date']."'";
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			}
		}
		
	}
	$Errs = "<div class='success'>" . ($i - 1) . " Invitees Attendance Marked Successfully.</div>";
	

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript"
	src="../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>

<link rel="Stylesheet" href="../../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="../../js/jquery.ui.timepicker.js"></script>
<style>
.ui-timepicker-table .ui-timepicker-title
{
line-height:0.9em;
}
.ui-timepicker-table td, .ui-timepicker-table th.periods {
 font-size:10px;
}

.timestyle
{
width:100px;
height:22px;}

</style>

</head>
<body>
	<div id="middleWrap">

		<?php 
		$eventdetails=getDetailsById("event","event_id",$event_id);
		
		$sqlorganizers=mysql_query("select * from event_organizer where event_id=".$event_id);
		$organizername="";
		if(mysql_num_rows($sqlorganizers)>0){
		while ($row_organizer = mysql_fetch_array($sqlorganizers)) {
			if ($row_organizer['person_type'] == "E") {
				
				$sqldetails = mysql_fetch_array(mysql_query("select emp_name as name from employee where empid=" . $row_organizer ['person_id']));
				$organizername.="&bull; ".$sqldetails['name']."(EMPLOYEE) <br>";
				
				
			}else {
				$sqldetails = mysql_fetch_array(mysql_query("select concat_ws(' ',stu_fname,stu_mname,stu_lname) as name from mst_students where stu_id=" . $row_organizer ['person_id']));
				$organizername.="&bull; ".$sqldetails['name']."(STUDENT) <br>";
			}
		}
		}
		
		echo "<div class='adminform1' style='padding: 5px 0px 5px 5px;font-size:15px;color:#000000;'>EVENT DETAILS</div>";
		echo "<div style='padding:5px 5px 5px 7px;color:#000000;'>
			  <div><div style='float:left;text-align:right;width:128px;'><b><font style='font-size:12px;'>Event Name : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'>".ucfirst($eventdetails['event_name'])."</font><font style='font-size:12px;float:right;'><b>Created Date : </b>".date('jS-M-Y, g:iA',strtotime($eventdetails['event_creation_datetime']))."</font></div>";
		echo "<div><div style='float:left;text-align:right;width:128px;'><b><font style='font-size:12px;'>Event Type : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'>".$eventdetails['event_type']."</font></div></div>";
		echo "<div><div style='float:left;text-align:right;width:128px;'><b><font style='font-size:12px; '>Start Date : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'>".date('jS-M-Y, g:iA',strtotime($eventdetails['event_start_date']))."</font></div></div>";
		echo "<div><div style='float:left;text-align:right;width:128px;'><b><font style='font-size:12px;'>End Date : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'>".date('jS-M-Y, g:iA',strtotime($eventdetails['event_end_date']))."</font></div></div>";
		echo "<div><div style='float:left;text-align:right;width:128px;'><b><font style='font-size:12px;'>Created By : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'>".$eventdetails['event_created_by']."</font></div></div>";
		echo "<div><div style='float:left;'><b><font style='font-size:12px;'>Organizer's Name : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'>".$organizername."</font></div></div>";
		echo "<div><div style='float:left;text-align:right;width:128px;'><b><font style='font-size:12px;'>Description : </font></b></div><div style='margin-left:140px;'><font style='font-size:12px;'><span style='word-wrap: break-word;'>".$eventdetails['event_description']."</span></font></div></div>";
		
		?>
		<br><br>

		<div class="head"><h2>EVENT INVITEES ATTENDANCE</h2></div>
		<br> <span id="spErr"><?php echo $Errs;?> </span>
		<?php
		$sqlverify = mysql_num_rows ( mysql_query ( "select * from event_organizer where event_id='" . $event_id . "' and person_id='" . $_SESSION ['empid'] . "' and person_type='E'" ) );
		if ($sqlverify > 0) {
			?>
		<form method="post" name="frmManu" id="frmManu"
			action="event_attendance.php?event_id=<?php echo $event_id;?>&act=<?php echo $act; ?>">
			<table class="adminlist" style="cursor: pointer;">
				<thead>
					<tr>
						<th>PERSON NAME</th>
						<th>PERSON TYPE</th>
						<th>PRESENT</th>
						<th>ABSENT</th>
						<th>LEAVE</th>
						<th>TO BE DONE</th>
						<th>IN TIME</th>
						<th>OUT TIME</th>
					</tr>
				</thead>
			<?php
			$i = 1;
			$sql = mysql_query ( "select * from event_person_invites where event_id=" . $event_id );
			if (mysql_num_rows ( $sql ) > 0) {
				while ( $row = mysql_fetch_array ( $sql ) ) {
					if ($row ['person_type'] == "E") 
						$sqldet = "select emp_name as name from employee where empid=" . $row ['person_id'];
					else 
						$sqldet = "select concat_ws(' ',stu_fname,stu_mname,stu_lname) as name from mst_students where stu_id=" . $row ['person_id'];
					
					$persondet = mysql_fetch_array ( mysql_query ( $sqldet ) );

					?>
				<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>>
					<td align="center"><?php echo $persondet['name'];?></td>
					<td align="center"><?php echo ($row['person_type']=="E")? "EMPLOYEE" : "STUDENT";?></td>
					<td align="center"><input type='radio' id='P<?php echo $i;?>' name='<?php echo $i;?>' value="P<?php echo $row['person_id'].$row['person_type'];?>" checked />Present</td>
					<td align="center"><input type='radio' id='A<?php echo $i;?>' name='<?php echo $i;?>' value="A<?php echo $row['person_id'].$row['person_type'];?>" <?php if($row['show_up_status']=="A") echo "checked";?> />Absent</td>
					<td align="center"><input type='radio' id='L<?php echo $i;?>' name='<?php echo $i;?>' value="L<?php echo $row['person_id'].$row['person_type'];?>" <?php if($row['show_up_status']=="L") echo "checked";?> />Leave</td>
					<td align="center"><input type='radio' id='TBD<?php echo $i;?>' name='<?php echo $i;?>' value="TBD<?php echo $row['person_id'].$row['person_type'];?>" <?php if($row['show_up_status']=="TBD") echo "checked";?> />TO BE DONE</td>
					<td align="center"><?php if($row['person_type']=="S") echo  date('H:i:s',strtotime($rowroutine['time_from']));else if($row['person_type']=="E") {echo  "<input type='text' name='time_from$i' id='time_from' readonly='readonly' maxlength='3' style='max-width:6em;' class='timestyle' value='"; echo date('H:i:s',strtotime($rowroutine['time_from'])); echo "'/>";}?></td>
					<script>
				        	$('#time_from').timepicker({
				            	minutes: { interval: 1 },
				            	timeFormat: "HH:mm",
				            	rows:5
				            	});
				        </script>
					<td align="center"><?php if($row['person_type']=="S") echo date('H:i:s',strtotime($rowroutine['time_to']));else if($row['person_type']=="E") {echo "<input type='text' name='time_to$i' id='time_to' readonly='readonly' maxlength='3' size='5' style='max-width:6em;' class='timestyle' value='"; echo date('H:i:s',strtotime($rowroutine['time_to'])); echo "'/>";} ?></td>
			
					
				        	<script>
				        	$('#time_to').timepicker({
				            	minutes: { interval: 1 },
				            	timeFormat: "HH:mm",
				            	rows:5
				            	});
				        </script>
				        	</tr>
			<?php
			$i ++;
				}
			?>
				<tr><td align="center" colspan='18'><input type="submit" class="btn save" value='<?php
				if (@$act == "add")
					echo "SAVE";
				if (@$act == "edit")
					echo "UPDATE";
				if (@$act == "delete")
					echo "CONFIRM DELETE";
				?>'	name="B1" /> 
				<input type="button" class="btn close" value="CLOSE"	onClick="parent.emailwindow.close();" />
				<input type='hidden' name='action' value='<?php
				if (@$act == "add")
					echo "SAVE";
				if (@$act == "edit")
					echo "UPDATE";
				if (@$act == "delete")
					echo "DELETE";
				?>' /></td>
				</tr>
			<?php
			} else
				echo "<tr><th colspan='18'>No One Invited Yet For This Event.</th></tr>"?>
			<input type="hidden" id="txtMax" name="txtMax"
					value='<?php echo $i;?>'>
			</table>
		</form>
	<?php
		
} else
	$Errs = "<div class='error'>You Are Not Authorised User To Mark Attendance</div><br><input type='button' value='CLOSE' onClick='parent.emailwindow.close();' />";
		?>
		</div></div>
</div>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
</body>
</html>