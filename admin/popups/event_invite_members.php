<?php
@session_start ();
include ("../conn.php");
include ('../check_session.php');
include ("../functions/common.php");
include ("../functions/employee/dropdown.php");
include("../functions/comm_functions.php");
require '../sms/SmsSystem.class.php';
include('../../email_settings.php');
$smssend = new SMS(); //create object instance of SMS class
$Errs = "";
makeSafe(extract($_REQUEST));
$event_id = makeSafe ( $_GET ['event_id'] );
$act = makeSafe ( $_GET ['act'] );
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
/*include '../modulemaster.php';
$id=option_academic_calender_sendinvitations;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
if(makeSafe(isset($_POST['submit'])))
{
	if(!empty($rdoID))
	{
		$count=0;
		$querysetting="select * from notification_setting where module_id=".EVENT." and notification_type='S' and sending_type='A'";
		$resquerysetting=mysql_query($querysetting,$link);
		$numrows1 = mysql_num_rows($resquerysetting);
		if($numrows1>0)
		{
		$query = mysql_query("SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g WHERE e.module_id=g.module_id  AND  e.module_name='Event' and available_for_school='Y'",$scslink);
		$fetch = mysql_fetch_assoc($query);
		$num = mysql_num_rows($query);
		}
		//for email template 

		$query_local = "select * from notification_setting where module_id = ".EVENT." and  notification_type='E' and sending_type='A' " ; // module_id = '6' for Event
		$res_local = mysql_query($query_local,$link);
		$num_local = mysql_num_rows($res_local);
		if($num_local>0) {
			$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_name = 'Event'";
			$sql_t = mysql_query($query_t,$scslink) or die('Error. Query failed.');
			$nums = mysql_num_rows($sql_t);
			$gettemp = mysql_fetch_assoc($sql_t);
			$template = $gettemp['email_temp_format'];
		}
	
		
		foreach ($rdoID as $id)
		{
			$sqlcheck=mysql_num_rows(mysql_query("select * from event_person_invites where event_id=".$event_id." and person_id=".$id." and person_type='".$inviteto."'"));
			if($sqlcheck==0)
			{
				$invite_id=getNextMaxId("event_person_invites","invites_id")+1;
				$sqlinsert=mysql_query("insert into event_person_invites (invites_id,event_id,person_id,person_type,invited_on) values (".$invite_id.",".$event_id.",".$id.",'".$inviteto."',NOW())");
				
				
				$getdata = mysql_fetch_assoc(mysql_query("select * from event where event_id=".@$event_id));
				if($inviteto=="S")
				{
					
				    $q1 = "select concat_ws(' ',stu_fname,stu_mname,stu_lname) as name, student_mobile, mob, p_email, student_email from mst_students where stu_id=".@$id ;
					$getsenddata = mysql_fetch_assoc(mysql_query($q1));
					
					$student_email = $getsenddata['student_email'];
					$p_mail =  $getsenddata['p_email'];
					if ($student_email!="") $emailto =  $student_email; else $emailto = $p_mail;
				}
				else if($inviteto=="E")
					$getsenddata = mysql_fetch_assoc(mysql_query("select emp_name as name, emp_mob, email from employee where empid=".@$id));
				
		
				//email query
				if(@$nums>0) {

					$subject= $getdata['event_type'].'-'.$getdata['event_name'];
					$path = POPUP;

					if($getdata['event_type']=="EVENT" || $getdata['event_type']=="HOLIDAY" || $getdata['event_type']=="MEETING")
					{
						if($inviteto=="E")
						{
							$emailto = $getsenddata['email'];
							$send_to = 'EMPLOYEE';
							$name = $getsenddata['name'];
						}
						else if($inviteto=="S")
						{
							$name = $getsenddata['name'];
						}
						
					$hashvalue = array(@$name,$getdata['event_type'],@$getdata['event_name'],date('jS-M-Y, g:iA',strtotime(@$getdata['event_start_date'])),date('jS-M-Y, g:iA',strtotime(@$getdata['event_end_date'])),$getdata['event_description']);
					$temp_value = getEmailMessage($template,$hashvalue);
					$sendEmail = Sending_EMail($temp_value,$emailto,$subject,$path); }
				}
				//sms query
				if($_SESSION['sms_t_count'] > 0 && $num > 0)
				{
					
					if($getdata['event_type']=="EVENT" || $getdata['event_type']=="HOLIDAY" || $getdata['event_type']=="MEETING")
					{
						if($inviteto=="E")
						{
							$mobile = $getsenddata['emp_mob'];
							$send_to = 'EMPLOYEE';
							$name = $getsenddata['name'];
						}
						else if($inviteto=="S")
						{
							$mobile = $getsenddata['student_mobile'];
							$send_to = 'STUDENT';
							$name = $getsenddata['name'];
							if($mobile=="")
							{
								$mobile = $getsenddata['mob'];
								$send_to = 'PARENT';
							}
						}
						$hashvalues = array(SCHOOL_NAME,@$name,$getdata['event_type'],@$getdata['event_name'],date('jS-M-Y, g:iA',strtotime(@$getdata['event_start_date'])),date('jS-M-Y, g:iA',strtotime(@$getdata['event_end_date'])),$getdata['event_description']);//schoolname,name,eventtype,eventname, satrtdatetime,enddatetime,description
						$message = $smssend->getSmsMessage($fetch['template_format'],$hashvalues);
						$sendMessage = $smssend->sendTransactionalSMS($fetch['template_id'],$mobile,$message,trim($sms_sender_id));
						$logInsert = $smssend->insertLog($sendMessage,$name,trim($message),$mobile,$send_to,'T');
					}
				}
			if($sqlinsert)
				$count++;
			}
		}
		if($count==0)
			$Errs="<div class='error'>Invitation(s) To Selected People Already Sent.</div>";
		else
			$Errs="<div class='success'>$count Invitation(s) Sent Successfully.</div>";
	}
	else 
		$Errs="<div class='error'>Please Select Atleast On Person For Invitation.</div>";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>
<script type="text/javascript">
$("#selstudents").hide();
function populateList(act)
{
	if(act=="sectionlist")
	{
		$("#inviteesList").empty();
		if($("#department").val()!="0")
		{
			$("#section").empty();
			var url="../ajax/populatedropdown.php?action=fetchsection&deptid="+$("#department").val();
			makePOSTRequest(url,'','section');
		}
		else
		{
			$("#section").empty();
			$("#section").html("<option value='0'>---SELECT SECTION---</option>");
		}
	}
}
function populatestudent()
{
	if($("#section").val()!="0")
	{
		document.getElementById("inviteesList").innerHTML = "<br><img src='../../images/loading.gif' alt='Loading..'><br> Please Wait ..";
		var dataString = "act=student&dept_id="+$("#department").val()+"&section_id="+$("#section").val();
			    $.ajax({
			    	type: "POST",
			    	url:"../ajax/get_invitees_list.php",
				    data: dataString,
				    success: function(html){
				    	$("#inviteesList").html(html);
					}
		    });
	}
	else
		$("#inviteesList").empty();
}
function populateinvitiees()
{
	$("#inviteesList").empty();
	if($("#inviteto").val()=="S")
	{
		$("#selstudents").show();
	}
	else
	{
		$("#department").val('0');
		$("#section").empty();
		$("#section").html("<option value='0'>---SELECT SECTION---</option>");
		$("#selstudents").hide();
		$("#inviteesList").empty();
		if($("#inviteto").val()=="E"){
			
			document.getElementById("inviteesList").innerHTML = "<br><img src='../../images/loading.gif' alt='Loading..'><br> Please Wait ..";
			
			var dataString = "act=employee";
				    $.ajax({
				    	type: "POST",
				    	url:"../ajax/get_invitees_list.php",
					    data: dataString,
					    success: function(html){
					    	$("#inviteesList").html(html);
						}
			    });
		}   
	}
}
function selectallid()
{

	var elms=document.getElementsByName("rdoID[]");
	if($('#selectall').is(':checked')) {
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = true;
			}
		}
	}
	else
		{
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = false;
				
				}
			}
	}
}
function getSelected()
{
	
	var elms=document.getElementsByName("rdoID[]");
	for (i=0;i<elms.length;i++){
		if (elms[i].type="checkbox" ){
			if(!$('#'+elms[i].value).is(':checked')) {
				$('#selectall').attr('checked',false);
			}
		}
}
}
function validateform()
{
	var count = $("[type='checkbox']:checked").length;
	if(count==0){
		alert("Please select atleast one person to proceed further");
		return false;
	}
}
</script>
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

		<div class="head"><h2>EVENT INVITATIONS</h2></div>
		<br> <span id="spErr"><?php echo $Errs;?> </span>
		<?php
		$sqlverify = mysql_num_rows ( mysql_query ( "select * from event_organizer where event_id='" . $event_id . "' and person_id='" . $_SESSION ['empid'] . "' and person_type='E'" ) );
		if ($sqlverify > 0)
		{
			?>
		<form method="post" name="frmManu" id="frmManu"	action="event_invite_members.php?event_id=<?php echo $event_id;?>&act=<?php echo $act; ?>" onsubmit="return validateform();">
		
			<?php 				
				$event=mysql_fetch_array(mysql_query("select * from event where event_id=".$event_id));
				if($event['event_type']=='LECTURE')
				{
					$count=mysql_num_rows(mysql_query("select * from routine_class where event_id=".$event_id));
					if($count==1)
					{
					?>
					<div style="color:Green;text-align:center;font-size:12px" > Student have been invited already. </div><br>
					<?php
					}
				}
				else
				{
			?>
				
		<table style="font-size: 13px;color:#000000;" align="center" >			
		<tr>
		<td colspan="4">Invite To : 
		<select name="inviteto" id="inviteto" onchange="populateinvitiees();">
		<option value="">---INVITE TO---</option>
		<option value="E">EMPLOYEES</option>
		<option value="S">STUDENTS</option>
		</select>
		
		</td><td></td><td></td><td></td>
		</tr>

		<tr id="selstudents" style="display: none;">
		<td class="redstar">Select Department : </td><td> <select  name="department" id="department" onchange="populateList('sectionlist')">
					<?php
					$sql="select d.* from mst_departments d where d.br_id=".@$_SESSION['br_id']." order by department_name";
					$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					echo "<option value='0'>--SELECT DEPARTMENT--</option>";
					while($row=mysql_fetch_array($res))
							echo "<option value='".$row['department_id']."'>".$row['department_name']."</option>";
					
					?>
			</select></td><td class="redstar">Select Section : </td><td><select style="width:150px;" name="section" id="section" onchange="populatestudent();"><option value='0'>---SELECT SECTION---</option></select></td>
		</tr>
		<tr>		
			
		<td width="100%" valign="top" colspan="4"><div id="inviteesList"></div>
		</td>
		
					
					</tr>
		
		</table>
		<?php } ?>
		</form>
	<?php
	}		
else
	$Errs = "<div class='error'>You Are Not Authorised User To Invite Members</div><br><input type='button' class='btn close' value='CLOSE' onClick='parent.emailwindow.close();' />";
		?>
		</div></div>
</div>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
</body>
</html>